<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): Response
    {
        $orders = Order::with(['user'])
            ->when(request('search'), fn($q, $s) =>
                $q->where('order_number', 'like', "%{$s}%")
            )
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => OrderResource::collection($orders),
            'statuses' => collect(OrderStatus::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'items', 'shippingAddress', 'billingAddress', 'payments']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => fn() => (new OrderResource($order))->resolve(),
            'statuses' => collect(OrderStatus::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $this->orderService->updateStatus($order, OrderStatus::from($request->status));

        return back()->with('success', 'Order status updated.');
    }
}
