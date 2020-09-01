@extends('index')

@section('content')

<div id="test-preview-container" class="container" style="padding: 20px; border: 1px solid blue;" data-slug="{{ $info['slug'] }}">
    @if(isset($info['description']))
        <p class="card-text">@lang('view.description'): {{ $info['description'] }}</p>
    @endif

    <input type="email"
        class="form-control"
        style="margin: 16px 0;"
        id="tested-email"
        placeholder="@lang('view.enter your email')"
        maxlength="50"
        onkeyup="Test.onEmailEnter(event)"
        required>
    <p id="test-timer">@lang('view.time for testing'): {{ $info['length'] }} @lang('view.minutes')</p>

    <button type="button" id="start-testing"
        class="btn btn-lg btn-success"
        onclick="Test.start(this);">@lang('view.start')</button>
</div>

<div id="test-process-container" class="container" style="display: none;padding: 20px;" data-resultid="0" data-slug="{{ $info['slug'] }}">
    <div class="questions-container">
        @foreach ($questions as $question)
            <div class="t-question-test-container">
                <p style="padding: 10px;font-size: 26px;text-align: left;    margin-bottom: 0px;">{{ $question['question'] }}</p>
                <div class="t-answers-container">
                    @foreach ($question['answers'] as $answer)
                        <div
                                class="t-answer-item"
                                data-questionid="{{$question['id']}}"
                                data-answerid="{{$answer['id']}}"
                                data-checked="false"
                                onclick="Test.answerClick(this, '{{$question['id']}}', '{{$answer['id']}}')"
                        >
                            <i class="fa fa-check" style="color: gainsboro; font-size: 32px; margin: 0 4px;"></i>
                            <div style="margin-left: 5px;">{{ $answer['answer'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div>
        <button id="finish-test" type="button" class="btn btn-success" onclick="Test.finish(this);">@lang('view.finish test')</button>
    </div>
</div>

@stop