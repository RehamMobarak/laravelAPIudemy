<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
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
    public function transform(Product $product)
    {
        return [
            'identifier' =>(int)$product->id,
            'title' =>(string)$product->name,
            'details' =>(string)$product->description,
            'stock' =>(int)$product->quantity,
            'situation' =>(string)$product->status,
            'picture' =>url("img/{$product->image}"),
            'seller' =>(int)$product->seller_id,
            'product' =>(int)$product->product_id,
            'creationDate' =>$product->created_at,
            'lastChangedDate' =>$product->updated_at,
            'deletionDate' =>isset($product->deleted_at) ? (String)$product->deleted_at : null,
        ];
    }
}
