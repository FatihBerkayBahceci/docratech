<!--
  RolesManager.vue
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Marka kitine tam uyumlu, Tailwind utility-first, enterprise seviye Roles yönetimi
-->

<template>
  <main class="w-full max-w-[1200px] mx-auto px-4 py-10">
    <!-- Header -->
    <div class="flex justify-between items-center bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-2xl text-white px-9 py-7 mb-10 shadow-xl">
      <div>
        <h1 class="flex items-center gap-3 text-3xl font-extrabold font-inter mb-1 animate-fade-in-up">
          <i class="bi bi-shield-lock text-2xl"></i>
          Roles Management
        </h1>
        <p class="text-[#e0e7ff] font-inter text-base">Manage user roles across your medical platform</p>
      </div>
      <button
        class="flex items-center gap-2 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white px-6 py-3 rounded-xl font-medium font-inter text-lg shadow-lg transition hover:scale-105 hover:shadow-2xl active:scale-100 focus:outline-none focus:ring-2 focus:ring-[#9D38CF] animate-fade-in"
        @click="openCreateModal"
        :disabled="loading"
      >
        <i class="bi bi-plus-circle"></i> Create Role
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center mt-16 gap-4 animate-fade-in">
      <span class="animate-spin rounded-full w-12 h-12 border-4 border-[#ede9fe] border-t-[#9D38CF]"></span>
      <div class="text-[#bcbacd] text-lg font-inter">Loading roles...</div>
    </div>

    <!-- Roles Grid -->
    <div v-else-if="roles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
      <div
        v-for="role in roles"
        :key="role.id"
        class="group bg-[#211232] rounded-2xl shadow-xl p-7 flex flex-col gap-2 transition-all duration-200 hover:shadow-2xl hover:scale-[1.025] animate-fade-in-up"
      >
        <div class="flex items-center gap-4 mb-2">
          <div class="w-14 h-14 rounded-full bg-[#9D38CF1F] text-[#9D38CF] font-bold text-2xl flex items-center justify-center shadow-md">
            {{ role.name.charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1">
            <div class="text-white text-lg font-semibold font-inter">{{ role.name }}</div>
            <div class="text-[#9D38CF] text-sm font-inter">{{ role.slug }}</div>
          </div>
          <span
            class="flex items-center gap-2 px-3 py-1 rounded-full font-inter text-xs capitalize font-semibold transition-all duration-300 shadow-sm"
            :class="role.is_active
              ? 'bg-[#10b98120] text-[#10b981] border-l-4 border-[#10b981]'
              : 'bg-[#ef444420] text-[#ef4444] border-l-4 border-[#ef4444]'"
          >
            <i class="bi bi-circle-fill"></i> {{ role.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div class="mb-4 text-[#C9BFE6] min-h-[36px] font-inter text-[1rem]">{{ role.description || 'No description' }}</div>
        <div class="flex justify-end">
          <button
            class="flex items-center gap-2 bg-[#2a183d] text-[#9D38CF] border border-[#9D38CF] rounded-xl font-inter px-4 py-2 font-semibold transition hover:bg-[#9D38CF] hover:text-white shadow"
            @click="openEditModal(role)"
            title="Edit Role"
          >
            <i class="bi bi-pencil"></i> Edit Role
          </button>
        </div>
      </div>
    </div>

    <div v-else class="flex flex-col items-center mt-20 text-center animate-fade-in-up">
      <i class="bi bi-shield-lock text-5xl text-[#6C5988] mb-4"></i>
      <h4 class="text-[#C9BFE6] text-xl font-semibold font-inter">No roles found</h4>
      <p class="text-[#8477A7] font-inter">Try creating your first role.</p>
    </div>

    <!-- Create/Edit Modal -->
    <RoleModal
      v-if="showModal"
      :role="selectedRole"
      :permissions="permissions"
      @close="closeModal"
      @saved="onRoleSaved"
    />

    <!-- Toast -->
    <transition name="fade">
      <div
        v-if="toast.visible"
        :class="[
          'fixed top-10 right-10 z-[9999] flex items-center gap-3 px-6 py-3 rounded-xl shadow-lg font-inter text-base font-semibold min-w-[260px] animate-fade-in',
          toast.type === 'success'
            ? 'bg-[#201137] text-[#10b981] border-l-4 border-[#10b981]'
            : 'bg-[#201137] text-[#ef4444] border-l-4 border-[#ef4444]'
        ]"
        role="status"
        aria-live="polite"
      >
        <i :class="toast.icon + ' text-2xl'"></i>
        <span>{{ toast.message }}</span>
      </div>
    </transition>
  </main>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'
import RoleModal from './RoleModal.vue'
import api from '../../../bootstrap'

const roles = ref([])
const permissions = ref([])
const loading = ref(false)
const showModal = ref(false)
const selectedRole = ref(null)
const toast = ref({ visible: false, message: '', type: '', icon: '' })

const loadRoles = async () => {
  loading.value = true
  try {
    const response = await api.get('/roles')
    roles.value = Array.isArray(response.data?.data?.roles) ? response.data.data.roles : []
  } catch (error) {
    showError('Failed to load roles')
  } finally {
    loading.value = false
  }
}
const loadPermissions = async () => {
  try {
    const response = await api.get('/permissions')
    permissions.value = Array.isArray(response.data?.data?.permissions) ? response.data.data.permissions : []
  } catch {}
}
const openCreateModal = () => {
  selectedRole.value = null
  showModal.value = true
}
const openEditModal = (role) => {
  selectedRole.value = role
  showModal.value = true
}
const closeModal = () => {
  showModal.value = false
  selectedRole.value = null
}
const onRoleSaved = () => {
  closeModal()
  loadRoles()
  showSuccess('Role saved successfully')
}
const showSuccess = (message) => {
  toast.value = { message, type: 'success', icon: 'bi bi-check-circle', visible: true }
  setTimeout(() => (toast.value.visible = false), 2200)
}
const showError = (message) => {
  toast.value = { message, type: 'danger', icon: 'bi bi-x-circle', visible: true }
  setTimeout(() => (toast.value.visible = false), 2200)
}

loadRoles()
loadPermissions()
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0;}
  to { opacity: 1;}
}
.animate-fade-in {
  animation: fade-in 0.32s cubic-bezier(.4,0,.2,1);
}
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(18px);}
  to { opacity: 1; transform: translateY(0);}
}
.animate-fade-in-up {
  animation: fade-in-up 0.44s cubic-bezier(0.4,0,0.2,1);
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.25s cubic-bezier(.4,0,.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
