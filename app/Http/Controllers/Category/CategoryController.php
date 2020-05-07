<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class CategoryController extends ApiController
{

    /**
     * Register the middleware
     */
    public function __construct()
    {
        parent::__construct();

        // Register the middleware.
        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $categories = Category::all();
       return $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // list the rules here
        $rules  = [
            'name' => 'required',
            'description' => 'required',
        ];

        // perfoem the validation against  the request
        $this->validate($request, $rules);

        // creating using the eloquent create
        $newCategory = Category::create($request->all());

        return $this->showOne($newCategory, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // fill the category with the new category instance from request
        $category->fill($request->only(
            'name',
            'description'
        ));

        /* isDirty == true if the model instance was changed not the same value */
        /* isClean == true if the model instance was the same value */
        if ($category->isClean()) {
            return $this->errorResponse('You need to specify any different value to update', 422);
        }

        // it change
        // isDirty is true regarding if it only change in 1 field
        $category->save();
        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->showOne($category);
    }
}
