<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\ProductService;
use App\Models\Customer;
use App\Models\InvoiceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $products = ProductService::active()->ordered()->get()->groupBy('category');
        $setting = InvoiceSetting::getSetting();
        return view('kasir.index', compact('products', 'setting'));
    }

    public function getProducts(Request $request)
    {
        $query = ProductService::active()->ordered();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        return response()->json($query->get());
    }

    public function searchCustomer(Request $request)
    {
        $q = $request->q;
        $customers = Customer::where('name', 'like', "%$q%")
            ->orWhere('phone', 'like', "%$q%")
            ->limit(10)
            ->get(['id', 'name', 'phone']);
        return response()->json($customers);
    }

    public function save(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tunai,transfer,qris',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Get or create customer
            $customerId = null;
            $customerName = $request->customer_name ?: 'Umum';
            $customerPhone = $request->customer_phone;

            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customerId = $customer->id;
                    $customerName = $customer->name;
                    $customerPhone = $customer->phone;
                }
            } elseif ($request->save_customer && $request->customer_name) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $request->customer_phone],
                    ['name' => $request->customer_name, 'phone' => $request->customer_phone]
                );
                $customerId = $customer->id;
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['qty'] * $item['price'];
            }

            $discount = 0;
            $discountType = $request->discount_type ?? 'nominal';
            if ($request->discount) {
                if ($discountType === 'percent') {
                    $discount = $subtotal * ($request->discount / 100);
                } else {
                    $discount = (float) $request->discount;
                }
            }

            $setting = InvoiceSetting::getSetting();
            $taxPercent = 0;
            $tax = 0;
            if ($setting->tax_enabled && $request->apply_tax) {
                $taxPercent = $setting->tax_percent;
                $tax = ($subtotal - $discount) * ($taxPercent / 100);
            }

            $total = $subtotal - $discount + $tax;
            $amountPaid = (float) $request->amount_paid;
            $change = $amountPaid - $total;

            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'discount_type' => $discountType,
                'tax' => $tax,
                'tax_percent' => $taxPercent,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'amount_paid' => $amountPaid,
                'change_amount' => max(0, $change),
                'status' => 'lunas',
                'notes' => $request->notes,
                'deadline' => $request->deadline ?: null,
            ]);

            // Create items
            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_service_id' => $item['product_service_id'] ?? null,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'paper_type' => $item['paper_type'] ?? null,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'qty' => $item['qty'],
                    'unit' => $item['unit'] ?? 'pcs',
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                    'custom_notes' => $item['custom_notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan!',
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
