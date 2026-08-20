@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('breadcrumb', 'Detail Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i>
        </a>
        <span class="fw-bold">Detail Transaksi</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.nota', $transaction->id) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="bi bi-file-text me-1"></i>Cetak Nota A4
        </a>
        <a href="{{ route('transactions.thermal', $transaction->id) }}" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-receipt me-1"></i>Cetak Thermal
        </a>
        @if(auth()->user()->isAdmin())
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @if($transaction->status === 'lunas')
                <li>
                    <form method="POST" action="{{ route('transactions.cancel', $transaction->id) }}" onsubmit="return confirm('Batalkan transaksi ini?')">
                        @csrf @method('PUT')
                        <button type="submit" class="dropdown-item text-warning">
                            <i class="bi bi-x-circle me-2"></i>Batalkan Transaksi
                        </button>
                    </form>
                </li>
                @endif
                <li>
                    <form method="POST" action="{{ route('transactions.destroy', $transaction->id) }}" onsubmit="return confirm('Hapus transaksi ini secara permanen?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-trash me-2"></i>Hapus Transaksi
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1" style="color:var(--primary);">{{ $transaction->invoice_number }}</h5>
                        <small class="text-muted">{{ $transaction->created_at->format('d F Y, H:i') }}</small>
                    </div>
                    <span class="badge fs-6 bg-{{ $transaction->status_badge }}">{{ $transaction->status_label }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item / Jasa</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    @if($item->description)<div class="text-muted small">{{ $item->description }}</div>@endif
                                    @if($item->size)<div class="text-muted small"><i class="bi bi-rulers me-1"></i>{{ $item->size }}</div>@endif
                                    @if($item->paper_type)<div class="text-muted small"><i class="bi bi-file me-1"></i>{{ $item->paper_type }}</div>@endif
                                    @if($item->color)<div class="text-muted small"><i class="bi bi-palette me-1"></i>{{ $item->color }}</div>@endif
                                    @if($item->custom_notes)<div class="text-muted small"><i class="bi bi-chat-left me-1"></i>{{ $item->custom_notes }}</div>@endif
                                </td>
                                <td class="text-center">{{ $item->qty }} {{ $item->unit }}</td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end">Subtotal</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($transaction->discount > 0)
                            <tr>
                                <td colspan="4" class="text-end text-danger">Diskon</td>
                                <td class="text-end text-danger fw-semibold">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($transaction->tax > 0)
                            <tr>
                                <td colspan="4" class="text-end">Pajak ({{ $transaction->tax_percent }}%)</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end fw-bold fs-6">TOTAL</td>
                                <td class="text-end fw-bold fs-6" style="color:var(--primary);">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($transaction->notes)
                <div class="alert alert-warning py-2 mb-0">
                    <i class="bi bi-sticky me-2"></i><strong>Catatan:</strong> {{ $transaction->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card mb-3">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2 text-primary"></i>Info Pelanggan</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="text-muted small">Nama</div>
                    <div class="fw-semibold">{{ $transaction->customer_name ?: 'Pelanggan Umum' }}</div>
                </div>
                @if($transaction->customer_phone)
                <div class="mb-2">
                    <div class="text-muted small">No. HP</div>
                    <div>{{ $transaction->customer_phone }}</div>
                </div>
                @endif
                @if($transaction->deadline)
                <div>
                    <div class="text-muted small">Deadline</div>
                    <div class="fw-semibold text-warning"><i class="bi bi-alarm me-1"></i>{{ $transaction->deadline->format('d F Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card mb-3">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-cash-stack me-2 text-success"></i>Info Pembayaran</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="text-muted small">Metode</div>
                    <div class="fw-semibold">{{ $transaction->payment_method_label }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted small">Total</div>
                    <div class="fw-bold fs-5" style="color:var(--primary);">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
                </div>
                @if($transaction->payment_method === 'tunai')
                <div class="mb-2">
                    <div class="text-muted small">Uang Bayar</div>
                    <div>Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-muted small">Kembalian</div>
                    <div class="fw-semibold text-success">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Kasir Info -->
        <div class="card">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-person-badge me-2 text-secondary"></i>Info Kasir</h6></div>
            <div class="card-body">
                <div class="mb-1">
                    <div class="text-muted small">Kasir</div>
                    <div>{{ $transaction->user->name }}</div>
                </div>
                <div>
                    <div class="text-muted small">Waktu Transaksi</div>
                    <div class="small">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
