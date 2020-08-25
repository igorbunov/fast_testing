<form class="edit-test-form">
    <input type="hidden" id="slug" value="{{ $info['slug'] }}">
    
    <div class="form-group">
        <label>Ссылка для прохождения данного теста:</label>

        <div class="input-group mb-3">
            <input onclick="TestEdit.onLinkClick('{{ $info['testLink'] }}');"  type="text" class="form-control" style="cursor: pointer;" readonly="true" aria-describedby="btn-copy-test-link" value="{{ $info['testLink'] }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" onclick="TestEdit.copyEditLink('{{ $info['testLink'] }}');">Скопировать</button>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>Ссылка на редактирование и просмотра статистики (только для Вас!):</label>
        
        <div class="input-group mb-3">
            <input onclick="TestEdit.onLinkClick('{{ $info['editLink'] }}');" type="text" class="form-control" style="cursor: pointer;" readonly="true" aria-describedby="btn-copy-edit-link" value="{{ $info['editLink'] }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" onclick="TestEdit.copyTestingLink('{{ $info['editLink'] }}');">Скопировать</button>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="exampleFormControlTextarea1">Описание (будет видно участникам тестирования)</label>
        <textarea
                class="form-control"
                id="exampleFormControlTextarea1"
                rows="3"
                name="description"
        >{{ $info['description'] }}</textarea>
    </div>

    <div class="form-group">
        <label for="formControlRange">Длительность теста: <span id="test-length-value">{{ $info['length'] }}</span> (минут)</label>

        <input type="range"
               list="tickmarks"
               class="form-control-range"
               id="test-length"
               min="5"
               max="180"
               step="5"
               name="test_time_minutes"
               value="{{ $info['length'] }}"
               oninput="setTestLength(this.value);">

        <datalist id="tickmarks">
            @for ($i = 10; $i <= 180; $i+=10)
              <option value="{{ $i }}">
            @endfor
        </datalist>
    </div>

    <div style="font-weight: bold; font-size: 24px;">
        @if ($info['isActive'])
            <span>Статус теста: активен</span>

            <button type="button"
                class="btn btn-danger btn-sm"
                data-toggle="tooltip"
                data-placement="top"
                onclick="TestEdit.deactivate()"
                title="Деактивировать">Деактивировать</button>
        @else
            <span style="color: red;">Статус теста: не активен</span>

            <button type="button"
                class="btn btn-success btn-lg"
                data-toggle="tooltip"
                data-placement="top"
                onclick="TestEdit.activate()"
                title="Активировать">Активировать</button>
        @endif
    </div>

    <div style="margin-top: 34px;">
        @if(empty($info['isActive']))
        <button type="button"
                class="btn btn-success btn-lg"
                data-toggle="tooltip"
                data-placement="top"
                onclick="TestEdit.save(this)"
                title="Сохранить данные">Сохранить данные</button>
        @endif
    </div>


</form>
