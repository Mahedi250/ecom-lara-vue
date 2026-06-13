<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    private function customerQuery()
    {
        return User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['admin', 'staff']))
            ->withCount('orders')
            ->withSum('orders', 'total');
    }

    public function index(): Response
    {
        $customers = $this->customerQuery()
            ->when(request('search'), fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")
            )
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => fn () => $customers->through(fn ($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'phone'        => $u->phone,
                'avatar'       => $u->avatar,
                'status'       => $u->status?->value ?? 'active',
                'orders_count' => $u->orders_count,
                'orders_sum_total' => (float) ($u->orders_sum_total ?? 0),
                'created_at'   => $u->created_at,
            ]),
            'filters' => request()->only(['search', 'status']),
            'stats' => fn () => [
                'total'   => $this->customerQuery()->count(),
                'active'  => $this->customerQuery()->where('status', UserStatus::Active)->count(),
                'banned'  => $this->customerQuery()->where('status', UserStatus::Banned)->count(),
                'new_this_month' => $this->customerQuery()->whereMonth('created_at', now()->month)->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status'   => UserStatus::Active,
        ]);

        $user->assignRole('customer');

        return back()->with('success', "Customer {$user->name} created.");
    }

    public function ban(User $customer): RedirectResponse
    {
        if ($customer->hasRole(['admin', 'staff'])) {
            return back()->with('error', 'Cannot ban admin or staff.');
        }

        $customer->update(['status' => UserStatus::Banned]);

        return back()->with('success', "{$customer->name} has been banned.");
    }

    public function unban(User $customer): RedirectResponse
    {
        $customer->update(['status' => UserStatus::Active]);

        return back()->with('success', "{$customer->name} has been unbanned.");
    }

    public function destroy(User $customer): RedirectResponse
    {
        if ($customer->hasRole(['admin', 'staff'])) {
            return back()->with('error', 'Cannot delete admin or staff from here.');
        }

        $customer->delete();

        return back()->with('success', 'Customer deleted.');
    }
}
