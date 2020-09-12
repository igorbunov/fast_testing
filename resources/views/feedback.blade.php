@extends('index')

@section('content')
    <div class="container">
        <div class="question-edit-container">

            <form id="feedback-form">
                @csrf
                <div class="form-group" style="text-align: left;">
                    <label for="feedback-email">@lang('view.enter your email'):</label>
                    <input type="email" id="feedback-email" name="email" required>
                </div>

                <div class="form-group" id="feedback-name-group" style="text-align: left;">
                    <label for="feedback-name">@lang('view.enter your name'):</label>
                    <input type="text" name="name" id="feedback-name">
                </div>

                <div class="form-group" style="text-align: left;">
                    <label for="feedback-message">@lang('view.enter your message'):</label>
                    <textarea autofocus maxlength="490" class="form-control" id="feedback-message" name="message" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">@lang('view.send')</button>
            </form>
        </div>
    </div>
@stop