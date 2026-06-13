<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): AnonymousResourceCollection
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        $order = $this->orderService->placeOrder($request->validated());

        return new OrderResource($order);
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);
        $order->load(['items', 'shippingAddress', 'billingAddress']);

        return new OrderResource($order);
    }
}
