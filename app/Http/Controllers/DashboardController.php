<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $uid = Auth::id();
        $orders = Order::where('user_id', $uid);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_orders'     => (clone $orders)->count(),
                'pending_orders'   => (clone $orders)->whereIn('status', ['pending', 'processing'])->count(),
                'delivered_orders' => (clone $orders)->where('status', 'delivered')->count(),
                'total_spent'      => (float) (clone $orders)->whereNotIn('status', ['cancelled'])->sum('total'),
            ],
            'recent_orders' => fn () => (clone $orders)->latest()->limit(5)->get()->map(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'status'       => $o->status->value,
                'status_label' => $o->status->label(),
                'status_color' => $o->status->color(),
                'total'        => (float) $o->total,
                'items_count'  => $o->items()->count(),
                'created_at'   => $o->created_at,
            ]),
        ]);
    }
}
