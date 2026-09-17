<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function pdf(Request $request)
    {
        $query = Order::with('items.product')->latest();
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->get();
        $totalSales = $orders->sum('total');
        $totalProfit = $orders->sum(fn ($order) => $order->items->sum(fn ($item) =>
            (float) $item->subtotal - ((float) ($item->product?->cost_price ?? 0) * (int) $item->quantity)
        ));

        $pdf = Pdf::loadView('reports.pdf', compact('orders', 'totalSales', 'totalProfit'));

        return $pdf->download('sales-report.pdf');
    }

    public function productsExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['ID', 'Name', 'Barcode', 'Category', 'Selling Price', 'Cost Price', 'Stock']], null, 'A1');

        foreach (Product::with('category')->get() as $index => $product) {
            $sheet->fromArray([[
                $product->id,
                $product->name,
                $product->barcode,
                $product->category?->name,
                $product->price,
                $product->cost_price,
                $product->stock,
            ]], null, 'A' . ($index + 2));
        }

        return $this->downloadSpreadsheet($spreadsheet, 'stock-list.xlsx');
    }

    public function customersExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['ID', 'Name', 'Phone', 'Email', 'Address', 'Points', 'Debt']], null, 'A1');

        foreach (Customer::all() as $index => $customer) {
            $sheet->fromArray([[
                $customer->id,
                $customer->name,
                $customer->phone,
                $customer->email,
                $customer->address,
                $customer->points,
                $customer->debt,
            ]], null, 'A' . ($index + 2));
        }

        return $this->downloadSpreadsheet($spreadsheet, 'customers-phones.xlsx');
    }

    public function debtPdf()
    {
        $customers = Customer::where('debt', '>', 0)->orderByDesc('debt')->get();

        return Pdf::loadView('customers.debt-pdf', compact('customers'))
            ->download('customer-debts.pdf');
    }

    public function productLabelsPdf()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return Pdf::loadView('products.labels-pdf', compact('products'))
            ->download('product-labels.pdf');
    }

    private function downloadSpreadsheet(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}
