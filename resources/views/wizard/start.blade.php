@extends('index')

@section('content')

    <div class="container">
        <form class="edit-test-form">
            <input type="hidden" id="slug" value="{{ $info['slug'] }}">
        </form>

        <div class="questions-container">
            <h4 style="text-align: center;">Список вопросов</h4>

            @foreach ($questions as $question)
                @include('edit-question', ['question' => $question])
            @endforeach
        </div>

        <div class="bottom-buttons">
            <button
                    type="button"
                    onclick="QuestionEdit.addNew(this)"
                    class="btn btn-secondary btn-lg">Добавить вопрос</button>


            <button
                    type="button"
                    onclick="Wizard.next()"
                    class="btn btn-primary btn-lg">Далее</button>

        </div>

    </div>

@stop