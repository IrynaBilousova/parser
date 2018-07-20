<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function productProperties()
    {
        return $this->hasMany('App\ProductProperty');
    }

    public static function createRecord($attributes)
    {
        $product = Product::where('name', $attributes['name'])->first();

        if ($product)
        {
            $product->delete();
        }

        $product = Product::create($attributes);

        return $product;
    }
}
