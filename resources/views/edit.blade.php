@extends('index')

@section('content')

<div class="container">
    @include('edit-main-info', ['info' => $info])

    <div class="results-preview-container">
        <p style="margin-right: 10px; font-size: 20px;">Тестов пройдено: 0 шт</p>
        <button
            type="button"
            onclick="Results.show();"
            class="btn btn-primary">Посмотреть результаты</button>
    </div>
    
    <div class="questions-container">
        <h3 style="text-align: center;">Список вопросов</h3>
        
        @foreach ($questions as $question)
            @include('edit-question', ['question' => $question])
        @endforeach
    </div>

    @if(empty($info['isActive']))
    <button 
        type="button" 
        onclick="QuestionEdit.addNew(this)"
        class="btn btn-primary btn-lg add-question-btn">Добавить вопрос</button>
    @endif

</div>

@stop