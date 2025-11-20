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
            <div class="app-logo">
                <a href="/admin/attendance/list">
                    <img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ" class="app-logo__img" />
                </a>
                <div id="hamburger" class="app-logo__hamburger"></div>
            </div>
            <ul class="header-btns" id="header-btns">
                <li class="header-btns__list">
                    <form action="/admin/attendance/list" method="get">
                        @csrf
                        <button type="submit" class="header-btn">勤怠一覧</button>
                    </form>
                </li>
                <li class="header-btns__list">
                    <form action="/admin/staff/list" method="get">
                        @csrf
                        <button type="submit" class="header-btn">スタッフ一覧</button>
                    </form>
                </li>
                <li class="header-btns__list">
                    <form action="/stamp_correction_request/list" method="get">
                        @csrf
                        <button type="submit" class="header-btn">申請一覧</button>
                    </form>
                </li>
                <li class="header-btns__list">
                    <form class="header-form-btn" action="/admin/logout" method="post">
                        @csrf
                        <button type="submit" class="header-btn" name="logout">ログアウト</button>
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