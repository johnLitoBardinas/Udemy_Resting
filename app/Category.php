<?php

namespace App;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	// adding the softDeletes traits
	use SoftDeletes;
	protected $dates = ['deleted_at'];

     // transformer linking to the model
    public $transformer = CategoryTransformer::class;

    // define some attributes that can be filled
    protected $fillable = ['name', 'description'];

    // removal of the pivot section on the return
    protected $hidden = ['pivot'];

    // 1 category Belongs to many Product
    public function products()
    {
    	return $this->belongsToMany(Product::class);
    }
}
