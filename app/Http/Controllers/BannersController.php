<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerValidationRequest;
use App\Models\Banners;
use Exception;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $data = Banners::with('subcategory')->orderBy('created_at','desc')->paginate(10);   
            return response()->json([
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'message' => $e
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerValidationRequest $request)
    {
        try {
            if ($request->has('image')) {
                $file = $request->file('image');
                $extention = $file->getClientOriginalExtension();
                $image_name = time() . "." . $extention;
                $file->move('upload/banners/', $image_name);
            }
            Banners::create([
                'image' => $image_name,
                'description' => $request->description,
                'banner_title' => $request->banner_title,
                'banner_url' => url("/upload/banners/$image_name"),
                'sub_category_id' => $request->sub_category_id
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'banner added successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'message' => $e
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $item = Banners::find($id);
            if (!$item) {
                return response()->json([
                    'code' => 401,
                    'message' => 'record not found'
                ], 401);
            }
            if ($request->has('image')) {
                if ($item->image) {
                    $name = $item->image;
                    $image_path = "upload/banners/$name";
                    unlink($image_path);
                }

                $file = $request->file('image');
                $extention = $file->getClientOriginalExtension();
                $banner_name = time() . "." . $extention;
                $file->move('upload/banners/', $banner_name);
                $item->image = $banner_name;
                $item->banner_url = url("/upload/banners/$banner_name");
            }
            $item->update($request->input());
            return response()->json([
                'code' => 200,
                'message' => 'banner updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'message' => $e
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = Banners::find($id);
            if(!$item){
                return response()->json([
                    'code'=>404,
                    'message'=>'record not found'
                ],404);
            }
            unlink("upload/banners/$item->image");
            $item->delete();
            return response()->json([
                'code' => 200,
                'message' => 'banner deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'message' => $e
            ], 404);
        }
    }
    // function for get banner frontend side 
    public function homeBanner()
    {
        try {
            $BannerWithSubcategory = Banners::select('id','image','description','banner_title','sub_category_id')->with('subcategory:id,name')->orderBy('id','DESC')->get();
            if ($BannerWithSubcategory) {
                foreach ($BannerWithSubcategory as $subcat) {
                    $subcat['image'] = url("/upload/banners/" . $subcat->image);
                }
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'BannerWithSubvategory' => $BannerWithSubcategory,
                    'message' => 'Banner Show Successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 404,
                    'message' => 'Banners Are Not Found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], 200);
        }
    }
}
