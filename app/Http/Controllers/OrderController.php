<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CartService $cartService,
    ) {}

    public function index(): Response
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->through(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status->value,
                'status_label' => $o->status->label(),
                'status_color' => $o->status->color(),
                'total'        => (float) $o->total,
                'items_count'  => $o->items_count,
                'created_at'   => $o->created_at,
            ]),
        ]);
    }

    private function isBanned(): bool
    {
        return Auth::check() && Auth::user()->status === UserStatus::Banned;
    }

    public function checkout(): Response|RedirectResponse
    {
        if ($this->isBanned()) {
            return redirect()->route('home')->with('error', 'Your account has been suspended.');
        }

        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('Checkout/Index', [
            'cart' => fn() => (new CartResource($cart))->resolve(),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        if ($this->isBanned()) {
            abort(403, 'Your account has been suspended.');
        }

        $order = $this->orderService->placeOrder($request->validated());

        session(['last_order_id' => $order->id]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order #{$order->order_number} placed successfully!");
    }

    public function show(Order $order): Response
    {
        if (Auth::check()) {
            if ($order->user_id !== Auth::id() && ! Auth::user()->hasRole(['admin', 'staff'])) {
                abort(403);
            }
        } else {
            if (session('last_order_id') !== $order->id) {
                abort(403);
            }
        }

        $order->load(['items.product', 'shippingAddress', 'billingAddress']);

        return Inertia::render('Orders/Show', [
            'order' => fn() => (new OrderResource($order))->resolve(),
        ]);
    }
}
