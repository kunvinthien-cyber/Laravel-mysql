<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToShop;

class Category extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'description',
        'status',
    ];
public function products()
{
    return $this->hasMany(Product::class);
}
}
