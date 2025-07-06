<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: UserModal (Enterprise Create User Modal)
  Project: DocraTech Medical Website Platform
-->

<template>
  <Teleport to="body">
    <Transition name="modal-overlay" appear>
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @mousedown="handleOverlayClick"
        aria-modal="true"
        :aria-label="isEditing ? 'Edit User Modal' : 'Create User Modal'"
      >
        <div
          ref="modalRef"
          class="relative w-full max-w-4xl max-h-[95vh] mx-2 sm:mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden"
          @mousedown.stop
          tabindex="-1"
          role="dialog"
          :aria-labelledby="isEditing ? 'edit-user-title' : 'create-user-title'"
          aria-describedby="modal-description"
        >
          <!-- Loading Overlay -->
          <Transition name="fade">
            <div 
              v-if="loading" 
              class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center"
            >
              <div class="text-center">
                <LoadingSpinner class="w-8 h-8 mx-auto mb-3" />
                <p class="text-sm text-gray-600">{{ isEditing ? 'Updating user...' : 'Creating user...' }}</p>
              </div>
            </div>
          </Transition>
          <!-- Modal Header with Progress -->
          <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#5A1188] to-[#9D38CF] flex items-center justify-center">
                  <UserPlusIcon class="w-5 h-5 text-white" />
                </div>
                <div>
                  <h2 
                    :id="isEditing ? 'edit-user-title' : 'create-user-title'" 
                    class="text-xl font-bold text-gray-900"
                  >
                    {{ isEditing ? 'Edit User' : 'Create New User' }}
                  </h2>
                  <p id="modal-description" class="text-sm text-gray-500">
                    {{ isEditing ? 'Update user information and settings' : 'Add a new user to your organization' }}
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
                <XMarkIcon class="w-6 h-6 font-bold group-hover:scale-110 transition-all duration-300 group-hover:drop-shadow-sm" />
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
                    <CheckCircleIcon class="w-3 h-3 text-green-500" />
                    <span class="text-green-600 text-xs">Ready</span>
                  </div>
                  <div v-else-if="completionPercentage === 100 && !isFormValid" class="flex items-center gap-1">
                    <ExclamationCircleIcon class="w-3 h-3 text-amber-500" />
                    <span class="text-amber-600 text-xs">Validation Required</span>
                  </div>
                  <div v-else-if="completionPercentage > 70" class="flex items-center gap-1">
                    <CloudArrowUpIcon class="w-3 h-3 text-blue-500" />
                    <span class="text-blue-600 text-xs">Almost Done</span>
                  </div>
                </div>
                <div class="flex items-center gap-4">
                  <!-- Auto-save Status -->
                  <div class="flex items-center gap-1" v-if="!isEditing">
                    <i 
                      class="bi text-xs"
                      :class="{
                        'bi-cloud-upload text-blue-500': isAutoSaving,
                        'bi-exclamation-circle text-orange-500': hasUnsavedChanges,
                        'bi-check-circle text-green-500': autoSaveStatus.status === 'saved',
                        'bi-circle text-gray-400': autoSaveStatus.status === 'idle'
                      }"
                    ></i>
                    <span class="text-xs" :class="{
                      'text-blue-600': isAutoSaving,
                      'text-orange-600': hasUnsavedChanges,
                      'text-green-600': autoSaveStatus.status === 'saved',
                      'text-gray-500': autoSaveStatus.status === 'idle'
                    }">
                      {{ autoSaveStatus.message }}
                    </span>
                  </div>
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
          <form @submit.prevent="handleSubmit" class="overflow-y-auto max-h-[calc(95vh-180px)]">
            <!-- Profile Photo Section -->
            <div class="px-6 py-4 bg-gradient-to-r from-[#5A1188]/5 to-[#9D38CF]/5">
              <div class="flex items-center gap-6">
                <div class="relative">
                  <Avatar
                    :src="avatarPreview || form.profile_photo"
                    :name="avatarName"
                    size="2xl"
                    class="ring-4 ring-white shadow-lg"
                  />
                  <button
                    type="button"
                    @click="triggerFileUpload"
                    class="absolute -bottom-1 -right-1 w-8 h-8 bg-[#5A1188] hover:bg-[#9D38CF] text-white rounded-full flex items-center justify-center shadow-lg transition-colors"
                    :aria-label="avatarPreview ? 'Change photo' : 'Add photo'"
                  >
                    <CameraIcon class="w-4 h-4" />
                  </button>
                  <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleFileUpload"
                  />
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">Profile Photo</h3>
                  <p class="text-sm text-gray-500 mb-3">Upload a profile photo for this user. JPG, PNG or GIF (max 5MB)</p>
                  <div class="flex gap-2">
                    <button
                      type="button"
                      @click="triggerFileUpload"
                      class="px-3 py-1.5 text-xs bg-[#5A1188] hover:bg-[#9D38CF] text-white rounded-lg transition-colors"
                    >
                      {{ avatarPreview ? 'Change' : 'Upload' }}
                    </button>
                    <button
                      v-if="avatarPreview || form.profile_photo"
                      type="button"
                      @click="removePhoto"
                      class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="px-6 pb-6">
              <!-- Section 1: Personal Information -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">1</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- First Name -->
                  <div class="form-group">
                    <label class="form-label required">First Name</label>
                    <InputText
                      v-model="form.first_name"
                      placeholder="Enter first name"
                      :error="errors.first_name"
                      :success="!errors.first_name && !!form.first_name"
                      @input="clearError('first_name')"
                      required
                    />
                  </div>

                  <!-- Last Name -->
                  <div class="form-group">
                    <label class="form-label required">Last Name</label>
                    <InputText
                      v-model="form.last_name"
                      placeholder="Enter last name"
                      :error="errors.last_name"
                      :success="!errors.last_name && !!form.last_name"
                      @input="clearError('last_name')"
                      required
                    />
                  </div>

                  <!-- Email -->
                  <div class="form-group">
                    <label class="form-label required">Email Address</label>
                    <InputText
                      v-model="form.email"
                      type="email"
                      placeholder="Enter email address"
                      :error="errors.email"
                      :success="!errors.email && form.email && isValidEmail(form.email)"
                      @input="clearError('email')"
                      required
                    />
                  </div>

                  <!-- Phone -->
                  <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <PhoneInput
                      v-model="form.phone"
                      :error="errors.phone"
                      :default-country="form.phone_country || 'TR'"
                      :is-verified="form.phone_verified || false"
                      help-text="Enter a valid phone number for SMS notifications"
                      show-phone-info
                      show-char-count
                      clearable
                      @country-change="handleCountryChange"
                      @validation-change="handlePhoneValidation"
                      @input="clearError('phone')"
                    />
                  </div>

                  <!-- Bio -->
                  <div class="form-group sm:col-span-2">
                    <label class="form-label">Bio</label>
                    <Textarea
                      v-model="form.bio"
                      placeholder="Brief description about the user"
                      :rows="3"
                      :error="errors.bio"
                      :success="!errors.bio && !!form.bio"
                      @input="clearError('bio')"
                    />
                  </div>
                </div>
              </div>

              <!-- Section 2: Account Settings -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">2</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Account Settings</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Role -->
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

                  <!-- Status -->
                  <div class="form-group">
                    <label class="form-label required">Status</label>
                    <SearchableSelect
                      v-model="form.status"
                      :options="statusOptions"
                      placeholder="Select user status"
                      :error="errors.status"
                      :success="!errors.status && !!form.status"
                      @update:modelValue="clearError('status')"
                      required
                    />
                  </div>

                  <!-- Departments (if available) -->
                  <div class="form-group" v-if="departmentOptions.length">
                    <label class="form-label">Department</label>
                    <SearchableSelect
                      v-model="form.department_id"
                      :options="departmentOptions"
                      placeholder="Select department"
                      :error="errors.department_id"
                      :success="!errors.department_id && !!form.department_id"
                      @update:modelValue="clearError('department_id')"
                    />
                  </div>

                  <!-- Email Verification -->
                  <div class="form-group">
                    <label class="form-label">Email Verification</label>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                      <SwitchToggle
                        v-model="form.email_verified"
                        @update:modelValue="clearError('email_verified')"
                      />
                      <span class="text-sm text-gray-700">Email is verified</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 3: Security Settings -->
              <div class="mb-8" v-if="!isEditing">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">3</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Password -->
                  <div class="form-group">
                    <label class="form-label required">Password</label>
                    <InputText
                      v-model="form.password"
                      type="password"
                      placeholder="Enter password"
                      :error="errors.password"
                      :success="!errors.password && form.password && passwordStrength >= 3"
                      @input="clearError('password')"
                      required
                    />
                  </div>

                  <!-- Confirm Password -->
                  <div class="form-group">
                    <label class="form-label required">Confirm Password</label>
                    <InputText
                      v-model="form.password_confirmation"
                      type="password"
                      placeholder="Confirm password"
                      :error="errors.password_confirmation"
                      :success="!errors.password_confirmation && form.password_confirmation && form.password === form.password_confirmation"
                      @input="clearError('password_confirmation')"
                      required
                    />
                  </div>

                  <!-- Password Strength -->
                  <div class="form-group sm:col-span-2" v-if="form.password">
                    <PasswordStrength :password="form.password" />
                  </div>

                  <!-- Two Factor Authentication -->
                  <div class="form-group sm:col-span-2">
                    <label class="form-label">Two-Factor Authentication</label>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                      <SwitchToggle
                        v-model="form.two_factor_enabled"
                        @update:modelValue="clearError('two_factor_enabled')"
                      />
                      <div>
                        <span class="text-sm text-gray-700 block">Enable 2FA for enhanced security</span>
                        <span class="text-xs text-gray-500">User will be prompted to set up 2FA on first login</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 4: Location & Contact -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">{{ isEditing ? '3' : '4' }}</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Location & Contact</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Address -->
                  <div class="form-group sm:col-span-2">
                    <label class="form-label">Address</label>
                    <InputText
                      v-model="form.address"
                      placeholder="Enter full address"
                      :error="errors.address"
                      :success="!errors.address && !!form.address"
                      @input="clearError('address')"
                    />
                  </div>

                  <!-- City -->
                  <div class="form-group">
                    <label class="form-label">City</label>
                    <InputText
                      v-model="form.city"
                      placeholder="Enter city"
                      :error="errors.city"
                      :success="!errors.city && !!form.city"
                      @input="clearError('city')"
                    />
                  </div>

                  <!-- Country -->
                  <div class="form-group">
                    <label class="form-label">Country</label>
                    <SearchableSelect
                      v-model="form.country"
                      :options="countryOptions"
                      placeholder="Select country"
                      :error="errors.country"
                      :success="!errors.country && !!form.country"
                      @update:modelValue="clearError('country')"
                    />
                  </div>
                </div>
              </div>

              <!-- Section 5: Professional Information -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">{{ isEditing ? '4' : '5' }}</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Professional Information</h3>
                </div>

                <div class="space-y-6">
                  <!-- Education -->
                  <DynamicList
                    v-model="form.education"
                    type="education"
                    title="Education"
                    description="Educational background and qualifications"
                    item-name="Education"
                    icon="bi-mortarboard"
                    @change="handleProfileChange"
                  />

                  <!-- Work Experience -->
                  <DynamicList
                    v-model="form.work_experience"
                    type="work_experience"
                    title="Work Experience"
                    description="Professional work history"
                    item-name="Experience"
                    icon="bi-briefcase"
                    @change="handleProfileChange"
                  />

                  <!-- Specialties -->
                  <DynamicList
                    v-model="form.specialties"
                    type="specialties"
                    title="Specialties"
                    description="Areas of expertise and specialization"
                    item-name="Specialty"
                    icon="bi-star"
                    @change="handleProfileChange"
                  />

                  <!-- Certificates -->
                  <DynamicList
                    v-model="form.certificates"
                    type="certificates"
                    title="Certificates"
                    description="Professional certifications and licenses"
                    item-name="Certificate"
                    icon="bi-award"
                    @change="handleProfileChange"
                  />

                  <!-- Languages -->
                  <DynamicList
                    v-model="form.languages"
                    type="languages"
                    title="Languages"
                    description="Language skills and proficiency levels"
                    item-name="Language"
                    icon="bi-translate"
                    @change="handleProfileChange"
                  />
                </div>
              </div>

              <!-- Section 6: Academic & Publications -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">{{ isEditing ? '5' : '6' }}</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Academic & Publications</h3>
                </div>

                <div class="space-y-6">
                  <!-- Publications -->
                  <DynamicList
                    v-model="form.publications"
                    type="publications"
                    title="Publications"
                    description="Research papers, articles, and academic publications"
                    item-name="Publication"
                    icon="bi-journal-text"
                    @change="handleProfileChange"
                  />

                  <!-- Awards -->
                  <DynamicList
                    v-model="form.awards"
                    type="awards"
                    title="Awards & Honors"
                    description="Professional awards and recognitions"
                    item-name="Award"
                    icon="bi-trophy"
                    @change="handleProfileChange"
                  />
                </div>
              </div>

              <!-- Section 7: Additional Settings -->
              <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                  <div class="w-6 h-6 rounded-full bg-[#5A1188] flex items-center justify-center">
                    <span class="text-white text-xs font-bold">{{ isEditing ? '6' : '7' }}</span>
                  </div>
                  <h3 class="text-lg font-semibold text-gray-900">Additional Settings</h3>
                </div>

                <div class="space-y-4">
                  <!-- Profile Settings -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                      <label class="form-label">Public Profile</label>
                      <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <SwitchToggle
                          v-model="form.profile_public"
                          @update:modelValue="clearError('profile_public')"
                        />
                        <span class="text-sm text-gray-700">Make profile visible to other users</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label">KVKK Approval</label>
                      <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <SwitchToggle
                          v-model="form.kvkk_approved"
                          @update:modelValue="clearError('kvkk_approved')"
                        />
                        <span class="text-sm text-gray-700">KVKK consent provided</span>
                      </div>
                    </div>
                  </div>

                  <!-- Send Welcome Email -->
                  <div class="form-group" v-if="!isEditing">
                    <label class="form-label">Notifications</label>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                      <SwitchToggle
                        v-model="form.send_welcome_email"
                        @update:modelValue="clearError('send_welcome_email')"
                      />
                      <div>
                        <span class="text-sm text-gray-700 block">Send welcome email</span>
                        <span class="text-xs text-gray-500">User will receive account details and setup instructions</span>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Notes -->
                  <div class="form-group">
                    <label class="form-label">Admin Notes</label>
                    <Textarea
                      v-model="form.admin_notes"
                      placeholder="Internal notes about this user (not visible to user)"
                      :rows="3"
                      :error="errors.admin_notes"
                      :success="!errors.admin_notes && !!form.admin_notes"
                      @input="clearError('admin_notes')"
                    />
                  </div>
                </div>
              </div>
            </div>
          </form>

          <!-- Modal Footer -->
          <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4">
            <!-- Enterprise Validation Feedback -->
            <div v-if="!isFormValid && validationMessages.length > 0" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
              <div class="flex items-start gap-2">
                <ExclamationCircleIcon class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                  <h4 class="text-sm font-semibold text-amber-800 mb-2">Form Validation Required</h4>
                  <ul class="text-sm text-amber-700 space-y-1">
                    <li v-for="message in validationMessages" :key="message" class="flex items-center gap-1">
                      <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                      {{ message }}
                    </li>
                  </ul>
                  <!-- Debug info for development -->
                  <details v-if="isDevelopment" class="mt-2">
                    <summary class="text-xs text-amber-600 cursor-pointer hover:text-amber-800">Debug Info</summary>
                    <pre class="text-xs bg-amber-100 p-2 rounded mt-1 overflow-auto">{{ JSON.stringify(validationStatus, null, 2) }}</pre>
                  </details>
                </div>
              </div>
            </div>
            
            <!-- Success indicator when form is valid -->
            <div v-else-if="isFormValid" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-center gap-2">
                <CheckCircleIcon class="w-5 h-5 text-green-600" />
                <span class="text-sm font-medium text-green-800">Form validation passed - Ready to submit</span>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">
                <ShieldCheckIcon class="w-4 h-4 text-[#5A1188] mr-1" />
                All data is encrypted and secure
              </div>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="handleCancel"
                  class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 hover:border-gray-300 rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                  :disabled="loading"
                >
                  <XCircleIcon class="w-4 h-4" />
                  Cancel
                </button>
                <div class="relative">
                  <button
                    type="submit"
                    @click="handleSubmit"
                    :disabled="loading || !isFormValid"
                    class="px-8 py-2.5 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] hover:from-[#6D28D9] hover:to-[#A855F7] text-white rounded-xl font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105"
                    :title="!isFormValid ? 'Please complete all required fields and fix validation errors' : ''"
                  >
                    <LoadingSpinner v-if="loading" class="w-4 h-4" />
                    <CheckCircleIcon v-else-if="isEditing" class="w-4 h-4" />
                    <UserPlusIcon v-else class="w-4 h-4" />
                    {{ isEditing ? 'Update User' : 'Create User' }}
                  </button>
                  
                  <!-- Validation tooltip for disabled state -->
                  <div 
                    v-if="!isFormValid && !loading" 
                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none z-50"
                  >
                    Complete required fields to enable submission
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>

  <!-- Enhanced Confirmation Modal with DocraTech Enterprise Design -->
  <AppDialog
    v-model="showCancelConfirm"
    size="lg"
    :closeOnOverlay="false"
    :closeOnEscape="false"
  >
    <div class="relative overflow-hidden">
      <!-- Premium Background Pattern -->
      <div class="absolute inset-0 bg-gradient-to-br from-[#5A1188]/5 via-transparent to-[#9D38CF]/5"></div>
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#5A1188] to-[#9D38CF]"></div>
      
      <div class="relative px-8 py-10 text-center">
        <!-- Professional Title in Modern Box -->
        <div class="mb-6 p-6 bg-white rounded-2xl border border-gray-200 shadow-lg">
          <div class="flex items-center justify-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-[#5A1188] to-[#9D38CF] flex items-center justify-center">
              <ExclamationTriangleIcon class="w-5 h-5 text-white" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900">
              Unsaved Changes Detected
            </h3>
          </div>
          <p class="text-lg font-medium text-gray-600 text-center">
            Are you sure you want to leave?
          </p>
        </div>
        
        <!-- Enhanced Message in Modern Card -->
        <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-200">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-1">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-gray-900 mb-2">Data Loss Warning</h4>
              <p class="text-gray-700 leading-relaxed">
                You have <span class="font-semibold text-[#5A1188]">unsaved changes</span> that will be permanently lost if you continue. 
                {{ hasChanges ? 'Your progress has been automatically saved locally to prevent data loss, but these changes are not yet submitted to the server.' : '' }}
              </p>
            </div>
          </div>
        </div>
        
        <!-- Professional Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            type="button"
            @click="showCancelConfirm = false"
            class="group px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 hover:border-[#5A1188]/30 rounded-2xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] focus:ring-4 focus:ring-[#5A1188]/20 focus:border-[#5A1188] relative overflow-hidden"
          >
            <span class="absolute inset-0 bg-gradient-to-r from-[#5A1188]/5 to-[#9D38CF]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            <span class="relative flex items-center justify-center gap-3">
              <ArrowLeftIcon class="w-5 h-5 transition-transform duration-300 group-hover:-translate-x-1" />
              <span>Stay and Continue Editing</span>
            </span>
          </button>
          
          <button
            type="button"
            @click="confirmCancel"
            class="group px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-2xl font-semibold transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-[1.02] focus:ring-4 focus:ring-red-500/30 relative overflow-hidden"
          >
            <span class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
            <span class="relative flex items-center justify-center gap-3">
              <TrashIcon class="w-5 h-5 transition-transform duration-300 group-hover:scale-110" />
              <span>Discard Changes and Leave</span>
            </span>
          </button>
        </div>
        
        <!-- Subtle Enterprise Footer -->
        <div class="mt-6 pt-4 border-t border-gray-100">
          <p class="text-xs text-gray-400 flex items-center justify-center gap-1">
            <ShieldCheckIcon class="w-3 h-3" />
            DocraTech Enterprise Security Protocol
          </p>
        </div>
      </div>
    </div>
  </AppDialog>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useToast } from '@/composables/useToast'
