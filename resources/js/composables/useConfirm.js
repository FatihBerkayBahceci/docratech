import { ref, h, render } from 'vue'
import ConfirmDialog from '@/components/modal/ConfirmDialog.vue'

export function useConfirm() {
  const show = (options = {}) => {
    return new Promise((resolve) => {
      // Create a container for the dialog
      const container = document.createElement('div')
      document.body.appendChild(container)
      
      // Default options
      const defaultOptions = {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        variant: 'primary'
      }
      
      const finalOptions = { ...defaultOptions, ...options }
      
      // Create the dialog component
      const dialogVNode = h(ConfirmDialog, {
        ...finalOptions,
        isVisible: true,
        onConfirm: () => {
          cleanup()
          resolve(true)
        },
        onCancel: () => {
          cleanup()
          resolve(false)
        },
        onClose: () => {
          cleanup()
          resolve(false)
        }
      })
      
      // Cleanup function
      const cleanup = () => {
        if (container.parentNode) {
          render(null, container)
          document.body.removeChild(container)
        }
      }
      
      // Render the dialog
      render(dialogVNode, container)
    })
  }

  return {
    show
  }
} 