<?php

namespace App\Models\Concerns;

use App\Models\Shop;

trait BelongsToShop
{
    protected static function bootBelongsToShop(): void
    {
        static::addGlobalScope('shop', function ($builder) {
            if (auth()->check() && !auth()->user()->isAdmin() && auth()->user()->shop_id) {
                $builder->where($builder->getModel()->getTable() . '.shop_id', auth()->user()->shop_id);
            }
        });

        static::creating(function ($model) {
            if (!$model->shop_id && auth()->check() && auth()->user()->shop_id) {
                $model->shop_id = auth()->user()->shop_id;
            }
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
