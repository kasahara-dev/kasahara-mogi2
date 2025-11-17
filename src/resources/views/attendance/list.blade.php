@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
    <!--jQuery JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" />
    </script>
    <!--jQuery UI JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!--jQuery UI CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">
@endsection

@section('content')
    <div class="list-area">
        <h1 class="list-title">勤怠一覧</h1>
        <div class="list-select-area">
            <div class="list-select">
                <p class="list-select-arrow">←</p>
                <p class="list-arrow-month">前月</p>
            </div>
            <div class="list-select-calendar">
                <input id="list_select_month" type="month" name="list-select-month" class="list-select-month"
                    value="{{ date('Y-m') }}" />
                <input type="text" id="datepicker">
            </div>
            <div class="list-select">
                <p class="list-arrow-month">翌月</p>
                <p class="list-select-arrow">→</p>
            </div>
        </div>
    </div>
    <script src="{{ asset('/js/selectMonth.js') }}"></script>
@endsection