<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function show(User $user): Response
    {
        $user->load(['roles', 'permissions']);

        return Inertia::render('Admin/Users/Show', [
            'user' => new UserResource($user),
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string'],
        ]);

        $user->update($data);

        if ($request->has('roles')) {
            $request->validate(['roles' => ['array'], 'roles.*' => ['string', 'exists:roles,name']]);
            $user->syncRoles($request->roles);
        }

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
