<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Http\Requests\UserRequest;
use App\Jobs\SendEmailUser;
use App\Mail\VerifyEmail;
use App\Models\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
                // $user->sendEmailVerificationNotification();
                SendEmailUser::dispatch($user);
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'message' => 'User Register Successfully.',
                ]);
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
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Invalid Credentials',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e->getMessage(),
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
                        'data' => $user,
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
                    'data' => $user,
                    'image_url' => url("/images/users/$user->user_logo"),
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e->getMessage(),
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
            $validator = Validator::make(
                $email,
                [
                    'email' => 'required|email|exists:users,email',
                ],
                [
                    'email' => [
                        'required' => 'The email field is required.',
                        'email' => 'The email field must be a valid email address.',
                        'exists' => 'Invalid Email Found',
                    ],
                ],
            );
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ]);
            }

            $user = ResetPassword::where('email', $email['email'])
                ->get()
                ->first();
            if (!is_null($user)) {
                $user->delete();
            }

            $otp = rand(100000, 999999);
            $password_reset = DB::table('reset_passwords')->insert([
                'email' => $email['email'],
                'otpCode' => Hash::make($otp),
                'created_at' => Carbon::now('Asia/Kolkata'),
                'expiry_at' => Carbon::now('Asia/Kolkata')->addMinute(10),
            ]);
            if ($password_reset) {
                Mail::to($email)->send(new VerifyEmail($otp, $email));
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'A Verification Mail Has Been Sent',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 500,
                    'message' => 'Internal Server Error',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // OTP Verification
    public function otpVerification(Request $request)
    {
        try {
            $otp = $request->only('otp');
            $validator = Validator::make(
                $otp,
                [
                    'otp' => 'required',
                ],
                [
                    'otp' => [
                        'required' => 'OTP is required.',
                    ],
                ],
            );
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ]);
            }

            $userOTP = ResetPassword::all();
            $flag = 0;
            if ($userOTP->first()) {
                foreach ($userOTP as $element) {
                    if (Hash::check($otp['otp'], $element->otpCode)) {
                        if (Carbon::now('Asia/Kolkata') > $element->expiry_at) {
                            $element->delete();
                            return response()->json([
                                'success' => false,
                                'status' => 404,
                                'message' => 'OTP Expiry',
                            ]);
                        } else {
                            return response()->json([
                                'success' => true,
                                'status' => 200,
                                'message' => 'OTP Verified SuccessFully',
                            ]);
                        }
                    } else {
                        $flag = 1;
                    }
                }
                if ($flag == 1) {
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'Invalid OTP',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Invalid OTP',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
    // User Reset Password Link
    public function resetPassword(Request $request)
    {
        try {
            $input = $request->only('otp', 'email', 'password', 'password_confirmation');
            $validator = Validator::make($input, [
                'otp' => 'required',
                'email' => 'required|email|exists:reset_passwords,email',
                'password' => 'required|confirmed|min:3',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $verifyEmail = ResetPassword::where('email', $input['email']);
            $otpCode = $verifyEmail->pluck('otpCode')->first();
            $expiryDate = ResetPassword::where('email', $input['email'])
                ->pluck('expiry_at')
                ->first();
            if (Hash::check($input['otp'], $otpCode)) {
                if (Carbon::now('Asia/Kolkata') > $expiryDate) {
                    $verifyEmail->delete();
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'OTP Expiry',
                    ]);
                } else {
                    $user = User::where('email', $input['email'])->first();
                    $user->password = Hash::make($input['password']);
                    $user->save();
                    $verifyEmail->delete();
                    return response()->json([
                        'success' => true,
                        'status' => 200,
                        'message' => 'Password has been successfully reset',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Invalid OTP',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
    // Verification Mail
    public function verify($id,Request $request)
    {   
        if (!$request->hasValidSignature()) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Invalid Expired URL Provided',
            ]);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Verification Mail Sent SuccessFully',
        ]);
    }

    public function resend(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
