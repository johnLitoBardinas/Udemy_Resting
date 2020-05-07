<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer) // User because it may be a newly purchase 
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];

        // validate the qunatity
        $this->validate($request, $rules);

        /* making sure that the buyer not equal into the the product seller_id */
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('The buyer must be different from the seller', 409); // conflict
        }

        // checking if the user/ buyer is verified
        if (!$buyer->isVerified()) {
            return $this->errorResponse('The buyer must be a verified user', 409);
        }

        // checking if the product seller was verified
        if (!$product->seller->isVerified()) {
            return $this->errorResponse('The seller must be a verified user', 409);
        }

        // checking if the product is still available
        if (!$product->isAvailable()) {
            return $this->errorResponse('The product is not available', 409);
        }

        // checking if the transaction qnty is > to the available product qnty
        if ( $product->quantity < $request->quantity ) {
            return $this->errorResponse('The product does not have enough units for this transaction', 409);
        }

        /* the quantity must be sequencially use the [ database transactions ] */
        /* return the DB::transaction*/
        return DB::transaction(function() use ($request, $product, $buyer)
        {
            /* decreasing the number of a quantity of a particular product */
            $product->quantity -= $request->quantity;
            $product->save();

            /* creation of the transactions table */
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            /* returning the transaction */
            return $this->showOne($transaction, 201); //creation of a new resource from the db
        });

    }

}
