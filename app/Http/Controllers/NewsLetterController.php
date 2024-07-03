<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsLatterRequest;
use App\Jobs\SendEmailNewsPaper;
use App\Models\NewsLetter;
use Exception;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       
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
