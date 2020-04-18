<form>
    <div class="form-group">
        <label for="exampleFormControlInput1">Ваш Email (нужен для редактирования и просмотра статистики теста)</label>
        <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com" value="{{ $info['email'] }}">
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
