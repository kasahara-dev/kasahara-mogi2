@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
    <!--jQuery JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
    </script>
    <!--jQuery UI JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <!--jQuery UI CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
@endsection

@section('content')
    <div class="list-area">
        <h1 class="list-title">勤怠一覧</h1>
        <div class="list-select-area">
            <a class="list-select" href="/attendance/list?year={{ $preYear }}&month={{ $preMonth }}">
                <p class="list-select-arrow">←</p>
                <p class="list-arrow-month">前月</p>
            </a>
            <div class="list-select-calendar">
                <label for="monthPicker">
                    <img src="{{ asset('/img/calendar-regular-full.svg') }}" class="calender-img" alt="カレンダー画像">
                </label>
                <input type="text" id="monthPicker" value="{{ $year . '/' . sprintf('%02d', $month) }}"
                    class="list-select-month" readonly="readonly" />
            </div>
            <a class="list-select" href="/attendance/list?year={{ $postYear }}&month={{ $postMonth }}">
                <p class="list-arrow-month">翌月</p>
                <p class="list-select-arrow">→</p>
            </a>
        </div>
    </div>
    <script>
        var setYear = {{ $year }};
        var setMonth = {{ $month }};
    </script>
    <script src="{{ asset('/js/selectMonth.js') }}"></script>
@endsection