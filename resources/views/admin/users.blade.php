@extends('layouts.v_template')

@section('content')

<h1>User</h1>

<table class="table table-bordered">
<thead>
<tr>
    <th>Nama</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>

@foreach($users as $user)
<tr>
    <td>{{ $user->nama }}</td>
    <td>
        <span class="badge 
            {{ $user->status_akun == 'aktif' ? 'badge-success' : 'badge-warning' }}">
            {{ $user->status_akun }}
        </span>
    </td>
    <td>
        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
            @csrf
            <button class="btn btn-success btn-sm">Approve</button>
        </form>
    </td>
</tr>
@endforeach

</table>

@endsection