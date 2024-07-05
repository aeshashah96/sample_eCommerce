<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\SubCategories;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class FeaturesController extends Controller
{
    public function search_data($id){
        $data = [];
        //category search
        $category =  Categories::where('name',$id);
        if($category->count()){
            $data = $category->paginate(10);
            $data->load('products.productImages');
            if(!isEmpty($data)){
                return $data;
            }
        }
        //sub category search
        $subcategory = SubCategories::where('name',$id);
        if($subcategory->count()){
            $data = $subcategory->paginate(10);
            $data->load('products.productImages');
            if(!isEmpty($data)){
                return $data;
            }
        }
        //color search
        $color = ProductColor::where('color',$id);
        if($color->count()){
            $data = $color->paginate(10);
            $data->load('products.productImages');
            if(!isEmpty($data)){
                return $data;
            }
        }
        //size search
        $size =  ProductSize::where('size',$id);
        if($size->count()){
            $data = $size->paginate(10);
            $data->load('products.productImages');
            if(!isEmpty($data)){
                return $data;
            }
        }
        return $data;
    }

    public function search_filter($id){

        try{
            $data = FeaturesController::search_data($id);
        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>"products fetch successfully",
            'data'=>$data
        ]);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
        
    }

    public function filter_product(Request $request){
        // $data = FeaturesController::search_data($request->id)->where('price','<',$request->price);
        $category =  Categories::where('name',$request->id);
        $subcategory = SubCategories::where('name',$request->id);
        $color = ProductColor::where('color',$request->id);
        $size =  ProductSize::where('size',$request->id);
        if($category->count()){
            $data = $category->with([
                'products' => function ($query){
                    
                    $query->where('price', '<',1000)->with('productImages');
                }
            ])->get();
            // $data = $category->with('products')->where('products.price', '<', 1000)->get();
            
        }

        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>"products fetch successfully",
            'data'=>$data
        ]);
    }
}
