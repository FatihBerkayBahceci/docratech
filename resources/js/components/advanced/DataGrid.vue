<!--
  DocraTech Enterprise Platform - DataGrid Component
  Developer: DocraTech Team - Fatih Berkay Bahceci

  Marka kitine %100 uyumlu, mikro animasyonlu, erişilebilir, mobil-first ve performanslı DataGrid bileşeni.
-->

<template>
  <div class="dtr-data-grid" role="table" aria-label="Veri Tablosu">
    <!-- Header -->
    <div class="dtr-data-grid-header">
      <div>
        <h3 v-if="title" class="dtr-data-grid-title">{{ title }}</h3>
        <p v-if="description" class="dtr-data-grid-description">{{ description }}</p>
      </div>
      <div class="dtr-data-grid-actions">
        <div v-if="searchable" class="dtr-data-grid-search">
          <SearchInput
            v-model="searchQuery"
            placeholder="Ara..."
            size="sm"
            @search="handleSearch"
          />
        </div>
        <div class="dtr-data-grid-buttons">
          <slot name="actions" />
          <AppButton
            v-if="exportable"
            variant="outline"
            size="sm"
            @click="exportData"
          >
            <Icon name="download" size="sm" />
            Dışa Aktar
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div v-if="showFilters" class="dtr-data-grid-filters">
      <slot name="filters" />
    </div>

    <!-- Table -->
    <div class="dtr-data-grid-table-container">
      <table class="dtr-data-grid-table">
        <thead>
          <tr>
            <th v-if="selectable" class="dtr-data-grid-th dtr-data-grid-th-checkbox" scope="col">
              <Checkbox
                :model-value="isAllSelected"
                :indeterminate="isIndeterminate"
                @update:model-value="toggleSelectAll"
                aria-label="Tümünü Seç"
              />
            </th>
            <th
              v-for="column in visibleColumns"
              :key="column.key"
              class="dtr-data-grid-th"
              :class="{ 'dtr-data-grid-th-sortable': column.sortable }"
              @click="handleSort(column)"
              :aria-sort="sortBy === column.key ? (sortOrder === 'asc' ? 'ascending' : 'descending') : 'none'"
              tabindex="0"
              scope="col"
            >
              <div class="dtr-data-grid-th-content">
                <span class="dtr-data-grid-th-text">{{ column.label }}</span>
                <Icon
                  v-if="column.sortable"
                  :name="getSortIcon(column.key)"
                  size="sm"
                  class="dtr-data-grid-th-icon"
                />
              </div>
            </th>
            <th v-if="showActions" class="dtr-data-grid-th dtr-data-grid-th-actions" scope="col">
              İşlemler
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, index) in paginatedData"
            :key="row.id || index"
            class="dtr-data-grid-tr"
            :class="{ 'dtr-data-grid-tr-selected': isRowSelected(row) }"
            tabindex="0"
            @keydown.enter="$emit('edit', row)"
          >
            <td v-if="selectable" class="dtr-data-grid-td dtr-data-grid-td-checkbox">
              <Checkbox
                :model-value="isRowSelected(row)"
                @update:model-value="toggleRowSelection(row)"
                aria-label="Satır Seç"
              />
            </td>
            <td
              v-for="column in visibleColumns"
              :key="column.key"
              class="dtr-data-grid-td"
            >
              <slot :name="`cell-${column.key}`" :row="row" :column="column">
                {{ getCellValue(row, column) }}
              </slot>
            </td>
            <td v-if="showActions" class="dtr-data-grid-td dtr-data-grid-td-actions">
              <slot name="actions" :row="row" :index="index">
                <div class="dtr-data-grid-actions-cell">
                  <AppButton
                    variant="ghost"
                    size="sm"
                    @click="$emit('edit', row)"
                    aria-label="Düzenle"
                  >
                    <Icon name="edit" size="sm" />
                  </AppButton>
                  <AppButton
                    variant="ghost"
                    size="sm"
                    @click="$emit('delete', row)"
                    aria-label="Sil"
                  >
                    <Icon name="trash" size="sm" />
                  </AppButton>
                </div>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-if="filteredData.length === 0" class="dtr-data-grid-empty" aria-live="polite">
      <EmptyState
        title="Veri bulunamadı"
        description="Arama kriterlerinize uygun veri bulunamadı."
        icon="search"
      />
    </div>

    <!-- Footer -->
    <div class="dtr-data-grid-footer">
      <div>
        <span class="dtr-data-grid-info-text">
          {{ paginationInfo }}
        </span>
      </div>
      <div class="dtr-data-grid-pagination">
        <Pagination
          v-model:current-page="currentPage"
          :total-pages="totalPages"
          :total-items="filteredData.length"
          :items-per-page="itemsPerPage"
          @update:items-per-page="updateItemsPerPage"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, watch } from 'vue'
