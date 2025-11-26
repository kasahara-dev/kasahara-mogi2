@extends('layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <form action="/attendance/detail/{{ $attendanceId }}" method="post">
            @csrf
            <h1 class="title">勤務詳細</h1>
            <dl class="list">
                <div class="list-line">
                    <dt class="list-line-title">名前</dt>
                    <dd class="list-line-data">{{ $name }}</dd>
                </div>
                <div class="list-line">
                    <dt class="list-line-title">日付</dt>
                    <dd class="list-line-data">{{ \Carbon\Carbon::parse($start)->format('Y年m月d日') }}</dd>
                </div>
                <div class="list-line">
                    <dt class="list-line-title">出勤・退勤</dt>
                    <dd class="list-line-data">
                        @if($pending)
                            {{ \Carbon\Carbon::parse($start)->format('H:i') }}～{{ \Carbon\Carbon::parse($end)->format('H:i') }}
                        @else
                            <div class="list-line-time">
                                <select name="attendance_start_hour" id="attendance_start_hour" class="list-line-selector">
                                    @for($i = 0; $i <= 23; $i++)
                                        <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($start)->format('H')) selected @endif>
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                :
                                <select name="attendance_start_minute" id="attendance_start_minute" class="list-line-selector">
                                    @for($i = 0; $i <= 59; $i++)
                                        <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($start)->format('i')) selected @endif>
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <p class="list-line-wave">～</p>
                            <div class="list-line-time">
                                <select name="attendance_end_hour" id="attendance_end_hour" class="list-line-selector">
                                    @for($i = 0; $i <= 24; $i++)
                                        <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($end)->format('H'))
                                            selected
                                        @elseif(\Carbon\Carbon::parse($start)->startOfDay()->lt(\Carbon\Carbon::parse($end)->startOfDay()) && $i == '24') selected @endif>
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                :
                                <select name="attendance_end_minute" id="attendance_end_minute" class="list-line-selector">
                                    @for($i = 0; $i <= 59; $i++)
                                        <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($end)->format('i'))
                                        selected @endif>
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        @endif
                    </dd>
                </div>
                @foreach ($rests as $key => $rest)
                    <div class="list-line">
                        <dt class="list-line-title">休憩@if($key != 0){{ $key + 1 }}@endif</dt>
                        <dd class="list-line-data">
                            @if($pending)
                                {{ \Carbon\Carbon::parse($rest->start)->format('H:i') }}～{{ \Carbon\Carbon::parse($rest->end)->format('H:i') }}
                            @else
                                <div class="list-line-time">
                                    <select name="rest_start_hour_{{ $key + 1 }}" id="rest_start_hour_{{ $key + 1 }}"
                                        class="list-line-selector">
                                        <option value="" selected>--</option>
                                        @for($i = 0; $i <= 23; $i++)
                                            <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->start)->format('H')) selected @endif>
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="rest_start_minute_{{ $key + 1 }}" id="rest_start_minute_{{ $key + 1 }}"
                                        class="list-line-selector">
                                        <option value="" selected>--</option>
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->start)->format('i')) selected @endif>
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <p class="list-line-wave">～</p>
                                <div class="list-line-time">
                                    <select name="rest_end_hour_{{ $key + 1 }}" id="rest_end_hour_{{ $key + 1 }}"
                                        class="list-line-selector">
                                        <option value="" selected>--</option>
                                        @for($i = 0; $i <= 24; $i++)
                                            <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->end)->format('H')) selected @endif>
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="rest_end_minute_{{ $key + 1 }}" id="rest_end_minute_{{ $key + 1 }}"
                                        class="list-line-selector">
                                        <option value="" selected>--</option>
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}" @if(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->end)->format('i')) selected @endif>
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            @endif
                        </dd>
                    </div>
                @endforeach
                @if(!$pending)
                    <div class="list-line">
                        <dt class="list-line-title">休憩@if($restsCount > 0){{ $restsCount + 1 }}@endif</dt>
                        <dd class="list-line-data">
                            <div class="list-line-time">
                                <select name="rest_start_hour_{{ $restsCount + 1 }}" id="rest_start_hour_{{ $restsCount + 1 }}"
                                    class="list-line-selector">
                                    <option value="" selected>--</option>
                                    @for($i = 0; $i <= 23; $i++)
                                        <option value="{{ $i }}">
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                :
                                <select name="rest_start_minute_{{ $restsCount + 1 }}"
                                    id="rest_start_minute_{{ $restsCount + 1 }}" class="list-line-selector">
                                    <option value="" selected>--</option>
                                    @for($i = 0; $i <= 59; $i++)
                                        <option value="{{ $i }}">
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <p class="list-line-wave">～</p>
                            <div class="list-line-time">
                                <select name="rest_end_hour_{{ $restsCount + 1 }}" id="rest_end_hour_{{ $restsCount + 1 }}"
                                    class="list-line-selector">
                                    <option value="" selected>--</option>
                                    @for($i = 0; $i <= 24; $i++)
                                        <option value="{{ $i }}">
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                                :
                                <select name="rest_end_minute_{{ $restsCount + 1 }}" id="rest_end_minute_{{ $restsCount + 1 }}"
                                    class="list-line-selector">
                                    <option value="" selected>--</option>
                                    @for($i = 0; $i <= 59; $i++)
                                        <option value="{{ $i }}">
                                            {{ sprintf('%02d', $i) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </dd>
                    </div>
                @endif
                <div class="list-line">
                    <dt class="list-line-title">備考</dt>
                    <dd class="list-line-data">
                        @if ($pending)
                            {{ $note }}
                        @else
                            <textarea class="list-line-textarea" name="note">{{ $note }}</textarea>
                        @endif
                    </dd>
                </div>
            </dl>
            <div class="list-bottom">
                @if ($pending)
                    <p class="list-bottom__message">＊承認待ちのため修正はできません</p>
                @else
                    <button type="submit" class="list-bottom__btn">修正</button>
                @endif
            </div>
        </form>
    </div>
    @if(!$pending)
        <script>
            const restsCount = {{ $restsCount + 1 }};
        </script>
        <script src="{{ asset('/js/lockTime.js') }}"></script>
    @endif
@endsection