<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Check for too many login attempts
            $this->ensureIsNotRateLimited($request);

            $request->authenticate();

            $request->session()->regenerate();

            // Log successful login
            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear rate limiting on successful login
            RateLimiter::clear($this->throttleKey($request));

            // Check if user is active
            if (Auth::user()->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact administrator.',
                ]);
            }

            // Redirect based on user role
            $redirectTo = $this->getRedirectPath(Auth::user());

            return redirect()->intended($redirectTo);

        } catch (ValidationException $e) {
            // Log failed login attempt
            Log::warning('Failed login attempt', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'errors' => $e->errors(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $userEmail = Auth::user()?->email;

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log logout
        Log::info('User logged out', [
            'user_id' => $userId,
            'email' => $userEmail,
            'ip_address' => $request->ip(),
        ]);

        return redirect('/');
    }

    /**
     * Handle API login request
     */
    public function apiStore(LoginRequest $request)
    {
        try {
            $this->ensureIsNotRateLimited($request);

            $request->authenticate();

            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return $this->errorResponse('Account is not active', null, 403);
            }

            $request->session()->regenerate();

            // Create API token (if using Sanctum)
            $token = $user->createToken('api-token')->plainTextToken;

            Log::info('API login successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            RateLimiter::clear($this->throttleKey($request));

            return $this->successResponse('Login successful', [
                'user' => $user->load('role'),
                'token' => $token,
            ]);

        } catch (ValidationException $e) {
            Log::warning('API login failed', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'errors' => $e->errors(),
            ]);

            return $this->validationErrorResponse($e->errors(), 'Login failed');
        } catch (\Exception $e) {
            Log::error('API login error', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle API logout request
     */
    public function apiDestroy(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                // Revoke all tokens (if using Sanctum)
                $user->tokens()->delete();

                Log::info('API logout successful', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip_address' => $request->ip(),
                ]);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->successResponse('Logout successful');

        } catch (\Exception $e) {
            Log::error('API logout error', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Logout failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user's current session info
     */
    public function sessionInfo(Request $request)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse('Not authenticated');
        }

        $user = Auth::user()->load('role');

        return $this->successResponse('Session info retrieved', [
            'user' => $user,
            'session_id' => $request->session()->getId(),
            'last_activity' => $request->session()->get('last_activity'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

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
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * Get redirect path based on user role
     */
    protected function getRedirectPath(User $user): string
    {
        // Redirect to welcome splash screen after login
        return route('welcome.splash');
    }

    /**
     * Check if user can login (for additional business logic)
     */
    protected function canLogin(User $user): bool
    {
        // Add any additional login restrictions here
        // For example: check if user is suspended, banned, etc.
        
        if ($user->status === 'suspended') {
            return false;
        }

        if ($user->status === 'inactive') {
            return false;
        }

        return true;
    }
}
