<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header-logo-area">
                <a href="/" class="header-logo"><img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ"
                        class="header-img" /></a>
            </div>
        </header>
        <main class="main">
            <div class="form-area">
                <h1 class="form-title">会員登録</h1>
                <form class="form" action="/register" method="post" novalidate autocomplete="off">
                    @csrf
                    <dl>
                        <dt class="form-name">ユーザー名</dt>
                        <dd class="form-content"><input type="text" name="name" class="form-input"
                                value="{{ old('name') }}" autocomplete="off" /></dd>
                        <dd class="form-error">@error('name'){{ $message }}@enderror</dd>
                        <dt class="form-name">メールアドレス</dt>
                        <dd class="form-content"><input type="email" name="email" class="form-input"
                                value="{{ old('email') }}" />
                        </dd>
                        <dd class="form-error">@error('email'){{ $message }}@enderror</dd>
                        <dt class="form-name">パスワード</dt>
                        <dd class="form-content"><input type="password" name="password" class="form-input"
                                value="{{ old('password') }}" autocomplete="off" /></dd>
                        <dd class="form-error">
                            @error('password')
                                @foreach ($errors->get('password') as $message)
                                    @if(Str::contains($message, config('word.match')))
                                    @else
                                        {{ $message }}
                                        @break
                                    @endif
                                @endforeach
                            @enderror
                        </dd>
                        <dt class="form-name">確認用パスワード</dt>
                        <dd class="form-content"><input type="password" name="password_confirmation" class="form-input"
                                value="{{ old('password_confirmation') }}" autocomplete="off" /></dd>
                        <dd class="form-error">
                            @error('password')
                                @foreach ($errors->get('password') as $message)
                                    @if(Str::contains($message, config('word.match')))
                                        {{ $message }}
                                    @endif
                                @endforeach
                            @enderror
                        </dd>
                    </dl>
                    <button type="submit" class="submit-btn register-btn" name="send">登録する</button>
                </form>
                <a href="/login" class="link">ログインはこちら　</a>
            </div>
        </main>
    </div>
</body>

</html>