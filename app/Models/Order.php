<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToShop;

class Order extends Model
{
    use BelongsToShop;

   protected $fillable = [
     'shop_id',

    'customer_id',

    'cashier_id',

    'total',

    'status',

    'payment_method',

    'receipt_no',

];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
