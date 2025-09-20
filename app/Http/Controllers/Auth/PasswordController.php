<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            // Check rate limiting
            $this->ensureIsNotRateLimited($request);

            $user = $request->user();
            
            // Additional validation: check if new password is different from current
            if (Hash::check($validated['password'], $user->password)) {
                return back()->withErrors(['password' => 'The new password must be different from your current password.']);
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Log password change
            Log::info('Password updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear rate limiting on successful update
            RateLimiter::clear($this->throttleKey($request));

            return back()->with('status', 'password-updated');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $request->user()->id,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['password' => 'An error occurred while updating your password. Please try again.']);
        }
    }

    /**
     * Handle API password update request
     */
    public function apiUpdate(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            $this->ensureIsNotRateLimited($request);

            $user = $request->user();
            
            if (Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('The new password must be different from your current password.');
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('API password updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            RateLimiter::clear($this->throttleKey($request));

            return $this->successResponse('Password updated successfully');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Validation failed');
        } catch (\Exception $e) {
            Log::error('API password update failed', [
                'user_id' => $request->user()->id,
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Password update failed: ' . $e->getMessage());
        }
    }

    /**
     * Check password strength
     */
    public function checkPasswordStrength(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string']
        ]);

        $password = $request->password;
        $strength = $this->calculatePasswordStrength($password);

        return $this->successResponse('Password strength calculated', [
            'password' => $password,
            'strength' => $strength,
            'score' => $strength['score'],
            'feedback' => $strength['feedback'],
        ]);
    }

    /**
     * Get password requirements
     */
    public function getPasswordRequirements()
    {
        $requirements = [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => true,
            'max_length' => 128,
        ];

        return $this->successResponse('Password requirements retrieved', $requirements);
    }

    /**
     * Calculate password strength
     */
    private function calculatePasswordStrength($password)
    {
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'Password should be at least 8 characters long';
        }

        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one uppercase letter';
        }

        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one lowercase letter';
        }

        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one number';
        }

        // Symbol check
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Password should contain at least one special character';
        }

        // Length bonus
        if (strlen($password) >= 12) {
            $score += 1;
        }

        // Determine strength level
        $strength = 'weak';
        if ($score >= 4) {
            $strength = 'strong';
        } elseif ($score >= 3) {
            $strength = 'medium';
        }

        return [
            'score' => $score,
            'strength' => $strength,
            'feedback' => $feedback,
        ];
    }

    /**
     * Ensure the password update request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'password' => "Too many password update attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return 'password-update:' . $request->user()->id . '|' . $request->ip();
    }
}
