<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Wishlists;
use Exception;
use Illuminate\Http\Request;

class WishlistsController extends Controller
{
    // Add Product To Wishlist
    public function addRemoveProductWishlist(Request $request, $id)
    {
        try {
            $user = $request->user();
            $productId = Product::find($id);
            if ($productId) {
                $flag = Wishlists::where('product_id', $id)
                    ->where('user_id', $user->id)
                    ->first();
                if (!$flag) {
                    $wishlist = Wishlists::create([
                        'user_id' => $user->id,
                        'product_id' => $id,
                    ]);
                    if ($wishlist) {
                        return response()->json(
                            [
                                'success' => true,
                                'status' => 201,
                                'message' => 'Product Added To Wishlist',
                            ],
                            201,
                        );
                    } else {
                        return response()->json(
                            [
                                'success' => false,
                                'status' => 200,
                                'message' => 'Product Not Added To Wishlist',
                            ],
                            200,
                        );
                    }
                } else {
                    $wishlist = Wishlists::where('product_id', $id)
                        ->where('user_id', $user->id)
                        ->get()
                        ->first();
                    if (!is_null($wishlist)) {
                        $wishlist->delete();
                        return response()->json(
                            [
                                'success' => true,
                                'status' => 200,
                                'message' => 'Product Removed From Wishlist',
                            ],
                            200,
                        );
                    } else {
                        return response()->json(
                            [
                                'success' => false,
                                'status' => 200,
                                'message' => 'Product Not Found',
                            ],
                            200,
                        );
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Product Not Found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e,
            ]);
        }
    }

    // Show Product In Wishlists
    public function showProductWishlists(Request $request)
    {
        $user = $request->user();
        try {
            $wishlist = Wishlists::where('user_id', $user->id)
                ->with([
                    'products' => function ($query) {
                        $query->select('id', 'name', 'price');
                    },
                ])
                ->paginate(5);

            foreach ($wishlist as $ele) {
                $productId = $ele->products->id;
                $productImg = Product::find($productId)->productImages->pluck('image')->first();
                $review = ProductReview::where('product_id', $ele->product_id)->pluck('rating');
                $ratingAverage = $review->avg();
                $totalReview = $review->count();
                if (is_null($ratingAverage)) {
                    $ratingAverage = 0;
                }
                $ele->product_images = url("/images/product/$productImg");;
                $ele->avg_rating = $ratingAverage;
                $ele->total_review = $totalReview;
            }

            if ($wishlist) {
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'message' => 'Product Found',
                        'wishlist' => $wishlist,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 500,
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
}
