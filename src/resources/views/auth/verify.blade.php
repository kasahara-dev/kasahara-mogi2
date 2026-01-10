<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header-logo-area">
                <img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ" class="header-img" />
            </div>
        </header>
        <main class="main">
            <div class="form-area">
                <div class="mail-info">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</div>
                <a href="{{env('APP_URL')}}:8025" class="mail-link">認証はこちらから</a>
                <form action="/email/verification-notification" method="post">
                    @csrf
                    <button type="submit" class="resend-link" name="send">認証メールを再送する</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>