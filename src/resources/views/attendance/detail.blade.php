@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">勤務詳細</h1>
        <dl class="list">
            <div class="line">
                <dt class="line__title">名前</dt>
                <dd class="line__data">{{ auth()->user()->name }}</dd>
            </div>
        </dl>
    </div>
@endsection