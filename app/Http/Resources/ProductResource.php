<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'reviews_count' => $this->whenCounted('reviews'),
            'reviews_avg_rating' => $this->whenAggregated('reviews', 'rating', 'avg'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
