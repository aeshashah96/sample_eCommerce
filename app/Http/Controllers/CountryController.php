<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try{
            $data = Country::paginate(10,['id','country_name','country_code','status']);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'data'=>$data,
                'message'=>'countries get successfully'
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
            $data = Country::find($id,['id','country_name','country_code','status']);
            if($data){
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'data'=>$data,
                    'message'=>'country details fetch successfully'
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
            $item = Country::find($id);
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
            $item = Country::find($id);
            if($item){
                $states = State::where('country_id',$id)->get();
                foreach($states as $state){
                    Country::where('state_id',$state->id)->delete();
                }
                State::where('country_id',$id)->delete();
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

    public function changeActiveStatus($id){
        $country=Country::find($id);
        if($country){

            if($country->status){
                // dd($id);
                $country->status=0;
                $country->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Country Status Change Successfully']);
            }else{
                $country->status=1;
                $country->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Country Status Change Successfully']);
            }
        }else{
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Country Not Found']);
        }
    }
}
