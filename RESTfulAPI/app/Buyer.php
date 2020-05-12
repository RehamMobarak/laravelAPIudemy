<?php

namespace App;

use App\Scopes\BuyerScope;

class Buyer extends User
{
    // testing global scope
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BuyerScope);
    }
    
    /* RELATIONSHIPS */

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
