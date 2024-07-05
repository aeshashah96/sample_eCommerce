<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageRequest;
use App\Http\Requests\LanguageUpdate;
use App\Models\Language;
use Exception;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        try{
            $data = Language::orderBy('created_at','DESC')->paginate(10,['id','language_name',
        'language_code',
        'status']);
            return response()->json([
                'success'=>true,
                'status'=>200,
                'data'=>$data
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
    public function store(LanguageRequest $request)
    {
        try{
            Language::create($request->input());
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'record created successfuly'
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $user = Language::find($id,['id','language_name',
        'language_code',
        'status']);
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
    public function update(Request $request, string $id)
    {   
        try{
            $validatedData = $request->validate([
                'language_name' => 'required',
                'language_code' => 'required',
            ]);
            $item = Language::find($id);
            if($item){
                $item->update($request->input());
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'record updated successfully'
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
            $item = Language::find($id);
            if(!$item){
                return response()->json([
                    'success'=>false,
                    'status'=>404,
                    'message'=>'record not found'
                ]);
            }
            $item->delete();
            return response()->json([
                'success'=>true,
                'status'=>200,
                'message'=>'language deleted successfully'
            ],200);
        }catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
        
    }
    public function changeStatus($id){
        $language=Language::find($id);

        if($language){
            if($language->status){
                // dd($id);
                $language->status=0;
                $language->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Language Status Change Successfully']);
            }else{
                $language->status=1;
                $language->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Language Status Change Successfully']);
            }
        }else{
            return response()->json(['success'=>false,'status'=>404,'message'=>'Language Not Found']);
        }
    }
}
