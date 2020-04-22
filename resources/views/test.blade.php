@extends('index')

@section('content')

<div class="container">
    <div class="card text-center">
        <div class="card-body">
            <h5 class="card-title">Пройдите тест</h5>
            <p class="card-text">{{ $info['description'] }}</p>
          
            <div class="questions-container">
            <h3 style="text-align: center;">Список вопросов</h3>

                @foreach ($questions as $question)
                    @include('test-question', ['question' => $question])
                @endforeach
            </div>
                        
            <div>
                <button type="button" class="btn btn-success">Закончить тест</button>
            </div>
        </div>
    </div>
</div>


@stop