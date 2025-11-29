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
                    <dd class="list-line-data">
                        {{ $name }}
                        <div class="list-line-errors-area"></div>
                    </dd>
                </div>
                <div class="list-line">
                    <dt class="list-line-title">日付</dt>
                    <dd class="list-line-data">
                        {{ \Carbon\Carbon::parse($start)->format('Y年m月d日') }}
                        <div class="list-line-errors-area"></div>
                    </dd>
                </div>
                <div class="list-line">
                    <dt class="list-line-title">出勤・退勤</dt>
                    <dd class="list-line-data">
                        @if($pending)
                            {{ \Carbon\Carbon::parse($start)->format('H:i') }}～{{ \Carbon\Carbon::parse($end)->format('H:i') }}
                        @else
                            <div class="list-line-selectors-area">
                                <div class="list-line-time">
                                    <select name="attendance_start_hour" id="attendance_start_hour" class="list-line-selector">
                                        @for($i = 0; $i <= 23; $i++)
                                            <option value="{{ $i }}"
                                            @if(!is_null(old('attendance_start_hour')))
                                                @if(sprintf('%02d', $i) == old('attendance_start_hour'))
                                                    selected
                                                @endif
                                            @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($start)->format('H'))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="attendance_start_minute" id="attendance_start_minute"
                                        class="list-line-selector">
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}"
                                            @if(!is_null(old('attendance_start_minute')))
                                                @if(sprintf('%02d', $i) == old('attendance_start_minute'))
                                                    selected
                                                @endif
                                            @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($start)->format('i'))
                                                selected
                                            @endif
                                            >
                                            {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <p class="list-line-wave">～</p>
                                <div class="list-line-time">
                                    <select name="attendance_end_hour" id="attendance_end_hour" class="list-line-selector">
                                        @for($i = 0; $i <= 24; $i++)
                                            <option value="{{ $i }}"
                                            @if(!is_null(old('attendance_end_hour')))
                                                @if(sprintf('%02d', $i) == old('attendance_end_hour'))
                                                    selected
                                                @endif
                                            @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($end)->format('H'))
                                                selected
                                            @elseif(\Carbon\Carbon::parse($start)->startOfDay()->lt(\Carbon\Carbon::parse($end)->startOfDay()) && $i == '24')
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="attendance_end_minute" id="attendance_end_minute" class="list-line-selector">
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}"
                                            @if(!is_null(old('attendance_end_minute')))
                                                @if(sprintf('%02d', $i) == old('attendance_end_minute'))
                                                    selected
                                                @endif
                                            @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($end)->format('i'))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="list-line-errors-area">
                            @error('attendance_start_num')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>
                    </dd>
                </div>
                @foreach ($rests as $key => $rest)
                    <div class="list-line">
                        <dt class="list-line-title">休憩@if($key != 0){{ $key + 1 }}@endif</dt>
                        <dd class="list-line-data">
                            @if($pending)
                                {{ \Carbon\Carbon::parse($rest->start)->format('H:i') }}～{{ \Carbon\Carbon::parse($rest->end)->format('H:i') }}
                            @else
                                <div class="list-line-selectors-area">
                                    <div class="list-line-time">
                                        <select name="rest_start_hour[{{ $key + 1 }}]" id="rest_start_hour_{{ $key + 1 }}"
                                            class="list-line-selector">
                                            <option value="-1" selected>--</option>
                                            @for($i = 0; $i <= 23; $i++)
                                                <option value="{{ $i }}"
                                                @if(!is_null(old("rest_start_hour." . $key + 1)))
                                                    @if(sprintf('%02d', $i) == old("rest_start_hour." . $key + 1))
                                                        selected
                                                    @endif
                                                @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->start)->format('H'))
                                                    selected
                                                @endif
                                                >
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                        :
                                        <select name="rest_start_minute[{{ $key + 1 }}]" id="rest_start_minute_{{ $key + 1 }}"
                                            class="list-line-selector">
                                            <option value="-1" selected>--</option>
                                            @for($i = 0; $i <= 59; $i++)
                                                <option value="{{ $i }}"
                                                @if(!is_null(old('rest_start_minute.' . $key + 1)))
                                                    @if(sprintf('%02d', $i) == old('rest_start_minute.' . $key + 1))
                                                        selected
                                                    @endif
                                                @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->start)->format('i'))
                                                    selected
                                                @endif
                                                >
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <p class="list-line-wave">～</p>
                                    <div class="list-line-time">
                                        <select name="rest_end_hour[{{ $key + 1 }}]" id="rest_end_hour_{{ $key + 1 }}"
                                            class="list-line-selector">
                                            <option value="-1" selected>--</option>
                                            @for($i = 0; $i <= 24; $i++)
                                                <option value="{{ $i }}"
                                                @if(!is_null(old('rest_end_hour.' . $key + 1)))
                                                    @if(sprintf('%02d', $i) == old('rest_end_hour.' . $key + 1))
                                                        selected
                                                    @endif
                                                @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->end)->format('H'))
                                                    selected
                                                @elseif(\Carbon\Carbon::parse($rest->start)->startOfDay()->lt(\Carbon\Carbon::parse($rest->end)->startOfDay()) && $i == '24')
                                                    selected
                                                @endif
                                                >
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                        :
                                        <select name="rest_end_minute[{{ $key + 1 }}]" id="rest_end_minute_{{ $key + 1 }}"
                                            class="list-line-selector">
                                            <option value="-1" selected>--</option>
                                            @for($i = 0; $i <= 59; $i++)
                                                <option value="{{ $i }}"
                                                @if(!is_null(old('rest_end_minute.' . $key + 1)))
                                                    @if(sprintf('%02d', $i) == old('rest_end_minute.' . $key + 1))
                                                        selected
                                                    @endif
                                                @elseif(sprintf('%02d', $i) == \Carbon\Carbon::parse($rest->end)->format('i'))
                                                    selected
                                                @endif
                                                >
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="list-line-errors-area">
                                <p class="error-msg">
                                    @if($errors->has("rest_start_hour." . $key + 1))
                                        {{  $errors->first("rest_start_hour." . $key + 1) }}
                                    @elseif($errors->has("rest_start_minute." . $key + 1))
                                        {{ $errors->first("rest_start_minute." . $key + 1) }}
                                    @elseif($errors->has("rest_end_hour." . $key + 1))
                                        {{  $errors->first("rest_end_hour." . $key + 1) }}
                                    @elseif($errors->has("rest_end_minute." . $key + 1))
                                        {{ $errors->first("rest_end_minute." . $key + 1) }}
                                    @elseif($errors->has("rest_start_num." . $key + 1))
                                        {{ $errors->first("rest_start_num." . $key + 1) }}
                                    @elseif($errors->has('rest_end_num.' . $key + 1))
                                        {{ $errors->first('rest_end_num.' . $key + 1) }}
                                    @elseif($errors->has('rest_batting.' . $key + 1))
                                        {{ $errors->first('rest_batting.' . $key + 1) }}
                                    @endif
                                </p>
                            </div>
                        </dd>
                    </div>
                @endforeach
                @if(!$pending)
                    <div class="list-line">
                        <dt class="list-line-title">休憩@if($restsCount > 0){{ $restsCount + 1 }}@endif</dt>
                        <dd class="list-line-data">
                            <div class="list-line-selectors-area">
                                <div class="list-line-time">
                                    <select name="rest_start_hour[{{ $restsCount + 1 }}]"
                                        id="rest_start_hour_{{ $restsCount + 1 }}" class="list-line-selector">
                                        <option value="-1" selected>--</option>
                                        @for($i = 0; $i <= 23; $i++)
                                            <option value="{{ $i }}"
                                            @if(old('rest_start_hour.' . $restsCount + 1) == sprintf('%02d', $i))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="rest_start_minute[{{ $restsCount + 1 }}]"
                                        id="rest_start_minute_{{ $restsCount + 1 }}" class="list-line-selector">
                                        <option value="-1" selected>--</option>
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}"
                                            @if(old('rest_start_minute.' . $restsCount + 1) == sprintf('%02d', $i))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>

                                </div>
                                <p class="list-line-wave">～</p>
                                <div class="list-line-time">
                                    <select name="rest_end_hour[{{ $restsCount + 1 }}]" id="rest_end_hour_{{ $restsCount + 1 }}"
                                        class="list-line-selector">
                                        <option value="-1" selected>--</option>
                                        @for($i = 0; $i <= 24; $i++)
                                            <option value="{{ $i }}"
                                            @if(old('rest_end_hour.' . $restsCount + 1) == sprintf('%02d', $i))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                    :
                                    <select name="rest_end_minute[{{ $restsCount + 1 }}]"
                                        id="rest_end_minute_{{ $restsCount + 1 }}" class="list-line-selector">
                                        <option value="-1" selected>--</option>
                                        @for($i = 0; $i <= 59; $i++)
                                            <option value="{{ $i }}"
                                            @if(old('rest_end_minute.' . $restsCount + 1) == sprintf('%02d', $i))
                                                selected
                                            @endif
                                            >
                                                {{ sprintf('%02d', $i) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="list-line-errors-area">
                                <p class="error-msg">
                                    @if($errors->has("rest_start_hour." . $restsCount + 1))
                                        {{  $errors->first("rest_start_hour." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_start_minute." . $restsCount + 1))
                                        {{ $errors->first("rest_start_minute." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_end_hour." . $restsCount + 1))
                                        {{  $errors->first("rest_end_hour." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_end_minute." . $restsCount + 1))
                                        {{ $errors->first("rest_end_minute." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_start_num." . $restsCount + 1))
                                        {{ $errors->first("rest_start_num." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_end_num." . $restsCount + 1))
                                        {{ $errors->first("rest_end_num." . $restsCount + 1) }}
                                    @elseif($errors->has("rest_batting." . $restsCount + 1))
                                        {{ $errors->first("rest_batting." . $restsCount + 1) }}
                                    @endif
                                                    </p>
                                                </div>
                                            </dd>
                                        </div>
                @endif
                <div class="list-line">
                    <dt class="list-line-title">備考</dt>
                    <dd class="list-line-data">
                        <div class="list-line-selectors-area">
                            @if ($pending)
                                {{ $note }}
                            @else
                                <textarea class="list-line-textarea" name="note">{{ old('note', $note) }}</textarea>
                            @endif
                        </div>
                        <div class="list-line-errors-area">
                            @error('note')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>
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