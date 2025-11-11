<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header-logo-area"><img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ" class="header-img" />
            </div>
        </header>
        <main class="main">
            <div class="form-area">
                <h1 class="form-title">ログイン</h1>
                <form class="form" action="/login" method="post" novalidate>
                    @csrf
                    <dl>
                        <dt class="form-name">メールアドレス</dt>
                        <dd class="form-content"><input type="email" class="form-input" name="email"
                                value="{{ old('email') }}" />
                        </dd>
                        <dd class="form-error">@error('email'){{ $message }}@enderror</dd>
                        <dt class="form-name">パスワード</dt>
                        <dd class="form-content"><input type="password" class="form-input" name="password"
                                value="{{ old('password') }}" /></dd>
                        <dd class="form-error">@error('password'){{ $message }}@enderror</dd>
                    </dl>
                    <button type="submit" class="submit-btn login-btn" name="send">ログインする</button>
                </form>
                <a href="/register" class="link">会員登録はこちら</a>
            </div>
        </main>
    </div>
</body>

</html>