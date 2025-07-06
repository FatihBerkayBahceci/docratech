<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise User Management
Description: Professional enterprise user management interface with advanced features
-->

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <!-- Modern Header with Glass Effect -->
    <div class="relative mb-8">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 rounded-3xl blur-3xl"></div>
      <div class="relative bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl p-8 shadow-2xl">
        <div class="flex items-center justify-between">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197V9a3 3 0 00-3-3m3 3a3 3 0 11-6 0m6 0a3 3 0 11-6 0m6 0v1a6 6 0 01-12 0V9a3 3 0 016-6m6 0a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                  User Management
                </h1>
                <p class="text-gray-600 text-lg">Advanced user administration with enterprise features</p>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-4">
            <!-- Export Button -->
            <button
              @click="exportUsers"
              :disabled="isLoading || !localFilteredUsers.length"
              class="group relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
              <div class="relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export {{ localFilteredUsers.length }}
              </div>
            </button>

            <!-- Add User Button -->
            <button
              @click="openCreateModal"
              class="group relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105"
            >
              <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
              <div class="relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add User
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div 
        v-for="(stat, index) in userStats" 
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
            placeholder="Search users by name, email, role, or any keyword..."
            class="block w-full pl-14 pr-6 py-4 text-lg border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-300 placeholder-gray-400"
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
          <!-- Role Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Role</label>
            <select 
              v-model="selectedRole"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-300"
              @change="handleRoleFilter"
            >
              <option value="">All Roles</option>
              <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                {{ role.display_name }}
              </option>
            </select>
          </div>

          <!-- Status Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Status</label>
            <select 
              v-model="selectedStatus"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-300"
              @change="handleStatusFilter"
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="pending">Pending</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>

          <!-- Sort Options -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Sort By</label>
            <select 
              v-model="sortBy"
              class="w-full px-4 py-3 border-0 bg-gray-50/50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all duration-300"
              @change="handleSort"
            >
              <option value="name">Name</option>
              <option value="email">Email</option>
              <option value="role">Role</option>
              <option value="created">Date Created</option>
              <option value="last_active">Last Active</option>
            </select>
          </div>

          <!-- Actions -->
          <div class="flex items-end">
            <button
              @click="resetFilters"
              class="w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-300 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              Reset Filters
            </button>
          </div>
        </div>
        
        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="flex items-center gap-3 flex-wrap">
          <span class="text-sm font-semibold text-gray-700">Active filters:</span>
          
          <div v-if="searchQuery" class="flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 text-sm rounded-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Search: "{{ searchQuery }}"
            <button @click="clearSearch" class="ml-1 hover:bg-blue-200 rounded-full p-1 transition-colors duration-200">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
              </svg>
            </button>
          </div>

          <div v-if="selectedRole" class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 text-sm rounded-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Role: {{ getRoleLabel(selectedRole) }}
            <button @click="clearRoleFilter" class="ml-1 hover:bg-purple-200 rounded-full p-1 transition-colors duration-200">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
              </svg>
            </button>
          </div>

          <div v-if="selectedStatus" class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 text-sm rounded-full">
            <div class="w-2 h-2 rounded-full bg-current"></div>
            Status: {{ selectedStatus }}
            <button @click="clearStatusFilter" class="ml-1 hover:bg-green-200 rounded-full p-1 transition-colors duration-200">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
              </svg>
            </button>
          </div>
          
          <button 
            @click="clearAllFilters" 
            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full transition-all duration-200"
          >
            Clear All
          </button>
        </div>
      </div>
    </div>

    <!-- Enhanced Users Table -->
    <div class="bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl overflow-hidden">
      <DataTable
        :columns="columns"
        :data="localPaginatedUsers"
        :loading="isLoading"
        :selected-rows="selectedUsers"
        :total-items="localFilteredUsers.length"
        :items-per-page="pageSize"
        :current-page="currentPage"
        :total-pages="totalPages"
        @sort="handleSort"
        @page-change="handlePageChange"
        @page-size-change="handlePageSizeChange"
        @select-all="selectAllUsers"
        @select-row="toggleUserSelection"
      >
        <!-- Enhanced Name Column with Avatar -->
        <template #cell-name="{ item }">
          <div class="flex items-center gap-4 py-2">
            <div class="relative">
              <Avatar
                :name="getFullName(item)"
                :src="item.avatar"
                size="lg"
                class="ring-2 ring-white shadow-md"
              />
              <div v-if="item.isOnline" class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            <div class="space-y-1">
              <p class="font-semibold text-gray-900 text-lg">{{ getFullName(item) }}</p>
              <p class="text-gray-600">{{ item.email }}</p>
              <div v-if="item.phone" class="flex items-center gap-1 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ item.phone }}
              </div>
            </div>
          </div>
        </template>

        <!-- Enhanced Role Column -->
        <template #cell-role="{ item }">
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl"
              :class="getRoleClasses(item.role)">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getRoleIcon(item.role)"/>
              </svg>
              {{ getRoleLabel(item.role) }}
            </span>
            <!-- Permission count badge -->
            <div v-if="item.permissions && item.permissions.length > 0" 
                 class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              {{ item.permissions.length }} permissions
            </div>
          </div>
        </template>

        <!-- Enhanced Status Column -->
        <template #cell-status="{ item }">
          <StatusBadge :status="item.status" enhanced />
        </template>

        <!-- Enhanced Last Active Column -->
        <template #cell-lastActive="{ item }">
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full"
                :class="item.isOnline ? 'bg-green-400 animate-pulse' : 'bg-gray-300'">
              </div>
              <div class="text-sm">
                <div class="font-medium text-gray-900">
                  {{ item.isOnline ? 'Online Now' : 'Offline' }}
                </div>
                <div class="text-gray-500">
                  {{ item.lastActive || 'Never' }}
                </div>
              </div>
            </div>
          </div>
        </template>

        <!-- Enterprise Inline Actions Column -->
        <template #cell-actions="{ item }">
          <!-- Normal Actions Row -->
          <div v-if="showQuickActionsFor !== item.id" class="flex items-center justify-center gap-3 py-2">
            <!-- View Button -->
            <button
              @click="handleViewUser(item.id)"
              class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:shadow-blue-500/25 transform hover:scale-110 transition-all duration-300"
              :title="`View ${getFullName(item)} details`"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>

            <!-- Edit Button -->
            <button
              @click="handleEditUser(item.id)"
              class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 text-white rounded-xl shadow-lg hover:shadow-xl hover:shadow-amber-500/25 transform hover:scale-110 transition-all duration-300"
              :title="`Edit ${getFullName(item)} information`"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </button>

            <!-- Delete Button -->
            <button
              @click.stop="handleDeleteUser(item.id)"
              class="delete-button flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:shadow-red-500/25 transform hover:scale-110 transition-all duration-300"
              :title="`Delete ${getFullName(item)} permanently`"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>

            <!-- Quick Actions Toggle -->
            <button
              @click.stop="toggleQuickActions(item.id)"
              class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-600 text-white rounded-xl shadow-lg hover:shadow-xl hover:shadow-gray-500/25 transform hover:scale-110 transition-all duration-300 hover:from-blue-500 hover:to-blue-600"
              :title="'More actions'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
              </svg>
            </button>
          </div>

          <!-- Enterprise Inline Quick Actions -->
          <div v-else class="relative py-8 px-6" data-quick-actions>
            <!-- Glassmorphism Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-50/95 via-white/98 to-purple-50/95 backdrop-blur-xl rounded-2xl border border-blue-200/40 shadow-2xl"></div>
            
            <!-- Enterprise Header -->
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-20">
              <div class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-xl border border-white/20">
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                <span class="text-sm font-bold tracking-wider">ENTERPRISE QUICK ACTIONS</span>
                <div class="w-2 h-2 bg-white/60 rounded-full animate-ping"></div>
              </div>
            </div>
            
            <!-- Quick Action Buttons Grid -->
            <div class="relative flex items-center justify-center gap-6 pt-4">
              <!-- Send Email -->
              <div class="group relative">
                <button
                  @click="sendEmailToUser(item)"
                  class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-2xl hover:shadow-3xl hover:shadow-blue-500/50 transform hover:scale-110 transition-all duration-300 border-2 border-blue-400/30 relative overflow-hidden"
                >
                  <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <div class="absolute inset-0 bg-blue-400 rounded-2xl opacity-0 group-hover:opacity-30 blur-xl transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  📧 Send Email
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>

              <!-- Reset Password -->
              <div class="group relative">
                <button
                  @click="resetUserPassword(item)"
                  class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-500 text-white rounded-2xl shadow-2xl hover:shadow-3xl hover:shadow-amber-500/50 transform hover:scale-110 transition-all duration-300 border-2 border-amber-400/30 relative overflow-hidden"
                >
                  <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"/>
                  </svg>
                  <div class="absolute inset-0 bg-amber-400 rounded-2xl opacity-0 group-hover:opacity-30 blur-xl transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  🔑 Reset Password
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>

              <!-- Login History -->
              <div class="group relative">
                <button
                  @click="viewLoginHistory(item)"
                  class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl shadow-2xl hover:shadow-3xl hover:shadow-emerald-500/50 transform hover:scale-110 transition-all duration-300 border-2 border-emerald-400/30 relative overflow-hidden"
                >
                  <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                  <div class="absolute inset-0 bg-emerald-400 rounded-2xl opacity-0 group-hover:opacity-30 blur-xl transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  📊 Login History
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>

              <!-- Suspend/Activate -->
              <div class="group relative" v-if="item.status === 'active'">
                <button
                  @click="suspendUser(item)"
                  class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 text-white rounded-2xl shadow-2xl hover:shadow-3xl hover:shadow-orange-500/50 transform hover:scale-110 transition-all duration-300 border-2 border-orange-400/30 relative overflow-hidden"
                >
                  <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/>
                  </svg>
                  <div class="absolute inset-0 bg-orange-400 rounded-2xl opacity-0 group-hover:opacity-30 blur-xl transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  ⚠️ Suspend Account
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>

              <div class="group relative" v-else-if="item.status === 'suspended' || item.status === 'inactive'">
                <button
                  @click="activateUser(item)"
                  class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-2xl shadow-2xl hover:shadow-3xl hover:shadow-green-500/50 transform hover:scale-110 transition-all duration-300 border-2 border-green-400/30 relative overflow-hidden"
                >
                  <svg class="w-8 h-8 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div class="absolute inset-0 bg-green-400 rounded-2xl opacity-0 group-hover:opacity-30 blur-xl transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-2xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  ✅ Activate Account
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>

              <!-- Close Button -->
              <div class="group relative ml-4">
                <button
                  @click.stop="showQuickActionsFor = null"
                  class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-gray-600 to-gray-700 text-white rounded-xl shadow-xl hover:shadow-2xl hover:shadow-gray-500/50 transform hover:scale-110 transition-all duration-300 hover:from-red-500 hover:to-red-600 border-2 border-gray-500/30 relative overflow-hidden"
                >
                  <svg class="w-7 h-7 transform rotate-45 transition-transform duration-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  <div class="absolute inset-0 bg-gray-400 rounded-xl opacity-0 group-hover:opacity-30 blur-lg transition-all duration-300"></div>
                  <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-xl"></div>
                </button>
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gray-900/95 text-white text-xs rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap backdrop-blur-sm border border-white/10">
                  ❌ Close Actions
                  <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                </div>
              </div>
            </div>

            <!-- DocraTech Enterprise Branding -->
            <div class="absolute -bottom-3 right-6">
              <div class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-gray-600/90 to-gray-700/90 text-white text-xs rounded-full backdrop-blur-sm border border-white/10">
                <div class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></div>
                <span class="font-semibold">DocraTech Enterprise</span>
              </div>
            </div>
          </div>
        </template>
      </DataTable>
    </div>

    <!-- Modals -->
    <UserModal
      v-model="showCreateModal"
      :user="editingUser"
      :roles="availableRoles"
      :departments="availableDepartments"
      :loading="isSubmitting"
      @save="handleUserSave"
      @cancel="closeCreateModal"
    />

    <BulkActionsModal
      v-if="showBulkActions"
      :selected-count="selectedUsers.length"
      @close="closeBulkActions"
      @action="handleBulkAction"
    />

    <!-- Toast Notifications -->
    <Toast />
    
    <!-- Success Notification -->
    <SuccessNotification
      :is-visible="showSuccessNotification"
      :message="successNotificationData.message"
      :user-info="successNotificationData.userInfo"
      :duration="5000"
      :auto-close="true"
      @close="showSuccessNotification = false"
    />

    <!-- Email Dialog -->
    <EmailDialog
      :is-visible="showEmailDialog"
      :recipient-name="emailDialogData.recipientName"
      :recipient-email="emailDialogData.recipientEmail"
      @close="closeEmailDialog"
      @send="handleEmailSend"
    />

    <!-- Login History Modal -->
    <LoginHistoryModal
      :is-visible="showLoginHistoryModal"
      :user-name="loginHistoryData.userName"
      :history-data="loginHistoryData.data"
      :is-loading="loginHistoryData.loading"
      :error="loginHistoryData.error"
      @close="closeLoginHistoryModal"
      @retry="retryLoginHistory"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import UserModal from '@/components/modal/UserModal.vue'
