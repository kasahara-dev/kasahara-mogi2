@extends('layout.admin.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/stamp/list.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1 class="title">申請一覧</h1>
        <div class="tabs">
            <div class="tabs__status">
                @if($pending)
                    <a href="/stamp_correction_request/list?tab=pending" class="tabs__tab--active">承認待ち</a>
                    <a href="/stamp_correction_request/list?tab=approved" class="tabs__tab">承認済み</a>
                @else
                    <a href="/stamp_correction_request/list?tab=pending" class="tabs__tab">承認待ち</a>
                    <a href="/stamp_correction_request/list?tab=approved" class="tabs__tab--active">承認済み</a>
                @endif
            </div>
            <div class="tabs__page">
                @if($pending)
                    {{ $requestedAttendances->appends(['tab' => 'pending'])->links() }}
                @else
                    {{ $requestedAttendances->appends(['tab' => 'approved'])->links() }}
                @endif
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="table__header">状態</th>
                    <th class="table__header">名前</th>
                    <th class="table__header">対象日時</th>
                    <th class="table__header">申請理由</th>
                    <th class="table__header">申請日時</th>
                    <th class="table__header">詳細</th>
                </tr>
            </thead>
            @foreach ($requestedAttendances as $requestedAttendance)
                <tr>
                    <td class="table__data">
                        @if($requestedAttendance->request->status == '1')
                            承認待ち
                        @else
                            承認済み
                        @endif
                    </td>
                    <td class="table__data">{{ $requestedAttendance->request->attendance->user->name }}</td>
                    <td class="table__data">{{ \Carbon\Carbon::parse($requestedAttendance->start)->format('Y/m/d')}}</td>
                    <td class="table__data">{{ $requestedAttendance->note }}</td>
                    <td class="table__data">
                        {{ \Carbon\Carbon::parse($requestedAttendance->request->created_at)->format('Y/m/d')}}</td>
                    <td class="table__data"><a class="table__data--link"
                            href="/stamp_correction_request/approve/{{ $requestedAttendance->request->id }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection