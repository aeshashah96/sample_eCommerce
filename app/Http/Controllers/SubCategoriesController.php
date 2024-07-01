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
        $subcategory = SubCategories::orderBy('created_at','DESC')->with('Categories')->paginate(10);

        return response()->json(['code' => 200, 'sub_category' => $subcategory], 200);
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
            $cat=SubCategories::where('name',$request->name);
            if($cat){
                if($cat->first()!=null){
                    if($cat->first()->category_id== $request->category_id){
                    return response()->json(['success' => false, 'code' => 0, 'message' => 'do not enter same sub category']);
                    }
                }
            }

            $subcategory =  SubCategories::create(['name' => $request->name,'category_id'=>$request->category_id]);
            if ($subcategory) {
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Sub Category Add Successfully'], 201);
            } else {
                return response()->json(['success' => false, 'code' => 500, 'message' => 'Error Found'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $subcategory = SubCategories::with('Categories')->where('id',$id)->first();
            if ($subcategory) {
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Sub Category get Successfully', 'sub_category' => $subcategory]);
            } else {
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Sub Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
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
            $cat=SubCategories::where('name',$request->name);
            if($cat){
                if($cat->first()!=null){
                    if($cat->first()->category_id== $request->category_id){
                    return response()->json(['success' => false, 'code' => 0, 'message' => 'do not enter same sub category']);
                    }
                }
            }
            $subcategory = SubCategories::find($id);

                if ($subcategory) {
                    $subcategory->update(['name' => $request->name,'category_id'=>$request->category_id]);
                    return response()->json(['success' => true, 'code' => 200, 'message' => 'Sub Category Update Successfully']);
                } else {
                    return response()->json(['success' => false,'code' => 404, 'message' => 'Sub Category Not Found'], 404);
                }
           
            $subcategory->save();
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
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
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Sub Category Delete Successfully']);
            } else {
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Sub Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchSubCategory(Request $request)
    {
        $subcategory = SubCategories::orderBy('created_at','DESC')->where('name', 'LIKE', '%' . $request->name . '%')->paginate(10);
        if ($subcategory) {
            return response()->json(['success' => true, 'code' => 200, 'message' => 'Sub Category Get Successfully', 'sub_category' => $subcategory]);
        } else {
            return response()->json(['success' => false, 'code' => 200, 'message' => 'Error Found'], 200);
        }
    }
    public function listSubCategory(){
        try {
            $subcategory = SubCategories::orderBy('id','DESC')->get();
            if ($subcategory){
                return response()->json([
                    'success' => true,
                    'status'=>200,
                    'sub_category' => $subcategory,
                    'message'=>'sub category show successfully'
                ],200);
            }
            else{
                return response()->json([
                    'succsess' => false,
                    'status'=>200,
                    'message' => 'sub category is not Found',
                ],200);
            }
            
        } catch (Exception $e) {
            return response()->json([
                'code'=>$e->getCode(),
                'message'=>$e->getMessage()
            ],200);
        }
    }
}
