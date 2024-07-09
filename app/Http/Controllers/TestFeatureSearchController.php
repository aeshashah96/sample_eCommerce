<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\SubCategories;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class TestFeatureSearchController extends Controller
{
    public function test_search($id){


        $key_words = explode(" ",$id);

        $data =[];

        foreach($key_words as $word){

            $category = Categories::where('name','like',"%$word%");
            if($category->count()){
                $data = Product::whereHas('category',function($query) use ($word){
                    $query->where('name','like',"%$word%");
                });
                array_shift($passed_word,$word);
                break;
            }

            $subcategory = SubCategories::where('name','like',"%$word");
            if($subcategory->count()){
                $data = Product::whereHas('subcategory',function($query) use ($word){
                    $query->where('name','like',"%$word%");
                });
                array_shift($passed_word,$word);
                break;
            }

            $color = ProductColor::where('color','like',"%$word%");
            if($color->count()){
                $data = Product::whereHas('colors',function($query) use ($word){
                    $query->where('color','like',"%$word%");
                });
                array_shift($passed_word,$word);
                break;
            }

            $size = ProductSize::where('size','like',"%$word%");
            if($size->count()){
                $data = Product::whereHas('colors',function($query) use ($word){
                    $query->where('size','like',"%$word%");
                });
                array_shift($passed_word,$word);
                break;
            }

            array_shift($passed_word,$word);
        }

        // if(!isEmpty($data)){
        //     foreach($key_words as $word){

        //         $category = Categories::where('name','like',"%$word%");
        //         if($category->count()){
        //             $data->whereHas('category',function($query) use ($word){
        //                 $query->where('name','like',"%$word%");
        //             });
        //             array_shift($passed_word,$word);
        //             continue;
        //         }
    
        //         $subcategory = SubCategories::where('name','like',"%$word");
        //         if($subcategory->count()){
        //             $data->whereHas('subcategory',function($query) use ($word){
        //                 $query->where('name','like',"%$word%");
        //             });
        //             array_shift($passed_word,$word);
        //             continue;
        //         }
    
        //         $color = ProductColor::where('color','like',"%$word%");
        //         if($color->count()){
        //             $data->whereHas('colors',function($query) use ($word){
        //                 $query->where('color','like',"%$word%");
        //             });
        //             array_shift($passed_word,$word);
        //             continue;
        //         }
    
        //         $size = ProductSize::where('size','like',"%$word%");
        //         if($size->count()){
        //             $data->whereHas('colors',function($query) use ($word){
        //                 $query->where('size','like',"%$word%");
        //             });
        //             array_shift($passed_word,$word);
        //             continue;
        //         }
        //     }
        // }
        
    }
}
