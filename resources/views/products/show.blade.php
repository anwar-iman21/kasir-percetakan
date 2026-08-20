@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Detail Produk')
@section('breadcrumb', 'Detail Produk')

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0">{{ $product->name }}</h5>
</div>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Nama</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $product->name }}</dd>
                    <dt class="col-sm-4 text-muted">Kategori</dt>
                    <dd class="col-sm-8">{{ $product->category ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Harga</dt>
                    <dd class="col-sm-8 fw-bold" style="color:var(--primary);">{{ $product->formatted_price }}</dd>
                    <dt class="col-sm-4 text-muted">Satuan</dt>
                    <dd class="col-sm-8">{{ $product->unit }}</dd>
                    <dt class="col-sm-4 text-muted">Deskripsi</dt>
                    <dd class="col-sm-8">{{ $product->description ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8">
                        @if($product->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </dd>
                </dl>
                <div class="mt-3 d-flex gap-2">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
