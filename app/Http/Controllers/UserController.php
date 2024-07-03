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
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Throwable;

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
                return response()->json(
                    [
                        'success' => true,
                        'status' => 201,
                        'message' => 'User Register Successfully.',
                        'user' => $user,
                        'image_url' => url("/images/users/$userAvatar"),
                    ],
                    201,
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'status' => 500,
                        'message' => 'Internal Server Error',
                    ],
                    500,
                );
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
        try {
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
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
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
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }

    // My Profile
    public function userProfile(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'user' => $user,
                        'image_url' => url("/images/users/$user->user_logo"),
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'User Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e,
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
                if ($user->user_logo != 'userLogo.png') {
                    unlink($img_path);
                }
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
                    'success' => true,
                    'status' => 200,
                    'message' => 'User Updated SuccessFully',
                    'user' => $user,
                    'image_url' => url("/images/users/$user->user_logo"),
                ],
                200,
            );
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e,
            ]);
        }
    }

    // User Change Password
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:4|regex:/^\S*$/u',
                    'confirm_password' => 'required|same:new_password',
                ],
                messages: [
                    'confirm_password' => 'Password and Confirm Password do not Match',
                ],
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ]);
            }

            $user = $request->user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->new_password),
                ]);

                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Password Successfully Updated',
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Invalid Password',
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

    // User Forgot Password
    public function forgotPassword(Request $request)
    {
        try {
            $email = $request->only('email');
            $validator = Validator::make($email, [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => 'Validations fails',
                    'errors' => $validator->errors()->first(),
                ]);
            }

            $status = Password::sendResetLink($email);

            return $status === Password::RESET_LINK_SENT
                ? response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => __($status),
                    ],
                    200,
                )
                : response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => __($status),
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }

    // User Reset Password Link
    public function resetPassword(Request $request)
    {
        try {
            $input = $request->only('email', 'token', 'password', 'password_confirmation');
            $validator = Validator::make($input, [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:3',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => 'Validations fails',
                    'errors' => $validator->errors()->first(),
                ]);
            }

            $status = Password::reset($input, function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            });

            return $status === Password::PASSWORD_RESET
                ? response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => __($status),
                    ],
                    200,
                )
                : response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => __($status),
                ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $th,
            ]);
        }
    }
}
