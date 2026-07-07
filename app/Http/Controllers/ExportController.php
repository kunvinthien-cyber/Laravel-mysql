<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function pdf()
    {
        $orders = Order::latest()->get();

        $pdf = Pdf::loadView('reports.pdf', compact('orders'));

        return $pdf->download('sales-report.pdf');
    }
}