import BulkActionsModal from '@/components/users/BulkActionsModal.vue'
import Toast from '@/components/Toast.vue'
import DataTable from '@/components/table/DataTable.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import Avatar from '@/components/media/Avatar.vue'
import SuccessNotification from '@/components/toast/SuccessNotification.vue'
import EmailDialog from '@/components/modal/EmailDialog.vue'
import LoginHistoryModal from '@/components/modal/LoginHistoryModal.vue'

// Router & Store
const router = useRouter()
const userStore = useUsersStore()
const roleStore = useRolesStore()
const toast = useToast()
const confirm = useConfirm()

// State
const isLoading = ref(true)
const isSubmitting = ref(false)
const showCreateModal = ref(false)
const showBulkActions = ref(false)
const editingUser = ref(null)
const searchQuery = ref('')
const selectedRole = ref('')
const selectedStatus = ref('')
const selectedUsers = ref([])
const currentPage = ref(1)
const pageSize = ref(10)
const sortBy = ref('name')
const sortOrder = ref('asc')

const showQuickActionsFor = ref(null)
const showSuccessNotification = ref(false)
const successNotificationData = ref({})

// Email Dialog State
const showEmailDialog = ref(false)
const emailDialogData = ref({
  recipientName: '',
  recipientEmail: ''
})
const currentEmailUser = ref(null)

