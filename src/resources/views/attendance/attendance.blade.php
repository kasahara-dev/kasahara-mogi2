@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="status">
        @if($working)
            出勤中
        @else
            勤務外
        @endif
    </div>
    <p class="date">
        {{ today()->isoFormat('YYYY年M月D日(ddd)') }}
    </p>
    <p class="time" id="time"></p>
    @if($working)
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
        <form action="/attendance/record" method="post">
            @csrf
            <button type="submit" class="attendance-btn">出勤</button>
        </form>
    @endif
    <script src="{{ asset('/js/showCurrentTime.js') }}"></script>
@endsection