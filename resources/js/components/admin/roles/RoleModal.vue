<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: RoleModal (Enterprise Create/Edit Role Modal)
  Project: DocraTech Medical Website Platform
  Version: 3.0 - Enterprise Level Design
-->

<template>
  <Teleport to="body">
    <Transition name="modal-overlay" appear>
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @mousedown="handleOverlayClick"
        aria-modal="true"
        :aria-label="isEditing ? 'Edit Role Modal' : 'Create Role Modal'"
      >
        <div
          ref="modalRef"
          class="relative w-full max-w-5xl max-h-[95vh] mx-2 sm:mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden"
          @mousedown.stop
          tabindex="-1"
          role="dialog"
          :aria-labelledby="isEditing ? 'edit-role-title' : 'create-role-title'"
          aria-describedby="modal-description"
        >
          <!-- Loading Overlay -->
          <Transition name="fade">
            <div 
              v-if="saving" 
              class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center"
            >
              <div class="text-center">
                <div class="animate-spin w-8 h-8 mx-auto mb-3 border-4 border-[#5A1188] border-t-transparent rounded-full"></div>
                <p class="text-sm text-gray-600">{{ isEditing ? 'Updating role...' : 'Creating role...' }}</p>
              </div>
            </div>
          </Transition>

          <!-- Modal Header with Progress -->
          <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#5A1188] to-[#9D38CF] flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                  </svg>
                </div>
                <div>
                  <h2 
                    :id="isEditing ? 'edit-role-title' : 'create-role-title'" 
                    class="text-xl font-bold text-gray-900"
                  >
                    {{ isEditing ? 'Edit Role' : 'Create New Role' }}
                  </h2>
                  <p id="modal-description" class="text-sm text-gray-500">
                    {{ isEditing ? 'Update role information and permissions' : 'Add a new role to your organization' }}
                  </p>
                </div>
              </div>
              <button
                type="button"
                @click="handleCancel"
                class="flex items-center justify-center w-12 h-12 bg-white text-gray-400 hover:text-white hover:bg-gradient-to-r hover:from-[#5A1188] hover:to-[#9D38CF] border-2 border-gray-200 hover:border-transparent rounded-full transition-all duration-300 shadow-lg hover:shadow-xl group z-50 relative transform hover:scale-110"
                aria-label="Close modal"
                title="Close Modal"
              >
                <svg class="w-6 h-6 font-bold group-hover:scale-110 transition-all duration-300 group-hover:drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Progress Bar -->
            <div class="relative">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-[#5A1188] to-[#9D38CF] transition-all duration-500 ease-out"
                  :style="{ width: `${completionPercentage}%` }"
                ></div>
              </div>
              <div class="flex justify-between text-xs text-gray-500 mt-1">
                <div class="flex items-center gap-2">
                  <span>{{ Math.round(completionPercentage) }}% Complete</span>
                  <!-- Progress status indicator -->
                  <div v-if="completionPercentage === 100 && isFormValid" class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-600 text-xs">Ready</span>
                  </div>
                  <div v-else-if="completionPercentage === 100 && !isFormValid" class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span class="text-amber-600 text-xs">Validation Required</span>
                  </div>
                  <div v-else-if="completionPercentage > 70" class="flex items-center gap-1">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span class="text-blue-600 text-xs">Almost Done</span>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <span class="font-medium">{{ completedFields }}/{{ totalFields }} required fields</span>
                  <!-- Missing fields indicator -->
                  <div v-if="validationStatus.missingCount > 0" class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></span>
                    <span class="text-red-600 text-xs">{{ validationStatus.missingCount }} missing</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Body -->
          <form @submit.prevent="saveRole" class="overflow-y-auto max-h-[calc(95vh-180px)]">
            <div class="px-6 pb-6">
              <!-- Section 1: Basic Information -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">1</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Role Name -->
                  <div class="form-group">
                    <label class="form-label required">Role Name</label>
                    <input
                      v-model="form.name"
                      type="text"
                      placeholder="Enter role name"
                      class="form-input"
                      :class="{ 'form-input-error': errors.name, 'form-input-success': !errors.name && form.name }"
                      @input="clearError('name')"
                      required
                    />
                    <div v-if="errors.name" class="form-error">{{ errors.name[0] }}</div>
                  </div>

                  <!-- Role Slug -->
                  <div class="form-group">
                    <label class="form-label required">Role Slug</label>
                    <input
                      v-model="form.slug"
                      type="text"
                      placeholder="Enter role slug (e.g., doctor, nurse)"
                      class="form-input"
                      :class="{ 'form-input-error': errors.slug, 'form-input-success': !errors.slug && form.slug }"
                      @input="clearError('slug')"
                      required
                    />
                    <div v-if="errors.slug" class="form-error">{{ errors.slug[0] }}</div>
                  </div>

                  <!-- Description -->
                  <div class="form-group sm:col-span-2">
                    <label class="form-label">Description</label>
                    <textarea
                      v-model="form.description"
                      placeholder="Enter role description"
                      rows="3"
                      class="form-textarea"
                      :class="{ 'form-textarea-error': errors.description, 'form-textarea-success': !errors.description && form.description }"
                      @input="clearError('description')"
                    ></textarea>
                    <div v-if="errors.description" class="form-error">{{ errors.description[0] }}</div>
                  </div>
                </div>
              </div>

              <!-- Section 2: Role Settings -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">2</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Role Settings</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <!-- Status Toggle -->
                  <div class="form-group">
                    <label class="form-label">Role Status</label>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                      <button
                        type="button"
                        :aria-checked="form.is_active"
                        role="switch"
                        @click="form.is_active = !form.is_active"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#5A1188] focus:ring-offset-2"
                        :class="form.is_active ? 'bg-[#10b981]' : 'bg-gray-200'"
                      >
                        <span
                          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                          :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                        ></span>
                      </button>
                      <div>
                        <span class="text-sm font-medium text-gray-900">
                          {{ form.is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <p class="text-xs text-gray-500">
                          {{ form.is_active ? 'Role is active and can be assigned' : 'Role is inactive and cannot be assigned' }}
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Role Type -->
                  <div class="form-group">
                    <label class="form-label">Role Type</label>
                    <div class="p-4 bg-gradient-to-r from-[#5A1188]/5 to-[#9D38CF]/5 rounded-xl border border-[#5A1188]/20">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#5A1188] flex items-center justify-center">
                          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"/>
                          </svg>
                        </div>
                        <div>
                          <span class="text-sm font-medium text-gray-900">Custom Role</span>
                          <p class="text-xs text-gray-500">User-defined role with custom permissions</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 3: Permission Management -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">3</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Permission Management</h3>
                  <div class="flex items-center gap-2 ml-auto">
                    <span class="text-sm text-gray-500">{{ form.permissions.length }} permissions selected</span>
                    <button
                      type="button"
                      @click="selectAllPermissions"
                      class="text-xs text-[#5A1188] hover:text-[#9D38CF] font-medium transition-colors"
                    >
                      Select All
                    </button>
                    <button
                      type="button"
                      @click="clearAllPermissions"
                      class="text-xs text-gray-500 hover:text-red-600 font-medium transition-colors"
                    >
                      Clear All
                    </button>
                  </div>
                </div>

                <!-- Permission Search -->
                <div class="mb-4">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                      </svg>
                    </div>
                    <input
                      v-model="permissionSearch"
                      type="text"
                      placeholder="Search permissions..."
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#5A1188] focus:border-[#5A1188] sm:text-sm"
                    />
                  </div>
                </div>

                <!-- Permission Categories -->
                <div class="space-y-4">
                  <div
                    v-for="group in groupedPermissions"
                    :key="group.module"
                    class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                  >
                    <!-- Category Header -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                          <div class="w-6 h-6 rounded-lg bg-[#5A1188] flex items-center justify-center">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                          </div>
                          <h4 class="font-semibold text-gray-900">{{ group.module }}</h4>
                          <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">
                            {{ group.permissions.length }} permissions
                          </span>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="text-xs text-gray-500">
                            {{ getSelectedCount(group.permissions) }}/{{ group.permissions.length }} selected
                          </span>
                          <button
                            type="button"
                            @click="toggleCategory(group.permissions)"
                            class="text-xs text-[#5A1188] hover:text-[#9D38CF] font-medium transition-colors"
                          >
                            {{ isCategorySelected(group.permissions) ? 'Deselect All' : 'Select All' }}
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Permissions Grid -->
                    <div class="p-4">
                      <div class="grid grid-cols-1 gap-3">
                        <div
                          v-for="permission in group.permissions"
                          :key="permission.id"
                          class="flex items-start gap-4 p-4 bg-white hover:bg-gray-50 border border-gray-200 hover:border-[#5A1188]/30 rounded-xl transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md"
                          @click="togglePermission(permission.id)"
                        >
                          <div class="flex-shrink-0 mt-1">
                            <input
                              :id="`permission_${permission.id}`"
                              v-model="form.permissions"
                              :value="permission.id"
                              type="checkbox"
                              class="h-5 w-5 text-[#5A1188] border-gray-300 rounded focus:ring-[#5A1188] focus:ring-2 transition-colors"
                            />
                          </div>
                          <label :for="`permission_${permission.id}`" class="flex-1 cursor-pointer min-w-0">
                            <div class="space-y-2">
                              <!-- Permission Name with Icon -->
                              <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-r from-[#5A1188]/10 to-[#9D38CF]/10 flex items-center justify-center">
                                  <svg class="w-3 h-3 text-[#5A1188]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                  </svg>
                                </div>
                                <div class="text-base font-semibold text-gray-900 leading-tight">
                                  {{ formatPermissionName(permission.name) }}
                                </div>
                                <div v-if="form.permissions.includes(permission.id)" class="ml-auto">
                                  <div class="w-6 h-6 rounded-full bg-[#10b981] flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                  </div>
                                </div>
                              </div>
                              
                              <!-- Permission Description -->
                              <div v-if="permission.description" class="text-sm text-gray-600 leading-relaxed">
                                {{ permission.description }}
                              </div>
                              
                              <!-- Permission Key (if different from name) -->
                              <div v-if="permission.key && permission.key !== permission.name" class="flex items-center gap-2">
                                <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                  {{ permission.key }}
                                </span>
                                <span class="text-xs text-gray-400">System Key</span>
                              </div>
                              
                              <!-- Permission Type Badge -->
                              <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                      :class="{
                                        'bg-blue-100 text-blue-800': permission.type === 'system',
                                        'bg-purple-100 text-purple-800': permission.type === 'custom',
                                        'bg-green-100 text-green-800': permission.type === 'hipaa',
                                        'bg-orange-100 text-orange-800': permission.type === 'medical',
                                        'bg-gray-100 text-gray-800': !permission.type
                                      }">
                                  {{ formatPermissionType(permission.type) }}
                                </span>
                                <span v-if="permission.guard_name && permission.guard_name !== 'web'" 
                                      class="text-xs text-gray-500">
                                  Guard: {{ permission.guard_name }}
                                </span>
                              </div>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-if="errors.permissions" class="form-error mt-2">{{ errors.permissions[0] }}</div>
              </div>
            </div>
          </form>

          <!-- Modal Footer -->
          <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4">
            <!-- Enterprise Validation Feedback -->
            <div v-if="!isFormValid && validationMessages.length > 0" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
              <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div class="flex-1">
                  <h4 class="text-sm font-semibold text-amber-800 mb-2">Form Validation Required</h4>
                  <ul class="text-sm text-amber-700 space-y-1">
                    <li v-for="message in validationMessages" :key="message" class="flex items-center gap-1">
                      <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                      {{ message }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            
            <!-- Success indicator when form is valid -->
            <div v-else-if="isFormValid" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-green-800">Form validation passed - Ready to submit</span>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">
                <svg class="w-4 h-4 text-[#5A1188] mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                All data is encrypted and secure
              </div>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="handleCancel"
                  class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 hover:border-gray-300 rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                  :disabled="saving"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Cancel
                </button>
                <div class="relative">
                  <button
                    type="submit"
                    :disabled="saving || !isFormValid"
                    class="px-6 py-2.5 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transform hover:scale-105"
                    @click="saveRole"
                  >
                    <svg v-if="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                      <path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ saving ? 'Saving...' : (isEditing ? 'Update Role' : 'Create Role') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed, reactive, ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  modelValue: Boolean,
  role: { type: Object, default: null },
  permissions: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue', 'close', 'saved'])

const rolesStore = useRolesStore()
const { error: showError, success: showSuccess } = useToast()

// Refs
const modalRef = ref(null)
const saving = ref(false)
const permissionSearch = ref('')

// Form state
const form = reactive({
  name: '',
  slug: '',
  description: '',
  is_active: true,
  permissions: []
})

const errors = reactive({})

// Computed
const isEditing = computed(() => !!props.role)

// Form validation and progress
const requiredFields = computed(() => ['name', 'slug'])

const totalFields = computed(() => requiredFields.value.length)

const completedFields = computed(() => {
  return requiredFields.value.filter(field => {
    const value = form[field]
    return value !== '' && value !== null && value !== undefined
  }).length
})

const completionPercentage = computed(() => {
  if (totalFields.value === 0) return 100
  return Math.round((completedFields.value / totalFields.value) * 100)
})

const isFormValid = computed(() => {
  return requiredFields.value.every(field => 
    form[field] !== '' && form[field] !== null
  )
})

// Validation feedback
const validationStatus = computed(() => {
  const status = {
    requiredFields: {},
    canSubmit: false,
    missingCount: 0
  }
  
  requiredFields.value.forEach(field => {
    const value = form[field]
    const isValid = value !== '' && value !== null && value !== undefined
    status.requiredFields[field] = {
      value,
      isValid,
      isEmpty: !value || value === ''
    }
    if (!isValid) status.missingCount++
  })
  
  status.canSubmit = isFormValid.value
  
  return status
})

const validationMessages = computed(() => {
  const messages = []
  const status = validationStatus.value
  
  if (status.missingCount > 0) {
    const missing = Object.entries(status.requiredFields)
      .filter(([_, data]) => !data.isValid)
      .map(([field, _]) => {
        const fieldNames = {
          'name': 'Role Name',
          'slug': 'Role Slug'
        }
        return fieldNames[field] || field
      })
    messages.push(`Please fill in required fields: ${missing.join(', ')}`)
  }
  
  return messages
})

// Filter & group permissions
const filteredPermissions = computed(() => {
  if (!permissionSearch.value) return props.permissions
  const search = permissionSearch.value.toLowerCase()
  return props.permissions.filter(p =>
    p.name.toLowerCase().includes(search) ||
    (p.description && p.description.toLowerCase().includes(search))
  )
})

const groupedPermissions = computed(() => {
  const groups = {}
  filteredPermissions.value.forEach(p => {
    const module = p.module || 'General'
    if (!groups[module]) groups[module] = []
    groups[module].push(p)
  })
  return Object.keys(groups).map(module => ({
    module,
    permissions: groups[module]
  }))
})

// Permission management methods
const selectAllPermissions = () => {
  form.permissions = props.permissions.map(p => p.id)
}

const clearAllPermissions = () => {
  form.permissions = []
}

const togglePermission = (permissionId) => {
  const index = form.permissions.indexOf(permissionId)
  if (index > -1) {
    form.permissions.splice(index, 1)
  } else {
    form.permissions.push(permissionId)
  }
}

const toggleCategory = (permissions) => {
  const permissionIds = permissions.map(p => p.id)
  const allSelected = permissionIds.every(id => form.permissions.includes(id))
  
  if (allSelected) {
    form.permissions = form.permissions.filter(id => !permissionIds.includes(id))
  } else {
    const newPermissions = permissionIds.filter(id => !form.permissions.includes(id))
    form.permissions.push(...newPermissions)
  }
}

const isCategorySelected = (permissions) => {
  const permissionIds = permissions.map(p => p.id)
  return permissionIds.every(id => form.permissions.includes(id))
}

const getSelectedCount = (permissions) => {
  const permissionIds = permissions.map(p => p.id)
  return permissionIds.filter(id => form.permissions.includes(id)).length
}

// Permission formatting functions
const formatPermissionName = (name) => {
  if (!name) return 'Unknown Permission'
  
  // Convert snake_case or kebab-case to Title Case
  return name
    .replace(/[-_]/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase())
    .trim()
}

const formatPermissionType = (type) => {
  if (!type) return 'General'
  
  const typeMap = {
    'system': 'System',
    'custom': 'Custom',
    'hipaa': 'HIPAA',
    'medical': 'Medical',
    'admin': 'Administrative',
    'user': 'User',
    'role': 'Role Management',
    'permission': 'Permission Management'
  }
  
  return typeMap[type] || type.charAt(0).toUpperCase() + type.slice(1)
}

// Form methods
const clearError = (field) => {
  if (errors[field]) {
    delete errors[field]
  }
}

const resetForm = () => {
  if (props.role) {
    form.name = props.role.name
    form.slug = props.role.slug
    form.description = props.role.description || ''
    form.is_active = props.role.is_active
    form.permissions = props.role.permissions?.map(p => p.id) || []
  } else {
    form.name = ''
    form.slug = ''
    form.description = ''
    form.is_active = true
    form.permissions = []
  }
  Object.keys(errors).forEach(key => delete errors[key])
  permissionSearch.value = ''
}

const saveRole = async () => {
  if (saving.value || !isFormValid.value) return
  
  saving.value = true
  Object.keys(errors).forEach(key => delete errors[key])
  
  try {
    const payload = {
      name: form.slug || form.name,
      display_name: form.name,
      description: form.description,
      type: 'custom',
      status: form.is_active ? 'active' : 'inactive',
      permissions: form.permissions
    }
    
    let response
    if (isEditing.value && props.role?.id) {
      response = await rolesStore.updateRole(props.role.id, payload)
      showSuccess('Role updated successfully!')
    } else {
      response = await rolesStore.createRole(payload)
      showSuccess('Role created successfully!')
    }
    
    emit('saved', response?.data)
    emit('update:modelValue', false)
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      Object.assign(errors, err.response.data.errors)
    } else {
      showError('Failed to save role. Please try again.')
    }
  } finally {
    saving.value = false
  }
}

const handleCancel = () => {
  emit('update:modelValue', false)
  emit('close')
}

const handleOverlayClick = (event) => {
  if (event.target === event.currentTarget) {
    handleCancel()
  }
}

const handleEscape = (event) => {
  if (event.key === 'Escape' && props.modelValue) {
    handleCancel()
  }
}

// Watchers
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    resetForm()
    nextTick(() => {
      modalRef.value?.focus()
    })
  }
})

watch(() => props.role, () => {
  if (props.modelValue) {
    resetForm()
  }
})

// Lifecycle
onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
.form-group {
  @apply mb-4;
}

.form-label {
  @apply block text-sm font-semibold text-gray-700 mb-2;
}

.form-label.required::after {
  content: ' *';
  @apply text-red-500;
}

.form-input {
  @apply block w-full px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#5A1188] focus:border-[#5A1188] sm:text-sm transition-colors;
}

.form-input-error {
  @apply border-red-300 focus:ring-red-500 focus:border-red-500;
}

.form-input-success {
  @apply border-green-300 focus:ring-green-500 focus:border-green-500;
}

.form-textarea {
  @apply block w-full px-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#5A1188] focus:border-[#5A1188] sm:text-sm transition-colors resize-none;
}

.form-textarea-error {
  @apply border-red-300 focus:ring-red-500 focus:border-red-500;
}

.form-textarea-success {
  @apply border-green-300 focus:ring-green-500 focus:border-green-500;
}

.form-error {
  @apply text-sm text-red-600 mt-1;
}

/* Modal animations */
.modal-overlay-enter-active,
.modal-overlay-leave-active {
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.modal-overlay-enter-from,
.modal-overlay-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

/* Loading overlay animation */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
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
