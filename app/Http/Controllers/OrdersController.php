<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailOrderInvoice;
use App\Models\Carts;
use App\Models\ItemOrder;
use App\Models\Orders;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class OrdersController extends Controller
{
    //

    public function index(){
        $order=Orders::with('user')->paginate(10);
        if($order){
            return response()->json(['success'=>true,'status'=>200,'message'=>'Order Get Successfully','data'=>$order]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Data found']);
        }
    }

    public function checkoutOrder(Request $request)
    {
        // DB::beginTransaction();
        try {
            // Perform database operations
            $user = $request->user();
            $cart = Carts::all();

            foreach ($cart as $item) {
                $item->product_name = $item->products->name;
                $item->product_price = $item->products->price;
                // dump($item->products->name);
                // dump(Product::find($item->product_id));
            }
            $cartItems = Carts::where('user_id', $user->id)->whereNull('deleted_at')->get();
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Please add items to your cart before placing an order.'
                ]);
            } else {
                $cart = $cart->makeHidden(['products', 'color', 'size', 'user_id', 'product_id', 'product_id', 'product_varient_id', 'quantity']);
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Details Get Successfully',
                    'data' => $cart
                ]);
            }
            // if($cart){
            //     $cart = $cart->makeHidden(['products','color','size','user_id','product_id','product_id','product_varient_id','quantity']);
            //     return response()->json([
            //         'success'=>true,
            //         'status'=>200,
            //         'message'=>'Product Details Get Successfully',
            //         'data'=>$cart
            //     ]);
            // }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            // Handle transaction failure
            // DB::rollBack();
        }
    }

    public function orderDetails(Request $request)
    {
        $user = $request->user();
        // dd($user->id);
        // dd($user->email);
        $cart = Carts::all();
        // $totalsum = 0;
        // dd(isset($cart));
        $cartItems = Carts::where('user_id', $user->id)->whereNull('deleted_at')->get();
        // dd($cartItems->isEmpty());

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Please add items to your cart before placing an order.'
            ]);
        }
        foreach ($cart as $item) {
            $totalsum = $item->pluck('total')->sum();
            // dd($totalsum);
        }
        if (is_null($request->shipping_address)) {
            // dd($request->shipping_address);
            $order = Orders::create([
                'user_id' => $user->id,
                'order_no' => uniqid('ORD-'),
                'order_status' => 'completed',
                'payment_method' => $request->payment_method,
                'total_price' => $totalsum,
                'shipping_address' => $request->billing_address
            ]);
        } else {
            dd('hello');
            Orders::create([
                'user_id' => $user->id,
                'order_no' => uniqid('ORD-'),
                'order_status' => 'completed',
                'payment_method' => $request->payment_method,
                'total_price' => $totalsum,
                'shipping_address' => $request->shipping_address
            ]);
        }
        if ($order) {
            foreach ($cart as $item) {
                // dd($item->product_varient_id);
                $itemOrder = ItemOrder::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->total,
                    'product_varients_id' => $item->product_varient_id
                ]);
            }
            Carts::where('user_id', $user->id)->delete();
        }
        $totalItem = ItemOrder::where('order_id', $order->id)->get();
        $productName = array();
        $productPrice = array();
        foreach ($totalItem as $item) {
            $name = Product::where('id', $item->product_id)->pluck('name')->first();
            array_push($productName, $name);
            $price = Product::where('id', $item->product_id)->pluck('price')->first();
            array_push($productPrice, $price);
        }
        //  foreach($totalItem as $ele){
        //     // dd($ele);
        //      $ele->productName = Product::find($ele->product_id)->name;
        //      $ele->productPrice = Product::find($ele->product_id)->price;
        //     //  dump($ele->productName);
        //  }
        // dd($totalItem);
        // dd($itemOrder);
        // foreach($itemOrder as $ele){
        //     dump($ele);
        // }
        // dump(Product::where('id',$itemOrder->product_id)->get());

        SendEmailOrderInvoice::dispatch($order, $totalItem, $user, $productName, $productPrice);
        // Orders::with('order_items')->get();
        return response()->json([
            'status' => true,
            'success' => 201,
            'message' => 'Order Add Successfully',
        ]);
    }

    public function show($id){
        $order=Orders::with(['item_order.product','user'])->find($id)->makeHidden(['user']);
        $order->user_name=$order->user->first_name.' '.$order->user->last_name;
        foreach($order->item_order as $items){
        
            $items->product_name=Product::find($items->product_id)->name;
            
        }
        if($order){
            return response()->json(['success'=>true,'status'=>200,'message'=>'Order Get Successfully','data'=>$order]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'Order Not Found']);
        }
    }
}
