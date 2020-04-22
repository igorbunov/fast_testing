<div id="answer-edit-container-{{ $questionId }}-{{ $answer['id']}}" class="input-group mb-3 edit-answer-container">
    <div class="input-group-prepend" >
        <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="Верный ответ?">
           <input type="checkbox">
        </div>
        <div style="margin-left: 5px;">{{ $answer['answerText'] }}</div>
    </div>    
</div>