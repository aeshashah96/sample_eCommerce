<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

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
            $product = Product::where('name','like',"%$id%");
            if($product->count()){
                $data = TestFeatureSearchController::product_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }
            $category = Categories::where('name','like',"%$id%");
            if($category->count()){
                $data = TestFeatureSearchController::category_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }
            $sub_category = SubCategories::where('name','like',"%$id%");
            if($sub_category->count()){
                $data = TestFeatureSearchController::sub_category_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];

            }
            $color = ProductColor::where('color','like',"%$id%");
            if($color->count()){
                $data = TestFeatureSearchController::color_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }
            $size = ProductSize::where('size','like',"%$id%");
            if($size->count()){
                $data = TestFeatureSearchController::size_search($data,$id);
                $flag = true;
                return ['flag'=>$flag,'data'=>$data];
            }
    }

    public function test_search($id){

        $data = DB::table('products')
                ->join('product_varients','products.id','=','product_varients.product_id')
                ->join('categories','categories.id','=','products.category_id')
                ->join('sub_categories','sub_categories.id','=','products.sub_category_id')
                ->leftJoin('product_sizes','product_sizes.id','=','product_varients.product_size_id',)
                ->leftJoin('product_colors','product_colors.id','=','product_varients.product_color_id',);
                
                
        $value =  TestFeatureSearchController::redirect_search($data,$id,$flag=false);
        if($value['flag']){
            $data = $value['data']
            ->join('product_images','product_images.product_id','=','products.id')
            ->select('products.*','product_images.image')
            // ->groupBy('products.id')
            ->paginate(10);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'products fetch successfully',
                'data'=>$data
            ]);
        }
        $key_words = explode(" ",$id);

        foreach($key_words as $word){
            $value =  TestFeatureSearchController::redirect_search($data,$word,$flag=false);
            $data = $value['data'];
        }

        return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'products fetch successfully',
                'data'=>$data
        ]);
        
    }
}
