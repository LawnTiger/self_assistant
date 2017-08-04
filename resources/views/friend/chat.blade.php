@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>chat</h2>
    <hr>

    <div id="chat-content"></div>
    <div id="sse">
        发送信息：<input type="text" id="content" /><button onclick="ws_send()">send</button>
    </div>

@endsection

@section('script')
    <script>
        // 打开一个 web socket
        var ws = new WebSocket("ws://192.168.10.10:9501");

        ws.onopen = function()
        {
            // Web Socket 已连接上，使用 send() 方法发送数据
            ws.send(JSON.stringify({'type': 'init', 'data': {'from': '{{ \Auth::id() }}', 'to': '{{ Request::get('to') }}'}}));
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

        function ws_send() {
            var content = $('#content').val();
            ws.send(JSON.stringify({'type': 'text', 'msg': content}));
            $('#chat-content').append('mine: ' + content + '<br>');
        }
    </script>
@endsection

@section('style')
    <style>

    </style>
@endsection
