<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $permissions = [
            // Users
            ['name' => 'users.view', 'group' => 'Users', 'guard_name' => 'web'],
            ['name' => 'users.create', 'group' => 'Users', 'guard_name' => 'web'],
            ['name' => 'users.update', 'group' => 'Users', 'guard_name' => 'web'],
            ['name' => 'users.delete', 'group' => 'Users', 'guard_name' => 'web'],

            // Courses
            ['name' => 'courses.view', 'group' => 'Courses', 'guard_name' => 'web'],
            ['name' => 'courses.create', 'group' => 'Courses', 'guard_name' => 'web'],
            ['name' => 'courses.update', 'group' => 'Courses', 'guard_name' => 'web'],
            ['name' => 'courses.delete', 'group' => 'Courses', 'guard_name' => 'web'],
            ['name' => 'courses.publish', 'group' => 'Courses', 'guard_name' => 'web'],

            // Enrollments
            ['name' => 'enrollments.view', 'group' => 'Enrollments', 'guard_name' => 'web'],
            ['name' => 'enrollments.create', 'group' => 'Enrollments', 'guard_name' => 'web'],
            ['name' => 'enrollments.update', 'group' => 'Enrollments', 'guard_name' => 'web'],
            ['name' => 'enrollments.delete', 'group' => 'Enrollments', 'guard_name' => 'web'],

            // Payments
            ['name' => 'payments.view', 'group' => 'Payments', 'guard_name' => 'web'],
            ['name' => 'payments.create', 'group' => 'Payments', 'guard_name' => 'web'],
            ['name' => 'payments.approve', 'group' => 'Payments', 'guard_name' => 'web'],
            ['name' => 'payments.reject', 'group' => 'Payments', 'guard_name' => 'web'],

            // Reports
            ['name' => 'reports.view', 'group' => 'Reports', 'guard_name' => 'web'],
            ['name' => 'reports.export', 'group' => 'Reports', 'guard_name' => 'web'],

            // Settings
            ['name' => 'settings.view', 'group' => 'Settings', 'guard_name' => 'web'],
            ['name' => 'settings.update', 'group' => 'Settings', 'guard_name' => 'web'],

            // Categories
            ['name' => 'categories.view', 'group' => 'Categories', 'guard_name' => 'web'],
            ['name' => 'categories.create', 'group' => 'Categories', 'guard_name' => 'web'],
            ['name' => 'categories.update', 'group' => 'Categories', 'guard_name' => 'web'],
            ['name' => 'categories.delete', 'group' => 'Categories', 'guard_name' => 'web'],

            // Instructors
            ['name' => 'instructors.view', 'group' => 'Instructors', 'guard_name' => 'web'],
            ['name' => 'instructors.manage', 'group' => 'Instructors', 'guard_name' => 'web'],

            // Students
            ['name' => 'students.view', 'group' => 'Students', 'guard_name' => 'web'],
            ['name' => 'students.manage', 'group' => 'Students', 'guard_name' => 'web'],

            // Blogs
            ['name' => 'blogs.view', 'group' => 'Blogs', 'guard_name' => 'web'],
            ['name' => 'blogs.create', 'group' => 'Blogs', 'guard_name' => 'web'],
            ['name' => 'blogs.update', 'group' => 'Blogs', 'guard_name' => 'web'],
            ['name' => 'blogs.delete', 'group' => 'Blogs', 'guard_name' => 'web'],
            ['name' => 'blogs.publish', 'group' => 'Blogs', 'guard_name' => 'web'],

            // Support
            ['name' => 'support.view', 'group' => 'Support', 'guard_name' => 'web'],
            ['name' => 'support.manage', 'group' => 'Support', 'guard_name' => 'web'],

            // LMS
            ['name' => 'lms.access', 'group' => 'LMS', 'guard_name' => 'web'],
        ];

        $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'is_default' => false]);

        foreach ($permissions as $perm) {
            $permission = Permission::create($perm);
            $role->givePermissionTo($permission);
        }

        $instructorRole = Role::create(['name' => 'instructor', 'guard_name' => 'web', 'is_default' => false]);
        $instructorRole->givePermissionTo('lms.access');
        $instructorRole->givePermissionTo('courses.view');
        $instructorRole->givePermissionTo('courses.create');
        $instructorRole->givePermissionTo('courses.update');

        $studentRole = Role::create(['name' => 'student', 'guard_name' => 'web', 'is_default' => true]);
    }
}
