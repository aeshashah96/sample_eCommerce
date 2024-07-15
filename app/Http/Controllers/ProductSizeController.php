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
        //get all sizes
        $size=ProductSize::orderBy('created_at','DESC')->paginate(10);
        if($size){

            return response()->json(['success'=>true,'status'=>200,'message'=>'Product Size Get Successfully','data'=>$size]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Recode Found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //add a new size in database
        try{
            $validatedData = $request->validate([
                'size' => 'required|string|max:30|unique:product_sizes,size',
            ]);
           $size = ProductSize::create([
                'size'=>$request->size,
            ]);
            if($size){
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Size Add Successfully']);
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
        //for get a single size data
        $size=ProductSize::find($id);
        if($size){

            return response()->json(['success'=>true,'status'=>200,'data'=>$size]);
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'No Size Found']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //update a specific size
        try{
            $validatedData = $request->validate([
                'size' => 'required|string|max:30|unique:product_sizes,size',
            ]);
           $size = ProductSize::find($id);
        
           if($size){
                $size->update([
                     'size'=>$request->size,
                 ]);
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Size Update Successfully']);
            }else{
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Size Not Found'],);

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
        //delete a specific size
        try{
            $size=ProductSize::find($id);
            if($size){
                $size->delete();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Size Deleted Successfully']);
            }else{
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Size Not Found']);
            }
        }catch(Exception $e){
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);

        }
    }
}
