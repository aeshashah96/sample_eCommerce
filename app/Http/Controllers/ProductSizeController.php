<?php

namespace App\Http\Controllers;

use App\Models\ProductSize;
use Exception;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $size=ProductSize::orderBy('created_at','DESC')->paginate(10);
        if($size){

            return response()->json(['success'=>true,'code'=>200,'size'=>$size]);
        }else{
            return response()->json(['success'=>false,'code'=>404,'message'=>'No Recode Found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $validatedData = $request->validate([
                'size' => 'required|string|max:255|unique:product_sizes,size',
            ]);
           $size = ProductSize::create([
                'size'=>$request->size,
            ]);
            if($size){
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Size Add Successfully'], 201);
            }else{
                return response()->json(['success' => false, 'code' => 500, 'message' => 'Error Found'], 500);

            }
        }catch(Exception $e){
            return response()->json(['success' => false,'code' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $size=ProductSize::find($id);
        if($size){

            return response()->json(['success'=>true,'code'=>200,'size'=>$size]);
        }else{
            return response()->json(['success'=>false,'code'=>404,'message'=>'No Size Found']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try{
            $validatedData = $request->validate([
                'size' => 'required|string|max:255|unique:product_sizes,size',
            ]);
           $size = ProductSize::find($id)->update([
                'size'=>$request->size,
            ]);
            if($size){
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Size Update Successfully'], 201);
            }else{
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Size Not Found'], 404);

            }
        }catch(Exception $e){
            return response()->json(['success' => false,'code' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{
            $size=ProductSize::find($id);
            if($size){
                $size->delete();
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Size Deleted Successfully'], 200);
            }else{
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Size Not Found'], 404);
            }
        }catch(Exception $e){
            return response()->json(['success' => false,'code' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }
}
