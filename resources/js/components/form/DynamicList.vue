<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: DynamicList (Enterprise Form Component)
Project: DocraTech Medical Website Platform
Description: Reusable component for managing dynamic lists like education, awards, publications
-->

<template>
  <div class="dynamic-list-container">
    <!-- Section Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-[#5A1188] to-[#9D38CF] flex items-center justify-center">
          <i class="bi text-white text-sm" :class="icon"></i>
        </div>
        <div>
          <h4 class="font-semibold text-gray-900">{{ title }}</h4>
          <p class="text-sm text-gray-500">{{ description }}</p>
        </div>
      </div>
      <button
        type="button"
        @click="addItem"
        class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] hover:from-[#6D28D9] hover:to-[#A855F7] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
      >
        <PlusIcon class="w-5 h-5" />
        Add {{ itemName }}
      </button>
    </div>

    <!-- Items List -->
    <div class="space-y-4" v-if="items.length > 0">
      <TransitionGroup name="list-item" tag="div">
        <div
          v-for="(item, index) in items"
          :key="`${type}-${index}`"
          class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300"
          :class="{ 
            'border-[#5A1188]/40 shadow-xl ring-2 ring-[#5A1188]/10 bg-gradient-to-r from-[#5A1188]/5 to-[#9D38CF]/5': expandedItems[index],
            'hover:border-[#5A1188]/20': !expandedItems[index]
          }"
        >
          <!-- Enhanced Collapsible Header -->
          <div 
            class="flex items-center justify-between p-4 cursor-pointer hover:bg-gradient-to-r hover:from-[#5A1188]/8 hover:to-[#9D38CF]/8 transition-all duration-300 rounded-t-xl"
            :class="{ 'bg-gradient-to-r from-[#5A1188]/10 to-[#9D38CF]/10': expandedItems[index] }"
            @click="toggleItem(index)"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white shadow-sm">
                {{ itemName }} #{{ index + 1 }}
              </span>
              <div class="text-sm text-gray-600 truncate">
                <span v-if="getItemSummary(item)" class="font-medium text-gray-900">{{ getItemSummary(item) }}</span>
                <span v-else class="text-gray-500 italic">Click to expand and add details</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <!-- Remove Button with updated colors and icon -->
              <button
                  type="button"
                  @click.stop="removeItem(index)"
                  class="flex items-center justify-center w-9 h-9 bg-white text-[#5A1188] hover:text-white hover:bg-gradient-to-r hover:from-[#5A1188] hover:to-[#9D38CF] border-2 border-[#5A1188] hover:border-transparent rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl group transform hover:scale-110"
                  :aria-label="`Remove ${itemName.toLowerCase()}`"
                  title="Delete this item"
              >
                <XMarkIcon class="w-4 h-4 font-bold group-hover:scale-110 transition-all duration-300 group-hover:drop-shadow-sm" />
              </button>

              <!-- Collapse/Expand Button (unchanged colors, kept consistent) -->
              <button
                type="button"
                class="flex items-center justify-center w-9 h-9 bg-white text-gray-400 hover:text-white hover:bg-gradient-to-r hover:from-[#5A1188] hover:to-[#9D38CF] border-2 border-gray-200 hover:border-transparent rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110"
                :aria-label="expandedItems[index] ? 'Collapse section' : 'Expand section'"
                :title="expandedItems[index] ? 'Collapse section' : 'Expand section'"
              >
                <ChevronUpIcon v-if="expandedItems[index]" class="w-4 h-4 font-bold transition-all duration-300" />
                <ChevronDownIcon v-else class="w-4 h-4 font-bold transition-all duration-300" />
              </button>
            </div>
          </div>

          <!-- Collapsible Content -->
          <Transition
            name="slide-down"
            @enter="onEnter"
            @after-enter="onAfterEnter"
            @before-leave="onBeforeLeave"
            @leave="onLeave"
          >
            <div v-show="expandedItems[index]" class="overflow-hidden">
              <div class="px-4 pb-4 border-t border-gray-100">

        <!-- Dynamic Fields based on type -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Education Fields -->
          <template v-if="type === 'education'">
            <div>
              <label class="form-label">Institution</label>
              <InputText
                v-model="item.institution"
                placeholder="University or school name"
              />
            </div>
            <div>
              <label class="form-label">Degree</label>
              <InputText
                v-model="item.degree"
                placeholder="Degree or qualification"
              />
            </div>
            <div>
              <label class="form-label">Field of Study</label>
              <InputText
                v-model="item.field"
                placeholder="Major or specialization"
              />
            </div>
            <div>
              <label class="form-label">Year</label>
              <InputText
                v-model="item.year"
                placeholder="Graduation year"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Description</label>
              <Textarea
                v-model="item.description"
                placeholder="Additional details about your education"
                rows="2"
              />
            </div>
          </template>

          <!-- Awards Fields -->
          <template v-else-if="type === 'awards'">
            <div class="sm:col-span-2">
              <label class="form-label">Award Name</label>
              <InputText
                v-model="item.name"
                placeholder="Name of the award"
              />
            </div>
            <div>
              <label class="form-label">Issuing Organization</label>
              <InputText
                v-model="item.organization"
                placeholder="Who awarded this"
              />
            </div>
            <div>
              <label class="form-label">Year</label>
              <InputText
                v-model="item.year"
                placeholder="Year received"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Description</label>
              <Textarea
                v-model="item.description"
                placeholder="Details about the award"
                rows="2"
              />
            </div>
          </template>

          <!-- Publications Fields -->
          <template v-else-if="type === 'publications'">
            <div class="sm:col-span-2">
              <label class="form-label">Title</label>
              <InputText
                v-model="item.title"
                placeholder="Publication title"
              />
            </div>
            <div>
              <label class="form-label">Journal/Conference</label>
              <InputText
                v-model="item.journal"
                placeholder="Published in"
              />
            </div>
            <div>
              <label class="form-label">Year</label>
              <InputText
                v-model="item.year"
                placeholder="Publication year"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Authors</label>
              <InputText
                v-model="item.authors"
                placeholder="List all authors"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">URL/DOI</label>
              <InputText
                v-model="item.url"
                placeholder="Link to publication"
              />
            </div>
          </template>

          <!-- Certificates Fields -->
          <template v-else-if="type === 'certificates'">
            <div class="sm:col-span-2">
              <label class="form-label">Certificate Name</label>
              <InputText
                v-model="item.name"
                placeholder="Certificate title"
              />
            </div>
            <div>
              <label class="form-label">Issuing Organization</label>
              <InputText
                v-model="item.organization"
                placeholder="Certifying body"
              />
            </div>
            <div>
              <label class="form-label">Issue Date</label>
              <InputText
                v-model="item.issue_date"
                type="date"
              />
            </div>
            <div>
              <label class="form-label">Expiry Date</label>
              <InputText
                v-model="item.expiry_date"
                type="date"
                placeholder="Leave empty if no expiry"
              />
            </div>
            <div>
              <label class="form-label">Credential ID</label>
              <InputText
                v-model="item.credential_id"
                placeholder="Certificate number"
              />
            </div>
          </template>

          <!-- Languages Fields -->
          <template v-else-if="type === 'languages'">
            <div>
              <label class="form-label">Language</label>
              <InputText
                v-model="item.language"
                placeholder="Language name"
              />
            </div>
            <div>
              <label class="form-label">Proficiency Level</label>
              <SearchableSelect
                v-model="item.level"
                :options="proficiencyLevels"
                placeholder="Select proficiency level"
              />
            </div>
          </template>

          <!-- Specialties Fields -->
          <template v-else-if="type === 'specialties'">
            <div class="sm:col-span-2">
              <label class="form-label">Specialty Name</label>
              <InputText
                v-model="item.name"
                placeholder="Area of specialization"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Description</label>
              <Textarea
                v-model="item.description"
                placeholder="Details about this specialty"
                rows="2"
              />
            </div>
          </template>

          <!-- Work Experience Fields -->
          <template v-else-if="type === 'work_experience'">
            <div>
              <label class="form-label">Company</label>
              <InputText
                v-model="item.company"
                placeholder="Company name"
              />
            </div>
            <div>
              <label class="form-label">Position</label>
              <InputText
                v-model="item.position"
                placeholder="Job title"
              />
            </div>
            <div>
              <label class="form-label">Start Date</label>
              <InputText
                v-model="item.start_date"
                type="date"
              />
            </div>
            <div>
              <label class="form-label">End Date</label>
              <InputText
                v-model="item.end_date"
                type="date"
                placeholder="Leave empty if current"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="form-label">Description</label>
              <Textarea
                v-model="item.description"
                placeholder="Key responsibilities and achievements"
                :rows="3"
              />
            </div>
          </template>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </TransitionGroup>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
      <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-gradient-to-r from-[#5A1188]/10 to-[#9D38CF]/10 flex items-center justify-center">
        <component 
          :is="getIconComponent" 
          class="w-6 h-6 text-[#5A1188]" 
        />
      </div>
      <h3 class="text-sm font-medium text-gray-900 mb-1">No {{ title.toLowerCase() }} added yet</h3>
      <p class="text-sm text-gray-500 mb-4">Start by adding your first {{ itemName.toLowerCase() }}</p>
      <button
        type="button"
        @click="addItem"
        class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] hover:from-[#6D28D9] hover:to-[#A855F7] text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
      >
        <PlusIcon class="w-5 h-5" />
        Add {{ itemName }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import InputText from '@/components/form/InputText.vue'
import Textarea from '@/components/form/Textarea.vue'
import SearchableSelect from '@/components/form/SearchableSelect.vue'

// Heroicons imports
import { 
  PlusIcon, 
  XMarkIcon, 
  ChevronUpIcon, 
  ChevronDownIcon,
  AcademicCapIcon,
  BriefcaseIcon,
  TrophyIcon,
  DocumentTextIcon,
  LanguageIcon,
  StarIcon,
  SparklesIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  type: { type: String, required: true },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  itemName: { type: String, required: true },
  icon: { type: String, default: 'bi-plus-circle' },
  maxItems: { type: Number, default: 10 }
})

const emit = defineEmits(['update:modelValue', 'change'])

// Local state
const items = ref([...props.modelValue])
const expandedItems = ref({}) // All sections are collapsed by default

// Computed properties
const proficiencyLevels = computed(() => [
  { value: 'native', label: 'Native', description: 'Mother tongue' },
  { value: 'fluent', label: 'Fluent', description: 'Near-native proficiency' },
  { value: 'advanced', label: 'Advanced', description: 'Highly proficient' },
  { value: 'intermediate', label: 'Intermediate', description: 'Conversational' },
  { value: 'beginner', label: 'Beginner', description: 'Basic knowledge' }
])

const getIconComponent = computed(() => {
  const iconMap = {
    'education': AcademicCapIcon,
    'work_experience': BriefcaseIcon,
    'awards': TrophyIcon,
    'publications': DocumentTextIcon,
    'languages': LanguageIcon,
    'specialties': StarIcon,
    'certificates': SparklesIcon
  }
  return iconMap[props.type] || PlusIcon
})

// Methods
function getDefaultItem() {
  const base = { id: Date.now() + Math.random() }
  
  switch (props.type) {
    case 'education':
      return { ...base, institution: '', degree: '', field: '', year: '', description: '' }
    case 'work_experience':
      return { ...base, company: '', position: '', start_date: '', end_date: '', description: '' }
    case 'publications':
      return { ...base, title: '', journal: '', year: '', authors: '', url: '' }
    case 'awards':
      return { ...base, name: '', organization: '', year: '', description: '' }
    case 'certificates':
      return { ...base, name: '', organization: '', issue_date: '', expiry_date: '', credential_id: '' }
    case 'languages':
      return { ...base, language: '', level: '' }
    case 'specialties':
      return { ...base, name: '', description: '' }
    default:
      return { ...base, name: '', description: '' }
  }
}

function addItem() {
  if (items.value.length >= props.maxItems) {
    return
  }
  
  const newIndex = items.value.length
  items.value.push(getDefaultItem())
  
  // Automatically expand the newly added item for immediate editing
  expandedItems.value[newIndex] = true
  
  emitChange()
}

function removeItem(index) {
  items.value.splice(index, 1)
  // Remove from expanded items
  delete expandedItems.value[index]
  // Reindex expanded items
  const newExpanded = {}
  Object.keys(expandedItems.value).forEach(key => {
    const keyIndex = parseInt(key)
    if (keyIndex > index) {
      newExpanded[keyIndex - 1] = expandedItems.value[key]
    } else if (keyIndex < index) {
      newExpanded[keyIndex] = expandedItems.value[key]
    }
  })
  expandedItems.value = newExpanded
  emitChange()
}

function toggleItem(index) {
  expandedItems.value[index] = !expandedItems.value[index]
}

function getItemSummary(item) {
  switch (props.type) {
    case 'education':
      return [item.degree, item.institution].filter(Boolean).join(' at ')
    case 'work_experience':
      return [item.position, item.company].filter(Boolean).join(' at ')
    case 'publications':
      return item.title || ''
    case 'awards':
    case 'certificates':
    case 'specialties':
      return item.name || ''
    case 'languages':
      return [item.language, item.level].filter(Boolean).join(' - ')
    default:
      return item.name || item.title || ''
  }
}

// Animation methods for smooth transitions
function onEnter(el) {
  el.style.height = '0'
}

function onAfterEnter(el) {
  el.style.height = 'auto'
}

function onBeforeLeave(el) {
  el.style.height = el.scrollHeight + 'px'
}

function onLeave(el) {
  el.offsetHeight // force reflow
  el.style.height = '0'
}

// Watchers with improved stability
watch(() => props.modelValue, (newValue) => {
  // Only update if the data is actually different
  if (JSON.stringify(newValue) !== JSON.stringify(items.value)) {
    items.value = [...newValue]
  }
}, { deep: true })

// Debounced emit to prevent data loss
let emitTimeout = null
function emitChange() {
  // Clear previous timeout
  if (emitTimeout) {
    clearTimeout(emitTimeout)
  }
  
  // Emit with slight delay to ensure stability
  emitTimeout = setTimeout(() => {
    emit('update:modelValue', [...items.value])
    emit('change', [...items.value])
  }, 100)
}
</script>

<style scoped>
.form-label {
  @apply block text-sm font-semibold text-gray-700 mb-2;
}

.form-label.required::after {
  content: ' *';
  @apply text-red-500;
}

/* Collapsible animations */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.slide-down-enter-from,
.slide-down-leave-to {
  height: 0;
}

/* List animations */
.list-item-enter-active,
.list-item-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.list-item-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

.list-item-leave-to {
  opacity: 0;
  transform: translateX(20px) scale(0.95);
}

.list-item-move {
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
