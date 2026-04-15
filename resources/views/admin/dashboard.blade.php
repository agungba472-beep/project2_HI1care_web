@extends('layouts.v_template')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow">
            <div class="card-body">
                Hijau: {{ $hijau }}
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow">
            <div class="card-body">
                Kuning: {{ $kuning }}
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow">
            <div class="card-body">
                Merah: {{ $merah }}
            </div>
        </div>
    </div>

</div>

@endsection