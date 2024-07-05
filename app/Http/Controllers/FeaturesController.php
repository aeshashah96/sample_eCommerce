<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\SubCategories;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    public function search_filter($id){
        $category =  Categories::where('name',$id);
        $subcategory = SubCategories::where('name',$id);
        $color = ProductColor::where('color',$id);
        $size =  ProductSize::where('size',$id);

        //category search
        if($category->count()){
            $category_id = $category->first()->id;
            $data = Product::with('productImages')->where('category_id',$category_id)->get();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>"$id category wise data fetch successfully",
                'data'=>$data
            ]);
        }

        //sub category search
        if($subcategory->count()){
            $sub_category_id = $subcategory->first()->id;
            $data = Product::with('productImages')->where('sub_category_id',$sub_category_id)->get();

            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>"$id sub-category wise data fetch successfully",
                'data'=>$data
            ]);
        }

        //color search
        if($color->count()){
           $data =  $color->with('products');
        dd($color->get());
           $data =  $color->first()->products->with('productImages');

            dd($data);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>"$id sub-category wise data fetch successfully",
                // 'data'=>$data
            ]);
        }
        
    }
}
