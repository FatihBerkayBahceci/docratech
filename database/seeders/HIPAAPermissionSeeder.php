<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class HIPAAPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createHIPAAPermissions();
        $this->assignPermissionsToRoles();
    }

    /**
     * Create HIPAA-compliant permissions
     */
    private function createHIPAAPermissions(): void
    {
        $permissions = [
            // Patient Data Access
            ['name' => 'patient.view', 'key' => 'patient_view', 'description' => 'View patient information', 'module' => 'patient', 'type' => 'system'],
            ['name' => 'patient.create', 'key' => 'patient_create', 'description' => 'Create new patient records', 'module' => 'patient', 'type' => 'system'],
            ['name' => 'patient.edit', 'key' => 'patient_edit', 'description' => 'Edit patient information', 'module' => 'patient', 'type' => 'system'],
            ['name' => 'patient.delete', 'key' => 'patient_delete', 'description' => 'Delete patient records', 'module' => 'patient', 'type' => 'system'],
            ['name' => 'patient.export', 'key' => 'patient_export', 'description' => 'Export patient data', 'module' => 'patient', 'type' => 'system'],
            
            // Medical Records
            ['name' => 'medical_records.view', 'key' => 'medical_records_view', 'description' => 'View medical records', 'module' => 'medical_records', 'type' => 'system'],
            ['name' => 'medical_records.create', 'key' => 'medical_records_create', 'description' => 'Create medical records', 'module' => 'medical_records', 'type' => 'system'],
            ['name' => 'medical_records.edit', 'key' => 'medical_records_edit', 'description' => 'Edit medical records', 'module' => 'medical_records', 'type' => 'system'],
            ['name' => 'medical_records.delete', 'key' => 'medical_records_delete', 'description' => 'Delete medical records', 'module' => 'medical_records', 'type' => 'system'],
            
            // Appointments
            ['name' => 'appointments.view', 'key' => 'appointments_view', 'description' => 'View appointments', 'module' => 'appointments', 'type' => 'system'],
            ['name' => 'appointments.create', 'key' => 'appointments_create', 'description' => 'Create appointments', 'module' => 'appointments', 'type' => 'system'],
            ['name' => 'appointments.edit', 'key' => 'appointments_edit', 'description' => 'Edit appointments', 'module' => 'appointments', 'type' => 'system'],
            ['name' => 'appointments.delete', 'key' => 'appointments_delete', 'description' => 'Delete appointments', 'module' => 'appointments', 'type' => 'system'],
            
            // Reports & Analytics
            ['name' => 'reports.view', 'key' => 'reports_view', 'description' => 'View reports', 'module' => 'reports', 'type' => 'system'],
            ['name' => 'reports.create', 'key' => 'reports_create', 'description' => 'Create reports', 'module' => 'reports', 'type' => 'system'],
            ['name' => 'reports.export', 'key' => 'reports_export', 'description' => 'Export reports', 'module' => 'reports', 'type' => 'system'],
            
            // User Management
            ['name' => 'users.view', 'key' => 'users_view', 'description' => 'View users', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.create', 'key' => 'users_create', 'description' => 'Create users', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.edit', 'key' => 'users_edit', 'description' => 'Edit users', 'module' => 'users', 'type' => 'system'],
            ['name' => 'users.delete', 'key' => 'users_delete', 'description' => 'Delete users', 'module' => 'users', 'type' => 'system'],
            
            // Role Management
            ['name' => 'roles.view', 'key' => 'roles_view', 'description' => 'View roles', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.create', 'key' => 'roles_create', 'description' => 'Create roles', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.edit', 'key' => 'roles_edit', 'description' => 'Edit roles', 'module' => 'roles', 'type' => 'system'],
            ['name' => 'roles.delete', 'key' => 'roles_delete', 'description' => 'Delete roles', 'module' => 'roles', 'type' => 'system'],
            
            // Permission Management
            ['name' => 'permissions.view', 'key' => 'permissions_view', 'description' => 'View permissions', 'module' => 'permissions', 'type' => 'system'],
            ['name' => 'permissions.assign', 'key' => 'permissions_assign', 'description' => 'Assign permissions', 'module' => 'permissions', 'type' => 'system'],
            
            // Audit & Security
            ['name' => 'audit.view', 'key' => 'audit_view', 'description' => 'View audit logs', 'module' => 'audit', 'type' => 'system'],
            ['name' => 'security.settings', 'key' => 'security_settings', 'description' => 'Manage security settings', 'module' => 'security', 'type' => 'system'],
            
            // System Administration
            ['name' => 'system.settings', 'key' => 'system_settings', 'description' => 'Manage system settings', 'module' => 'system', 'type' => 'system'],
            ['name' => 'system.backup', 'key' => 'system_backup', 'description' => 'Manage system backups', 'module' => 'system', 'type' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission['name'],
                    'guard_name' => 'web'
                ],
                array_merge($permission, [
                    'guard_name' => 'web',
                    'is_active' => true
                ])
            );
        }
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - All permissions
        $superAdmin = Role::where('name', 'super-admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::all();
            $superAdmin->syncPermissions($allPermissions);
        }

        // Admin - Most permissions except system-level
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereNotIn('module', ['system'])->get();
            $admin->syncPermissions($adminPermissions);
        }

        // Doctor - Patient and medical records access
        $doctor = Role::where('name', 'doctor')->first();
        if ($doctor) {
            $doctorPermissions = Permission::whereIn('module', ['patient', 'medical_records', 'appointments', 'reports'])
                ->whereNotIn('name', ['patient.delete', 'medical_records.delete', 'appointments.delete'])
                ->get();
            $doctor->syncPermissions($doctorPermissions);
        }

        // Nurse - Limited patient access
        $nurse = Role::where('name', 'nurse')->first();
        if ($nurse) {
            $nursePermissions = Permission::whereIn('module', ['patient', 'appointments'])
                ->whereIn('name', ['patient.view', 'appointments.view', 'appointments.create', 'appointments.edit'])
                ->get();
            $nurse->syncPermissions($nursePermissions);
        }

        // Receptionist - Basic access
        $receptionist = Role::where('name', 'receptionist')->first();
        if ($receptionist) {
            $receptionistPermissions = Permission::whereIn('module', ['patient', 'appointments'])
                ->whereIn('name', ['patient.view', 'patient.create', 'appointments.view', 'appointments.create', 'appointments.edit'])
                ->get();
            $receptionist->syncPermissions($receptionistPermissions);
        }
    }
} 