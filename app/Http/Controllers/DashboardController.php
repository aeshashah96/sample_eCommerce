<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function getDashboardDetails(){
        $user = User::count();
        $product = Product::count();
        $order = Orders::count();

        $rating = ProductReview::all();
        $avgRating= $rating->avg('rating');
        $totalRating=$rating->count();

        return response()->json(['success'=>true,'status'=>200,'message'=>'Data Get Successfully','data'=>['user'=>$user,'product'=>$product,'order'=>$order,'avg_rating'=>$avgRating,'total_rating'=>$totalRating]]);
        
    }
}
