<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminEditProfileRequest;
use App\Http\Requests\AdminValidationRequest;
use App\Http\Requests\PasswordValidationRequest;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function admin_login(AdminValidationRequest $request){

        try{
            $user =  Admin::where('email',$request->email)->first();
        if($user){
            $userValidation = Hash::check($request->password,$user->password);
            if($userValidation){
                auth('adminApi')->setUser($user);
                $token = $user->createToken('Tokenname')->accessToken;
                return response()->json([
                    'success'=>true,
                    'status'=>'200',
                    'message'=>'login successfully',
                    'token'=>$token
                ]);    
            }else{
                return response()->json([
                    'success'=>false,
                    'status'=>'401',
                    'message'=>'incorrect password'
                ]);    
            }
        }else{
            return response()->json([
                'success'=>false,
                'status'=>'401',
                'message'=>'incorrect email'
            ]);
        }
            
        }
        catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status'=>404,
                'error'=>$e
            ]);
        }
    }

    public function admin_logout(Request $request){
        try{
        $request->user()->tokens()->delete();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'logout successfully'
            ]);
        }catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status'=>404,
                'error'=>$e
            ]);
        }
        
    }

    public function admin_profile(Request $request){
        try{
            $user =  $request->user()->get([
                'first_name',
                'last_name',
                'email',
                'password',
                'phone_number',
                'admin_logo',
                'admin_logo_url'
            ]);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'data'=>$user,
                'message'=>'admin profile data'
            ]);
        }catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status'=>404,
                'error'=>$e
            ]);
        }
    }

    public function edit_admin_profile(AdminEditProfileRequest $request){
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
         $item->update($request->input());
         return response()->json([
            'success'=>true,
            'status'=>200,
            'message'=>'profile updated successfully'
         ]);
        }
        catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status'=>404,
                'error'=>$e
            ]);
        }
    }

    public function change_admin_password(PasswordValidationRequest $request){
        
        $user =  Admin::find($request->user()->id);
            if(Hash::check($request->current_password,$user->password)){
                    $user->password = Hash::make($request->new_password);
                    $user->save();
                    return response()->json([
                        'success'=>true,
                        'status'=>200,
                        'message'=>'password change successfully'
                    ]);
            }else{
                return response()->json([
                    'success'=>false,
                    'status'=>404,
                    'message'=>'enter a correct password'
                ]);
            }
    }
    
}
