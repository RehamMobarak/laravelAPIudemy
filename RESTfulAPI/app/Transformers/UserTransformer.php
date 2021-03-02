<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $user)
    {
        return [
            'identifier' =>(int)$user->id,
            'name' =>(string)$user->name,
            'email' =>(string)$user->email,
            'isVerified' =>(int)$user->verified,
            'isAdmin' =>($user->admin === 'true'),
            'creationDate' =>(string)$user->created_at,
            'lastChangedDate' =>(string)$user->updated_at,
            'deletionDate' =>isset($user->deleted_at) ? (String)$user->deleted_at : null,
        ];
    }
}
