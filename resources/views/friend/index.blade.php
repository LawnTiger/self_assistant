@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>friends</h2>
    <hr>

    <div>
        <h4>your friends</h4>
        <table border="1" id="friends-list">
            <tr>
                <td>id</td>
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
    <div>
        <h4>add notices</h4>
        <div class="add-list"></div>
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
        <input type="text" class="send-content"><button onclick="chat_send('user')" class="chat-send">sent</button>
    </div>


    <h2>group</h2>
    <hr>
    <div>
        <h4>your groups</h4>
        <table border="1" id="group-list">
            <tr>
                <td>id</td>
                <td>group name</td>
                <td>do</td>
            </tr>
        </table>
    </div>
    <div id="group-add" style="display: none;">
        add member: <input type="text" id="group-add-input"><button onclick="group_add()">ok</button>
    </div>

    <div>
        <h4>create group</h4>
        name: <input type="text" name="name" />
        <button id="create-group">create</button>
    </div>
    <hr>
    <div>
        <h4>group chat</h4>
        <h5>content</h5>
        <div class="group-content">

        </div>
        <span class="to-group"></span>
        <input type="text" class="group-send-content"><button onclick="chat_send('group')" class="group-send">sent</button>
    </div>
@endsection

@section('script')
<script>
    var ws = new WebSocket("ws://59.110.136.203:4001");

    ws.onopen = function()
    {
        // Web Socket 已连接上，使用 send() 方法发送数据
        var data = JSON.stringify({'code': 'init', 'data': {'id': '{{ \Auth::id() }}'}});
        ws.send(data);
        console.log("连接开启...");
    };

    ws.onmessage = function (evt)
    {
        console.log(evt.data);
        var recieve = JSON.parse(evt.data);
        if (recieve.data.chatType == 'p2p') {
            $('.chat-content').append(recieve.data.userName + ' : ' + recieve.data.content.body + '<br>');
        } else if(recieve.data.chatType == 'group') {
            $('.group-content').append(recieve.data.groupName + ' : ' + recieve.data.content.body + '<br>');
        } else if (recieve.code == 'notice') {
            if (recieve.data.type == 'addFriend') {
                alert('somebody add you');
                add_notice();
            } else if (recieve.data.type == 'responseFriend') {
                if (recieve.data.isAccept == 1) {
                    var msg = recieve.data.name + ' accept you';
                } else {
                    var msg = recieve.data.name + ' reject you';
                }
                alert(msg);
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
        refresh_groups();
    });

    function refresh_friends()
    {
        $.get("{{ action('FriendController@get_list', ['type' => 2]) }}",
            function (response) {
                console.log(response);
                $('#friends-list tr:not(:eq(0))').remove();
                for (var i=0;i<response.length;i++)
                {
                    var tr = '<tr><td>'+response[i].friend_id+'</td><td>'+response[i].name+'</td><td>'+response[i].email+
                        '</td><td><button onclick="chat_set('+response[i].friend_id+', \''+response[i].name+'\')">chat</button> '+
                        '<a href="javascript:ajaxDelete(\'{{ url('friend') }}/'+response[i].id+'\');">delete</a></td></tr>';
                    $("#friends-list tr:last").after(tr);
                }
            }
        );
    }

    function refresh_groups()
    {
        $.get("{{ action('GroupController@index') }}",
            function (response) {
                console.log(response);
                $('#group-list tr:not(:eq(0))').remove();
                for (var i=0;i<response.length;i++)
                {
                    var tr = '<tr><td>'+response[i].group.id+'</td><td>'+response[i].group.name
                        +'</td><td><button onclick="chat_set('+response[i].group.id+',\''+response[i].group.name+'\', \'group\')">chat</button>'
                        +'<a target="_blank" href="{{ url('group') }}/'+response[i].group.id+'">member</a> '
                        +'<button onclick="group_add_show('+response[i].group.id+')">add</button></td></tr>';
                    $("#group-list tr:last").after(tr);
                }
            }
        );
    }

    function add_notice()
    {
        $.get("{{ action('FriendController@get_list', ['type' => 1]) }}",
            function (response) {
                console.log(response);
                $('.add-list').html('');
                for (var i=0;i<response.length;i++)
                {
                    var add = "email: " + response[i].email + ", nickname: " + response[i].name + "<br>";
                    add += '<button id="add-friends" onclick="add_friend(\'' + response[i].friend_id + '\', 1)">accept</button>';
                    add += '<button id="add-friends" onclick="add_friend(\'' + response[i].friend_id + '\', 2)">reject</button>';
                    console.log(add);
                    $('.add-list').append(add);
                }
            }
        );
    }

    function group_add_show(id) {
        $('#group-add').show();
        $('#group-add').attr('data-id', id);
    }

    function group_add() {
        var group = $('#group-add').attr('data-id');
        var user = $('#group-add-input').val();
        $.post('{{ action('GroupController@update', 1) }}',
            {'group_id': group, 'user_id': user, '_method': 'PUT'},
            function (result) {
                alert(result);
            }
        )
    }

    $('#add-friends').click(function () {
        var email = $('[name=email]').val();
        if (email == '') {
            alert('不能为空！');
            return ;
        }
        $.post("{{ action('FriendController@store') }}", {'email': email},
            function(result) {
                if (result.status == 1) {
                    var data = JSON.stringify(
                        {'code': 'notice', 'data':
                            {'type': 'addFriend', 'id': result.data.id, 'content': 'fuck', 'time': (Date.parse(new Date())/1000)}});
                    ws.send(data);
                }
                alert(result.data.message);
            }
        );
    });

    $('#create-group').click(function () {
        var name = $('[name=name]').val();
        if (name == '') {
            alert('群名不能为空！');
            return ;
        }
        $.post("{{ action('GroupController@store') }}", {'name': name},
            function(result) {
                alert('添加成功');
                refresh_groups();
            }
        );
    });

    function add_friend(id, type) {
        var url = "{{ url('friend') }}/" + id;
        var param = {'_method': 'PUT', 'type': type};
        $.post(url, param, function(result) {
            if (type == 2) {
                var data = {'code': 'notice', 'data':
                    {'type': 'responseFriend', 'isAccept': 0, 'id': id, 'content': 'fuck', 'time': (Date.parse(new Date())/1000)}};
            } else if (type == 1) {
                var data = {'code': 'notice', 'data':
                    {'type': 'responseFriend', 'isAccept': 1, 'id': id, 'content': 'fuck', 'time': (Date.parse(new Date())/1000)}};
            }
            ws.send(JSON.stringify(data));
            alert(result.message);
            refresh_friends();
            add_notice();
        });
    }

    function chat_set(id, name, type)
    {
        if (type == undefined) {
            $('.to-whom').html('to ' + name);
            $('.chat-send').attr('data-id', id);
            $('.chat-send').attr('data-name', name);
        } else {
            $('.to-group').html('to ' + name);
            $('.group-send').attr('data-id', id);
            $('.group-send').attr('data-name', name);
        }
    }

    function chat_send(type)
    {
        if (type == 'user') {
            var select_send = '.chat-send';
            var select_content = '.send-content';
            var select_show = '.chat-content';
            var select_type = 'p2p';
        } else {
            var select_send = '.group-send';
            var select_content = '.group-send-content';
            var select_show = '.group-content';
            var select_type = 'group';
        }
        var id = $(select_send).attr('data-id');
        if (id == undefined) {
            alert('请选择发送人');
            return ;
        }
        var content = $(select_content).val();
        if (content == '') {
            alert('请输入发送内容');
            return ;
        }
        var name = $(select_send).attr('data-name');
        var data = JSON.stringify({'type': 'chat', 'data': {'to': id, 'msg': content, 'type': type}});
        var data = JSON.stringify({"code": "msg", "data": {"chatType": select_type, "id": id, "time": (Date.parse(new Date())/1000), "content": {"contentType": "txt", "body": content}}});
        ws.send(data);
        console.log(data);
        $(select_show).append('<span class="blue">to ' + name + ' : ' + content + '</span><br>');
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
