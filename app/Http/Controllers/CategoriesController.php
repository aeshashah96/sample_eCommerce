<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\SubCategories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $category = Categories::orderBy('created_at', 'DESC')->with('subCategory')->paginate(10);

        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        return response()->json(['success' => true, 'status' => 200, 'Categories' => $category], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'required',
                'category_image' => 'required|image',
            ]);
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $category = Categories::create(['name' => $request->name, 'description' => $request->description, 'category_image' => $imageName, 'is_Active' => true, 'category_slug' => Str::slug($request->name)]);
            if ($category) {
                $image->move(public_path('/images/Categories'), $imageName);
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Category Add Successfully'], 201);
            } else {
                return response()->json(['success' => false, 'status' => 500, 'message' => 'Error Found'], 500);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'status' => 422, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $category = Categories::with('subCategory')->where('id', $id)->first();
            if ($category) {
                $category->category_image = url('/images/Categories/' . $category->category_image);
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Category get Successfully', 'category' => $category]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
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
                    return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found'], 404);
                }
            } else {
                if ($category) {
                    $category->update(['name' => $request->name, 'description' => $request->description]);
                    return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found'], 404);
                }
            }
            $category->save();
        } catch (Exception $e) {
            return response()->json(['success' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()]);
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
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Delete Successfully']);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchCategory(Request $request)
    {
        $category = Categories::where('name', 'LIKE', '%' . $request->name . '%')->paginate(10);
        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        if ($category) {
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Get Successfully', 'Categories' => $category]);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found'], 404);
        }
    }

    public function addCategory(Request $request)
    {
        try {
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $category = Categories::create([
                'name' => $request->name, 'description' => $request->description, 'category_image' => $imageName, 'is_Active' => true, 'category_slug'=> Str::slug($request->name)
            ]);
            if($category){
                $image->move(public_path('/images/Categories'), $imageName);
                return response()->json([
                    'success' => true,
                    'category' => $category
                ], 200);
            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Category is not Added'
                ], 200);
            }
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

            $CategoryWithSubcategory = Categories::select('id', 'name', 'description', 'category_image','category_slug')->with('subcategory:id,category_id,name')->orderBy('id', 'DESC')->get();
            if ($CategoryWithSubcategory) {
                foreach ($CategoryWithSubcategory as $sub) {
                    $sub['category_image'] = url("/images/Categories/" . $sub->category_image);
                }
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'categoryData' => $CategoryWithSubcategory,
                    'message' => 'Category Show successfully'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Category Not Found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
