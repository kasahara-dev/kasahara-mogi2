@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/attendance.css') }}">
@endsection

@section('content')
    <div class="status">
        @if($workingStatus == 0)
            勤務外
        @elseif($workingStatus == 1)
            @if($resting)
                休憩中
            @else
                出勤中
            @endif
        @else
            退勤済
        @endif
    </div>
    <p class="date" id="date"></p>
    <p class="time" id="time"></p>
    @if($workingStatus == 0)
        <form action="/attendance/record" method="post">
            @csrf
            <button type="submit" class="attendance-btn">出勤</button>
        </form>
    @elseif($workingStatus == 1)
        @if($resting)
            <form action="/attendance/rest" method="post">
                @method('PUT')
                @csrf
                <button type="submit" class="rest-btn">休憩戻</button>
            </form>
        @else
            <div class="working-btns-area">
                <form action="/attendance/record" method="post">
                    @method('PUT')
                    @csrf
                    <button type="submit" class="attendance-btn">退勤</button>
                </form>
                <form action="/attendance/rest" method="post">
                    @csrf
                    <button type="submit" class="rest-btn">休憩入</button>
                </form>
            </div>
        @endif
    @else
        <p class="attendance-msg">お疲れ様でした。</p>
    @endif
    <script src="{{ asset('/js/showCurrentTime.js') }}"></script>
@endsection