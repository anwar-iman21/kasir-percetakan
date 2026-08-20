@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Detail Pelanggan')
@section('breadcrumb', 'Detail Pelanggan')

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">Detail Pelanggan</h5>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center p-4">
                <div style="width:72px;height:72px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:white;font-size:2rem;font-weight:700;">
                    {{ strtoupper(substr($customer->name,0,1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                @if($customer->phone)<p class="text-muted mb-1"><i class="bi bi-telephone me-1"></i>{{ $customer->phone }}</p>@endif
                @if($customer->email)<p class="text-muted mb-1"><i class="bi bi-envelope me-1"></i>{{ $customer->email }}</p>@endif
                @if($customer->address)<p class="text-muted small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $customer->address }}</p>@endif
                @if($customer->notes)<p class="text-muted small mb-0"><i class="bi bi-sticky me-1"></i>{{ $customer->notes }}</p>@endif
                <hr>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Transaksi</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" style="font-size:0.83rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Invoice</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="pe-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->transactions as $tx)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="text-decoration-none fw-semibold" style="color:var(--primary);">{{ $tx->invoice_number }}</a>
                                </td>
                                <td class="fw-bold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                <td><span class="badge bg-{{ $tx->status_badge }}">{{ $tx->status_label }}</span></td>
                                <td class="pe-3 text-muted">{{ $tx->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