// Login History Modal State
const showLoginHistoryModal = ref(false)
const loginHistoryData = ref({
  userName: '',
  data: null,
  loading: false,
  error: null
})
const currentHistoryUser = ref(null)

// Local data to prevent infinite loops
const localUsers = ref([])
const localTotalUsers = ref(0)
const localActiveUsers = ref(0)
const localInactiveUsers = ref(0)
const localNewUsers = ref(0)
const localStats = ref({
  totalGrowth: 0,
  activeGrowth: 0,
  inactiveGrowth: 0,
  newGrowth: 0
})

// Computed properties using local data
const localFilteredUsers = computed(() => {
  if (!Array.isArray(localUsers.value)) {
    return []
  }
  
  let filtered = [...localUsers.value]
  
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => {
      if (!user) return false
      return getFullName(user).toLowerCase().includes(search) ||
             user.email?.toLowerCase().includes(search) ||
             user.role?.name?.toLowerCase().includes(search) ||
             user.phone?.toLowerCase().includes(search)
    })
  }
  
  if (selectedRole.value) {
    filtered = filtered.filter(user => {
      if (!user || !user.role) return false
      return user.role?.name === selectedRole.value
    })
  }
  
  if (selectedStatus.value) {
    filtered = filtered.filter(user => {
      if (!user) return false
      return user.status === selectedStatus.value
    })
  }
  
  return filtered
})

