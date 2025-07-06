<!--
  Developer: DocraTech Team | Fatih Berkay Bahceci
  Language: American English (US)
  License: MIT
  Project: DocraTech Medical Website Platform
-->

<template>
  <div
    class="flex min-h-screen bg-docratech-background overflow-hidden"
  >
    <!-- Sidebar -->
    <Sidebar
      v-bind="sidebarProps"
      v-model:collapsed="sidebarCollapsed"
      class="z-40"
      title="DocraTech"
      logo="/favicon.ico"
    >
      <template #header>
        <slot name="sidebar-header" />
      </template>
      <slot name="sidebar" :isCollapsed="sidebarCollapsed" />
      <template #footer>
        <slot name="sidebar-footer" />
      </template>
    </Sidebar>

    <!-- Main Area -->
    <div class="flex flex-1 flex-col min-w-0 min-h-screen">
      <!-- Navbar -->
      <Navbar
        v-bind="navbarProps"
        class="z-30"
        @mobile-menu-toggle="sidebarCollapsed = !sidebarCollapsed"
      >
        <template #breadcrumb>
          <slot name="breadcrumb" />
        </template>
        <template #center>
          <slot name="navbar-center" />
        </template>
        <template #actions>
          <slot name="navbar-actions" />
        </template>
        <template #user-menu>
          <slot name="user-menu" />
        </template>
      </Navbar>

      <!-- Content Area -->
      <main
        class="flex-1 bg-docratech-surface transition-colors duration-300 animate-fadeInUp"
        tabindex="-1"
      >
        <!-- Global Page Container - no padding, only spacing -->
        <div class="space-y-6">
          <slot />
        </div>
      </main>

      <!-- Footer -->
      <Footer v-bind="footerProps" class="z-20" />
    </div>
  </div>
</template>

<script setup>
// Developer: DocraTech Team | Fatih Berkay Bahceci

import { ref } from 'vue'
import Sidebar from '../visual/Sidebar.vue'
import Navbar from '../visual/Navbar.vue'
import Footer from '../visual/Footer.vue'

const props = defineProps({
  sidebarProps: { type: Object, default: () => ({}) },
  navbarProps: { type: Object, default: () => ({}) },
  footerProps: { type: Object, default: () => ({}) },
  initialSidebarCollapsed: { type: Boolean, default: false }
})

const sidebarCollapsed = ref(props.initialSidebarCollapsed)
</script>

<style scoped>
@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(24px);}
  100% { opacity: 1; transform: translateY(0);}
}
.animate-fadeInUp {
  animation: fadeInUp 0.7s cubic-bezier(.4,0,.2,1) both;
}
</style>
