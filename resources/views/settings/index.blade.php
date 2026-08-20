@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')

@section('content')
<h5 class="fw-bold mb-3"><i class="bi bi-gear me-2 text-primary"></i>Pengaturan Aplikasi</h5>

<div class="row g-3">
    <!-- Identitas Usaha -->
    <div class="col-lg-8">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf @method('PUT')
            <div class="card mb-3">
                <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-shop me-2 text-primary"></i>Identitas Usaha</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $setting->business_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tagline / Slogan</label>
                            <input type="text" name="business_tagline" class="form-control" value="{{ old('business_tagline', $setting->business_tagline) }}" placeholder="Solusi Cetak Profesional">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $setting->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $setting->whatsapp) }}" placeholder="628xxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-receipt me-2 text-primary"></i>Pengaturan Nota</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prefix Invoice</label>
                            <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', $setting->invoice_prefix) }}" placeholder="INV">
                            <div class="form-text">Contoh: INV, TRX, FCT</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ukuran Kertas Default</label>
                            <select name="paper_size" class="form-select">
                                <option value="a4" {{ $setting->paper_size == 'a4' ? 'selected' : '' }}>A4</option>
                                <option value="a5" {{ $setting->paper_size == 'a5' ? 'selected' : '' }}>A5</option>
                                <option value="thermal80" {{ $setting->paper_size == 'thermal80' ? 'selected' : '' }}>Thermal 80mm</option>
                                <option value="thermal58" {{ $setting->paper_size == 'thermal58' ? 'selected' : '' }}>Thermal 58mm</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ukuran Font (px)</label>
                            <select name="font_size" class="form-select">
                                <option value="11" {{ $setting->font_size == '11' ? 'selected' : '' }}>11px (Kecil)</option>
                                <option value="12" {{ $setting->font_size == '12' ? 'selected' : '' }}>12px (Normal)</option>
                                <option value="13" {{ $setting->font_size == '13' ? 'selected' : '' }}>13px (Sedang)</option>
                                <option value="14" {{ $setting->font_size == '14' ? 'selected' : '' }}>14px (Besar)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Footer Nota</label>
                            <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $setting->footer_text) }}" placeholder="Terima kasih telah mempercayakan kebutuhan cetak Anda...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan di Nota</label>
                            <input type="text" name="custom_notes" class="form-control" value="{{ old('custom_notes', $setting->custom_notes) }}" placeholder="Barang yang sudah dibeli tidak dapat dikembalikan...">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="show_qr" id="showQr" value="1" {{ $setting->show_qr ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="showQr">Tampilkan QR Code di Nota</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-percent me-2 text-primary"></i>Pajak</h6></div>
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="tax_enabled" id="taxEnabled" value="1"
                                    {{ $setting->tax_enabled ? 'checked' : '' }} onchange="toggleTax(this)">
                                <label class="form-check-label fw-semibold" for="taxEnabled">Aktifkan Pajak</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="taxPercentField" style="{{ !$setting->tax_enabled ? 'opacity:0.4;pointer-events:none;' : '' }}">
                            <label class="form-label fw-semibold">Persentase Pajak (%)</label>
                            <div class="input-group">
                                <input type="number" name="tax_percent" class="form-control" value="{{ old('tax_percent', $setting->tax_percent) }}" min="0" max="100" step="0.5">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-palette me-2 text-primary"></i>Tampilan</h6></div>
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warna Tema Utama</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="primary_color" class="form-control form-control-color"
                                    value="{{ old('primary_color', $setting->primary_color) }}" style="width:50px;height:38px;padding:2px;">
                                <input type="text" id="colorHex" class="form-control form-control-sm" value="{{ $setting->primary_color }}"
                                    oninput="document.querySelector('[name=primary_color]').value=this.value" placeholder="#0d6efd">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="dark_mode" id="darkMode" value="1" {{ $setting->dark_mode ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="darkMode">Dark Mode</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Logo Upload + Preview -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-image me-2 text-primary"></i>Logo Usaha</h6></div>
            <div class="card-body text-center">
                <div id="logoPreview" class="mb-3" style="min-height:80px;display:flex;align-items:center;justify-content:center;">
                    @if($setting->logo)
                        <img src="{{ $setting->logo_url }}" alt="Logo" style="max-height:100px;max-width:100%;border-radius:8px;">
                    @else
                        <div style="width:80px;height:80px;background:#f0f2f5;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#9ca3af;">
                            <i class="bi bi-image"></i>
                        </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('settings.logo') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="logo" id="logoInput" class="form-control form-control-sm mb-2" accept="image/*" onchange="previewLogo(this)">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-upload me-1"></i>Upload Logo
                    </button>
                    <div class="form-text">JPG/PNG, maks. 2MB</div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-2"><h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2 text-primary"></i>Info Akun</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="text-muted small">Login sebagai</div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted small">Email</div>
                    <div>{{ auth()->user()->email }}</div>
                </div>
                <div>
                    <div class="text-muted small">Role</div>
                    <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('logoPreview').innerHTML = `<img src="${e.target.result}" style="max-height:100px;max-width:100%;border-radius:8px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function toggleTax(cb) {
    const field = document.getElementById('taxPercentField');
    field.style.opacity = cb.checked ? '1' : '0.4';
    field.style.pointerEvents = cb.checked ? 'auto' : 'none';
}
document.querySelector('[name=primary_color]').addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});
</script>
@endpush
@endsection
