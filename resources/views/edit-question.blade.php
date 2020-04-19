<div id="question-edit-container-{{ $question['id'] }}" class="question-edit-container">    
    <div class="question-edit-title-container">
        <div style="width: 100%;">
            <label for="edit-question-{{ $question['id'] }}" >Вопрос:</label>
            <textarea 
                style="resize: none;" 
                class="form-control" 
                id="edit-question-{{ $question['id'] }}" 
                maxlength="500"
                placeholder="Введите вопрос"
                rows="3">{{ $question['questionText'] }}</textarea>
        </div>
    </div>
    
    <p>Ответы:</p>
    <div id="answers-container-{{ $question['id'] }}" class="answers-container">
        @foreach ($question['answers'] as $answer)                    
            @include('edit-answer', ['answer' => $answer, 'questionId' => $question['id'] ])                        
        @endforeach
    </div>
    
    <div class="add-answer-btn btn-primary"
         data-toggle="tooltip"
         data-placement="top"
         onclick="AnswerEdit.addNew(this, {{ $question['id'] }})"
         title="Добавить ответ">
        <i class="fa fa-plus"></i>        
    </div>
    
    <div class="question-main-buttons-container">
        <button type="button" 
                class="btn btn-success"
                data-toggle="tooltip" 
                data-placement="top"                 
                onclick="QuestionEdit.save(this, {{ $question['id'] }})"
                title="Сохранить данные">Сохранить</button>
        <button type="button"
                class="btn btn-danger"
                data-toggle="tooltip"
                data-placement="top"
                onclick="QuestionEdit.delete(this, {{ $question['id'] }})"
                title="Удалить вопрос">Удалить</button>        
    </div>
</div>