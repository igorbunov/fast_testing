@extends('index')

@section('content')

<div id="test-preview-container" class="container">
    <div class="card text-center" style="padding: 20px;">
        <p class="card-text">{{ $info['description'] }}</p>
            
        <form>
            <div class="form-group row">
                <label for="tested-name" class="col-sm-2 col-form-label">Ваше имя *</label>
                <div class="col-sm-10">
                  <input type="text" 
                         class="form-control" 
                         id="tested-name" 
                         placeholder="Введите ваше имя"
                         maxlength="50"
                         required
                         >
                </div>
            </div>
            <div class="form-group">
                <p>Время на прохождение: {{ $info['length'] }} минут</p>
            </div>
            <div class="form-group">
                <p>Всего вопросов:  {{ $info['questions_count'] }} </p>
            </div>
          </form>
        <div>
            <button type="button" 
                    class="btn btn-lg btn-success"
                    onclick="Test.start({{ $info['length'] }});"
                    >Начать</button>
        </div>
    </div>
</div>

<div id="test-process-container" class="container" style="display: none;padding: 20px;">
   <div class="card text-center" style="padding: 20px;">
        <p class="card-text">{{ $info['description'] }}</p>

        <div class="form-group">
            <p>Оставшееся время: <span id="test-timer"></span></p>
            </div>
        
        
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

@stop