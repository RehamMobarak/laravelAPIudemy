<?php

namespace App\Transformers;

use App\Seller;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
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
    public function transform(Seller $seller)
    {
        return [
            'identifier' =>(int)$seller->id,
            'name' =>(string)$seller->name,
            'email' =>(string)$seller->email,
            'isVerified' =>(int)$seller->verified,
            'creationDate' =>(string)$seller->created_at,
            'lastChangedDate' =>(string)$seller->updated_at,
            'deletionDate' =>isset($seller->deleted_at) ? (String)$seller->deleted_at : null,

            'links' =>[
                [
                    'rel' => 'self',
                    'href' => route('sellers.show', $seller->id)
                ],
                [
                    'rel' => 'sellers.categories',
                    'href' => route('sellers.categories.index', $seller->id)
                ],
                [
                    'rel' => 'sellers.products',
                    'href' => route('sellers.products.index', $seller->id)
                ],
                [
                    'rel' => 'sellers.buyers',
                    'href' => route('sellers.buyers.index', $seller->id)
                ],
                [
                    'rel' => 'sellers.transactions',
                    'href' => route('sellers.transactions.index', $seller->id)
                ],
                [
                    'rel' => 'profile',
                    'href' => route('users.show', $seller->id)
                ],
            ]
        ];
    }

    public static function originalValues($index){
        $attributes = [
            'identifier' =>'id',
            'name' =>'name',
            'email' =>'email',
            'isVerified' =>'verified',
            'creationDate' =>'created_at',
            'lastChangedDate' =>'updated_at',
            'deletionDate' => 'deleted_at' ,
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }


    public static function transformedValues($index){
        $attributes = [
            'id' =>'identifier',
            'name' =>'name',
            'email' =>'email',
            'verified' =>'isVerified',
            'created_at' =>'creationDate',
            'updated_at' =>'lastChangedDate',
            'deleted_at' => 'deletionDate' ,
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
