<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsLatterRequest;
use App\Jobs\SendEmailNewsPaper;
use App\Models\NewsLetter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = NewsLetter::paginate(10);
            return response()->json([
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'error' => $e
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsLatterRequest $request)
    {
        try {
            NewsLetter::create($request->input());
            return response()->json([
                'code' => 200,
                'message' => 'record created sucessfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'error' => $e
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = NewsLetter::find($id);
            return response()->json([
                'code' => 200,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'error' => $e
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $item = NewsLetter::find($id);
            if(!$item){
               return response()->json([
                'code'=>404,
                'message'=>'record not found'
               ],404);
            }else{
                $item->update($request->input());
                return response()->json([
                    'code' => 200,
                    'message' => 'record updated successfully'
                ], 200);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'error' => $e
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            NewsLetter::find($id)->delete();
            return response()->json([
                'code' => 200,
                'message' => 'record deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 404,
                'error' => $e
            ], 404);
        }
    }

    public function addNewsLetter(NewsLatterRequest $request)
    {
        try {
            $newsletter = NewsLetter::create($request->input());
            if ($newsletter) {
                SendEmailNewsPaper::dispatch($newsletter);
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'message' => 'News Letter Add Successfully',
                    'newsletter' => $newsletter
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'mesasge' => 'News Letter Not Added'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
