<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 2.0 - Complete Frontend Rewrite
Description: Standard page template for consistent layout across all pages
-->

<template>
  <div class="px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">PAGE_TITLE</h1>
          <p class="text-gray-600 mt-2">PAGE_DESCRIPTION</p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Action buttons go here -->
          <AppButton variant="primary" size="sm" @click="handleAction">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Action Button
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Statistics Cards (Optional) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Metric Name</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">123</p>
          </div>
          <div class="p-3 rounded-full" style="background: rgba(90, 17, 136, 0.1)">
            <svg class="w-6 h-6" style="color: #5A1188" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
        </div>
        <div class="mt-4 flex items-center">
          <span class="text-sm font-medium text-green-600">+12%</span>
          <span class="text-sm text-gray-500 ml-2">from last month</span>
        </div>
      </div>
    </div>

    <!-- Filters and Search (Optional) -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
          <div class="relative">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200"
              style="--focus-ring-color: rgba(90, 17, 136, 0.2)"
            >
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>

        <!-- Filter Dropdowns -->
        <div>
          <select v-model="filters.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent transition-colors duration-200">
            <option value="">All Categories</option>
            <option value="category1">Category 1</option>
            <option value="category2">Category 2</option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2">
          <button
            @click="resetFilters"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200"
          >
            Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-gray-900">Content Title</h2>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Additional info</span>
          </div>
        </div>
      </div>

      <!-- Content goes here -->
      <div class="p-6">
        <!-- Your main content, tables, lists, forms, etc. -->
        <p class="text-gray-600">Replace this with your actual page content.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import AppButton from '@/components/button/AppButton.vue'

// State management
const filters = reactive({
  search: '',
  category: ''
})

// Methods
const handleAction = () => {
  // Handle action button click
  console.log('Action button clicked')
}

const resetFilters = () => {
  filters.search = ''
  filters.category = ''
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 