<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request with permission checking.
     * Supports role-based and department-based permissions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $permission The permission to check (e.g., 'super_admin', 'security', 'dept_head', 'osa')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $roleName = $user->role?->name;
        $userDepartment = $user->profile?->department;

        // Check permission based on role and department
        $hasPermission = $this->hasPermission($roleName, $permission, $userDepartment);

        if (!$hasPermission) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }

    /**
     * Determine if user has permission
     */
    private function hasPermission(?string $roleName, string $permission, ?string $userDepartment): bool
    {
        $permissions = [
            'admin' => ['admin', 'view_all_violations', 'view_all_reports', 'manage_all_employees', 'create_violation', 'edit_violation', 'delete_violation', 'manage_users', 'manage_roles', 'manage_settings', 'view_all_departments'],
            'security' => ['security', 'create_violation', 'edit_own_violations', 'view_violations'],
            'dept_head' => ['dept_head', 'view_department', 'manage_department_employees', 'view_department_violations'],
            'osa' => ['osa', 'create_violation', 'edit_violation', 'view_violations', 'resolve_violations'],
            'teacher' => ['teacher', 'view_own_profile'],
        ];

        $userPermissions = $permissions[$roleName] ?? [];

        return in_array($permission, $userPermissions);
    }
}
