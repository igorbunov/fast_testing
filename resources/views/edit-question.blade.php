<div class="question-edit-container">    
    <div class="question-edit-title-container">
        <p>Вопрос: {{ $question['questionText'] }}</p>
    
        <i class="fa fa-trash delete-question" data-toggle="tooltip" data-placement="top" title="Удалить вопрос"></i>
    </div>
    
    <p>Ответы:</p>
    <div>
        @foreach ($question['answers'] as $answer)                    
            @include('edit-answer', ['answer' => $answer])                        
        @endforeach
    </div>
    <button type="button" class="btn btn-primary">Добавить ответ</button>
</div>