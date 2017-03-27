@extends('layouts.layout')

@section('title', 'user')

@section('content')
<h3>user arear</h3>
TODO
<h4>reset password</h4>
<div>
    <form action="{{ action('UserController@resetpwd') }}" method="POST">
        old word:<input type="password" name="old_word" /><br />
        new word:<input type="password" name="new_word" /><br />
        {{ csrf_field() }}
        <input type="submit" />
    </form>
</div>
<h4>update profile</h4>
<div>
    <form action="{{ action('UserController@profiles') }}" method="POST">
        username:<input type="text" name="name" /><br />
        {{ csrf_field() }}
        <input type="submit" />
    </form>
</div>
@include('layouts.errors')

@endsection
