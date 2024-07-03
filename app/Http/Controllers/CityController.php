<?php

namespace App\Http\Controllers;

use App\Models\City;
use Exception;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function get_cities(){
        try{
            $data =  City::paginate(10,['id','city_name','status']);
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
    public function delete_city($id){
        $item = city::find($id);
        if($item){
            $item->delete();
            return response()->json([
                'success'=>true,
                'code'=>200,
                'message'=>'record deleted successfully'
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'code'=>404,
                'message'=>'record not found'
            ]);
        }
    }

    public function edit_city(Request $request,$id){
        try{
        $validatedData = $request->validate([
            'status' => 'required|boolean',
        ]);
        $item = City::find($id);
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
        } catch (Exception $e) {
        return response()->json(['success' => false,'status' => 422, 'message' => $e->getMessage()]);
        }
    }

    public function view_city($id){
        try{
            $data = City::find($id,['id','city_name','status']);
            if($data){
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'data'=>$data,
                    'message'=>'city details fetch successfully'
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
}
