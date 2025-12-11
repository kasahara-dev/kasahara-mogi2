@extends('layout.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/stamp/detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">勤務詳細</h1>
        <dl class="list">
            <div class="list-line">
                <dt class="list-line-title">名前</dt>
                <dd class="list-line-data">
                    {{ $name }}
                </dd>
            </div>
            <div class="list-line">
                <dt class="list-line-title">日付</dt>
                <dd class="list-line-data">
                    {{ \Carbon\Carbon::parse($start)->format('Y年m月d日') }}
                </dd>
            </div>
            <div class="list-line">
                <dt class="list-line-title">出勤・退勤</dt>
                <dd class="list-line-data">
                    @if(\Carbon\Carbon::parse($start)->startOfDay()->eq(\Carbon\Carbon::parse($end)->startOfDay()))
                        {{ \Carbon\Carbon::parse($start)->format('H:i') }}～{{ \Carbon\Carbon::parse($end)->format('H:i') }}
                    @else
                        {{ \Carbon\Carbon::parse($start)->format('H:i') }}～24:00
                    @endif
                </dd>
            </div>
            @if(count($rests) == 0)
                <div class="list-line">
                    <dt class="list-line-title">休憩</dt>
                    <dd class="list-line-data">
                    </dd>
                </div>
            @endif
            @foreach ($rests as $key => $rest)
                <div class="list-line">
                    <dt class="list-line-title">休憩@if($key != 0){{ $key + 1 }}@endif</dt>
                    <dd class="list-line-data">
                        @if (\Carbon\Carbon::parse($rest->start)->startOfDay()->eq(\Carbon\Carbon::parse($rest->end)->startOfDay()))
                            {{ \Carbon\Carbon::parse($rest->start)->format('H:i') }}～{{ \Carbon\Carbon::parse($rest->end)->format('H:i') }}
                        @else
                            {{ \Carbon\Carbon::parse($rest->start)->format('H:i') }}～24:00
                        @endif
                    </dd>
                </div>
            @endforeach
            <div class="list-line">
                <dt class="list-line-title">備考</dt>
                <dd class="list-line-data">
                    <div class="list-line-selectors-area">
                        {{ $note }}
                    </div>
                </dd>
            </div>
        </dl>
        <div class="list-bottom">
            @if($pending)
                <form action="/stamp_correction_request/approve/{{ $id }}" method="post">
                    @method('PUT')
                    @csrf
                    <input type="submit" value="承認" class="list-bottom__btn"></input>
                </form>
            @else
                <button class="list-bottom__btn--disable" disabled>承認済み</button>
            @endif
        </div>
    </div>
@endsection