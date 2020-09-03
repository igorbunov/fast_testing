@extends('index')

@section('content')
    <div class="container">
        <input type="text" id="sub-question" style="padding: 0;margin: 0;height: 1px;border: 0;" value="" />

        <div class="step1-container">
            <div class="questions-container">
                <h4 style="text-align: center;">@lang('view.Step 1 create a list of questions')</h4>

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
                            <div class="input-group-prepend" >
                                <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="@lang('view.is it correct answer')">
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
                        <div class="input-group mb-3 edit-answer-container">
                            <div class="input-group-prepend" >
                                <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="@lang('view.is it correct answer')">
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
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="QuestionEdit.add(this)"
                        class="btn btn-secondary btn-lg">@lang('view.add question')
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">@lang('view.forward')
                </button>
            </div>
        </div>

        <div class="step2-container">
            <h4 style="text-align: center;">@lang('view.Step 2 set test length')</h4>

            <div class="form-group">
                <label for="formControlRange">@lang('view.test length'): <span id="test-length-value">30</span> (@lang('view.minutes'))</label>

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
                <label for="test-description">@lang('view.description will be seen by testers')</label>
                <textarea
                        class="form-control"
                        id="test-description"
                        rows="3"
                        placeholder="@lang('view.enter test description')"
                        name="description"></textarea>
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="Wizard.back()"
                        class="btn btn-secondary btn-lg">@lang('view.backwards')
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">@lang('view.forward')
                </button>
            </div>
        </div>

        <div class="step3-container">
            <h4 style="text-align: center;">@lang('view.Step 3 set your email')</h4>

            <div class="form-group">
                <label for="user-email">@lang('view.email address we will send data there to pass the test and view the results')</label>
                <input type="email" class="form-control" id="user-email" aria-describedby="emailHelp" placeholder="@lang('view.enter your email')">
                <small id="emailHelp" class="form-text text-muted">@lang('view.Nobody will know your email address')</small>
            </div>

            <div class="bottom-buttons">
                <button
                        type="button"
                        onclick="Wizard.back()"
                        class="btn btn-secondary btn-lg">@lang('view.backwards')
                </button>

                <button
                        type="button"
                        onclick="Wizard.next()"
                        class="btn btn-primary btn-lg">@lang('view.finish')
                </button>
            </div>
        </div>

        <div class="step4-container">
            <h4 style="text-align: center;">@lang('view.congratulations you have successfully created a test')</h4>

            <br/>

            <h5>@lang('view.data sende to your email'): <span id="email-address"></span></h5>

            <br/>
            <br/>

            <div class="form-group">
                <label>@lang('view.link for passing this test'):</label>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" onclick="TestEdit.onTestLinkClick();" id="test-slug" style="cursor: pointer;" readonly="true" aria-describedby="btn-copy-test-link">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="TestEdit.copyTestLink();">@lang('view.copy')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop