@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Produk & Jasa')
@section('breadcrumb', 'Produk & Jasa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Produk & Jasa</h5>
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah Baru
    </a>
</div>

<div class="card mb-3">
    <div class="card-body p-2">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Nama Produk/Jasa</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th class="text-center">Status</th>
                        <th class="pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $product->name }}</div>
                            @if($product->description)
                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-light text-dark border">{{ $product->category }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="fw-bold" style="color:var(--primary);">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->unit }}</td>
                        <td class="text-center">
                            @if($product->is_active)
                                <span class="badge bg-success rounded-pill">Aktif</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Nonaktif</span>
                            @endif
                        </td>
                        <td class="pe-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-xs btn-outline-primary" style="padding:0.15rem 0.5rem;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                            Belum ada produk/jasa.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer py-2">{{ $products->links() }}</div>
    @endif
</div>
@endsection