import { useAutoSave } from '@/composables/useAutoSave'
import InputText from '@/components/form/InputText.vue'
import PhoneInput from '@/components/form/PhoneInput.vue'
import Textarea from '@/components/form/Textarea.vue'
import SearchableSelect from '@/components/form/SearchableSelect.vue'
import PasswordStrength from '@/components/form/PasswordStrength.vue'
import SwitchToggle from '@/components/form/SwitchToggle.vue'
import Avatar from '@/components/media/Avatar.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import AppDialog from '@/components/modal/AppDialog.vue'
import DynamicList from '@/components/form/DynamicList.vue'

// Heroicons imports
import { 
  UserPlusIcon, 
  XMarkIcon, 
  CameraIcon, 
  ShieldCheckIcon, 
  XCircleIcon,
  ExclamationTriangleIcon,
  ArrowLeftIcon,
  TrashIcon,
  CheckCircleIcon,
  CloudArrowUpIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'

// Props & Emits
const props = defineProps({
  modelValue: Boolean,
  user: { type: Object, default: null },
  roles: { type: Array, default: () => [] },
  departments: { type: Array, default: () => [] },
  loading: Boolean
})

const emit = defineEmits(['update:modelValue', 'save', 'cancel'])

// Composables
const { showToast } = useToast()

// Refs
const modalRef = ref(null)
const fileInput = ref(null)
const showCancelConfirm = ref(false)
const avatarPreview = ref(null)
const avatarFile = ref(null)

// Form state
const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  phone_country: 'TR',
  phone_verified: false,
  bio: '',
  role_id: '',
  status: 'active',
  department_id: '',
  email_verified: false,
  password: '',
  password_confirmation: '',
  two_factor_enabled: false,
  address: '',
  city: '',
  country: '',
  profile_photo: '',
  profile_public: true,
  kvkk_approved: false,
  admin_notes: '',
  send_welcome_email: true,
  // Professional Information Arrays
  education: [],
  work_experience: [],
  specialties: [],
  certificates: [],
  languages: [],
  publications: [],
  awards: [],
  references: [],
  insurances: [],
  documents: []
})

