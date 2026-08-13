<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page (requires login).
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.web')
                             ->with('info', 'Please login to view your wishlist.');
        }

        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->latest()
            ->get();

        $defaultFallback = 'website/assets/img/bg/Sunglasses1.png';

        foreach ($wishlistItems as $item) {
            // Find product in tbl_product_code by product_id or id
            $product = Product::where('product_id', $item->product_id)
                              ->orWhere('id', $item->product_id)
                              ->first();

            if ($product) {
                $typeLower = strtolower($product->product_type ?: 'frame');
                $imageUrl  = asset($defaultFallback);

                if ($product->main_image) {
                    if ($product->parent_product_code) {
                        $path = "uploads/{$typeLower}/product/{$product->parent_product_code}/{$product->main_image}";
                        if (file_exists(public_path($path))) {
                            $imageUrl = asset($path);
                        }
                    } else {
                        $pathWithId = "uploads/{$typeLower}/product/{$product->product_id}/{$product->main_image}";
                        if (file_exists(public_path($pathWithId))) {
                            $imageUrl = asset($pathWithId);
                        } else if (file_exists(public_path($product->main_image))) {
                            $imageUrl = asset($product->main_image);
                        }
                    }
                } else if ($product->product_image) {
                    if (file_exists(public_path($product->product_image))) {
                        $imageUrl = asset($product->product_image);
                    }
                }

                $product->resolved_image_url  = $imageUrl;
                $product->resolved_name       = $product->product_name ?: ($product->product_code ?: 'Product #' . $product->id);
                $product->resolved_brand      = $product->Company ?: 'Speckarts';
                $product->resolved_price      = floatval($product->Retail_Price ?: ($product->discount_price ?: 0));
                $product->resolved_detail_url = url('/product/' . ($product->product_id ?: $product->id));

                $item->product = $product;
            }
        }

        return view('website.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Toggle wishlist — add if not present, remove if present.
     * Called via AJAX from product listings / detail pages.
     * Requires user to be authenticated (enforced by middleware).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'status'  => 'unauthenticated',
                'message' => 'Please login to save items to your wishlist.',
            ], 401);
        }

        $userId    = Auth::id();
        $productId = $request->input('product_id');

        // Verify product exists
        $product = Product::where('product_id', $productId)
                          ->orWhere('id', $productId)
                          ->first();

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        $realProductId = $product->product_id ?: $product->id;

        // Check if already wishlisted
        $existing = Wishlist::where('user_id', $userId)
                            ->where('product_id', $realProductId)
                            ->first();

        if ($existing) {
            $existing->delete();
            $count = Wishlist::where('user_id', $userId)->count();
            return response()->json([
                'status'  => 'removed',
                'message' => 'Removed from wishlist.',
                'count'   => $count,
            ]);
        }

        Wishlist::create([
            'user_id'    => $userId,
            'product_id' => $realProductId,
        ]);

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'status'  => 'added',
            'message' => 'Added to wishlist!',
            'count'   => $count,
        ]);
    }

    /**
     * Remove a specific wishlist item by its DB id (used on wishlist page).
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $item = Wishlist::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found.'], 404);
        }

        $item->delete();
        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'status'  => 'success',
            'message' => 'Removed from wishlist.',
            'count'   => $count,
        ]);
    }

    /**
     * Get current wishlist count for the authenticated user.
     * Used for badge updates after login.
     */
    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Wishlist::where('user_id', Auth::id())->count();
        return response()->json(['count' => $count]);
    }
}
