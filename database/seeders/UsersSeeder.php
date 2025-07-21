<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Maklad\Permission\Models\Role;
use Maklad\Permission\Models\Permission;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin= User::firstOrCreate(
            ['email'=>'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'email_verified_at'=> now(),
                'password' => Hash::make('admin1234'),
                'remember_token' => Str::random(10),
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'user_name' => 'super_admin',
                'status' => 1
        ]);
        $superAdminrole =Role::where('name', 'Super Admin')->first();
        $superAdminPermissions =Permission::pluck('name')->toArray();
        $superAdminrole->syncPermissions($superAdminPermissions);
        $superAdmin->assignRole($superAdminrole->name);
       
        User::firstOrCreate(
            ['email'=>'exd_admin@exdnow.com'],
            [
                'name' => 'ExD Admin',
                'email' => 'exd_admin@exdnow.com',
                'email_verified_at'=> now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'user_name' => 'exd_admin',
                'status' => 1
        ]);

        User::firstOrCreate(
            ['email'=>'mca_mobile_user@gmail.com'],
            [
                'name' => 'MCA Mobile User',
                'email' => 'mca_mobile_user@gmail.com',
                'email_verified_at'=> now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'user_name' => 'mca_mobile_user',
                'status' => 1
        ]);
        User::firstOrCreate(
            ['email'=>'mmt_mobile_user@hotmail.com'],    
            [
                'name' => 'MMT Mobile User',
                'email' => 'mmt_mobile_user@hotmail.com',
                'email_verified_at'=> now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'user_name' => 'mmt_mobile_user',
                'status' => 1
        ]);
        User::firstOrCreate(
            ['email'=>'lab_supervisor_mobile_user@yahoo.com'],
            [
            'name' => 'Lab Supervisor Mobile User',
            'email' => 'lab_supervisor_mobile_user@yahoo.com',
            'email_verified_at'=> now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
            'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'user_name' => 'lab_supervisor_mobile_user',
            'status' => 1
        ]);
        User::firstOrCreate(
            ['email'=>'plant_gate_officer@gmail.com'],
            [
            'name' => 'Plant Gate Officer',
            'email' => 'plant_gate_officer@gmail.com',
            'email_verified_at'=> now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
            'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'user_name' => 'plant_gate_officer',
            'status' => 1
        ]);
        User::firstOrCreate(
            ['email'=>'admin@exdnow.com'],
            [
            'name' => 'Admin',
            'email' => 'admin@exdnow.com',
            'email_verified_at'=> now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
            'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            'user_name' => 'admin',
            'status' => 1
        ]);
                
    }
}

