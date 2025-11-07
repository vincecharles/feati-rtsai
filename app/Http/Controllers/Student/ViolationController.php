<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Violation;
use App\Models\ViolationType;
use App\Models\ViolationEvidence;
use App\Models\ViolationAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViolationController extends Controller
{
    /**
     * Display a listing of student's violations
     */
    public function index(Request $request)
    {
        $query = Violation::with(['violationType', 'reporter', 'resolver', 'evidence'])
            ->where('student_id', Auth::id());

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('incident_location', 'like', "%{$search}%")
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

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('incident_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('incident_date', '<=', $request->date_to);
        }

        $violations = $query->orderBy('incident_date', 'desc')->paginate(15);

        if ($request->expectsJson()) {
            return $this->successResponse('Violations retrieved successfully', [
                'violations' => $violations->items(),
                'pagination' => $this->getPaginationData($violations)
            ]);
        }

        return view('student.violations.index', compact('violations'));
    }

    /**
     * Display the specified violation
     */
    public function show(Violation $violation)
    {
        // Ensure student can only view their own violations
        if ($violation->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to violation record.');
        }

        $violation->load(['violationType', 'reporter', 'resolver', 'evidence', 'appeals']);

        if (request()->expectsJson()) {
            return $this->successResponse('Violation retrieved successfully', [
                'violation' => $violation
            ]);
        }

        return view('student.violations.show', compact('violation'));
    }

    /**
     * Show the form for creating a violation appeal
     */
    public function createAppeal(Violation $violation)
    {
        if ($violation->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to violation record.');
        }

        if ($violation->status === 'resolved' || $violation->status === 'dismissed') {
            return back()->with('error', 'Cannot appeal a resolved or dismissed violation.');
        }

        return view('student.violations.appeal', compact('violation'));
    }

    /**
     * Store a violation appeal
     */
    public function storeAppeal(Request $request, Violation $violation)
    {
        if ($violation->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to violation record.');
        }

        $request->validate([
            'appeal_reason' => 'required|string|max:2000',
            'supporting_evidence' => 'nullable|string|max:1000',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $appeal = ViolationAppeal::create([
                'violation_id' => $violation->id,
                'student_id' => Auth::id(),
                'appeal_reason' => $request->appeal_reason,
                'supporting_evidence' => $request->supporting_evidence,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            // Handle evidence file uploads
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('violation-appeals/' . $appeal->id, $filename, 'public');
                    
                    ViolationEvidence::create([
                        'violation_id' => $violation->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => 'Appeal evidence',
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Appeal submitted successfully', [
                    'appeal' => $appeal
                ]);
            }

            return redirect()->route('student.violations.show', $violation)
                ->with('success', 'Appeal submitted successfully. It will be reviewed by the administration.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to submit appeal: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to submit appeal: ' . $e->getMessage());
        }
    }

    /**
     * Get violation statistics for student
     */
    public function getStatistics(Request $request)
    {
        try {
            $studentId = Auth::id();
            
            $stats = [
                'total_violations' => Violation::where('student_id', $studentId)->count(),
                'pending_violations' => Violation::where('student_id', $studentId)->where('status', 'pending')->count(),
                'active_violations' => Violation::where('student_id', $studentId)->where('status', 'active')->count(),
                'resolved_violations' => Violation::where('student_id', $studentId)->where('status', 'resolved')->count(),
                'citation_violations' => Violation::where('student_id', $studentId)->where('sanction', 'Disciplinary Citation (E)')->count(),
                'suspension_violations' => Violation::where('student_id', $studentId)->where('sanction', 'Suspension (D)')->count(),
                'preventive_suspension_violations' => Violation::where('student_id', $studentId)->where('sanction', 'Preventive Suspension (C)')->count(),
                'exclusion_violations' => Violation::where('student_id', $studentId)->where('sanction', 'Exclusion (B)')->count(),
                'expulsion_violations' => Violation::where('student_id', $studentId)->where('sanction', 'Expulsion (A)')->count(),
                'total_penalty_points' => Violation::where('student_id', $studentId)->sum('penalty'),
                'pending_appeals' => ViolationAppeal::where('student_id', $studentId)->where('status', 'pending')->count(),
            ];

            return $this->successResponse('Violation statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve violation statistics: ' . $e->getMessage());
        }
    }

    /**
     * Get violation types
     */
    public function getViolationTypes()
    {
        try {
            $types = ViolationType::active()
                ->select('id', 'name', 'description', 'severity_level', 'penalty_points')
                ->orderBy('severity_level')
                ->get();

            return $this->successResponse('Violation types retrieved successfully', [
                'types' => $types
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve violation types: ' . $e->getMessage());
        }
    }

    /**
     * Download violation evidence
     */
    public function downloadEvidence(ViolationEvidence $evidence)
    {
        if ($evidence->violation->student_id !== Auth::id()) {
            abort(403, 'Unauthorized access to evidence file.');
        }

        if (!Storage::disk('public')->exists($evidence->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($evidence->file_path, $evidence->file_name);
    }
}
