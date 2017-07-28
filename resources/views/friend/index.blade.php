@extends('layouts.layout')

@section('title', 'note')

@section('content')
    <h2>friends</h2>
    <hr>

    <div>
        <h4>add friend</h4>
        email: <input type="text" name="email" /><br>
        <button id="add-friends">add friends</button>
    </div>
@endsection

@section('script')
<script>
    $('#add-friends').click(function () {console.log('?');
        var email = $('[name=email]').val();
        if (email == '') {
            alert('不能为空！');
            return ;
        }
        $.post("{{ action('FriendController@store') }}", {'email': email},
            function(result) {
                if (result.status == -1) {
                    alert('未找到用户！');
                } else {
                    alert('请求成功！');
                }
            }
        );
    });
</script>
@endsection
