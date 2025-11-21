@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
    <!--jQuery JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js">
    </script>
    <!--jQuery UI JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
    <!--jQuery UI CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/base/jquery-ui.min.css">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">勤怠一覧</h1>
        <div class="selector">
            <a class="selector__pre-month" href="/attendance/list?year={{ $preYear }}&month={{ $preMonth }}">
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
            <a class="selector__next-month" href="/attendance/list?year={{ $nextYear }}&month={{ $nextMonth }}">
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
                        <td class="table__data">@if($listLine['end']){{ $listLine['end']->format('H:i') }}@endif</td>
                        <td class="table__data">
                            @if($listLine['end']){{ sprintf('%02d', $listLine['restHours']) . ':' . sprintf('%02d', $listLine['restMinutes']) }}@endif
                        </td>
                        <td class="table__data">
                            @if($listLine['end']){{ sprintf('%02d', $listLine['workHours']) . ':' . sprintf('%02d', $listLine['workMinutes']) }}@endif
                        </td>
                        @if($listLine['end'])
                            <td class="table__data table__data--detail">
                                <a href="/attendance/detail/{{ $listLine['attendanceId'] }}" class="table__data--active">詳細</a>
                            </td>
                        @else
                            <td class="table__data table__data--detail">詳細</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        var setYear = {{ $year }};
        var setMonth = {{ $month }};
    </script>
    <script src="{{ asset('/js/selectMonth.js') }}"></script>
@endsection