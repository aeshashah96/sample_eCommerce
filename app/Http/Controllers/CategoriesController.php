<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\SubCategories;
use Exception;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $category = Categories::with('subCategories')->get();

        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        return response()->json(['code' => 200, 'Categories' => $category], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required',
                'category_image' => 'required|image',
            ]);
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $category = Categories::create(['name' => $request->name, 'description' => $request->description, 'category_image' => $imageName, 'is_Active' => true]);
            if ($category) {
                $image->move(public_path('/images/Categories'), $imageName);
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Add Successfully'], 200);
            } else {
                return response()->json(['success' => true, 'code' => 503, 'message' => 'Error Found'], 503);
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
            $category->category_image = url('/images/Categories/' . $category->category_image);
            if ($category) {
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Category get Successfully', 'category' => $category]);
            } else {
                return response()->json(['success' => true, 'code' => 503, 'message' => 'Error Found'], 503);
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
                    $category->update(['success' => true, 'name' => $request->name, 'description' => $request->description, 'category_image' => $imageName]);
                    return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['code' => 503, 'message' => 'Error Found'], 503);
                }
            } else {
                if ($category) {
                    $category->update(['name' => $request->name, 'description' => $request->description]);
                    return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    return response()->json(['success' => true, 'code' => 503, 'message' => 'Error Found'], 503);
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
                return response()->json(['success' => true, 'code' => 200, 'message' => 'Error Found'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchCategory(Request $request)
    {
        $category = Categories::where('name', 'LIKE', '%' . $request->name . '%')->get();
        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        if ($category) {
            return response()->json(['success' => true, 'code' => 200, 'message' => 'Category Get Successfully', 'Categories' => $category]);
        } else {
            return response()->json(['success' => true, 'code' => 200, 'message' => 'Error Found'], 200);
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

    public function showCategory()
    {
        try {
            $category = Categories::all();
            if($category){
                return response()->json([
                    'success'=>true,
                    'category'=>$category,
                    'message'=>'Category show successfully'
                ],200);
            }
        } catch (Exception $e) {
            return response()->json([
                'succsess' => false,
                'message' => 'Category is not get',
                'error' => $e
            ]);
        }
    }
}
