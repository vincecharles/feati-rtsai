<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\EmployeeProfile;
use App\Models\Dependent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;

        // Check authorization - allow admin, department_head, and program_head
        $allowedRoles = ['admin', 'department_head', 'program_head'];
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'You do not have permission to view the employee list.');
        }

        $query = User::with(['role', 'profile'])
            ->whereHas('profile')
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['teacher', 'department_head', 'program_head', 'osa', 'security']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            
            $employeeIds = User::search($search)
                ->get()
                ->pluck('id');
            
            if ($employeeIds->isNotEmpty()) {
                $query->whereIn('id', $employeeIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('department')) {
            $query->whereHas('profile', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if (in_array($userRole, ['department_head', 'program_head']) && $userDepartment) {
            $query->whereHas('profile', function($q) use ($userDepartment) {
                $q->where('department', $userDepartment);
            });
        }

        $employees = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('employees.index', compact('employees'));
    }

    /**
     * Autocomplete search for employees (AJAX endpoint)
     */
    public function autocomplete(Request $request)
    {
        $search = $request->input('q', '');
        
        if (empty($search)) {
            return response()->json([]);
        }

        $results = User::search($search)
            ->take(10)
            ->get()
            ->filter(function($user) {
                return $user->role && in_array($user->role->name, ['teacher', 'department_head', 'program_head', 'osa', 'security']);
            })
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . ($user->profile->employee_number ?? 'No ID') . ')',
                    'name' => $user->name,
                    'employee_number' => $user->profile->employee_number ?? 'N/A',
                    'department' => $user->profile->department ?? 'N/A',
                    'email' => $user->email,
                ];
            })
            ->values();

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only super_admin can create employees
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can create employees.');
        }

        // Fetch only employee roles (exclude student and admin roles)
        $roles = Role::whereNotIn('name', ['student', 'admin'])->orderBy('label')->get();
        return view('employees.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only super_admin can store employees
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can create employees.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'preferred_name' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'age' => 'nullable|integer|min:18|max:100',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'profile_email' => 'nullable|email|max:255',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'date_hired' => 'required|date',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);

            // Generate employee number
            $employeeNumber = $this->generateEmployeeNumber($validated['date_hired']);

            // Get role for position
            $role = Role::find($validated['role_id']);

            // Create employee profile
            $user->profile()->create([
                'employee_number' => $employeeNumber,
                'date_hired' => $validated['date_hired'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'suffix' => $validated['suffix'],
                'preferred_name' => $validated['preferred_name'],
                'sex' => $validated['sex'],
                'age' => $validated['age'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mobile'],
                'email' => $validated['profile_email'],
                'current_address' => $validated['current_address'],
                'permanent_address' => $validated['permanent_address'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_phone' => $validated['emergency_phone'],
                'emergency_address' => $validated['emergency_address'],
                'department' => $validated['department'],
                'position' => $validated['position'] ?? $role->label,
            ]);

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully. Employee Number: ' . $employeeNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $employee)
    {
        $user = Auth::user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;

        // Check authorization - allow admin, department_head, and program_head
        $allowedRoles = ['admin', 'department_head', 'program_head'];
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'You do not have permission to view employee details.');
        }

        // Department/Program heads can only view employees in their department
        if (in_array($userRole, ['department_head', 'program_head']) && $userDepartment) {
            if ($employee->profile->department !== $userDepartment) {
                abort(403, 'You can only view employees in your department.');
            }
        }

        $employee->load(['role', 'profile', 'dependents']);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        // Only super_admin can edit employees
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can edit employees.');
        }

        // Fetch only employee roles (exclude student and admin roles)
        $roles = Role::whereNotIn('name', ['student', 'admin'])->orderBy('label')->get();
        $employee->load('profile');
        return view('employees.edit', compact('employee', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {
        // Only super_admin can update employees
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can update employees.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'password' => 'nullable|min:8',
            'role_id' => 'required|exists:roles,id',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'preferred_name' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'age' => 'nullable|integer|min:18|max:100',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'profile_email' => 'nullable|email|max:255',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'date_hired' => 'required|date',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $userData = [
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'role_id' => $validated['role_id'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $employee->update($userData);

            // Get role for position
            $role = Role::find($validated['role_id']);

            // Update or create employee profile
            $profileData = [
                'date_hired' => $validated['date_hired'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'suffix' => $validated['suffix'],
                'preferred_name' => $validated['preferred_name'],
                'sex' => $validated['sex'],
                'age' => $validated['age'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mobile'],
                'email' => $validated['profile_email'],
                'current_address' => $validated['current_address'],
                'permanent_address' => $validated['permanent_address'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_phone' => $validated['emergency_phone'],
                'emergency_address' => $validated['emergency_address'],
                'department' => $validated['department'],
                'position' => $validated['position'] ?? $role->label,
            ];

            if ($employee->profile) {
                $employee->profile->update($profileData);
            } else {
                $employee->profile()->create(array_merge($profileData, [
                    'employee_number' => $this->generateEmployeeNumber($validated['date_hired'])
                ]));
            }

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee Update Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        // Only super_admin can delete employees
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can delete employees.');
        }

        try {
            DB::beginTransaction();

            // Delete dependents first
            $employee->dependents()->delete();
            
            // Delete profile
            $employee->profile()->delete();
            
            // Delete user
            $employee->delete();

            DB::commit();

            return redirect()->route('employees.index')
                ->with('success', 'Employee deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Add a dependent to an employee
     */
    public function addDependent(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $employee->dependents()->create($validated);
            return back()->with('success', 'Dependent added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add dependent: ' . $e->getMessage());
        }
    }

    /**
     * Remove a dependent from an employee
     */
    public function removeDependent(User $employee, Dependent $dependent)
    {
        try {
            if ($dependent->user_id !== $employee->id) {
                return back()->with('error', 'Dependent not found for this employee.');
            }

            $dependent->delete();
            return back()->with('success', 'Dependent removed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove dependent: ' . $e->getMessage());
        }
    }

    /**
     * Generate employee number in format YY-XXXXXXXX
     */
    private function generateEmployeeNumber($dateHired)
    {
        $year = date('y', strtotime($dateHired));
        
        // Get the last employee number for this year
        $lastEmployee = EmployeeProfile::where('employee_number', 'like', $year . '-%')
            ->orderBy('employee_number', 'desc')
            ->first();

        if ($lastEmployee) {
            // Extract the sequential number and increment
            $lastNumber = (int) substr($lastEmployee->employee_number, 3);
            $newNumber = $lastNumber + 1;
        } else {
            // First employee of the year
            $newNumber = 1;
        }

        return $year . '-' . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
    }
}
