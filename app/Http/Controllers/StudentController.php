<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;

        $query = User::with('role')
            ->whereNotNull('student_id')
            ->whereHas('role', function($q) {
                $q->where('name', 'student');
            });

        // Apply role-based department filtering
        switch ($userRole) {
            case 'department_head':
            case 'program_head':
                // Department and program heads can only see their own department's students
                $query->where('program', $userDepartment);
                break;
            case 'student':
                // Students can only see themselves
                $query->where('id', $user->id);
                break;
            case 'teacher':
                // Teachers can see all students (no restriction)
                break;
            case 'security':
                // Security can see all students
                break;
            // super_admin and others see all students
        }

        // Search functionality using Algolia Scout
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            
            // Use Scout search
            $studentIds = User::search($search)
                ->where('role', 'student')
                ->get()
                ->pluck('id');
            
            if ($studentIds->isNotEmpty()) {
                $query->whereIn('id', $studentIds);
            } else {
                // No results found, return empty
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by department (program for students) - only if user has permission
        if ($request->has('department') && $request->department) {
            if ($userRole === 'admin' || $userRole === 'security' || $userRole === 'osa') {
                // Only super admin, security, and osa can filter by arbitrary departments
                $query->where('program', $request->department);
            } elseif ($userRole === 'department_head' || $userRole === 'program_head') {
                // Dept heads and program heads can only see their own department
                if ($request->department === $userDepartment) {
                    $query->where('program', $request->department);
                }
            }
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
        // Only super_admin can create students
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can create students.');
        }

        $roles = Role::where('name', 'student')->get();
        $departments = $this->getDepartments();
        $programs = $this->getPrograms();
        $departmentPrograms = $this->getDepartmentProgramsMapping();
        
        // Get all countries using the laravel-countries package
        $countries = \Lwwcas\LaravelCountries\Models\Country::select('id', 'official_name', 'iso_alpha_2')
            ->where('is_visible', 1)
            ->orderBy('official_name')
            ->get()
            ->pluck('official_name', 'official_name');
        
        return view('students.create', compact('roles', 'departments', 'programs', 'departmentPrograms', 'countries'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        // Only super_admin can store students
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can create students.');
        }

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
                'middle_name' => $validated['middle_name'] ?? null,
                'suffix' => $validated['suffix'] ?? null,
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'civil_status' => $validated['civil_status'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
                'emergency_name' => $validated['emergency_name'] ?? null,
                'emergency_relationship' => $validated['emergency_relationship'] ?? null,
                'emergency_phone' => $validated['emergency_phone'] ?? null,
                'emergency_address' => $validated['emergency_address'] ?? null,
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
        $user = auth()->user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;
        
        // Check authorization
        if (!in_array($userRole, ['admin', 'osa', 'program_head', 'department_head', 'teacher', 'security'])) {
            abort(403, 'You do not have permission to view student details.');
        }
        
        // Program heads, department heads, and teachers can only view students in their program
        if (in_array($userRole, ['program_head', 'department_head', 'teacher']) && $userDepartment) {
            if ($student->program !== $userDepartment) {
                abort(403, 'You can only view students in your program/department.');
            }
        }
        
        $student->load(['role', 'profile', 'dependents']);
        
        // Determine what fields can be viewed based on role
        $canViewFullInfo = in_array($userRole, ['admin', 'osa']) || 
                          ($userRole === 'program_head' && $student->program === $userDepartment);
        
        if (request()->expectsJson()) {
            return $this->successResponse('Student retrieved successfully', [
                'student' => $student,
                'canViewFullInfo' => $canViewFullInfo
            ]);
        }
        
        return view('students.show', compact('student', 'canViewFullInfo'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(User $student)
    {
        // Only super_admin can edit students
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can edit students.');
        }

        $roles = Role::where('name', 'student')->get();
        $departments = $this->getDepartments();
        $programs = $this->getPrograms();
        $departmentPrograms = $this->getDepartmentProgramsMapping();
        $student->load('profile');
        
        // Get all countries using the laravel-countries package
        $countries = \Lwwcas\LaravelCountries\Models\Country::select('id', 'official_name', 'iso_alpha_2')
            ->where('is_visible', 1)
            ->orderBy('official_name')
            ->get()
            ->pluck('official_name', 'official_name');
        
        return view('students.edit', compact('student', 'roles', 'departments', 'programs', 'departmentPrograms', 'countries'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, User $student)
    {
        // Only super_admin can update students
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can update students.');
        }

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
                'middle_name' => $validated['middle_name'] ?? null,
                'suffix' => $validated['suffix'] ?? null,
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'civil_status' => $validated['civil_status'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'current_address' => $validated['current_address'] ?? null,
                'permanent_address' => $validated['permanent_address'] ?? null,
                'emergency_name' => $validated['emergency_name'] ?? null,
                'emergency_relationship' => $validated['emergency_relationship'] ?? null,
                'emergency_phone' => $validated['emergency_phone'] ?? null,
                'emergency_address' => $validated['emergency_address'] ?? null,
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
        // Only super_admin can delete students
        if (Auth::user()->role->name !== 'admin') {
            abort(403, 'Only Super Admin can delete students.');
        }

        try {
            DB::beginTransaction();

            
            $student->dependents()->delete();
            
            
            $student->profile()->delete();
            
            
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
            'COE' => 'College of Engineering',
            'CME' => 'College of Maritime Education',
            'COB' => 'College of Business',
            'COA' => 'College of Architecture',
            'SFA' => 'School of Fine Arts',
            'CASE' => 'College of Arts, Sciences and Education',
        ];
    }

    /**
     * Get programs list based on department/college
     */
    private function getPrograms()
    {
        return [
            // College of Engineering
            'BSCE' => 'Bachelor of Science in Civil Engineering',
            'BSEE' => 'Bachelor of Science in Electrical Engineering',
            'BSGE' => 'Bachelor of Science in Geodetic Engineering',
            'BSEcE' => 'Bachelor of Science in Electronics Engineering',
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSCS' => 'Bachelor of Science in Computer Science',
            'ACS' => 'Associate in Computer Science',
            'BSME' => 'Bachelor of Science in Mechanical Engineering',
            'BSAeroE' => 'Bachelor of Science in Aeronautical Engineering',
            'BSAMT' => 'Bachelor of Science in Aircraft Maintenance Technology',
            'CAMT' => 'Certificate in Aircraft Maintenance Technology',
            
            // College of Maritime Education
            'BSMarE' => 'Bachelor of Science in Marine Engineering',
            'BSMarT' => 'Bachelor of Science in Marine Transportation',
            
            // College of Business
            'BSTM' => 'Bachelor of Science in Tourism Management',
            'BSCA' => 'Bachelor of Science in Customs Administration',
            'BSBA' => 'Bachelor of Science in Business Administration',
            
            // College of Architecture
            'BSArch' => 'Bachelor of Science in Architecture',
            
            // School of Fine Arts
            'BFA-VC' => 'Bachelor of Fine Arts major in Visual Communication',
            
            // College of Arts, Sciences and Education
            'BAC' => 'Bachelor of Arts in Communication',
        ];
    }

    /**
     * Get department to programs mapping
     */
    private function getDepartmentProgramsMapping()
    {
        return [
            'COE' => ['BSCE', 'BSEE', 'BSGE', 'BSEcE', 'BSIT', 'BSCS', 'ACS', 'BSME', 'BSAeroE', 'BSAMT', 'CAMT'],
            'CME' => ['BSMarE', 'BSMarT'],
            'COB' => ['BSTM', 'BSCA', 'BSBA'],
            'COA' => ['BSArch'],
            'SFA' => ['BFA-VC'],
            'CASE' => ['BAC'],
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
