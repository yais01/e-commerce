<?php
namespace App\Services\Feature;

use App\Models\Feature\Cart;
use App\Models\Master\Product;
use App\Repositories\CrudRepositories;
use Illuminate\Support\Str;
class CartService{

    protected $cart;
    public function __construct(Cart $cart)
    {
        $this->cart = new CrudRepositories($cart);
    }

    public function store($data)
    {
        $cek = $this->cart->Query()->where(['user_id' => auth()->user()->id, 'product_id' => $data['cart_product_id']])->first();

if ($cek) {
    $productQty = $cek->Product->qty; // Mengambil qty dari model Product yang berhubungan dengan model Cart
    $newQty = $cek->qty + $data['cart_qty'];

    // Memastikan nilai $newQty tidak melebihi qty produk
    if ($newQty > $productQty) {
        $newQty = $productQty;
    }

    $cek->qty = $newQty;
    $cek->update();
} else {
    $product = Product::find($data['cart_product_id']);

    if ($product) {
        $productQty = $product->qty; // Mengambil qty dari model Product
        $qty = $data['cart_qty'];

        // Memastikan nilai qty tidak melebihi qty produk
        if ($qty > $productQty) {
            $qty = $productQty;
        }

        $this->cart->store([
            'product_id' => $data['cart_product_id'],
            'qty' => $qty,
            'user_id' => auth()->user()->id,
        ]);
    }
}

        
    }

    public function update($data)
    {
        $cek = $this->cart->Query()->where(['user_id' => auth()->user()->id,'product_id' => $data['cart_product_id']])->first();
        if($cek){
            $cek->qty = $cek->qty + $data['cart_qty'];
            $cek->update();
        }else{
            $this->cart->store([
                'product_id' => $data['cart_product_id'],
                'qty'        => $data['cart_qty'],
                'user_id'    => auth()->user()->id,
            ]);
        }
        
    }

    public function getUserCart()
    {
        return $this->cart->Query()->where('user_id',auth()->user()->id)->get();
    }
    
    public function deleteUserCart()
    {
        return $this->cart->Query()->where('user_id',auth()->user()->id)->delete();
    }
    

}