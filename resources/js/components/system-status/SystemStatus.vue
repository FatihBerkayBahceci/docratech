<!--
  Project: DocraTech Enterprise Platform
  Component: SystemStatus
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="system-status-card w-full bg-white dark:bg-docratech-background rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-docratech-border relative overflow-hidden"
  >
    <div class="absolute inset-0 pointer-events-none opacity-60 animate-gradient-fade"
         style="background: linear-gradient(90deg,theme('colors.docratech.primaryDark') 0%,theme('colors.docratech.primaryLight') 100%); z-index:0; filter: blur(42px);"></div>
    <h5 class="card-title font-semibold text-xl text-docratech-accent dark:text-white mb-5 relative z-10 tracking-tight flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 20 20"><circle cx="10" cy="10" r="9" stroke="theme('colors.docratech.primaryLight')" stroke-width="2"/><path d="M7 10.5l2.2 2.5L14 7" stroke="theme('colors.docratech.primaryDark')" stroke-width="2" stroke-linecap="round"/></svg>
      System Status
    </h5>
    <div class="status-list flex flex-col gap-4 relative z-10">
      <div
        v-for="item in statuses"
        :key="item.label"
        class="status-item flex items-center gap-4 px-2 py-2 rounded-lg transition-all duration-200 group hover:bg-purple-50 dark:hover:bg-docratech-accent animate-status-item"
        :tabindex="0"
        :aria-label="item.label + ' status: ' + item.status"
      >
        <span
          class="status-indicator flex items-center justify-center mr-1"
          :class="item.status"
        >
          <span
            class="inline-block w-3 h-3 rounded-full shadow-md ring-2"
            :class="{
              'bg-green-500 ring-green-300': item.status === 'online',
              'bg-red-500 ring-red-300': item.status === 'offline',
              'bg-yellow-500 ring-yellow-300': item.status === 'warning'
            }"
          ></span>
        </span>
        <span class="status-label flex-1 font-medium text-base truncate text-docratech-accent dark:text-gray-100">{{ item.label }}</span>
        <i
          v-if="item.icon"
          :class="['bi', item.icon, 'status-icon ml-1 text-lg transition-transform duration-200']"
        ></i>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  statuses: { type: Array, required: true }
})
</script>

<style scoped>
@keyframes statusItemIn {
  0% { opacity: 0; transform: translateY(12px);}
  100% { opacity: 1; transform: translateY(0);}
}
.animate-status-item {
  animation: statusItemIn 0.35s cubic-bezier(.22,1,.36,1);
}

@keyframes gradientFade {
  0% { opacity: 0.3; }
  50% { opacity: 0.6; }
  100% { opacity: 0.3; }
}
.animate-gradient-fade {
  animation: gradientFade 2.8s ease-in-out infinite;
}

.status-item:focus {
  outline: 2px solid theme('colors.docratech.primaryLight');
  outline-offset: 2px;
}

.status-icon {
  color: theme('colors.docratech.primaryLight');
  transition: color 0.2s, transform 0.2s;
}
.status-item:hover .status-icon {
  color: theme('colors.docratech.primaryDark');
  transform: scale(1.12) rotate(-8deg);
}
</style>
