<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\SubCategories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $category = Categories::orderBy('created_at', 'DESC')->with('subCategories')->paginate(10);

        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        return response()->json(['success' => true, 'code' => 200, 'Categories' => $category], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'required',
                'category_image' => 'required|image',
            ]);
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $category = Categories::create(['name' => $request->name, 'description' => $request->description, 'category_image' => $imageName, 'is_Active' => true]);
            if ($category) {
                $image->move(public_path('/images/Categories'), $imageName);
                return response()->json(['success' => true, 'code' => 201, 'message' => 'Category Add Successfully'], 201);
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
            $category = Categories::with('subCategories')->where('id', $id)->first();
            if ($category) {
                $category->category_image = url('/images/Categories/' . $category->category_image);
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Category get Successfully', 'category' => $category]);
            } else {
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Category Not Found'], 404);
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

            $validatedData = Validator::make($request->all(), [

                'name' => 'required|string|max:255',
                'description' => 'required',
                'category_image' => 'image',
            ]);
            $category = Categories::find($id);
            $image = $request->file('category_image');
            if ($image) {
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                unlink(public_path('/images/Categories/' . $category->category_image));
                $image->move(public_path('/images/Categories'), $imageName);

                if ($category) {
                    $category->update(['name' => $request->name, 'description' => $request->description, 'category_image' => $imageName]);
                    return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['success' => false, 'code' => 404, 'message' => 'Category Not Found'], 404);
                }
            } else {
                if ($category) {
                    $category->update(['name' => $request->name, 'description' => $request->description]);
                    return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['success' => false, 'code' => 404, 'message' => 'Category Not Found'], 404);
                }
            }
            $category->save();
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
            $category = Categories::find($id);
            if ($category) {
                unlink(public_path('/images/Categories/' . $category->category_image));
                SubCategories::where('category_id', $id)->delete();
                $category->delete();
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Delete Successfully']);
            } else {
                return response()->json(['success' => false, 'code' => 404, 'message' => 'Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchCategory(Request $request)
    {
        $category = Categories::where('name', 'LIKE', '%' . $request->name . '%')->paginate(10);
        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        if ($category) {
            return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Get Successfully', 'Categories' => $category]);
        } else {
            return response()->json(['success' => false, 'code' => 404, 'message' => 'Category Not Found'], 404);
        }
    }
        
    public function createCategoreis(Request $request)
    {
        try {
            $categoryimage = time() . '.' . $request->file('category_image')->getClientOriginalExtension();
            $request->category_image->move(public_path('images/category'), $categoryimage);
            $imagename = url("/images/category/$categoryimage");
            $category = Categories::create([
                "name" => $request->name,
                "description" => $request->description,
                "category_image" => $imagename
            ]);
            return response()->json([
                'success' => true,
                'category' => $category
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'succsess' => false,
                'message' => 'Category is not get',
                'error' => $e
            ]);
        }
    }

    // get category list for front end side 
    public function listCategory()
    {
        try {
            $category = Categories::orderBy('id','DESC')->all();
            foreach($category as $cat){
                $cat['category_image'] = url("/images/category/ ".$cat->category_image);
            }
            $subcategory = Categories::select('id','name')->with('subCategory')->get();
            foreach($subcategory as $sub){
                $sub['category_image'] = url("/images/category/ ".$sub->category_image);
            }
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'category'=>$category,
                    'sub_category'=>$subcategory,
                    'message'=>'Category show successfully '
                ],200);
            
        } catch (Exception $e) {
            return response()->json([
                'code'=>$e->getCode(),
                'message'=>$e->getMessage()
            ]);
        }
    }
}
