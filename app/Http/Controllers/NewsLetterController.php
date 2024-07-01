<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsLatterRequest;
use App\Models\NewsLetter;
use Exception;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try{
            $data = NewsLetter::get();
            return response()->json([
                'code'=>200,
                'data'=>$data
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsLatterRequest $request)
    {   
        try{
            NewsLetter::create($request->input());
            return response()->json([
                'code'=>200,
                'message'=>'record created sucessfully'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $data = NewsLetter::find($id);
            return response()->json([
                'code'=>200,
                'data'=>$data
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        try{
            NewsLetter::find($id)->update($request->input());
            return response()->json([
                'code'=>200,
                'message'=>'record updated successfully'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
            
    }   

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
            NewsLetter::find($id)->delete();
            return response()->json([
                'code'=>200,
                'message'=>'record deleted successfully'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }
}
