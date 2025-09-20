<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            // Check rate limiting
            $this->ensureIsNotRateLimited($request);

            // Check if user exists and is active
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'We cannot find a user with that email address.']);
            }

            if ($user->status !== 'active') {
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Your account is not active. Please contact administrator.']);
            }

            // Send password reset link
            $status = Password::sendResetLink(
                $request->only('email')
            );

            // Log password reset request
            Log::info('Password reset requested', [
                'email' => $request->email,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $status,
            ]);

            if ($status == Password::RESET_LINK_SENT) {
                // Clear rate limiting on successful request
                RateLimiter::clear($this->throttleKey($request));
                
                return back()->with('status', __($status));
            }

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Password reset request failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'An error occurred. Please try again later.']);
        }
    }

    /**
     * Handle API password reset request
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $this->ensureIsNotRateLimited($request);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return $this->errorResponse('User not found', null, 404);
            }

            if ($user->status !== 'active') {
                return $this->errorResponse('Account is not active', null, 403);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            Log::info('API password reset requested', [
                'email' => $request->email,
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'status' => $status,
            ]);

            if ($status == Password::RESET_LINK_SENT) {
                RateLimiter::clear($this->throttleKey($request));
                
                return $this->successResponse('Password reset link sent successfully');
            }

            return $this->errorResponse('Failed to send password reset link');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation failed');
        } catch (\Exception $e) {
            Log::error('API password reset request failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Password reset request failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if email exists for password reset
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();
        $exists = $user !== null;
        $isActive = $user && $user->status === 'active';

        return $this->successResponse('Email check completed', [
            'email' => $request->email,
            'exists' => $exists,
            'is_active' => $isActive,
            'can_reset' => $exists && $isActive,
        ]);
    }

    /**
     * Ensure the password reset request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Too many password reset attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return 'password-reset:' . strtolower($request->input('email')) . '|' . $request->ip();
    }
}
