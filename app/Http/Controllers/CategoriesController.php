<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SubCategories;
use App\Models\Wishlists;
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
        $category = Categories::orderBy('created_at', 'DESC')->with('subCategory:id,name,category_id')->paginate(10);

        if($category){

            foreach ($category as $cat) {
                $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
            }
            return response()->json(['success' => true, 'status' => 200,'message'=>'Category Get Successfully', 'data' => $category]);
        }else{
            return response()->json(['success' => false, 'status' => 404, 'message' =>'Category Not Found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:15|unique:categories,name',
                'category_image' => 'required|image',
            ]);
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $category = Categories::create(['name' => $request->name, 'description' => 'null', 'category_image' => $imageName, 'is_Active' => 1, 'category_slug' => Str::slug($request->name)]);
            if ($category) {
                $image->move(public_path('/images/Categories'), $imageName);
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Category Add Successfully']);
            } else {
                return response()->json(['success' => false, 'status' => 500, 'message' => 'Error Found']);
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
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Category get Successfully', 'data' => $category]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found']);
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

                'name' => 'required|string|max:15',
                'category_image' => 'image',
            ]);
            $category = Categories::find($id);
            
            
            if ($category) {
                    if ($request->hasFile('category_image')) {
                        $image = $request->file('category_image');
                        $imageName = time() . '.' . $image->getClientOriginalExtension();
                        unlink(public_path('/images/Categories/' . $category->category_image));
                        $image->move(public_path('/images/Categories'), $imageName);
                    $category->update(['name' => $request->name, 'description' => 'null', 'category_image' => $imageName]);
                    return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Update Successfully']);
                } else {
                    $category->update(['name' => $request->name, 'description' => 'null']);
                    return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Update Successfully']);
                    
                }
            } else {
                
                    return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found']);
            
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
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found']);
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    public function SearchCategory(Request $request)
    {
        $category = Categories::where('name', 'LIKE', $request->name . '%')->paginate(10);
        foreach ($category as $cat) {
            $cat['category_image'] = url('/images/Categories/' . $cat['category_image']);
        }
        if ($category) {
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Get Successfully', 'data' => $category]);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found']);
        }
    }

    public function changeActiveStatus($id){
        $category=Categories::find($id);
        if($category){

            if($category->is_Active){
                // dd($id);
                $category->is_Active=0;
                $category->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Status Change Successfully']);
            }else{
                $category->is_Active=1;
                $category->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Category Status Change Successfully']);
            }
        }else{
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Category Not Found']);
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
                    'status'=>201,
                    'category' => $category
                ], 201);
            }
            else{
                return response()->json([
                    'success' => false,
                    'status'=>404,
                    'message' => 'Category is not Added'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'succsess' => false,
                'status'=>$e->getCode(),
                'message'=>$e->getMessage()
            ]);
        }
    }

    // get category list for front end side 

    public function listCategory()
    {
        try {
            $CategoryWithSubcategory = Categories::select('id', 'name', 'description', 'category_image','category_slug')->with('subcategory:id,category_id,name','products')->orderBy('id', 'DESC')->get();
            if ($CategoryWithSubcategory) {
                foreach ($CategoryWithSubcategory as $sub) {
                    $sub['category_image'] = url("/images/Categories/" . $sub->category_image);
                }
                foreach($CategoryWithSubcategory as $cat){
                    $cat->total_products = $cat->products->where('category_id',$cat->id)->count();
                }
                $CategoryWithSubcategory =  $CategoryWithSubcategory->makeHidden('products');
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Category Get successfully',
                    'data' => $CategoryWithSubcategory,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Category Not Found'
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

    public function getProductBasedCategory($id){
        try {
            $user = auth()->guard('api')->user();
            $categoryId = Categories::find($id);
            if($categoryId){
                $product = Product::where('category_id',$id)->get();
                if($product->first()){
                    if ($user) {
                        foreach ($product as $ele) {
                            $wishlistProduct = Wishlists::where('user_id', $user->id)
                                ->where('product_id', $ele->id)
                                ->first();
                            if ($wishlistProduct) {
                                $ele->isWishlist = 1;
                            } else {
                                $ele->isWishlist = 0;
                            }
                        }
                    } else {
                        foreach ($product as $ele) {
                            $ele->isWishlist = 0;
                        }
                    }
                    foreach($product as $element){
                        $productReview = ProductReview::where('product_id',$element->id)->pluck('rating');
                        $productImg = ImageProduct::where('product_id',$element->id)->pluck('image')->first();
                        $ratingAverage = $productReview->avg();
                        $totalReview = $productReview->count();
                        if (is_null($ratingAverage)) {
                            $ratingAverage = 0;
                        }
                        $element->product_images = url("/images/product/$productImg");
                        $element->avg_rating = number_format((float)$ratingAverage, 2, '.', '');
                        $element->total_review = $totalReview;
                    }
                    return response()->json([
                        'success' => true,
                        'status' => 200,
                        'message' => 'Product Found',
                        'data' => $product,
                    ]);
                }
                else{
                    return response()->json([
                        'success' => false,
                        'status' => 404,
                        'message' => 'Product Not Found'
                    ]);
                }
            }
            else{
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Category Not Found'
                ]);
            }
        } 
        catch (Exception $e) {
            return response()->json([
                'success'=>false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }

    }

}
