@extends('layouts.v_template')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Monitoring HI!-CARE</h1>
    
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow">
                <div class="card-body">Total Pasien: {{ $stats['total_pasien'] }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4 shadow">
                <div class="card-body">Pending Verifikasi: {{ $stats['pending_verifikasi'] }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 shadow">
                <div class="card-body">Pasien Patuh (Hijau): {{ $stats['kepatuhan_hijau'] }}</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4 shadow">
                <div class="card-body">Risiko Tinggi (Merah): {{ $stats['kepatuhan_merah'] }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-chart-pie me-1"></i> Analitik Kepatuhan</div>
                <div class="card-body"><canvas id="complianceChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('complianceChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hijau (Patuh)', 'Kuning (Peringatan)', 'Merah (Drop-out)'],
            datasets: [{
                data: [{{ $stats['kepatuhan_hijau'] }}, 0, {{ $stats['kepatuhan_merah'] }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
            }]
        }
    });
</script>
@endsection