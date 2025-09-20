<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // Get default role (student) if not specified
            $roleId = $request->role_id ?? Role::where('name', 'student')->first()?->id;
            
            if (!$roleId) {
                throw new \Exception('Default role not found');
            }

            $user = User::create([
                'name' => trim($request->name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $roleId,
                'status' => 'active',
                'email_verified_at' => null, // Require email verification
            ]);

            // Log registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            event(new Registered($user));

            DB::commit();

            // Don't auto-login, require email verification
            return redirect()->route('login')
                ->with('success', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
            ]);

            return back()->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }

    /**
     * Handle API registration request
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        try {
            DB::beginTransaction();

            $roleId = $request->role_id ?? Role::where('name', 'student')->first()?->id;
            
            if (!$roleId) {
                return $this->errorResponse('Default role not found');
            }

            $user = User::create([
                'name' => trim($request->name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $roleId,
                'status' => 'active',
                'email_verified_at' => null,
            ]);

            event(new Registered($user));

            DB::commit();

            return $this->successResponse('Registration successful. Please verify your email.', [
                'user' => $user->load('role')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->errorResponse('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Check if email is available
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $exists = User::where('email', $request->email)->exists();

        return $this->successResponse('Email availability checked', [
            'email' => $request->email,
            'available' => !$exists
        ]);
    }

    /**
     * Get available roles for registration
     */
    public function getRoles()
    {
        $roles = Role::where('name', '!=', 'admin')
            ->select('id', 'name', 'display_name')
            ->get();

        return $this->successResponse('Roles retrieved successfully', [
            'roles' => $roles
        ]);
    }
}
