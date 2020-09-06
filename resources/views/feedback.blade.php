@extends('index')

@section('content')
    <div class="container">
        <div class="question-edit-container">
            <form method="POST" action="add_feedback">
                @csrf

                <div class="form-group" style="text-align: left;">
                    <label for="text">@lang('view.enter your message'):</label>
                    <textarea autofocus maxlength="490" class="form-control" id="text" name="message" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">@lang('view.send')</button>
            </form>
        </div>
    </div>
@stop