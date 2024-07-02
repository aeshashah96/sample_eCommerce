<?php

namespace App\Http\Controllers;

use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductDescription;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        //
        try {
            $product =  Product::get();
            return response()->json(['success' => true, 'message' => 'Product Get Successfully', 'product' => $product]);
        } catch (Exception $e) {
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            $randomString = fake()->regexify('[A-Z0-9]{10}');
            // dd($randomString);
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'sku' => $randomString,
                'isActive' => true,
                'slug' => $request->slug,
                'is_featured' => $request->is_featured,
                'long_description' => $request->long_description,
            ]);
            if ($product) {

                ProductDescription::create(['additional_information'=>$request->additional_information,
                'product_id'=>$product->id,
                'description'=>$product->description,
            ]);

                if ($request->hasFile('image')) {
                    
                    $files=$request->file('image');
                    foreach ($files as $file) {
                        $imageName = time() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('/images/product'), $imageName);
                        ImageProduct::create([
                            'product_id' => $product->id,
                            'image' => $imageName,
                        ]);
                    }
                    
                    return response()->json(['success' => true,'code'=>201, 'message' => 'Product Add Successfully']);
                }
            } else {
                return response()->json(['success' => true, 'message' => 'Error']);
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
        try{

            $product=Product::find($id);

        }catch(Exception $e){
            return response()->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }



    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function list_featured_product(){
        try{
            $productlist = Product::select('id','name','price')->with('productReview','productImages')->where('is_featured',1)->get();
            if($productlist){
                foreach($productlist as $image){
                    foreach($image->productImages as $img){
                        $img->image=url("/images/product/".$img->image);
                    }
                }
                $rating = 0;
                foreach($productlist as $review){
                    foreach($review->productReview as $ele){
                        $rating = $ele->where('product_id',$review->id)->pluck('rating')->avg();
                    }
                   $review->avg_rating = $rating;
                   $rating=0;
                }
                return response()->json([
                    'success'=>true,
                    'status'=>200,
                    'message'=>'Is Feautured Product Get Successfully',
                    'productDetails'=>$productlist,
                ]);
            }
            else{
                return response()->json([
                    'success'=>true,
                    'status'=>404,
                    'message'=>'Is Feautured Product Not Found',
                    'productDetails'=>$productlist,
                ],404);
            }
            
        }
        catch(Exception $e){
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