const errors = ref({})
const originalForm = ref({})

// Auto-save functionality
const { 
  isAutoSaving, 
  hasUnsavedChanges, 
  autoSaveStatus, 
  pauseAutoSave, 
  resumeAutoSave,
  loadFromLocalStorage,
  clearLocalStorage 
} = useAutoSave(
  form, 
  null, // We'll handle saving manually
  { 
    delay: 5000, // 5 seconds
    enableToasts: false,
    storageKey: `userModal_${props.user?.id || 'new'}`
  }
)

// Computed
const isEditing = computed(() => !!props.user)
const isDevelopment = computed(() => import.meta.env.DEV)

const avatarName = computed(() => {
  return `${form.value.first_name} ${form.value.last_name}`.trim() || 'User'
})

const roleOptions = computed(() => {
  console.log('🔍 [UserModal] roleOptions computed - props.roles:', props.roles)
  
  if (!props.roles || !Array.isArray(props.roles)) {
    console.warn('⚠️ [UserModal] No valid roles provided via props')
    return []
  }
  
  const options = props.roles.map(role => ({
    value: role.id,
    label: role.display_name || role.name,
    description: role.description || `${role.name} role`
  }))
  
  console.log('✅ [UserModal] Generated role options:', options)
  return options
})

const departmentOptions = computed(() => 
  props.departments.map(dept => ({
    value: dept.id,
    label: dept.name,
    description: dept.description
  }))
)