const localPaginatedUsers = computed(() => {
  const filtered = localFilteredUsers.value || []
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  return filtered.slice(start, end)
})

const totalPages = computed(() => {
  const filtered = localFilteredUsers.value || []
  return Math.ceil(filtered.length / pageSize.value) || 1
})

const hasActiveFilters = computed(() => {
  return !!(searchQuery.value || selectedRole.value || selectedStatus.value)
})

// Available roles - FIXED: Proper role data fetching
const availableRoles = computed(() => {
  console.log('🔍 [Users.vue] availableRoles computed - roleStore.roles:', roleStore.roles)
  console.log('🔍 [Users.vue] availableRoles computed - userStore.availableRoles:', userStore.availableRoles)
  
  // CRITICAL FIX: Always prioritize real API data from roleStore
  if (roleStore.roles && Array.isArray(roleStore.roles) && roleStore.roles.length > 0) {
    console.log('✅ [Users.vue] Using roleStore.roles:', roleStore.roles)
    return roleStore.roles
  }
  
  // Second priority: userStore roles if available
  if (userStore.availableRoles && Array.isArray(userStore.availableRoles) && userStore.availableRoles.length > 0) {
    console.log('⚠️ [Users.vue] Fallback to userStore.availableRoles:', userStore.availableRoles)
    return userStore.availableRoles
  }
  
  // Last resort: static data (should not happen in production)
  console.warn('🚨 [Users.vue] Using fallback static roles - API data not available!')
  return [
    { id: 1, name: 'admin', display_name: 'Administrator', description: 'Full system access' },
    { id: 2, name: 'manager', display_name: 'Manager', description: 'Limited administrative access' },
    { id: 3, name: 'user', display_name: 'User', description: 'Standard user access' },
    { id: 4, name: 'guest', display_name: 'Guest', description: 'Read-only access' }
  ]
})

// Available departments - with fallback
const availableDepartments = computed(() => {
  return userStore.availableDepartments || [
    { id: 1, name: 'Administration', description: 'Administrative department' },
    { id: 2, name: 'Medical', description: 'Medical staff' },
    { id: 3, name: 'Support', description: 'Customer support' },
    { id: 4, name: 'IT', description: 'Information technology' }
  ]
})

// Enhanced user statistics
const userStats = computed(() => [
  {
    key: 'total',
    label: 'Total Users',
    value: localTotalUsers.value,
    trend: localStats.value.totalGrowth || 0,
    bgColor: 'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)',
    hoverGradient: 'from-blue-400 to-purple-500',
    iconPath: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    key: 'active',
    label: 'Active Users',
    value: localActiveUsers.value,
    trend: localStats.value.activeGrowth || 0,
    bgColor: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
    hoverGradient: 'from-emerald-400 to-green-500',
    iconPath: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
  {
    key: 'inactive',
    label: 'Inactive Users',
    value: localInactiveUsers.value,
    trend: localStats.value.inactiveGrowth || 0,
    bgColor: 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
    hoverGradient: 'from-amber-400 to-orange-500',
    iconPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    key: 'new',
    label: 'New Users (30d)',
    value: localNewUsers.value,
    trend: localStats.value.newGrowth || 0,
    bgColor: 'linear-gradient(135deg, #3B82F6 0%, #2563EB 100%)',
    hoverGradient: 'from-sky-400 to-blue-500',
    iconPath: 'M12 4v16m8-8H4'
  }
])

// Enhanced table columns
const columns = [
  { key: 'select', label: '', sortable: false, width: '40px' },
  { key: 'name', label: 'User Information', sortable: true, width: '300px' },
  { key: 'role', label: 'Role', sortable: true, width: '150px' },
  { key: 'status', label: 'Status', sortable: true, width: '120px' },
  { key: 'lastActive', label: 'Activity', sortable: true, width: '150px' },
  { key: 'actions', label: 'Actions', sortable: false, width: '120px' }
]

// Helper functions
const getFullName = (user) => {
  if (!user) return 'Unknown User'
  if (user.name) return user.name
  if (user.first_name && user.last_name) {
    return `${user.first_name} ${user.last_name}`
  }
  return user.first_name || user.last_name || 'Unknown User'
}

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0)
}

const getRoleClasses = (role) => {
  if (!role) return 'bg-gray-100 text-gray-800 border border-gray-200'
  
  const roleMap = {
    'admin': 'bg-purple-100 text-purple-800 border border-purple-200',
    'manager': 'bg-blue-100 text-blue-800 border border-blue-200',
    'user': 'bg-green-100 text-green-800 border border-green-200',
    'guest': 'bg-gray-100 text-gray-800 border border-gray-200'
  }
  
  const roleName = typeof role === 'object' ? role?.name : role
  return roleMap[roleName?.toLowerCase()] || roleMap.guest
}

const getRoleIcon = (role) => {
  if (!role) return 'M8.828 8.828a4 4 0 015.344 0M9 10h1m4 0h1m-6 4h8m-5-10V3a1 1 0 011-1h1a1 1 0 011 1v1M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'
  
  const iconMap = {
    'admin': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'manager': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
    'user': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'guest': 'M8.828 8.828a4 4 0 115.344 0M9 10h1m4 0h1m-6 4h8m-5-10V3a1 1 0 011-1h1a1 1 0 011 1v1M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'
  }
  
  const roleName = typeof role === 'object' ? role?.name : role
  return iconMap[roleName?.toLowerCase()] || iconMap.guest
}

