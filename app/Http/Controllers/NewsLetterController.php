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
                'success'=>true,
                'status'=>200,
                'data' => $data
            ], 200);
        } catch (Exception $e) {
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
            $item = NewsLetter::find($id);
            if($item){
                $item->delete();
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message' => 'record deleted successfully'
                ], 200);
            }else{
                return response()->json([
                    'success'=>false,
                    'status'=>404,
                    'message' => 'record not found'
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

    public function addNewsLetter(NewsLatterRequest $request)
    {
        try {
            $newsletter = NewsLetter::create($request->input());
            if ($newsletter) {
                // SendEmailNewsPaper::dispatch($newsletter);
                return response()->json([
                    'success' => true,
                    'status' => 201,
                    'message' => 'NewsLetter Add Successfully',
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'mesasge' => 'NewsLetter Not Added'
                ]);
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
