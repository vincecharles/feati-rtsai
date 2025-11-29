<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationType;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SecurityMobileController extends Controller
{
    /**
     * Mobile login for security personnel
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user is security personnel
            if ($user->role?->name !== 'security') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. This app is only for security personnel.'
                ], 403);
            }

            // Generate token (using Sanctum or similar)
            $token = $user->createToken('security-mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->name,
                        'employee_number' => $user->profile->employee_number ?? null,
                    ],
                    'token' => $token,
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Logout
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Scan QR code or barcode to get student information
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function scanStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $code = $request->input('code');

        // Try to find student by QR code first, then by student number
        $student = User::whereHas('studentProfile', function($query) use ($code) {
            $query->where('qr_code', $code)
                  ->orWhere('student_number', $code);
        })->with(['studentProfile', 'violations' => function($query) {
            $query->latest()->take(5);
        }])->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Get violation statistics
        $violationStats = [
            'total' => $student->violations()->count(),
            'pending' => $student->violations()->where('status', 'pending')->count(),
            'under_review' => $student->violations()->where('status', 'under_review')->count(),
            'resolved' => $student->violations()->where('status', 'resolved')->count(),
            'dismissed' => $student->violations()->where('status', 'dismissed')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Student found',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'student_number' => $student->studentProfile->student_number,
                    'department' => $student->studentProfile->department,
                    'program' => $student->studentProfile->program,
                    'year_level' => $student->studentProfile->year_level,
                    'mobile' => $student->studentProfile->mobile,
                    'photo_url' => null, // Add photo URL if available
                ],
                'violations' => [
                    'statistics' => $violationStats,
                    'recent' => $student->violations->map(function($violation) {
                        return [
                            'id' => $violation->id,
                            'type' => $violation->violationType->name ?? 'N/A',
                            'description' => $violation->description,
                            'date' => $violation->date_of_violation,
                            'status' => $violation->status,
                        ];
                    }),
                ]
            ]
        ], 200);
    }

    /**
     * Get violation types
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViolationTypes()
    {
        $types = ViolationType::select('id', 'name', 'description', 'severity_level')
            ->orderBy('severity_level', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ], 200);
    }

    /**
     * Create a new violation
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createViolation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'description' => 'required|string|max:1000',
            'date_of_violation' => 'required|date',
            'time_of_violation' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'witnesses' => 'nullable|string|max:500',
            'evidence_files' => 'nullable|array',
            'evidence_files.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $violation = Violation::create([
                'student_id' => $request->student_id,
                'violation_type_id' => $request->violation_type_id,
                'description' => $request->description,
                'date_of_violation' => $request->date_of_violation,
                'time_of_violation' => $request->time_of_violation,
                'location' => $request->location,
                'witnesses' => $request->witnesses,
                'reported_by' => $request->user()->id,
                'status' => 'pending',
            ]);

            // Handle evidence file uploads
            if ($request->hasFile('evidence_files')) {
                foreach ($request->file('evidence_files') as $file) {
                    $path = $file->store('violations/evidence', 'public');
                    
                    $violation->evidence()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                        'uploaded_by' => $request->user()->id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Violation recorded successfully',
                'data' => [
                    'violation' => [
                        'id' => $violation->id,
                        'student_id' => $violation->student_id,
                        'description' => $violation->description,
                        'status' => $violation->status,
                        'date_of_violation' => $violation->date_of_violation,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create violation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security personnel's violation history
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMyViolations(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $status = $request->input('status');

        $query = Violation::where('reported_by', $request->user()->id)
            ->with(['student.studentProfile', 'violationType'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $violations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $violations
        ], 200);
    }

    /**
     * Get specific violation details
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViolation($id)
    {
        $violation = Violation::with([
            'student.studentProfile',
            'violationType',
            'reporter.profile',
            'evidence',
            'notes.user'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $violation
        ], 200);
    }

    /**
     * Search students by name or student number
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStudents(Request $request)
    {
        $search = $request->input('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ], 200);
        }

        $students = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })
        ->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('studentProfile', function($q) use ($search) {
                  $q->where('student_number', 'like', "%{$search}%");
              });
        })
        ->with('studentProfile')
        ->limit(10)
        ->get()
        ->map(function($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_number' => $student->studentProfile->student_number,
                'department' => $student->studentProfile->department,
                'program' => $student->studentProfile->program,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $students
        ], 200);
    }

    /**
     * Get dashboard statistics for security personnel
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        $userId = $request->user()->id;

        $stats = [
            'today' => Violation::where('reported_by', $userId)
                ->whereDate('created_at', today())
                ->count(),
            'this_week' => Violation::where('reported_by', $userId)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Violation::where('reported_by', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total' => Violation::where('reported_by', $userId)->count(),
            'by_status' => [
                'pending' => Violation::where('reported_by', $userId)
                    ->where('status', 'pending')
                    ->count(),
                'under_review' => Violation::where('reported_by', $userId)
                    ->where('status', 'under_review')
                    ->count(),
                'resolved' => Violation::where('reported_by', $userId)
                    ->where('status', 'resolved')
                    ->count(),
                'dismissed' => Violation::where('reported_by', $userId)
                    ->where('status', 'dismissed')
                    ->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }
}
