# DocraTech Medical Platform - Rol Sistemi Entegrasyon Dökümantasyonu

## 📋 İçindekiler

1. [Sistem Genel Bakış](#sistem-genel-bakış)
2. [Mimari Yapı](#mimari-yapı)
3. [Backend Entegrasyonu](#backend-entegrasyonu)
4. [Frontend Entegrasyonu](#frontend-entegrasyonu)
5. [API Endpoints](#api-endpoints)
6. [Veritabanı Yapısı](#veritabanı-yapısı)
7. [Kullanım Senaryoları](#kullanım-senaryoları)
8. [Güvenlik ve Uyumluluk](#güvenlik-ve-uyumluluk)
9. [Troubleshooting](#troubleshooting)
10. [Performans Optimizasyonu](#performans-optimizasyonu)

---

## 🎯 Sistem Genel Bakış

### İki Sistemin Amacı

DocraTech Medical Platform'da iki farklı rol yönetim sistemi birlikte çalışır:

1. **Custom Role ID Sistemi** - Hızlı erişim ve performans için
2. **Spatie Permission Sistemi** - Granüler izin yönetimi ve HIPAA uyumluluğu için

### Sistem Avantajları

| Özellik | Custom Role ID | Spatie Permission |
|---------|----------------|-------------------|
| **Performans** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Granüler İzinler** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **HIPAA Uyumluluğu** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Audit Trail** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Multi-Role** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Kolay Sorgular** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |

---

## 🏗️ Mimari Yapı

### Backend Mimari

```
app/
├── Models/
│   ├── User.php (HasRoles trait + role_id field)
│   ├── Role.php (Spatie Role extend)
│   └── Permission.php (Spatie Permission extend)
├── Services/
│   ├── UserService.php (Çift sistem entegrasyonu)
│   ├── RoleService.php (Rol yönetimi)
│   └── PermissionService.php (İzin yönetimi)
└── Controllers/
    ├── UserController.php
    └── RoleController.php
```

### Frontend Mimari

```
resources/js/
├── components/
│   ├── admin/roles/
│   │   ├── RolesManager.vue
│   │   ├── RoleModal.vue
│   │   └── RoleViewModal.vue
│   └── modal/
│       └── UserModal.vue
├── views/
│   ├── Users.vue
│   └── Roles.vue
└── stores/
    ├── users.js
    └── roles.js
```

---

## 🔧 Backend Entegrasyonu

### 1. Model Yapılandırması

#### User Model
```php
<?php
namespace App\Models;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'role_id', // Custom role_id field
        // ... diğer alanlar
    ];

    // Role ilişkisi (custom)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Spatie roles ilişkisi
    // HasRoles trait ile otomatik olarak gelir
}
```

#### Role Model
```php
<?php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'type',
        'status',
        'guard_name',
    ];

    // Users ilişkisi (custom)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Spatie users ilişkisi
    public function spatieUsers()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', User::class);
    }
}
```

### 2. Service Entegrasyonu

#### UserService
```php
<?php
namespace App\Services;

class UserService
{
    public function createUser($data)
    {
        DB::beginTransaction();
        try {
            // 1. User oluştur (role_id ile)
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'], // Custom role_id
                // ... diğer alanlar
            ]);

            // 2. Spatie role assignment
            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role); // Spatie assignment
                }
            }

            DB::commit();
            return $user->load(['role', 'roles', 'permissions']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUser($id, $data)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            // 1. User güncelle
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'], // Custom role_id
                // ... diğer alanlar
            ]);

            // 2. Spatie role sync
            if (isset($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->syncRoles([$role]); // Spatie sync
                }
            }

            DB::commit();
            return $user->load(['role', 'roles', 'permissions']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### 3. Controller Entegrasyonu

#### UserController
```php
<?php
namespace App\Http\Controllers\Api;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'roles', 'permissions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
            // ... diğer validasyonlar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->userService->createUser($request->all());
            
            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

## 🎨 Frontend Entegrasyonu

### 1. Store Yapılandırması

#### Users Store
```javascript
// stores/users.js
import { defineStore } from 'pinia'
import { userService } from '@/services/api'

export const useUsersStore = defineStore('users', () => {
  const users = ref([])
  const currentUser = ref(null)
  const loading = ref(false)

  const fetchUsers = async (params = {}) => {
    loading.value = true
    try {
      const response = await userService.getUsers(params)
      users.value = response.data.users
      return response
    } catch (error) {
      console.error('Fetch users error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData) => {
    loading.value = true
    try {
      const response = await userService.createUser(userData)
      
      // Store'a ekle
      users.value.unshift(response.data)
      
      return response
    } catch (error) {
      console.error('Create user error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  return {
    users,
    currentUser,
    loading,
    fetchUsers,
    createUser,
    // ... diğer metodlar
  }
})
```

### 2. Component Entegrasyonu

#### UserModal Component
```vue
<!-- components/modal/UserModal.vue -->
<template>
  <div class="form-group">
    <label class="form-label required">Role</label>
    <SearchableSelect
      v-model="form.role_id"
      :options="roleOptions"
      placeholder="Select user role"
      :error="errors.role_id"
      :success="!errors.role_id && !!form.role_id"
      @update:modelValue="clearError('role_id')"
      required
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  }
})

// Role options computed
const roleOptions = computed(() => {
  if (!props.roles || !Array.isArray(props.roles)) {
    return []
  }
  
  return props.roles.map(role => ({
    value: role.id,        // role_id için
    label: role.display_name || role.name,
    description: role.description || `${role.name} role`
  }))
})

// Form data
const form = ref({
  role_id: '', // Custom role_id field
  // ... diğer alanlar
})
</script>
```

### 3. Users View Entegrasyonu

#### Users.vue
```vue
<!-- views/Users.vue -->
<template>
  <div class="users-table">
    <DataTable :data="filteredUsers" :columns="columns">
      <template #cell-role="{ item }">
        <div class="role-display">
          <span class="role-name">{{ getRoleLabel(item.role_id) }}</span>
          <span class="permission-count">
            ({{ item.permissions?.length || 0 }} permissions)
          </span>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useUsersStore } from '@/stores/users'
import { useRolesStore } from '@/stores/roles'

const userStore = useUsersStore()
const roleStore = useRolesStore()

// Role label helper
const getRoleLabel = (roleId) => {
  if (!roleId) return 'No Role'
  
  const role = availableRoles.value?.find(r => r.id === roleId)
  return role ? (role.display_name || role.name) : 'No Role'
}

// Available roles computed
const availableRoles = computed(() => roleStore.roles)

// Filtered users
const filteredUsers = computed(() => {
  return userStore.users.map(user => ({
    ...user,
    role_name: getRoleLabel(user.role_id)
  }))
})
</script>
```

---

## 🔌 API Endpoints

### User Endpoints
```php
// routes/api.php
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/{id}/activate', [UserController::class, 'activate']);
    Route::post('/{id}/deactivate', [UserController::class, 'deactivate']);
    Route::get('/{id}/permissions', [UserController::class, 'permissions']);
    Route::post('/{id}/roles', [UserController::class, 'assignRoles']);
});
```

### Role Endpoints
```php
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
    Route::get('/{id}/permissions', [RoleController::class, 'permissions']);
    Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions']);
    Route::get('/active', [RoleController::class, 'active']);
    Route::get('/system', [RoleController::class, 'system']);
    Route::get('/custom', [RoleController::class, 'custom']);
});
```

### API Response Formatları

#### User List Response
```json
{
  "data": {
    "users": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role_id": 2,
        "role": {
          "id": 2,
          "name": "doctor",
          "display_name": "Doctor",
          "description": "Medical doctor role"
        },
        "roles": [
          {
            "id": 2,
            "name": "doctor",
            "permissions": [
              {
                "id": 1,
                "name": "patients.view",
                "display_name": "View Patients"
              }
            ]
          }
        ],
        "permissions": [
          {
            "id": 1,
            "name": "patients.view",
            "display_name": "View Patients"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 100
    }
  }
}
```

---

## 🗄️ Veritabanı Yapısı

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role_id BIGINT UNSIGNED NULL, -- Custom role_id field
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);
```

### Roles Table (Spatie Extended)
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    display_name VARCHAR(255) NULL,
    description TEXT NULL,
    type ENUM('system', 'custom') DEFAULT 'custom',
    status ENUM('active', 'inactive') DEFAULT 'active',
    guard_name VARCHAR(255) DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Spatie Permission Tables
```sql
-- model_has_roles table (Spatie)
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- permissions table (Spatie)
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    display_name VARCHAR(255) NULL,
    description TEXT NULL,
    module VARCHAR(255) NULL,
    guard_name VARCHAR(255) DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- role_has_permissions table (Spatie)
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

---

## 📋 Kullanım Senaryoları

### 1. Yeni Kullanıcı Oluşturma

```javascript
// Frontend
const createUser = async (userData) => {
  try {
    const response = await userService.createUser({
      name: 'John Doe',
      email: 'john@example.com',
      role_id: 2, // Doctor role ID
      password: 'secure_password',
      // ... diğer alanlar
    })
    
    console.log('User created:', response.data)
  } catch (error) {
    console.error('Error creating user:', error)
  }
}
```

```php
// Backend - UserService
public function createUser($data)
{
    DB::beginTransaction();
    try {
        // 1. User oluştur (role_id ile)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'], // Custom role_id
            'password' => Hash::make($data['password']),
        ]);

        // 2. Spatie role assignment
        $role = Role::find($data['role_id']);
        if ($role) {
            $user->assignRole($role); // Spatie assignment
        }

        DB::commit();
        return $user->load(['role', 'roles', 'permissions']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### 2. Kullanıcı Rolü Güncelleme

```javascript
// Frontend
const updateUserRole = async (userId, roleId) => {
  try {
    const response = await userService.updateUser(userId, {
      role_id: roleId
    })
    
    console.log('User role updated:', response.data)
  } catch (error) {
    console.error('Error updating user role:', error)
  }
}
```

```php
// Backend - UserService
public function updateUser($id, $data)
{
    DB::beginTransaction();
    try {
        $user = User::findOrFail($id);
        
        // 1. User güncelle
        $user->update([
            'role_id' => $data['role_id'], // Custom role_id
        ]);

        // 2. Spatie role sync
        $role = Role::find($data['role_id']);
        if ($role) {
            $user->syncRoles([$role]); // Spatie sync
        }

        DB::commit();
        return $user->load(['role', 'roles', 'permissions']);
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### 3. Kullanıcı İzinlerini Kontrol Etme

```php
// Backend - Middleware
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->hasPermissionTo($permission)) {
            return response()->json([
                'message' => 'Unauthorized - Insufficient permissions'
            ], 403);
        }

        return $next($request);
    }
}
```

```javascript
// Frontend - Route Guard
const checkPermission = (permission) => {
  const user = useAuthStore().user
  return user?.permissions?.some(p => p.name === permission) || false
}

// Route meta kontrolü
router.beforeEach((to, from, next) => {
  if (to.meta.permissions) {
    const hasPermission = to.meta.permissions.some(permission => 
      checkPermission(permission)
    )
    
    if (!hasPermission) {
      next('/unauthorized')
      return
    }
  }
  
  next()
})
```

---

## 🔒 Güvenlik ve Uyumluluk

### HIPAA Uyumluluğu

#### Audit Trail
```php
// AuditLog Model
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'event',
        'description',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];
}
```

#### Permission Logging
```php
// UserService - Audit logging
private function logUserActivity($user, $event, $description)
{
    AuditLog::create([
        'user_id' => auth()->id(),
        'subject_type' => User::class,
        'subject_id' => $user->id,
        'event' => $event,
        'description' => $description,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'metadata' => [
            'role_id' => $user->role_id,
            'assigned_roles' => $user->roles->pluck('name'),
            'permissions' => $user->permissions->pluck('name')
        ]
    ]);
}
```

### Güvenlik Önlemleri

#### Role Validation
```php
// Role validation rules
'role_id' => [
    'required',
    'exists:roles,id',
    function ($attribute, $value, $fail) {
        $role = Role::find($value);
        if (!$role || $role->status !== 'active') {
            $fail('Selected role is not available.');
        }
    }
]
```

#### Permission Checks
```php
// Controller level permission checks
public function store(Request $request)
{
    // Check if user can create users
    if (!auth()->user()->can('users.create')) {
        return response()->json([
            'message' => 'Unauthorized - Cannot create users'
        ], 403);
    }

    // Check if user can assign the selected role
    $selectedRole = Role::find($request->role_id);
    if ($selectedRole && !auth()->user()->can("roles.assign.{$selectedRole->name}")) {
        return response()->json([
            'message' => 'Unauthorized - Cannot assign this role'
        ], 403);
    }

    // ... rest of the method
}
```

---

## 🛠️ Troubleshooting

### Yaygın Sorunlar ve Çözümleri

#### 1. Role ID Null Sorunu

**Sorun:** Kullanıcıların role_id alanı null kalıyor.

**Çözüm:**
```php
// UserService - createUser metodunda
public function createUser($data)
{
    DB::beginTransaction();
    try {
        // role_id'nin var olduğundan emin ol
        if (!isset($data['role_id'])) {
            throw new \Exception('Role ID is required');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'], // Bu alanın dolu olduğundan emin ol
        ]);

        // Spatie assignment
        $role = Role::find($data['role_id']);
        if ($role) {
            $user->assignRole($role);
        }

        DB::commit();
        return $user;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

#### 2. Spatie Role Assignment Hatası

**Sorun:** Spatie role assignment çalışmıyor.

**Çözüm:**
```php
// User model'de HasRoles trait'inin doğru yüklendiğinden emin ol
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // Guard name'i belirt
    protected $guard_name = 'web';
}
```

#### 3. Frontend Role Display Sorunu

**Sorun:** Roller frontend'de görünmüyor.

**Çözüm:**
```javascript
// Users.vue - Role display helper'ını güncelle
const getRoleLabel = (value) => {
  console.log('🔍 getRoleLabel called with:', value)
  console.log('🔍 availableRoles:', availableRoles.value)
  
  if (!value) return 'No Role'
  
  // Role object kontrolü
  if (typeof value === 'object' && value?.display_name) {
    return value.display_name
  }
  
  // Role ID ile arama
  if (typeof value === 'number') {
    const role = availableRoles.value?.find(r => r.id === value)
    if (role) {
      return role.display_name || role.name
    }
  }
  
  return 'No Role'
}
```

#### 4. Performance Sorunları

**Sorun:** Çok fazla kullanıcı olduğunda performans düşüyor.

**Çözüm:**
```php
// UserController - Eager loading ile optimize et
public function index()
{
    $users = User::with([
        'role:id,name,display_name',
        'roles:id,name,display_name',
        'permissions:id,name,display_name'
    ])
    ->select(['id', 'name', 'email', 'role_id', 'status', 'created_at'])
    ->orderBy('created_at', 'desc')
    ->paginate(20);

    return response()->json([
        'data' => [
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]
    ]);
}
```

### Debug Araçları

#### Backend Debug
```php
// Debug helper
function debugUserRoles($user)
{
    dd([
        'user_id' => $user->id,
        'custom_role_id' => $user->role_id,
        'custom_role' => $user->role,
        'spatie_roles' => $user->roles,
        'spatie_permissions' => $user->permissions,
        'all_permissions' => $user->getAllPermissions(),
    ]);
}
```

#### Frontend Debug
```javascript
// Debug helper
const debugUserRoles = (user) => {
  console.log('🔍 User Debug:', {
    id: user.id,
    name: user.name,
    role_id: user.role_id,
    role: user.role,
    roles: user.roles,
    permissions: user.permissions
  })
}
```

---

## ⚡ Performans Optimizasyonu

### Database Indexing

```sql
-- Users table indexes
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_email ON users(email);

-- Spatie tables indexes
CREATE INDEX idx_model_has_roles_model ON model_has_roles(model_type, model_id);
CREATE INDEX idx_model_has_roles_role ON model_has_roles(role_id);
CREATE INDEX idx_role_has_permissions_role ON role_has_permissions(role_id);
CREATE INDEX idx_role_has_permissions_permission ON role_has_permissions(permission_id);
```

### Caching Strategy

```php
// Role caching
class RoleService
{
    public function getActiveRoles()
    {
        return Cache::remember('active_roles', 3600, function () {
            return Role::where('status', 'active')
                ->with(['permissions'])
                ->orderBy('name')
                ->get();
        });
    }

    public function getUserRoles($userId)
    {
        return Cache::remember("user_roles_{$userId}", 1800, function () use ($userId) {
            $user = User::with(['roles', 'permissions'])->find($userId);
            return [
                'custom_role' => $user->role,
                'spatie_roles' => $user->roles,
                'permissions' => $user->permissions
            ];
        });
    }
}
```

### Query Optimization

```php
// Optimized user queries
class UserService
{
    public function getUsersWithRoles($filters = [])
    {
        $query = User::with([
            'role:id,name,display_name,description',
            'roles:id,name,display_name',
            'permissions:id,name,display_name'
        ])
        ->select(['id', 'name', 'email', 'role_id', 'status', 'created_at']);

        // Apply filters
        if (isset($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}
```

---

## 📚 Ek Kaynaklar

### Dokümantasyon Linkleri
- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Laravel Eloquent Relationships](https://laravel.com/docs/10.x/eloquent-relationships)
- [Vue.js Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)

### Kod Örnekleri
- [Complete User Management Example](examples/user-management.md)
- [Role Permission Examples](examples/role-permissions.md)
- [Frontend Integration Examples](examples/frontend-integration.md)

### Test Senaryoları
- [Unit Tests](tests/Unit/RoleSystemTest.php)
- [Feature Tests](tests/Feature/UserManagementTest.php)
- [Integration Tests](tests/Integration/RolePermissionTest.php)

---

## 📞 Destek

### Teknik Destek
- **Email:** support@docratech.com
- **Telefon:** +90 xxx xxx xx xx
- **Dokümantasyon:** https://docs.docratech.com

### Geliştirici Kaynakları
- **GitHub Repository:** https://github.com/docratech/medical-platform
- **API Documentation:** https://api.docratech.com/docs
- **Developer Portal:** https://developers.docratech.com

---

*Bu dökümantasyon DocraTech Medical Platform v3.0 için hazırlanmıştır. Son güncelleme: 2024* 