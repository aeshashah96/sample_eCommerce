<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{   

    // User Registration
    public function userRegister(UserRequest $request)
    {   
        try {
            $userAvatar  = 'userLogo.png';

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'password_confirmation ' => $request->password_confirmation,
                'phoneNumber' => $request->phoneNumber,
                'user_logo'=>$userAvatar,
            ]);

            if ($user) {
                return response()->json([
                    'status' => 200,
                    'msg' => 'User Register Successfully.',
                ]);
            } 
            else {
                return response()->json([
                    'status' => 503,
                    'msg' => 'User Not Register',
                ]);
            }
        } 
        catch (Exception $th) {
            return response()->json([
                'status' => 'warning',
                'msg' => $th,
            ]);
        }
    }

    // User Login

}
