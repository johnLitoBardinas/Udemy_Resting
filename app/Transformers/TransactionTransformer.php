<?php

namespace App\Transformers;

use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'identifier'    => (int)$transaction->id,
            'quantity'      => (int)$transaction->quantity,
            'buyer'         => (int)$transaction->buyer_id,
            'product'       => (int)$transaction->product_id,
            'creationDate'  => (string)$transaction->created_at,
            'lastChange'    => (string)$transaction->updated_at,
            'deletedDate'   => isset($transaction->deleted_at) ? (string)$transaction->deleted_at : null,
        ];
    }

    /*
        3:53 PM 01-31-2019
        Mapping the original value from fractal name into the model property name
    */
    public static function originalAttribute($index)
    {
        $attributes = 
            'identifier'    => 'id',
            'quantity'      => 'quantity',
            'buyer'         => 'buyer_id',
            'product'       => 'product_id',
            'creationDate'  => 'created_at',
            'lastChange'    => 'updated_at',
            'deletedDate'   => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
