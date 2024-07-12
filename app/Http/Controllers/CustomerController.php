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
            $datas = User::orderBy('created_at','desc')->paginate(10);
           foreach($datas as $data){
            // dump($data);
            $data->user_logo=url('images/users/'.$data->user_logo);
           }

            return response()->json([
                'success'=>true,
                'status'=>200,
                'data'=>$datas
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {   
        try{
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
                    'success'=>true,
                    'status' => 200,
                    'message' => 'customer added successfully',
                ],200);
            }else {
                return response()->json([
                    'success'=>false,
                    'status' => 503,
                    'message' => 'Customer Not Register',
                ],503);
            }    
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        try{
            $user = User::find($id);
            $user->user_logo=url('images/users/'.$user->user_logo);
            if($user){
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'data'=>$user
                ],200);
            }else{
                return response()->json([
                    'success'=>false,
                    'status'=>404,
                    'message'=>'record not found'
                ]);
            }
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
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
                    'success'=>false,
                    'status'=>404,
                    'message'=>'Record not found'
                ]);    
            }
            if($request->has('user_logo')){
                if($item->user_logo && ($item->user_logo != 'userLogo.png')){
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
                'success'=>true,
                'status'=>200,
                'message'=>'User Record Updated'
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
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
                    'success'=>false,
                    'status'=>404,
                    'message'=>'record not found'
                ]);    
            }
            if($item->user_logo != 'userLogo.png'){
                $name = $item->user_logo;
                 $image_path = "images/users/$name";
                 unlink($image_path);
            }
            $item->delete();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'deleted record successfully'
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }
}
