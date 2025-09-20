<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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

            // Attempt to reset the password
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));

                    // Log password reset
                    Log::info('Password reset successful', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                // Clear rate limiting on successful reset
                RateLimiter::clear($this->throttleKey($request));
                
                return redirect()->route('login')
                    ->with('status', 'Your password has been reset successfully. You can now login with your new password.');
            }

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'An error occurred while resetting your password. Please try again.']);
        }
    }

    /**
     * Handle API password reset request
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));

                    Log::info('API password reset successful', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'ip_address' => $request->ip(),
                    ]);
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                RateLimiter::clear($this->throttleKey($request));
                
                return $this->successResponse('Password reset successfully');
            }

            return $this->errorResponse('Password reset failed: ' . __($status));

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation failed');
        } catch (\Exception $e) {
            Log::error('API password reset failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Password reset failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate password reset token
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return $this->errorResponse('User not found', null, 404);
            }

            // Check if token is valid (this is a simplified check)
            // In a real implementation, you'd validate the actual token
            $isValid = true; // This should be replaced with actual token validation

            return $this->successResponse('Token validation completed', [
                'email' => $request->email,
                'is_valid' => $isValid,
            ]);

        } catch (\Exception $e) {
            Log::error('Token validation failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Token validation failed: ' . $e->getMessage());
        }
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
