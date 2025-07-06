<template>
  <transition name="modal-fade">
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Yarı saydam koyu arka plan, blur yok -->
      <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" @click.self="closeModal" tabindex="-1"></div>
      <!-- Modal kutusu -->
      <div class="relative bg-white rounded-3xl shadow-2xl border border-gray-200 max-w-3xl w-full mx-4 my-8 overflow-y-auto max-h-[90vh] flex flex-col animate-modal-enter">
        <!-- Başlık ve Kapatma -->
        <div class="flex items-center justify-between px-8 pt-7 pb-3 border-b border-gray-100">
          <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Permission Templates</h2>
            <p class="text-gray-500 text-sm mt-1">Kurumsal seviyede, hızlı rol yönetimi için şablonlar</p>
          </div>
          <button @click="closeModal" aria-label="Kapat" class="ml-4 p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <!-- Arama ve Filtre -->
        <div class="flex flex-col md:flex-row md:items-center gap-3 px-8 pt-5 pb-2 bg-white">
          <input v-model="searchQuery" type="text" placeholder="Şablonlarda ara..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full md:w-64" />
          <select v-model="selectedCategory" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full md:w-56">
            <option value="">Tüm Kategoriler</option>
            <option value="system">Sistem</option>
            <option value="medical">Medikal</option>
            <option value="administrative">İdari</option>
            <option value="custom">Özel</option>
          </select>
        </div>
        <!-- İçerik -->
        <div class="py-6 px-8 flex-1 min-h-[300px]">
          <!-- Loading State -->
          <div v-if="loading" class="flex flex-col items-center justify-center py-16">
            <div class="w-16 h-16 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-500">Şablonlar yükleniyor...</p>
          </div>
          <!-- Empty State -->
          <div v-else-if="filteredTemplates.length === 0" class="flex flex-col items-center justify-center py-16">
            <svg class="w-20 h-20 text-gray-300 mb-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Şablon bulunamadı</h3>
            <p class="text-gray-500 mb-4">Arama kriterlerinize uygun şablon yok</p>
            <button @click="showCreateForm = true" class="px-5 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg shadow hover:from-green-600 hover:to-emerald-700 transition">İlk Şablonunu Oluştur</button>
          </div>
          <!-- Templates Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-7">
            <div v-for="template in filteredTemplates" :key="template.id" class="relative group bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 overflow-hidden">
              <!-- Header -->
              <div class="p-5 pb-2 border-b border-gray-100 bg-gradient-to-r from-green-50/80 to-emerald-50/80">
                <div class="flex items-center gap-3 mb-2">
                  <span class="text-lg font-bold text-gray-900 tracking-tight">{{ template.display_name || template.name }}</span>
                  <span v-if="template.category" :class="['px-2 py-1 text-xs font-semibold rounded-full', getCategoryColor(template.category)]">{{ template.category }}</span>
                  <span v-if="template.is_system" class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Sistem</span>
                </div>
                <p class="text-gray-600 text-sm line-clamp-2">{{ template.description || 'Açıklama yok' }}</p>
              </div>
              <!-- Stats -->
              <div class="grid grid-cols-2 gap-2 px-5 pt-4 pb-2">
                <div class="flex flex-col items-center bg-gradient-to-r from-green-100/60 to-emerald-100/60 rounded-lg py-2">
                  <span class="text-2xl font-extrabold text-green-700">{{ template.permissions?.length || 0 }}</span>
                  <span class="text-xs text-gray-500">İzin</span>
                </div>
                <div class="flex flex-col items-center bg-gradient-to-r from-gray-100/60 to-gray-50/60 rounded-lg py-2">
                  <span class="text-2xl font-extrabold text-gray-700">{{ template.usage_count || 0 }}</span>
                  <span class="text-xs text-gray-500">Kullanım</span>
                </div>
              </div>
              <!-- Permission Preview -->
              <div v-if="template.permissions?.length" class="px-5 pb-2 pt-2">
                <h5 class="text-xs font-medium text-gray-700 mb-1">Dahil Olan İzinler:</h5>
                <div class="flex flex-wrap gap-1">
                  <span v-for="permission in template.permissions.slice(0, 4)" :key="permission.id || permission" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-medium shadow-sm">
                    {{ permission.display_name || permission.name || permission }}
                  </span>
                  <span v-if="template.permissions.length > 4" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full font-medium">+{{ template.permissions.length - 4 }} daha</span>
                </div>
              </div>
              <!-- Actions -->
              <div class="flex items-center gap-2 px-5 pb-4 pt-2">
                <button @click="quickApply(template)" class="flex-1 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg font-semibold shadow hover:from-green-600 hover:to-emerald-700 transition-all text-sm">Hızlı Uygula</button>
                <div class="relative">
                  <button @click="toggleDropdown(template.id)" class="p-2 text-gray-400 hover:text-green-600 rounded-full hover:bg-green-50 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                  </button>
                  <div v-if="activeDropdown === template.id" class="absolute right-0 top-8 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-20 min-w-[180px] animate-fade-in">
                    <button @click="applyTemplateToRole(template)" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                      <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                      </svg>
                      Role Uygula
                    </button>
                    <button @click="applyTemplateToUser(template)" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                      <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      Kullanıcıya Uygula
                    </button>
                    <hr class="my-1">
                    <button @click="duplicateTemplate(template)" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                      <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                      </svg>
                      Kopyala
                    </button>
                    <button v-if="!template.is_system" @click="editTemplate(template)" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                      <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Düzenle
                    </button>
                    <button v-if="!template.is_system" @click="deleteTemplate(template)" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Sil
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <div class="flex justify-end px-8 pb-7 pt-3 border-t border-gray-100 bg-white">
          <button @click="closeModal" class="px-5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition">Kapat</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Örnek veri (gerçek API ile değiştirilebilir)
