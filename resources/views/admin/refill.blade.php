@extends('layouts.v_template')

@section('content')

<h1>Monitoring Refill ARV</h1>

<table border="1">
<tr>
    <th>Nama</th>
    <th>Tanggal</th>
    <th>Status</th>
</tr>

@foreach($refill as $r)
<tr>
    <td>{{ $r->pasien->user->nama }}</td>
    <td>{{ $r->tanggal_refill }}</td>
    <td>{{ $r->status }}</td>
</tr>
@endforeach
</table>

@endsection