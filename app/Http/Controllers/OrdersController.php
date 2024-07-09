<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Product;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //

    public function index(){
        $order=Orders::paginate(10);
        if($order){
            return response()->json(['success'=>true,'status'=>200,'message'=>'Order Get Successfully','data'=>$order]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Data found']);
        }
    }
    
    public function orders(Request $request,$id){
        $user = $request->user();
        
        $productId = Product::find($id);
        dd($productId);
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
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Order found']);
        }
    }
}
