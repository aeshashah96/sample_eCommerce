<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailOrderInvoice;
use App\Models\Carts;
use App\Models\ImageProduct;
use App\Models\ItemOrder;
use App\Models\Orders;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\UserAddresses;
use Exception;
use Illuminate\Http\Request;
class OrdersController extends Controller
{
    //

    public function index()
    {
        $order = Orders::with('user')->paginate(10);
        if ($order) {
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Order Get Successfully', 'data' => $order]);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'No Data found']);
        }
    }

    // Make a Function When User Proceed to Checkout Then User Cart Details SHow 
    public function checkoutOrder(Request $request)
    {
        try {
            $user = $request->user();
            $cart = Carts::where('user_id', $user->id)->get();

            foreach ($cart as $item) {
                $item->product_name = $item->products->name;
                $item->product_price = $item->products->price;
            }
            $sub_total = $cart->sum('total');
            $cartItems = Carts::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->get();
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Please add items to your cart before placing an order.',
                ]);
            } else {
                $cart = $cart->makeHidden(['products', 'color', 'size', 'user_id', 'product_id', 'product_id', 'product_varient_id']);
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Details Get Successfully',
                    'data' => ['data' => $cart, 'sub_total' => $sub_total],
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Make a Function To Place Order By User 
    public function orderDetails(Request $request)
    {
        $user = $request->user();
        $cart = Carts::where('user_id', $user->id)->get();
        $billing_address = json_encode([
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'zipcode' => $request->zipcode,
        ]);
        if ($request->ship_to_different_address) {

            //shipping address validation

            $validatedData = $request->validate([
                'shipping_address_line_1' => 'required',
                'shipping_address_line_2' => 'required',
                'shipping_city' => 'required|string',
                'shipping_state' => 'required|string',
                'shipping_country' => 'required|string',
                'shipping_zipcode' => 'required|numeric',
            ]);

            $shipping_address = json_encode([
                'address_line_1' => $request->shipping_address_line_1,
                'address_line_2' => $request->shipping_address_line_2,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'country' => $request->shipping_country,
                'zipcode' => $request->shipping_zipcode,
            ]);
        } else {
            // dd('hello');   
            $shipping_address = null;
        }
        UserAddresses::create([
            'user_id' => $user->id,
            'billing_address' => $billing_address,
            'shipping_address' => $shipping_address
        ]);
        $cartItems = Carts::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Please add items to your cart before placing an order.',
            ]);
        }
        $totalsum = $cart->pluck('total')->sum();
        if ($request->ship_to_different_address) {
            $shipping_address = json_encode([
                'address_line_1' => $request->shipping_address_line_1,
                'address_line_2' => $request->shipping_address_line_2,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'country' => $request->shipping_country,
                'zipcode' => $request->shipping_zipcode,
            ]);
            $order = Orders::create([
                'user_id' => $user->id,
                'order_no' => uniqid('ORD-'),
                'order_status' => 'completed',
                'payment_method' => $request->payment_method,
                'total_price' => $totalsum,
                'shipping_address' => $shipping_address,
            ]);
        } else {
            $order = Orders::create([
                'user_id' => $user->id,
                'order_no' => uniqid('ORD-'),
                'order_status' => 'completed',
                'payment_method' => $request->payment_method,
                'total_price' => $totalsum,
                'shipping_address' => $billing_address,
            ]);
        }
        if ($order) {
            foreach ($cart as $item) {
                $cartQuantity = $item->quantity;
                $productVariantDetails = ProductVarient::where('id', $item->product_varient_id)->first();
                // dd($productVariantDetails);
                $productVariantDetails->stock = $productVariantDetails->stock - $cartQuantity;
                $productVariantDetails->save();
                if ($productVariantDetails->stock_status == "out_of_stock") {
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'Order Item Out of Stock'
                    ]);
                }
                if ($productVariantDetails->stock == 0) {
                    $productVariantDetails->stock_status = "out_of_stock";
                    $productVariantDetails->save();
                }
            }
            foreach ($cart as $item) {
                $cartQuantity = $item->quantity;
                $productVariantDetails = ProductVarient::where('id', $item->product_varient_id)->first();
                $productVariantDetails->stock = $productVariantDetails->stock - $cartQuantity;
                $productVariantDetails->save();
                if ($productVariantDetails->stock_status == 'out_of_stock') {
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'Order Item Out of Stock',
                    ]);
                }
                if ($productVariantDetails->stock == 0) {
                    $productVariantDetails->stock_status = 'out_of_stock';
                    $productVariantDetails->save();
                }
            }
            foreach ($cart as $item) {
                $itemOrder = ItemOrder::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->total,
                    'product_varients_id' => $item->product_varient_id,
                ]);
            }
            Carts::where('user_id', $user->id)->delete();
        }
        $totalItem = ItemOrder::where('order_id', $order->id)->get();
        $productName = [];
        $productPrice = [];
        $productVariantName = [];
        foreach ($totalItem as $item) {
            $name = Product::where('id', $item->product_id)
                ->pluck('name')
                ->first();
            array_push($productName, $name);
            $price = Product::where('id', $item->product_id)
                ->pluck('price')
                ->first();
            array_push($productPrice, $price);
            $varientName = ProductVarient::where('id', $item->product_varients_id)
                ->pluck('variant_name')
                ->first();
            array_push($productVariantName, $varientName);
        }
        $address = json_decode($order->shipping_address, true);
        // SendEmailOrderInvoice::dispatch($order, $totalItem, $user, $productName, $productPrice, $productVariantName, $address);
        return response()->json([
            'status' => true,
            'success' => 201,
            'message' => 'Order Add Successfully',
        ]);
    }

    public function show($id)
    {
        $order = Orders::with(['order_items.product', 'user'])->find($id)->makeHidden(['user', 'product']);
        $order->user_name = $order->user->first_name . ' ' . $order->user->last_name;
        foreach ($order->order_items as $items) {
            $items->product_name = Product::withTrashed()->find($items->product_id)->name;
        }
        if ($order) {
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Order Get Successfully', 'data' => $order]);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Order Not Found']);
        }
    }

    public function orders(Request $request)
    {
        $user = $request->user();
        $orderDetails = Orders::where('user_id', $user->id)->get();
        if ($orderDetails) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Order Details Get Successfully',
                'data' => $orderDetails
            ]);
        } else {
            return response()->json([
                'success' => true,
                'status' => 404,
                'message' => 'Order Details Not Found',

            ]);
        }
    }

    public function orderHistory(Request $request, $id)
    {
        try{

            $itemOrder = ItemOrder::where('order_id', $id)->get();
            foreach ($itemOrder as $item) {
                $product = Product::select('id','name','price','slug')->withTrashed()->where('id', $item->product_id)->first();
                $productImage = ImageProduct::where('product_id', $product->id)->pluck('image')->first();
                $item->product_detail = $product;
                $item->product_image = url("/images/product/".$productImage);
            }
            if ($itemOrder) {
                $itemOrder = $itemOrder->makeHidden(['product_id','created_at','updated_at','product_varients_id','deleted_at','isActive']);
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Order Items Details Get Successfully',
                    'data' => $itemOrder
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Order Items are Not Found'
                ]);
            }
        }
        catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status' =>$e->getCode(),
                'message'=>$e->getMessage()
            ]);
        }
    }
}