import Icon from '../visual/Icon.vue'
import SearchInput from '../form/SearchInput.vue'
import Checkbox from '../form/Checkbox.vue'
import AppButton from '../button/AppButton.vue'
import Pagination from '../navigation/Pagination.vue'
import EmptyState from '../empty/EmptyState.vue'

const props = defineProps({
  data: { type: Array, default: () => [] },
  columns: { type: Array, default: () => [] },
  title: { type: String, default: null },
  description: { type: String, default: null },
  searchable: { type: Boolean, default: true },
  selectable: { type: Boolean, default: false },
  showActions: { type: Boolean, default: true },
  showFilters: { type: Boolean, default: false },
  exportable: { type: Boolean, default: false },
  itemsPerPage: { type: Number, default: 10 },
  sortBy: { type: String, default: null },
  sortOrder: {
    type: String,
    default: 'asc',
    validator: (value) => ['asc', 'desc'].includes(value)
  }
})

const emit = defineEmits([
  'edit', 'delete', 'selection-change', 'sort-change', 'search'
])

const searchQuery = ref('')
const currentPage = ref(1)
const selectedRows = ref([])
const sortBy = ref(props.sortBy)
const sortOrder = ref(props.sortOrder)

const visibleColumns = computed(() => props.columns.filter(column => !column.hidden))

const getCellValue = (row, column) => row[column.key] ?? ''

const filteredData = computed(() => {
  let data = [...props.data]
  if (searchQuery.value) {
    data = data.filter(row => visibleColumns.value.some(column => {
      const value = getCellValue(row, column)
      return String(value).toLowerCase().includes(searchQuery.value.toLowerCase())
    }))
  }
  if (sortBy.value) {
    data.sort((a, b) => {
      const aValue = getCellValue(a, { key: sortBy.value })
      const bValue = getCellValue(b, { key: sortBy.value })
      if (aValue < bValue) return sortOrder.value === 'asc' ? -1 : 1
      if (aValue > bValue) return sortOrder.value === 'asc' ? 1 : -1
      return 0
    })
  }
  return data
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / props.itemsPerPage))

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * props.itemsPerPage
  const end = start + props.itemsPerPage
  return filteredData.value.slice(start, end)
})

const isRowSelected = (row) => selectedRows.value.some(selected => selected.id === row.id)

const isAllSelected = computed(() => paginatedData.value.length > 0 &&
  paginatedData.value.every(row => isRowSelected(row)))

const isIndeterminate = computed(() => {
  const selectedCount = paginatedData.value.filter(row => isRowSelected(row)).length
  return selectedCount > 0 && selectedCount < paginatedData.value.length
})

const paginationInfo = computed(() => {
  const start = (currentPage.value - 1) * props.itemsPerPage + 1
  const end = Math.min(currentPage.value * props.itemsPerPage, filteredData.value.length)
  return `${filteredData.value.length ? start : 0}-${filteredData.value.length ? end : 0} / ${filteredData.value.length} kayıt`
})

