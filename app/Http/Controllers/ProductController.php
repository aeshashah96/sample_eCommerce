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
                            'product_id' => 2,
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
}
