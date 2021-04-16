<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\Transformers\TransactionTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:'.TransactionTransformer::class)->only(['store']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];
        $this->validate($request, $rules);

        if ($product->seller_id == $buyer->id) {
            return $this->errorResponse('buyer must be different from the seller', 409);
        };

        if (!$product->isAvailable()) {
            return $this->errorResponse('product is not available', 409);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse('buyer is not verified', 409);
        }

        if (!$product->seller->isVerified()) {
            return $this->errorResponse('seller is not verified', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('product quantity is less than the request', 409);
        }

        return DB::transaction(function () use ($request, $buyer, $product) {
            $product->quantity -= $request->quantity;
            $product->save();

            $newTransaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id
            ]);

            return $this->showOne($newTransaction);
        });
    }
}
