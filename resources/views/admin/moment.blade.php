@extends('admin.base')

@section('title', '朋友圈列表')

@section('content')
    <div class="card card-bordered style-primary">
        <div class="card-head">
            <header>朋友圈列表</header>
        </div>
        <div class="card-body style-default-bright">
            <table class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th>id</th>
                    <th>用户名</th>
                    <th>内容</th>
                    <th>图片</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($moments as $moment)
                    <tr>
                        <td>{{ $moment->id }}</td>
                        <td>{{ $moment->user->name }}</td>
                        <td>{{ $moment->content }}</td>
                        <td>
                            @foreach ($moment->pictures as $picture)
                                <img src="{{ $picture }}">
                            @endforeach
                        </td>
                        <td>{{ $moment->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {!! $moments->render() !!}
            </div>
        </div>
    </div>
@stop

@section('script')
    <script src="/js/user-all.js"></script>
@stop
