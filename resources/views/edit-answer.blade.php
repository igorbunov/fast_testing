<div class="input-group mb-3 edit-answer-container">
    <div class="input-group-prepend" >
        <div class="input-group-text" data-toggle="tooltip" data-placement="top" title="Верный ответ?">
           <input type="checkbox" @if($answer['isTrue']) checked @endif>
        </div>
    </div>
    
    <input type="text" class="form-control" value="{{ $answer['answerText'] }}" />
    
    <i class="fa fa-trash delete-answer" data-toggle="tooltip" data-placement="top" title="Удалить ответ"></i>
</div>