<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->subtotal ?? ($this->unit_price * $this->quantity);

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name ?? $this->product_name,
            'product_sku' => $this->product->sku ?? $this->product_sku,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'price' => (float) $this->unit_price,
            'subtotal' => (float) $subtotal,
        ];
    }
}
