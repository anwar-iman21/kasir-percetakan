@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Pelanggan')
@section('breadcrumb', 'Data Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Data Pelanggan</h5>
    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah Pelanggan
    </a>
</div>

<div class="card mb-3">
    <div class="card-body p-2">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari nama, HP, email..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-search"></i></button>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th class="text-center">Total Transaksi</th>
                        <th class="pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $customer->name }}</div>
                            @if($customer->address)
                                <small class="text-muted">{{ Str::limit($customer->address, 40) }}</small>
                            @endif
                        </td>
                        <td>{{ $customer->phone ?: '-' }}</td>
                        <td>{{ $customer->email ?: '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill">{{ $customer->transactions_count }}</span>
                        </td>
                        <td class="pe-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-xs btn-outline-secondary" style="padding:0.15rem 0.5rem;" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-xs btn-outline-primary" style="padding:0.15rem 0.5rem;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" class="d-inline" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" style="padding:0.15rem 0.5rem;" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people d-block mb-2" style="font-size:2rem;"></i>
                            Belum ada data pelanggan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer py-2">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
