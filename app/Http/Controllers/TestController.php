<?php

namespace App\Http\Controllers;

use App\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function startNewTest(Request $request)
    {
        $editSlug = md5(date('YmdHis'));
        
        return redirect('/edit/' . $editSlug);
    }
    public function showEditTest(string $slug)
    {
        $info = [
            'editLink' => url("/edit/{$slug}"),
            'testLink' => url("/test/{$slug}"),
            'description' => 'atata',
            'email' => 'mepata@yandex.ru',
            'length' => 60,
            'isActive' => 1
        ];
        
        $questions = [
            [
                'number' => 1,
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
                'number' => 2,
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
            ]
        ];
        
        return view('edit', [
            'info' => $info,            
            'questions' => $questions
        ]);
    }
    
    
}