const statusOptions = computed(() => [
  { value: 'active', label: 'Active', description: 'User can access the system' },
  { value: 'inactive', label: 'Inactive', description: 'User access is disabled' },
  { value: 'pending', label: 'Pending', description: 'Awaiting activation' }
])

const countryOptions = computed(() => [
  { value: 'TR', label: 'Turkey' },
  { value: 'US', label: 'United States' },
  { value: 'GB', label: 'United Kingdom' },
  { value: 'DE', label: 'Germany' },
  { value: 'FR', label: 'France' },
  // Add more countries as needed
])

// Form validation and progress
const requiredFields = computed(() => {
  const base = ['first_name', 'last_name', 'email', 'role_id', 'status']
  if (!isEditing.value) {
    base.push('password', 'password_confirmation')
  }
  return base
})

const totalFields = computed(() => {
  // Only count required fields for progress calculation
  return requiredFields.value.length
})

const completedFields = computed(() => {
  // Only count completed required fields
  return requiredFields.value.filter(field => {
    const value = form.value[field]
    return value !== '' && value !== null && value !== undefined
  }).length
})

const completionPercentage = computed(() => {
  if (totalFields.value === 0) return 100
  return Math.round((completedFields.value / totalFields.value) * 100)
})

const passwordStrength = computed(() => {
  const password = form.value.password
  if (!password) return 0
  
  let strength = 0
  if (password.length >= 8) strength++
  if (/[A-Z]/.test(password)) strength++
  if (/[a-z]/.test(password)) strength++
  if (/[0-9]/.test(password)) strength++
  if (/[^A-Za-z0-9]/.test(password)) strength++
  
  return strength
})

