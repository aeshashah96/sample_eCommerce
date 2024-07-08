<?php

namespace App\Http\Controllers;

use App\Http\Requests\addProductReviewRequest;
use App\Models\ImageProduct;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductDescription;
use App\Models\ProductReview;
use App\Models\ProductSize;
use App\Models\ProductVarient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        //
        try {
            $product = Product::orderBy('created_at', 'DESC')
                ->with('productReview', 'productImages')
                ->paginate(10, ['id', 'name', 'price', 'isActive']);

            foreach ($product as $image) {
                foreach ($image->productImages as $img) {
                    $img->image = url('/images/product/' . $img->image);
                }
                // $image->images = $image->productImages->pluck('image');
                $stock = ProductVarient::where('product_id', $image->id)
                    ->whereIn('stock', ['unlimited'])
                    ->get();

                if ($stock->first()) {
                    $image->stock = 'AVAILABLE';
                } else {
                    $sum = ProductVarient::where('product_id', $image->id)
                        ->pluck('stock')
                        ->sum();
                    if ($sum == 0) {
                        $image->stock = 'UNAVAILABLE';
                    } else {
                        $image->stock = 'AVAILABLE';
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
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Get Successfully', 'data' => $product]);
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
            // $varient = [[1, null, 20], [5, null, 30], [3, null, 50]];
            // $varient = [[4, 2, 100], [5, 3, 500], [3, 2, 5]];

            $validatedData = $request->validate([
                'name' => 'required|string|max:30|unique:products,name',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'is_featured' => 'required',
                'long_description' => 'required',
                'image' => 'required',
                'varient' => 'required',
            ]);

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
                'slug' => Str::slug($request->name, '-'),
                'is_featured' => $request->is_featured,
                'long_description' => $request->long_description,
            ]);
            if ($product) {
                //for add in Product in ProductVarient

                foreach ($request->varient as $col) {
                    $color = ProductColor::find($col['color']);

                    if ($color) {
                        if ($col['size'] != null) {
                            $size = ProductSize::find($col['size'])->size;
                            $varient_name = $color->color . ' ' . $size . ' ' . $request->name;
                        } else {
                            $size = null;
                            $varient_name = $color->color . ' ' . $request->name;
                        }
                        $productVarient = ProductVarient::create([
                            'product_id' => $product->id,
                            'product_color_id' => $col['color'],
                            'product_size_id' => $col['size'],
                            'variant_name' => $varient_name,
                            'stock' => $col['stock'],
                        ]);
                        $varient_name = '';
                    } else {
                        Product::find($product->id)->delete();
                        return response()->json(['success' => false, 'status' => 422, 'message' => 'Please Enter Valid Data']);
                    }
                }
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

                // dd($varient);

                if ($productImage != null && $productDescription != null && $productVarient != null) {
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
            if ($product) {
                $product->makeHidden(['productImages', 'productReview', 'sku']);
                $img = $product->productImages->pluck('image');
                $product->productInformation->additional_information;
                $product->avrageRating = $product->productReview->pluck('rating')->avg();
                $colors = $product->colors->pluck('color');
                $product->color = $colors;
                $product->size = $product->sizes->pluck('size');
                $product->categoryName = $product->category->name;
                $product->subcategoryName = $product->subcategory->name;
                foreach ($img as $key => $imgs) {
                    $img[$key] = url('images/product/' . $imgs);
                }
                $product->images = $img;
                $stock = ProductVarient::where('product_id', $product->id)
                    ->whereIn('stock', ['unlimited'])
                    ->get();

                if ($stock->first()) {
                    $product->stock = 'UNLIMITED';
                } else {
                    $sum = ProductVarient::where('product_id', $product->id)
                        ->pluck('stock')
                        ->sum();
                    if ($sum == 0) {
                        $product->stock = 'UNAVAILABLE';
                    } else {
                        $product->stock = $sum;
                    }
                }

                return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Get Successfully', 'Product Data' => $product]);
            } else {
                return response()->json(['success' => false, 'status' => 404, 'message' => 'Product Not Found']);
            }
        } catch (Exception $e) {
            return response()->json(['success' => true, 'status' => $e->getCode(), 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
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
            // validation for new varienrt data
            foreach ($request->varinet as $varient) {
                if ($varient['stock'] == null && $varient['color'] == null) {
                    return response()->json(['success' => true, 'status' => 422, 'message' => 'Please Enter Valid Data']);
                }
                $check = ProductVarient::where('product_color_id', $varient['color'])
                    ->where('product_size_id', $varient['size'])
                    ->first();
                if ($check) {
                    return response()->json(['success' => true, 'status' => 422, 'message' => 'Do not Enter Same Data']);
                }
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'price' => 'required|numeric',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'is_featured' => 'required',
                'long_description' => 'required',
                'varient' => 'required',
            ]);

            //update existing varient table data
            $varientid = ProductVarient::where('product_id', $id)->get();
            // dd(count($varientid));
            foreach ($request->varient as $key => $varient) {
                // dump($varient['color']);
                $varient_name = generateVarientName($varient['color'], $varient['size'], $request->name);

                if ($key < count($varientid)) {
                    ProductVarient::find($varientid[$key]->id)->update([
                        'product_color_id' => $varient['color'],
                        'product_size_id' => $varient['size'],
                        'variant_name' => $varient_name,
                        'stock' => $varient['stock'],
                    ]);
                } else {
                    // dump($varient_name."add");
                    $productVarient = ProductVarient::create([
                        'product_id' => $id,
                        'product_color_id' => $varient['color'],
                        'product_size_id' => $varient['size'],
                        'variant_name' => $varient_name,
                        'stock' => $varient['stock'],
                    ]);
                }
            }

            //update data in Product table
            $product = Product::find($id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'slug' => Str::slug($request->name, '-'),
                'is_featured' => $request->is_featured,
                'long_description' => $request->long_description,
            ]);
            if ($product) {
                //update Product in Product Description
                $productDescription = ProductDescription::where('product_id', $id)
                    ->first()
                    ->update(['additional_information' => $request->additional_information, 'description' => $request->description]);

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
                    if ($productImage != null && $productDescription != null) {
                        return response()->json(['success' => true, 'status' => 201, 'message' => 'Product Update Successfully']);
                    } else {
                        return response()->json(['success' => false, 'status' => 422, 'message' => 'Some Error Found']);
                    }
                }
                if ($productDescription != null) {
                    return response()->json(['success' => true, 'status' => 201, 'message' => 'Product Update Successfully']);
                } else {
                    return response()->json(['success' => false, 'status' => 422, 'message' => 'Some Error Found']);
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
            if ($productImage != null && $productDescription != null && $productVarient != null) {
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Delete Successfully']);
            } else {
                return response()->json(['success' => true, 'status' => 422, 'message' => 'Some Error Found']);
            }
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Product Not Found']);
        }
    }

    public function removeImageOfProduct($id)
    {
        $productImage = ImageProduct::where('image', $id)->first();
        if ($productImage) {
            $productImage->delete();
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Image Remove Successfully']);
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Product Not Found']);
        }
    }

    public function changeActiveStatus($id)
    {
        $product = Product::find($id);
        if ($product) {
            if ($product->isActive) {
                // dd($id);
                $product->isActive = 0;
                $product->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Status Change Successfully']);
            } else {
                $product->isActive = 1;
                $product->save();
                return response()->json(['success' => true, 'status' => 200, 'message' => 'Product Status Change Successfully']);
            }
        } else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Product Not Found']);
        }
    }

    public function list_featured_product(Request $request)
    {
        try {
            $limit = $request->input('limit');
            $productlist = Product::select('id', 'name', 'price')->with('productReview', 'productImages:id,product_id,image')->where('is_featured', 1)->get();
            $productlist = Product::limit($limit)
                ->get()
                ->makeHidden(['productReview', 'created_at', 'updated_at', 'sku', 'is_featured', 'long_description', 'description', 'slug', 'isActive', 'category_id', 'sub_category_id']);

            if ($productlist) {
                foreach ($productlist as $image) {
                    // $image->productImages[0]->image = url("/images/product/".$image->productImages[0]->image);
                    foreach ($image->productImages as $img) {
                        $img->image = url('/images/product/' . $img->image);
                    }
                }

                $rating = 0;
                $productreview = 0;
                foreach ($productlist as $review) {
                    // dd($review);
                    foreach ($review->productReview as $ele) {
                        // dd($ele);
                        $rating = $ele
                            ->where('product_id', $review->id)
                            ->pluck('rating')
                            ->avg();
                        $productreview = $ele
                            ->where('product_id', $review->id)
                            ->pluck('rating')
                            ->count();
                    }
                    $review->avg_rating = $rating;
                    $review->total_review = $productreview;
                    $rating = 0;
                    $productreview = 0;
                }
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Feautured Products Get Successfully',
                        'data' => $productlist,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 404,
                    'message' => 'Feautured Products Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getProduct($slug)
    {
        try {
            $productlist = Product::select('id', 'name', 'description', 'price', 'long_description')
                ->with(['colors:id,color', 'sizes:id,size', 'productImages:id,product_id,image', 'productReview:id,product_id,user_id,comment,rating'])
                ->where('slug', $slug)
                ->first();
            foreach ($productlist->productImages as $list) {
                $list->image = url('/images/product/' . $list->image);
            }
            // dd($productlist->productReview);
            $rat = [];
            $total_review = [];
            // dd($productlist);
            foreach ($productlist->productReview as $review) {
                // dd($review);
                $rating = $review
                    ->where('product_id', $productlist->id)
                    ->pluck('rating')
                    ->avg();
                $rat[] = $rating;
                $final_review = $review
                    ->where('product_id', $productlist->id)
                    ->pluck('rating')
                    ->count();
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

            if ($productlist) {
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Product Get Successfully',
                        'data' => $productlist,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 404,
                    'message' => 'Product Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function productAdditionalInformation(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);
            $id = $request->query('id');

            $productData = Product::select('id', 'name')->with('productInformation:id,product_id,description,additional_information')->findOrFail($id);
            if ($productData) {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Product Information Get Successfully',
                    'data' => $productData,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Product Information Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function productReview(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);
            $id = $request->query('id');
            $productData = Product::select('id', 'name')->with('productReview:id,product_id,user_id,comment,rating,created_at')->findOrFail($id);
            foreach ($productData->productReview as $product) {
                $img = $product->user->user_logo;
                // dump($img);
                $product->user->image = url("/images/users/$img");
            }
            // dd('hi');

            if ($productData) {
                $productData = $productData->makeHidden('user');
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Product Review Get Successfully',
                        'data' => $productData,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Product Review Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function addProductReview(addProductReviewRequest $request, $id)
    {
        try {
            $user = $request->user();
            // dd($user);
            $productId = Product::find($id);
            // dd($productId);
            if ($productId) {
                $productReview = ProductReview::create([
                    'user_id' => $user->id,
                    'product_id' => $id,
                    'comment' => $request->comment,
                    'rating' => $request->rating,
                ]);
                if ($productReview) {
                    return response()->json(
                        [
                            'success' => true,
                            'status' => 201,
                            'message' => 'Product Review Add Successfully',
                        ],
                        201,
                    );
                } else {
                    return response()->json([
                        'success' => true,
                        'status' => 500,
                        'message' => 'Product Review Not Added',
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getRelatedProduct($slug)
    {
        try {
            $relatedProduct = Product::with('category', 'subcategory', 'productReview', 'productImages:id,product_id,image')->where('slug', $slug)->get();
            $products = [];
            // $avg_rating = 0;

            foreach ($relatedProduct as $product) {
                // dd($product);
                $products = $product
                    ->where('sub_category_id', $product->subcategory->id)
                    ->with('category', 'subcategory', 'productReview', 'productImages')
                    ->get();
                $relatedProduct = $products;
            }
            $rating = 0;
            $total = 0;
            foreach ($relatedProduct as $ele) {
                foreach ($ele->productReview as $review) {
                    $rating = $review
                        ->where('product_id', $ele->id)
                        ->pluck('rating')
                        ->avg();
                    $total = $review
                        ->where('product_id', $ele->id)
                        ->pluck('rating')
                        ->count();
                }
                $ele->avg_rating = $rating;
                $ele->total_review = $total;
                $rating = 0;
                $total = 0;
            }
            if ($relatedProduct) {
                foreach ($relatedProduct as $product) {
                    foreach ($product->productImages as $img) {
                        // $img = $image->image;
                        // dd(url("/images/product/".$img->image));
                        $img->image = url('/images/product/' . $img->image);
                    }
                }
                $relatedProduct = $relatedProduct->makeHidden(['productReview', 'subcategory', 'category', 'category_id', 'sub_category_id', 'sku', 'slug', 'isActive', 'is_featured', 'description', 'long_description'])->toArray();
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Related Product Get Successfully',
                        'data' => $relatedProduct,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => true,
                    'status' => 404,
                    'message' => 'Related Product Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
