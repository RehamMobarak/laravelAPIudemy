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
        ];
    }
}
