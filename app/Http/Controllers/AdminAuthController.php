<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function admin_auth(Request $request){
        $user =  Admin::where('email',$request->email)->first();
        if($user){
            $userValidation = Hash::check($request->password,$user->password);
            if($userValidation){
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
}
