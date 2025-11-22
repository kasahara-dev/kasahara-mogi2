@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">勤務詳細</h1>
        <dl class="list">
            <div class="list-line">
                <dt class="list-line__title">名前</dt>
                <dd class="list-line__data">{{ $name }}</dd>
            </div>
            <div class="list-line">
                <dt class="list-line__title">日付</dt>
                <dd class="list-line__data">{{ \Carbon\Carbon::parse($start)->format('Y年m月d日') }}</dd>
            </div>
            <div class="list-line">
                <dt class="list-line__title">出勤・退勤</dt>
                <dd class="list-line__data">
                    {{ \Carbon\Carbon::parse($start)->format('H:i') }}～{{ \Carbon\Carbon::parse($end)->format('H:i') }}
                </dd>
            </div>
            @foreach ($rests as $key => $rest)
                <div class="list-line">
                    <dt class="list-line__title">休憩@if($key != 0){{ $key + 1 }}@endif</dt>
                    <dd class="list-line__data">
                        {{ \Carbon\Carbon::parse($rest->start)->format('H:i') }}～{{ \Carbon\Carbon::parse($rest->end)->format('H:i') }}
                    </dd>
                </div>
            @endforeach
            <div class="list-line">
                <dt class="list-line__title">休憩{{ $restsCount + 1 }}</dt>
                <dd class="list-line__data">
                </dd>
            </div>
            <div class="list-line">
                <dt class="list-line__title">備考</dt>
                <dd class="list-line__data">{{ $note }}</dd>
            </div>
        </dl>
        <div class="list__bottom">
            @if ($pending)
                <p class="message">＊承認待ちのため修正はできません</p>
            @else
                <button type="submit" class="submit-btn">修正</button>
            @endif
        </div>
    </div>
@endsection