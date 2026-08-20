@php $setting = \App\Models\InvoiceSetting::getSetting(); @endphp
@extends('layouts.app')
@section('title', 'Kasir / POS')
@section('breadcrumb', 'Kasir / POS')

@push('styles')
<style>
.pos-wrapper {
    display: flex;
    gap: 1rem;
    height: calc(100vh - var(--header-height) - 3rem);
}
.pos-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.pos-right {
    width: 360px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 0.5rem;
    overflow-y: auto;
    flex: 1;
    padding: 0.5rem 0;
}
.product-btn {
    border: 1.5px solid #e9ecef;
    border-radius: 10px;
    padding: 0.65rem 0.5rem;
    background: white;
    cursor: pointer;
    transition: all 0.15s;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.product-btn:hover {
    border-color: var(--primary);
    background: rgba(13,110,253,0.05);
    transform: translateY(-1px);
}
.product-btn .p-name {
    font-size: 0.78rem;
    font-weight: 600;
    line-height: 1.2;
    color: inherit;
}
.product-btn .p-price {
    font-size: 0.72rem;
    color: var(--primary);
    font-weight: 700;
}
.product-btn .p-cat {
    font-size: 0.65rem;
    color: #9ca3af;
}
.cart-items {
    flex: 1;
    overflow-y: auto;
}
.cart-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 0.6rem 0.75rem;
    margin-bottom: 0.4rem;
    border: 1px solid #e9ecef;
}
[data-theme="dark"] .product-btn { background: #2d313a; border-color: #3a3f4a; }
[data-theme="dark"] .cart-item { background: #2d313a; border-color: #3a3f4a; }
[data-theme="dark"] .product-btn:hover { border-color: var(--primary); background: rgba(13,110,253,0.1); }
.category-tabs { overflow-x: auto; white-space: nowrap; scrollbar-width: none; }
.category-tabs::-webkit-scrollbar { display: none; }
.cat-tab {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    border-radius: 99px;
    font-size: 0.78rem;
    cursor: pointer;
    border: 1.5px solid #e9ecef;
    margin-right: 0.35rem;
    transition: all 0.15s;
    background: transparent;
}
.cat-tab.active, .cat-tab:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}
.total-section {
    background: var(--primary);
    border-radius: 12px;
    padding: 1rem;
    color: white;
}
@media (max-width: 768px) {
    .pos-wrapper { flex-direction: column; height: auto; }
    .pos-right { width: 100%; }
    .product-grid { grid-template-columns: repeat(3, 1fr); max-height: 300px; }
}
</style>
@endpush

@section('content')
<div class="pos-wrapper">
    <!-- LEFT: Products -->
    <div class="pos-left">
        <div class="card mb-2 flex-shrink-0">
            <div class="card-body p-2">
                <div class="d-flex gap-2 mb-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk/jasa...">
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="showCustomItem()" title="Item Custom">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>
                <div class="category-tabs" id="categoryTabs">
                    <span class="cat-tab active" data-cat="">Semua</span>
                    @foreach($products->keys() as $cat)
                        @if($cat)
                        <span class="cat-tab" data-cat="{{ $cat }}">{{ $cat }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="product-grid" id="productGrid">
            @foreach($products as $cat => $items)
                @foreach($items as $item)
                <div class="product-btn" data-cat="{{ $cat }}" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-unit="{{ $item->unit }}"
                    onclick="addToCart({id: {{ $item->id }}, name: '{{ addslashes($item->name) }}', price: {{ $item->price }}, unit: '{{ $item->unit }}'})">
                    <div class="p-cat">{{ $cat ?: 'Umum' }}</div>
                    <div class="p-name">{{ $item->name }}</div>
                    <div class="p-price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    <div style="font-size:0.65rem;color:#aaa;">/ {{ $item->unit }}</div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- RIGHT: Cart & Checkout -->
    <div class="pos-right">
        <div class="card flex-shrink-0 mb-2">
            <div class="card-body p-2">
                <div class="row g-2">
                    <div class="col-7">
                        <input type="text" id="customerName" class="form-control form-control-sm" placeholder="Nama pelanggan (opsional)" autocomplete="off">
                        <div id="customerSuggestions" class="position-relative"></div>
                    </div>
                    <div class="col-5">
                        <input type="text" id="customerPhone" class="form-control form-control-sm" placeholder="No. HP">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="card flex-1 mb-2" style="flex:1;overflow:hidden;display:flex;flex-direction:column;">
            <div class="card-header p-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-cart3 me-1"></i>Keranjang (<span id="cartCount">0</span>)</span>
                <button class="btn btn-xs btn-outline-danger" onclick="clearCart()" style="font-size:0.7rem;padding:0.15rem 0.4rem;">
                    <i class="bi bi-trash"></i> Bersihkan
                </button>
            </div>
            <div class="card-body p-2 cart-items" id="cartItems">
                <div id="cartEmpty" class="text-center text-muted py-4">
                    <i class="bi bi-cart3 d-block mb-2" style="font-size:2rem;"></i>
                    <div class="small">Pilih produk/jasa di sebelah kiri</div>
                </div>
            </div>
        </div>

        <!-- Order Notes & Deadline -->
        <div class="card flex-shrink-0 mb-2">
            <div class="card-body p-2">
                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" id="orderNotes" class="form-control form-control-sm" placeholder="Catatan pesanan (opsional)">
                    </div>
                    <div class="col-12">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                            <input type="date" id="orderDeadline" class="form-control form-control-sm" title="Deadline pengerjaan">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals & Payment -->
        <div class="card flex-shrink-0">
            <div class="card-body p-2">
                <div class="row g-1 mb-2">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" title="Diskon">D</span>
                            <input type="number" id="discountInput" class="form-control" placeholder="Diskon" min="0" oninput="recalculate()">
                            <select id="discountType" class="form-select" style="max-width:60px;" onchange="recalculate()">
                                <option value="nominal">Rp</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <select id="paymentMethod" class="form-select form-select-sm">
                            <option value="tunai">💵 Tunai</option>
                            <option value="transfer">🏦 Transfer</option>
                            <option value="qris">📱 QRIS</option>
                        </select>
                    </div>
                </div>

                <div class="total-section mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1" id="discountRow" style="display:none!important;">
                        <span>Diskon</span>
                        <span id="discountDisplay" class="text-warning">- Rp 0</span>
                    </div>
                    @if($setting->tax_enabled)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Pajak ({{ $setting->tax_percent }}%)</span>
                        <span id="taxDisplay">Rp 0</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between fw-bold mt-1 pt-1" style="border-top:1px solid rgba(255,255,255,0.3);">
                        <span>TOTAL</span>
                        <span id="totalDisplay" style="font-size:1.1rem;">Rp 0</span>
                    </div>
                </div>

                <div class="mb-2" id="cashSection">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-cash"></i></span>
                        <input type="number" id="amountPaid" class="form-control" placeholder="Uang bayar" min="0" oninput="calcChange()">
                        <button class="btn btn-outline-secondary btn-sm" onclick="setExactPay()" title="Uang pas">Pas</button>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">Kembalian:</small>
                        <small class="fw-bold" id="changeDisplay">Rp 0</small>
                    </div>
                </div>

                <div class="d-grid gap-1">
                    <button class="btn btn-success btn-sm fw-bold" onclick="saveTransaction()" id="btnSave">
                        <i class="bi bi-check-circle me-1"></i> Simpan & Bayar
                    </button>
                    <div class="row g-1">
                        <div class="col-6">
                            <button class="btn btn-outline-secondary btn-sm w-100" onclick="clearCart()">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100" onclick="saveTransaction(true)" id="btnSavePrint">
                                <i class="bi bi-printer me-1"></i>Simpan & Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Item Modal -->
<div class="modal fade" id="customItemModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Item Custom</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Nama Item *</label>
                    <input type="text" id="ci_name" class="form-control form-control-sm" placeholder="Contoh: Banner 3x2m Flexi">
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Deskripsi / Spesifikasi</label>
                    <input type="text" id="ci_desc" class="form-control form-control-sm" placeholder="Bahan, warna, ukuran, dll">
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Ukuran</label>
                        <input type="text" id="ci_size" class="form-control form-control-sm" placeholder="2x1 meter">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Jenis Kertas</label>
                        <input type="text" id="ci_paper" class="form-control form-control-sm" placeholder="A4, Flexi, dll">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Harga *</label>
                        <input type="number" id="ci_price" class="form-control form-control-sm" placeholder="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Satuan</label>
                        <input type="text" id="ci_unit" class="form-control form-control-sm" placeholder="pcs" value="pcs">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Catatan Khusus</label>
                    <textarea id="ci_notes" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="addCustomItem()">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal after save -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <div style="width:64px;height:64px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:#059669;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h5 class="fw-bold">Transaksi Berhasil!</h5>
                <p class="text-muted small mb-1">Invoice: <strong id="successInvoice"></strong></p>
                <p class="text-muted small mb-3">Total: <strong id="successTotal"></strong></p>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" id="btnPrintNota">
                        <i class="bi bi-printer me-1"></i>Cetak Nota A4
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="btnPrintThermal">
                        <i class="bi bi-receipt me-1"></i>Cetak Thermal
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="newTransaction()">
                        <i class="bi bi-plus-circle me-1"></i>Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let cart = [];
let lastTxId = null;
const taxEnabled = {{ $setting->tax_enabled ? 'true' : 'false' }};
const taxPercent = {{ $setting->tax_percent ?? 0 }};

// --- PRODUCT FILTER ---
document.getElementById('searchProduct').addEventListener('input', function() {
    filterProducts(this.value, activeCategory);
});

let activeCategory = '';
document.querySelectorAll('.cat-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        activeCategory = this.dataset.cat;
        filterProducts(document.getElementById('searchProduct').value, activeCategory);
    });
});

