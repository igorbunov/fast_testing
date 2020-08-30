<div class="input-group mb-3 edit-answer-container">
    <div class="input-group-prepend" >
        <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="Верный ответ?">
            <input type="checkbox">
        </div>
    </div>

    <input type="text" maxlength="200" placeholder="Введите ответ" class="form-control" />

    <i class="fa fa-trash delete-answer"
       data-toggle="tooltip"
       data-placement="top"
       onclick="AnswerEdit.del(this)"
       title="Удалить ответ">
    </i>
</div>