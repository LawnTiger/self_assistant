@extends('layouts.layout')

@section('title', 'note')

@section('content')
<h2>notes</h2>
<hr>
@if (empty($notes->toArray()))
    没数据
@endif
@foreach ($notes as $note)
    <p>{{ $note->id }}</p>
@endforeach

@include('layouts.errors')

@endsection
