<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try{
            $data = State::paginate(10,['id','state_name','status']);
        return response()->json([
            'success'=>true,
            'status'=>200,
            'data'=>$data
        ]);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        try{
            $data = State::find($id,['id','state_name','status']);
            if($data){
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'data'=>$data,
                    'message'=>'state details fetch successfully'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'code'=>404,
                    'message'=>'record not found'
                ]);
            }
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validatedData = $request->validate([
                'status' => 'required|boolean',
            ]);
            $item = State::find($id);
            if($item){
                $item->update($request->input());
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'record updated successfully'
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'code'=>404,
                    'message'=>'record not found'
                ]);
            }
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
            $item = State::find($id);
            if($item){
                City::where('state_id',$id)->delete();
                $item->delete();
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'record deleted successfully',
                ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'code'=>404,
                    'message'=>'record not found'
                ]);
            }
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $e->getMessage()
            ]);
        }
    }
}
