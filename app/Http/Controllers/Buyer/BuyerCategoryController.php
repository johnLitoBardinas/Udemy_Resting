<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer) // Implicit Model Binding 
    {
        $categories = $buyer->transactions()
                        ->with('product.categories')
                        ->get()
                        ->pluck('product.categories')
                        ->collapse() // collapsing collection of collection into single collection
                        ->unique('id') // removal  of duplicated id in the set of collection
                        ->values(); // removal of the 

                         
        return $this->showAll($categories);
    }

}
