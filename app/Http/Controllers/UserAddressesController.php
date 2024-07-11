<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressEditValidation;
use App\Http\Requests\UserAddressValidation;
use App\Models\UserAddresses;
use Exception;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function get_user_address($id){
        $data = UserAddresses::where('user_id',$id)->first();
        if($data){
            $final_data = [
                'id'=>$data->id,
                'billing_address'=>json_decode($data->billing_address),
                'shipping_address'=>json_decode($data->shipping_address)
            ];
            return response()->json([
                'Success'=>true,
                'status'=>200,
                'data'=>$final_data
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'status'=>404,
                'message'=>'record not found'
            ]);
        }  
    }

    public function add_user_address(UserAddressValidation $request){
        try{
            $user_id = $request->user_id;
            $billing_address = json_encode([
                'address_line_1' =>$request->address_line_1,
                'address_line_2' =>$request->address_line_2,
                'city'=>$request->city,
                'state'=>$request->state,
                'country'=>$request->country,
                'zipcode'=>$request->zipcode,
            ]);
            if($request->ship_to_different_address){

                //shipping address validation

                $validatedData = $request->validate([
                    'shipping_address_line_1' =>'required',
                    'shipping_address_line_2' =>'required',
                    'shipping_city'=>'required|string',
                    'shipping_state'=>'required|string',
                    'shipping_country'=>'required|string',
                    'shipping_zipcode'=>'required|numeric',
                ]);

                $shipping_address = json_encode([
                    'address_line_1' =>$request->shipping_address_line_1,
                    'address_line_2' =>$request->shipping_address_line_2,
                    'city'=>$request->shipping_city,
                    'state'=>$request->shipping_state,
                    'country'=>$request->shipping_country,
                    'zipcode'=>$request->shipping_zipcode,
                ]); 
            }else{
                $shipping_address = null;
            }
            UserAddresses::create([
                'user_id' =>$user_id,
                'billing_address' => $billing_address,
                'shipping_address'=> $shipping_address
            ]);
            return response()->json([
                'success'=>true,
                'status'=>201,
                'message'=>'address saved successfully'
            ]);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }

    public function edit_user_address(UserAddressEditValidation $request,$id){
        $item = UserAddresses::find($id);
        if($item){
            $billing_address = json_encode([
                'address_line_1' =>$request->address_line_1,
                'address_line_2' =>$request->address_line_2,
                'city'=>$request->city,
                'state'=>$request->state,
                'country'=>$request->country,
                'zipcode'=>$request->zipcode,
            ]);
            $item->update([
                'billing_address'=>$billing_address
            ]);
            return response()->json([
                'success'=>true,
                'code'=>200,
                'message'=>'address updated successfully'
            ]);
        }else{
            return response()->json([
                    'success'=>false,
                    'code'=>404,
                    'message'=>'record not found'
                ]);
        }
    }
}
