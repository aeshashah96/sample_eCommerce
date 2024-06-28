<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Http\Requests\UserRequest;
use App\Jobs\SendEmailUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    // User Registration
    public function userRegister(UserRequest $request)
    {
        try {
            $userAvatar = 'userLogo.png';
            // $url = 'data:image/png;base64,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';
            // $imgContent = file_get_contents($url);
            // if ($imgContent == false) {
            //     return response()->json(['message' => 'error']);
            // }
            // $save = file_put_contents(storage_path("app/public/user/$userAvatar"), $imgContent);

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
                // SendEmailUser::dispatch($user);
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'User Register Successfully.',
                    'user' => $user,
                    'image_url' => url("/images/users/$userAvatar"),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 503,
                    'message' => 'User Not Register',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'warning',
                'message' => $e,
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
                'success' => true,
                'status' => 200,
                'message' => 'Login In SuccessFully',
                'token' => $token,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Invalid Credentials',
            ]);
        }
    }

    // User Logout
    public function userLogout(Request $request)
    {
        try {
            $request->user()->isActive = false;
            $request->user()->save();
            $request->user()->tokens()->delete();
            return response()->json(
                [
                    'success' => true,
                    'status' => 200,
                    'message' => 'Log Out SuccessFully',
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e,
            ]);
        }
    }

    // My Profile
    public function userProfile(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'user' => $user,
                    'image_url' => url("/images/users/$user->user_logo"),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }
}