const getRoleLabel = (value) => {
  console.log('🔍 [Users.vue] getRoleLabel called with:', value)
  console.log('🔍 [Users.vue] availableRoles:', availableRoles.value)
  
  if (!value) {
    console.log('❌ [Users.vue] No role value provided')
    return 'No Role'
  }
  
  // If value is already a role object with display_name
  if (typeof value === 'object' && value?.display_name) {
    console.log('✅ [Users.vue] Using role.display_name:', value.display_name)
    return value.display_name
  }
  
  // If value is already a role object with name
  if (typeof value === 'object' && value?.name) {
    console.log('✅ [Users.vue] Using role.name:', value.name)
    return value.name.charAt(0).toUpperCase() + value.name.slice(1)
  }
  
  // If value is a role ID, find the role in availableRoles
  if (typeof value === 'number' || (typeof value === 'string' && !isNaN(value))) {
    const roleId = parseInt(value)
    const role = availableRoles.value?.find(r => r.id === roleId)
    if (role) {
      console.log('✅ [Users.vue] Found role by ID:', role)
      return role.display_name || role.name.charAt(0).toUpperCase() + role.name.slice(1)
    }
  }
  
  // If value is a role name string, find the role in availableRoles
  if (typeof value === 'string') {
    const role = availableRoles.value?.find(r => r.name === value)
    if (role) {
      console.log('✅ [Users.vue] Found role by name:', role)
      return role.display_name || role.name.charAt(0).toUpperCase() + role.name.slice(1)
    }
  }
  
  console.log('❌ [Users.vue] No matching role found for:', value)
  return 'No Role'
}

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    // Search happens locally now, no need to call API
  }, 300)
}

// API Methods
const fetchUsers = async () => {
  console.log('🚀 [Users.vue] fetchUsers() called!')
  isLoading.value = true
  try {
    console.log('📞 [Users.vue] Calling userStore.fetchUsers()...')
    await userStore.fetchUsers({
      page: 1, // Always fetch first page to get all data
      perPage: 100, // Get more data for better local filtering
      sortBy: sortBy.value,
      sortOrder: sortOrder.value
    })
    
    console.log('✅ [Users.vue] userStore.fetchUsers() completed')
    console.log('📊 [Users.vue] userStore.users:', userStore.users)
    
    // Update local data from store
    await nextTick(() => {
      localUsers.value = [...(userStore.users || [])]
      localTotalUsers.value = userStore.totalUsers || 0
      localActiveUsers.value = userStore.activeUsers || 0
      localInactiveUsers.value = userStore.inactiveUsers || 0
      localNewUsers.value = userStore.newUsers || 0
      localStats.value = { ...userStore.stats }
      
      console.log('🔄 [Users.vue] Local data updated:', {
        users: localUsers.value.length,
        total: localTotalUsers.value,
        active: localActiveUsers.value
      })
    })
  } catch (error) {
    console.error('❌ [Users.vue] Error fetching users:', error)
    toast.error('Failed to fetch users')
  } finally {
    isLoading.value = false
    console.log('🏁 [Users.vue] fetchUsers() finished')
  }
}

// Event handlers
const handleRoleFilter = () => {
  currentPage.value = 1
}

const handleStatusFilter = () => {
  currentPage.value = 1
}

const handleSort = ({ key, order }) => {
  sortBy.value = key
  sortOrder.value = order
  currentPage.value = 1
  
  // Sort locally
  localUsers.value.sort((a, b) => {
    let aVal, bVal
    
    switch (key) {
      case 'name':
        aVal = getFullName(a)
        bVal = getFullName(b)
        break
      case 'role':
        aVal = a.role?.name || ''
        bVal = b.role?.name || ''
        break
      default:
        aVal = a[key] || ''
        bVal = b[key] || ''
    }
    
    if (order === 'asc') {
      return aVal > bVal ? 1 : -1
    } else {
      return aVal < bVal ? 1 : -1
    }
  })
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedRole.value = ''
  selectedStatus.value = ''
  sortBy.value = 'name'
  sortOrder.value = 'asc'
  currentPage.value = 1
}

const clearSearch = () => {
  searchQuery.value = ''
}

const clearRoleFilter = () => {
  selectedRole.value = ''
}

const clearStatusFilter = () => {
  selectedStatus.value = ''
}

const clearAllFilters = () => {
  resetFilters()
}

const handlePageChange = (page) => {
  currentPage.value = page
}

const handlePageSizeChange = (size) => {
  pageSize.value = size
  currentPage.value = 1
}

const openCreateModal = () => {
  editingUser.value = null
  showCreateModal.value = true
}

const closeCreateModal = () => {
  showCreateModal.value = false
  editingUser.value = null
}

const handleUserSave = async ({ formData, isEditing, userId }) => {
  try {
    isSubmitting.value = true
    
    // Handle user save operation
    
          if (isEditing) {
        await userStore.updateUser(userId, formData)
        toast.success('User updated successfully')
      } else {
        await userStore.createUser(formData)
        toast.success('User created successfully')
      }
    
    closeCreateModal()
    await fetchUsers()
      } catch (error) {
      console.error('User save error:', error)
      toast.error(isEditing ? 'Failed to update user' : 'Failed to create user')
    } finally {
    isSubmitting.value = false
  }
}

