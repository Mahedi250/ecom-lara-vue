<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\UserStatus;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\UniquePhone;
use App\Support\AdminMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        // access gated by role:admin|staff middleware

        $users = User::with('roles')
            ->when(request('search'), fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")
            )
            ->when(request('role'), fn($q, $r) => $q->role($r))
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($users),
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', new UniquePhone],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'roles' => ['array'],
            'roles.*' => ['string', Rule::in(Role::whereNotIn('name', AdminMenu::storefrontRoles())->pluck('name'))],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => UserStatus::from($data['status']),
            'email_verified_at' => now(), // admin-created accounts are pre-verified
        ]);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('admin.users.show', $user)->with('success', "User {$user->name} created.");
    }

    public function show(User $user): Response
    {
        $user->load(['roles', 'permissions']);

        return Inertia::render('Admin/Users/Show', [
            'user' => fn() => (new UserResource($user))->resolve(),
            'roles' => $this->assignableRoles(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string'],
        ]);

        $user->update($data);

        if ($request->has('roles')) {
            $request->validate([
                'roles' => ['array'],
                'roles.*' => ['string', Rule::in(Role::whereNotIn('name', AdminMenu::storefrontRoles())->pluck('name'))],
            ]);
            $user->syncRoles($request->roles);
        }

        return back()->with('success', 'User updated.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Password updated for {$user->name}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    /**
     * Roles that can be assigned from the admin panel — storefront roles
     * (customer/vendor) are auto-managed and never assignable here.
     */
    private function assignableRoles()
    {
        return Role::whereNotIn('name', AdminMenu::storefrontRoles())->get(['id', 'name']);
    }
}
