<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status->label(),
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'shipping_cost' => (float) $this->shipping_cost,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'coupon_code' => $this->coupon_code,
            'notes' => $this->notes,
            'user' => $this->whenLoaded('user', fn() => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ] : null),
            'items' => $this->whenLoaded('items', fn() => OrderItemResource::collection($this->items)->resolve()),
            'shipping_address' => $this->whenLoaded('shippingAddress', fn() => (new OrderAddressResource($this->shippingAddress))->resolve()),
            'billing_address' => $this->whenLoaded('billingAddress', fn() => (new OrderAddressResource($this->billingAddress))->resolve()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
