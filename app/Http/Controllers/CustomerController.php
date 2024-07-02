<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerEditRequest;
use App\Http\Requests\CustomerRequest;
use App\Jobs\SendPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try{
            $data = User::orderBy('created_at','desc')->paginate(10);
            return response()->json([
                'code'=>200,
                'data'=>$data
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {   
        
        if($request->has('user_logo')){
            $file = $request->file('user_logo');
            $extention = $file->getClientOriginalExtension();
            $user_logo_name = time().".".$extention;
            $file->move('images/users/',$user_logo_name);
        }
        
        $password = Str::password(16, true, true, true, false);
        $user = User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'user_logo'=>$user_logo_name,
            'password'=>Hash::make($password),
            'isActive'=>1,
        ]);
        if($user){
            SendPasswordMail::dispatch($user,$password);
            return response()->json([
                'code' => 200,
                'message' => 'Customer Register Successfully.',
            ],200);
        }else {
            return response()->json([
                'code' => 503,
                'message' => 'Customer Not Register',
            ],503);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        try{
            $user = User::find($id);
            if($user){
                return response()->json([
                    'code'=>200,
                    'data'=>$user
                ],200);
            }else{
                return response()->json([
                    'code'=>404,
                    'message'=>'record not found'
                ],404);
            }
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerEditRequest $request, string $id)
    {   
        try{
            $item = User::find($id);
            if(!$item){
                return response()->json([
                    'code'=>404,
                    'error'=>'record not found'
                ],404);    
            }
            if($request->has('user_logo')){
                if($item->user_logo && $item->user_logo != 'userLogo.png'){
                    $name = $item->user_logo;
                     $image_path = "images/users/$name";
                     unlink($image_path);
                }
    
                 $file = $request->file('user_logo');
                 $extention = $file->getClientOriginalExtension();
                 $user_logo_name = time().".".$extention;
                 $file->move('images/users/',$user_logo_name);
                 $item->update(['user_logo'=>$user_logo_name]);
            }
            $item->update($request->input());
            return response()->json([
                'code'=>200,
                'messasge'=>'user record updated'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
            $item = User::find($id);
            if(!$item){
                return response()->json([
                    'code'=>404,
                    'error'=>'record not found'
                ],404);    
            }
            if($item->user_logo){
                $name = $item->user_logo;
                 $image_path = "images/users/$name";
                 unlink($image_path);
            }
            $item->delete();
            return response()->json([
                'code'=>200,
                'message'=>'deleted record successfully'
            ],200);
        }catch(Exception $e){
            return response()->json([
                'code'=>404,
                'error'=>$e
            ],404);
        }
        
    }
}
