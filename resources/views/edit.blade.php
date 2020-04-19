@extends('index')

@section('content')

<div class="container">
    @include('edit-main-info', ['info' => $info])
    
    <div class="questions-container">
        <h3 style="text-align: center;">Список вопросов</h3>
        
        @foreach ($questions as $question)
            @include('edit-question', ['question' => $question])
        @endforeach
    </div>
      
    <button 
        type="button" 
        onclick="QuestionEdit.addNew(this)"
        class="btn btn-primary btn-lg add-question-btn">Добавить вопрос</button>
</div>

@stop