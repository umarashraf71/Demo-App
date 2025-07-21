<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maklad\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            Role::firstOrCreate(
            [
                'name' => 'Plant Gate Officer',
            ],
            [
                'name' => 'Plant Gate Officer',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]);
            Role::firstOrCreate(
            [
                'name' => 'Lab Supervisor',
            ],
            [
                'name' => 'Lab Supervisor',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]);
            Role::firstOrCreate(
            [
                'name' => 'MMT',
            ],
            [
                'name' => 'MMT',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]);
            Role::firstOrCreate(
            [
                'name' => 'MCA',
            ],
            [
                'name' => 'MCA',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]);
            Role::firstOrCreate(
            [
                'name' => 'Test',
            ],
            [
                'name' => 'Test',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]);
            Role::firstOrCreate(
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Admin',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]);
            Role::firstOrCreate(
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Super Admin',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]);
        
    }
}