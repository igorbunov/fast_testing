<?php

namespace App\Http\Controllers;

use App\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function startNewTest(Request $request)
    {
        $editSlug = md5(date('YmdHis').Str::uuid()->toString());
        
        //TODO: check existing
        
        return redirect('/edit/' . $editSlug);
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
        $testSlug = substr(md5(date('YmdHis').$editSlug.Str::uuid()->toString()), 0, 10);
        
        $info = [
            'slug' => $editSlug,
            'editLink' => url("/edit/{$editSlug}"),
            'testLink' => url("/{$testSlug}"),
            'description' => 'atata',
            'length' => 60,
            'isActive' => 1
        ];
        
        $questions = [
            [
                'id' => 1,
                'questionText' => 'Сколько будет 2+2 если вы ретроградный меркурий?',
                'answers' => [
                    [
                        'id' => 1,
                        'answerText' => 'ответ 1',
                        'isTrue' => false
                    ], [
                        'id' => 2,
                        'answerText' => 'ответ 2',
                        'isTrue' => true
                    ], [
                        'id' => 3,
                        'answerText' => 'ответ 3',
                        'isTrue' => false
                    ]
                ]
            ], [
                'id' => 2,
                'questionText' => 'Кто такой галустя и с чем его едят, если он полетит в америку и побреется а потом опять отрастит бороду?',
                'answers' => [
                    [
                        'id' => 4,
                        'answerText' => 'ответ 4',
                        'isTrue' => true
                    ], [
                        'id' => 5,
                        'answerText' => 'ответ 5',
                        'isTrue' => false
                    ], [
                        'id' => 6,
                        'answerText' => 'ответ 6',
                        'isTrue' => true
                    ]
                ]
            ], [
                'id' => 3,
                'questionText' => '',
                'answers' => [
                    [
                        'id' => 7,
                        'answerText' => '',
                        'isTrue' => false
                    ], [
                        'id' => 8,
                        'answerText' => '',
                        'isTrue' => false
                    ]
                ]
            ]
        ];
        
        return view('edit', [
            'info' => $info,            
            'questions' => $questions
        ]);
    }
        
    public function getAnswerForm(Request $request)
    {
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');
        
        sleep(2);
        //TODO: create answer in db and get it
        
        $answer = [
            'id' => 11,
            'answerText' => '',
            'isTrue' => false
        ];
                
        return response()->json([
            'success' => true,
            'slug' => $slug,
            'html' => view('edit-answer', ['answer' => $answer, 'questionId' => $questionId])->render()
        ]);
    }
    
    public function getQuestionForm(Request $request)
    {
        $slug = $request->post('slug');
        
        sleep(2);
        //TODO: create question in db and get it
        $question = [
            'id' => 4,
            'questionText' => '',
            'answers' => [
                [
                    'id' => 9,
                    'answerText' => '',
                    'isTrue' => false
                ], [
                    'id' => 10,
                    'answerText' => '',
                    'isTrue' => false
                ]
            ]
        ];
                
        return response()->json([
            'success' => true,
            'slug' => $slug,
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
        $slug = $request->post('slug');
        $questionId = (int) $request->post('questionId');
        $answerId = (int) $request->post('answerId');
        sleep(2);
        //TODO: remove in db 
        return response()->json([
            'success' => true
        ]);
    }    
    
    
}
