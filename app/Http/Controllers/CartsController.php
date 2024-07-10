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
        $productId = Product::where('id', $id)->first();
        if ($productId) {
            $productPrice = $productId->price;
            $colorId = ProductColor::where('color', $request->color)
                ->pluck('id')
                ->first();
            $sizeId = ProductSize::where('size', $request->size)
                ->pluck('id')
                ->first();
            $variant = ProductVarient::where('product_id', $id)->where('product_size_id', $sizeId)->where('product_color_id', $colorId)->get();
            $variantId = $variant->pluck('id')->first();
            $stock = $variant->pluck('stock')->first();

            if ($variantId) {
                if ($stock == 0) {
                    return response()->json([
                        'success' => false,
                        'status' => 200,
                        'message' => 'Product Out Of Stock',
                    ]);
                }
                $stock = $variant->pluck('stock')->first();
                $cartFlag = Carts::where('user_id', $user->id)
                    ->where('product_id', $id)
                    ->where('product_varient_id', $variantId)
                    ->first();

                if (is_null($cartFlag)) {
                    if ($request->quantity < $stock) {
                        $cart = Carts::create([
                            'user_id' => $user->id,
                            'product_id' => $id,
                            'product_varient_id' => $variantId,
                            'quantity' => $request->quantity,
                            'total' => $productPrice * $request->quantity,
                            'color' => $request->color,
                            'size' => $request->size,
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
                        return response()->json([
                            'success' => false,
                            'status' => 200,
                            'message' => 'Product Limit Reached',
                        ]);
                    }
                } else {
                    if ($cartFlag->quantity + $request->quantity <= $stock) {
                        $cartFlag->quantity = $cartFlag->quantity + $request->quantity;
                        $cartFlag->total = $productPrice * $cartFlag->quantity;
                        $cartFlag->save();
                        return response()->json([
                            'success' => true,
                            'status' => 200,
                            'message' => 'Product Updated SuccessFully',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'status' => 200,
                            'message' => 'Product Limit Reached',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 200,
                    'message' => 'No Product Variant Found',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'No Product Found',
            ]);
        }
    }

    // Show Cart Products
    public function showCartProduct(Request $request)
    {
        $user = $request->user();
        $cart = Carts::where('user_id', $user->id)->get();

        if ($cart->first()) {
            foreach ($cart as $element) {
                $varaintFlag = $element->product_varient_id;
                $productVariant = ProductVarient::where('id', $varaintFlag)->get();
                $productId = $element->product_id;
                $product = Product::where('id', $productId)->first();
                $productImg = Product::find($productId)->productImages->pluck('image')->first();
                $element->product_name = $product->name;
                $element->product_prize = $product->price;
                $element->slug = $product->slug;
                $element->stock = $productVariant->pluck('stock')->first();
                $element->stock_status = $productVariant->pluck('stock_status')->first();
                $element->product_img = url("/images/product/$productImg");
            }
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Your Cart History',
                'data' => $cart,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Please Add Item To Cart',
            ]);
        }
    }

    // Update : Add Item Quantity Manage :  $id --> Cart Id
    public function addItem(Request $request, $id)
    {
        $user = $request->user();
        // dd($user);
        $cart = Carts::where('id', $id)
            ->where('user_id', $user->id)
            ->get()
            ->first();
        if ($cart) {
            $productPrice = Product::where('id', $cart->product_id)
                ->pluck('price')
                ->first();
            $varaintFlag = $cart->product_varient_id;
            $productVariant = ProductVarient::where('id', $varaintFlag)->get();
            $stock = $productVariant->pluck('stock')->first();
            if ($cart->quantity < $stock) {
                $totalPrice = $cart->total;
                $cart->quantity = $cart->quantity + 1;
                $cart->Total = $totalPrice + $productPrice;
                $cart->save();
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Updated SuccessFully',
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Limit Reached',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Not Found',
            ]);
        }
    }

    // Update : Remove Item Quantity Manage :  $id --> Cart Id
    public function removeItem(Request $request, $id)
    {
        $user = $request->user();
        $cart = Carts::where('id', $id)
            ->where('user_id', $user->id)
            ->get()
            ->first();
        if ($cart) {
            $productPrice = Product::where('id', $cart->product_id)
                ->pluck('price')
                ->first();
            $totalPrice = $cart->total;
            $cart->quantity = $cart->quantity - 1;
            $cart->Total = $totalPrice - $productPrice;
            $cart->save();
            if ($cart->quantity == 0) {
                $cart->delete();
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Removed SuccessFully.',
                ]);
            }
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Product Updated SuccessFully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'No Product Found. Please Add Product.',
            ]);
        }
    }

    // Delete Product From Cart : $id --> Cart Id
    public function deleteCartProduct(Request $request, $id)
    {
        $user = $request->user();
        $cart = Carts::where('user_id', $user->id)
            ->where('id', $id)
            ->get()
            ->first();

        if (!is_null($cart)) {
            $cart->delete();
            return response()->json(
                [
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Removed From Cart',
                ],
                200,
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'status' => 200,
                    'message' => 'Product Not Found In Cart',
                ],
                200,
            );
        }
    }
}
