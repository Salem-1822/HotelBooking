<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $admin   = Auth::guard('admin')->user();
        $hotelId = $admin->hotel_id;

        if (!$hotelId) {
            abort(403, __('admin.no_hotel_assigned_error'));
        }

        $reviews = Review::with(['user', 'hotel'])
            ->where('hotel_id', $hotelId)
            ->latest()
            ->paginate(15);

        $totalReviews  = Review::where('hotel_id', $hotelId)->count();
        $averageRating = Review::where('hotel_id', $hotelId)->avg('rating');

        return view('admin.reviews.index', [
            'reviews'       => $reviews,
            'totalReviews'  => $totalReviews,
            'averageRating' => $averageRating ? round($averageRating, 1) : 0,
        ]);
    }

    public function destroy(Review $review)
    {
        $admin   = Auth::guard('admin')->user();
        $hotelId = $admin->hotel_id;

        if (!$hotelId || $review->hotel_id !== $hotelId) {
            abort(403, __('admin.unauthorized_delete_review'));
        }

        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', __('admin.review_deleted_success'));
    }
}
