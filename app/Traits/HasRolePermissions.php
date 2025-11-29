<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasRolePermissions
{
    /**
     * Get permission matrix for all roles
     */
    public static function getPermissionMatrix()
    {
        return [
            'admin' => [
                'view_all_students' => true,
                'view_all_employees' => true,
                'view_all_violations' => true,
                'create_violation' => true,
                'edit_violation' => true,
                'delete_violation' => true,
                'manage_users' => true,
                'manage_roles' => true,
                'view_reports' => true,
                'manage_settings' => true,
                'view_all_departments' => true,
            ],
            'department_head' => [
                'view_department' => true,
                'edit_department_students' => true,
                'view_department_violations' => true,
                'approve_applications' => true,
                'view_department_only' => true,
            ],
            'program_head' => [
                'view_program' => true,
                'view_program_students' => true,
                'edit_program_students' => true,
                'view_program_violations' => true,
                'view_program_only' => true,
                // Violation permissions (department-restricted)
                'create_violation' => true,
                'view_violations' => true,
                // Student view permissions (department-restricted)
                'view_students' => true,
                'view_student_profile' => true,
            ],
            'security' => [
                'create_violation' => true,
                'edit_violation' => true,
                'view_violations' => true,
                'view_students_with_violations' => true,
                'generate_violation_reports' => true,
            ],
            'osa' => [
                // Violation CRUD permissions
                'view_all_violations' => true,
                'create_violation' => true,
                'edit_violation' => true,
                'delete_violation' => true,
                // Verdict permissions
                'resolve_violation' => true,
                'dismiss_violation' => true,
                'give_verdict' => true,
                // Student view permissions
                'view_all_students' => true,
                'view_student_profile' => true,
                'view_student_violations' => true,
                // Other OSA permissions
                'approve_applications' => true,
                'manage_events' => true,
                'view_student_activities' => true,
                'view_all_departments' => true,
                'view_reports' => true,
            ],
            'teacher' => [
                'view_students' => true,
                'report_violations' => true,
            ],
            'student' => [
                'view_own_profile' => true,
                // Violation permissions for students
                'create_violation' => true,           // Can file complaints against other students
                'view_own_violations' => true,        // Can view violations against them
                'view_filed_complaints' => true,      // Can view complaints they filed
                'select_complaint_target' => true,    // Can select who to file complaint against
                // Other student permissions
                'appeal_violations' => true,
                'submit_applications' => true,
            ],
        ];
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permission)
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return false;
        }

        $matrix = self::getPermissionMatrix();
        $roleName = $user->role->name;

        return isset($matrix[$roleName][$permission]) && $matrix[$roleName][$permission];
    }

    /**
     * Check if user is viewing their own department
     */
    public function isOwnDepartment($department)
    {
        $user = Auth::user();
        if (!$user || !$user->profile) {
            return false;
        }

        return $user->profile->department === $department;
    }

    /**
     * Filter query by role access
     */
    public function filterByRoleAccess($query, $model = null)
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return $query->whereRaw('1=0'); // Return empty
        }

        switch ($user->role->name) {
            case 'admin':
            case 'osa':
                // Admin and OSA have full access to all data
                return $query; 
            
            case 'department_head':
                // Department heads see their department only
                if (method_exists($query->getModel(), 'program')) {
                    return $query->where('program', $user->profile->department);
                }
                return $query->whereHas('profile', function($q) use ($user) {
                    $q->where('department', $user->profile->department);
                });
            
            case 'program_head':
                // Program heads see their program only
                if (method_exists($query->getModel(), 'program')) {
                    return $query->where('program', $user->profile->department);
                }
                return $query->whereHas('profile', function($q) use ($user) {
                    $q->where('department', $user->profile->department);
                });
            
            case 'security':
                // Security sees only students with violations
                return $query->whereHas('violations');
            
            case 'student':
                // Students see only themselves
                return $query->where('id', $user->id);
            
            default:
                return $query;
        }
    }

    /**
     * Get accessible departments for user
     */
    public function getAccessibleDepartments()
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        switch ($user->role->name ?? null) {
            case 'super_admin':
            case 'admin':
            case 'osa':
                // All programs for admin and OSA
                return [
                    'BS Civil Engineering',
                    'BS Electrical Engineering',
                    'BS Geodetic Engineering',
                    'BS Electronics Engineering',
                    'BS Information Technology',
                    'BS Computer Science',
                    'Associate in Computer Science',
                    'BS Mechanical Engineering',
                    'BS Aeronautical Engineering',
                    'BS Aircraft Maintenance Technology',
                    'Certificate in Aircraft Maintenance Technology',
                    'BS Marine Engineering',
                    'BS Marine Transportation',
                    'BS Tourism Management',
                    'BS Customs Administration',
                    'BS Business Administration',
                    'BS Architecture',
                    'BFA major in Visual Communication',
                    'BA in Communication',
                ];
            
            case 'department_head':
            case 'program_head':
                // Only their department
                return $user->profile->department ? [$user->profile->department] : [];
            
            case 'security':
                // All programs for violation tracking
                return [
                    'BS Civil Engineering',
                    'BS Electrical Engineering',
                    'BS Geodetic Engineering',
                    'BS Electronics Engineering',
                    'BS Information Technology',
                    'BS Computer Science',
                    'Associate in Computer Science',
                    'BS Mechanical Engineering',
                    'BS Aeronautical Engineering',
                    'BS Aircraft Maintenance Technology',
                    'Certificate in Aircraft Maintenance Technology',
                    'BS Marine Engineering',
                    'BS Marine Transportation',
                    'BS Tourism Management',
                    'BS Customs Administration',
                    'BS Business Administration',
                    'BS Architecture',
                    'BFA major in Visual Communication',
                    'BA in Communication',
                ];
            
            case 'student':
                // Only their own program
                return $user->program ? [$user->program] : [];
            
            default:
                return [];
        }
    }
}
