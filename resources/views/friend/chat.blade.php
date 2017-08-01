@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>chat</h2>
    <hr>

@endsection

@section('script')
    <script>
        // 打开一个 web socket
        var ws = new WebSocket("ws://127.0.0.1:9501");

        ws.onopen = function()
        {
            // Web Socket 已连接上，使用 send() 方法发送数据
            var url = window.location.href;

            ws.send(JSON.stringify({'type': 'init', 'from': url}));
            console.log("连接开启...");
        };

        ws.onmessage = function (evt)
        {
            var received_msg = evt.data;
            $('#chat-content').append('others: ' + received_msg + '<br>');
            console.log(received_msg);
        };

        ws.onclose = function()
        {
            // 关闭 websocket
            console.log("连接已关闭...");
        };
    </script>
@endsection

@section('style')
    <style>

    </style>
@endsection
