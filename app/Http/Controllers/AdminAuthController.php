<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminValidationRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function admin_auth(AdminValidationRequest $request){

        try{
            $user =  Admin::where('email',$request->email)->first();
        if($user){
            $userValidation = Hash::check($request->password,$user->password);
            if($userValidation){
                auth('adminApi')->setUser($user);
                $token = $user->createToken('Tokenname')->accessToken;
                return response()->json([
                    'code'=>'200',
                    'Message'=>'login successfully',
                    'Token'=>$token
                ],200);    
            }else{
                return response()->json([
                    'code'=>'200',
                    'Message'=>'incorrect password'
                ],200);    
            }
        }else{
            return response()->json([
                'code'=>'200',
                'Message'=>'Your email is not in database please register first'
            ],200);
        }
            
        }
        catch(Exception $e){
            return response()->json([
                'code'=>500,
                'error'=>$e
            ],500);
        }
    }

    // public function admin_logout(){
    //     auth('adminApi')->logout();
    //     return response()->json([
    //         'code'=>200,
    //         'message'=>'logout successful'
    //     ],200);
    // }

}
