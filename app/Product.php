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

    /**
     * Decide either create, or update or do nothing with parsed data.
     *
     * @param $attributes
     * @param $can_update
     * @return array|void
     */
    public static function manageRecords($attributes, $can_update)
    {
        $product = Product::where('name', $attributes['name'])->first();

        $status = 'created';

        if ($product && $can_update) {
            $product->delete();

            $status = 'updated';
        } elseif ($product && !$can_update) {
            return;
        }

        $product = Product::create($attributes);

        return ['product' => $product, 'status' => $status];
    }
}
