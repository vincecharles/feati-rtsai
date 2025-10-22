<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereNotNull('student_id')
            ->whereHas('role', function($q) {
                $q->where('name', 'student');
            });

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%");
            });
        }

        // Filter by department (program for students)
        if ($request->has('department') && $request->department) {
            $query->where('program', $request->department);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->expectsJson()) {
            return $this->successResponse('Students retrieved successfully', [
                'students' => $students->items(),
                'pagination' => $this->getPaginationData($students)
            ]);
        }

        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $roles = Role::where('name', 'student')->get();
        $departments = $this->getDepartments();
        
        return view('students.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'department' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:5',
            'student_id' => 'required|string|unique:employee_profiles,employee_number',
            'enrollment_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Get student role
            $studentRole = Role::where('name', 'student')->first();
            if (!$studentRole) {
                throw new \Exception('Student role not found');
            }

            // Create user
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $studentRole->id,
                'status' => 'active',
            ]);

            // Create student profile
            $user->profile()->create([
                'employee_number' => $validated['student_id'],
                'date_hired' => $validated['enrollment_date'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'suffix' => $validated['suffix'],
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mobile'],
                'current_address' => $validated['current_address'],
                'permanent_address' => $validated['permanent_address'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_phone' => $validated['emergency_phone'],
                'emergency_address' => $validated['emergency_address'],
                'department' => $validated['department'],
                'course' => $validated['course'],
                'year_level' => $validated['year_level'],
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Student created successfully', [
                    'student' => $user->load('profile')
                ]);
            }

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully. Student ID: ' . $validated['student_id']);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to create student: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student
     */
    public function show(User $student)
    {
        $student->load(['role', 'profile', 'dependents']);
        
        if (request()->expectsJson()) {
            return $this->successResponse('Student retrieved successfully', [
                'student' => $student
            ]);
        }
        
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(User $student)
    {
        $roles = Role::where('name', 'student')->get();
        $departments = $this->getDepartments();
        $student->load('profile');
        
        return view('students.edit', compact('student', 'roles', 'departments'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->id)],
            'password' => 'nullable|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'mobile' => 'nullable|string|max:20',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'department' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:5',
            'student_id' => ['required', 'string', Rule::unique('employee_profiles', 'employee_number')->ignore($student->profile->id)],
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended,graduated',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $userData = [
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'status' => $validated['status'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $student->update($userData);

            // Update student profile
            $profileData = [
                'employee_number' => $validated['student_id'],
                'date_hired' => $validated['enrollment_date'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'suffix' => $validated['suffix'],
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'mobile' => $validated['mobile'],
                'current_address' => $validated['current_address'],
                'permanent_address' => $validated['permanent_address'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_phone' => $validated['emergency_phone'],
                'emergency_address' => $validated['emergency_address'],
                'department' => $validated['department'],
                'course' => $validated['course'],
                'year_level' => $validated['year_level'],
            ];

            if ($student->profile) {
                $student->profile->update($profileData);
            } else {
                $student->profile()->create($profileData);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Student updated successfully', [
                    'student' => $student->load('profile')
                ]);
            }

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to update student: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(User $student)
    {
        try {
            DB::beginTransaction();

            // Delete dependents first
            $student->dependents()->delete();
            
            // Delete profile
            $student->profile()->delete();
            
            // Delete user
            $student->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return $this->successResponse('Student deleted successfully');
            }

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return $this->errorResponse('Failed to delete student: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    /**
     * Get departments list
     */
    private function getDepartments()
    {
        return [
            'Engineering' => 'Engineering',
            'Business' => 'Business Administration',
            'IT' => 'Information Technology',
            'Arts' => 'Liberal Arts',
            'Science' => 'Natural Sciences',
            'Education' => 'Education',
            'Nursing' => 'Nursing',
            'Medicine' => 'Medicine',
        ];
    }

    /**
     * Get students statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_students' => User::whereHas('role', function($q) {
                    $q->where('name', 'student');
                })->count(),
                'active_students' => User::whereHas('role', function($q) {
                    $q->where('name', 'student');
                })->where('status', 'active')->count(),
                'graduated_students' => User::whereHas('role', function($q) {
                    $q->where('name', 'student');
                })->where('status', 'graduated')->count(),
                'suspended_students' => User::whereHas('role', function($q) {
                    $q->where('name', 'student');
                })->where('status', 'suspended')->count(),
            ];

            return $this->successResponse('Student statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve student statistics: ' . $e->getMessage());
        }
    }

    /**
     * Bulk operations on students
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        try {
            $students = User::whereIn('id', $request->student_ids)
                ->whereHas('role', function($q) {
                    $q->where('name', 'student');
                })
                ->get();

            DB::beginTransaction();

            foreach ($students as $student) {
                switch ($request->action) {
                    case 'activate':
                        $student->update(['status' => 'active']);
                        break;
                    case 'deactivate':
                        $student->update(['status' => 'inactive']);
                        break;
                    case 'suspend':
                        $student->update(['status' => 'suspended']);
                        break;
                    case 'delete':
                        $student->dependents()->delete();
                        $student->profile()->delete();
                        $student->delete();
                        break;
                }
            }

            DB::commit();

            return $this->successResponse(
                ucfirst($request->action) . ' action completed successfully',
                ['affected_count' => $students->count()]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to perform bulk action: ' . $e->getMessage());
        }
    }
}
