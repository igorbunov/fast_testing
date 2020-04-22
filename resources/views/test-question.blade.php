<div id="question-test-container-{{ $question['id'] }}" class="question-edit-container">    
    <div class="question-test-container">
        <p>{{ $question['questionText'] }}</p> 
        <p>Ответы:</p>
        <div id="answers-container-{{ $question['id'] }}" class="answers-container">
            @foreach ($question['answers'] as $answer)                    
                @include('test-answer', ['answer' => $answer, 'questionId' => $question['id'] ])                        
            @endforeach
        </div>
    </div>    
</div>