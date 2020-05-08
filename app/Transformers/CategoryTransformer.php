<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier'    => (int)$category->id,
            'title'         => (string)$category->name,
            'details'   => (string)$category->description,
            'creationDate'  => (string)$category->created_at,
            'lastChange'    => (string)$category->updated_at,
            'deletedDate'   => isset($category->deleted_at) ? (string)$category->deleted_at : null,
            'links'         => [
                [
                    'rel' => 'self',// self link
                    'href'=> route('categories.show', $category->id), // url then the id of category
                ],
                [
                    'rel' => 'category.buyers',// self link
                    'href'=> route('categories.buyers.index', $category->id), // url then the id of category
                ],
                [
                    'rel' => 'category.products',// self link
                    'href'=> route('categories.products.index', $category->id), // url then the id of category
                ],
                [
                    'rel' => 'category.sellers',// self link
                    'href'=> route('categories.sellers.index', $category->id), // url then the id of category
                ],
                [
                    'rel' => 'category.transactions',// self link
                    'href'=> route('categories.transactions.index', $category->id), // url then the id of category
                ],

            ]
        ];
    }

    /*
        3:53 PM 01-31-2019
        Mapping the original value from fractal name into the model property name
    */
    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier'    => 'id',
            'title'         => 'name',
            'details'   => 'description',
            'creationDate'  => 'created_at',
            'lastChange'    => 'updated_at',
            'deletedDate'   => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /*
        23:37 05-08-2020
        Mapping fractal name(request name) -> Model property name.
    */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id'          => 'identifier',
            'name'        => 'title',
            'description' => 'details',
            'created_at'  => 'creationDate',
            'updated_at'  => 'lastChange',
            'deleted_at'  => 'deletedDate',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
