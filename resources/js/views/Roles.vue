<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise Role Management
Description: Professional enterprise role management interface with advanced features matching Users page standards

UX IMPROVEMENTS:
- Modern glass-morphism header with gradient effects
- Advanced statistics cards with animations and trends
- Sophisticated search and filtering system
- Professional table layout with enhanced interactions
- Consistent design patterns with Users page
- Enhanced accessibility and responsive design
- Advanced bulk actions and export capabilities
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <!-- Modern Header with Glass Effect -->
    <div class="relative mb-8">
      <div class="absolute inset-0 bg-gradient-to-r from-purple-600/10 to-blue-600/10 rounded-3xl blur-3xl"></div>
      <div class="relative bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl p-8 shadow-2xl">
    <div class="flex items-center justify-between">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
      <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                  Role Management
                </h1>
                <p class="text-gray-600 text-lg">Advanced role administration with enterprise security</p>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <!-- Export Button -->
            <button
              @click="exportRoles"
              :disabled="isLoading || !filteredRoles.length"
              class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
              <div class="relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export {{ filteredRoles.length }}
              </div>
            </button>

            <!-- Create Role Button -->
            <button
              @click="openCreateModal"
              class="group relative overflow-hidden bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105"
            >
              <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
              <div class="relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Role
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div
        v-for="(stat, index) in roleStats" 
        :key="stat.key"
        class="group relative overflow-hidden bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 cursor-pointer transform hover:scale-105"
        :class="`hover:bg-gradient-to-br ${stat.hoverGradient}`"
        @click="filterByStatType(stat.key)"
        :style="{ animationDelay: `${index * 100}ms` }"
      >
        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500" 
             :style="{ background: stat.bgColor }"></div>
        
        <div class="relative flex items-center justify-between">
          <div class="space-y-4">
            <div class="space-y-2">
              <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">{{ stat.label }}</p>
              <p class="text-4xl font-bold text-gray-900 group-hover:text-white transition-colors duration-300">
                {{ formatNumber(stat.value) }}
                </p>
              </div>
            
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium"
                   :class="stat.trend > 0 ? 'bg-emerald-100 text-emerald-700' : stat.trend < 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'">
                <svg v-if="stat.trend > 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"/>
                </svg>
                <svg v-else-if="stat.trend < 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"/>
                </svg>
                <span>{{ stat.trend > 0 ? '+' : '' }}{{ stat.trend }}%</span>
              </div>
              <span class="text-sm text-gray-500">vs last month</span>
              </div>
            </div>
            
          <div class="relative">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg transition-all duration-300 group-hover:scale-110"
                 :style="{ background: stat.bgColor }">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.iconPath"/>
              </svg>
            </div>
            <div class="absolute -inset-2 bg-gradient-to-r opacity-0 group-hover:opacity-20 rounded-2xl blur-lg transition-opacity duration-300"
                 :style="{ background: stat.bgColor }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Advanced Search & Filters -->
    <div class="bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl mb-8">
      <div class="space-y-6">
        <!-- Search Bar -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search roles by name, type, permissions, or any keyword..."
            class="block w-full pl-14 pr-6 py-4 text-lg border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300 placeholder-gray-400"
            @input="debouncedSearch"
          >
          <div v-if="searchQuery" class="absolute inset-y-0 right-0 pr-6 flex items-center">
            <button
              @click="clearSearch"
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Type Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Type</label>
            <select 
              v-model="selectedType"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300"
              @change="handleTypeFilter"
            >
              <option value="">All Types</option>
              <option value="system">System</option>
              <option value="custom">Custom</option>
            </select>
          </div>

          <!-- Status Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Status</label>
            <select 
              v-model="selectedStatus"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300"
              @change="handleStatusFilter"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <!-- Sort Options -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Sort By</label>
            <select 
              v-model="sortBy"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300"
              @change="handleSort"
            >
              <option value="name">Name</option>
              <option value="type">Type</option>
              <option value="status">Status</option>
              <option value="created">Date Created</option>
              <option value="users_count">Users Count</option>
            </select>
    </div>
    
          <!-- Actions -->
          <div class="flex items-end">
            <button
              @click="resetFilters"
              class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-300 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
              Reset
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Enhanced Data Table -->
    <div class="bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl overflow-hidden">
      <DataTable
        :columns="tableColumns"
        :data="paginatedRoles"
        :loading="isLoading"
        :selected-rows="selectedRoles"
        :is-all-selected="isAllSelected"
        :current-page="currentPage"
        :items-per-page="itemsPerPage"
        :total-items="filteredRoles.length"
        :sort-by="sortBy"
        :sort-order="sortOrder"
        @select-row="toggleRoleSelection"
        @select-all="toggleAllRoles"
        @sort="handleSort"
        @page-change="handlePageChange"
        @page-size-change="handlePageSizeChange"
      >
        <!-- Role Name Column -->
        <template #cell-name="{ item }">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                 :class="item.type === 'system' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-purple-500 to-pink-600'">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      :d="item.type === 'system' ? 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' : 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z'"/>
                  </svg>
                </div>
                <div>
              <div class="font-semibold text-gray-900">{{ item.display_name || item.name }}</div>
              <div class="text-sm text-gray-500">{{ item.description || 'No description' }}</div>
                  </div>
                </div>
        </template>

        <!-- Type Column -->
        <template #cell-type="{ item }">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="item.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
            {{ item.type }}
          </span>
        </template>

        <!-- Status Column -->
        <template #cell-status="{ item }">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="item.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
            <span class="w-2 h-2 mr-2 rounded-full"
                  :class="item.status === 'active' ? 'bg-green-500' : 'bg-red-500'"></span>
            {{ item.status }}
          </span>
        </template>

        <!-- Permissions Column -->
        <template #cell-permissions="{ item }">
          <div class="text-center">
            <div class="text-lg font-semibold text-gray-900">{{ item.permissions_count || 0 }}</div>
            <div class="text-sm text-gray-500">permissions</div>
          </div>
        </template>

        <!-- Users Column -->
        <template #cell-users="{ item }">
          <div class="text-center">
            <div class="text-lg font-semibold text-gray-900">{{ item.users_count || 0 }}</div>
            <div class="text-sm text-gray-500">users</div>
              </div>
        </template>

        <!-- Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex items-center justify-end gap-2">
            <button
                                    @click="viewRole(item)"
              class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200"
              title="View Role"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
            <button
              @click="editRole(item)"
              class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
              title="Edit Role"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>
                <button
              v-if="item.type === 'custom'"
              @click="deleteRole(item)"
              class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
              title="Delete Role"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
        </template>
      </DataTable>
            </div>

    <!-- Bulk Actions (when roles are selected) -->
    <div v-if="selectedRoles.length > 0" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
      <div class="bg-white/90 backdrop-blur-xl border border-white/20 rounded-2xl p-4 shadow-2xl">
        <div class="flex items-center gap-4">
          <span class="text-sm font-medium text-gray-700">{{ selectedRoles.length }} roles selected</span>
          <div class="flex items-center gap-2">
            <button
              @click="bulkExportRoles"
              class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Export
            </button>
            <button
              @click="clearSelection"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200"
            >
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Role Modal -->
    <RoleModal
      v-model="showModal"
      :role="selectedRole"
      :permissions="permissionsStore.permissions"
      @close="closeModal"
      @saved="onRoleSaved"
    />

    <!-- View Role Modal -->
    <RoleViewModal
      v-if="showViewModal"
      :role="selectedRole"
      @close="closeViewModal"
      @edit="editRole"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import DataTable from '@/components/table/DataTable.vue'
