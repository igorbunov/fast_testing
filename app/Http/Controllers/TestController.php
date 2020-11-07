<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Result;
use App\ResultAnswer;
use App\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function showResults($editSlug)
    {
        $test = Test::getByEditSlug($editSlug);

        $info = [
            'slug' => $test['edit_slug'],
            'testLink' => url("/t/{$test['test_slug']}"),
            'description' => $test['description'],
            'length' => $test['test_time_minutes'],
            'isActive' => $test['is_active']
        ];

        $results = Result::getByTestId($test['id']);

        foreach ($results as $i => $result) {
            $results[$i]->report = Result::getSuccessResult($result->id);
        }


        return view('results', [
            'info' => $info,
            'results' => $results,
            'translateValues' => (new TranslateController())->getValues()
        ]);
    }

    public function showOneResult($editSlug, $resultId)
    {
        $test = Test::getByEditSlug($editSlug);

        $info = [
            'slug' => $test['edit_slug'],
            'testLink' => url("/t/{$test['test_slug']}"),
            'description' => $test['description'],
            'length' => $test['test_time_minutes'],
            'isActive' => $test['is_active']
        ];

        $questionsTmp = Question::getQuestionsByTestId($test['id']);
        $questions = [];

        foreach ($questionsTmp as $question) {
            $questionRow = [
                'id' => $question['id'],
                'questionText' => $question['question'],
                'answers' => []
            ];

            $answers = Answer::getAnswersByQuestionId($question['id']);

            foreach ($answers as $answer) {
                $isUserSelect = ResultAnswer::getByResultQuestionAnswer($resultId, $question['id'], $answer['id']);

                $questionRow['answers'][] = [
                    'id' => $answer['id'],
                    'answerText' => $answer['answer'],
                    'isTrue' => $answer['is_true'],
                    'isUserSelect' => $isUserSelect
                ];
            }

            $questions[] = $questionRow;
        }

        return view('result', [
            'info' => $info,
            'questions' => $questions,
            'translateValues' => (new TranslateController())->getValues()
        ]);
    }

    public function finishExpiredTests()
    {
        $tests = Test::getActiveTests();

        foreach ($tests as $test) {
            $results = Result::getDelayedTests($test['id'], $test['test_time_minutes']);

            foreach ($results as $result) {
                Result::edit($result->id, [
                    Result::STATUS => Result::STATUS_FINISHED,
                    Result::END_DT => Result::DT_NOW
                ]);

                Log::alert('Finished delayed test result, id = ' . $result->id);
            }
        }
    }

    public function removeOldTests()
    {
        // TODO: do this if more 1 year
        $oldTests = Test::getOldTests();

        foreach ($oldTests as $test) {
            $testId = (int) $test->id;
            //TODO: delete
        }
        // select * from tests where updated_at + interval 1 year < now();

//        select * from tests where id = 17;
//        select * from questions where test_id = 17;
//        select * from answers where question_id in (select id from questions where test_id = 17);
//        select * from results where test_id = 17;
//        select * from result_answers where result_id in (select id from results where test_id = 17);
    }

    public function startNewTest()
    {
        return view('wizard.create_test', [
            'isQuestionare' => 0,
            'translateValues' => (new TranslateController())->getValues()
        ]);
    }

    public function setLanguage(Request $request)
    {
        $lang = $request->post('lang', 'en');

        session(['lang' => $lang]);

        return response()->json([
            'success' => true
        ]);
    }

    public function saveNewTest(Request $request)
    {
        $subQuestion = $request->get('sub_question');

        if (!empty($subQuestion)) {
            return;
        }

        $data = \json_decode($request->post('params'), true);
        $isQuestionare = (int) $request->get('is_questionare', 0);

        if ($isQuestionare == 1) {
            $data['test_length'] = 10;
            $data['is_anonymous'] = filter_var($data['is_anonymous'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $data['is_anonymous'] = true;
        }

        if (
            !is_array($data)
            or empty($data['test_length'])
            or empty($data['email'])
            or empty($data['questions'])
        ) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error wrong data')
            ]);
        }

        $test = Test::newTest($data['email'], $data['description'], $data['test_length'], $data['is_anonymous']);

        foreach ($data['questions'] as $question) {
            $newQ = Question::newQuestion($test['id'], $question['questionText']);

            foreach ($question['answers'] as $answer) {
                Answer::newAnswer($newQ['id'], $answer['answerText'], $answer['isTrue']);
            }
        }

        $emailSender = new EmailSender($data['email'], __('messages.test creation'));
        $isEmailSended = $emailSender->sendTestCreated(url("/t/{$test[Test::TEST_SLUG]}"), url("/r/{$test[Test::EDIT_SLUG]}"));

        if (!$isEmailSended) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error sending message to email')
            ]);
        }

        return response()->json([
            'success' => true,
            'email' => $data['email'],
            'testSlug' => url("/t/{$test[Test::TEST_SLUG]}")
        ]);
    }

    public function editTestStatus(Request $request)
    {
        $slug = $request->post('slug');
        $isActive = (int) $request->post('is_active', 0);

        $test = Test::getByEditSlug($slug);

        if (is_null($test)) {
            return response()->json([
                'success' => false,
                'slug' => $slug,
                'message' => __('messages.error getting test')
            ]);
        }

        Test::edit($test['id'], [
            Test::IS_ACTIVE => $isActive
        ]);

        return response()->json([
            'success' => true,
            'slug' => $slug
        ]);
    }

    public function showTest(string $testSlug)
    {
        $test = Test::getByTestSlug($testSlug);

        if (is_null($test)) {
            return redirect('main');
        }

        if (empty($test->is_active)) {
            return __('messages.test is not active');
        }

        $questions = Question::getQuestionsByTestId($test->id);

        if (count($questions) == 0) {
            return redirect('main');
        }

        foreach ($questions as &$question) {
            $answers = Answer::getAnswersByQuestionId($question['id']);

            $question['answers'] = $answers;
        }

        unset($question);

        return view('test', [ 
            'info' => [
                'slug' => $testSlug,
                'testLink' => url("/{$testSlug}"),
                'description' => $test->description,
                'length' => $test->test_time_minutes,
                'questions_count' => count($questions)
            ],
            'questions' => $questions,
            'translateValues' => (new TranslateController())->getValues()
        ]);
    }

    public function startTest(Request $request)
    {
        $testSlug = $request->post('slug');
        $email = $request->post('email');

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email cant be empty')
            ]);
        }

        $test = Test::getByTestSlug($testSlug);

        if (empty($test)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.test not found')
            ]);
        }

        $result = Result::add([
            Result::TEST_ID => $test->id,
            Result::EMAIL => $email,
            Result::STATUS => Result::STATUS_STARTED
        ]);

        if (empty($result)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error start testing')
            ]);
        }

        return response()->json([
            'success' => true,
            'result_id' => $result->id,
            'time' => $test[Test::TEST_TIME_MINUTES] * 60
        ]);
    }

    public function getTestInfo(Request $request)
    {
        $testSlug = $request->post('slug');
        $resultId = (int) $request->post('result_id');

        $test = Test::getByTestSlug($testSlug);
        $result = Result::getById($resultId);

        if (is_null($result)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.result not found')
            ]);
        }

        $result = $result->toArray();

        if ($result[Result::TEST_ID] != $test['id']) {
            return response()->json([
                'success' => false,
                'message' => __('messages.this result is from another test')
            ]);
        }

        if ($result[Result::STATUS] == Result::STATUS_FINISHED) {
            return response()->json([
                'success' => false,
                'message' => __('messages.time is out')
            ]);
        }

        return response()->json([
            'success' => true,
            'seconds_to_end' => Result::getSecondsToEnd($test['id'], $resultId)
        ]);
    }

    public function finishTest(Request $request)
    {
        $testSlug = $request->post('slug');
        $resultId = (int) $request->post('result_id');
        $email = $request->post('email');
        $data = \json_decode($request->post('data'), true);

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email cant be empty')
            ]);
        }
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no answers')
            ]);
        }

        $test = Test::getByTestSlug($testSlug);

        if (empty($test)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.test not found')
            ]);
        }

        $test = $test->toArray();

        foreach ($data as $row) {
            $questionId = (int) $row['question_id'];
            $answerId = (int) $row['answer_id'];

            if (!Question::isLinkedToSlug($questionId, $test[Test::EDIT_SLUG])) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.these are not your questions')
                ]);
            }

            if (!Answer::isLinkedToQuestion($answerId, $questionId)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.these are not your answers')
                ]);
            }
        }

        $result = Result::getById($resultId);

        if (is_null($result)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.result not found')
            ]);
        }

        $result = $result->toArray();

        if ($result[Result::TEST_ID] != $test['id']) {
            return response()->json([
                'success' => false,
                'message' => __('messages.this result is from another test')
            ]);
        }

        Result::edit($result['id'], [
            Result::STATUS => Result::STATUS_FINISHED,
            Result::END_DT => Result::DT_NOW
        ]);

        foreach ($data as $row) {
            $questionId = (int) $row['question_id'];
            $answerId = (int) $row['answer_id'];
            $isChecked = filter_var($row['checked'], FILTER_VALIDATE_BOOLEAN);

            ResultAnswer::add([
                ResultAnswer::RESULT_ID => $result['id'],
                ResultAnswer::QUESTION_ID => $questionId,
                ResultAnswer::ANSWER_ID => $answerId,
                ResultAnswer::IS_CHECKED => $isChecked
            ]);
        }

        $emailSender = new EmailSender($test['email'], __('messages.test passing'));
        $isEmailSended = $emailSender->sendTestPassed($email, url("/r/{$test[Test::EDIT_SLUG]}"));

        if (!$isEmailSended) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error sending message to email')
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function getAnswerForm(Request $request)
    {
        $isQuestionare = (int) $request->post('is_questionare', 0);

        return response()->json([
            'success' => true,
            'html' => view(
                'wizard.answer',
                [
                    'isQuestionare' => $isQuestionare
                ]
            )->render()
        ]);
    }
    
    public function getNewQuestionForm(Request $request)
    {
        $isQuestionare = (int) $request->post('is_questionare', 0);

        return response()->json([
            'success' => true,
            'html' => view(
                'wizard.question',
                [
                    'isQuestionare' => $isQuestionare
                ]
            )->render()
        ]);
    }
}
