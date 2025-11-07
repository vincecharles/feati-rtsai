<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\User;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    /**
     * Display a listing of violations
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;

        $query = Violation::with(['student', 'reporter', 'notes.user']);

        // Filter by department if specified
        $filterDepartment = $request->has('department') && $request->department ? $request->department : null;

        // Role-based filtering
        if ($userRole === 'department_head' && $userDepartment) {
            // Department heads can only see violations for their department's students
            $query->whereHas('student', function($q) use ($userDepartment) {
                $q->where('program', $userDepartment);
            });
        } elseif ($userRole === 'program_head' && $userDepartment) {
            // Program heads can only see violations for their program's students
            $query->whereHas('student', function($q) use ($userDepartment) {
                $q->where('program', $userDepartment);
            });
        } elseif ($userRole === 'teacher') {
            // Teachers can't view violations (implement if needed)
            abort(403, 'You do not have permission to view violations.');
        } elseif ($userRole === 'student') {
            // Students can only see their own violations
            $query->where('student_id', $user->id);
        } else if ($filterDepartment && in_array($userRole, ['admin', 'osa', 'security'])) {
            // Super Admin, OSA, Security can filter by department
            $query->whereHas('student', function($q) use ($filterDepartment) {
                $q->where('program', $filterDepartment);
            });
        }

        // Search functionality using Algolia Scout
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            
            // Use Scout search
            $violationIds = Violation::search($search)
                ->get()
                ->pluck('id');
            
            if ($violationIds->isNotEmpty()) {
                $query->whereIn('id', $violationIds);
            } else {
                // No results found, return empty
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by sanction
        if ($request->has('sanction') && $request->sanction) {
            $query->where('sanction', $request->sanction);
        }

        // Filter by student
        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('violation_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('violation_date', '<=', $request->date_to);
        }

        $violations = $query->orderBy('violation_date', 'desc')->paginate(15);

        // Get students for filter dropdown based on role and selected department
        if ($userRole === 'dept_head' && $userDepartment) {
            $students = User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->where('program', $userDepartment)->select('id', 'name', 'email', 'program')->get();
        } else if ($filterDepartment && in_array($userRole, ['admin', 'osa', 'security'])) {
            $students = User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->where('program', $filterDepartment)->select('id', 'name', 'email', 'program')->get();
        } else {
            $students = User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->select('id', 'name', 'email', 'program')->get();
        }

        // Get all departments for the department filter dropdown
        $departments = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->select('program')->distinct()->orderBy('program')->pluck('program');

        if ($request->expectsJson()) {
            return $this->successResponse('Violations retrieved successfully', [
                'violations' => $violations->items(),
                'pagination' => $this->getPaginationData($violations)
            ]);
        }

        return view('violations.index', compact('violations', 'students', 'departments'));
    }

    /**
     * Get students for autocomplete/filter (API endpoint)
     */
    public function getStudents(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;
        $search = $request->query('q', '');

        $query = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        });

        // Role-based filtering
        if (in_array($userRole, ['department_head', 'program_head', 'teacher']) && $userDepartment) {
            // Department heads, program heads, and teachers can only see students in their program/department
            $query->where('program', $userDepartment);
        }

        // Search by name or student_id
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->select('id', 'name', 'student_id', 'email', 'program')
            ->limit(20)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id,
                    'email' => $student->email,
                    'program' => $student->program,
                    'text' => "{$student->name} ({$student->student_id})"
                ];
            });

        return response()->json($students);
    }

    /**
     * Show the form for creating a new violation
     */
    public function create()
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;
        
        // Check authorization - only these roles can create violations
        $allowedRoles = ['admin', 'osa', 'security', 'program_head', 'teacher', 'department_head'];
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'You do not have permission to create violations.');
        }
        
        // Get students based on role
        if (in_array($userRole, ['program_head', 'teacher', 'department_head']) && $userDepartment) {
            $students = User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->where('program', $userDepartment)->get();
        } else {
            $students = User::whereHas('role', function($q) {
                $q->where('name', 'student');
            })->get();
        }
        
        // Get violation types from handbook
        $violationTypes = \App\Models\ViolationType::orderBy('code')->get()->groupBy('category');
        
        return view('violations.create', compact('students', 'violationTypes'));
    }    /**
     * Store a newly created violation
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        $userDepartment = $user->profile?->department;
        
        // Check authorization - only these roles can create violations
        $allowedRoles = ['admin', 'osa', 'security', 'program_head', 'teacher', 'department_head'];
        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'You do not have permission to create violations.');
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'violation_type' => 'required|string|max:255',
            'sanction' => 'required|in:Disciplinary Citation (E),Suspension (D),Preventive Suspension (C),Exclusion (B),Expulsion (A)',
            'violation_date' => 'required|date',
            'description' => 'required|string|max:2000',
            'action_taken' => 'nullable|string|max:1000',
        ]);

        try {
            // Verify that program heads/teachers can only report violations for students in their program
            if (in_array($userRole, ['program_head', 'teacher', 'department_head']) && $userDepartment) {
                $student = User::find($validated['student_id']);
                if ($student->program !== $userDepartment) {
                    return back()->withInput()
                        ->with('error', 'You can only report violations for students in your program/department.');
                }
            }
            
            // Create violation
            $violation = Violation::create([
                'student_id' => $validated['student_id'],
                'violation_type' => $validated['violation_type'],
                'sanction' => $validated['sanction'],
                'reported_by' => auth()->id(),
                'violation_date' => $validated['violation_date'],
                'description' => $validated['description'],
                'status' => 'pending',
                'action_taken' => $validated['action_taken'],
            ]);

            if ($request->expectsJson()) {
                return $this->successResponse('Violation created successfully', [
                    'violation' => $violation
                ]);
            }

            return redirect()->route('violations.index')
                ->with('success', 'Violation created successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to create violation: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to create violation: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified violation
     */
    public function show(Violation $violation)
    {
        $violation->load(['student', 'reporter']);
        
        if (request()->expectsJson()) {
            return $this->successResponse('Violation retrieved successfully', [
                'violation' => $violation
            ]);
        }
        
        return view('violations.show', compact('violation'));
    }

    /**
     * Show the form for editing the specified violation
     */
    public function edit(Violation $violation)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        
        // Only admin, OSA can edit any violation
        // Security can only edit violations they created
        if (!in_array($userRole, ['admin', 'osa'])) {
            if ($userRole === 'security' && $violation->reported_by !== $user->id) {
                abort(403, 'Security personnel can only edit violations they reported.');
            } elseif (!in_array($userRole, ['security'])) {
                abort(403, 'You do not have permission to edit violations.');
            }
        }
        
        $violation->load(['student', 'reporter', 'notes.user']);
        
        return view('violations.edit', compact('violation'));
    }

    /**
     * Update the specified violation
     */
    public function update(Request $request, Violation $violation)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        
        // Only admin, OSA can update any violation
        // Security can only update violations they created
        if (!in_array($userRole, ['admin', 'osa'])) {
            if ($userRole === 'security' && $violation->reported_by !== $user->id) {
                abort(403, 'Security personnel can only update violations they reported.');
            } elseif (!in_array($userRole, ['security'])) {
                abort(403, 'You do not have permission to update violations.');
            }
        }
        
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'action_taken' => 'nullable|string|max:1000',
            'resolution_date' => 'nullable|date',
        ]);

        try {
            // Security personnel cannot change status to 'resolved' or 'dismissed'
            if ($userRole === 'security' && in_array($validated['status'], ['resolved', 'dismissed'])) {
                return back()->withInput()
                    ->with('error', 'Security personnel cannot resolve or dismiss violations. Only OSA and Admin can do this.');
            }
            
            $violation->update([
                'status' => $validated['status'],
                'action_taken' => $validated['action_taken'],
                'resolution_date' => $validated['resolution_date'],
            ]);

            if ($request->expectsJson()) {
                return $this->successResponse('Violation updated successfully', [
                    'violation' => $violation
                ]);
            }

            return redirect()->route('violations.index')
                ->with('success', 'Violation updated successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to update violation: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update violation: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified violation
     */
    public function destroy(Violation $violation)
    {
        $user = auth()->user();
        $userRole = $user->role?->name;
        
        // Only admin, OSA can delete any violation
        // Security can only delete violations they created
        if (!in_array($userRole, ['admin', 'osa'])) {
            if ($userRole === 'security' && $violation->reported_by !== $user->id) {
                abort(403, 'Security personnel can only delete violations they reported.');
            } elseif (!in_array($userRole, ['security'])) {
                abort(403, 'You do not have permission to delete violations.');
            }
        }
        
        try {
            $violation->delete();

            if (request()->expectsJson()) {
                return $this->successResponse('Violation deleted successfully');
            }

            return redirect()->route('violations.index')
                ->with('success', 'Violation deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return $this->errorResponse('Failed to delete violation: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to delete violation: ' . $e->getMessage());
        }
    }

    /**
     * Resolve a violation
     */
    public function resolve(Request $request, Violation $violation)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
            'action_taken' => 'nullable|string|max:1000',
        ]);

        try {
            $violation->update([
                'status' => 'resolved',
                'action_taken' => $request->action_taken ?? $violation->action_taken,
                'resolution_date' => now(),
            ]);

            // Add note
            $violation->notes()->create([
                'user_id' => auth()->id(),
                'note' => $request->note,
            ]);

            if ($request->expectsJson()) {
                return $this->successResponse('Violation resolved successfully');
            }

            return back()->with('success', 'Violation resolved successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to resolve violation: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to resolve violation: ' . $e->getMessage());
        }
    }

    /**
     * Add a note to a violation
     */
    public function addNote(Request $request, Violation $violation)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        try {
            $violation->notes()->create([
                'user_id' => auth()->id(),
                'note' => $request->note,
            ]);

            if ($request->expectsJson()) {
                return $this->successResponse('Note added successfully');
            }

            return back()->with('success', 'Note added successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to add note: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to add note: ' . $e->getMessage());
        }
    }

    /**
     * Get violation statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_violations' => Violation::count(),
                'pending_violations' => Violation::where('status', 'pending')->count(),
                'under_review_violations' => Violation::where('status', 'under_review')->count(),
                'resolved_violations' => Violation::where('status', 'resolved')->count(),
                'dismissed_violations' => Violation::where('status', 'dismissed')->count(),
                'citation_violations' => Violation::where('sanction', 'Disciplinary Citation (E)')->count(),
                'suspension_violations' => Violation::where('sanction', 'Suspension (D)')->count(),
                'preventive_suspension_violations' => Violation::where('sanction', 'Preventive Suspension (C)')->count(),
                'exclusion_violations' => Violation::where('sanction', 'Exclusion (B)')->count(),
                'expulsion_violations' => Violation::where('sanction', 'Expulsion (A)')->count(),
            ];

            return $this->successResponse('Violation statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve violation statistics: ' . $e->getMessage());
        }
    }
}
