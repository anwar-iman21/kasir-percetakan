<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .thermal {
            background: white;
            width: 80mm;
            padding: 6mm 5mm;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }
        .t-center { text-align: center; }
        .t-right { text-align: right; }
        .t-bold { font-weight: bold; }
        .t-big { font-size: 1.1rem; }
        .t-sm { font-size: 0.72rem; }
        .t-xs { font-size: 0.65rem; color: #6b7280; }
        .divider { border-top: 1px dashed #888; margin: 5px 0; }
        .divider-solid { border-top: 1px solid #333; margin: 5px 0; }
        table { width: 100%; font-size: 0.75rem; border-collapse: collapse; }
        td { vertical-align: top; padding: 1px 0; }
        .total-row { font-weight: bold; font-size: 0.85rem; }
        .grand-total {
            display: flex; justify-content: space-between;
            font-weight: 900; font-size: 1rem;
            padding: 5px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            margin: 4px 0;
        }
        .qr-wrap { text-align: center; margin: 8px 0; }
        .print-actions {
            position: fixed; bottom: 20px; right: 20px;
            display: flex; flex-direction: column; gap: 6px;
        }
        .print-actions button {
            padding: 7px 16px; border: none; border-radius: 6px;
            cursor: pointer; font-size: 0.82rem; font-weight: 600;
        }
        @media print {
            body { background: white; padding: 0; }
            .thermal { box-shadow: none; }
            .print-actions { display: none !important; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>
<div class="thermal">
    <!-- Header -->
    <div class="t-center">
        @if($setting->logo)
            <img src="{{ $setting->logo_url }}" alt="" style="height:40px;margin-bottom:4px;">
            <br>
        @endif
        <div class="t-bold t-big">{{ $setting->business_name }}</div>
        @if($setting->business_tagline)
            <div class="t-xs">{{ $setting->business_tagline }}</div>
        @endif
        @if($setting->address)
            <div class="t-xs">{{ $setting->address }}</div>
        @endif
        @if($setting->phone)
            <div class="t-xs">📞 {{ $setting->phone }}</div>
        @endif
        @if($setting->whatsapp)
            <div class="t-xs">💬 WA: {{ $setting->whatsapp }}</div>
        @endif
    </div>

    <div class="divider-solid"></div>

    <table>
        <tr>
            <td class="t-sm">Invoice</td>
            <td class="t-right t-sm t-bold">{{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td class="t-sm">Tanggal</td>
            <td class="t-right t-sm">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="t-sm">Kasir</td>
            <td class="t-right t-sm">{{ $transaction->user->name }}</td>
        </tr>
        <tr>
            <td class="t-sm">Pelanggan</td>
            <td class="t-right t-sm">{{ $transaction->customer_name ?: 'Umum' }}</td>
        </tr>
        @if($transaction->customer_phone)
        <tr>
            <td class="t-sm">No. HP</td>
            <td class="t-right t-sm">{{ $transaction->customer_phone }}</td>
        </tr>
        @endif
        @if($transaction->deadline)
        <tr>
            <td class="t-sm">Deadline</td>
            <td class="t-right t-sm">{{ $transaction->deadline->format('d/m/Y') }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- Items -->
    @foreach($transaction->items as $item)
    <div style="font-size:0.78rem;">
        <div class="t-bold">{{ $item->name }}</div>
        @if($item->description)<div class="t-xs">{{ $item->description }}</div>@endif
        @if($item->size)<div class="t-xs">Ukuran: {{ $item->size }}</div>@endif
        @if($item->paper_type)<div class="t-xs">Kertas: {{ $item->paper_type }}</div>@endif
        @if($item->custom_notes)<div class="t-xs">Catatan: {{ $item->custom_notes }}</div>@endif
        <table>
            <tr>
                <td class="t-xs">{{ $item->qty }} {{ $item->unit }} x Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="t-right t-bold" style="font-size:0.78rem;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    <div class="divider"></div>
    @endforeach

    <!-- Totals -->
    <table>
        <tr>
            <td class="t-sm">Subtotal</td>
            <td class="t-right t-sm">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount > 0)
        <tr>
            <td class="t-sm">Diskon</td>
            <td class="t-right t-sm">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($transaction->tax > 0)
        <tr>
            <td class="t-sm">Pajak ({{ $transaction->tax_percent }}%)</td>
            <td class="t-right t-sm">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    <div class="grand-total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
    </div>

    <table style="margin-top:4px;">
        <tr>
            <td class="t-sm">Bayar ({{ $transaction->payment_method_label }})</td>
            <td class="t-right t-sm">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->payment_method === 'tunai')
        <tr>
            <td class="t-sm t-bold">Kembali</td>
            <td class="t-right t-sm t-bold">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>

    @if($transaction->notes)
    <div class="divider"></div>
    <div class="t-xs t-center">📝 {{ $transaction->notes }}</div>
    @endif

    @if($setting->show_qr)
    <div class="divider"></div>
    <div class="qr-wrap">
        <canvas id="qrCanvas"></canvas>
        <div class="t-xs" style="margin-top:2px;">{{ $transaction->invoice_number }}</div>
    </div>
    @endif

    <div class="divider"></div>
    <div class="t-center">
        @if($setting->footer_text)
            <div class="t-xs">{{ $setting->footer_text }}</div>
        @else
            <div class="t-xs">Terima kasih atas kepercayaan Anda!</div>
        @endif
        @if($setting->custom_notes)
            <div class="t-xs">{{ $setting->custom_notes }}</div>
        @endif
        <div class="t-xs" style="margin-top:4px;color:#d1d5db;">{{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="print-actions">
    <button onclick="window.print()" style="background:#198754;color:white;">🖨️ Cetak</button>
    <button onclick="window.close()" style="background:#6c757d;color:white;">✕ Tutup</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
@if($setting->show_qr)
QRCode.toCanvas(document.getElementById('qrCanvas'), '{{ $transaction->invoice_number }}', {
    width: 80, margin: 1,
});
@endif
</script>
</body>
</html>
