<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // User Registration
    public function userRegister(UserRequest $request)
    {
        try {
            $userAvatar = 'userLogo.png';

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'password_confirmation ' => $request->password_confirmation,
                'phone_number' => $request->phone_number,
                'user_logo' => $userAvatar,
            ]);

            if ($user) {
                return response()->json([
                    'status' => 200,
                    'msg' => 'User Register Successfully.',
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'status' => 503,
                    'msg' => 'User Not Register',
                ]);
            }
        } catch (Exception $th) {
            return response()->json([
                'status' => 'warning',
                'msg' => $th,
            ]);
        }
    }

    // User Login
    public function userLogin(UserLogin $request)
    {
        $userCredential = request(['email', 'password']);

        if (Auth::attempt($userCredential)) {
            $user = $request->user();
            $user->isActive = true;
            $user->save();
            $token = $user->createToken('Has API Token')->accessToken;
            return response()->json([
                'status' => 200,
                'msg' => 'Login In SuccessFully',
                'token' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'msg' => 'Invalid Credentials',
            ]);
        }
    }

    // User Logout
    public function userLogout(Request $request)
    {   
        $request->user()->isActive = false;
        $request->user()->save();
        $request->user()->tokens()->delete();
        return response()->json(
            [
                'status' => 200,
                'msg' => 'Log Out SuccessFully',
            ],
            200,
        );
    }
}
