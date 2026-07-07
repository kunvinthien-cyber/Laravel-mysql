<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderController extends Controller
{

public function index(Request $request)
{
    $query = Order::with(['items.product', 'customer']);

    // Search by Order ID
    if ($request->filled('search')) {
        $query->where('id', $request->search);
    }

    // Filter by Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $orders = $query->latest()
                    ->paginate(10)
                    ->withQueryString();

    return view('orders.index', compact('orders'));
}

    public function create()
{
    $products = Product::all();
    $customers = Customer::all();

    return view('orders.create', compact('products', 'customers'));
}

public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'customer_id' => 'required|exists:customers,id',
        'status' => 'required',
    ]);

    $product = Product::findOrFail($request->product_id);

    // Order::create([
    //     'product_id' => $request->product_id,
    //     'customer_id' => $request->customer_id,
    //     'total' => $product->price,
    //     'status' => $request->status,
    // ]);

    return redirect()->route('orders.index')
        ->with('success', 'Order created successfully.');
}

public function edit(Order $order)
{
    $products = Product::all();
    $customers = Customer::all();

    return view('orders.edit', compact(
        'order',
        'products',
        'customers'
    ));
}

public function update(Request $request, Order $order)
{
    $request->validate([
        // 'product_id' => 'required|exists:products,id',
        'customer_id' => 'required|exists:customers,id',
        'status' => 'required',
    ]);

    $product = Product::findOrFail($request->product_id);

    $order->update([
        // 'product_id' => $request->product_id,
        'customer_id' => $request->customer_id,
        'total' => $product->price,
        'status' => $request->status,
    ]);

    return redirect()->route('orders.index')
        ->with('success', 'Order updated successfully.');
}

public function destroy(Order $order)
{
    $order->delete();

    return redirect()->route('orders.index')
        ->with('success', 'Order deleted successfully.');
}

public function invoice(Order $order)
{
    $order->load(['items.product', 'customer']);

    return view('orders.invoice', compact('order'));
}
public function exportExcel()
{
    $orders = \App\Models\Order::with(['customer', 'items.product'])->get();

    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

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
        $sheet->setCellValue('B'.$row, $order->customer?->name);
        $sheet->setCellValue('C'.$row, $products);
        $sheet->setCellValue('D'.$row, $order->total);
        $sheet->setCellValue('E'.$row, ucfirst($order->status));
        $sheet->setCellValue(
            'F'.$row,
            $order->created_at->format('d M Y')
        );

        $row++;
    }

    foreach (range('A', 'F') as $column) {
        $sheet->getColumnDimension($column)
              ->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);

    $filename = 'orders.xlsx';

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename);
}
}
