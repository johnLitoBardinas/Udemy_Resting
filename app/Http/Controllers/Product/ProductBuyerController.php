<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $buyers = $product->transactions()
                ->with('buyer')  // with buyer relationshit
                ->get()          // getting the collection
                ->pluck('buyer') // plucking just the buyer portion
                ->unique('id')   // selecting only unique id
                ->values();       // removal of any empty properties

        return $this->showAll($buyers);
    }

}
