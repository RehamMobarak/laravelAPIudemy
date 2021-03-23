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
            'creationDate' =>(string)$product->created_at,
            'lastChangedDate' =>(string)$product->updated_at,
            'deletionDate' =>isset($product->deleted_at) ? (String)$product->deleted_at : null,

            'links' =>[
                [
                    'rel' => 'self',
                    'href' => route('products.show', $product->id)
                ],
                [
                    'rel' => 'products.buyers',
                    'href' => route('products.buyers.index', $product->id)
                ],
                [
                    'rel' => 'seller',
                    'href' => route('sellers.show', $product->seller_id)
                ],
                [
                    'rel' => 'products.categories',
                    'href' => route('products.categories.index', $product->id)
                ],
                [
                    'rel' => 'products.transactions',
                    'href' => route('products.transactions.index', $product->id)
                ],
            ]
        ];
    }

    public static function originalValues($index){
        $attributes = [
            'identifier' =>'id',
            'title' =>'name',
            'details' =>'description',
            'stock' =>'quantity',
            'situation' =>'status',
            'picture' =>'image',
            'seller' =>'seller_id',
            'product' =>'product_id',
            'creationDate' =>'created_at',
            'lastChangedDate' =>'updated_at',
            'deletionDate' => 'deleted_at' ,
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
