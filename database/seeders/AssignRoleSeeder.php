<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // assign roles to specific users

        $superAdmin = User::create([
            'name'=> 'Super Admin',
            'email'=> 'superadmin@sms.com',
            'password'=> Hash::make('Superadmin123')
        ]);

        $superAdmin ->assignRole('Superadmin');


        $schoolAdmin = User::create([
            'name'=> 'School Admin',
            'email'=> 'schooladmin@sms.com',
            'password'=> Hash::make('Schooladmin123')
        ]);

        $schoolAdmin ->assignRole('Schooladmin');


        $teacher = User::create([
            'name'=> 'Teacher',
            'email'=> 'teacher@sms.com',
            'password'=> Hash::make('Teacher123')
        ]);

        $teacher ->assignRole('Teacher');


        $student = User::create([
            'name'=> 'Student',
            'email'=> 'student@sms.com',
            'password'=> Hash::make('Student123')
        ]);

        $student ->assignRole('student');


        $parent = User::create([
            'name'=> 'Parent',
            'email'=> 'parent@sms.com',
            'password'=> Hash::make('Parent123')
        ]);

        $parent ->assignRole('parent');


        $bursar = User::create([
            'name'=> 'Bursar',
            'email'=> 'bursar@sms.com',
            'password'=> Hash::make('Bursar123')
        ]);

        $bursar ->assignRole('bursar');
    }
}
