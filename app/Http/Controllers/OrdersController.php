<?php

namespace App\Http\Controllers;

use App\Models\Orders;
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
}
