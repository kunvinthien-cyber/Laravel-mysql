<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ReportController extends Controller
{
   public function index(Request $request)
{
    $query = Order::with(['customer', 'items.product']);

    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    // Total Sales
    $totalSales = (clone $query)->sum('total');

    $staffSales = (clone $query)
        ->reorder()
        ->with('cashier')
        ->selectRaw('cashier_id, COUNT(*) as order_count, SUM(total) as total_sales')
        ->whereNotNull('cashier_id')
        ->groupBy('cashier_id')
        ->orderByDesc('total_sales')
        ->get();

    // Orders List
    $orders = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('reports.index', compact(
        'orders',
        'totalSales',
        'staffSales'
    ));
}
    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['Order ID', 'Date', 'Staff', 'Customer', 'Product', 'Qty', 'Revenue', 'Cost', 'Profit', 'Status']], null, 'A1');

        $row = 2;
        foreach (Order::with(['customer', 'cashier', 'items.product'])->latest()->get() as $order) {
            foreach ($order->items as $item) {
                $revenue = (float) $item->subtotal;
                $cost = (float) ($item->product?->cost_price ?? 0) * (int) $item->quantity;
                $sheet->fromArray([[
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->cashier?->name,
                    $order->customer?->name,
                    $item->product?->name,
                    $item->quantity,
                    $revenue,
                    $cost,
                    $revenue - $cost,
                    ucfirst($order->status),
                ]], null, 'A' . $row++);
            }
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Sales_Report.xlsx');
    }
}
