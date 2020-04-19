<form>
    <input type="hidden" id="slug" value="{{ $info['slug'] }}">
    
    <div class="form-group">
        <label>Ссылка для прохождения данного теста:</label>
        <!--<a href="{{ $info['testLink'] }}" target="_blank">{{ $info['testLink'] }}</a>-->
        
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly="true" aria-describedby="btn-copy-test-link" value="{{ $info['testLink'] }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="btn-copy-test-link">Скопировать</button>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>Ссылка на редактирование и просмотра статистики (только для Вас!):</label>
        <!--<a href="{{ $info['editLink'] }}" target="_blank">{{ $info['editLink'] }}</a>-->
        
        <div class="input-group mb-3">
            <input type="text" class="form-control" readonly="true" aria-describedby="btn-copy-edit-link" value="{{ $info['editLink'] }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="btn-copy-edit-link">Скопировать</button>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="exampleFormControlTextarea1">Описание (будет видно участникам тестирования)</label>
        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3">{{ $info['description'] }}</textarea>
    </div>

    <div class="form-group">
        <label for="formControlRange">Длительность теста: <span id="test-length-value">{{ $info['length'] }}</span> (минут)</label>

        <input type="range" list="tickmarks" class="form-control-range"
             id="test-length" min="10" max="180" step="10" value="{{ $info['length'] }}"
             oninput="setTestLength(this.value);">

        <datalist id="tickmarks">
            @for ($i = 10; $i <= 180; $i+=10)
              <option value="{{ $i }}">
            @endfor
        </datalist>
    </div>

    Статус теста: <input 
        type="checkbox" 
        data-onstyle="success" 
        data-offstyle="outline-danger"
        data-toggle="toggle" 
        data-on="Активен" 
        data-off="Не активен" @if ($info['isActive']) checked @endif >
</form>
