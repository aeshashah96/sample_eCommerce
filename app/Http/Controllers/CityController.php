<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
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
                    'message'=>'city details fetch successfully',
                    'data'=>$data
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
    //search city api
    public function search_city($id = null){
        try{
            if(!$id){
                return response()->json([
                    "success"=>true,
                    'status'=>200,
                    'message'=>'cities fetch successfully',
                    'data'=>[]
                ]);
            }
            $data = City::where('city_name','like',"%$id%")->get(['id','city_name']);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'cities fetch successfully',
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

    //select city
    public function select_city($id){
        try{
            $city = City::with('get_state_from_city')->get()->find($id);
            $state = State::with('get_country_from_state')->get()->find($city->get_state_from_city->id);
            $data = [
                'city_name'=>$city->city_name,
                'state_name'=>$city->get_state_from_city->state_name,
                'country_name'=>$state->get_country_from_state->country_name
            ];
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'city fetch successfully',
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

    public function changeActiveStatus($id){
        $city=City::find($id);
        if($city){

            if($city->status){
                // dd($id);
                $city->status=0;
                $city->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'City Status Change Successfully']);
            }else{
                $city->status=1;
                $city->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'City Status Change Successfully']);
            }
        }else{
            return response()->json(['success' => false, 'status' => 404, 'message' => 'City Not Found']);
        }
    }
}
