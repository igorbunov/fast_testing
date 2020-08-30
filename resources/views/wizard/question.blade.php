<div class="question-edit-container">
    <div class="question-edit-title-container">
        <div style="width: 100%;">
            <label>Вопрос:</label>
            <textarea
                    style="resize: none;"
                    class="form-control"
                    maxlength="500"
                    placeholder="Введите вопрос"
                    rows="3"></textarea>
        </div>
    </div>

    <p>Ответы:</p>
    <div class="answers-container">
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
    </div>

    <div class="question-buttons-container">
        <div class="add-answer-btn btn-secondary"
             data-toggle="tooltip"
             data-placement="top"
             onclick="AnswerEdit.add(this)"
             title="Добавить ответ">
            Добавить ответ
        </div>
        <button type="button"
                class="btn btn-danger"
                data-toggle="tooltip"
                data-placement="top"
                onclick="QuestionEdit.del(this)"
                title="Удалить вопрос">Удалить вопрос
        </button>
    </div>
</div>