import RoleModal from '@/components/admin/roles/RoleModal.vue'
import RoleViewModal from '@/components/admin/roles/RoleViewModal.vue'
import { usePermissionsStore } from '@/stores/permissions'

// Stores & Composables
const router = useRouter()
const rolesStore = useRolesStore()
const { show: showToast, error: showError, success: showSuccess } = useToast()
const { confirm } = useConfirm()
const permissionsStore = usePermissionsStore()

// State
const showModal = ref(false)
const showViewModal = ref(false)
const selectedRole = ref(null)
const searchQuery = ref('')
const selectedType = ref('')
const selectedStatus = ref('')
const sortBy = ref('name')
const sortOrder = ref('asc')
const currentPage = ref(1)
const itemsPerPage = ref(10)
const selectedRoles = ref([])

// Computed
const roles = computed(() => rolesStore.roles || [])
const isLoading = computed(() => rolesStore.loading)

// Enhanced role statistics with trends
const roleStats = computed(() => [
  {
    key: 'total',
    label: 'Total Roles',
    value: rolesStore.totalRoles,
    trend: 5,
    bgColor: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    hoverGradient: 'from-purple-500 to-blue-600',
    iconPath: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
  {
    key: 'active',
    label: 'Active Roles',
    value: rolesStore.activeRoles,
    trend: 2,
    bgColor: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
    hoverGradient: 'from-green-500 to-teal-600',
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    key: 'custom',
    label: 'Custom Roles',
    value: rolesStore.customRoles,
    trend: 8,
    bgColor: 'linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)',
    hoverGradient: 'from-indigo-500 to-purple-600',
    iconPath: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z'
  },
  {
    key: 'system',
    label: 'System Roles',
    value: rolesStore.systemRoles,
    trend: 0,
    bgColor: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    hoverGradient: 'from-blue-500 to-purple-600',
    iconPath: 'M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m5 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-5 0V3a2 2 0 012-2h2a2 2 0 012 2v2M9 5a2 2 0 002 2h2a2 2 0 002-2'
  }
])

// Table configuration
const tableColumns = [
  { key: 'select', label: '', width: '12' },
  { key: 'name', label: 'Role', sortable: true },
  { key: 'type', label: 'Type', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'permissions', label: 'Permissions', sortable: true },
  { key: 'users', label: 'Users', sortable: true },
  { key: 'actions', label: 'Actions', width: '32' }
]

// Filtering and sorting
const filteredRoles = computed(() => {
  let filtered = [...roles.value]

  // Search filter
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase()
    filtered = filtered.filter(role =>
      role.name.toLowerCase().includes(search) ||
      role.display_name?.toLowerCase().includes(search) ||
      role.description?.toLowerCase().includes(search) ||
      role.type.toLowerCase().includes(search)
    )
  }

  // Type filter
  if (selectedType.value) {
    filtered = filtered.filter(role => role.type === selectedType.value)
  }

  // Status filter
  if (selectedStatus.value) {
    filtered = filtered.filter(role => role.status === selectedStatus.value)
  }

  // Sorting
  if (sortBy.value) {
    filtered.sort((a, b) => {
      let aVal = a[sortBy.value]
      let bVal = b[sortBy.value]
      
      if (sortBy.value === 'name') {
        aVal = a.display_name || a.name
        bVal = b.display_name || b.name
      }
      
      if (typeof aVal === 'string') {
        aVal = aVal.toLowerCase()
        bVal = bVal.toLowerCase()
      }
      
      const result = aVal < bVal ? -1 : aVal > bVal ? 1 : 0
      return sortOrder.value === 'desc' ? -result : result
    })
  }

  return filtered
})

