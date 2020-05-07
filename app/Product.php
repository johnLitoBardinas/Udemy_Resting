<?php

namespace App;

use App\Category;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // adding the softDeletes traits
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    // transformer linking to the model
    public $transformer = ProductTransformer::class;

	// define constant for status for the sake of abstraction
	const AVAILABLE_PRODUCT   = 'available';
	const UNAVAILABLE_PRODUCT = 'unavailable';

    // define some attributes that can be filled
    protected $fillable = [
    	'name', 
    	'description', 
    	'quantity', 
    	'status', 
    	'image', 
    	'seller_id'
    ];

    // removal of the pivot section on the return
    protected $hidden = ['pivot'];

    /* checking if the current model intance is available */
    public function isAvailable()
    {
    	return $this->status === Product::AVAILABLE_PRODUCT;
    }

    /* defining the Product and Seller model relationship */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /* defining the relationship of Product and Transaction model */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /* defining the product and categories relationship */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
