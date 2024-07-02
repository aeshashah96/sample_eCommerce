<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;
use Exception;
use Illuminate\Http\Request;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;

class ProductColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $color=ProductColor::orderBy('created_at','DESC')->get();
        if($color){

            return response()->json(['success'=>true,'code'=>200,'color'=>$color]);
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
                'color' => 'required|string|max:255|unique:product_colors,color',
                'hex_code'=>'string',
            ]);
           $color = ProductColor::create([
                'color'=>$request->color,
                'hex_code'=>$request->hex_code,
            ]);
            if($color){
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Color Add Successfully'], 201);
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try{
            $validatedData = $request->validate([
                'color' => 'string|max:255|unique:product_colors,color',
                'hex_code'=>'string'
            ]);
           $color = ProductColor::find($id)->update([
                'color'=>$request->color,
                'hex_code'=>$request->hex_code,
            ]);
            if($color){
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Color Update Successfully'], 201);
            }else{
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Color Not Found'], 404);

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
            $color=ProductColor::find($id);
            if($color){
                $color->delete();
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Color Deleted Successfully'], 200);
            }else{
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Color Not Found'], 404);
            }
        }catch(Exception $e){
            return response()->json(['success' => false,'code' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }
}
