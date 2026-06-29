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
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price !== null ? (float) $this->sale_price : null,
            'effective_price' => (float) ($this->sale_price ?? $this->price),
            'stock' => $this->stock,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'reviews_count' => $this->whenCounted('reviews'),
            'reviews_avg_rating' => $this->whenAggregated('reviews', 'rating', 'avg'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
