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
            'editLink' => url("/e/{$test['edit_slug']}"),
            'testLink' => url("/t/{$test['test_slug']}"),
            'description' => $test['description'],
            'length' => $test['test_time_minutes'],
            'isActive' => $test['is_active']
        ];

        $results = Result::getByTestId($test['id']);

        foreach ($results as $i => $result) {
            $results[$i]->report = Result::getSuccessResult($result->id);
        }

//        dd($results);

        return view('results', [
            'info' => $info,
            'results' => $results
        ]);
    }

    public function showOneResult($editSlug, $resultId)
    {
        $test = Test::getByEditSlug($editSlug);

        $info = [
            'slug' => $test['edit_slug'],
            'editLink' => url("/e/{$test['edit_slug']}"),
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
                $questionRow['answers'][] = [
                    'id' => $answer['id'],
                    'answerText' => $answer['answer'],
                    'isTrue' => $answer['is_true']
                ];
            }

            $questions[] = $questionRow;
        }

//        dd($info);

        return view('result', [
            'info' => $info,
            'questions' => $questions
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

    public function startNewTest()
    {
        $test = Test::newTest();

        return redirect('/e/' . $test['edit_slug']);
    }

    public function editTest(Request $request)
    {
        $slug = $request->post('slug');
        $form = \json_decode($request->post('form'), true);
        $questions = \json_decode($request->post('questions'), true);
        $data = [];
        $test = Test::getByEditSlug($slug);

        if (is_null($test)) {
            return response()->json([
                'success' => false,
                'slug' => $slug,
                'message' => 'Ошибка получения тэста'
            ]);
        }

        foreach ($form as $row) {
            $data[$row['name']] = $row['value'];
        }

        $test->edit($test->id, $data);

        foreach ($questions as $question) {
            if (!Question::isLinkedToSlug($question["questionId"], $slug)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Это не ваш вопрос'
                ]);
            }

            Question::edit($question["questionId"], $question["questionText"]);

            foreach ($question['answers'] as $answer) {
                if (!Answer::isLinkedToQuestion($answer["answerId"], $question["questionId"])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Это не ваш ответ'
                    ]);
                }

                $isTrue = filter_var($answer['isTrue'], FILTER_VALIDATE_BOOLEAN);

                Answer::edit($answer["answerId"], $answer["answerText"], $isTrue);
            }
        }

        return response()->json([
            'success' => true,
            'slug' => $slug
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
                'message' => 'Ошибка получения теста'
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
            return 'Тест не активен';
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
//dd($questions);
        return view('test', [ 
            'info' => [
                'slug' => $testSlug,
                'testLink' => url("/{$testSlug}"),
                'description' => $test->description,
                'length' => $test->test_time_minutes,
                'questions_count' => count($questions)
            ],
            'questions' => $questions
        ]);
    }

    public function startTest(Request $request)
    {
        $testSlug = $request->post('slug');
        $email = $request->post('email');

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email не может быть пустым'
            ]);
        }

        $test = Test::getByTestSlug($testSlug);

        if (empty($test)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет найден тест'
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
                'message' => 'Ошибка начала тестирования'
            ]);
        }

        return response()->json([
            'success' => true,
            'result_id' => $result->id,
            'time' => $test[Test::TEST_TIME_MINUTES] * 60
            //'html' => view('edit-answer', ['answer' => $answer, 'questionId' => $questionId])->render()
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
                'message' => 'Нет найден результат'
            ]);
        }

        $result = $result->toArray();

        if ($result[Result::TEST_ID] != $test['id']) {
            return response()->json([
                'success' => false,
                'message' => 'Результат не от этого теста'
            ]);
        }

        if ($result[Result::STATUS] == Result::STATUS_FINISHED) {
            return response()->json([
                'success' => false,
                'message' => 'Время вышло'
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
                'message' => 'Email не может быть пустым'
            ]);
        }
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет ответов'
            ]);
        }

        $test = Test::getByTestSlug($testSlug);

        if (empty($test)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет найден тест'
            ]);
        }

        $test = $test->toArray();

        foreach ($data as $row) {
            $questionId = (int) $row['question_id'];
            $answerId = (int) $row['answer_id'];

            if (!Question::isLinkedToSlug($questionId, $test[Test::EDIT_SLUG])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Это не ваши вопросы'
                ]);
            }

            if (!Answer::isLinkedToQuestion($answerId, $questionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Это не ваши ответы'
                ]);
            }
        }

        $result = Result::getById($resultId);

        if (is_null($result)) {
            return response()->json([
                'success' => false,
                'message' => 'Нет найден результат'
            ]);
        }

        $result = $result->toArray();

        if ($result[Result::TEST_ID] != $test['id']) {
            return response()->json([
                'success' => false,
                'message' => 'Результат не от этого теста'
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

//        dd($testSlug, $email, $data);

        return response()->json([
            'success' => true
            //'html' => view('edit-answer', ['answer' => $answer, 'questionId' => $questionId])->render()
        ]);
    }
    
    public function showEditTest(string $editSlug)
    {
        $test = Test::getByEditSlug($editSlug);

        $info = [
            'slug' => $test['edit_slug'],
            'editLink' => url("/e/{$test['edit_slug']}"),
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
                $questionRow['answers'][] = [
                    'id' => $answer['id'],
                    'answerText' => $answer['answer'],
                    'isTrue' => $answer['is_true']
                ];
            }

            $questions[] = $questionRow;
        }

//        dd($info);

        return view('edit', [
            'info' => $info,
            'questions' => $questions
        ]);
    }
        
    public function getAnswerForm(Request $request)
    {
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');

        $answer = Answer::newAnswer($questionId);

        return response()->json([
            'success' => true,
            'slug' => $slug,
            'html' => view('edit-answer', ['answer' => $answer, 'questionId' => $questionId])->render()
        ]);
    }
    
    public function getNewQuestionForm(Request $request)
    {
        $editSlug = $request->post('slug');

        $test = Test::getByEditSlug($editSlug);

        $question = Question::newQuestion($test['id']);

        $emptyAnswer1 = Answer::newAnswer($question['id']);
        $emptyAnswer2 = Answer::newAnswer($question['id']);

        $question['answers'] = [
            [
                'id' => $emptyAnswer1['id'],
                'answerText' => $emptyAnswer1['answer'],
                'isTrue' => $emptyAnswer1['is_true']
            ], [
                'id' => $emptyAnswer2['id'],
                'answerText' => $emptyAnswer2['answer'],
                'isTrue' => $emptyAnswer2['is_true']
            ]
        ];

        return response()->json([
            'success' => true,
            'slug' => $editSlug,
            'html' => view('edit-question', ['question' => $question])->render()
        ]);
    }

    public function deleteQuestion(Request $request)
    {
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');

        if (!Question::isLinkedToSlug($questionId, $slug)) {
            return response()->json([
                'success' => false,
                'message' => 'its not your question'
            ]);
        }

        try {
            Answer::deleteByQuestionId($questionId);
            Question::deleteById($questionId);
        } catch (\Exception $err) {
            return response()->json([
                'success' => false,
                'message' => $err->getMessage()
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
    
    public function deleteAnswer(Request $request)
    {
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');
        $answerId = (int) $request->post('answerId');

        if (!Question::isLinkedToSlug($questionId, $slug)) {
            return response()->json([
                'success' => false,
                'message' => 'its not your question'
            ]);
        }

        if (!Answer::isLinkedToQuestion($answerId, $questionId)) {
            return response()->json([
                'success' => false,
                'message' => 'its not your answer'
            ]);
        }

        Answer::deleteById($answerId);

        return response()->json([
            'success' => true
        ]);
    }
}
