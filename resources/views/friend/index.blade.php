@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>friends</h2>
    <hr>

    <div>
        <h4>your friends</h4>
        <table border="1">
            <tr>
                <td>no</td>
                <td>name</td>
                <td>email</td>
                <td>do</td>
            </tr>
            @foreach($friends as $key => $friend)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $friend->name }}</td>
                    <td>{{ $friend->email }}</td>
                    <td>
                        <a href="{{ action('ChatController@getIndex', ['to' => $friend->chat_key]) }}">chat</a>
                        <a href="javascript:ajaxDelete('{{ action('FriendController@destroy', $friend->id) }}');">delete</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <div>
        <h4>add friend</h4>
        email: <input type="text" name="email" />
        <button id="add-friends">add friend</button>
    </div>
    <hr>
    <div>
        <h4>add notices</h4>
        @foreach ($adds as $user)
            email: {{ $user['email'] }}, nickname: {{ $user['name'] }}<br>
            <button id="add-friends" onclick="addFriend('{{ action('FriendController@update', $user->adder_id) }}', 1)">accept</button>
            <button id="add-friends" onclick="addFriend('{{ action('FriendController@update', $user->adder_id) }}', 3)">reject</button>
        @endforeach
    </div>
@endsection

@section('script')
<script>
    $('#add-friends').click(function () {
        var email = $('[name=email]').val();
        if (email == '') {
            alert('不能为空！');
            return ;
        }
        $.post("{{ action('FriendController@store') }}", {'email': email},
            function(result) {
                alert(result.message);
            }
        );
    });

    function addFriend(href, type) {
        $.post(href, {'_method': 'PUT', 'type': type},
            function(result) {
                alert(result.message);
            }
        );
        location.reload();
    }
</script>
<script>
    var ws = new WebSocket("ws://192.168.10.10:9501");

    ws.onopen = function()
    {
        // Web Socket 已连接上，使用 send() 方法发送数据
        ws.send(JSON.stringify({'type': 'init', 'data': {'from': '{{ \Auth::id() }}', 'to': ''}}));
        console.log("连接开启...");
    };

    ws.onmessage = function (evt)
    {
        alert(evt.data);
    };

    ws.onclose = function()
    {
        console.log("连接已关闭...");
    };
</script>
@endsection

@section('style')
<style>
    td {
        padding: 5px;
    }
</style>
@endsection
