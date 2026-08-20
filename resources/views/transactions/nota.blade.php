<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $transaction->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .nota-container {
            background: white;
            width: 210mm;
            min-height: 150mm;
            padding: 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.12);
            border-radius: 4px;
            overflow: hidden;
        }
        .nota-header {
            background: {{ $setting->primary_color ?? '#0d6efd' }};
            color: white;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .nota-header .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nota-header .logo-area img {
            height: 50px; width: 50px; object-fit: contain; border-radius: 8px; background: rgba(255,255,255,0.2);
        }
        .nota-header .logo-icon {
            width: 50px; height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
        }
        .business-name { font-size: 1.3rem; font-weight: 800; }
        .business-tagline { font-size: 0.78rem; opacity: 0.85; margin-top: 2px; }
        .business-contact { font-size: 0.73rem; opacity: 0.8; margin-top: 4px; line-height: 1.6; }
        .invoice-info { text-align: right; }
        .invoice-number { font-size: 1.1rem; font-weight: 800; letter-spacing: 0.5px; }
        .invoice-label { font-size: 0.7rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .invoice-date { font-size: 0.78rem; opacity: 0.85; margin-top: 6px; }
        .nota-body { padding: 20px 28px; }
        .customer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e9ecef;
        }
        .customer-to { font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .customer-name { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
        .customer-phone { font-size: 0.8rem; color: #6b7280; margin-top: 2px; }
        .meta-item { text-align: right; }
        .meta-label { font-size: 0.68rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-value { font-size: 0.83rem; font-weight: 600; color: #374151; }
        table.items-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; margin-bottom: 16px; }
        table.items-table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        table.items-table thead th {
            padding: 8px 10px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 700;
        }
        table.items-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        table.items-table tbody tr:last-child td { border-bottom: none; }
        .item-name { font-weight: 600; color: #1f2937; }
        .item-desc { font-size: 0.73rem; color: #6b7280; margin-top: 2px; }
        .totals-section { margin-left: auto; width: 240px; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 0.83rem; color: #6b7280; }
        .total-final {
            display: flex; justify-content: space-between; padding: 10px 12px; margin-top: 6px;
            background: {{ $setting->primary_color ?? '#0d6efd' }};
            color: white; border-radius: 8px; font-weight: 800; font-size: 1rem;
        }
        .payment-section {
            margin-top: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            gap: 24px;
        }
        .pay-item { }
        .pay-label { font-size: 0.68rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .pay-value { font-size: 0.9rem; font-weight: 700; color: #1f2937; }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-lunas { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-batal { background: #fee2e2; color: #991b1b; }
        .nota-notes {
            margin-top: 14px;
            padding: 10px 14px;
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            font-size: 0.78rem;
            color: #78350f;
        }
        .nota-footer {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px dashed #e9ecef;
            text-align: center;
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .qr-section { text-align: center; }
        .qr-section canvas { width: 70px!important; height: 70px!important; }
        .deadline-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #fef3c7; color: #92400e;
            padding: 3px 10px; border-radius: 99px;
            font-size: 0.73rem; font-weight: 600;
        }
        .print-actions {
            position: fixed; bottom: 20px; right: 20px;
            display: flex; flex-direction: column; gap: 8px;
            z-index: 100;
        }
        .print-actions button {
            padding: 8px 18px; border: none; border-radius: 8px;
            cursor: pointer; font-size: 0.85rem; font-weight: 600;
            display: flex; align-items: center; gap: 6px;
        }
        @media print {
            body { background: white; padding: 0; }
            .nota-container { box-shadow: none; border-radius: 0; }
            .print-actions { display: none!important; }
            @page { margin: 0; size: A4; }
        }
    </style>
</head>
<body>

<div class="nota-container">
    <!-- Header -->
    <div class="nota-header">
        <div class="logo-area">
            @if($setting->logo)
                <img src="{{ $setting->logo_url }}" alt="Logo">
            @else
                <div class="logo-icon">🖨️</div>
            @endif
            <div>
                <div class="business-name">{{ $setting->business_name }}</div>
                @if($setting->business_tagline)
                    <div class="business-tagline">{{ $setting->business_tagline }}</div>
                @endif
                <div class="business-contact">
                    @if($setting->address)📍 {{ $setting->address }}<br>@endif
                    @if($setting->phone)📞 {{ $setting->phone }}@endif
                    @if($setting->whatsapp) &nbsp;💬 {{ $setting->whatsapp }}@endif
                    @if($setting->email)<br>✉️ {{ $setting->email }}@endif
                </div>
            </div>
        </div>
        <div class="invoice-info">
            <div class="invoice-label">Invoice</div>
            <div class="invoice-number">{{ $transaction->invoice_number }}</div>
            <div class="invoice-date">📅 {{ $transaction->created_at->format('d F Y, H:i') }}</div>
            <div class="invoice-date" style="margin-top:4px;">👤 {{ $transaction->user->name }}</div>
        </div>
    </div>

    <!-- Body -->
    <div class="nota-body">
        <!-- Customer & Meta -->
        <div class="customer-section">
            <div>
                <div class="customer-to">Kepada</div>
                <div class="customer-name">{{ $transaction->customer_name ?: 'Umum' }}</div>
                @if($transaction->customer_phone)
                <div class="customer-phone">📱 {{ $transaction->customer_phone }}</div>
                @endif
            </div>
            <div style="display:flex;gap:20px;align-items:flex-start;">
                @if($transaction->deadline)
                <div class="meta-item">
                    <div class="meta-label">Deadline</div>
                    <div class="deadline-badge">⏰ {{ $transaction->deadline->format('d M Y') }}</div>
                </div>
                @endif
                <div class="meta-item">
                    <div class="meta-label">Status</div>
                    <span class="status-badge status-{{ $transaction->status }}">
                        {{ $transaction->status_label }}
                    </span>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Pembayaran</div>
                    <div class="meta-value">{{ $transaction->payment_method_label }}</div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item / Jasa</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right" style="text-align:right;">Harga</th>
                    <th class="text-right" style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $i => $item)
                <tr>
                    <td style="color:#9ca3af;">{{ $i+1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->name }}</div>
                        @if($item->description)<div class="item-desc">{{ $item->description }}</div>@endif
                        @if($item->size)<div class="item-desc">📐 Ukuran: {{ $item->size }}</div>@endif
                        @if($item->paper_type)<div class="item-desc">📄 Kertas: {{ $item->paper_type }}</div>@endif
                        @if($item->color)<div class="item-desc">🎨 Warna: {{ $item->color }}</div>@endif
                        @if($item->custom_notes)<div class="item-desc">📝 {{ $item->custom_notes }}</div>@endif
                    </td>
                    <td class="text-center">{{ $item->qty }} {{ $item->unit }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals + QR -->
        <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;">
            <!-- QR Code -->
            <div>
                @if($setting->show_qr)
                <div class="qr-section">
                    <canvas id="qrCanvas"></canvas>
                    <div style="font-size:0.65rem;color:#9ca3af;margin-top:4px;">{{ $transaction->invoice_number }}</div>
                </div>
                @endif
            </div>

            <!-- Totals -->
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaction->discount > 0)
                <div class="total-row">
                    <span>Diskon</span>
                    <span style="color:#ef4444;">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($transaction->tax > 0)
                <div class="total-row">
                    <span>Pajak ({{ $transaction->tax_percent }}%)</span>
                    <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="total-final">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-section">
            <div class="pay-item">
                <div class="pay-label">Metode Bayar</div>
                <div class="pay-value">{{ $transaction->payment_method_label }}</div>
            </div>
            @if($transaction->payment_method === 'tunai')
            <div class="pay-item">
                <div class="pay-label">Uang Bayar</div>
                <div class="pay-value">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</div>
            </div>
            <div class="pay-item">
                <div class="pay-label">Kembalian</div>
                <div class="pay-value">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</div>
            </div>
            @endif
        </div>

        @if($transaction->notes)
        <div class="nota-notes">
            📝 <strong>Catatan:</strong> {{ $transaction->notes }}
        </div>
        @endif

        <!-- Footer -->
        <div class="nota-footer">
            @if($setting->footer_text)
                <div style="margin-bottom:4px;">{{ $setting->footer_text }}</div>
            @endif
            @if($setting->custom_notes)
                <div>{{ $setting->custom_notes }}</div>
            @endif
            @if($setting->whatsapp)
                <div style="margin-top:4px;">WhatsApp: {{ $setting->whatsapp }}</div>
            @endif
            <div style="margin-top:6px;color:#d1d5db;font-size:0.68rem;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>

<!-- Print Actions -->
<div class="print-actions no-print">
    <button onclick="window.print()" style="background:{{ $setting->primary_color ?? '#0d6efd' }};color:white;">
        🖨️ Cetak Nota
    </button>
    <button onclick="window.close()" style="background:#6c757d;color:white;">
        ✕ Tutup
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
@if($setting->show_qr)
QRCode.toCanvas(document.getElementById('qrCanvas'), '{{ $transaction->invoice_number }}', {
    width: 70, margin: 1,
    color: { dark: '#1f2937', light: '#ffffff' }
});
@endif

// Auto print if opened for direct print
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('autoprint') === '1') {
    window.onload = () => setTimeout(() => window.print(), 500);
}
</script>
</body>
</html>
