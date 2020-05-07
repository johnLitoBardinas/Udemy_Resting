<?php

namespace App;

use App\Scopes\BuyerScope;
use App\Transaction;
use App\Transformers\BuyerTransformer;

class Buyer extends User
{
	 // transformer linking to the model
    public $transformer = BuyerTransformer::class;

	/* defining boot method to automatically used the BuyerScope scope */
	protected static function boot()
	{
		/* executed when an intance of this particular model was instantiated */
		parent::boot();
		static::addGlobalScope(new BuyerScope);
	}

	/* implementing buyer has many transaction relationship */
	/* 1:M relationship */
    public function transactions()
    {
    	return $this->hasMany(Transaction::class);
    }

    /* implementing the global scopes query on the model */
    # find it in Scopes/BuyerScope.php
}
