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
use Illuminate\Support\Facades\Validator;

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
                SendEmailUser::dispatch($user);
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
            $request->user()->token()->delete();
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
                return response()->json(
                    [
                        'success' => false,
                        'status' => 404,
                    ],
                    404,
                );
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }

    // Update User Profile
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            if ($request->hasFile('user_logo')) {
                $img_path = public_path("/images/users/$user->user_logo");
                unlink($img_path);
                $userFile = $request->file('user_logo');
                $userLogo = time() . '.' . $userFile->getClientOriginalExtension();
                $destinationPath = public_path('images/users/');
                $userFile->move($destinationPath, $userLogo);
                $user->update([
                    'user_logo' => $userLogo,
                ]);
            }
            $user->update($request->input());
            return response()->json(
                [
                    'status' => 200,
                    'success' => true,
                    'message' => 'User Updated SuccessFully',
                    'user' => $user,
                    'image_url' => url("/images/users/$user->user_logo"),
                ],
                200,
            );
        } 
        catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }

    // User Change Password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:4',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'validations fails',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $user = $request->user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json(
                [
                    'success'=>true,
                    'status' => 200,
                    'message' => 'Password Successfully Updated',
                    'errors' => $validator->errors(),
                ],
                200,
            );
        }
        else{
            return response()->json(
                [
                    'success'=>false,
                    'status' => 200,
                    'message' => 'Invalid Password',
                ],
                200,
            );
        }
    }
}
