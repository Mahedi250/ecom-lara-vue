<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): AnonymousResourceCollection
    {
        $orders = Order::with('user')
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        $order->load(['user', 'items', 'shippingAddress', 'billingAddress', 'payments']);

        return new OrderResource($order);
    }

    public function update(Request $request, Order $order): OrderResource
    {
        $request->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);

        $this->orderService->updateStatus($order, OrderStatus::from($request->status));

        return new OrderResource($order->fresh());
    }
}
