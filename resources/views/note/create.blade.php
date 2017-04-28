@extends('layouts.layout')

@section('title', 'add note')

@section('content')
    <h2>add notes</h2>
    <hr>
    <form method="post" action="{{ action('NoteController@store') }}">
        {{ csrf_field() }}
        标题: <input type="text" name="title"><br>
        内容: <textarea name="content"></textarea><br>
        <input type="submit" value="提交">
    </form>

    @include('layouts.errors')

@endsection
