<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use App\Models\ImageProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request as HttpRequest;

// use Illuminate\Http\Request;

class BannersController extends Controller
{
    public function bannerCreate(HttpRequest $request)
    {
        $bannerimage = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->image->move(public_path('images/banners'), $bannerimage);
        $imagename = url("/images/banners/$bannerimage");
        $banners = Banners::create([
            'image' => $imagename,
            'description' => $request->description,
            'banner_title' => $request->banner_title,
            'sub_category_id' => $request->sub_category_id
        ]);
        return response()->json([
            'success' => true,
            'banners' => $banners
        ]);
    }

    public function showBanner()
    {
        try {
            $banner = Banners::all();
            $subcategory = Banners::with('subcategory')->get();
            return response()->json([
                'success' => true,
                'banner' => $banner,
                'subcatrgory' => $subcategory,
                'message' => 'Banner get Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'succsess' => false,
                'message' => 'Banner is not get',
                'error' => $e
            ]);
        }
    }
}
