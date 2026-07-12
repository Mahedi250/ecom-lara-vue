<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_products' => Product::count(),
                'total_orders'   => Order::count(),
                'total_users'    => User::whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['admin', 'staff']))->count(),
                'total_revenue'  => (float) Order::where('status', '!=', 'cancelled')->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'today_orders'   => Order::whereDate('created_at', today())->count(),
                'today_revenue'  => (float) Order::whereDate('created_at', today())->sum('total'),
                'low_stock'      => Product::where('manage_stock', true)->where('stock', '<=', 5)->count(),
            ],
            'recent_orders' => fn () => Order::with('user')->latest()->limit(6)->get()->map(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'customer'     => $o->user?->name ?? 'Guest',
                'total'        => (float) $o->total,
                'status'       => $o->status->value,
                'status_label' => $o->status->label(),
                'created_at'   => $o->created_at?->diffForHumans(),
            ]),
        ]);
    }
}
