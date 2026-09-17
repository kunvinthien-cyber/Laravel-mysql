<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
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
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,aba,aceleda,machine',
            'receipt_no' => 'nullable|string|max:100',
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

            $taxRate = (float) (Setting::query()
                ->where('key', 'tax_rate')
                ->value('value') ?? 0);
            $taxAmount = $subtotal * ($taxRate / 100);
            $total = $subtotal + $taxAmount;

            $customer = $request->customer_id ? Customer::find($request->customer_id) : null;

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'cashier_id' => $request->user()->id,
                'total' => $total,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'receipt_no' => $request->receipt_no,
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

            if ($customer) {
                $customer->increment('points', (int) round($total));
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
