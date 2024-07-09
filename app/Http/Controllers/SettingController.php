<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getSettingData(){
        try{
            $data = Setting::get();

            ($data[0]->value=url('upload/logo/'.$data[0]->value));
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'fetch setting data successfully',
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
    public function updateSettingData(SettingRequest $request){
        try{

            if($request->has('LOGO')){
                $image = $request->LOGO;
                $logoname=Setting::where('key','LOGO')->first()->value;
                unlink(public_path('/upload/logo/'.$logoname));
                $image->move(public_path('/upload/logo/'), $image->getClientOriginalName());
                Setting::where('key','LOGO')->update(['value'=>$image->getClientOriginalName()]);
            }
            foreach($request->input() as $key=>$val){
                Setting::where('key',$key)->update(['value'=>$val]);
            }
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'Data updated sucessfully '
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
