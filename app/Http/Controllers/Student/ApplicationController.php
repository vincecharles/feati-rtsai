<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Display a listing of student's applications
     */
    public function index(Request $request)
    {
        $query = Application::where('user_id', Auth::id());

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->where('department', $request->department);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->expectsJson()) {
            return $this->successResponse('Applications retrieved successfully', [
                'applications' => $applications->items(),
                'pagination' => $this->getPaginationData($applications)
            ]);
        }

        return view('student.applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application
     */
    public function create()
    {
        $departments = $this->getDepartments();
        $courses = $this->getCourses();
        
        return view('student.applications.create', compact('departments', 'courses'));
    }

    /**
     * Store a newly created application
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'current_address' => 'required|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'department' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:5',
            'previous_school' => 'nullable|string|max:255',
            'previous_course' => 'nullable|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Generate application number
            $applicationNumber = $this->generateApplicationNumber();

            // Create application
            $application = Application::create([
                'application_number' => $applicationNumber,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'],
                'suffix' => $validated['suffix'],
                'email' => Auth::user()->email,
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'place_of_birth' => $validated['place_of_birth'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'current_address' => $validated['current_address'],
                'permanent_address' => $validated['permanent_address'],
                'emergency_name' => $validated['emergency_name'],
                'emergency_relationship' => $validated['emergency_relationship'],
                'emergency_phone' => $validated['emergency_phone'],
                'emergency_address' => $validated['emergency_address'],
                'department' => $validated['department'],
                'course' => $validated['course'],
                'year_level' => $validated['year_level'],
                'previous_school' => $validated['previous_school'],
                'previous_course' => $validated['previous_course'],
                'gpa' => $validated['gpa'],
                'status' => 'pending',
                'remarks' => $validated['remarks'],
                'user_id' => Auth::id(),
            ]);

            // Handle file uploads
            if ($request->hasFile('documents')) {
                $this->handleDocumentUploads($application, $request->file('documents'));
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Application submitted successfully', [
                    'application' => $application,
                    'application_number' => $applicationNumber
                ]);
            }

            return redirect()->route('student.applications.show', $application)
                ->with('success', 'Application submitted successfully. Application Number: ' . $applicationNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to submit application: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified application
     */
    public function show(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application record.');
        }

        if (request()->expectsJson()) {
            return $this->successResponse('Application retrieved successfully', [
                'application' => $application
            ]);
        }

        return view('student.applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified application
     */
    public function edit(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application record.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Cannot edit a processed application.');
        }

        $departments = $this->getDepartments();
        $courses = $this->getCourses();

        return view('student.applications.edit', compact('application', 'departments', 'courses'));
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application record.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Cannot edit a processed application.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'current_address' => 'required|string',
            'permanent_address' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_address' => 'nullable|string',
            'department' => 'required|string|max:100',
            'course' => 'required|string|max:100',
            'year_level' => 'required|integer|min:1|max:5',
            'previous_school' => 'nullable|string|max:255',
            'previous_course' => 'nullable|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $application->update($validated);

            // Handle file uploads
            if ($request->hasFile('documents')) {
                $this->handleDocumentUploads($application, $request->file('documents'));
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Application updated successfully', [
                    'application' => $application
                ]);
            }

            return redirect()->route('student.applications.show', $application)
                ->with('success', 'Application updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to update application: ' . $e->getMessage());
            }
            
            return back()->withInput()
                ->with('error', 'Failed to update application: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified application
     */
    public function destroy(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to application record.');
        }

        if ($application->status !== 'pending') {
            return back()->with('error', 'Cannot delete a processed application.');
        }

        try {
            // Delete associated files
            $this->deleteApplicationFiles($application);
            
            $application->delete();

            if (request()->expectsJson()) {
                return $this->successResponse('Application deleted successfully');
            }

            return redirect()->route('student.applications.index')
                ->with('success', 'Application deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return $this->errorResponse('Failed to delete application: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to delete application: ' . $e->getMessage());
        }
    }

    /**
     * Get application statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $stats = [
                'total_applications' => Application::where('user_id', $userId)->count(),
                'pending_applications' => Application::where('user_id', $userId)->where('status', 'pending')->count(),
                'approved_applications' => Application::where('user_id', $userId)->where('status', 'approved')->count(),
                'rejected_applications' => Application::where('user_id', $userId)->where('status', 'rejected')->count(),
                'under_review_applications' => Application::where('user_id', $userId)->where('status', 'under_review')->count(),
            ];

            return $this->successResponse('Application statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve application statistics: ' . $e->getMessage());
        }
    }

    /**
     * Generate application number
     */
    private function generateApplicationNumber()
    {
        $year = date('Y');
        $lastApplication = Application::where('application_number', 'like', $year . '-%')
            ->orderBy('application_number', 'desc')
            ->first();

        if ($lastApplication) {
            $lastNumber = (int) substr($lastApplication->application_number, 5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
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
     * Get courses list
     */
    private function getCourses()
    {
        return [
            'BSIT' => 'Bachelor of Science in Information Technology',
            'BSCS' => 'Bachelor of Science in Computer Science',
            'BSCE' => 'Bachelor of Science in Civil Engineering',
            'BSEE' => 'Bachelor of Science in Electrical Engineering',
            'BSME' => 'Bachelor of Science in Mechanical Engineering',
            'BSBA' => 'Bachelor of Science in Business Administration',
            'BSA' => 'Bachelor of Science in Accountancy',
            'BSN' => 'Bachelor of Science in Nursing',
            'BSE' => 'Bachelor of Science in Education',
        ];
    }

    /**
     * Handle document uploads
     */
    private function handleDocumentUploads($application, $files)
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('applications/' . $application->id, $filename, 'public');
            $uploadedFiles[] = $path;
        }
        
        $application->update(['documents' => json_encode($uploadedFiles)]);
    }

    /**
     * Delete application files
     */
    private function deleteApplicationFiles($application)
    {
        if ($application->documents) {
            $files = json_decode($application->documents, true);
            foreach ($files as $file) {
                if (file_exists(storage_path('app/public/' . $file))) {
                    unlink(storage_path('app/public/' . $file));
                }
            }
        }
    }
}
