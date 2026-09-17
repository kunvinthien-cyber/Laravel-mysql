<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToShop;

class OrderItem extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function invoice(Order $order)
{
    $order->load([
        'customer',
        'items.product'
    ]);

    return view('orders.invoice', compact('order'));
}
}