const handleEditUser = async (id) => {
  try {
    // Find the user in local data first
    const user = localUsers.value.find(u => u.id === id)
    if (user) {
      editingUser.value = user
      showCreateModal.value = true
    } else {
      // If not found locally, fetch from store
      await userStore.fetchUser(id)
      editingUser.value = userStore.currentUser
      showCreateModal.value = true
    }
  } catch (error) {
    console.error('Error loading user for edit:', error)
    toast.error('Failed to load user data')
  }
}

const handleViewUser = (id) => {
  router.push(`/users/${id}`)
}

const handleDeleteUser = async (id) => {
  const user = localUsers.value.find(u => u.id === id)
  const userName = user ? getFullName(user) : 'this user'
  
  try {
    
    const confirmed = await confirm.show({
      title: 'Delete User Account',
      message: `Are you absolutely sure you want to permanently delete <strong>${userName}</strong>?<br><br>
      This action will remove all associated data including:<br>
      • User profile and personal information<br>
      • Login history and session data<br>
      • Assigned permissions and roles<br>
      • All activity logs and audit trails`,
      confirmText: 'Delete Permanently',
      cancelText: 'Cancel',
      variant: 'danger'
    })

    if (confirmed) {
      try {
        const deleteToast = toast.loading(`Deleting ${userName}...`)
        
        await userStore.deleteUser(id)
        
        toast.dismiss(deleteToast)
        
        // Show enterprise success notification
        successNotificationData.value = {
          message: `<strong>${userName}</strong> has been successfully deleted from the system.`,
          userInfo: {
            name: userName,
            email: user?.email || 'N/A'
          }
        }
        showSuccessNotification.value = true
        
        // Remove user from local state immediately for better UX
        const userIndex = localUsers.value.findIndex(u => u.id === id)
        
        if (userIndex !== -1) {
          localUsers.value.splice(userIndex, 1)
          localTotalUsers.value = Math.max(0, localTotalUsers.value - 1)
          
          // Update stats if needed
          if (user?.status === 'active') {
            localActiveUsers.value = Math.max(0, localActiveUsers.value - 1)
          } else if (user?.status === 'inactive') {
            localInactiveUsers.value = Math.max(0, localInactiveUsers.value - 1)
          }
        }
        
        // Refresh data from server after a short delay
        setTimeout(() => {
          fetchUsers()
        }, 1000)
        
      } catch (error) {
        toast.error(`❌ Failed to delete ${userName}. ${error.response?.data?.message || error.message}`, {
          duration: 10000
        })
      }
    }
  } catch (error) {
    toast.error(`❌ Unexpected error: ${error.message}`)
  }
}

const toggleUserSelection = (userId) => {
  const index = selectedUsers.value.indexOf(userId)
  if (index === -1) {
    selectedUsers.value.push(userId)
  } else {
    selectedUsers.value.splice(index, 1)
  }
}

const selectAllUsers = () => {
  if (selectedUsers.value.length === localPaginatedUsers.value.length) {
    selectedUsers.value = []
  } else {
    selectedUsers.value = localPaginatedUsers.value.map(user => user.id)
  }
}

const closeBulkActions = () => {
  showBulkActions.value = false
}

const handleBulkAction = async (actionData) => {
  try {
    await userStore.bulkUpdateUsers({
      userIds: selectedUsers.value,
      ...actionData
    })
    toast.success('Bulk action completed successfully')
    selectedUsers.value = []
    closeBulkActions()
    await fetchUsers()
  } catch (error) {
    toast.error('Failed to perform bulk action')
  }
}

const exportUsers = async () => {
  try {
    await userStore.exportUsers()
    toast.success('Export started. You will be notified when it\'s ready.')
  } catch (error) {
    toast.error('Failed to export users')
  }
}

const filterByStatType = (type) => {
  clearAllFilters()
  
  switch (type) {
    case 'active':
      selectedStatus.value = 'active'
      break
    case 'inactive':
      selectedStatus.value = 'inactive'
      break
    case 'pending':
      selectedStatus.value = 'pending'
      break
  }
}

// Quick Actions
const toggleQuickActions = (userId) => {
  showQuickActionsFor.value = showQuickActionsFor.value === userId ? null : userId
}

const sendEmailToUser = async (user) => {
  showQuickActionsFor.value = null
  currentEmailUser.value = user
  emailDialogData.value = {
    recipientName: getFullName(user),
    recipientEmail: user.email
  }
  showEmailDialog.value = true
}

