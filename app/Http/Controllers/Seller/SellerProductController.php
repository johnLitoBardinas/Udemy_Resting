<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products; // relationshi directly
        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller) // user don't have product
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store(''); // double quote is required
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product) // resolving the Product in the Service Container
    {
        // if seller_id not equal to product_id
        // can't change the status  of a particular product to available if it does'nt have a category
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image'  => 'image',
        ];

        $this->validate($request, $rules);

        // separate logic for checking if the current request_id === to the product_seller_id
        $this->checkSeller($seller, $product);

        // the checkSeller was not encountered any errors
        // use fill to change the value of the existing model instance
        $product->fill($request->only([
            'name',
            'description',
            'quantity'
        ]));

        // checking if the $request has a status property [OC mode to match the value both in available and Unavailable]
        if ($request->has('status')) {
            $product->status = $request->status;
     
            // checking if the product is available && product categories === 0 => error
            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('An active product must have at least one category', 409);
            }
        }

        // checking if the image was change
        if ($request->hasFile('image')) {
            // delete the old image at the file image
            Storage::delete($product->image);

            // assigning the new image proerties of the Product
            $product->image = $request->image->store('');
        }

        // isClean determine if the intance of the Product argument was the same after all the change above
        if ($product->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        // deletion of the product instance
        $product->delete(); // soft delete

        // calling the Storage::delete deletion of the product image
        Storage::delete($product->image);

        return $this->showOne($product);
    }

    /* any private or protected function that are not related to the controller functionalities */

    // line 71 method
    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, "The specified seller is not actual seller of the product", 1);
        }
    }
}
