<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\ProductService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');

        // Today stats
        $todayTransactions = Transaction::whereDate('created_at', $today)
            ->where('status', 'lunas')->count();
        $todayRevenue = Transaction::whereDate('created_at', $today)
            ->where('status', 'lunas')->sum('total');

        // This month stats
        $monthRevenue = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'lunas')->sum('total');

        $lastMonthRevenue = Transaction::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->where('status', 'lunas')->sum('total');

        $monthTransactions = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'lunas')->count();

        // Total customers
        $totalCustomers = Customer::count();

        // Top services this month
        $topServices = TransactionItem::select('name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaction', function($q) {
                $q->whereYear('created_at', now()->year)
                  ->whereMonth('created_at', now()->month)
                  ->where('status', 'lunas');
            })
            ->groupBy('name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Chart data - last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Transaction::whereDate('created_at', $date->toDateString())
                ->where('status', 'lunas')->sum('total');
            $chartData[] = [
                'date' => $date->format('d/m'),
                'revenue' => (float) $revenue,
            ];
        }

        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        // Payment method breakdown this month
        $paymentBreakdown = Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'lunas')
            ->groupBy('payment_method')
            ->get();

        return view('dashboard.index', compact(
            'todayTransactions',
            'todayRevenue',
            'monthRevenue',
            'lastMonthRevenue',
            'monthTransactions',
            'totalCustomers',
            'topServices',
            'chartData',
            'recentTransactions',
            'paymentBreakdown'
        ));
    }
}
