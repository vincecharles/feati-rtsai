<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => null,
        ];
    }

    public function admin(?Role $role = null): Factory {
        return $this->state(function () use ($role) {
            $adminRoleId = $role?->id ?? optional(Role::firstWhere('name','admin'))->id;
            return [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('qwerty123'),
                'role_id' => $adminRoleId,
            ];
        });
    }
}

