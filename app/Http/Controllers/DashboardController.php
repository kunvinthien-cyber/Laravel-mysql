<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    public function index()
    {
        $revenue = Order::sum('total');

        $orders = Order::count();

        $customers = Customer::count();

        $outOfStock = Product::where('stock', 0)->count();

        // 🟢 បន្ថែម Query ទាញយកទំនិញជិតអស់ពីស្តុក (Stock <= 5)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();

        $recentOrders = Order::latest()
            ->take(5)
            ->get();

        $sales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->take(7)
            ->get();

        // Additional values required by the dashboard view
        $products = Product::count();

        // Build chart data for the last 7 days (ensure 7 values)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $row = $sales->firstWhere('date', $date);
            $chartData[] = $row ? (float) $row->total : 0;
        }

        $completedOrders = Order::where('status', 'completed')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $bestSellingProducts = OrderItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $topCustomers = \App\Models\Order::select(
                'customer_id',
                DB::raw('SUM(total) as total_spent'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->with('customer')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'revenue',
            'orders',
            'customers',
            'outOfStock',
            'lowStockProducts', // 🟢 បានបន្ថែម variable នេះ
            'recentOrders',
            'sales',
            'products',
            'chartData',
            'completedOrders',
            'pendingOrders',
            'cancelledOrders',
            'bestSellingProducts',
            'topCustomers',
        ));
    }
}
