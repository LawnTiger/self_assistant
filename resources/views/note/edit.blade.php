@extends('layouts.layout')

@section('title', 'add note')

@section('content')
    <h2>update notes</h2>
    <hr>
    <form method="post" action="{{ action('NoteController@update', [$note->id]) }}">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        标题: <input type="text" name="title" value="{{ $note->title }}"><br>
        内容: <textarea name="content">{{ $note->content }}</textarea><br>
        <input type="submit" value="提交">
    </form>

    @include('layouts.errors')

@endsection
