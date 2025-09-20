<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\ViolationType;
use App\Models\User;
use App\Models\ViolationEvidence;
use App\Models\ViolationAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViolationController extends Controller
{
    /**
     * Display a listing of violations
     */
    public function index(Request $request)
    {
        $query = Violation::with(['student', 'violationType', 'reporter', 'resolver', 'evidence']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('incident_location', 'like', "%{$search}%")
                  ->orWhereHas('student', function($studentQuery) use ($search) {
                      $studentQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('violationType', function($typeQuery) use ($search) {
                      $typeQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->has('severity') && $request->severity) {
            $query->where('severity_level', $request->severity);
        }

        // Filter by student
        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('incident_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('incident_date', '<=', $request->date_to);
        }

        $violations = $query->orderBy('incident_date', 'desc')->paginate(15);

        // Get students for filter dropdown
        $students = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->select('id', 'name', 'email')->get();

        if ($request->expectsJson()) {
            return $this->successResponse('Violations retrieved successfully', [
                'violations' => $violations->items(),
                'pagination' => $this->getPaginationData($violations)
            ]);
        }

        return view('violations.index', compact('violations', 'students'));
    }

    /**
     * Show the form for creating a new violation
     */
    public function create()
    {
        $students = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->select('id', 'name', 'email')->get();
        
        $violationTypes = ViolationType::active()->get();
        
        return view('violations.create', compact('students', 'violationTypes'));
    }

    /**
     * Store a newly created violation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'incident_date' => 'required|date',
            'incident_location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'severity_level' => 'required|in:minor,moderate,major,severe',
            'penalty' => 'nullable|integer|min:0',
            'penalty_description' => 'nullable|string|max:1000',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Create violation
            $violation = Violation::create([
                'student_id' => $validated['student_id'],
                'violation_type_id' => $validated['violation_type_id'],
                'reported_by' => auth()->id(),
                'incident_date' => $validated['incident_date'],
                'incident_location' => $validated['incident_location'],
                'description' => $validated['description'],
                'severity_level' => $validated['severity_level'],
                'status' => 'pending',
                'penalty' => $validated['penalty'] ?? 0,
                'penalty_description' => $validated['penalty_description'],
                'created_by' => auth()->id(),
            ]);

            // Handle evidence file uploads
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('violations/' . $violation->id, $filename, 'public');
                    
                    ViolationEvidence::create([
                        'violation_id' => $violation->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => 'Evidence file',
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Violation created successfully', [
                    'violation' => $violation
                ]);
            }

            return redirect()->route('violations.show', $violation)
                ->with('success', 'Violation created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
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
        $violation->load(['student', 'violationType', 'reporter', 'resolver', 'evidence', 'appeals.student']);
        
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
        })->select('id', 'name', 'email')->get();
        
        $violationTypes = ViolationType::active()->get();
        
        return view('violations.edit', compact('violation', 'students', 'violationTypes'));
    }

    /**
     * Update the specified violation
     */
    public function update(Request $request, Violation $violation)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'incident_date' => 'required|date',
            'incident_location' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'severity_level' => 'required|in:minor,moderate,major,severe',
            'status' => 'required|in:pending,active,resolved,dismissed',
            'penalty' => 'nullable|integer|min:0',
            'penalty_description' => 'nullable|string|max:1000',
            'resolution_notes' => 'nullable|string|max:1000',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $violation->update([
                'student_id' => $validated['student_id'],
                'violation_type_id' => $validated['violation_type_id'],
                'incident_date' => $validated['incident_date'],
                'incident_location' => $validated['incident_location'],
                'description' => $validated['description'],
                'severity_level' => $validated['severity_level'],
                'status' => $validated['status'],
                'penalty' => $validated['penalty'] ?? 0,
                'penalty_description' => $validated['penalty_description'],
                'resolution_notes' => $validated['resolution_notes'],
                'updated_by' => auth()->id(),
            ]);

            // Handle evidence file uploads
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('violations/' . $violation->id, $filename, 'public');
                    
                    ViolationEvidence::create([
                        'violation_id' => $violation->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => 'Evidence file',
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Violation updated successfully', [
                    'violation' => $violation
                ]);
            }

            return redirect()->route('violations.show', $violation)
                ->with('success', 'Violation updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
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
            // Delete evidence files
            foreach ($violation->evidence as $evidence) {
                if (Storage::disk('public')->exists($evidence->file_path)) {
                    Storage::disk('public')->delete($evidence->file_path);
                }
            }
            
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
            'resolution_notes' => 'required|string|max:1000',
            'penalty' => 'nullable|integer|min:0',
            'penalty_description' => 'nullable|string|max:1000',
        ]);

        try {
            $violation->update([
                'status' => 'resolved',
                'resolution_notes' => $request->resolution_notes,
                'penalty' => $request->penalty ?? $violation->penalty,
                'penalty_description' => $request->penalty_description ?? $violation->penalty_description,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
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
                'active_violations' => Violation::where('status', 'active')->count(),
                'resolved_violations' => Violation::where('status', 'resolved')->count(),
                'minor_violations' => Violation::where('severity_level', 'minor')->count(),
                'moderate_violations' => Violation::where('severity_level', 'moderate')->count(),
                'major_violations' => Violation::where('severity_level', 'major')->count(),
                'severe_violations' => Violation::where('severity_level', 'severe')->count(),
                'total_penalty_points' => Violation::sum('penalty'),
                'pending_appeals' => ViolationAppeal::where('status', 'pending')->count(),
            ];

            return $this->successResponse('Violation statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve violation statistics: ' . $e->getMessage());
        }
    }
}
