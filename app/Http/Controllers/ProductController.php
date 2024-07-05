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
            $product = Product::orderBy('created_at', 'DESC')->with('productReview', 'productImages')->paginate(10,['id','name','price','isActive']);

            foreach ($product as $image) {
                // $image->img =url('/images/product/' . $image->productImages->pluck('image'));
                // dump($img);
                foreach ($image->productImages as $img) {
                    $img->image = url('/images/product/' . $img->image);
                }
                $image->images=$image->productImages->pluck('image');
                $stock=ProductVarient::where('product_id',$image->id)->whereIn('stock',['unlimited'])->get();
                
                if($stock->first()){

                    $image->stock='AVAILABLE';
                }else{
                    $sum=ProductVarient::where('product_id',$image->id)->pluck('stock')->sum();
                    if($sum==0){

                        $image->stock='UNAVAILABLE';
                    }else{
                    $image->stock='AVAILABLE';
                    }

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
                'name' => "required|string|max:30|unique:products,name",
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'slug' => 'required',
                'is_featured' => 'required',
                'long_description' => 'required',
                'image' => 'required',

            ]);
            $varient = [[5, 2, 200], [4, 2, 300], [3, null, 'unlimited']];

            foreach($varient as $varient){
                if($varient[2]==null){
                    return response()->json(['success'=>true,'status'=>422,'message'=>'Please Enter Valid Data']);
                }
            }

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
            $product = Product::find($id)->makeHidden(['productImages','productReview', 'sku']);
            $product->images=$product->productImages->pluck('image');
            $product->avrageRating=$product->productReview->pluck('rating')->avg();
            $colors=$product->colors->pluck('color');
            $product->color=$colors;
            $product->size=$product->sizes->pluck('size');
            $product->categoryName=$product->category->name;
            $product->subcategoryName=$product->subcategory->name;


            $stock=ProductVarient::where('product_id',$product->id)->whereIn('stock',['unlimited'])->get();
                
                if($stock->first()){

                    $product->stock='UNLIMITED';
                }else{
                    $sum=ProductVarient::where('product_id',$product->id)->pluck('stock')->sum();
                    if($sum==0){

                        $product->stock='UNAVAILABLE';
                    }else{
                    $product->stock=$sum;
                    }

                }
        
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

        //for generate varientName
        function generateVarientName($col, $si, $name)
        {
            $color = ProductColor::find($col)->color;
            if ($si != null) {
                $size = ProductSize::find($si)->size;
                 $varient_name = $color . ' ' . $size . ' ' . $name;
                return $varient_name;
            } else {
                $size = null;
                $varient_name = $color . ' ' . $name;
                return $varient_name;
            }
        }
        //dummy data for product varient
        $varientupdated = [[5, 2, 'unlimited'], [4, null, 'unlimited'], [3, null, 'unlimited'],[5,2,22],[3,null,10]];
        $varientNewData = [[5, null, '10']];


        try {
            // validation for new varienrt data
            foreach($varientNewData as $varient){
                if($varient[2]==null && $varient[0]==null){
                    return response()->json(['success'=>true,'status'=>422,'message'=>'Please Enter Valid Data']);
                }
                $check= ProductVarient::where('product_color_id',$varient[0])->where('product_size_id',$varient[1])->first();
                if($check){
                     return response()->json(['success'=>true,'status'=>422,'message'=>'Do not Enter Same Data']);
                }
             }

             //validation for old data
             foreach($varientupdated as $varient){
                if($varient[2]==null && $varient[0]==null){
                    return response()->json(['success'=>true,'status'=>422,'message'=>'Please Enter Valid Data']);
                }
            }
             
            $validatedData = $request->validate([
                'name' => "required|string|max:255",
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'slug' => 'required',
                'is_featured' => 'required',
                'long_description' => 'required'
            ]);


            // dd($request->all());
            //update data in Product table
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
            if ($product) {
                //update Product in Product Description
                $productDescription = ProductDescription::where('product_id', $id)->first()->update(['additional_information' => $request->additional_information, 'description' => $request->description]);;


                //add data for new images enter in product update
                if ($request->hasFile('image')) {

                    $files = $request->file('image');
                    foreach ($files as $file) {
                        $imageName = time() . '' . $file->getClientOriginalName();
                        $file->move(public_path('/images/product'), $imageName);
                        $productImage = ImageProduct::create([
                            'product_id' => $id,
                            'image' => $imageName,
                        ]);
                    }
                }

                //update existing varient table data
                $varientid = ProductVarient::where('product_id', $id)->get();
                foreach ($varientid as $key => $varientid) {

                    $varient_name = generateVarientName($varientupdated[$key][0], $varientupdated[$key][1], $request->name);

                    ProductVarient::find($varientid->id)->update([
                        'product_color_id' => $varientupdated[$key][0],
                        'product_size_id' => $varientupdated[$key][1],
                        'variant_name' => $varient_name,
                        'stock' => $varientupdated[$key][2],
                    ]);
                }

                //for add a new data for product varient table
                foreach ($varientNewData as $col) {
                    $varient_name = generateVarientName($col[0], $col[1], $request->name);
                    $productVarient = ProductVarient::create([
                        'product_id' => $id,
                        'product_color_id' => $col[0],
                        'product_size_id' => $col[1],
                        'variant_name' => $varient_name,
                        'stock' => $col[2],
                    ]);
                }
                if (($productImage != null) && ($productDescription != null) && ($productVarient != null)) {
                    return response()->json(['success' => true, 'status' => 201, 'message' => 'Product Update Successfully']);
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

    public function removeImageOfProduct($id){
        $productImage= ImageProduct::where('image',$id)->first();
        dd($productImage->delete());
    }

    public function list_featured_product(Request $request)
    {
        try {
            $limit = $request->input('limit');
            $productlist = Product::select('id', 'name', 'price')->with('productReview', 'productImages:id,product_id,image')->where('is_featured', 1)->offset(0)->limit(4)->get();
            $productlist = Product::limit($limit)->get()->makeHidden(['productReview', 'created_at', 'updated_at', 'sku', 'is_featured', 'long_description', 'description', 'slug', 'isActive', 'category_id', 'sub_category_id']);

            if ($productlist) {
                $images = array();
                foreach ($productlist as $image) {
                    // $image->productImages[0]->image = url("/images/product/".$image->productImages[0]->image);
                    foreach ($image->productImages as $img) {
                        $img->image = url("/images/product/" . $img->image);
                        // dd($img->image);
                        $images[] =  $img->image;
                    }
                    // dd($images[0]);
                }
                // $productlist->xyz = $images[0];

                $rating = 0;
                $productreview = 0;
                foreach ($productlist as $review) {
                    foreach ($review->productReview as $ele) {
                        $rating = $ele->where('product_id', $review->id)->pluck('rating')->avg();
                        $productreview = $ele->where('product_id', $review->id)->pluck('rating')->count();
                    }
                    $review->avg_rating = $rating;
                    $review->total_review = $productreview;
                    $rating = 0;
                    $productreview = 0;
                }
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Is Feautured Product Get Successfully',
                    'productData' => $productlist,
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

    public function getProduct($id)
    {
        try {
            $productlist = Product::select('id', 'name', 'description', 'price', 'long_description')->with(['colors:id,color', 'sizes:id,size', 'productImages:id,product_id,image', 'productReview:id,product_id,user_id,comment,rating'])->find($id);
            // dd($productlist->productImages);
            foreach ($productlist->productImages as $list) {
                $list->image = url("/images/product/" . $list->image);
            }
            // dd($productlist->productReview);
            $rat = array();
            $total_review = array();
            foreach ($productlist->productReview as $review) {
                // dd($review);
                $rating = $review->where('product_id', $review->id)->pluck('rating')->avg();
                $rat[] = $rating;
                // dd($rating);
                $final_review = $review->where('product_id', $review->id)->pluck('rating')->count();
                // $productlist->total_review = $total_review;
                $total_review[] = $final_review;
            }
            if (!empty($rat) && !empty($total_review)) {
                $productlist->avg_rate = $rat[0];
                $productlist->total_review = $total_review[0];
            } else {
                $productlist->avg_rate = 0;
                $productlist->total_review = 0;
            }
            // dd($productlist);

            if ($productlist) {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Get Successfully',
                    'product' => $productlist
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
