<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    const AVAILABLE_PRODUCT ='available';
    const UNAVAILABLE_PRODUCT ='unavailable';

    protected $fillable = ['name','description','quantity', 'status','image','seller_id'];
    use SoftDeletes;
    protected $dates=['deleted_at'];
    protected $hidden = ['pivot'];

    public function isAvailable()
    {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    /* RELATIONSHIPS */
    
    public function categories()
    {
       return $this->belongsToMany(Category::class);
    }

    public function seller()
    {
       return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