const exampleTemplates = [
  {
    id: 1,
    name: 'doctor_full',
    display_name: 'Doktor - Tam Erişim',
    category: 'medical',
    is_system: true,
    description: 'Tüm hasta verilerine erişim',
    permissions: [
      { id: 1, name: 'view_users', display_name: 'Kullanıcıları Görüntüle' },
      { id: 2, name: 'edit_users', display_name: 'Kullanıcıları Düzenle' },
      { id: 3, name: 'view_dashboard', display_name: 'Paneli Görüntüle' },
      { id: 4, name: 'edit_profile', display_name: 'Profili Düzenle' },
      { id: 5, name: 'change_password', display_name: 'Şifre Değiştir' }
    ],
    usage_count: 0
  },
  {
    id: 2,
    name: 'nurse_support',
    display_name: 'Hemşire - Klinik Destek',
    category: 'medical',
    is_system: false,
    description: 'Klinik destek erişimi',
    permissions: [
      { id: 1, name: 'view_dashboard', display_name: 'Paneli Görüntüle' },
      { id: 2, name: 'view_activity', display_name: 'Aktiviteyi Görüntüle' }
    ],
    usage_count: 0
  }
]

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  templates: {
    type: Array,
    default: undefined
  }
})
const emit = defineEmits(['update:modelValue', 'close'])

const searchQuery = ref('')
const selectedCategory = ref('')
const loading = ref(false)
const showCreateForm = ref(false)
const activeDropdown = ref(null)

const templatesData = computed(() => {
  if (props.templates && props.templates.length > 0) return props.templates
  return exampleTemplates
})

const filteredTemplates = computed(() => {
  let arr = templatesData.value
  if (selectedCategory.value) {
    arr = arr.filter(t => t.category === selectedCategory.value)
  }
  if (searchQuery.value) {
    arr = arr.filter(t =>
      (t.display_name || t.name).toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  return arr
})

function closeModal() {
  emit('update:modelValue', false)
  emit('close')
}
function quickApply(template) {
  alert('Hızlı Uygula: ' + (template.display_name || template.name))
}
function toggleDropdown(id) {
  activeDropdown.value = activeDropdown.value === id ? null : id
}
function applyTemplateToRole(template) {
  alert('Role Uygula: ' + (template.display_name || template.name))
  activeDropdown.value = null
}
function applyTemplateToUser(template) {
  alert('Kullanıcıya Uygula: ' + (template.display_name || template.name))
  activeDropdown.value = null
}
function duplicateTemplate(template) {
  alert('Kopyala: ' + (template.display_name || template.name))
  activeDropdown.value = null
}
function editTemplate(template) {
  alert('Düzenle: ' + (template.display_name || template.name))
  activeDropdown.value = null
}
function deleteTemplate(template) {
  alert('Sil: ' + (template.display_name || template.name))
  activeDropdown.value = null
}
function getCategoryColor(category) {
  switch (category) {
    case 'medical': return 'bg-green-100 text-green-800'
    case 'system': return 'bg-blue-100 text-blue-800'
    case 'administrative': return 'bg-purple-100 text-purple-800'
    case 'custom': return 'bg-gray-100 text-gray-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.25s cubic-bezier(0.4,0,0.2,1), transform 0.25s cubic-bezier(0.4,0,0.2,1);
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
.animate-modal-enter {
  animation: modal-enter 0.25s cubic-bezier(0.4,0,0.2,1);
}
@keyframes modal-enter {
  from { opacity: 0; transform: scale(0.97); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style> 