<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\InvoiceSetting;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items', 'customer'])->latest();

        if ($request->search) {
            $q = $request->search;
            $query->where(function($qr) use ($q) {
                $qr->where('invoice_number', 'like', "%$q%")
                   ->orWhere('customer_name', 'like', "%$q%")
                   ->orWhere('customer_phone', 'like', "%$q%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $transaction = Transaction::with(['items.productService', 'user', 'customer'])->findOrFail($id);
        $setting = InvoiceSetting::getSetting();
        return view('transactions.show', compact('transaction', 'setting'));
    }

    public function printNota($id)
    {
        $transaction = Transaction::with(['items.productService', 'user', 'customer'])->findOrFail($id);
        $setting = InvoiceSetting::getSetting();
        return view('transactions.nota', compact('transaction', 'setting'));
    }

    public function printThermal($id)
    {
        $transaction = Transaction::with(['items.productService', 'user', 'customer'])->findOrFail($id);
        $setting = InvoiceSetting::getSetting();
        return view('transactions.thermal', compact('transaction', 'setting'));
    }

    public function cancel($id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Hanya admin yang dapat membatalkan transaksi.');
        }

        $transaction->update(['status' => 'batal']);
        return back()->with('success', 'Transaksi berhasil dibatalkan.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Hanya admin yang dapat menghapus transaksi.');
        }

        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
