<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class SellerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $categories = $seller->products() // ACCESS THE SELLER WITH HAS MANY PRODUCTS
                    ->whereHas('categories') // SPECIFYING SELLER -> PRODUCTS -> CATEGORIES
                    ->with('categories')    // GETTING PRODUCT ->CATEGORIES 
                    ->get()                 // GET THE COLLECTION
                    ->pluck('categories')   // TRIM THE COLLECTION TO SELECT ONLY THE CATEGORIES
                    ->collapse()            // COLLAPSE A DIMENSIONAL COLLECTION INTO 1
                    ->unique('id')          // SELLECT ONLY THE UNIQUE ID OF THE CATEGORIES
                    ->values();             // REMOVE ALL THE EMPTY CATEGORIES INTANCE

        return $this->showAll($categories);
    }

}
