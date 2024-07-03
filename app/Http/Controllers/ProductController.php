<?php

namespace App\Http\Controllers;

use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductDescription;
use App\Models\ProductSize;
use App\Models\ProductVarient;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        //
        try {
            $product = Product::orderBy('created_at', 'DESC')->with('productReview','productImages')->paginate(10)->makeHidden(['productReview','description', 'category_id', 'sub_category_id', 'sku', 'slug', 'is_featured', 'long_description']);
       
            foreach ($product as $image) {
                foreach ($image->productImages as $img) {
                    $img->image = url('/images/product/' . $img->image);
                }
            }
            $rating = 0;
            foreach ($product as $review) {
                foreach ($review->productReview as $ele) {
                    $rating = $ele
                        ->where('product_id', $review->id)
                        ->pluck('rating')
                        ->avg();
                }
                $review->avg_rating = $rating;
                $rating = 0;
            }
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Get Successfully', 'Product Data' => $product]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => "required|string|max:255",
                'category_id' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required',
                'slug' => 'required',
                'is_featured' => 'required',
                'long_description' => 'required',
                'image'=>'required',

            ]);
            $varient = [[5, 2, 500], [4, 2, 300], [3, null, 'unlimited']];

            // dd($request->all());
            $randomString = fake()->regexify('[A-Z0-9]{10}');
            //add Product in Product 
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sku' => $randomString,
                'isActive' => 1,
                'slug' => $request->slug,
                'is_featured' => $request->is_featured,
                'long_description' => $request->long_description,
            ]);
            if ($product) {
                //add Product in Product Description
                $productDescription = ProductDescription::create(['additional_information' => $request->additional_information, 'product_id' => $product->id, 'description' => $product->description]);

                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    foreach ($files as $file) {
                        $imageName = time() . '' . $file->getClientOriginalName();
                        $file->move(public_path('/images/product'), $imageName);
                        $productImage = ImageProduct::create([
                            'product_id' => $product->id,
                            'image' => $imageName,
                        ]);
                    }
                }

                //for add in Product in ProductVarient
                foreach ($varient as $col) {

                    $color = ProductColor::find($col[0])->color;
                    if ($col[1] != null) {
                        $size = ProductSize::find($col[1])->size;
                        $varient_name = $color . ' ' . $size . ' ' . $request->name;
                    } else {
                        $size = null;
                        $varient_name = $color . ' ' . $request->name;
                    }
                    $productVarient = ProductVarient::create([
                        'product_id' => $product->id,
                        'product_color_id' => $col[0],
                        'product_size_id' => $col[1],
                        'variant_name' => $varient_name,
                        'stock' => $col[2],
                    ]);
                }
                if (($productImage != null) && ($productDescription != null) && ($productVarient != null)) {
                    return response()->json(['success' => true, 'status' => 201, 'message' => 'Product Add Successfully']);
                } else {
                    return response()->json(['success' => true, 'status' => 422, 'message' => 'Some Error Found']);
                }
            } else {
                return response()->json(['success' => false, 'status' => 201, 'message' => 'Error']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $product = Product::find($id);
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Get Successfully', 'Product Data' => $product]);
        } catch (Exception $e) {
            return response()->json(['success' => true, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try{
            $validatedData = $request->validate([
                'name' => "required|string|max:255",
                'category_id' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required',
                'slug' => 'required',
                'is_featured' => 'required',
                'long_description' => 'required'
            ]);
            $varient = [[5, 2, 500], [4, 2, 300], [3, null, 'unlimited']];
            
            // dd($request->all());
            //add Product in Product 
            $product = Product::find($id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'slug' => $request->slug,
                'is_featured' => $request->is_featured,
                'long_description' => $request->long_description,
            ]);
            // dd($product);
            if ($product) {
            //add Product in Product Description
dump($id);
            $productDescription = ProductDescription::where('product_id',$id)->first()->update(['additional_information' => $request->additional_information, 'description' => $request->description]);;
           
       if ($request->hasFile('image')) {
                $image=ImageProduct::where('product_id',$id)->get();
                foreach($image as $img){
                    unlink(public_path('images/product/'.$img->image));
                    $img->delete();
                }
                // dd($image);
                $files = $request->file('image');
                foreach ($files as $file) {
                    $imageName = time() . '' . $file->getClientOriginalName();
                    $file->move(public_path('/images/product'), $imageName);
                    $productImage = ImageProduct::create([
                        'product_id'=>$id,
                        'image' => $imageName,
                    ]);
                }
            }

            //for add in Product in ProductVarient
            foreach ($varient as $col) {

                $color = ProductColor::find($col[0])->color;
                if ($col[1] != null) {
                    $size = ProductSize::find($col[1])->size;
                    $varient_name = $color . ' ' . $size . ' ' . $request->name;
                } else {
                    $size = null;
                    $varient_name = $color . ' ' . $request->name;
                }
                $productVarient = ProductVarient::create([
                    'product_color_id' => $col[0],
                    'product_size_id' => $col[1],
                    'variant_name' => $varient_name,
                    'stock' => $col[2],
                ]);
            }
            if (($productImage != null) && ($productDescription != null) && ($productVarient != null)) {
                return response()->json(['success' => true, 'status' => 201, 'message' => 'Product Add Successfully']);
            } else {
                return response()->json(['success' => true, 'status' => 422, 'message' => 'Some Error Found']);
            }
        } else {
            return response()->json(['success' => false, 'status' => 201, 'message' => 'Error']);
        }
    } catch (Exception $e) {
        return response()->json(['status' => $e->getCode(), 'message' => $e->getMessage()]);
    }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $product = Product::find($id);
        if ($product) {

            $productImage = ImageProduct::where('product_id', $product->id)->delete();
            $productVarient = ProductVarient::where('product_id', $product->id)->delete();
            $productDescription = ProductDescription::where('product_id', $product->id)->delete();
            $product->delete();
            if (($productImage != null) && ($productDescription != null) && ($productVarient != null)) {
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Delete Successfully']);
            } else {
                return response()->json(['success' => true, 'status' => 422, 'message' => 'Some Error Found']);
            }
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Product Not Found']);
        }
    }

    public function list_featured_product(){
        try{
            $productlist = Product::select('id','name','price')->with('productReview:id,user_id,product_id,rating','productImages:id,product_id,image')->where('is_featured',1)->offset(0)->limit(8)->get();
            if($productlist){
                $productlist = $productlist->makeHidden('productReview');
                
                foreach($productlist as $image){
                    // $image->productImages[0]->image = url("/images/product/".$image->productImages[0]->image);
                    foreach($image->productImages as $img){
                        $img->image=url("/images/product/".$img->image);
                    }
                }
                $rating = 0;
                $productreview = 0;
                foreach($productlist as $review){
                    foreach($review->productReview as $ele){
                        $rating = $ele->where('product_id',$review->id)->pluck('rating')->avg();
                        $productreview = $ele->where('product_id',$review->id)->pluck('rating')->count();
                    }
                   $review->avg_rating = $rating;
                   $review->total_review = $productreview;
                   $rating=0;
                   $productreview=0;
                }
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'Is Feautured Product Get Successfully',
                    'productData'=>$productlist,
                ]);
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'status' => 404,
                        'message' => 'Is Feautured Product Not Found',
                        'productDetails' => $productlist,
                    ],
                    404,
                );
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getProduct($id){
        try{
            $productlist = Product::select('id','name','description','price','long_description')->with(['colors:id,color','sizes:id,size','productImages:id,product_id,image','productReview'])->findOrFail($id);
            // dd($productlist->productImages);
            foreach($productlist->productImages as $list){
                $list->image = url("/images/product/".$list->image);
            }
            // dd($productlist->productReview);
            foreach($productlist->productReview as $review){
                dd($review);
            }
            if($productlist){
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'Product Get Successfully',
                    'product'=>$productlist
                ],200);
            }
        }
        catch(Exception $e){
            return response()->json([
                'success'=>false,
                'status'=>$e->getCode(),
                'message'=>$e->getMessage()
            ]);
        }
       

    }
}
