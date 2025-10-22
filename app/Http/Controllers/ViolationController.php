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

        $query = Violation::with(['student', 'reporter']);

        // Filter by department if specified
        $filterDepartment = $request->has('department') && $request->department ? $request->department : null;

        // Role-based filtering
        if ($userRole === 'dept_head' && $userDepartment) {
            // Department heads can only see violations for their department's students
            $query->whereHas('student', function($q) use ($userDepartment) {
                $q->where('program', $userDepartment);
            });
        } elseif ($userRole === 'teacher') {
            // Teachers can't view all violations (implement if needed)
            abort(403, 'You do not have permission to view violations.');
        } else if ($filterDepartment && in_array($userRole, ['super_admin', 'osa', 'security'])) {
            // Super Admin, OSA, Security can filter by department
            $query->whereHas('student', function($q) use ($filterDepartment) {
                $q->where('program', $filterDepartment);
            });
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('violation_type', 'like', "%{$search}%")
                  ->orWhereHas('student', function($studentQuery) use ($search) {
                      $studentQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity) {
            $query->where('severity', $request->severity);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
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
        } else if ($filterDepartment && in_array($userRole, ['super_admin', 'osa', 'security'])) {
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

        // Role-based filtering for department heads
        if ($userRole === 'dept_head' && $userDepartment) {
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
        $students = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->select('id', 'name', 'email', 'student_id')->orderBy('name')->get();
        
        return view('violations.create', compact('students'));
    }

    /**
     * Store a newly created violation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'violation_type' => 'required|string|max:255',
            'level' => 'required|in:Level 1,Level 2,Level 3,Expulsion',
            'violation_date' => 'required|date',
            'description' => 'required|string|max:2000',
            'severity' => 'required|in:minor,moderate,major,severe',
            'action_taken' => 'nullable|string|max:1000',
        ]);

        try {
            // Create violation
            $violation = Violation::create([
                'student_id' => $validated['student_id'],
                'violation_type' => $validated['violation_type'],
                'level' => $validated['level'],
                'reported_by' => auth()->id(),
                'violation_date' => $validated['violation_date'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
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
        $students = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->select('id', 'name', 'email', 'student_id')->orderBy('name')->get();
        
        return view('violations.edit', compact('violation', 'students'));
    }

    /**
     * Update the specified violation
     */
    public function update(Request $request, Violation $violation)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'violation_type' => 'required|string|max:255',
            'level' => 'required|in:Level 1,Level 2,Level 3,Expulsion',
            'violation_date' => 'required|date',
            'description' => 'required|string|max:2000',
            'severity' => 'required|in:minor,moderate,major,severe',
            'status' => 'required|in:pending,under_review,resolved,dismissed',
            'action_taken' => 'nullable|string|max:1000',
            'resolution_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $violation->update([
                'student_id' => $validated['student_id'],
                'violation_type' => $validated['violation_type'],
                'level' => $validated['level'],
                'violation_date' => $validated['violation_date'],
                'description' => $validated['description'],
                'severity' => $validated['severity'],
                'status' => $validated['status'],
                'action_taken' => $validated['action_taken'],
                'resolution_date' => $validated['resolution_date'],
                'notes' => $validated['notes'],
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
            'notes' => 'required|string|max:1000',
            'action_taken' => 'nullable|string|max:1000',
        ]);

        try {
            $violation->update([
                'status' => 'resolved',
                'notes' => $request->notes,
                'action_taken' => $request->action_taken ?? $violation->action_taken,
                'resolution_date' => now(),
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
                'minor_violations' => Violation::where('severity', 'minor')->count(),
                'moderate_violations' => Violation::where('severity', 'moderate')->count(),
                'major_violations' => Violation::where('severity', 'major')->count(),
                'severe_violations' => Violation::where('severity', 'severe')->count(),
            ];

            return $this->successResponse('Violation statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve violation statistics: ' . $e->getMessage());
        }
    }
}
