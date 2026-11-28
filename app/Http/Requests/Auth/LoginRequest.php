<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserStatus;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * The identifier may be an email address or a phone number.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! $this->attemptCredentials()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Reject banned users immediately after credentials verified
        if (Auth::user()->status === UserStatus::Banned) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended. Please contact support.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Try every plausible credential form for the given identifier.
     */
    private function attemptCredentials(): bool
    {
        $password = $this->string('password');
        $identifier = trim($this->string('email'));
        $remember = $this->boolean('remember');

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
        }

        $digits = preg_replace('/\D/', '', $identifier);

        foreach ($this->phoneCandidates($digits) as $candidate) {
            if (Auth::attempt(['phone' => $candidate, 'password' => $password], $remember)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A stored 8801XXXXXXXXX can be typed as 01XXXXXXXXX and vice versa.
     */
    private function phoneCandidates(string $digits): array
    {
        $candidates = [$digits];

        if (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            $candidates[] = '0'.substr($digits, 3);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $candidates[] = '880'.substr($digits, 1);
        }

        return array_unique($candidates);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
