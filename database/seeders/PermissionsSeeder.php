<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maklad\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::firstOrCreate(
            [
                'name' => 'View Permissions',
            ],
            [
                'name' => 'View Permissions',
                'module' => 'Permissions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Permissions',
            ],
            [
                'name' => 'Create Permissions',
                'module' => 'Permissions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Permissions',
            ],
            [
                'name' => 'Edit Permissions',
                'module' => 'Permissions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Permissions',
            ],
            [
                'name' => 'Delete Permissions',
                'module' => 'Permissions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Roles',
            ],
            [
                'name' => 'View Roles',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Roles',
            ],
            [
                'name' => 'Create Roles',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Roles',
            ],
            [
                'name' => 'Edit Roles',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Roles',
            ],
            [
                'name' => 'Delete Roles',
                'module' => 'Roles',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Users',
            ],
            [
                'name' => 'View Users',
                'module' => 'Users',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Users',
            ],
            [
                'name' => 'Create Users',
                'module' => 'Users',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Users',
            ],
            [
                'name' => 'Edit Users',
                'module' => 'Users',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Users',
            ],
            [
                'name' => 'Delete Users',
                'module' => 'Users',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Plant',
            ],
            [
                'name' => 'View Plant',
                'module' => 'Plant',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Plant',
            ],
            [
                'name' => 'Delete Plant',
                'module' => 'Plant',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Plant',
            ],
            [
                'name' => 'Edit Plant',
                'module' => 'Plant',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Plant',
            ],
            [
                'name' => 'Create Plant',
                'module' => 'Plant',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Department',
            ],
            [
                'name' => 'Delete Department',
                'module' => 'Department',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Department',
            ],
            [
                'name' => 'Create Department',
                'module' => 'Department',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Department',
            ],
            [
                'name' => 'View Department',
                'module' => 'Department',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Department',
            ],
            [
                'name' => 'Edit Department',
                'module' => 'Department',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Section',
            ],
            [
                'name' => 'Edit Section',
                'module' => 'Section',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Section',
            ],
            [
                'name' => 'Delete Section',
                'module' => 'Section',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Section',
            ],

            [
                'name' => 'Create Section',
                'module' => 'Section',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Section',
            ],
            [
                'name' => 'View Section',
                'module' => 'Section',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Zone',
            ],
            [
                'name' => 'Edit Zone',
                'module' => 'Zone',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Zone',
            ],
            [
                'name' => 'Delete Zone',
                'module' => 'Zone',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Zone',
            ],
            [
                'name' => 'View Zone',
                'module' => 'Zone',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Zone',
            ],
            [
                'name' => 'Create Zone',
                'module' => 'Zone',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Area Office',
            ],
            [
                'name' => 'Edit Area Office',
                'module' => 'Area Office',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Area Office',
            ],
            [
                'name' => 'Delete Area Office',
                'module' => 'Area Office',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Area Office',
            ],
            [
                'name' => 'Create Area Office',
                'module' => 'Area Office',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Area Office',
            ],
            [
                'name' => 'View Area Office',
                'module' => 'Area Office',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Collection Points',
            ],
            [
                'name' => 'Create Collection Points',
                'module' => 'Collection Point',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Collection Points',
            ],
            [
                'name' => 'Delete Collection Points',
                'module' => 'Collection Point',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Collection Points',
            ],
            [
                'name' => 'Edit Collection Points',
                'module' => 'Collection Point',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Collection Points',
            ],
            [
                'name' => 'View Collection Points',
                'module' => 'Collection Point',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Source Type',
            ],
            [
                'name' => 'Create Source Type',
                'module' => 'Source Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Source Type',
            ],
            [
                'name' => 'View Source Type',
                'module' => 'Source Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Source Type',
            ],
            [
                'name' => 'Edit Source Type',
                'module' => 'Source Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Source Type',
            ],
            [
                'name' => 'Delete Source Type',
                'module' => 'Source Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Supplier',
            ],
            [
                'name' => 'Create Supplier',
                'module' => 'Supplier',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Supplier',
            ],
            [
                'name' => 'View Supplier',
                'module' => 'Supplier',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Supplier',
            ],
            [
                'name' => 'Edit Supplier',
                'module' => 'Supplier',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Supplier',
            ],
            [
                'name' => 'Delete Supplier',
                'module' => 'Supplier',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create MCVehicle',
            ],
            [
                'name' => 'Create MCVehicle',
                'module' => 'MCVehicle',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View MCVehicle',
            ],
            [
                'name' => 'View MCVehicle',
                'module' => 'MCVehicle',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit MCVehicle',
            ],
            [
                'name' => 'Edit MCVehicle',
                'module' => 'MCVehicle',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete MCVehicle',
            ],
            [
                'name' => 'Delete MCVehicle',
                'module' => 'MCVehicle',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create QaLabTest',
            ],
            [
                'name' => 'Create QaLabTest',
                'module' => 'QA LabTest',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View QaLabTest',
            ],
            [
                'name' => 'View QaLabTest',
                'module' => 'QA LabTest',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit QaLabTest',
            ],
            [
                'name' => 'Edit QaLabTest',
                'module' => 'QA LabTest',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete QaLabTest',
            ],
            [
                'name' => 'Delete QaLabTest',
                'module' => 'QA LabTest',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Inventory Item',
            ],
            [
                'name' => 'Create Inventory Item',
                'module' => 'Inventory Item',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Inventory Item',
            ],
            [
                'name' => 'View Inventory Item',
                'module' => 'Inventory Item',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Inventory Item',
            ],
            [
                'name' => 'Edit Inventory Item',
                'module' => 'Inventory Item',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Inventory Item',
            ],
            [
                'name' => 'Delete Inventory Item',
                'module' => 'Inventory Item',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Customer',
            ],
            [
                'name' => 'Create Customer',
                'module' => 'Customer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Customer',
            ],
            [
                'name' => 'View Customer',
                'module' => 'Customer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Customer',
            ],
            [
                'name' => 'Edit Customer',
                'module' => 'Customer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Customer',
            ],
            [
                'name' => 'Delete Customer',
                'module' => 'Customer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Test UOM',
            ],
            [
                'name' => 'Create Test UOM',
                'module' => 'Test UOM',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Test UOM',
            ],
            [
                'name' => 'View Test UOM',
                'module' => 'Test UOM',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Test UOM',
            ],
            [
                'name' => 'Edit Test UOM',
                'module' => 'Test UOM',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Test UOM',
            ],
            [
                'name' => 'Delete Test UOM',
                'module' => 'Test UOM',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Vendor Profile',
            ],
            [
                'name' => 'Create Vendor Profile',
                'module' => 'Vendor Profile',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Vendor Profile',
            ],
            [
                'name' => 'View Vendor Profile',
                'module' => 'Vendor Profile',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Vendor Profile',
            ],
            [
                'name' => 'Edit Vendor Profile',
                'module' => 'Vendor Profile',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Vendor Profile',
            ],
            [
                'name' => 'Delete Vendor Profile',
                'module' => 'Vendor Profile',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );


        Permission::firstOrCreate(
            [
                'name' => 'View Route Plan',
            ],
            [
                'name' => 'View Route Plan',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Route Plan',
            ],
            [
                'name' => 'Create Route Plan',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Route Plan',
            ],
            [
                'name' => 'Edit Route Plan',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Route Plan',
            ],
            [
                'name' => 'Delete Route Plan',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Route Vehicles',
            ],
            [
                'name' => 'View Route Vehicles',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Route Vehicles',
            ],
            [
                'name' => 'Create Route Vehicles',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Route Vehicles',
            ],
            [
                'name' => 'Edit Route Vehicles',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Route Vehicles',
            ],
            [
                'name' => 'Delete Route Vehicles',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Open Closed Route',
            ],
            [
                'name' => 'Open Closed Route',
                'module' => 'Route Plan',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Incentive Configuration',
            ],
            [
                'name' => 'View Incentive Configuration',
                'module' => 'Delivery Configuration',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Incentive Configuration',
            ],
            [
                'name' => 'Create Incentive Configuration',
                'module' => 'Delivery Configuration',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Delivery Configuration',
            ],
            [
                'name' => 'View Delivery Configuration',
                'module' => 'Delivery Configuration',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Delivery Configuration',
            ],
            [
                'name' => 'Edit Delivery Configuration',
                'module' => 'Delivery Configuration',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );


        Permission::firstOrCreate(
            [
                'name' => 'View Workflow',
            ],
            [
                'name' => 'View Workflow',
                'module' => 'Workflow',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Workflow',
            ],
            [
                'name' => 'Edit Workflow',
                'module' => 'Workflow',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Workflow',
            ],
            [
                'name' => 'Create Workflow',
                'module' => 'Workflow',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Workflow',
            ],
            [
                'name' => 'Delete Workflow',
                'module' => 'Workflow',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'MPR',
            ],
            [
                'name' => 'MPR',
                'module' => 'MCA App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'MPS',
            ],
            [
                'name' => 'MPS',
                'module' => 'MCA App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Transfer Note',
            ],
            [
                'name' => 'Transfer Note',
                'module' => 'MCA App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'MCC Reception',
            ],
            [
                'name' => 'MCC Reception',
                'module' => 'MMT App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Unplanned Visit',
            ],
            [
                'name' => 'Unplanned Visit',
                'module' => 'MMT App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'MR',
            ],
            [
                'name' => 'MR',
                'module' => 'MMT App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Route Plan',
            ],
            [
                'name' => 'Route Plan',
                'module' => 'MMT App',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'LR',
            ],
            [
                'name' => 'LR',
                'module' => 'Lab Supervisor',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Gate In',
            ],
            [
                'name' => 'Gate In',
                'module' => 'Plant Gate Officer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Gate Out',
            ],
            [
                'name' => 'Gate Out',
                'module' => 'Plant Gate Officer',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Lab Reception',
            ],
            [
                'name' => 'Lab Reception',
                'module' => 'Plant Lab',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Base Price',
            ],
            [
                'name' => 'Milk Base Price',
                'module' => 'Approvals',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Transfer (mcc to mcc)',
            ],
            [
                'name' => 'Milk Transfer (mcc to mcc)',
                'module' => 'Approvals',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Transfer (ao to ao)',
            ],
            [
                'name' => 'Milk Transfer (ao to ao)',
                'module' => 'Approvals',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'View Incentive Type',
            ],
            [
                'name' => 'View Incentive Type',
                'module' => 'Incentive Types',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Incentive Type',
            ],
            [
                'name' => 'Create Incentive Type',
                'module' => 'Incentive Types',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Incentive Type',
            ],
            [
                'name' => 'Edit Incentive Type',
                'module' => 'Incentive Types',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Incentive Type',
            ],
            [
                'name' => 'Delete Incentive Type',
                'module' => 'Incentive Types',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );


        Permission::firstOrCreate(
            [
                'name' => 'View Measurement Unit',
            ],
            [
                'name' => 'View Measurement Unit',
                'module' => 'Measurement Unit',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Measurement Unit',
            ],
            [
                'name' => 'Create Measurement Unit',
                'module' => 'Measurement Unit',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Measurement Unit',
            ],
            [
                'name' => 'Edit Measurement Unit',
                'module' => 'Measurement Unit',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Measurement Unit',
            ],
            [
                'name' => 'Delete Measurement Unit',
                'module' => 'Measurement Unit',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Test Based Supplier Incentives',
            ],
            [
                'name' => 'View Test Based Supplier Incentives',
                'module' => 'Test Based Supplier Incentives',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Test Based Supplier Incentives',
            ],
            [
                'name' => 'Create Test Based Supplier Incentives',
                'module' => 'Test Based Supplier Incentives',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Test Based Supplier Incentives',
            ],
            [
                'name' => 'Edit Test Based Supplier Incentives',
                'module' => 'Test Based Supplier Incentives',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Test Based Supplier Incentives',
            ],
            [
                'name' => 'Delete Test Based Supplier Incentives',
                'module' => 'Test Based Supplier Incentives',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Incentive Rates',
            ],
            [
                'name' => 'View Incentive Rates',
                'module' => 'Incentive Rates',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Incentive Rates',
            ],
            [
                'name' => 'Create Incentive Rates',
                'module' => 'Incentive Rates',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Incentive Rates',
            ],
            [
                'name' => 'Edit Incentive Rates',
                'module' => 'Incentive Rates',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Incentive Rates',
            ],
            [
                'name' => 'Delete Incentive Rates',
                'module' => 'Incentive Rates',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );



        Permission::firstOrCreate(
            [
                'name' => 'View Base Pricing',
            ],
            [
                'name' => 'View Base Pricing',
                'module' => 'Base Pricing',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Base Pricing',
            ],
            [
                'name' => 'Create Base Pricing',
                'module' => 'Base Pricing',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Base Pricing',
            ],
            [
                'name' => 'Edit Base Pricing',
                'module' => 'Base Pricing',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Base Pricing',
            ],
            [
                'name' => 'Delete Base Pricing',
                'module' => 'Base Pricing',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Inventory Item Type',
            ],
            [
                'name' => 'View Inventory Item Type',
                'module' => 'Inventory Item Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Inventory Item Type',
            ],
            [
                'name' => 'Create Inventory Item Type',
                'module' => 'Inventory Item Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Inventory Item Type',
            ],
            [
                'name' => 'Edit Inventory Item Type',
                'module' => 'Inventory Item Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Inventory Item Type',
            ],
            [
                'name' => 'Delete Inventory Item Type',
                'module' => 'Inventory Item Type',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );


        Permission::firstOrCreate(
            [
                'name' => 'View Categories',
            ],
            [
                'name' => 'View Categories',
                'module' => 'Categories',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Edit Categories',
            ],
            [
                'name' => 'Edit Categories',
                'module' => 'Categories',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Create Categories',
            ],
            [
                'name' => 'Create Categories',
                'module' => 'Categories',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Categories',
            ],
            [
                'name' => 'Delete Categories',
                'module' => 'Categories',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Receptions MMT',
            ],
            [
                'name' => 'Receptions MMT',
                'module' => 'Milk Purchases and Receptions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Purchases',
            ],
            [
                'name' => 'Milk Purchases',
                'module' => 'Milk Purchases and Receptions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Receptions AO',
            ],
            [
                'name' => 'Receptions AO',
                'module' => 'Milk Purchases and Receptions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Bank',
            ],
            [
                'name' => 'Create Bank',
                'module' => 'Banks',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Edit Bank',
            ],
            [
                'name' => 'Edit Bank',
                'module' => 'Banks',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Bank',
            ],
            [
                'name' => 'Delete Bank',
                'module' => 'Banks',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Bank',
            ],
            [
                'name' => 'View Bank',
                'module' => 'Banks',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create District',
            ],
            [
                'name' => 'Create District',
                'module' => 'Districts',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Edit District',
            ],
            [
                'name' => 'Edit District',
                'module' => 'Districts',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete District',
            ],
            [
                'name' => 'Delete District',
                'module' => 'Districts',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View District',
            ],
            [
                'name' => 'View District',
                'module' => 'Districts',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Create Tehsil',
            ],
            [
                'name' => 'Create Tehsil',
                'module' => 'Tehsils',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Edit Tehsil',
            ],
            [
                'name' => 'Edit Tehsil',
                'module' => 'Tehsils',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Delete Tehsil',
            ],
            [
                'name' => 'Delete Tehsil',
                'module' => 'Tehsils',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'View Tehsil',
            ],
            [
                'name' => 'View Tehsil',
                'module' => 'Tehsils',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Milk Rejection',
            ],
            [
                'name' => 'Milk Rejection',
                'module' => 'Rejection',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Purchases',
            ],
            [
                'name' => 'Milk Purchases',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Receptions',
            ],
            [
                'name' => 'Milk Receptions',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Transfers',
            ],
            [
                'name' => 'Milk Transfers',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Milk Dispatches',
            ],
            [
                'name' => 'Milk Dispatches',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );



        /**
         * Reports Permissions Start Here
         */
        Permission::firstOrCreate(
            [
                'name' => 'AO Collection Summary',
            ],
            [
                'name' => 'AO Collection Summary',
                'module' => 'Reports',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Dispatch Report',
            ],
            [
                'name' => 'Dispatch Report',
                'module' => 'Lab Supervisor',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Vehicle Weight',
            ],
            [
                'name' => 'Vehicle Weight',
                'module' => 'Plant Vehicle Weight',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'CIP',
            ],
            [
                'name' => 'CIP',
                'module' => 'Plant CIP',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Plant Receptions',
            ],
            [
                'name' => 'Plant Receptions',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Milk Rejections',
            ],
            [
                'name' => 'Milk Rejections',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Milk Purchased Rejections',
            ],
            [
                'name' => 'Milk Purchased Rejections',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Payment Process',
            ],
            [
                'name' => 'Payment Process',
                'module' => 'Operations',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );

        Permission::firstOrCreate(
            [
                'name' => 'Details Reception',
            ],
            [
                'name' => 'Details Reception',
                'module' => 'Receptions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
        Permission::firstOrCreate(
            [
                'name' => 'Delete Reception',
            ],
            [
                'name' => 'Delete Reception',
                'module' => 'Receptions',
                'guard_name' => 'web',
                'updated_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000),
                'created_at' => new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000)
            ]
        );
    }
}
