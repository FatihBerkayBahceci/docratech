import { ref } from 'vue'

export function useModal() {
  const isOpen = ref(false)
  const modalData = ref(null)

  const open = (data = null) => {
    modalData.value = data
    isOpen.value = true
  }

  const close = () => {
    isOpen.value = false
    modalData.value = null
  }

  const toggle = () => {
    isOpen.value = !isOpen.value
  }

  return {
    isOpen,
    modalData,
    open,
    close,
    toggle
  }
} 