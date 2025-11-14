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
    <div class="date">
        {{ today()->isoFormat('YYYY年M月D日(ddd)') }}
    </div>
@endsection