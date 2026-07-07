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

    // Orders List
    $orders = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('reports.index', compact(
        'orders',
        'totalSales'
    ));
}
    public function exportExcel()
{
    $orders = Order::with(['customer', 'items.product'])->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'Order ID');
    $sheet->setCellValue('B1', 'Customer');
    $sheet->setCellValue('C1', 'Products');
    $sheet->setCellValue('D1', 'Total');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Date');

    $row = 2;

    foreach ($orders as $order) {

        $products = $order->items
            ->pluck('product.name')
            ->implode(', ');

        $sheet->setCellValue('A'.$row, $order->id);
        $sheet->setCellValue('B'.$row, optional($order->customer)->name);
        $sheet->setCellValue('C'.$row, $products);
        $sheet->setCellValue('D'.$row, $order->total);
        $sheet->setCellValue('E'.$row, ucfirst($order->status));
        $sheet->setCellValue(
            'F'.$row,
            $order->created_at->format('d-m-Y')
        );

        $row++;
    }

    foreach (range('A', 'F') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, 'Sales_Report.xlsx');
}
}
