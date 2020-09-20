@extends('index')

@section('content')

<h5 class="card-title">@lang('view.create test')</h5>
<p class="card-text">@lang('view.fast test creation, 5 minutes without registration')</p>

<form method="POST" action="new">
    @csrf
      <button type="submit" class="btn btn-primary btn-lg">@lang('view.start')</button>
</form>

@if (!\Illuminate\Support\Facades\App::isLocale('en'))
<div class="video-container">
<iframe width="420" height="315" src="{{ env('VIDEO_URL', 'https://www.youtube.com/') }}" frameborder="0" allowfullscreen>
</iframe>
@endif

@stop