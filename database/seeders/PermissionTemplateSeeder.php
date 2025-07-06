<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionTemplate;
use App\Models\Permission;

class PermissionTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createMedicalTemplates();
        $this->createAdministrativeTemplates();
        $this->createSystemTemplates();
    }

    /**
     * Create medical role templates.
     */
    private function createMedicalTemplates(): void
    {
        // Get existing permissions, create only templates for existing permissions
        $doctorPermissionNames = [
            'users.view', 'users.create', 'users.edit',
            'patients.view', 'patients.create', 'patients.edit', 'patients.export',
            'medical_records.view', 'medical_records.create', 'medical_records.edit',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
            'prescriptions.view', 'prescriptions.create', 'prescriptions.edit',
            'reports.view', 'reports.generate',
            'dashboard.view', 'dashboard.stats', 'dashboard.activity',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $doctorPermissions = Permission::whereIn('name', $doctorPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($doctorPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'doctor_full_access'],
                [
                    'display_name' => 'Doctor - Full Medical Access',
                    'description' => 'Complete access to patient data, medical records, and clinical operations for licensed physicians',
                    'category' => 'medical',
                    'permissions' => $doctorPermissions,
                    'metadata' => [
                        'role_type' => 'clinical',
                        'security_level' => 'high',
                        'hipaa_compliant' => true,
                        'requires_license' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Nurse - Clinical Support
        $nursePermissionNames = [
            'patients.view', 'patients.create', 'patients.edit',
            'medical_records.view', 'medical_records.create',
            'appointments.view', 'appointments.create', 'appointments.edit',
            'prescriptions.view',
            'dashboard.view', 'dashboard.activity',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $nursePermissions = Permission::whereIn('name', $nursePermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($nursePermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'nurse_clinical_support'],
                [
                    'display_name' => 'Nurse - Clinical Support',
                    'description' => 'Clinical support access for registered nurses with patient care responsibilities',
                    'category' => 'medical',
                    'permissions' => $nursePermissions,
                    'metadata' => [
                        'role_type' => 'clinical',
                        'security_level' => 'medium',
                        'hipaa_compliant' => true,
                        'requires_license' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Medical Assistant
        $assistantPermissionNames = [
            'patients.view', 'patients.create', 'patients.edit',
            'appointments.view', 'appointments.create', 'appointments.edit',
            'dashboard.view',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $assistantPermissions = Permission::whereIn('name', $assistantPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($assistantPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'medical_assistant_basic'],
                [
                    'display_name' => 'Medical Assistant - Basic Access',
                    'description' => 'Basic patient management and appointment scheduling for medical assistants',
                    'category' => 'medical',
                    'permissions' => $assistantPermissions,
                    'metadata' => [
                        'role_type' => 'support',
                        'security_level' => 'medium',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Lab Technician
        $labTechPermissionNames = [
            'patients.view',
            'medical_records.view', 'medical_records.create',
            'lab_results.view', 'lab_results.create', 'lab_results.edit',
            'reports.view', 'reports.generate',
            'dashboard.view',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $labTechPermissions = Permission::whereIn('name', $labTechPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($labTechPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'lab_technician_access'],
                [
                    'display_name' => 'Lab Technician - Laboratory Access',
                    'description' => 'Laboratory operations and test result management for lab technicians',
                    'category' => 'medical',
                    'permissions' => $labTechPermissions,
                    'metadata' => [
                        'role_type' => 'technical',
                        'security_level' => 'medium',
                        'hipaa_compliant' => true,
                        'requires_license' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }
    }

    /**
     * Create administrative templates.
     */
    private function createAdministrativeTemplates(): void
    {
        // Clinic Administrator - sadece mevcut permission'ları kullan
        $adminPermissionNames = [
            'users.view', 'users.create', 'users.edit', 'users.export',
            'roles.view', 'roles.create', 'roles.edit', 'roles.assign_permissions',
            'permissions.view',
            'patients.view', 'patients.create', 'patients.edit', 'patients.export',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
            'billing.view', 'billing.create', 'billing.edit',
            'reports.view', 'reports.generate', 'reports.export',
            'dashboard.view', 'dashboard.stats', 'dashboard.activity', 'dashboard.system_status',
            'settings.view', 'settings.edit',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $adminPermissions = Permission::whereIn('name', $adminPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($adminPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'clinic_administrator'],
                [
                    'display_name' => 'Clinic Administrator',
                    'description' => 'Full administrative access for clinic management and operations',
                    'category' => 'administrative',
                    'permissions' => $adminPermissions,
                    'metadata' => [
                        'role_type' => 'administrative',
                        'security_level' => 'high',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Office Manager
        $officeManagerPermissionNames = [
            'users.view', 'users.create', 'users.edit',
            'patients.view', 'patients.create', 'patients.edit',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
            'billing.view', 'billing.create', 'billing.edit',
            'reports.view', 'reports.generate',
            'dashboard.view', 'dashboard.stats', 'dashboard.activity',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $officeManagerPermissions = Permission::whereIn('name', $officeManagerPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($officeManagerPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'office_manager'],
                [
                    'display_name' => 'Office Manager',
                    'description' => 'Office operations management including scheduling and billing',
                    'category' => 'administrative',
                    'permissions' => $officeManagerPermissions,
                    'metadata' => [
                        'role_type' => 'administrative',
                        'security_level' => 'medium',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Receptionist
        $receptionistPermissionNames = [
            'patients.view', 'patients.create', 'patients.edit',
            'appointments.view', 'appointments.create', 'appointments.edit',
            'dashboard.view',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $receptionistPermissions = Permission::whereIn('name', $receptionistPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($receptionistPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'receptionist_basic'],
                [
                    'display_name' => 'Receptionist - Front Desk',
                    'description' => 'Front desk operations including patient check-in and appointment scheduling',
                    'category' => 'administrative',
                    'permissions' => $receptionistPermissions,
                    'metadata' => [
                        'role_type' => 'support',
                        'security_level' => 'low',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Billing Specialist
        $billingPermissionNames = [
            'patients.view',
            'billing.view', 'billing.create', 'billing.edit', 'billing.process',
            'insurance.view', 'insurance.verify', 'insurance.submit',
            'reports.view', 'reports.generate',
            'dashboard.view',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $billingPermissions = Permission::whereIn('name', $billingPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($billingPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'billing_specialist'],
                [
                    'display_name' => 'Billing Specialist',
                    'description' => 'Financial operations including billing, insurance, and payment processing',
                    'category' => 'administrative',
                    'permissions' => $billingPermissions,
                    'metadata' => [
                        'role_type' => 'financial',
                        'security_level' => 'medium',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }
    }

    /**
     * Create system templates.
     */
    private function createSystemTemplates(): void
    {
        // System Administrator - tüm aktif permission'ları al
        $systemAdminPermissions = Permission::where('is_active', true)->pluck('id')->toArray();

        if (!empty($systemAdminPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'system_administrator'],
                [
                    'display_name' => 'System Administrator',
                    'description' => 'Full system access including user management, system settings, and security',
                    'category' => 'system',
                    'permissions' => $systemAdminPermissions,
                    'metadata' => [
                        'role_type' => 'system',
                        'security_level' => 'critical',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                        'full_access' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // IT Support
        $itSupportPermissionNames = [
            'users.view', 'users.edit',
            'system.backup', 'system.maintenance',
            'audit.view',
            'security.view', 'security.resolve',
            'dashboard.view', 'dashboard.system_status',
            'settings.view',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $itSupportPermissions = Permission::whereIn('name', $itSupportPermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($itSupportPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'it_support'],
                [
                    'display_name' => 'IT Support Specialist',
                    'description' => 'Technical support and system maintenance with limited administrative access',
                    'category' => 'system',
                    'permissions' => $itSupportPermissions,
                    'metadata' => [
                        'role_type' => 'technical',
                        'security_level' => 'high',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Compliance Officer
        $compliancePermissionNames = [
            'users.view',
            'audit.view', 'audit.export',
            'security.view', 'security.resolve',
            'permissions.view',
            'roles.view',
            'reports.view', 'reports.generate', 'reports.export',
            'dashboard.view', 'dashboard.activity',
            'profile.view', 'profile.edit', 'profile.password',
        ];
        
        $compliancePermissions = Permission::whereIn('name', $compliancePermissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($compliancePermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'compliance_officer'],
                [
                    'display_name' => 'Compliance Officer',
                    'description' => 'HIPAA compliance monitoring, audit review, and security oversight',
                    'category' => 'system',
                    'permissions' => $compliancePermissions,
                    'metadata' => [
                        'role_type' => 'compliance',
                        'security_level' => 'high',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                        'audit_access' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }

        // Read-Only Observer - sadece .view permission'ları
        $readOnlyPermissions = Permission::where('name', 'like', '%.view')
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (!empty($readOnlyPermissions)) {
            PermissionTemplate::firstOrCreate(
                ['name' => 'read_only_observer'],
                [
                    'display_name' => 'Read-Only Observer',
                    'description' => 'View-only access for training, auditing, or temporary access needs',
                    'category' => 'system',
                    'permissions' => $readOnlyPermissions,
                    'metadata' => [
                        'role_type' => 'observer',
                        'security_level' => 'low',
                        'hipaa_compliant' => true,
                        'requires_license' => false,
                        'read_only' => true,
                    ],
                    'is_system' => true,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
        }
    }
} 