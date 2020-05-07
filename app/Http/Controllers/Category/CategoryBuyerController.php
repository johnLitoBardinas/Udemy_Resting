<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        $buyers = $category->products() // using the query builder not the relationship in the category Model
                ->whereHas('transactions') // checking if a particular product exist in the transactions table regardless if how many intance was that and then return it 
                ->with('transactions.buyer') // converting the product.buyer_id into the intance of the buyer asscociated in each transaction instance
                ->get() // initial get
                ->pluck('transactions') // plucking all the result of the collection the just return the transaction part of every instance
                ->collapse() // with collections of collections apply a collapse  so a master collection will be generated
                ->pluck('buyer') // pluc again just the buyer portion of that collection
                ->unique('id') // get the unique intance of buyer
                ->values(); // remove all the empty values 

        return $this->showAll($buyers);
    }

}
