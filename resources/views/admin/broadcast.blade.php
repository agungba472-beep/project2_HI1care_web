@extends('layouts.v_template')

@section('content')

<h1>Broadcast</h1>

<form method="POST" action="{{ route('admin.broadcast.send') }}">
    @csrf
    <textarea class="form-control" name="pesan"></textarea>
    <br>
    <button class="btn btn-primary">Kirim</button>
</form>

@endsection