const resetUserPassword = async (user) => {
  showQuickActionsFor.value = null
  
  try {
    console.log('🔑 [Reset Password] Starting reset for user:', user)
    
    const confirmed = await confirm.show({
      title: '🔑 Reset Password',
      message: `Reset password for <strong>${getFullName(user)}</strong>?
      
      <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <div class="flex items-center gap-2 text-amber-800 font-semibold mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Password Reset Process
        </div>
        <ul class="text-amber-700 text-sm space-y-1">
          <li>• A new temporary password will be generated</li>
          <li>• User will be notified via email with new credentials</li>
          <li>• User will be required to change password on next login</li>
          <li>• Current active sessions will be terminated</li>
        </ul>
      </div>`,
      type: 'warning'
    })
    
    console.log('🔑 [Reset Password] User confirmed:', confirmed)
    
    if (confirmed) {
      console.log('🔑 [Reset Password] Showing loading toast...')
      toast.loading(`Resetting password for ${getFullName(user)}...`)
      
      console.log('🔑 [Reset Password] Calling userStore.resetUserPassword with userId:', user.id)
      const result = await userStore.resetUserPassword(user.id)
      console.log('🔑 [Reset Password] API result:', result)
      
      console.log('🔑 [Reset Password] Showing success toast...')
      toast.success(`🔑 Password reset successfully for ${getFullName(user)}. Notification email sent.`)
      
      console.log('🔑 [Reset Password] Reset completed successfully')
    }
    
  } catch (error) {
    console.error('❌ [Reset Password] Error occurred:', error)
    console.error('❌ [Reset Password] Error details:', {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data,
      stack: error.stack
    })
    toast.error(`Failed to reset password: ${error.message}`)
  }
}

const viewLoginHistory = async (user) => {
  showQuickActionsFor.value = null
  currentHistoryUser.value = user
  
  // Setup modal data
  loginHistoryData.value = {
    userName: getFullName(user),
    data: null,
    loading: true,
    error: null
  }
  
  showLoginHistoryModal.value = true
  
  try {
    const history = await userStore.getUserLoginHistory(user.id)
    loginHistoryData.value.data = history
    loginHistoryData.value.loading = false
    
  } catch (error) {
    console.error('Login history error:', error)
    loginHistoryData.value.error = error.message
    loginHistoryData.value.loading = false
  }
}

const suspendUser = async (user) => {
  showQuickActionsFor.value = null
  
  try {
    const reason = prompt(`⚠️ Suspend ${getFullName(user)}\n\nPlease provide a reason for suspension:`, 'Policy violation or security concern')
    if (!reason) return
    
    const confirmed = await confirm.show({
      title: '⚠️ Suspend User Account',
      message: `Suspend <strong>${getFullName(user)}</strong> for: "${reason}"?
      
      <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
        <div class="flex items-center gap-2 text-orange-800 font-semibold mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Suspension Effects
        </div>
        <ul class="text-orange-700 text-sm space-y-1">
          <li>• User will be immediately logged out of all sessions</li>
          <li>• Account access will be blocked until reactivated</li>
          <li>• User will receive suspension notification email</li>
          <li>• Account can be reactivated later by administrators</li>
        </ul>
      </div>`,
      type: 'warning'
    })
    
    if (confirmed) {
      toast.loading(`Suspending ${getFullName(user)}...`)
      
      await userStore.suspendUser(user.id, reason)
      
      toast.success(`⚠️ ${getFullName(user)} has been suspended`)
      
      // Refresh users list
      await fetchUsers()
    }
    
  } catch (error) {
    console.error('Suspend user error:', error)
    toast.error(`Failed to suspend user: ${error.message}`)
  }
}

const activateUser = async (user) => {
  showQuickActionsFor.value = null
  
  try {
    const confirmed = await confirm.show({
      title: '✅ Activate User Account',
      message: `Activate <strong>${getFullName(user)}</strong>'s account?
      
      <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center gap-2 text-green-800 font-semibold mb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Activation Effects
        </div>
        <ul class="text-green-700 text-sm space-y-1">
          <li>• User will regain full account access</li>
          <li>• User can log in immediately</li>
          <li>• User will receive activation notification email</li>
          <li>• All previous permissions and roles will be restored</li>
        </ul>
      </div>`,
      type: 'success'
    })
    
    if (confirmed) {
      toast.loading(`Activating ${getFullName(user)}...`)
      
      await userStore.activateUser(user.id)
      
      toast.success(`✅ ${getFullName(user)} has been activated`)
      
      // Refresh users list
      await fetchUsers()
    }
    
  } catch (error) {
    console.error('Activate user error:', error)
    toast.error(`Failed to activate user: ${error.message}`)
  }
}

// Handle click outside to close quick actions
const handleClickOutside = (event) => {
  if (!event.target.closest('[data-quick-actions]')) {
    showQuickActionsFor.value = null
  }
}

// Email Dialog Handlers
const closeEmailDialog = () => {
  showEmailDialog.value = false
  currentEmailUser.value = null
}

const handleEmailSend = async (emailData) => {
  if (!currentEmailUser.value) return
  
  try {
    await userStore.sendEmailToUser(currentEmailUser.value.id, emailData)
    toast.success(`📧 Email sent successfully to ${getFullName(currentEmailUser.value)}`)
    closeEmailDialog()
  } catch (error) {
    console.error('Send email error:', error)
    toast.error(`Failed to send email: ${error.message}`)
  }
}

// Login History Modal Handlers
const closeLoginHistoryModal = () => {
  showLoginHistoryModal.value = false
  currentHistoryUser.value = null
  loginHistoryData.value = {
    userName: '',
    data: null,
    loading: false,
    error: null
  }
}

const retryLoginHistory = async () => {
  if (!currentHistoryUser.value) return
  
  loginHistoryData.value.loading = true
  loginHistoryData.value.error = null
  
  try {
    const history = await userStore.getUserLoginHistory(currentHistoryUser.value.id)
    loginHistoryData.value.data = history
    loginHistoryData.value.loading = false
  } catch (error) {
    console.error('Login history retry error:', error)
    loginHistoryData.value.error = error.message
    loginHistoryData.value.loading = false
  }
}

