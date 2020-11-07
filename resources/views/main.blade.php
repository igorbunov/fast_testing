@extends('index')

@section('content')

<div class="create-blocks-container">

    <div class="create-online-test-block">
        <h5 class="card-title">@lang('view.create test')</h5>
        <p class="card-text">@lang('view.fast test creation, 5 minutes without registration')</p>

        <button
            type="submit"
            onclick="Page.showCreateTest();"
            class="btn btn-success btn-lg"
        >
            @lang('view.start')
        </button>
    </div>

    <div class="create-online-questionnaire-block">
        <h5 class="card-title">@lang('view.create questionnaire')</h5>
        <p class="card-text">@lang('view.fast questionnaire creation, 5 minutes without registration')</p>

        <button
            type="submit"
            onclick="Page.showCreateQuestionare();"
            class="btn btn-success btn-lg"
        >
            @lang('view.start')
        </button>
    </div>

</div>



@if (!\Illuminate\Support\Facades\App::isLocale('en'))
    <div class="video-container">
        <iframe width="420" height="315" src="{{ env('VIDEO_URL', 'https://www.youtube.com/') }}" frameborder="0" allowfullscreen>
        </iframe>
@endif

@stop