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
            <a class="selector__pre-day" href="/admin/attendance/list?year={{ $preYear }}&month={{ $preMonth }}&day={{ $preDay }}">
                <p class="selector__arrow">←</p>
                <p class="selector__day">前日</p>
            </a>
            <div class="calendar">
                <label for="datePicker">
                    <img src="{{ asset('/img/calendar-regular-full.svg') }}" class="calendar__img" alt="カレンダー画像">
                </label>
                <input type="text" id="datePicker"
                    value="{{ $year . '/' . sprintf('%02d', $month) . '/' . sprintf('%02d', $day) }}"
                    class="calendar__input" readonly="readonly" />
            </div>
            <a class="selector__next-day"
                href="/admin/attendance/list?year={{ $nextYear }}&month={{ $nextMonth }}&day={{ $nextDay }}">
                <p class="selector__day">翌日</p>
                <p class="selector__arrow">→</p>
            </a>
        </div>
    </div>
    <script>
        var setYear = {{ $year }};
        var setMonth = {{ $month }};
        var setDay ={{ $day }};
    </script>
    <script src="{{ asset('/js/selectDay.js') }}"></script>
@endsection