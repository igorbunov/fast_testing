<div class="question-edit-container">
    <div class="question-edit-title-container">
        <div style="width: 100%;">
            <label>@lang('view.question'):</label>
            <textarea
                    style="resize: none;"
                    class="form-control"
                    maxlength="500"
                    placeholder="@lang('view.enter question')"
                    rows="3"></textarea>
        </div>
    </div>

    <p>@lang('view.answers'):</p>
    <div class="answers-container">
        <div class="input-group mb-3 edit-answer-container">
            @if ($isQuestionare == 0)
            <div class="input-group-prepend" >
                <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="@lang('view.is it correct answer')">
                    <input type="checkbox">
                </div>
            </div>
            @endif

            <input type="text" maxlength="200" placeholder="@lang('view.enter answer')" class="form-control" />

            <i class="fa fa-trash delete-answer"
               data-toggle="tooltip"
               data-placement="top"
               onclick="AnswerEdit.del(this)"
               title="@lang('view.delete answer')">
            </i>
        </div>
        <div class="input-group mb-3 edit-answer-container">
            @if ($isQuestionare == 0)
            <div class="input-group-prepend" >
                <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="@lang('view.is it correct answer')">
                    <input type="checkbox">
                </div>
            </div>
            @endif

            <input type="text" maxlength="200" placeholder="@lang('view.enter answer')" class="form-control" />

            <i class="fa fa-trash delete-answer"
               data-toggle="tooltip"
               data-placement="top"
               onclick="AnswerEdit.del(this)"
               title="@lang('view.delete answer')">
            </i>
        </div>
    </div>

    <div class="question-buttons-container">
        <div class="add-answer-btn btn-secondary"
             data-toggle="tooltip"
             data-placement="top"
             onclick="AnswerEdit.add(this)"
             title="@lang('view.add answer')">
            @lang('view.add answer')
        </div>
        <button type="button"
                class="btn btn-danger"
                data-toggle="tooltip"
                data-placement="top"
                onclick="QuestionEdit.del(this)"
                title="@lang('view.delete question')">@lang('view.delete question')
        </button>
    </div>
</div>