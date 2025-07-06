import { ref, computed, watch, onUnmounted } from 'vue'
import { useToast } from '@/composables/useToast'

/**
 * Auto-save composable for forms
 * Automatically saves form data after user stops typing
 */
export function useAutoSave(formData, saveFunction, options = {}) {
  const {
    delay = 3000, // 3 seconds delay
    excludeFields = ['password', 'password_confirmation'],
    enableToasts = false,
    storageKey = 'autoSaveData'
  } = options

  const { showToast } = useToast()
  const isAutoSaving = ref(false)
  const lastSaved = ref(null)
  const autoSaveTimer = ref(null)
  const hasUnsavedChanges = ref(false)

  // Save to localStorage for persistence
  function saveToLocalStorage(data) {
    try {
      const filteredData = { ...data }
      excludeFields.forEach(field => delete filteredData[field])
      localStorage.setItem(storageKey, JSON.stringify({
        data: filteredData,
        timestamp: Date.now()
      }))
    } catch (error) {
      console.warn('Auto-save to localStorage failed:', error)
    }
  }

  // Load from localStorage
  function loadFromLocalStorage() {
    try {
      const saved = localStorage.getItem(storageKey)
      if (saved) {
        const { data, timestamp } = JSON.parse(saved)
        // Only restore if less than 24 hours old
        if (Date.now() - timestamp < 24 * 60 * 60 * 1000) {
          return data
        }
        // Clean up old data
        localStorage.removeItem(storageKey)
      }
    } catch (error) {
      console.warn('Auto-save load from localStorage failed:', error)
    }
    return null
  }

  // Clear localStorage
  function clearLocalStorage() {
    try {
      localStorage.removeItem(storageKey)
    } catch (error) {
      console.warn('Auto-save clear localStorage failed:', error)
    }
  }

  // Debounced auto-save function
  function debouncedSave() {
    if (autoSaveTimer.value) {
      clearTimeout(autoSaveTimer.value)
    }

    autoSaveTimer.value = setTimeout(async () => {
      if (!hasUnsavedChanges.value) return

      try {
        isAutoSaving.value = true
        
        // Save to localStorage first
        saveToLocalStorage(formData.value)
        
        // Call the actual save function if provided
        if (saveFunction) {
          await saveFunction(formData.value, { isAutoSave: true })
        }
        
        lastSaved.value = new Date()
        hasUnsavedChanges.value = false
        
        if (enableToasts) {
          showToast('Changes saved automatically', 'success', { duration: 2000 })
        }
      } catch (error) {
        console.error('Auto-save failed:', error)
        if (enableToasts) {
          showToast('Auto-save failed', 'error', { duration: 3000 })
        }
      } finally {
        isAutoSaving.value = false
      }
    }, delay)
  }

  // Watch for form changes
  const stopWatcher = watch(
    () => formData.value,
    (newData, oldData) => {
      // Skip initial load
      if (!oldData) return
      
      // Check if data actually changed
      const newDataString = JSON.stringify(newData)
      const oldDataString = JSON.stringify(oldData)
      
      if (newDataString !== oldDataString) {
        hasUnsavedChanges.value = true
        debouncedSave()
      }
    },
    { deep: true }
  )

  // Manual save function
  async function forceSave() {
    if (autoSaveTimer.value) {
      clearTimeout(autoSaveTimer.value)
    }

    try {
      isAutoSaving.value = true
      
      if (saveFunction) {
        await saveFunction(formData.value, { isManualSave: true })
      }
      
      saveToLocalStorage(formData.value)
      lastSaved.value = new Date()
      hasUnsavedChanges.value = false
      
      if (enableToasts) {
        showToast('Changes saved successfully', 'success')
      }
    } catch (error) {
      console.error('Manual save failed:', error)
      if (enableToasts) {
        showToast('Save failed', 'error')
      }
      throw error
    } finally {
      isAutoSaving.value = false
    }
  }

  // Pause auto-save (useful during form submission)
  function pauseAutoSave() {
    if (autoSaveTimer.value) {
      clearTimeout(autoSaveTimer.value)
    }
  }

  // Resume auto-save
  function resumeAutoSave() {
    if (hasUnsavedChanges.value) {
      debouncedSave()
    }
  }

  // Get auto-save status
  const autoSaveStatus = computed(() => {
    if (isAutoSaving.value) {
      return { status: 'saving', message: 'Saving...' }
    }
    if (hasUnsavedChanges.value) {
      return { status: 'unsaved', message: 'Unsaved changes' }
    }
    if (lastSaved.value) {
      const timeAgo = Math.floor((Date.now() - lastSaved.value.getTime()) / 1000)
      if (timeAgo < 60) {
        return { status: 'saved', message: `Saved ${timeAgo}s ago` }
      } else if (timeAgo < 3600) {
        return { status: 'saved', message: `Saved ${Math.floor(timeAgo / 60)}m ago` }
      } else {
        return { status: 'saved', message: `Saved ${Math.floor(timeAgo / 3600)}h ago` }
      }
    }
    return { status: 'idle', message: 'No changes' }
  })

  // Cleanup
  onUnmounted(() => {
    if (autoSaveTimer.value) {
      clearTimeout(autoSaveTimer.value)
    }
    stopWatcher()
  })

  // Warn before page unload if there are unsaved changes
  function handleBeforeUnload(event) {
    if (hasUnsavedChanges.value) {
      event.preventDefault()
      event.returnValue = 'You have unsaved changes. Are you sure you want to leave?'
      return event.returnValue
    }
  }

  // Setup beforeunload listener
  window.addEventListener('beforeunload', handleBeforeUnload)
  
  onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload)
  })

  return {
    isAutoSaving,
    lastSaved,
    hasUnsavedChanges,
    autoSaveStatus,
    forceSave,
    pauseAutoSave,
    resumeAutoSave,
    loadFromLocalStorage,
    clearLocalStorage,
    saveToLocalStorage
  }
} 