// Pagination
const paginatedRoles = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredRoles.value.slice(start, end)
})

const isAllSelected = computed(() => {
  return paginatedRoles.value.length > 0 && 
         paginatedRoles.value.every(role => selectedRoles.value.includes(role.id))
})

// Methods
const formatNumber = (value) => {
  return new Intl.NumberFormat('en-US').format(value)
}

// Simple debounce function
const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

const debouncedSearch = debounce(() => {
  currentPage.value = 1
}, 300)

const clearSearch = () => {
  searchQuery.value = ''
  currentPage.value = 1
}

const filterByStatType = (type) => {
  selectedType.value = type === 'total' ? '' : type === 'active' ? '' : type
  if (type === 'active') {
    selectedStatus.value = 'active'
  } else if (type === 'total') {
    selectedStatus.value = ''
  }
  currentPage.value = 1
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
  sortBy.value = 'name'
  sortOrder.value = 'asc'
  currentPage.value = 1
}

const handleTypeFilter = () => {
  currentPage.value = 1
}

const handleStatusFilter = () => {
  currentPage.value = 1
}

const handleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
  currentPage.value = 1
}

const handlePageChange = (page) => {
  currentPage.value = page
}

const handlePageSizeChange = (size) => {
  itemsPerPage.value = size
  currentPage.value = 1
}

