<div id="answer-edit-container-{{ $questionId }}-{{ $answer['id']}}" class="input-group mb-3 edit-answer-container">
    <div class="input-group-prepend" >
        <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="Верный ответ?">
           <input type="checkbox" @if($answer['isTrue']) checked @endif>
        </div>
    </div>
    
    <input type="text" maxlength="200" class="form-control" value="{{ $answer['answerText'] }}" />
    
    <i class="fa fa-trash delete-answer"
       data-toggle="tooltip"
       data-placement="top" 
       onclick="AnswerEdit.delete(this, {{ $questionId }}, {{ $answer['id']}} )"
       title="Удалить ответ"></i>
</div>