function toggleRowSelection(row) {
  const index = selectedRows.value.findIndex(selected => selected.id === row.id)
  if (index > -1) {
    selectedRows.value.splice(index, 1)
  } else {
    selectedRows.value.push(row)
  }
  emit('selection-change', selectedRows.value)
}

function toggleSelectAll(value) {
  if (value) {
    selectedRows.value = [...paginatedData.value]
  } else {
    selectedRows.value = []
  }
  emit('selection-change', selectedRows.value)
}

function handleSort(column) {
  if (!column.sortable) return
  if (sortBy.value === column.key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column.key
    sortOrder.value = 'asc'
  }
  emit('sort-change', { sortBy: sortBy.value, sortOrder: sortOrder.value })
}

function getSortIcon(key) {
  if (sortBy.value !== key) return 'chevron-up-down'
  return sortOrder.value === 'asc' ? 'chevron-up' : 'chevron-down'
}

function handleSearch(query) {
  searchQuery.value = query
  currentPage.value = 1
  emit('search', query)
}

function updateItemsPerPage() {
  currentPage.value = 1
}

function exportData() {
  // Export logic (CSV, Excel, vs.) burada geliştirilebilir
  console.log('Exporting data:', filteredData.value)
}

watch(() => props.data, () => {
  currentPage.value = 1
})
</script>

<style scoped>
/* DocraTech Brand Variables */
:root {
  --dtr-color-primary: #5A1188;
  --dtr-color-primary-gradient: linear-gradient(90deg,#9D38CF 0%,#5A1188 100%);
  --dtr-color-accent: #9D38CF;
  --dtr-color-background: #211232;
  --dtr-color-card: #23132b;
  --dtr-color-table-header: #31214a;
  --dtr-color-table-row-hover: #34215a;
  --dtr-color-shadow: 0 6px 30px 0 rgba(90,17,136,0.11);
  --dtr-radius: 1.25rem;
  --dtr-radius-sm: 0.75rem;
  --dtr-radius-lg: 2rem;
  --dtr-font: 'Inter', Arial, sans-serif;
}

.dtr-data-grid {
  background: var(--dtr-color-card);
  border-radius: var(--dtr-radius);
  box-shadow: var(--dtr-color-shadow);
  overflow: hidden;
  font-family: var(--dtr-font);
  color: #fff;
  border: 1px solid #2a183d;
  transition: box-shadow 0.24s;
}

.dtr-data-grid-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2rem 2.2rem 1.25rem 2.2rem;
  background: var(--dtr-color-primary-gradient);
  border-bottom: 1px solid #37215a;
  border-radius: var(--dtr-radius) var(--dtr-radius) 0 0;
  box-shadow: 0 4px 24px 0 rgba(90,17,136,0.09);
}
.dtr-data-grid-title {
  font-size: 1.55rem;
  font-weight: 800;
  letter-spacing: -0.01em;
  color: #fff;
  margin: 0;
}
.dtr-data-grid-description {
  font-size: 1rem;
  color: #c0b7da;
  margin-top: 0.2rem;
}