const toggleRoleSelection = (roleId) => {
  const index = selectedRoles.value.indexOf(roleId)
  if (index > -1) {
    selectedRoles.value.splice(index, 1)
  } else {
    selectedRoles.value.push(roleId)
  }
}

const toggleAllRoles = () => {
  if (isAllSelected.value) {
    selectedRoles.value = selectedRoles.value.filter(id => 
      !paginatedRoles.value.some(role => role.id === id)
    )
  } else {
    const newSelections = paginatedRoles.value
      .filter(role => !selectedRoles.value.includes(role.id))
      .map(role => role.id)
    selectedRoles.value.push(...newSelections)
  }
}

const clearSelection = () => {
  selectedRoles.value = []
}

// Modal methods
const openCreateModal = () => {
  selectedRole.value = null
  showModal.value = true
}

const editRole = (role) => {
  selectedRole.value = role
  showModal.value = true
}

const viewRole = (role) => {
  selectedRole.value = role
  showViewModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedRole.value = null
}

const closeViewModal = () => {
  showViewModal.value = false
  selectedRole.value = null
}

const onRoleSaved = () => {
  closeModal()
  showSuccess('Role saved successfully!')
}

const deleteRole = async (role) => {
  const isConfirmed = await confirm({
    title: 'Delete Role',
    message: `Are you sure you want to delete the role "${role.display_name || role.name}"? This action cannot be undone.`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
    type: 'danger'
  })

  if (isConfirmed) {
    try {
      await rolesStore.deleteRole(role.id)
      showSuccess('Role deleted successfully!')
    } catch (error) {
      showError('Failed to delete role. Please try again.')
    }
  }
}

// Export functionality
const exportRoles = () => {
  const csvContent = convertRolesToCSV(filteredRoles.value)
  downloadCSV(csvContent, 'roles-export.csv')
}

const bulkExportRoles = () => {
  const selectedRoleData = roles.value.filter(role => selectedRoles.value.includes(role.id))
  const csvContent = convertRolesToCSV(selectedRoleData)
  downloadCSV(csvContent, 'selected-roles-export.csv')
}

const convertRolesToCSV = (rolesData) => {
  const headers = ['Name', 'Type', 'Status', 'Permissions', 'Users', 'Description']
  const rows = rolesData.map(role => [
    role.display_name || role.name,
    role.type,
    role.status,
    role.permissions_count || 0,
    role.users_count || 0,
    role.description || ''
  ])
  
  return [headers, ...rows].map(row => row.map(field => `"${field}"`).join(',')).join('\n')
}

const downloadCSV = (content, filename) => {
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  link.setAttribute('href', url)
  link.setAttribute('download', filename)
  link.style.visibility = 'hidden'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Lifecycle
onMounted(async () => {
  try {
    await rolesStore.fetchRoles()
    permissionsStore.fetchPermissions({ per_page: 100 })
  } catch (error) {
    showError('Failed to load roles. Please refresh the page.')
  }
})

// Watchers
watch(searchQuery, () => {
  rolesStore.setFilters({ search: searchQuery.value })
})

watch(selectedType, () => {
  rolesStore.setFilters({ type: selectedType.value })
})

watch(selectedStatus, () => {
  rolesStore.setFilters({ status: selectedStatus.value })
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
