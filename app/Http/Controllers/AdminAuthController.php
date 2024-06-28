<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminEditProfileRequest;
use App\Http\Requests\AdminValidationRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
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
                'Message'=>'incorrect email'
            ],200);
        }
            
        }
        catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    public function admin_logout(Request $request){
        try{
        $request->user()->tokens()->delete();
            return response()->json([
                'code'=>200,
                'message'=>'logout successful'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
        
    }

    public function fetch_admin_data(Request $request){
        try{
            $user =  $request->user();
            return response()->json([
                'code'=>200,
                'data'=>$user
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    public function edit_admin_data(AdminEditProfileRequest $request){
        try{
            $item = Admin::find($request->user()->id);
        if($request->has('admin_logo')){
            if($item->admin_logo){
                $name = $item->admin_logo;
                 $image_path = "upload/Admin/admin_logo/$name";
                 unlink($image_path);
            }

             $file = $request->file('admin_logo');
             $extention = $file->getClientOriginalExtension();
             $admin_logo_name = time().".".$extention;
             $file->move('upload/Admin/admin_logo/',$admin_logo_name);
             $item->admin_logo = $admin_logo_name;
             $item->admin_logo_url =url("/upload/Admin/admin_logo/$admin_logo_name");
             $item->save();
         }
         $item->first_name = $request->first_name;
         $item->last_name = $request->last_name;
         $item->phone_number = $request->phone_number;
         $item->save();
         return response()->json([
             'code'=>200,
             'message'=>'data updated successfully'
         ],200);
        }
        catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    public function change_admin_password(Request $request){
        $user =  Admin::find($request->user()->id);
            if(Hash::check($request->current_password,$user->password)){
                if($request->new_password == $request->confirm_password){
                    $user->password = Hash::make($request->new_password);
                    $user->save();
                    return response()->json([
                        'code'=>200,
                        'message'=>'password change successfully'
                    ],200);
                }else{
                    return response()->json([
                        'code'=>404,
                        'message'=>'new password and confirm password are not same'
                    ],404);    
                }
            }else{
                return response()->json([
                    'code'=>404,
                    'message'=>'enter a correct password'
                ],404);
            }
    }
    
}
