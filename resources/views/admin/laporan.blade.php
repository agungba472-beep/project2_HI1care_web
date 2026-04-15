@extends('layouts.v_template')

@section('content')

<h1>Laporan Pasien</h1>

<table border="1">
<tr>
    <th>Nama</th>
    <th>Status</th>
</tr>

@foreach($pasien as $p)
<tr>
    <td>{{ $p->user->nama }}</td>
    <td>{{ $p->status_kepatuhan }}</td>
</tr>
@endforeach
</table>

@endsection