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
                    <td>{{ $friend->friend_id }}</td>
                    <td>{{ $friend->name }}</td>
                    <td>{{ $friend->email }}</td>
                    <td>
                        {{--<a href="{{ action('ChatController@getIndex', ['to' => $friend->chat_key]) }}">chat</a>--}}
                        <button onclick="chat_set({{ $friend->friend_id }}, '{{ $friend->name }}')">chat</button>
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
        <hr>
    </div>
    <div>
        <h4>chat</h4>
        <h5>content</h5>
        <div class="chat-content">
            @foreach ($messages as $message)
                {{ $message->created_at }} <br>from {{ $message->user->name }} : {{ $message->message }}<br>
            @endforeach
        </div>
        <span class="to-whom"></span>
        <input type="text" class="send-content"><button onclick="chat_send()" class="chat-send">sent</button>
    </div>
@endsection

@section('script')
<script>
    var ws = new WebSocket("ws://192.168.10.10:9501");

    ws.onopen = function()
    {
        // Web Socket 已连接上，使用 send() 方法发送数据
        var data = JSON.stringify({'type': 'init', 'data': {'id': '{{ \Auth::id() }}'}});
        ws.send(data);
        console.log("连接开启...");
    };

    ws.onmessage = function (evt)
    {
        console.log(evt);
        var recieve = JSON.parse(evt.data);
        $('.chat-content').append(recieve.name + ' : ' + recieve.msg + '<br>');
    };

    ws.onclose = function()
    {
        console.log("连接已关闭...");
    };
</script>
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

    function chat_set(id, name)
    {
        $('.to-whom').html('to ' + name);
        $('.chat-send').attr('data-id', id);
        $('.chat-send').attr('data-name', name);
    }

    function chat_send()
    {
        var content = $('.send-content').val();
        var id = $('.chat-send').attr('data-id');
        var name = $('.chat-send').attr('data-name');
        var data = JSON.stringify({'type': 'send', 'data': {'to': id, 'msg': content}});
        ws.send(data);
        $('.chat-content').append('<span class="blue">to ' + name + ' : ' + content + '</span><br>');
    }
</script>
@endsection

@section('style')
<style>
    td {
        padding: 5px;
    }
    .blue {
        color: #3768ff;
    }
</style>
@endsection
