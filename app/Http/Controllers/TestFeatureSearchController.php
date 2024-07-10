<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductReview;
use App\Models\ProductSize;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TestFeatureSearchController extends Controller
{   
    public function product_search($data,$id){
        $data = $data->where('products.name','like',"%$id%");
        return $data;
    }
    public function category_search($data,$id){
        $data = $data->where('categories.name','like',"%$id%");
        return $data;
    }
    public function sub_category_search($data,$id){
        $data = $data->where('sub_categories.name','like',"%$id%");
        return $data;
    }
    public function color_search($data,$id){
        $data = $data->where('product_colors.color','like',"%$id%");
        return $data;
    }
    public function size_search($data,$id){
        $data = $data->where('product_sizes.size','like',"%$id%");
        return $data;
    }
    public function redirect_search($data,$id,$flag){
            //searching string in  category name
            $category = Categories::where('name','like',"%$id%");
            if($category->count()){
                //redirect to category_search
                $data = TestFeatureSearchController::category_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }

            //searching string in sub-category name
            $sub_category = SubCategories::where('name','like',"%$id%");
            if($sub_category->count()){
                //redirect to sub_category_search
                $data = TestFeatureSearchController::sub_category_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];

            }

            //searching string in productColor table
            $color = ProductColor::where('color','like',"%$id%");
            if($color->count()){
                //redirect to color_search
                $data = TestFeatureSearchController::color_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }

            //searching string in productSize table
            $size = ProductSize::where('size','like',"%$id%");
            if($size->count()){
                //redirect to size_search
                $data = TestFeatureSearchController::size_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }

            //searching string in products table
            $product = Product::where('name','like',"%$id%");
            if($product->count()){
                //redirect to product_search
                $data = TestFeatureSearchController::product_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }
            return ['flag'=>$flag,'data'=>$data];
    }

    public function test_search($id){
        /*
        join category,subcategory,productColor,productSize tables to product table 
        */
        $data = DB::table('products')
                ->join('product_varients','products.id','=','product_varients.product_id')
                ->join('categories','categories.id','=','products.category_id')
                ->join('sub_categories','sub_categories.id','=','products.sub_category_id')
                ->leftJoin('product_sizes','product_sizes.id','=','product_varients.product_size_id',)
                ->leftJoin('product_colors','product_colors.id','=','product_varients.product_color_id',);
                
        /*
        sending $data collecting and searched string to redirect_search function 
        */
        $value =  TestFeatureSearchController::redirect_search($data,$id,$flag=false);
        
        /*
        if records are found for whole string then $value['flag'] returns true and return data to serach or filter function
        */
        if($value['flag']){
            return $value['data'];
            
        }
        /*
        if record not found for whole string then devide string by space and find for each word 
        */
        $key_words = explode(" ",$id);

        foreach($key_words as $word){
            /*
            searching for each word of string using redirect_search function
            */
            $value =  TestFeatureSearchController::redirect_search($data,$word,$flag=false);
            $data = $value['data'];
        }   
        /*
        return to search or filter function 
         */
        return $value['data'];
        
    }

    public function search_filter(Request $request){
        /*
        sending searching string from param to test_search function
        */
        $value = TestFeatureSearchController::test_search($request->search);
        $data = $value->get('products.id')->toArray();      
        /*
        get search result products ids and getting data using model
        */    
        $final = array_unique(array_column($data, 'id'));   
        
        $product = Product::whereIn('id',$final)->paginate(10);
        foreach($product as $item){
            $item->avg_rating = ProductReview::where('product_id',$item->id)->pluck('rating')->avg();
            $item->total_review = ProductReview::where('product_id',$item->id)->pluck('rating')->count();
            $item->product_image = url("/images/product/".ImageProduct::where('product_id',$item->id)->pluck('image')->first());
        }
        
        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>'products fetch successfully',
            'data'=>$product
        ]);
    }
    public function filter_feature(Request $request){
        /*
        sending searching string from param to test_search function
        */
        $value = TestFeatureSearchController::test_search($request->search);
        /*
        filter search results by price 
        */
        if($request->price){
            $value->where('products.price','<',$request->price);
        }
        /*
        filter search results by color 
        */
        if($request->color){
            $value->where('product_colors.color',$request->color);
        }
        /*
        filter search results by size 
        */
        if($request->size){
            $value->where('product_sizes.size',$request->size);
        }
        $data = $value->get('products.id')->toArray();
        $final = array_unique(array_column($data, 'id'));
        /*
        get search result products ids and getting data using model
        */    
        $product = Product::whereIn('id',$final)->paginate(10);
        foreach($product as $item){
            $item->avg_rating = ProductReview::where('product_id',$item->id)->pluck('rating')->avg();
            $item->total_review = ProductReview::where('product_id',$item->id)->pluck('rating')->count();
            $item->product_image = url("/images/product/".ImageProduct::where('product_id',$item->id)->pluck('image')->first());
        }
        return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>'products fetch successfully',
            'data'=>$product
        ]);
    }
}
