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
        </div>
        <div class="card-footer text-muted">
          <!--2 days ago-->
        </div>
    </div>


@stop