@extends('index')

@section('content')

    <div class="card text-center">
        <div class="card-header">
          <!--Featured-->
        </div>
        <div class="card-body">
            <h5 class="card-title">Создайте тест</h5>
            <p class="card-text">Быстрое создание тестов, за 5 минут, без регистрации.</p>

            <form method="POST" action="create_new_test">
                @csrf
                  <button type="submit" class="btn btn-primary btn-lg">Начать</button>
            </form>

            <div class="video-container">
                <iframe width="420" height="315" src="{{ env('VIDEO_URL', 'https://www.youtube.com/') }}" frameborder="0" allowfullscreen>

                </iframe>
            </div>
        </div>
        <div class="card-footer text-muted" style="height: calc(100vh - 672px);">
          <!--2 days ago-->
        </div>
    </div>


@stop