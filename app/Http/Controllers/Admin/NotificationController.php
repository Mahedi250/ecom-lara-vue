<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(30)
            ->get()
            ->map(fn($n) => [
                'id'           => $n->id,
                'event'        => $n->data['event'] ?? 'unknown',
                'message'      => $n->data['message'] ?? '',
                'order_number' => $n->data['order_number'] ?? null,
                'amount'       => $n->data['amount'] ?? null,
                'url'          => $n->data['url'] ?? null,
                'read'         => !is_null($n->read_at),
                'time_ago'     => $n->created_at->diffForHumans(),
                'created_at'   => $n->created_at->toISOString(),
            ]);

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markRead(string $id): JsonResponse
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}
