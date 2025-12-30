@extends('layouts.admin')

@section('title', 'Laporan Dokumen')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up"></i> Laporan Dokumen</h2>
    </div>

    <!-- Filter Rentang Bulan & Tahun -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Periode Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.documents') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_month" class="form-label">Dari Bulan</label>
                    <select name="start_month" id="start_month" class="form-select" required>
                        @foreach($months as $key => $monthName)
                            <option value="{{ $key }}" {{ $startMonth == $key ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="end_month" class="form-label">Sampai Bulan</label>
                    <select name="end_month" id="end_month" class="form-select" required>
                        @foreach($months as $key => $monthName)
                            <option value="{{ $key }}" {{ $endMonth == $key ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Tahun</label>
                    <select name="year" id="year" class="form-select" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Periode Terpilih -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-calendar-range"></i> 
                <strong>Periode Laporan:</strong> 
                {{ $months[$startMonth] }} {{ $selectedYear }} 
                @if($startMonth != $endMonth)
                    - {{ $months[$endMonth] }} {{ $selectedYear }}
                @endif
                <span class="ms-3">
                    <i class="bi bi-calendar3"></i> 
                    {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu Ringkasan Total -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h3><i class="bi bi-file-earmark-text text-primary"></i></h3>
                    <h2 class="text-primary">{{ number_format($totalSK) }}</h2>
                    <p class="card-text">Surat Keputusan</p>
                    <small class="text-muted">Total periode ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3><i class="bi bi-file-earmark-check text-success"></i></h3>
                    <h2 class="text-success">{{ number_format($totalSP) }}</h2>
                    <p class="card-text">Surat Perjanjian</p>
                    <small class="text-muted">Total periode ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3><i class="bi bi-file-earmark-plus text-warning"></i></h3>
                    <h2 class="text-warning">{{ number_format($totalAddendum) }}</h2>
                    <p class="card-text">Addendum</p>
                    <small class="text-muted">Total periode ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h3><i class="bi bi-files text-info"></i></h3>
                    <h2 class="text-info">{{ number_format($grandTotal) }}</h2>
                    <p class="card-text">Total Semua Dokumen</p>
                    <small class="text-muted">Total periode ini</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Batang -->
    @if(count($monthlyData) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart"></i> 
                        Grafik Dokumen per Bulan 
                        ({{ $months[$startMonth] }} - {{ $months[$endMonth] }} {{ $selectedYear }})
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="documentChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Rekap Detail Per Bulan -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i> 
                        Tabel Rekap Detail - {{ $months[$startMonth] }} s/d {{ $months[$endMonth] }} {{ $selectedYear }}
                    </h5>
                    <span class="badge bg-primary">Total: {{ number_format($grandTotal) }} dokumen</span>
                </div>
                <div class="card-body">
                    @if(count($monthlyData) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Bulan</th>
                                    <th class="text-center">Surat Keputusan</th>
                                    <th class="text-center">Surat Perjanjian</th>
                                    <th class="text-center">Addendum</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $data['month_name'] }} {{ $selectedYear }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($data['sk']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($data['sp']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark">{{ number_format($data['addendum']) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ number_format($data['total']) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $percentage = $grandTotal > 0 ? ($data['total'] / $grandTotal) * 100 : 0;
                                        @endphp
                                        <span class="badge bg-info">{{ number_format($percentage, 1) }}%</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">TOTAL PERIODE:</th>
                                    <th class="text-center">{{ number_format($totalSK) }}</th>
                                    <th class="text-center">{{ number_format($totalSP) }}</th>
                                    <th class="text-center">{{ number_format($totalAddendum) }}</th>
                                    <th class="text-center">{{ number_format($grandTotal) }}</th>
                                    <th class="text-center">100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="mt-3">Tidak Ada Data</h5>
                        <p class="text-muted">Tidak ada dokumen pada periode yang dipilih</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(count($monthlyData) > 0)
    const ctx = document.getElementById('documentChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Surat Keputusan',
                        data: {!! json_encode($chartDataSK) !!},
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Surat Perjanjian',
                        data: {!! json_encode($chartDataSP) !!},
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Addendum',
                        data: {!! json_encode($chartDataAddendum) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y + ' dokumen';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endsection