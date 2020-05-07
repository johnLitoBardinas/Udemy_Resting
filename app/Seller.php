<?php

namespace App;

use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;

class Seller extends User
{
	// transformer linking to the model
    public $transformer = SellerTransformer::class;

	/* defining boot method to automatically used the BuyerScope scope */
	protected static function boot()
	{
		/* executed when an intance of this particular model was instantiated */
		parent::boot();
		static::addGlobalScope(new SellerScope);
	}
	
    /* defining a relationship between seller and products 1 seller can have many product */
    public function products()
    {
    	return $this->hasMany(Product::class);
    }
}
