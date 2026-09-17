<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToShop;

class Customer extends Model
{
    use BelongsToShop;

   protected $fillable = [
     'shop_id',
    'name',
    'email',
    'phone',
    'address',
    'points',
    'debt',
];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
