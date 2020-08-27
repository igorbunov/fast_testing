@extends('index')

@section('content')
    <div class="container">
        <form class="edit-test-form">
            <input type="hidden" id="slug" value="{{ $info['slug'] }}">

            <div class="form-group">
                <label for="exampleFormControlTextarea1">Описание</label>
                <textarea
                        class="form-control"
                        id="exampleFormControlTextarea1"
                        rows="3"
                        disabled="disabled"
                        name="description"
                >{{ $info['description'] }}</textarea>
            </div>

            <div class="form-group">
                <label for="formControlRange">Длительность теста: <span id="test-length-value">{{ $info['length'] }}</span> (минут)</label>

            </div>

            <div>
                @if ($info['isActive'])
                    <span>Статус теста: активен</span>
                @else
                    <span style="color: red;">Статус теста: не активен</span>
                @endif
            </div>

            <h3 style="margin: 20px 0;">Результаты тестирования</h3>

            <div id="summary-results-by-users" >
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Дата завершения</th>
                        <th scope="col">Email</th>
                        <th scope="col">Результат</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($results as $index => $result)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $result->end_dt }}</td>
                            <td>{{ $result->email }}</td>
                            <td>{{ $result->report['correct'] }} / {{ $result->report['total'] }}</td>
                            <td><a href="/r/{{ $info['slug'] }}/{{ $result->id }}">подробнее</a></td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>


            </div>
        </form>
    </div>
@stop
