<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettingData(){
        try{
            $data = Setting::get();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'data'=>$data
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }
    public function updateSettingData(Request $request){
        try{
            if($request->has('LOGO')){
                $image = $request->LOGO;
                unlink(public_path('/upload/logo/logo.png'));
                $image->move(public_path('/upload/logo'), 'logo.png');
            }
            foreach($request->input() as $key=>$val){
                Setting::where('key',$key)->update(['value'=>$val]);
            }
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'Data updated sucessfully'
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }
}