const isFormValid = computed(() => {
  // Check basic required fields only
  const requiredValid = requiredFields.value.every(field => 
    form.value[field] !== '' && form.value[field] !== null
  )
  
  // Check email format
  const emailValid = !form.value.email || isValidEmail(form.value.email)
  
  // For new users, check password match and strength
  if (!isEditing.value) {
    const passwordMatch = form.value.password === form.value.password_confirmation
    const passwordStrong = passwordStrength.value >= 3
    return requiredValid && emailValid && passwordMatch && passwordStrong
  }
  
  // For editing users, only basic fields and email
  return requiredValid && emailValid
})

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value)
})

// Validation feedback for enterprise-level debugging
const validationStatus = computed(() => {
  const status = {
    requiredFields: {},
    validations: {},
    canSubmit: false,
    missingCount: 0,
    errorCount: 0
  }
  
  // Check each required field
  requiredFields.value.forEach(field => {
    const value = form.value[field]
    const isValid = value !== '' && value !== null && value !== undefined
    status.requiredFields[field] = {
      value,
      isValid,
      isEmpty: !value || value === ''
    }
    if (!isValid) status.missingCount++
  })
  
  // Check email format
  status.validations.email = {
    isValid: !form.value.email || isValidEmail(form.value.email),
    error: form.value.email && !isValidEmail(form.value.email) ? 'Invalid email format' : null
  }
  
  // Check password for new users
  if (!isEditing.value) {
    status.validations.password = {
      strength: passwordStrength.value,
      isStrong: passwordStrength.value >= 3,
      match: form.value.password === form.value.password_confirmation,
      error: passwordStrength.value < 3 ? 'Password too weak' : 
             form.value.password !== form.value.password_confirmation ? 'Passwords do not match' : null
    }
  }
  
  // Count total errors
  status.errorCount = Object.keys(errors.value).length
  
  // Can submit?
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
          'first_name': 'First Name',
          'last_name': 'Last Name', 
          'email': 'Email Address',
          'role_id': 'Role',
          'status': 'Status',
          'password': 'Password',
          'password_confirmation': 'Password Confirmation'
        }
        return fieldNames[field] || field
      })
    
    messages.push(`Missing required fields: ${missing.join(', ')}`)
  }
  
  if (!status.validations.email.isValid) {
    messages.push('Invalid email format')
  }
  
  if (!isEditing.value && status.validations.password?.error) {
    messages.push(status.validations.password.error)
  }
  
  if (status.errorCount > 0) {
    messages.push(`${status.errorCount} validation error(s) found`)
  }
  
  return messages
})

