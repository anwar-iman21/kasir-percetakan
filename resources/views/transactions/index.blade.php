@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('breadcrumb', 'Riwayat Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Riwayat Transaksi</h5>
    <a href="{{ route('kasir.index') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
    </a>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('transactions.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="No. invoice / nama pelanggan" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Dari tanggal">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Sampai tanggal">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">Semua Pembayaran</option>
                        <option value="tunai" {{ request('payment_method') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Invoice</th>
                        <th>Pelanggan</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('transactions.show', $tx->id) }}" class="fw-semibold text-decoration-none" style="color:var(--primary);">
                                {{ $tx->invoice_number }}
                            </a>
                            @if($tx->deadline)
                                <br><small class="text-warning"><i class="bi bi-alarm me-1"></i>{{ $tx->deadline->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <div>{{ $tx->customer_name ?: 'Umum' }}</div>
                            @if($tx->customer_phone)
                                <small class="text-muted">{{ $tx->customer_phone }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $tx->items->count() }} item</span>
                        </td>
                        <td class="fw-bold">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ ['tunai'=>'success','transfer'=>'info','qris'=>'primary'][$tx->payment_method] ?? 'secondary' }}">
                                {{ $tx->payment_method_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $tx->status_badge }}">{{ $tx->status_label }}</span>
                        </td>
                        <td>
                            <div>{{ $tx->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $tx->created_at->format('H:i') }}</small>
                        </td>
                        <td class="pe-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('transactions.show', $tx->id) }}" class="btn btn-xs btn-outline-secondary" style="padding:0.15rem 0.5rem;" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('transactions.nota', $tx->id) }}" target="_blank" class="btn btn-xs btn-outline-primary" style="padding:0.15rem 0.5rem;" title="Cetak Nota A4">
                                    <i class="bi bi-file-text"></i>
                                </a>
                                <a href="{{ route('transactions.thermal', $tx->id) }}" target="_blank" class="btn btn-xs btn-outline-success" style="padding:0.15rem 0.5rem;" title="Cetak Thermal">
                                    <i class="bi bi-receipt"></i>
                                </a>
                                @if(auth()->user()->isAdmin() && $tx->status === 'lunas')
                                <form method="POST" action="{{ route('transactions.cancel', $tx->id) }}" class="d-inline" onsubmit="return confirm('Batalkan transaksi ini?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-xs btn-outline-warning" style="padding:0.15rem 0.5rem;" title="Batalkan">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                            Tidak ada transaksi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer py-2">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
