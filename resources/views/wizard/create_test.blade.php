@extends('index')

@section('content')
    <div class="container">
        <div class="step1-container">
            <div class="questions-container">
                <h4 style="text-align: center;">Шаг 1: Создайте список вопросов</h4>

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
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="QuestionEdit.add(this)"
                        class="btn btn-secondary btn-lg">Добавить вопрос
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">Далее
                </button>
            </div>
        </div>

        <div class="step2-container">
            <h4 style="text-align: center;">Шаг 2: Укажите длительность теста</h4>

            <div class="form-group">
                <label for="formControlRange">Длительность теста: <span id="test-length-value">30</span> (минут)</label>

                <input type="range"
                       list="tickmarks"
                       class="form-control-range"
                       id="test-length"
                       min="5"
                       max="180"
                       step="5"
                       name="test_time_minutes"
                       value="30"
                       oninput="setTestLength(this.value);">

                <datalist id="tickmarks">
                    @for ($i = 10; $i <= 180; $i+=10)
                        <option value="{{ $i }}">
                    @endfor
                </datalist>
            </div>

            <br/>

            <div class="form-group">
                <label for="test-description">Описание (будет видно участникам тестирования)</label>
                <textarea
                        class="form-control"
                        id="test-description"
                        rows="3"
                        placeholder="Введите описание теста"
                        name="description"></textarea>
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="Wizard.back()"
                        class="btn btn-secondary btn-lg">Назад
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">Далее
                </button>
            </div>
        </div>

        <div class="step3-container">
            <h4 style="text-align: center;">Шаг 3: Укажите ваш email</h4>

            <div class="form-group">
                <label for="user-email">Email адрес (туда мы отправим данные для прохождения теста и просмотра результатов)</label>
                <input type="email" class="form-control" id="user-email" aria-describedby="emailHelp" placeholder="Введите ваш email">
                <small id="emailHelp" class="form-text text-muted">Ваш email адрес никто не узнает.</small>
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="Wizard.back()"
                        class="btn btn-secondary btn-lg">Назад
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">Финиш
                </button>
            </div>
        </div>

        <div class="step4-container">
            <h4 style="text-align: center;">Поздравляем, вы успешно создали тест</h4>

            <br/>

            <h5>Данные отправлены на адрес: <span id="email-address"></span></h5>

            <br/>
            <br/>

            <div class="form-group">
                <label>Ссылка для прохождения данного теста:</label>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" onclick="TestEdit.onTestLinkClick();" id="test-slug" style="cursor: pointer;" readonly="true" aria-describedby="btn-copy-test-link">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="TestEdit.copyTestLink();">Скопировать</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop