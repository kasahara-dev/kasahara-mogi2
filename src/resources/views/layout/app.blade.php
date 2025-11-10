<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <div class="wrapper">
        <header class="header" id="header">
            <div class="header-logo-area">
                <a href="/" class="header-logo" id="header-logo"><img src="{{ asset('img/logo.svg') }}" alt="コーチテックロゴ"
                        class="header-img" /></a>
                <div id="hamburger" class="hamburger"></div>
            </div>
            <form action="/" method="get" class="header-form" id="header-form">
                @csrf
                <input type="search" name="keyword" @if(isset($keyword)) value="{{ $keyword }}" @endif
                    placeholder="なにをお探しですか?" class="header-search">
                <input type="hidden" @if(isset($tab)) value="{{ $tab }}" @endif name="tab" />
            </form>
            <ul class="header-btns" id="header-btns">
                @auth
                    <li class="header-btn">
                        <form class="header-form-logout" action="/logout" method="post" class="header-btn">
                            @csrf
                            <button type="submit" class="header-logout" name="logout">ログアウト</button>
                        </form>
                    </li>
                    <li class="header-btn"><a href="/mypage" class="header-mypage">マイページ</a></li>
                    <li class="header-btn"><button onclick="location.href='/sell'" class="header-exhibit">出品</button></li>
                @endauth
                @guest
                    <li class="header-btn"><a href="/login" class="header-login">ログイン</a></li>
                    <li class="header-btn"><a href="/mypage" class="header-mypage">マイページ</a></li>
                    <li class="header-btn"><button onclick="location.href='/sell'" class="header-exhibit">出品</button></li>
                @endguest
            </ul>
        </header>
        <main class="main" id="main">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('/js/hamburger.js') }}"></script>
</body>

</html>