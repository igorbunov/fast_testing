@extends('index')

@section('content')

<div class="container">
    @include('edit-main-info', ['info' => $info])
    
    <div class="questions-container">
        <p style="margin-top: 20px; text-align: center;">Список вопросов</p>
        
        @foreach ($questions as $question)
            @include('edit-question', ['question' => $question])
        @endforeach
                
        <button type="button" class="btn btn-primary add-question-btn">Добавить вопрос</button>
    </div>
</div>

@stop