.dtr-data-grid-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.dtr-data-grid-search {
  width: 240px;
  min-width: 140px;
  max-width: 280px;
}
.dtr-data-grid-search input:focus {
  box-shadow: 0 0 0 2px #9D38CF44;
  border-color: #9D38CF;
  transition: box-shadow 0.19s;
}
.dtr-data-grid-buttons {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.dtr-data-grid-filters {
  padding: 1rem 2.2rem;
  background: #27143c;
  border-bottom: 1px solid #37215a;
}

.dtr-data-grid-table-container {
  overflow-x: auto;
  background: var(--dtr-color-card);
}

.dtr-data-grid-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  min-width: 900px;
  background: var(--dtr-color-card);
}
.dtr-data-grid-th,
.dtr-data-grid-td {
  padding: 1.05rem 1.3rem;
  font-size: 1rem;
  border-bottom: 1px solid #31214a;
  text-align: left;
  vertical-align: middle;
  background: transparent;
  transition: background 0.15s;
}
.dtr-data-grid-th {
  background: var(--dtr-color-table-header);
  font-weight: 700;
  color: #ccb6fa;
  border-bottom: 1.5px solid #9D38CF44;
  user-select: none;
  position: relative;
}
.dtr-data-grid-th-sortable {
  cursor: pointer;
  transition: background 0.16s;
}
.dtr-data-grid-th-sortable:hover {
  background: #3a245c;
}
.dtr-data-grid-th-content {
  display: flex;
  align-items: center;
  gap: 0.55rem;
}
.dtr-data-grid-th-icon {
  color: #bb97ea;
  margin-left: 2px;
  transition: transform 0.22s cubic-bezier(.44,1.16,.38,1);
}
.dtr-data-grid-th[aria-sort="ascending"] .dtr-data-grid-th-icon {
  transform: rotate(0deg) scale(1.07);
}
.dtr-data-grid-th[aria-sort="descending"] .dtr-data-grid-th-icon {
  transform: rotate(180deg) scale(1.07);
}

.dtr-data-grid-th-checkbox,
.dtr-data-grid-td-checkbox {
  width: 3.5rem;
  text-align: center;
  padding-left: 0.9rem;
  padding-right: 0.6rem;
}
.dtr-data-grid-th-actions,
.dtr-data-grid-td-actions {
  width: 8rem;
  text-align: right;
}
.dtr-data-grid-tbody {
  background: var(--dtr-color-card);
}
.dtr-data-grid-tr {
  border-bottom: 1px solid #27143c;
  transition: background 0.21s, box-shadow 0.18s, transform 0.15s;
}
.dtr-data-grid-tr:hover {
  background: var(--dtr-color-table-row-hover);
  box-shadow: 0 2px 18px #9D38CF17;
  transform: scale(1.007);
}
.dtr-data-grid-tr-selected {
  background: linear-gradient(90deg, #432e70 92%, #9D38CF26 100%);
  box-shadow: 0 1px 8px 0 #5a11887a;
  border-left: 4px solid #9D38CF;
  transform: scale(1.012);
}
.dtr-data-grid-td {
  color: #e0d4f7;
  font-weight: 400;
}
.dtr-data-grid-td-actions {
  padding-right: 1.2rem;
}
.dtr-data-grid-actions-cell {
  display: flex;
  align-items: center;
  gap: 0.32rem;
}

/* Empty State */
.dtr-data-grid-empty {
  padding: 3.5rem 1.5rem;
  background: var(--dtr-color-card);
  text-align: center;
  color: #bb97ea;
}

/* Footer */
.dtr-data-grid-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.2rem 2.2rem 1.3rem 2.2rem;
  background: #27143c;
  border-top: 1px solid #37215a;
  border-radius: 0 0 var(--dtr-radius) var(--dtr-radius);
}
.dtr-data-grid-info-text {
  font-size: 0.98rem;
  color: #bda5e4;
}

/* Pagination (override) */
.dtr-data-grid-pagination {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Responsive */
@media (max-width: 1100px) {
  .dtr-data-grid-table { min-width: 680px; }
}
@media (max-width: 800px) {
  .dtr-data-grid-header, .dtr-data-grid-footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    padding-left: 1.2rem;
    padding-right: 1.2rem;
  }
  .dtr-data-grid-filters {
    padding-left: 1.2rem;
    padding-right: 1.2rem;
  }
}
@media (max-width: 650px) {
  .dtr-data-grid-header, .dtr-data-grid-footer { padding: 1rem; }
  .dtr-data-grid-table { min-width: 480px; }
}

/* Custom Scrollbar */
.dtr-data-grid-table-container::-webkit-scrollbar {
  height: 6px;
}
.dtr-data-grid-table-container::-webkit-scrollbar-thumb {
  background: #432e70;
  border-radius: 4px;
}
</style>
