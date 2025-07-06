<!--
  RoleCard.vue
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Kurumsal rol kartı, marka kitine ve modern UI/UX standartlarına uygun.
-->

<template>
  <!-- Ana Kart -->
  <div
    class="relative group transition-all duration-300 rounded-2xl bg-gradient-to-br from-[#181124] via-[#2A183D] to-[#181124] shadow-xl hover:shadow-2xl hover:scale-[1.03] cursor-pointer flex flex-col gap-4 p-6 min-h-[170px] outline-none"
    tabindex="0"
    aria-label="Kullanıcı rol kartı"
  >
    <!-- Icon + Başlık -->
    <div class="flex items-center gap-4">
      <!-- Slot/Varsayılan Icon -->
      <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg bg-gradient-to-br from-[#5A1188] to-[#9D38CF] transition-transform duration-300 group-hover:scale-110 animate-fade-in"
        aria-hidden="true"
      >
        <slot name="icon">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3 1.343 3 3 3 3-1.343 3-3zm0 0c0-1.657 1.343-3 3-3s3 1.343 3 3-1.343 3-3 3-3-1.343-3-3zm0 0v2m0 4h.01"/>
          </svg>
        </slot>
      </div>
      <!-- Rol Bilgileri -->
      <div class="flex-1">
        <h3 class="text-xl font-semibold font-inter text-white group-hover:text-[#9D38CF] transition-colors duration-200">
          {{ role.display_name || role.name }}
        </h3>
        <p class="text-sm font-inter text-[#C9BFE6] mt-1" v-if="role.description">
          {{ role.description }}
        </p>
      </div>
    </div>
    <!-- Permissions -->
    <div v-if="role.permissions?.length" class="flex flex-wrap items-center gap-2 mt-1">
      <span
        v-for="permission in role.permissions"
        :key="permission.id"
        class="px-3 py-1 rounded-full bg-[#9D38CF1F] text-[#9D38CF] font-medium text-xs font-inter border border-[#9D38CF44] animate-fade-in-up transition-all duration-200"
        :title="permission.description || permission.display_name || permission.name"
      >
        {{ permission.display_name || permission.name }}
      </span>
    </div>
    <!-- Status & Type -->
    <div class="flex justify-between items-center mt-3">
      <span class="text-xs text-[#A9A0C1] font-inter">
        {{ role.type }}
      </span>
      <span
        :class="[
          'px-3 py-1 rounded-full font-inter text-xs capitalize transition-all duration-300 animate-fade-in',
          role.status === 'active'
            ? 'bg-[#10b98124] text-[#10b981] shadow-[0_2px_6px_0_rgba(16,185,129,0.12)]'
            : 'bg-[#ef44441F] text-[#ef4444] shadow-[0_2px_6px_0_rgba(239,68,68,0.10)]'
        ]"
      >
        {{ role.status }}
      </span>
    </div>

    <!-- Loading State -->
    <transition name="fade">
      <div v-if="loading" class="absolute inset-0 bg-[#181124cc] flex items-center justify-center rounded-2xl z-10">
        <svg class="animate-spin h-7 w-7 text-[#9D38CF]" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
      </div>
    </transition>
    <!-- Error State -->
    <transition name="fade">
      <div v-if="error" class="absolute inset-0 bg-[#2A183Dcc] flex items-center justify-center rounded-2xl z-10">
        <span class="text-[#ef4444] text-sm font-medium">Yüklenemedi</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { defineProps, ref } from 'vue'

const props = defineProps({
  role: {
    type: Object,
    required: true
  }
})

// Mikro animasyonlar & loading/error örneği (senaryoya göre tetikleyebilirsin)
const loading = ref(false)
const error = ref(false)

// Dışarıdan emit ile loading ve error yönetilebilir, ör: defineExpose({ setLoading, setError })
</script>

<style scoped>
/* Animasyonlar */
@keyframes fade-in {
  from { opacity: 0; transform: scale(0.97); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
  animation: fade-in 0.45s cubic-bezier(0.4,0,0.2,1);
}
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(14px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
  animation: fade-in-up 0.5s cubic-bezier(0.4,0,0.2,1);
}
/* Vue transition */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.35s cubic-bezier(0.4,0,0.2,1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
