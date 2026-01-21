<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        // create roles

        $superAdmin = Role::create(['name'=> 'SuperAdmin']);
        $schoolAdmin = Role::create(['name'=> 'SchoolAdmin']);
        $teacher = Role::create(['name'=> 'Teacher']);
        $student = Role::create(['name'=> 'Student']);
        $parent = Role::create(['name'=> 'Parent']);
        $bursar = Role::create(['name'=> 'Bursar']);


    }
}
