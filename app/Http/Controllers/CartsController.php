<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVarient;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    public function addProductCart(Request $request, $id)
    {
        $user = $request->user();
        $productId = Product::find($id);
        if ($productId) {
            $productPrice = $productId->pluck('price')->first();
            $colorId = ProductColor::where('color', $request->color)
                ->pluck('id')
                ->first();
            $sizeId = ProductSize::where('size', $request->size)
                ->pluck('id')
                ->first();
            $variant = ProductVarient::where('product_id', $id)->where('product_size_id', $sizeId)->where('product_color_id', $colorId)->get();
            $variantId = $variant->pluck('id')->first();
            if ($variantId) {
                $cartFlag = Carts::where('user_id', $user->id)
                    ->where('product_id', $id)
                    ->where('product_varient_id', $variantId)
                    ->first();
                if (is_null($cartFlag)) {
                    $cart = Carts::create([
                        'user_id' => $user->id,
                        'product_id' => $id,
                        'product_varient_id' => $variantId,
                        'quantity' => $request->quantity,
                        'total' => $productId->pluck('price')->first() * $request->quantity,
                        'color'=>$request->color,
                        'size'=>$request->size,
                    ]);
                    if ($cart) {
                        return response()->json([
                            'success' => true,
                            'status' => 200,
                            'message' => 'Product Added To Cart SuccessFully',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'status' => 500,
                            'message' => 'Internal Server Error',
                        ]);
                    }
                } else {
                    $cartFlag->quantity = $cartFlag->quantity + $request->quantity;
                    $cartFlag->total = $productPrice * $cartFlag->quantity;
                    $cartFlag->save();
                    return response()->json([
                        'success' => true,
                        'status' => 200,
                        'message' => 'Product Updated SuccessFully',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 200,
                    'message' => 'No Product Variant Found',
                ]);
            }
        }
        else{
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'No Product Found',
            ]);
        }
    }

    public function showCartProduct(Request $request){
        $user = $request->user();
        $cart = Carts::where('user_id',$user->id)->with([
            'products' => function ($query) {
                $query->select('id', 'name', 'price');
            },
        ])->get();
        if($cart->first()){
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Your Cart History',
                'product'=>$cart,
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Please Add Item To Cart',
            ]); 
        }
    }
}