function filterProducts(search, cat) {
    document.querySelectorAll('.product-btn').forEach(btn => {
        const matchSearch = !search || btn.dataset.name.toLowerCase().includes(search.toLowerCase());
        const matchCat = !cat || btn.dataset.cat === cat;
        btn.style.display = (matchSearch && matchCat) ? '' : 'none';
    });
}

// --- CART ---
function addToCart(item, options = {}) {
    const existing = cart.findIndex(c => c.id === item.id && !c.custom);
    if (existing > -1) {
        cart[existing].qty++;
    } else {
        cart.push({
            id: item.id || null,
            name: item.name,
            price: item.price,
            qty: 1,
            unit: item.unit || 'pcs',
            description: options.description || '',
            paper_type: options.paper_type || '',
            size: options.size || '',
            color: options.color || '',
            custom_notes: options.notes || '',
            custom: options.custom || false,
        });
    }
    renderCart();
}

function renderCart() {
    const el = document.getElementById('cartItems');
    const empty = document.getElementById('cartEmpty');
    document.getElementById('cartCount').textContent = cart.reduce((s, i) => s + i.qty, 0);

    if (cart.length === 0) {
        empty.style.display = '';
        el.innerHTML = '';
        el.appendChild(empty);
        recalculate();
        return;
    }
    empty.style.display = 'none';

    let html = '';
    cart.forEach((item, idx) => {
        const subtotal = item.price * item.qty;
        html += `
        <div class="cart-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div style="flex:1;min-width:0;">
                    <div class="fw-semibold" style="font-size:0.8rem;">${escHtml(item.name)}</div>
                    ${item.description ? `<div class="text-muted" style="font-size:0.7rem;">${escHtml(item.description)}</div>` : ''}
                    ${item.size ? `<div class="text-muted" style="font-size:0.7rem;">📐 ${escHtml(item.size)}</div>` : ''}
                    ${item.custom_notes ? `<div class="text-muted" style="font-size:0.7rem;">📝 ${escHtml(item.custom_notes)}</div>` : ''}
                </div>
                <button class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="removeItem(${idx})">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-xs btn-outline-secondary" style="padding:0.1rem 0.4rem;font-size:0.75rem;" onclick="changeQty(${idx}, -1)">-</button>
                    <input type="number" value="${item.qty}" min="1" class="form-control form-control-sm text-center"
                        style="width:50px;font-size:0.8rem;padding:0.1rem;" onchange="setQty(${idx}, this.value)">
                    <button class="btn btn-xs btn-outline-secondary" style="padding:0.1rem 0.4rem;font-size:0.75rem;" onclick="changeQty(${idx}, 1)">+</button>
                    <small class="text-muted">${escHtml(item.unit)}</small>
                </div>
                <div class="text-end">
                    <div style="font-size:0.7rem;color:#9ca3af;">@Rp ${fmt(item.price)}</div>
                    <div class="fw-bold" style="font-size:0.82rem;color:var(--primary);">Rp ${fmt(subtotal)}</div>
                </div>
            </div>
        </div>`;
    });

    el.innerHTML = html;
    recalculate();
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function removeItem(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function changeQty(idx, delta) {
    cart[idx].qty = Math.max(1, cart[idx].qty + delta);
    renderCart();
}

function setQty(idx, val) {
    cart[idx].qty = Math.max(1, parseInt(val) || 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function newTransaction() {
    clearCart();
    document.getElementById('customerName').value = '';
    document.getElementById('customerPhone').value = '';
    document.getElementById('orderNotes').value = '';
    document.getElementById('orderDeadline').value = '';
    document.getElementById('discountInput').value = '';
    document.getElementById('amountPaid').value = '';
    bootstrap.Modal.getInstance(document.getElementById('successModal'))?.hide();
}

// --- CALCULATE ---
function fmt(n) { return Number(n).toLocaleString('id-ID'); }

function recalculate() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    let discount = 0;
    const discVal = parseFloat(document.getElementById('discountInput').value) || 0;
    const discType = document.getElementById('discountType').value;
    if (discType === 'percent') discount = subtotal * (discVal / 100);
    else discount = discVal;
    discount = Math.min(discount, subtotal);

    const afterDiscount = subtotal - discount;
    let tax = 0;
    if (taxEnabled) tax = afterDiscount * (taxPercent / 100);
    const total = afterDiscount + tax;

    document.getElementById('subtotalDisplay').textContent = 'Rp ' + fmt(subtotal);
    document.getElementById('discountDisplay').textContent = '- Rp ' + fmt(discount);
    document.getElementById('discountRow').style.display = discount > 0 ? '' : 'none';
    if (document.getElementById('taxDisplay')) document.getElementById('taxDisplay').textContent = 'Rp ' + fmt(tax);
    document.getElementById('totalDisplay').textContent = 'Rp ' + fmt(total);

    const pm = document.getElementById('paymentMethod').value;
    document.getElementById('cashSection').style.display = pm === 'tunai' ? '' : 'none';

    calcChange();
}

document.getElementById('paymentMethod').addEventListener('change', recalculate);

function calcChange() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discVal = parseFloat(document.getElementById('discountInput').value) || 0;
    const discType = document.getElementById('discountType').value;
    let discount = discType === 'percent' ? subtotal * (discVal / 100) : discVal;
    discount = Math.min(discount, subtotal);
    const afterDiscount = subtotal - discount;
    let tax = 0;
    if (taxEnabled) tax = afterDiscount * (taxPercent / 100);
    const total = afterDiscount + tax;
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = paid - total;
    document.getElementById('changeDisplay').textContent = 'Rp ' + fmt(Math.max(0, change));
    document.getElementById('changeDisplay').style.color = change < 0 ? '#dc3545' : '#198754';
}

function setExactPay() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discVal = parseFloat(document.getElementById('discountInput').value) || 0;
    const discType = document.getElementById('discountType').value;
    let discount = discType === 'percent' ? subtotal * (discVal / 100) : discVal;
    const afterDiscount = subtotal - discount;
    let tax = taxEnabled ? afterDiscount * (taxPercent / 100) : 0;
    const total = afterDiscount + tax;
    document.getElementById('amountPaid').value = total;
    calcChange();
}

// --- CUSTOM ITEM ---
function showCustomItem() {
    document.getElementById('ci_name').value = '';
    document.getElementById('ci_desc').value = '';
    document.getElementById('ci_size').value = '';
    document.getElementById('ci_paper').value = '';
    document.getElementById('ci_price').value = '';
    document.getElementById('ci_unit').value = 'pcs';
    document.getElementById('ci_notes').value = '';
    new bootstrap.Modal(document.getElementById('customItemModal')).show();
}

function addCustomItem() {
    const name = document.getElementById('ci_name').value.trim();
    const price = parseFloat(document.getElementById('ci_price').value) || 0;
    if (!name) { alert('Nama item wajib diisi!'); return; }
    addToCart({
        id: 'custom_' + Date.now(),
        name: name,
        price: price,
        unit: document.getElementById('ci_unit').value || 'pcs',
    }, {
        description: document.getElementById('ci_desc').value,
        size: document.getElementById('ci_size').value,
        paper_type: document.getElementById('ci_paper').value,
        notes: document.getElementById('ci_notes').value,
        custom: true,
    });
    bootstrap.Modal.getInstance(document.getElementById('customItemModal')).hide();
}

// --- CUSTOMER SEARCH ---
let customerSearchTimeout;
document.getElementById('customerName').addEventListener('input', function() {
    clearTimeout(customerSearchTimeout);
    const q = this.value.trim();
    if (q.length < 2) { hideSuggestions(); return; }
    customerSearchTimeout = setTimeout(() => {
        fetch(`{{ route('kasir.customer.search') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) { hideSuggestions(); return; }
                let html = '<div class="list-group" style="position:absolute;top:0;left:0;right:0;z-index:1000;box-shadow:0 4px 12px rgba(0,0,0,0.15);border-radius:8px;overflow:hidden;">';
                data.forEach(c => {
                    html += `<button type="button" class="list-group-item list-group-item-action py-1 px-2 small" onclick="selectCustomer(${c.id}, '${escHtml(c.name)}', '${c.phone || ''}')">
                        <i class="bi bi-person me-1"></i><strong>${escHtml(c.name)}</strong> ${c.phone ? `<span class="text-muted">(${c.phone})</span>` : ''}
                    </button>`;
                });
                html += '</div>';
                document.getElementById('customerSuggestions').innerHTML = html;
            });
    }, 300);
});

function selectCustomer(id, name, phone) {
    document.getElementById('customerName').value = name;
    document.getElementById('customerPhone').value = phone;
    document.getElementById('customerName').dataset.customerId = id;
    hideSuggestions();
}

function hideSuggestions() {
    document.getElementById('customerSuggestions').innerHTML = '';
}

document.addEventListener('click', e => {
    if (!e.target.closest('#customerName') && !e.target.closest('#customerSuggestions')) {
        hideSuggestions();
    }
});

// --- SAVE TRANSACTION ---
async function saveTransaction(autoPrint = false) {
    if (cart.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Tambahkan produk/jasa terlebih dahulu!', confirmButtonColor: 'var(--primary)' });
        return;
    }

    const pm = document.getElementById('paymentMethod').value;
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discVal = parseFloat(document.getElementById('discountInput').value) || 0;
    const discType = document.getElementById('discountType').value;
    let discount = discType === 'percent' ? subtotal * (discVal / 100) : discVal;
    discount = Math.min(discount, subtotal);
    const total = subtotal - discount + (taxEnabled ? (subtotal - discount) * taxPercent / 100 : 0);

    if (pm === 'tunai' && amountPaid < total) {
        Swal.fire({ icon: 'error', title: 'Uang Kurang', text: `Uang bayar (Rp ${fmt(amountPaid)}) kurang dari total (Rp ${fmt(total)})`, confirmButtonColor: 'var(--primary)' });
        return;
    }

    const btnSave = document.getElementById('btnSave');
    btnSave.disabled = true;
    btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

    const payload = {
        customer_id: document.getElementById('customerName').dataset.customerId || null,
        customer_name: document.getElementById('customerName').value || null,
        customer_phone: document.getElementById('customerPhone').value || null,
        payment_method: pm,
        amount_paid: pm === 'tunai' ? amountPaid : total,
        discount: discVal,
        discount_type: discType,
        notes: document.getElementById('orderNotes').value,
        deadline: document.getElementById('orderDeadline').value || null,
        apply_tax: taxEnabled,
        items: cart.map(item => ({
            product_service_id: typeof item.id === 'number' ? item.id : null,
            name: item.name,
            description: item.description,
            paper_type: item.paper_type,
            size: item.size,
            qty: item.qty,
            unit: item.unit,
            price: item.price,
            custom_notes: item.custom_notes,
        })),
    };

    try {
        const res = await fetch('{{ route("kasir.save") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            lastTxId = data.transaction_id;
            document.getElementById('successInvoice').textContent = data.invoice_number;
            document.getElementById('successTotal').textContent = 'Rp ' + fmt(total);

            document.getElementById('btnPrintNota').onclick = () => window.open(`/transaksi/${lastTxId}/nota`, '_blank');
            document.getElementById('btnPrintThermal').onclick = () => window.open(`/transaksi/${lastTxId}/nota-thermal`, '_blank');

            clearCart();
            document.getElementById('customerName').removeAttribute('data-customer-id');

            if (autoPrint) {
                window.open(`/transaksi/${lastTxId}/nota`, '_blank');
                newTransaction();
                showToast('Transaksi berhasil disimpan & nota dibuka!', 'success');
            } else {
                new bootstrap.Modal(document.getElementById('successModal')).show();
            }
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi.' });
    } finally {
        btnSave.disabled = false;
        btnSave.innerHTML = '<i class="bi bi-check-circle me-1"></i> Simpan & Bayar';
    }
}
</script>
@endpush
