@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Laporan')
@section('breadcrumb', 'Laporan Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Laporan Transaksi</h5>
    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-sm btn-success">
        <i class="bi bi-download me-1"></i>Export CSV
    </a>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Tampilkan</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
            <!-- Quick Filters -->
            <div class="col-auto ms-auto d-flex gap-1 flex-wrap">
                <a href="{{ route('reports.index', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                    class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">Hari Ini</a>
                <a href="{{ route('reports.index', ['date_from' => now()->startOfWeek()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                    class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">Minggu Ini</a>
                <a href="{{ route('reports.index', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->toDateString()]) }}"
                    class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;padding:0.25rem 0.6rem;">Bulan Ini</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card" style="border-left:4px solid #198754;">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">Total Pendapatan</div>
                <div class="h4 fw-bold mb-0" style="color:#198754;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="small text-muted">{{ $dateFrom }} s/d {{ $dateTo }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="border-left:4px solid #0d6efd;">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">Total Transaksi</div>
                <div class="h4 fw-bold mb-0" style="color:#0d6efd;">{{ number_format($totalTransactions) }}</div>
                <div class="small text-muted">transaksi lunas</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="border-left:4px solid #fd7e14;">
            <div class="card-body p-3">
                <div class="text-muted small mb-1">Rata-rata per Transaksi</div>
                <div class="h4 fw-bold mb-0" style="color:#fd7e14;">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</div>
                <div class="small text-muted">per transaksi</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Payment Breakdown -->
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Metode Pembayaran</h6></div>
            <div class="card-body">
                @foreach($paymentBreakdown as $pb)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold small">
                            @if($pb->payment_method=='tunai') 💵 Tunai
                            @elseif($pb->payment_method=='transfer') 🏦 Transfer
                            @else 📱 QRIS @endif
                        </span>
                        <span class="small text-muted">{{ $pb->count }}x transaksi</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @php $maxT = $paymentBreakdown->max('total') ?: 1; @endphp
                        <div class="progress flex-grow-1" style="height:8px;border-radius:99px;">
                            <div class="progress-bar" style="width:{{ ($pb->total/$maxT)*100 }}%;background:var(--primary);border-radius:99px;"></div>
                        </div>
                        <span class="small fw-bold" style="min-width:90px;text-align:right;">Rp {{ number_format($pb->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
                @if($paymentBreakdown->isEmpty())
                    <div class="text-center text-muted py-3">Tidak ada data</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Services -->
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>Jasa Terlaris</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size:0.82rem;">
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
                                        <span style="color:{{ ['#ffd700','#c0c0c0','#cd7f32'][$i] }};">
                                            <i class="bi bi-trophy-fill"></i>
                                        </span>
                                    @else {{ $i+1 }} @endif
                                </td>
                                <td class="fw-semibold">{{ Str::limit($svc->name, 30) }}</td>
                                <td class="text-center">{{ number_format($svc->total_qty) }}</td>
                                <td class="pe-3 fw-bold" style="color:var(--primary);">Rp {{ number_format($svc->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Table -->
<div class="card">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-list-ul me-2 text-primary"></i>Detail Transaksi</h6>
        <small class="text-muted">{{ $transactions->total() }} transaksi</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.83rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Invoice</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th class="pe-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('transactions.show', $tx->id) }}" class="fw-semibold text-decoration-none" style="color:var(--primary);">{{ $tx->invoice_number }}</a>
                        </td>
                        <td>{{ $tx->customer_name ?: 'Umum' }}</td>
                        <td class="text-muted">{{ $tx->user->name ?? '-' }}</td>
                        <td class="fw-bold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ ['tunai'=>'success','transfer'=>'info','qris'=>'primary'][$tx->payment_method] ?? 'secondary' }}">
                                {{ $tx->payment_method_label }}
                            </span>
                        </td>
                        <td class="pe-3 text-muted">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi dalam rentang waktu ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer py-2">{{ $transactions->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
