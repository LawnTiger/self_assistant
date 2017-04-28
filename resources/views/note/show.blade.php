@extends('layouts.layout')

@section('title', 'note')

@section('content')
{{ $note->title }}
<hr>
{!! $note->content !!}

<a href="{{ action('NoteController@edit', [$note->id]) }}">修改</a>
@endsection
