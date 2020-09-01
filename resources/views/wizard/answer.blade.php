<div class="input-group mb-3 edit-answer-container">
    <div class="input-group-prepend" >
        <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="@lang('view.is it correct answer')?">
            <input type="checkbox">
        </div>
    </div>

    <input type="text" maxlength="200" placeholder="@lang('view.enter answer')" class="form-control" />

    <i class="fa fa-trash delete-answer"
       data-toggle="tooltip"
       data-placement="top"
       onclick="AnswerEdit.del(this)"
       title="@lang('view.delete answer')">
    </i>
</div>