@extends('index')

@section('content')
    <div class="container">
        <h2>Результаты тестирования</h2>

        <div id="results-container">
            @foreach ($questions as $question)
                <div id="question-edit-container-{{ $question['id'] }}" class="question-edit-container">
                    <div class="question-edit-title-container">
                        <div style="width: 100%;">
                            <label for="edit-question-{{ $question['id'] }}" >Вопрос:</label>
                            <textarea
                                    style="resize: none;"
                                    class="form-control"
                                    id="edit-question-{{ $question['id'] }}"
                                    maxlength="500"
                                    disabled="disabled"
                                    rows="3">{{ $question['questionText'] }}</textarea>
                        </div>
                    </div>

                    <p>Ответы:</p>
                    <div id="answers-container-{{ $question['id'] }}" class="answers-container">
                        @foreach ($question['answers'] as $answer)
                            <div id="answer-edit-container-{{ $question['id'] }}-{{ $answer['id']}}" class="input-group mb-3 edit-answer-container">
                                <div class="input-group-prepend" >
                                    <div class="input-group-text" data-toggle="tooltip" data-placement="right" title="Верный ответ?">
                                        <input type="checkbox" @if($answer['isTrue']) checked disabled="disabled" @endif>
                                    </div>
                                </div>

                                <input type="text" maxlength="200" disabled="disabled" class="form-control" value="{{ $answer['answerText'] }}" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop