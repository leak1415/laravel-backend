<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => new ReviewResource($review),
        ], 201);
    }

    public function productReviews(Product $product): JsonResponse
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(ReviewResource::collection($reviews));
    }
}
