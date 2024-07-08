<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVarient;
use App\Models\SubCategories;
use Exception;
use Illuminate\Http\Request;

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
        $category = Categories::where('name', $request->id);
        $data=[];
        if($category->count()){
            $data = $category->with(['products'=>function($query) use ($request){
                if ($request->price) {
                    $query->where('price', '<', $request->price);
                }
                
                if ($request->color) {
                    $query->whereHas('colors', function ($query) use ($request) {
                        $query->where('color', $request->color);
                    });
                    
                }
                
                if ($request->size) {
                    $query->whereHas('sizes', function ($query) use ($request) {
                        $query->where('size', $request->size);
                    });
                }
                $query->with('productImages');
            }])->paginate(10);
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => "products fetch successfully",
                'data' => $data
            ]);
        }

        $subcategory = SubCategories::where('name', $request->id);

        if($subcategory->count()){
            $data = $subcategory->with(['products'=>function($query) use ($request){
                if ($request->price) {
                    $query->where('price', '<', $request->price);
                }
                
                if ($request->color) {
                    $query->whereHas('colors', function ($query) use ($request) {
                        $query->where('color', $request->color);
                    });
                    
                }
                
                if ($request->size) {
                    $query->whereHas('sizes', function ($query) use ($request) {
                        $query->where('size', $request->size);
                    });
                }
                $query->with('productImages');
            }])->paginate(10);
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => "products fetch successfully",
                'data' => $data
            ]);
        }
        
        $color = ProductColor::where('color',$request->id);

        if($color->count()){
            $data = $color->with(['products'=>function($query) use ($request){
                if ($request->price) {
                    $query->where('price', '<', $request->price);
                }
                
                if ($request->color) {
                    $query->whereHas('colors', function ($query) use ($request) {
                        $query->where('color', $request->color);
                    });
                    
                }
                
                if ($request->size) {
                    $query->whereHas('sizes', function ($query) use ($request) {
                        $query->where('size', $request->size);
                    });
                }
                $query->with('productImages');
            }])->paginate(10);
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => "products fetch successfully",
                'data' => $data
            ]);
        }  
        
        
        $size =  ProductSize::where('size',$request->id);

        if($size->count()){
            $data = $size->with(['products'=>function($query) use ($request){
                if ($request->price) {
                    $query->where('price', '<', $request->price);
                }
                
                if ($request->color) {
                    $query->whereHas('colors', function ($query) use ($request) {
                        $query->where('color', $request->color);
                    });
                    
                }
                
                if ($request->size) {
                    $query->whereHas('sizes', function ($query) use ($request) {
                        $query->where('size', $request->size);
                    });
                }
                $query->with('productImages');
            }])->paginate(10);
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => "products fetch successfully",
                'data' => $data
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 404,
            'message' => "products not found",
            'data' => $data
        ]);
        
    }

    public function search_by_vatiant($id){
        $data = ProductVarient::where('variant_name','like',"%$id%")->paginate(10);
        $data = $data->load('products.productImages');
        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>'products fetch successfully',
            'data'=>$data
        ]);
    }

    public function filter_by_vatiant(Request $request){
        $data = ProductVarient::where('variant_name','like',"%$request->id%");
        if ($request->price) {
            $data->whereHas('products',function($query) use($request){
                $query->where('price','<',$request->price);
            });
        }
        
        if ($request->color) {
            $color_id = ProductColor::where('color',$request->color)->first()->id;
            $data->where('product_color_id',$color_id);
            
        }
        
        if ($request->size) {
            $size_id = ProductSize::where('size',$request->size)->first()->id;
            $data->where('product_size_id',$size_id);
        }
        $data = $data->with('products.productImages')->paginate(10);
        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>'products fetch successfully',
            'data'=>$data
        ]);
    }
}
