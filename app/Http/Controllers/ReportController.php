<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $query = Transaction::whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('status', 'lunas');

        $totalRevenue = $query->sum('total');
        $totalTransactions = $query->count();
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $transactions = Transaction::with(['items', 'user'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('status', 'lunas')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $paymentBreakdown = Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('status', 'lunas')
            ->groupBy('payment_method')
            ->get();

        $topServices = TransactionItem::select('name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
                  ->where('status', 'lunas');
            })
            ->groupBy('name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'dateFrom', 'dateTo',
            'totalRevenue', 'totalTransactions', 'avgTransaction',
            'transactions', 'paymentBreakdown', 'topServices'
        ));
    }

    public function export(Request $request)
    {
        // CSV export
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $transactions = Transaction::with(['items', 'user'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->where('status', 'lunas')
            ->latest()
            ->get();

        $filename = 'laporan-transaksi-' . $dateFrom . '-' . $dateTo . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No Invoice', 'Tanggal', 'Pelanggan', 'Kasir', 'Subtotal', 'Diskon', 'Total', 'Metode Bayar', 'Status']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->invoice_number,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->customer_name,
                    $t->user->name,
                    $t->subtotal,
                    $t->discount,
                    $t->total,
                    $t->payment_method_label,
                    $t->status_label,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