// Methods
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function clearError(field) {
  if (errors.value[field]) {
    delete errors.value[field]
  }
}

// Phone handlers
function handleCountryChange(country) {
  form.value.phone_country = country.iso
}

function handlePhoneValidation(validation) {
  if (validation.errors && validation.errors.length > 0) {
    errors.value.phone = validation.errors[0]
  } else {
    delete errors.value.phone
  }
}

function triggerFileUpload() {
  fileInput.value?.click()
}

function handleFileUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  // Validate file type
  if (!file.type.startsWith('image/')) {
    showToast('Please select a valid image file', 'error')
    return
  }

  // Validate file size (5MB)
  if (file.size > 5 * 1024 * 1024) {
    showToast('File size must be less than 5MB', 'error')
    return
  }

  avatarFile.value = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

function removePhoto() {
  avatarPreview.value = null
  avatarFile.value = null
  form.value.profile_photo = ''
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function resetForm() {
  if (props.user) {
    // Editing existing user
    form.value = {
      first_name: props.user.first_name || '',
      last_name: props.user.last_name || '',
      email: props.user.email || '',
      phone: props.user.phone || '',
      phone_country: props.user.phone_country || 'TR',
      phone_verified: props.user.phone_verified || false,
      bio: props.user.bio || '',
      role_id: props.user.role_id || '',
      status: props.user.status || 'active',
      department_id: props.user.department_id || '',
      email_verified: props.user.email_verified || false,
      password: '',
      password_confirmation: '',
      two_factor_enabled: props.user.two_factor_enabled || false,
      address: props.user.address || '',
      city: props.user.city || '',
      country: props.user.country || '',
      profile_photo: props.user.profile_photo || '',
      profile_public: props.user.profile_public ?? true,
      kvkk_approved: props.user.kvkk_approved || false,
      admin_notes: props.user.admin_notes || '',
      send_welcome_email: false,
      // Professional Information Arrays
      education: props.user.education || [],
      work_experience: props.user.work_experience || [],
      specialties: props.user.specialties || [],
      certificates: props.user.certificates || [],
      languages: props.user.languages || [],
      publications: props.user.publications || [],
      awards: props.user.awards || [],
      references: props.user.references || [],
      insurances: props.user.insurances || [],
      documents: props.user.documents || []
    }
  } else {
    // Creating new user
    form.value = {
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      phone_country: 'TR',
      phone_verified: false,
      bio: '',
      role_id: '',
      status: 'active',
      department_id: '',
      email_verified: false,
      password: '',
      password_confirmation: '',
      two_factor_enabled: false,
      address: '',
      city: '',
      country: '',
      profile_photo: '',
      profile_public: true,
      kvkk_approved: false,
      admin_notes: '',
      send_welcome_email: true,
      // Professional Information Arrays
      education: [],
      work_experience: [],
      specialties: [],
      certificates: [],
      languages: [],
      publications: [],
      awards: [],
      references: [],
      insurances: [],
      documents: []
    }
  }
  
  originalForm.value = { ...form.value }
  errors.value = {}
  avatarPreview.value = null
  avatarFile.value = null
}

function handleSubmit() {
  if (props.loading) return
  
  // Pause auto-save during submission
  pauseAutoSave()
  
  if (!validateForm()) {
    showToast('Please fix the errors in the form', 'error')
    resumeAutoSave()
    return
  }

  const formData = prepareFormData()

  // Form data prepared for submission

  emit('save', {
    formData,
    isEditing: isEditing.value,
    userId: props.user?.id
  })
  
  // Clear auto-save data after successful submission
  clearLocalStorage()
}

function handleCancel() {
  if (hasChanges.value) {
    showCancelConfirm.value = true
  } else {
    clearLocalStorage()
    emit('update:modelValue', false)
    emit('cancel')
  }
}

function confirmCancel() {
  showCancelConfirm.value = false
  resetForm()
  clearLocalStorage()
  emit('update:modelValue', false)
  emit('cancel')
}

function handleOverlayClick(event) {
  if (event.target === event.currentTarget) {
    handleCancel()
  }
}

function handleEscape(event) {
  if (event.key === 'Escape' && props.modelValue) {
    handleCancel()
  }
}

function handleProfileChange() {
  // This method is called when profile arrays change
  // Auto-save will handle the saving automatically
}

// Enhanced form submission to handle arrays and booleans properly
function prepareFormData() {
  // Boolean fields that need explicit conversion
  const booleanFields = ['send_welcome_email', 'two_factor_enabled', 'profile_public', 'kvkk_approved', 'email_verified']
  
  // If there's an avatar file, use FormData
  if (avatarFile.value) {
    const formData = new FormData()
    
    // Add basic form fields
    Object.entries(form.value).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== '') {
        // Handle arrays by converting to JSON
        if (Array.isArray(value)) {
          formData.append(key, JSON.stringify(value))
        } 
        // Handle boolean fields - ensure they are true boolean values
        else if (booleanFields.includes(key)) {
          // Convert to proper boolean: true/false, "true"/"false", 1/0, "1"/"0", "on"/"off"
          const boolValue = value === true || value === 'true' || value === 1 || value === '1' || value === 'on'
          formData.append(key, boolValue ? 'true' : 'false')
        }
        else {
          formData.append(key, value)
        }
      } else if (booleanFields.includes(key)) {
        // For boolean fields, always include them even if empty/null - default to false
        formData.append(key, 'false')
      }
    })

    // Add avatar file
    formData.append('avatar_file', avatarFile.value)
    return formData
  }
  
  // No file upload - use JSON object for better Laravel validation
  const jsonData = {}
  
  Object.entries(form.value).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      // Handle boolean fields - ensure they are true boolean values
      if (booleanFields.includes(key)) {
        // Convert to proper boolean
        jsonData[key] = value === true || value === 'true' || value === 1 || value === '1' || value === 'on'
      }
      // Handle arrays and other values
      else {
        jsonData[key] = value
      }
    } else if (booleanFields.includes(key)) {
      // For boolean fields, always include them even if empty/null - default to false
      jsonData[key] = false
    }
  })

  // Clean up empty password fields for editing
  if (isEditing.value && (!jsonData.password || jsonData.password === '')) {
    delete jsonData.password
    delete jsonData.password_confirmation
  }

  return jsonData
}

function validateForm() {
  errors.value = {}
  let isValid = true

  // ONLY basic required field validation - NO dynamic sections
  requiredFields.value.forEach(field => {
    if (!form.value[field]) {
      errors.value[field] = 'This field is required'
      isValid = false
    }
  })

  // Email validation
  if (form.value.email && !isValidEmail(form.value.email)) {
    errors.value.email = 'Please enter a valid email address'
    isValid = false
  }

  // Password validation for new users
  if (!isEditing.value) {
    if (form.value.password !== form.value.password_confirmation) {
      errors.value.password_confirmation = 'Passwords do not match'
      isValid = false
    }
    
    if (passwordStrength.value < 3) {
      errors.value.password = 'Password is too weak. Please use a stronger password.'
      isValid = false
    }
  }

  // Dynamic sections are always valid (optional)
  // education, work_experience, specialties, certificates, languages, publications, awards
  // These arrays can be empty without preventing form submission

  return isValid
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

watch(() => props.user, () => {
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
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
