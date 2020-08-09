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
        $data = [
//            'slug' => $slug
        ];

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

//        dd($data, $test->id);

        $test->edit($test->id, $data);

        return response()->json([
            'success' => true,
            'slug' => $slug
        ]);
    }

    public function showTest(string $testSlug)
    {        
        $questions = [
            [
                'id' => 1,
                'questionText' => 'Сколько будет 2+2 если вы ретроградный меркурий?',
                'answers' => [
                    [
                        'id' => 1,
                        'answerText' => 'ответ 1'
                    ], [
                        'id' => 2,
                        'answerText' => 'ответ 2'
                    ], [
                        'id' => 3,
                        'answerText' => 'ответ 3'
                    ]
                ]
            ], [
                'id' => 2,
                'questionText' => 'Кто такой галустя и с чем его едят, если он полетит в америку и побреется а потом опять отрастит бороду?',
                'answers' => [
                    [
                        'id' => 4,
                        'answerText' => 'ответ 4'
                    ], [
                        'id' => 5,
                        'answerText' => 'ответ 5'
                    ], [
                        'id' => 6,
                        'answerText' => 'ответ 6'
                    ]
                ]
            ], [
                'id' => 3,
                'questionText' => '',
                'answers' => [
                    [
                        'id' => 7,
                        'answerText' => ''
                    ], [
                        'id' => 8,
                        'answerText' => ''
                    ]
                ]
            ]
        ];
        
        $info = [
            'slug' => $testSlug,
            'testLink' => url("/{$testSlug}"),
            'description' => 'Привет, это тест про то и вот это, пройди и будешь молодцом!',
            'length' => 1,
            'questions_count' => count($questions)
        ];
        
        
//        dd($info, $questions);
        return view('test', [ 
            'info' => $info,
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
        sleep(2);
        //TODO: remove in db 
        return response()->json([
            'success' => true
        ]);
    }
    
    public function deleteAnswer(Request $request)
    {
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');
        $answerId = (int) $request->post('answerId');
        sleep(2);
        //TODO: remove in db 
        return response()->json([
            'success' => true
        ]);
    } 
    
    public function saveQuestion(Request $request)
    {
        $editSlug = $request->post('slug');
        $params = $request->post('params');

        $test = Test::getByEditSlug($editSlug);
        $questions = Question::getQuestionsByTestId($test['id']);

        $isValidSlug = false;
        $index = -1;

        foreach ($questions as $i => $question) {
            if ($question['id'] == $params['questionId']) {
                $isValidSlug = true;
                $index = $i;
                break;
            }
        }

        if ($isValidSlug) {
            $curQuestion = $questions[$index];

            Question::edit($curQuestion['id'], $params['questionText']);

            foreach ($params['answers'] as $answer) {
                if ($curQuestion['id'] == Answer::getQuestionId($answer['answerId'])) {
                    $answer['isTrue'] = filter_var($answer['isTrue'], FILTER_VALIDATE_BOOLEAN);

                    Answer::edit($answer['answerId'], $answer['answerText'], $answer['isTrue']);
                }
            }

//            dd($curQuestion['id'], $answer['answerId']);

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Это не ваш тест!'
        ]);
    }    
    
    
}