// Lifecycle hooks
onMounted(async () => {
  console.log('🏗️ [Users.vue] Component mounted, starting initialization...')
  
  try {
    // CRITICAL: Roles verilerini önce yükle, sonra users'ları
    console.log('📞 [Users.vue] Fetching roles first...')
    await roleStore.fetchRoles()
    console.log('✅ [Users.vue] Roles loaded:', roleStore.roles)
    
    console.log('📞 [Users.vue] Fetching users...')
    await fetchUsers()
    console.log('✅ [Users.vue] Users loaded')
    
  } catch (error) {
    console.error('❌ [Users.vue] Initialization error:', error)
    toast.error('Failed to initialize Users page')
  }
  
  // Click outside to close quick actions
  document.addEventListener('click', handleClickOutside)
  
  console.log('✅ [Users.vue] Component initialization completed')
  console.log('📊 [Users.vue] Final roles available:', availableRoles.value)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-5px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-slide-in-up {
  animation: slideInUp 0.6s ease-out forwards;
}

.animate-fadeInScale {
  animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}

/* Action button hover effects */
.action-button {
  position: relative;
  overflow: hidden;
}

.action-button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: width 0.3s ease, height 0.3s ease;
}

.action-button:hover::before {
  width: 100px;
  height: 100px;
}

/* Enhanced tooltip positioning */
.tooltip-container {
  position: relative;
}

.tooltip-container:hover .tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateY(-2px);
}

.tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.9);
  color: white;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
  z-index: 1000;
  backdrop-filter: blur(10px);
}

.tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border: 4px solid transparent;
  border-top-color: rgba(0, 0, 0, 0.9);
}

/* Glow effects for action buttons */
.glow-blue {
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
}

.glow-amber {
  box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
}

.glow-red {
  box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
}

.glow-gray {
  box-shadow: 0 0 20px rgba(107, 114, 128, 0.3);
}

/* Quick actions dropdown enhancements */
.quick-actions-dropdown {
  backdrop-filter: blur(20px);
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.quick-actions-dropdown::before {
  content: '';
  position: absolute;
  top: -6px;
  right: 16px;
  width: 12px;
  height: 12px;
  background: inherit;
  border: inherit;
  border-bottom: none;
  border-right: none;
  transform: rotate(45deg);
  z-index: -1;
}

.action-item {
  transition: all 0.2s ease;
  border-radius: 8px;
  margin: 2px 8px;
}

.action-item:hover {
  transform: translateX(4px);
  background: rgba(59, 130, 246, 0.05);
}

/* Status badge enhanced colors */
.status-active {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.status-inactive {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
}

.status-suspended {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
}

.status-pending {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
}

/* Row deletion animation */
.row-delete-enter-active,
.row-delete-leave-active {
  transition: all 0.5s ease;
}

.row-delete-enter-from {
  opacity: 0;
  transform: translateX(-100px);
}

.row-delete-leave-to {
  opacity: 0;
  transform: translateX(100px) scale(0.8);
  background: linear-gradient(90deg, #fee2e2, #fecaca);
}

/* Success pulse animation */
@keyframes success-pulse {
  0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
  100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

.animate-success-pulse {
  animation: success-pulse 2s infinite;
}

/* Delete button special effects */
.delete-button:hover {
  box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
  transform: scale(1.1) rotate(1deg);
}

.delete-button:active {
  transform: scale(0.95) rotate(-1deg);
}

/* Quick Actions Dropdown Enhanced Styling */
.quick-actions-container .animate-fadeInScale {
  transform-origin: top right;
  z-index: 9999 !important;
  position: fixed !important;
}

/* Ensure dropdown appears above everything */
.quick-actions-container {
  position: relative;
  z-index: 1;
}

/* Individual action item animations */
.action-item {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.action-item:nth-child(1) { animation-delay: 0.1s; }
.action-item:nth-child(2) { animation-delay: 0.15s; }
.action-item:nth-child(3) { animation-delay: 0.2s; }
.action-item:nth-child(4) { animation-delay: 0.25s; }
.action-item:nth-child(5) { animation-delay: 0.3s; }

/* Backdrop blur enhancement */
.dropdown-backdrop {
  backdrop-filter: blur(8px);
}

/* Custom gradient borders */
.gradient-border {
  position: relative;
}

.gradient-border::before {
  content: '';
  position: absolute;
  inset: 0;
  padding: 1px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6, #3b82f6);
  border-radius: inherit;
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: xor;
}

/* Floating animation for dropdown */
@keyframes float-dropdown {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-2px); }
}

.quick-actions-container:hover .animate-fadeInScale {
  animation: float-dropdown 2s ease-in-out infinite;
}

/* Enhanced hover states for actions */
.action-item:hover {
  backdrop-filter: blur(20px);
  background: rgba(255, 255, 255, 0.9);
}

/* Ripple effect */
@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 1;
  }
  100% {
    transform: scale(4);
    opacity: 0;
  }
}

.action-item::after {
  content: '';
  position: absolute;
  inset: 50%;
  width: 5px;
  height: 5px;
  background: currentColor;
  border-radius: 50%;
  transform: scale(0);
  opacity: 0;
  pointer-events: none;
  transition: transform 0.3s, opacity 0.3s;
}

.action-item:active::after {
  animation: ripple 0.6s linear;
}
</style>