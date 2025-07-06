<!--
  DocraTech Medical Platform - Data Table Component
  Features: Sorting, selection, pagination, responsive design
-->

<template>
  <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <!-- Table Header -->
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              :class="[
                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                column.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                column.width ? `w-${column.width}` : ''
              ]"
              @click="column.sortable ? handleSort(column.key) : null"
            >
              <div class="flex items-center space-x-1">
                <!-- Checkbox for select all -->
                <input
                  v-if="column.key === 'select'"
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="$emit('select-all')"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                
                <!-- Column Label -->
                <span v-else>{{ column.label }}</span>
                
                <!-- Sort Icons -->
                <div v-if="column.sortable && column.key !== 'select'" class="flex flex-col">
                  <svg
                    :class="[
                      'w-3 h-3',
                      sortBy === column.key && sortOrder === 'asc' ? 'text-blue-600' : 'text-gray-400'
                    ]"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                  </svg>
                  <svg
                    :class="[
                      'w-3 h-3 -mt-1',
                      sortBy === column.key && sortOrder === 'desc' ? 'text-blue-600' : 'text-gray-400'
                    ]"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="bg-white divide-y divide-gray-200">
          <!-- Loading State -->
          <tr v-if="loading">
            <td :colspan="columns.length" class="px-6 py-12 text-center">
              <div class="flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>
              <p class="text-gray-500 mt-2">Loading...</p>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="!data || data.length === 0">
            <td :colspan="columns.length" class="px-6 py-12 text-center">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-gray-500 text-lg">No data available</p>
              <p class="text-gray-400 text-sm mt-1">There are no items to display at this time.</p>
            </td>
          </tr>

          <!-- Data Rows -->
          <tr 
            v-else
            v-for="(item, index) in data" 
            :key="item.id || index"
            class="hover:bg-gray-50 transition-colors duration-200"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap"
            >
              <!-- Checkbox Column -->
              <input
                v-if="column.key === 'select'"
                type="checkbox"
                :checked="selectedRows.includes(item.id)"
                @change="$emit('select-row', item.id)"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              
              <!-- Custom Slot Content -->
              <slot
                v-else-if="$slots[`cell-${column.key}`]"
                :name="`cell-${column.key}`"
                :item="item"
                :value="getNestedValue(item, column.key)"
              />
              
              <!-- Default Content -->
              <span v-else class="text-sm text-gray-900">
                {{ getNestedValue(item, column.key) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && data && data.length > 0" class="bg-white px-6 py-3 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <!-- Items per page -->
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-700">Show</span>
          <select
            :value="itemsPerPage"
            @change="$emit('page-size-change', parseInt($event.target.value))"
            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span class="text-sm text-gray-700">entries</span>
        </div>

        <!-- Pagination Info and Controls -->
        <div class="flex items-center space-x-4">
          <!-- Info -->
          <span class="text-sm text-gray-700">
            Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} entries
          </span>

          <!-- Pagination Controls -->
          <div class="flex items-center space-x-1">
            <!-- Previous Button -->
            <button
              :disabled="currentPage <= 1"
              @click="$emit('page-change', currentPage - 1)"
              class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>

            <!-- Page Numbers -->
            <button
              v-for="page in visiblePages"
              :key="page"
              :class="[
                'px-3 py-1 text-sm border rounded-md',
                page === currentPage
                  ? 'bg-blue-600 text-white border-blue-600'
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
              @click="$emit('page-change', page)"
            >
              {{ page }}
            </button>

            <!-- Next Button -->
            <button
              :disabled="currentPage >= totalPages"
              @click="$emit('page-change', currentPage + 1)"
              class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  selectedRows: {
    type: Array,
    default: () => []
  },
  totalItems: {
    type: Number,
    default: 0
  },
  itemsPerPage: {
    type: Number,
    default: 10
  },
  currentPage: {
    type: Number,
    default: 1
  },
  totalPages: {
    type: Number,
    default: 1
  },
  sortBy: {
    type: String,
    default: ''
  },
  sortOrder: {
    type: String,
    default: 'asc'
  }
})

const emit = defineEmits([
  'sort',
  'page-change',
  'page-size-change',
  'select-all',
  'select-row'
])

// Computed properties
const isAllSelected = computed(() => {
  return props.data.length > 0 && props.selectedRows.length === props.data.length
})

const startItem = computed(() => {
  return ((props.currentPage - 1) * props.itemsPerPage) + 1
})

const endItem = computed(() => {
  return Math.min(props.currentPage * props.itemsPerPage, props.totalItems)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, props.currentPage - Math.floor(maxVisible / 2))
  let end = Math.min(props.totalPages, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Methods
const handleSort = (key) => {
  let order = 'asc'
  if (props.sortBy === key && props.sortOrder === 'asc') {
    order = 'desc'
  }
  
  emit('sort', { key, order })
}

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((current, key) => current?.[key], obj)
}
</script>

<style scoped>
/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Row hover effect */
tbody tr:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
* {
  transition: all 0.2s ease-in-out;
}
</style>
