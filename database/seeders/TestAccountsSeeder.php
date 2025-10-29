<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\EmployeeProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $deptHeadRole = Role::where('name', 'department_head')->first();
            $programHeadRole = Role::where('name', 'program_head')->first();
            $securityRole = Role::where('name', 'security')->first();
            $studentRole = Role::where('name', 'student')->first();

            if (!$deptHeadRole || !$programHeadRole || !$securityRole) {
                throw new \Exception('Required roles not found. Run RoleSeeder first.');
            }

            $deptHead = User::create([
                'name' => 'Dr. Maria Santos',
                'email' => 'dept.head.cs@feati.edu',
                'password' => Hash::make('DeptHead123!'),
                'role_id' => $deptHeadRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $deptHead->profile()->create([
                'employee_number' => 'EMP-2025-001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'department' => 'College of Engineering',
                'position' => 'Department Head',
                'date_hired' => now(),
            ]);

            $programHead = User::create([
                'name' => 'Eng. Juan Cruz',
                'email' => 'program.head.me@feati.edu',
                'password' => Hash::make('ProgHead123!'),
                'role_id' => $programHeadRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $programHead->profile()->create([
                'employee_number' => 'EMP-2025-002',
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'department' => 'BS Mechanical Engineering',
                'position' => 'Program Head',
                'date_hired' => now(),
            ]);

            $security = User::create([
                'name' => 'Security Officer Raul',
                'email' => 'security.raul@feati.edu',
                'password' => Hash::make('Security123!'),
                'role_id' => $securityRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $security->profile()->create([
                'employee_number' => 'EMP-2025-003',
                'first_name' => 'Raul',
                'last_name' => 'Reyes',
                'department' => 'Security',
                'position' => 'Security Personnel',
                'date_hired' => now(),
            ]);

            $student = User::create([
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.dela.cruz@feati.edu',
                'password' => Hash::make('Student123!'),
                'role_id' => $studentRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'student_id' => 'STU-2025-001',
                'program' => 'BS Information Technology',
                'year_level' => 1,
            ]);

            DB::commit();

            $this->command->info('Test accounts created successfully!');
            $this->command->info('');
            $this->command->info('Department Head Account:');
            $this->command->info('  Email: dept.head.cs@feati.edu');
            $this->command->info('  Password: DeptHead123!');
            $this->command->info('  Department: College of Engineering');
            $this->command->info('');
            $this->command->info('Program Head Account:');
            $this->command->info('  Email: program.head.me@feati.edu');
            $this->command->info('  Password: ProgHead123!');
            $this->command->info('  Department: College of Engineering');
            $this->command->info('');
            $this->command->info('Security Account:');
            $this->command->info('  Email: security.raul@feati.edu');
            $this->command->info('  Password: Security123!');
            $this->command->info('');
            $this->command->info('Student Account:');
            $this->command->info('  Email: juan.dela.cruz@feati.edu');
            $this->command->info('  Password: Student123!');
            $this->command->info('  Department: BS Information Technology');
            $this->command->info('');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error creating test accounts: ' . $e->getMessage());
        }
    }
}
