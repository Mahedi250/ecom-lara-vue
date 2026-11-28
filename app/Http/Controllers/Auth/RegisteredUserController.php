<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\UniquePhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Phone-only registration: name + phone + password. Phone is normalized
        // to digits (e.g. "+880 1700-123456" -> "8801700123456") and must be
        // unique — it is the login identifier. No OTP, no email required.
        $request->merge([
            'phone' => $request->filled('phone')
                ? preg_replace('/\D/', '', $request->input('phone'))
                : $request->input('phone'),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:20', new UniquePhone],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
