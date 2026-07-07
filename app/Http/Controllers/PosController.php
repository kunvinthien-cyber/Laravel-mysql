<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->get();

        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact(
            'products',
            'customers'
        ));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            // ១. កែសម្រួលទៅជា nullable ដើម្បីអនុញ្ញាតឱ្យលក់ជា ភ្ញៀវទូទៅ (Guest Checkout)
            'customer_id' => 'nullable|exists:customers,id',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;

            $products = Product::whereIn('id', collect($request->cart)->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($request->cart as $item) {
                $product = $products[$item['id']];

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Not enough stock for {$product->name}.");
                }

                $subtotal += $product->price * $item['qty'];
            }

            // ២. គណនាពន្ធឌីណាមិកតាមការកំណត់របស់ហាង (Dynamic Tax Calculation)
            $taxRate = (float) config('settings.tax_rate', 0); // ទាញយកអត្រាពន្ធពី Database
            $taxAmount = $subtotal * ($taxRate / 100);       // គណនាទឹកប្រាក់ពន្ធ
            $total = $subtotal + $taxAmount;                  // តម្លៃសរុបចុងក្រោយរួមបញ្ចូលទាំងពន្ធ

            $order = Order::create([
                'customer_id' => $request->customer_id, // អាចជា ID អតិថិជន ឬ null (ភ្ញៀវទូទៅ)
                'total' => $total,
                'status' => 'completed',
                // បើសិនជាតារាង orders របស់អ្នកមាន column subtotal និង tax អ្នកអាចបញ្ជូនតម្លៃទាំងនោះបាន៖
                // 'subtotal' => $subtotal,
                // 'tax' => $taxAmount,
            ]);

            foreach ($request->cart as $item) {
                $product = $products[$item['id']];
                $itemSubtotal = $product->price * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $itemSubtotal,
                ]);

                $product->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully.',
                'redirect' => route('orders.invoice', $order),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
