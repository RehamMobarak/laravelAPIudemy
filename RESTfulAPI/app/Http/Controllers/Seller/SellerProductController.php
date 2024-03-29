<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\Transformers\ProductTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:'.ProductTransformer::class)->only(['store','update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;
        return $this->showAll($products);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'quantity' => 'required|integer',
            'image' => 'required|image',
            'description' => 'required|min:1',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['seller_id'] = $seller->id;
        $data['image'] =$request->image->store('');
        $new_product = Product::create($data);

        return $this->showOne($new_product);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id !== $product->seller_id) {
            throw new HttpException(422, 'You must be the owner to update this product');
        }
    }

    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'min:1|integer',
            'image' => 'image',
            'status' => 'in:'. Product::AVAILABLE_PRODUCT. ','. Product::UNAVAILABLE_PRODUCT,
        ];

        $this->validate($request, $rules);
        $this->checkSeller($seller, $product);

        //->only : to ignore null or empty values
        $product->fill($request->only(['name', 'description', 'quantity']));

        if ($request->has('status')) {
            $product->status = $request->status;

            if ($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('product must have one category at least to be available', 409);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('must modify values before updating', 422);
        }
       if ($request->hasFile('image')) {
            Storage::delete($product->image);
            $product->image = $request->image->store('');
        }
        $product->save();



        return $this->showOne($product);

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller,Product $product)
    {
        $this->checkSeller($seller,$product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);

    }
}
