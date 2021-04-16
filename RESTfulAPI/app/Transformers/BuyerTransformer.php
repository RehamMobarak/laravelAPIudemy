<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
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
    public function transform(Buyer $buyer)
    {
        return [
            'identifier' =>(int)$buyer->id,
            'name' =>(string)$buyer->name,
            'email' =>(string)$buyer->email,
            'isVerified' =>(int)$buyer->verified,
            'creationDate' =>(string)$buyer->created_at,
            'lastChangedDate' =>(string)$buyer->updated_at,
            'deletionDate' =>isset($buyer->deleted_at) ? (String)$buyer->deleted_at : null,

            'links' =>[
                [
                    'rel' => 'self',
                    'href' => route('buyers.show', $buyer->id)
                ],
                [
                    'rel' => 'buyers.categories',
                    'href' => route('buyers.categories.index', $buyer->id)
                ],
                [
                    'rel' => 'buyers.products',
                    'href' => route('buyers.products.index', $buyer->id)
                ],
                [
                    'rel' => 'buyers.sellers',
                    'href' => route('buyers.sellers.index', $buyer->id)
                ],
                [
                    'rel' => 'buyers.transactions',
                    'href' => route('buyers.transactions.index', $buyer->id)
                ],
                [
                    'rel' => 'profile',
                    'href' => route('users.show', $buyer->id)
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
