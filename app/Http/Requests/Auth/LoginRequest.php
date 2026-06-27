<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'turnstile_token' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->validateTurnstile();
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function validateTurnstile(): void
    {
        $secret = config('services.turnstile.secret');

        if (app()->environment('local') && empty($secret)) {
            return; // allow local dev when not configured
        }

        if (empty($secret)) {
            throw ValidationException::withMessages([
                'turnstile_token' => 'Captcha configuration error.',
            ]);
        }

        $token = $this->input('turnstile_token');
        if (!$token) {
            throw ValidationException::withMessages([
                'turnstile_token' => 'Please complete the captcha.',
            ]);
        }

        // Configure HTTP client with proxy if available
        $httpClient = Http::asForm();
        
        $proxy = env('HTTPS_PROXY') ?: env('https_proxy');
        if ($proxy) {
            $httpClient = $httpClient->withOptions([
                'proxy' => [
                    'http' => $proxy,
                    'https' => $proxy,
                ],
                'verify' => false, // Disable SSL verification for proxy
            ]);
        }

        $response = $httpClient->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $this->ip(),
        ]);

        if (!$response->ok()) {
            throw ValidationException::withMessages([
                'turnstile_token' => 'Captcha verification failed. Try again.',
            ]);
        }

        $data = $response->json();
        if (!($data['success'] ?? false)) {
            throw ValidationException::withMessages([
                'turnstile_token' => 'Captcha was not valid. Please try again.',
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
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
