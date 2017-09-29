@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>friends</h2>
    <hr>

    <div>
        <h4>your friends</h4>
        <table border="1" id="friends-list">
            <tr>
                <td>no</td>
                <td>name</td>
                <td>email</td>
                <td>do</td>
            </tr>
        </table>
    </div>

    <div>
        <h4>add friend</h4>
        email: <input type="text" name="email" />
        <button id="add-friends">add friend</button>
    </div>
    <hr>
    <div class="add-list">
        <h4>add notices</h4>
    </div>
    <hr>
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
        console.log(evt.data);
        var recieve = JSON.parse(evt.data);
        if (recieve.type == 'chat') {
            $('.chat-content').append(recieve.data.name + ' : ' + recieve.data.msg + '<br>');
        } else if (recieve.type == 'notice') {
            alert(recieve.data.notice);
            if (recieve.data.type == 'add') {
                add_notice();
            } else if (recieve.data.type == 'accept') {
                refresh_friends();
            }
        }
    };

    ws.onclose = function()
    {
        console.log("连接已关闭...");
    };
</script>
<script>
    $(function(){
        refresh_friends();
        add_notice();
    });

    function refresh_friends()
    {
        console.log('refreshing');
        $.get("{{ action('FriendController@get_list', ['type' => 2]) }}",
            function (response) {
                $('#friends-list tr:gt(0):not(:eq(1))').remove();
                for (var i=0;i<response.length;i++)
                {
                    console.log(response[i]);
                    var tr = '<tr><td>'+response[i].friend_id+'</td><td>'+response[i].name+'</td><td>'+response[i].email+
                        '</td><td><button onclick="chat_set('+response[i].friend_id+', \''+response[i].name+'\')">chat</button>'+
                        '<a href="javascript:ajaxDelete(\'{{ url('friend') }}/'+response[i].id+'\');">delete</a></td></tr>';
                    console.log(tr);
                    $("#friends-list tr:last").after(tr);
                }
            }
        );
    }

    function add_notice()
    {
        $.get("{{ action('FriendController@get_list', ['type' => 1]) }}",
            function (response) {
                console.log(response);
                for (var i=0;i<response.length;i++)
                {
                    var add = "email: " + response[i].email + ", nickname: " + response[i].name + "<br>";
                    add += '<button id="add-friends" onclick="add_friend(\'' + response[i].adder_id + '\', 1)">accept</button>';
                    add += '<button id="add-friends" onclick="add_friend(\'' + response[i].adder_id + '\', 2)">reject</button>';
                    console.log(add);
                    $('.add-list').append(add);
                }
            }
        );
    }

    // add friend
    $('#add-friends').click(function () {
        var email = $('[name=email]').val();
        if (email == '') {
            alert('不能为空！');
            return ;
        }
        $.post("{{ action('FriendController@store') }}", {'email': email},
            function(result) {
                if (result.status == 1) {
                    var data = JSON.stringify({'type': 'notice', 'data': {'type': 'add', 'to': result.data.id}});
                    ws.send(data);
                }
                alert(result.data.message);
            }
        );
    });

    function add_friend(id, type) {
        var url = "{{ url('friend') }}/" + id;
        var param = {'_method': 'PUT', 'type': type};
        $.post(url, param, function(result) {
            alert(result.message);
            if (type == 2) {
                var data = {'type': 'notice', 'data': {'type':'reject', 'to': id}};
            } else if (type == 1) {
                var data = {'type': 'notice', 'data': {'type':'accept', 'to': id}};
            }
            ws.send(JSON.stringify(data));
            refresh_friends();
        });
    }

    function chat_set(id, name)
    {
        $('.to-whom').html('to ' + name);
        $('.chat-send').attr('data-id', id);
        $('.chat-send').attr('data-name', name);
    }

    function chat_send()
    {
        var id = $('.chat-send').attr('data-id');
        if (id == undefined) {
            alert('请选择发送人');
            return ;
        }
        var content = $('.send-content').val();
        if (content == '') {
            alert('请输入发送内容');
            return ;
        }
        var name = $('.chat-send').attr('data-name');
        var data = JSON.stringify({'type': 'chat', 'data': {'to': id, 'msg': content}});
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
