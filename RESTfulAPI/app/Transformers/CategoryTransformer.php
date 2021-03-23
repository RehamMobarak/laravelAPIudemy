<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier' =>(int)$category->id,
            'title' =>(string)$category->name,
            'details' =>(string)$category->description,
            'creationDate' =>(string)$category->created_at,
            'lastChangedDate' =>(string)$category->updated_at,
            'deletionDate' =>isset($category->deleted_at) ? (String)$category->deleted_at : null,

            'links' =>[
                [
                    'rel' => 'self',
                    'href' => route('categories.show', $category->id)
                ],
                [
                    'rel' => 'categories.buyers',
                    'href' => route('categories.buyers.index', $category->id)
                ],
                [
                    'rel' => 'categories.sellers',
                    'href' => route('categories.sellers.index', $category->id)
                ],
                [
                    'rel' => 'categories.products',
                    'href' => route('categories.products.index', $category->id)
                ],
                [
                    'rel' => 'categories.transactions',
                    'href' => route('categories.transactions.index', $category->id)
                ],
            ]
        ];
    }

    public static function originalValues($index){
        $attributes = [
            'identifier' =>'id',
            'title' =>'name',
            'details' =>'description',
            'creationDate' =>'created_at',
            'lastChangedDate' =>'updated_at',
            'deletionDate' => 'deleted_at' ,
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
