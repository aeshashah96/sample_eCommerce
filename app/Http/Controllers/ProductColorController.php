<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;
use Exception;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $color=ProductColor::orderBy('created_at','DESC')->paginate(10);
        if($color){
            return response()->json(['success'=>true,'status'=>200,'message'=>'Color Get Successfully','data'=>$color]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Recode Found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'color' => 'required|string|max:255|unique:product_colors,color',
                'hex_code'=>'string',
            ]);
           $color = ProductColor::create([
                'color'=>$request->color,
                'hex_code'=>$request->hex_code,
            ]);
            if($color){
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Color Add Successfully']);
            }else{
                return response()->json(['success' => false, 'status' => 500, 'message' => 'Error Found']);

            }
        }catch(Exception $e){
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $color=ProductColor::find($id);
        if($color){
            return response()->json(['success'=>true,'status'=>200,'message'=>'Color Get Successfully','data'=>$color]);

        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Color Found']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validatedData = $request->validate([
                'color' => 'string|max:15|unique:product_colors,color',
                'hex_code'=>'string'
            ]);
            $color = ProductColor::find($id);
            if($color){
                $color->update([
                     'color'=>$request->color,
                     'hex_code'=>$request->hex_code,
                 ]);
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Color Update Successfully']);
            }else{
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Color Not Found']);

            }
        }catch(Exception $e){
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $color=ProductColor::find($id);
            if($color){
                $color->delete();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Color Deleted Successfully']);
            }else{
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Color Not Found']);
            }
        }catch(Exception $e){
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }
}
