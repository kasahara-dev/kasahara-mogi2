<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="wrapper">
        <header class="header" id="header">
            <div class="header-logo-area">
                <img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ" class="header-img" />
                <div id="hamburger" class="hamburger"></div>
            </div>
            <ul class="header-btns" id="header-btns">
                <li class="header-btn" style="color: white;">
                    勤怠
                </li>
                <li class="header-btn" style="color: white;">
                    勤怠一覧
                </li>
                <li class="header-btn" style="color: white;">
                    申請
                </li>
                <li class="header-btn">
                    <form class="header-form-logout" action="/logout" method="post" class="header-btn">
                        @csrf
                        <button type="submit" class="header-logout" name="logout">ログアウト</button>
                    </form>
                </li>
            </ul>
        </header>
        <main class="main" id="main">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('/js/hamburger.js') }}"></script>
</body>

</html>