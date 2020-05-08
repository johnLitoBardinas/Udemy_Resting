<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'identifier'    => (int)$product->id,
            'title'         => (string)$product->name,
            'details'       => (string)$product->description,
            'stock'         => (int)$product->quantity,
            'situation'     => (string)$product->status,
            'picture'       => url("img/{$product->image}"), // for the image relative path
            'seller'        => (int)$product->seller_id,
            'creationDate'  => (string)$product->created_at,
            'lastChange'    => (string)$product->updated_at,
            'deletedDate'   => isset($product->deleted_at) ? (string)$product->deleted_at : null,

            'links'         => [
                [
                    'rel' => 'self',// self link
                    'href'=> route('products.show', $product->id), // url then the id of product
                ],
                [
                    'rel' => 'product.buyers',// self link
                    'href'=> route('products.buyers.index', $product->id), // url then the id of product
                ],
                [
                    'rel' => 'product.transactions',// self link
                    'href'=> route('products.transactions.index', $product->id), // url then the id of product
                ],
                [
                    'rel' => 'seller',// self link
                    'href'=> route('sellers.show', $product->seller_id), // url then the id of product
                ],

            ]
        ];
    }

    /*
        3:53 PM 01-31-2019
        Mapping the original value from fractal name into the model property name
    */
    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier'    => 'id',
            'title'         => 'name',
            'details'       => 'description',
            'stock'         => 'quantity',
            'situation'     => 'status',
            'picture'       => 'image',
            'seller'        => 'seller_id',
            'creationDate'  => 'created_at',
            'lastChange'    => 'updated_at',
            'deletedDate'   => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /*
        23:32 05-08-2020
        Mapping fractal name(request name) -> Model property name.
    */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id'          => 'identifier',
            'name'        => 'title',
            'description' => 'details',
            'quantity'    => 'stock',
            'status'      => 'situation',
            'image'       => 'picture',
            'seller_id'   => 'seller',
            'created_at'  => 'creationDate',
            'updated_at'  => 'lastChange',
            'deleted_at'  => 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
