<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlists;
use Exception;
use Illuminate\Http\Request;

class WishlistsController extends Controller
{
    // Add Product To Wishlist
    public function addProductWishlist(Request $request, $id)
    {
        try {
            $user = $request->user();
            $flag = Wishlists::where('product_id', $id)->first();
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
                return response()->json(
                    [
                        'success' => false,
                        'status' => 500,
                        'message' => 'Product Already Added In Wishlist',
                    ],
                    500,
                );
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
    public function showProductWishlists()
    {
        try {
            $wishlist = Wishlists::with('products')->get();
            if ($wishlist) {
                return response()->json(
                    [
                        'success' => true,
                        'status' => 200,
                        'wishlist' => $wishlist,
                    ],
                    200,
                );
            } 
            else {
                return response()->json(
                    [
                        'success' => false,
                        'status' => 500,
                    ],
                    500,
                );
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'warning',
                'message' => $e,
            ]);
        }
    }

    // Delete Product From Wishlist
    public function removeProductWishlist(Request $request, $id)
    {
        $user = $request->user();
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
        } 
        else {
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
}
