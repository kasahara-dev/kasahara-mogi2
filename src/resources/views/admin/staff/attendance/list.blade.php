@extends('layout.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/attendance/list.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">{{ $name }}さんの勤怠</h1>
        <div class="selector">
            <a class="selector__pre-month"
                href="/admin/attendance/staff/{{ $id }}/?year={{ $preYear }}&month={{ $preMonth }}">
                <p class="selector__arrow">←</p>
                <p class="selector__month">前月</p>
            </a>
            <div class="calendar">
                <label for="monthPicker">
                    <img src="{{ asset('/img/calendar-regular-full.svg') }}" class="calendar__img" alt="カレンダー画像">
                </label>
                <input type="text" id="monthPicker" value="{{ $year . '/' . sprintf('%02d', $month) }}"
                    class="calendar__input" readonly="readonly" />
            </div>
            <a class="selector__next-month"
                href="/admin/attendance/staff/{{ $id }}/?year={{ $nextYear }}&month={{ $nextMonth }}">
                <p class="selector__month">翌月</p>
                <p class="selector__arrow">→</p>
            </a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="table__header">日付</th>
                    <th class="table__header">出勤</th>
                    <th class="table__header">退勤</th>
                    <th class="table__header">休憩</th>
                    <th class="table__header">合計</th>
                    <th class="table__header">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dayList as $listLine)
                    <tr>
                        <td class="table__data">{{ $listLine['day'] }}</td>
                        <td class="table__data">@if($listLine['start']){{ $listLine['start']->format('H:i') }}@endif</td>
                        <td class="table__data">
                            @if($listLine['end'])
                                @if(\Carbon\Carbon::parse($listLine['start'])->startOfDay()->lt(\Carbon\Carbon::parse($listLine['end'])->startOfDay()))
                                    24:00
                                @else
                                    {{ $listLine['end']->format('H:i') }}
                                @endif
                            @endif
                        </td>
                        <td class="table__data">
                            @if($listLine['hasRests']){{ sprintf('%02d', $listLine['restHours']) . ':' . sprintf('%02d', $listLine['restMinutes']) }}@elseif($listLine['end'])
                            00:00
                            @endif
                        </td>
                        <td class="table__data">
                            @if($listLine['end']){{ sprintf('%02d', $listLine['workHours']) . ':' . sprintf('%02d', $listLine['workMinutes']) }}@endif
                        </td>
                        @if($listLine['end'])
                            @if($listLine['pending'])
                                <td class="table__data">
                                    <a href="/admin/requested_attendance/{{ $listLine['sendAttendanceId'] }}/?pending=true"
                                        class="table__data--active">詳細</a>
                                </td>
                            @else
                                <td class="table__data">
                                    <a href="/admin/attendance/{{ $listLine['sendAttendanceId'] }}" class="table__data--active">詳細</a>
                                </td>

                            @endif
                        @else
                            <td class="table__data">詳細</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        <form action="/admin/attendance/staff/{{ $id }}/export">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <div class="table__bottom">
                <button type="submit" class="btn__submit">CSV出力</button>
            </div>
        </form>
    </div>
    <script>
        var setYear = {{ $year }};
        var setMonth = {{ $month }};
        const setId ={{ $id }};
    </script>
    <script src="{{ asset('/js/admin/selectMonth.js') }}"></script>
@endsection