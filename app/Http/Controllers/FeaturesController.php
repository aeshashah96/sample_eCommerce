<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\SubCategories;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    public function search_filter($id){
        $category =  Categories::where('name',$id);
        $subcategory = SubCategories::where('name',$id);
        $color = ProductColor::where('color',$id);
        if($category->count()){
            $data = $category->with('products')->get();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>"$id category wise data fetch successfully",
                'data'=>$data
            ]);
        }
        if($subcategory->count()){
            // $data = $subcategory->with('products')->get();
           $data = $subcategory->first()->products;
            foreach($data as $e){
                $id = $e->id;
                $img = ImageProduct::where('product_id',$id)->get()->pluck('image');
                $e->img = $img;                
            }
            

            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>"$id sub-category wise data fetch successfully",
                'data'=>$data
            ]);
        }
        
    }
}
