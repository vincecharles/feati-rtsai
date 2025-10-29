<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications
     */
    public function index(Request $request)
    {
        $query = Application::with(['user.profile', 'reviewedBy']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
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

        return view('applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application
     */
    public function create()
    {
        $departments = $this->getDepartments();
        $courses = $this->getCourses();
        
        return view('applications.create', compact('departments', 'courses'));
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
            'email' => 'required|email|unique:users,email',
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
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
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
                'email' => $validated['email'],
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
                'user_id' => auth()->id(),
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

            return redirect()->route('applications.show', $application)
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
        $application->load(['user.profile', 'reviewedBy']);
        
        if (request()->expectsJson()) {
            return $this->successResponse('Application retrieved successfully', [
                'application' => $application
            ]);
        }
        
        return view('applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified application
     */
    public function edit(Application $application)
    {
        $departments = $this->getDepartments();
        $courses = $this->getCourses();
        
        return view('applications.edit', compact('application', 'departments', 'courses'));
    }

    /**
     * Update the specified application
     */
    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($application->user_id)],
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
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
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

            return redirect()->route('applications.show', $application)
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
        try {
            // Delete associated files
            $this->deleteApplicationFiles($application);
            
            $application->delete();

            if (request()->expectsJson()) {
                return $this->successResponse('Application deleted successfully');
            }

            return redirect()->route('applications.index')
                ->with('success', 'Application deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return $this->errorResponse('Failed to delete application: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to delete application: ' . $e->getMessage());
        }
    }

    /**
     * Review application (approve/reject)
     */
    public function review(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,under_review',
            'review_notes' => 'nullable|string|max:1000',
            'reviewer_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $application->update([
                'status' => $request->status,
                'review_notes' => $request->review_notes,
                'reviewer_notes' => $request->reviewer_notes,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // If approved, create user account
            if ($request->status === 'approved') {
                $this->createUserFromApplication($application);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return $this->successResponse('Application reviewed successfully', [
                    'application' => $application
                ]);
            }

            return redirect()->route('applications.show', $application)
                ->with('success', 'Application ' . $request->status . ' successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to review application: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to review application: ' . $e->getMessage());
        }
    }

    /**
     * Get application statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_applications' => Application::count(),
                'pending_applications' => Application::where('status', 'pending')->count(),
                'approved_applications' => Application::where('status', 'approved')->count(),
                'rejected_applications' => Application::where('status', 'rejected')->count(),
                'under_review_applications' => Application::where('status', 'under_review')->count(),
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
            'COE' => 'College of Engineering',
            'CME' => 'College of Maritime Education',
            'COB' => 'College of Business',
            'COA' => 'College of Architecture',
            'SFA' => 'School of Fine Arts',
            'CASE' => 'College of Arts, Sciences and Education',
        ];
    }

    /**
     * Get courses list
     */
    private function getCourses()
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

    /**
     * Create user from approved application
     */
    private function createUserFromApplication($application)
    {
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            throw new \Exception('Student role not found');
        }

        $user = User::create([
            'name' => trim($application->first_name . ' ' . $application->last_name),
            'email' => $application->email,
            'password' => bcrypt('password123'), // Default password
            'role_id' => $studentRole->id,
            'status' => 'active',
        ]);

        // Create student profile
        $user->profile()->create([
            'employee_number' => $application->application_number,
            'date_hired' => now(),
            'last_name' => $application->last_name,
            'first_name' => $application->first_name,
            'middle_name' => $application->middle_name,
            'suffix' => $application->suffix,
            'sex' => $application->sex,
            'date_of_birth' => $application->date_of_birth,
            'place_of_birth' => $application->place_of_birth,
            'civil_status' => $application->civil_status,
            'nationality' => $application->nationality,
            'mobile' => $application->phone,
            'current_address' => $application->current_address,
            'permanent_address' => $application->permanent_address,
            'emergency_name' => $application->emergency_name,
            'emergency_relationship' => $application->emergency_relationship,
            'emergency_phone' => $application->emergency_phone,
            'emergency_address' => $application->emergency_address,
            'department' => $application->department,
            'course' => $application->course,
            'year_level' => $application->year_level,
        ]);

        return $user;
    }
}
