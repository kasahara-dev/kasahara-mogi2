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
            <div class="app-logo"><img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ" class="app-logo__img" />
            </div>
        </header>
        <main class="main">
            <h1 class="form-title">管理者ログイン</h1>
            <form class="form" action="/admin/login" method="post" novalidate>
                @csrf
                <dl>
                    <dt class="form__name">メールアドレス</dt>
                    <dd class="form__content"><input type="email" class="form__input" name="email"
                            value="{{ old('email') }}" />
                    </dd>
                    <dd class="form__error">@error('email'){{ $message }}@enderror</dd>
                    <dt class="form__name">パスワード</dt>
                    <dd class="form__content"><input type="password" class="form__input" name="password"
                            value="{{ old('password') }}" /></dd>
                    <dd class="form__error">@error('password'){{ $message }}@enderror</dd>
                </dl>
                <button type="submit" class="submit-btn login-btn" name="send">管理者ログインする</button>
            </form>
        </main>
    </div>
</body>

</html>