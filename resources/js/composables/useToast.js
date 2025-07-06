import { ref } from 'vue'

export function useToast() {
  const toasts = ref([])
  const defaultDuration = 5000

  const show = (message, type = 'info', duration = defaultDuration, options = {}) => {
    const id = Date.now()
    const toast = {
      id,
      message,
      type,
      duration,
      ...options
    }

    toasts.value.push(toast)

    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }

    return id
  }

  const remove = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index !== -1) {
      toasts.value.splice(index, 1)
    }
  }

  const success = (message, options = {}) => {
    const duration = options.duration ?? defaultDuration
    return show(message, 'success', duration, options)
  }
  
  const error = (message, options = {}) => {
    const duration = options.duration ?? (defaultDuration * 2) // Error messages stay longer
    return show(message, 'error', duration, options)
  }
  
  const warning = (message, options = {}) => {
    const duration = options.duration ?? defaultDuration
    return show(message, 'warning', duration, options)
  }
  
  const info = (message, options = {}) => {
    const duration = options.duration ?? defaultDuration
    return show(message, 'info', duration, options)
  }

  const loading = (message, options = {}) => {
    // Loading toasts don't auto-dismiss (duration = 0)
    return show(message, 'loading', 0, options)
  }

  const dismiss = (id) => {
    remove(id)
  }

  return {
    toasts,
    show,
    remove,
    dismiss,
    success,
    error,
    warning,
    info,
    loading
  }
} 