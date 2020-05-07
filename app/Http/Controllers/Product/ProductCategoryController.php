<?php

namespace App\Http\Controllers\Product;

use App\Category;
use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {            // query builder relationshit
        // interacting with many to many relatiosnhit
        /*
            1:59 PM 01-29-2019
            * attach               - does not check if a given category already related at the product
            * sync                 - add the new category but remove all the previous categories related to a product
            * syncWithoutDetaching - solve both the attach and sync error [ automatically checking if a category exist on a product if it exist then retain it, if not them just add the new category on a product ]
        */
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->ShowAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        /*$product = $product->delete();
        return $this->showOne($product); sala */

        /* 
            2:16 PM 01-29-2019
            -> Removal of a specific category on a listed category of the particular Product
        */

        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse('The specified category is not a category of this Product', 404); // cause cannot find the specified category for the particular product
        }

        // magical detaching the particular catergory on the particular Product
        $product->categories()->detach($category->id);

        return $this->showAll($product->categories);

    }
}
