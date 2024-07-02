<?php

namespace App\Http\Controllers;

use App\Models\SubCategories;
use Exception;
use Illuminate\Http\Request;

class SubCategoriesController extends Controller
{
    //

    public function index()
    {
        //
        $subcategory = SubCategories::orderBy('created_at','DESC')->with('category')->paginate(10);

        
        return response()->json(['success' => true,'status' => 200, 'sub_category' => $subcategory], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
            $validatedData = $request->validate([
                'name' => "required|string|max:255",
                'category_id'=>'required'
            ]);
            $cat=SubCategories::where('category_id',$request->category_id)->where('name',$request->name)->first();
            if($cat != null){

                        return response()->json(['success' => false, 'status' => 0, 'message' => 'do not enter same sub category']);
                      
            }

            $subcategory =  SubCategories::create(['name' => $request->name,'category_id'=>$request->category_id]);
            if ($subcategory) {
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Sub Category Add Successfully'], 201);
            } else {
                return response()->json(['success' => false, 'status' => 500, 'message' => 'Error Found'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $subcategory = SubCategories::with('category')->where('id',$id)->first();
            if ($subcategory) {
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Sub Category get Successfully', 'sub_category' => $subcategory]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Sub Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id'=>'required',
            ]);
            $cat=SubCategories::where('category_id',$request->category_id)->where('name',$request->name)->first();
            if($cat != null){

                        return response()->json(['success' => false, 'status' => 0, 'message' => 'do not enter same sub category']);
                      
            }
            $subcategory = SubCategories::find($id);

                if ($subcategory) {
                    $subcategory->update(['name' => $request->name,'category_id'=>$request->category_id]);
                    return response()->json(['success' => true, 'status' => 200, 'message' => 'Sub Category Update Successfully']);
                } else {
                    return response()->json(['success' => false,'status' => 404, 'message' => 'Sub Category Not Found'], 404);
                }
           
            $subcategory->save();
        } catch (Exception $e) {
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $subcategory = SubCategories::find($id);
            if ($subcategory) {
                $subcategory->delete();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Sub Category Delete Successfully']);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Sub Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false,'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchSubCategory(Request $request)
    {
        $subcategory = SubCategories::orderBy('created_at','DESC')->where('name', 'LIKE', '%' . $request->name . '%')->paginate(10);
        if ($subcategory) {
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Sub Category Get Successfully', 'sub_category' => $subcategory]);
        } else {
            return response()->json(['success' => false, 'status' => 200, 'message' => 'Error Found'], 200);
        }
    }
    public function listSubCategory(){
        try {
            $subcategory = SubCategories::select('id','category_id','name')->orderBy('id','DESC')->get();
            if ($subcategory){
                return response()->json([
                    'success' => true,
                    'status'=>200,
                    'sub_category' => $subcategory,
                    'message'=>'Sub Category Show Successfully'
                ],200);
            }
            else{
                return response()->json([
                    'succsess' => false,
                    'status'=>404,
                    'message' => 'Sub Category Not Found',
                ],404);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'code'=>$e->getCode(),
                'message'=>$e->getMessage()
            ],200);
        }
    }
}
