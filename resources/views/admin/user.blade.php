@extends('admin.base')

@section('title', '查看技术人员订单')

@section('content')
    <div class="card card-bordered style-primary">
        <div class="card-head">
            <div class="tools">
                <button type="button" class="btn ink-reaction btn-flat btn-primary" title="返回上一级">
                    <a href="{{ action('Admin\UserController@index') }}">返回上一级</a>
                </button>
            </div>
            <head>用户列表</head>
        </div>
        <div class="card-body style-default-bright">
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th>id</th>
                    <th>用户名</th>
                    <th>用户邮箱</th>
                    <th>注册时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {!! $users->render() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="/js/user-all.js"></script>
@stop
