@extends('layouts.v_template')

@section('content')

<h1>Monitoring Pasien</h1>

<table border="1">
<tr>
    <th>Nama</th>
    <th>No Reg</th>
    <th>Status</th>
</tr>

@foreach($pasien as $p)
<tr>
    <td>{{ $p->user->nama }}</td>
    <td>{{ $p->master->no_reg_hiv }}</td>
    <td>
        @if($p->status_kepatuhan == 'merah')
            🔴
        @elseif($p->status_kepatuhan == 'kuning')
            🟡
        @else
            🟢
        @endif
    </td>
</tr>
@endforeach
</table>

@endsection