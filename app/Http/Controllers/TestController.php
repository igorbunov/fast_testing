<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
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

    public function showTest(string $testSlug)
    {
        $test = Test::getByTestSlug($testSlug);

        if (is_null($test)) {
            return redirect('main');
        }

        if (!$test->is_active) {
            return redirect('main');
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
