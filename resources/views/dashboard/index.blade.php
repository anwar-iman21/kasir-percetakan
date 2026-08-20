@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat Cards -->
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-left: 4px solid #0d6efd;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Transaksi Hari Ini</div>
                        <div class="h4 fw-bold mb-0">{{ $todayTransactions }}</div>
                        <div class="small text-muted">transaksi</div>
                    </div>
                    <div style="width:42px;height:42px;background:rgba(13,110,253,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#0d6efd;">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-left: 4px solid #198754;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Pendapatan Hari Ini</div>
                        <div class="h5 fw-bold mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                        <div class="small text-muted">hari ini</div>
                    </div>
                    <div style="width:42px;height:42px;background:rgba(25,135,84,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#198754;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-left: 4px solid #fd7e14;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Pendapatan Bulan Ini</div>
                        <div class="h5 fw-bold mb-0">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
                        <div class="small text-muted">{{ $monthTransactions }} transaksi</div>
                    </div>
                    <div style="width:42px;height:42px;background:rgba(253,126,20,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fd7e14;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100" style="border-left: 4px solid #6f42c1;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small mb-1">Total Pelanggan</div>
                        <div class="h4 fw-bold mb-0">{{ $totalCustomers }}</div>
                        <div class="small text-muted">pelanggan</div>
                    </div>
                    <div style="width:42px;height:42px;background:rgba(111,66,193,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#6f42c1;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>Pendapatan 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Payment Breakdown -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Metode Pembayaran</h6>
            </div>
            <div class="card-body">
                @foreach($paymentBreakdown as $pb)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">
                            @if($pb->payment_method == 'tunai') <i class="bi bi-cash me-1"></i>Tunai
                            @elseif($pb->payment_method == 'transfer') <i class="bi bi-bank me-1"></i>Transfer
                            @else <i class="bi bi-qr-code me-1"></i>QRIS
                            @endif
                        </span>
                        <span class="small text-muted">{{ $pb->count }}x</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height:6px;border-radius:99px;">
                            @php
                                $maxTotal = $paymentBreakdown->max('total') ?: 1;
                                $pct = ($pb->total / $maxTotal) * 100;
                            @endphp
                            <div class="progress-bar" style="width:{{ $pct }}%;background:var(--primary);border-radius:99px;"></div>
                        </div>
                        <span class="small fw-bold">Rp {{ number_format($pb->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
                @if($paymentBreakdown->isEmpty())
                    <div class="text-center text-muted py-3">Belum ada data bulan ini</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Top Services -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-star me-2 text-warning"></i>Jasa Terlaris Bulan Ini</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Nama Jasa</th>
                                <th class="text-center">Qty</th>
                                <th class="pe-3">Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topServices as $i => $svc)
                            <tr>
                                <td class="ps-3">
                                    @if($i < 3)
                                        <span style="color:{{ ['#ffd700','#c0c0c0','#cd7f32'][$i] }}; font-size:1rem;">
                                            <i class="bi bi-trophy-fill"></i>
                                        </span>
                                    @else
                                        <span class="text-muted">{{ $i+1 }}</span>
                                    @endif
                                </td>
                                <td class="small fw-semibold">{{ Str::limit($svc->name, 25) }}</td>
                                <td class="text-center small">{{ $svc->total_qty }}</td>
                                <td class="pe-3 small">Rp {{ number_format($svc->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Transaksi Terbaru</h6>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Invoice</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Bayar</th>
                                <th class="pe-3">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="text-decoration-none fw-semibold small" style="color:var(--primary);">
                                        {{ $tx->invoice_number }}
                                    </a>
                                </td>
                                <td class="small">{{ $tx->customer_name }}</td>
                                <td class="small fw-bold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ ['tunai'=>'success','transfer'=>'info','qris'=>'primary'][$tx->payment_method] ?? 'secondary' }} rounded-pill small">
                                        {{ ucfirst($tx->payment_method) }}
                                    </span>
                                </td>
                                <td class="pe-3 small text-muted">{{ $tx->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Button -->
<a href="{{ route('kasir.index') }}" style="position:fixed;bottom:2rem;right:2rem;width:56px;height:56px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem;box-shadow:0 4px 15px rgba(0,0,0,0.2);text-decoration:none;z-index:100;" title="Transaksi Baru">
    <i class="bi bi-plus-lg"></i>
</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
const chartData = @json($chartData);
const ctx = document.getElementById('revenueChart').getContext('2d');
const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
const textColor = isDark ? '#aaa' : '#6c757d';

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [{
            label: 'Pendapatan',
            data: chartData.map(d => d.revenue),
            backgroundColor: 'rgba(13,110,253,0.15)',
            borderColor: 'rgba(13,110,253,0.8)',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: gridColor },
                ticks: {
                    color: textColor,
                    callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v)
                }
            },
            x: {
                grid: { display: false },
                ticks: { color: textColor }
            }
        }
    }
});
</script>
@endpush
