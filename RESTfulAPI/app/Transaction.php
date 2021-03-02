<?php

namespace App;

use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    protected $fillable = ['quantity', 'product_id', 'buyer_id'];
    use SoftDeletes;
    protected $dates=['deleted_at'];
    public $transformer = TransactionTransformer::class;


    /* RELATIONSHIPS */

    public function buyer(){
        return $this -> belongsTo(Buyer::class);
    }

    public function product(){
        return $this -> belongsTo(Product::class);
    }

}
