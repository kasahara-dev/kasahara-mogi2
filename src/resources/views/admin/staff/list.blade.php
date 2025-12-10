@extends('layout.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">スタッフ一覧</h1>
        <div>{{ $users->links() }}</div>
        <table class="table">
            <thead class="table-header">
                <tr>
                    <th class="table-header__name--wide">名前</th>
                    <th class="table-header__name--wide">メールアドレス</th>
                    <th class="table-header__name">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="table-line">
                        <td class="table-line__data">{{ $user->name }}</td>
                        <td class="table-line__data">{{ $user->email }}</td>
                        <td class="table-line__data"><a class="table-line__link"
                                href="/admin/attendance/staff/{{ $user->id }}">詳細</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection