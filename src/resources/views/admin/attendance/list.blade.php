@extends('layout.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/list.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
    <script src="https://rawgit.com/jquery/jquery-ui/master/ui/i18n/datepicker-ja.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">{{ $year }}年{{ $month }}月{{ $day }}日の勤怠</h1>
        <div class="selector">
            @if (is_null($page))
                <a class="selector__pre-day"
                    href="/admin/attendance/list?year={{ $preYear }}&month={{ $preMonth }}&day={{ $preDay }}">
                    <p class="selector__arrow">←</p>
                    <p class="selector__day">前日</p>
                </a>

            @else
                <a class="selector__pre-day"
                    href="/admin/attendance/list?year={{ $preYear }}&month={{ $preMonth }}&day={{ $preDay }}&page={{ $page }}">
                    <p class="selector__arrow">←</p>
                    <p class="selector__day">前日</p>
                </a>

            @endif
            <div class="calendar">
                <label for="datePicker">
                    <img src="{{ asset('/img/calendar-regular-full.svg') }}" class="calendar__img" alt="カレンダー画像">
                </label>
                <input type="text" id="datePicker"
                    value="{{ $year . '/' . sprintf('%02d', $month) . '/' . sprintf('%02d', $day) }}"
                    class="calendar__input" readonly="readonly" />
            </div>
            @if(is_null($page))
                <a class="selector__next-day"
                    href="/admin/attendance/list?year={{ $nextYear }}&month={{ $nextMonth }}&day={{ $nextDay }}">
                    <p class="selector__day">翌日</p>
                    <p class="selector__arrow">→</p>
                </a>
            @else
                <a class="selector__next-day"
                    href="/admin/attendance/list?year={{ $nextYear }}&month={{ $nextMonth }}&day={{ $nextDay }}&page={{ $page }}">
                    <p class="selector__day">翌日</p>
                    <p class="selector__arrow">→</p>
                </a>
            @endif
        </div>
        <div>{{ $usersList->links() }}</div>
        <table class="table">
            <thead class="table-header">
                <tr>
                    <th class="table-header__name--wide">名前</th>
                    <th class="table-header__name">出勤</th>
                    <th class="table-header__name">退勤</th>
                    <th class="table-header__name">休憩</th>
                    <th class="table-header__name">合計</th>
                    <th class="table-header__name">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usersList as $userList)
                    <tr class="table-line">
                        <td class="table-line__data">{{ $userList['name'] }}</td>
                        <td class="table-line__data">
                            @if(!is_null($userList['start']))
                                {{ \Carbon\Carbon::parse($userList['start'])->format('H:i') }}
                            @endif
                        </td>
                        @if(!is_null($userList['end']))
                            @if(\Carbon\Carbon::parse($userList['start'])->startOfDay()->lt(\Carbon\Carbon::parse($userList['end'])->startOfDay()))
                                <td class="table-line__data">24:00</td>
                            @else
                                <td class="table-line__data">{{ \Carbon\Carbon::parse($userList['end'])->format('H:i') }}</td>
                            @endif
                        @else
                            <td class="table-line__data"></td>
                        @endif
                        @if($userList['hasRests'])
                            <td class="table-line__data">
                                {{ sprintf('%02d', $userList['restHours']) }}:{{ sprintf('%02d', $userList['restMinutes']) }}
                            </td>
                        @else
                            <td class="table-line__data"></td>
                        @endif
                        @if(!is_null($userList['end']))
                            <td class="table-line__data">
                                {{ sprintf('%02d', $userList['workHours']) }}:{{ sprintf('%02d', $userList['workMinutes']) }}
                            </td>
                            @if($userList['pending'])
                                <td class="table-line__data"><a href="/admin/requested_attendance/{{ $userList['sendAttendanceId'] }}"
                                        class="table-line__data--link">詳細</a></td>
                            @else
                                <td class="table-line__data"><a href="/admin/attendance/{{ $userList['sendAttendanceId'] }}"
                                        class="table-line__data--link">詳細</a></td>
                            @endif
                        @else
                            <td class="table-line__data"></td>
                            <td class="table-line__data">詳細</td>
                        @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        var setYear = {{ $year }};
        var setMonth = {{ $month }};
        var setDay ={{ $day }};
    </script>
    <script src="{{ asset('/js/selectDay.js') }}"></script>
@endsection