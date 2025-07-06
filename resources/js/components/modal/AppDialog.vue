<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Project: DocraTech Medical Website Platform
  Component: AppDialog (Enterprise-Grade)
-->
<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div :class="modalClasses" role="dialog" aria-modal="true">
          <header v-if="title || $slots.header" class="mb-4 flex items-center justify-between">
            <slot name="header">
              <h2 class="text-lg font-semibold">{{ title }}</h2>
              <button v-if="showClose" @click="close" class="ml-4 btn btn-secondary" aria-label="Close">&times;</button>
            </slot>
          </header>
          <section class="mb-4">
            <slot />
          </section>
          <footer v-if="$slots.footer" class="mt-4">
            <slot name="footer" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
import { computed, ref, onMounted, onUnmounted, nextTick } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

export default {
  name: 'AppDialog',
  components: { Icon, AppButton },
  props: {
    modelValue: { type: Boolean, default: false },
    title: { type: String, default: null },
    description: { type: String, default: null },
    icon: { type: String, default: null },
    size: {
      type: String,
      default: 'md',
      validator: v => ['sm', 'md', 'lg', 'xl', 'full'].includes(v)
    },
    showClose: { type: Boolean, default: true },
    closeOnOverlay: { type: Boolean, default: true },
    closeOnEscape: { type: Boolean, default: true },
    animated: { type: Boolean, default: true },
    showDefaultFooter: { type: Boolean, default: false },
    confirmText: { type: String, default: 'Confirm' },
    cancelText: { type: String, default: 'Cancel' },
    confirmVariant: {
      type: String,
      default: 'primary',
      validator: v => ['primary', 'danger', 'success', 'warning'].includes(v)
    },
    loading: { type: Boolean, default: false }
  },
  emits: ['update:modelValue', 'confirm', 'cancel', 'close'],
  setup(props, { emit }) {
    const dialogRef = ref(null);

    const dialogClasses = computed(() => [
      'app-dialog',
      `app-dialog-${props.size}`,
      { 'app-dialog-animated': props.animated }
    ]);

    const dialogStyles = computed(() => ({
      '--dialog-transition': props.animated
        ? 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
        : 'none'
    }));

    const close = () => {
      emit('update:modelValue', false);
      emit('close');
    };

    const handleOverlayClick = (e) => {
      if (e.target !== e.currentTarget) return;
      if (props.closeOnOverlay) close();
    };

    const handleCancel = () => {
      emit('cancel');
      close();
    };

    const handleConfirm = () => {
      emit('confirm');
    };

    // Focus management for accessibility
    const focusFirst = () => {
      nextTick(() => {
        const firstFocusable = dialogRef.value?.querySelector(
          'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (firstFocusable) firstFocusable.focus();
      });
    };

    // ESC key
    const handleEscape = (event) => {
      if (
        event.key === 'Escape' &&
        props.closeOnEscape &&
        props.modelValue &&
        !props.loading
      ) {
        close();
      }
    };

    // Trap focus in modal
    const focusNext = (e) => {
      const focusableEls = dialogRef.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      );
      const els = Array.from(focusableEls);
      const index = els.indexOf(document.activeElement);
      const nextIndex = (index + 1) % els.length;
      els[nextIndex].focus();
      e.preventDefault();
    };
    const focusPrev = (e) => {
      const focusableEls = dialogRef.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      );
      const els = Array.from(focusableEls);
      const index = els.indexOf(document.activeElement);
      const prevIndex = (index - 1 + els.length) % els.length;
      els[prevIndex].focus();
      e.preventDefault();
    };

    onMounted(() => {
      if (props.modelValue) focusFirst();
      if (props.closeOnEscape) window.addEventListener('keydown', handleEscape);
    });
    onUnmounted(() => {
      window.removeEventListener('keydown', handleEscape);
    });

    return {
      dialogClasses,
      dialogStyles,
      dialogRef,
      close,
      handleOverlayClick,
      handleCancel,
      handleConfirm,
      focusNext,
      focusPrev
    };
  }
};
</script>

<style scoped>
.app-dialog-overlay {
  position: fixed;
  inset: 0;
  background: rgba(40, 16, 61, 0.58);
  backdrop-filter: blur(8px) saturate(1.07);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
  animation: overlay-in 0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes overlay-in {
  0% { opacity: 0; }
  100% { opacity: 1; }
}

.app-dialog {
  background: #fff;
  border-radius: 1.25rem;
  box-shadow: 0 25px 60px -12px #5A118860, 0 2px 8px #2A183D10;
  max-width: 100%;
  max-height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: dialog-in 0.32s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (prefers-color-scheme: dark) {
  .app-dialog {
    background: #181124;
    border: 1px solid #2A183D;
    box-shadow: 0 25px 60px -12px #2A183D, 0 2px 8px #5A118860;
  }
}

.app-dialog-sm { width: 400px; }
.app-dialog-md { width: 500px; }
.app-dialog-lg { width: 640px; }
.app-dialog-xl { width: 800px; }
.app-dialog-full { width: 98vw; height: 98vh; }

.app-dialog-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: 2rem 2rem 1.25rem 2rem;
  border-bottom: 1px solid #ede9fe;
  flex-shrink: 0;
}

.app-dialog-title-section {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  flex: 1;
  min-width: 0;
}

.app-dialog-icon {
  color: #9D38CF;
  flex-shrink: 0;
  margin-top: 0.18rem;
}

.app-dialog-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #5A1188;
  margin: 0 0 0.25rem 0;
  line-height: 1.4;
  letter-spacing: -0.01em;
}

.app-dialog-description {
  font-size: 0.98rem;
  color: #6b7280;
  margin: 0;
  line-height: 1.5;
}

.app-dialog-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
  background: none;
  border: none;
  color: #9D38CF;
  cursor: pointer;
  border-radius: 0.65rem;
  transition: background 0.22s;
  flex-shrink: 0;
}
.app-dialog-close:disabled { opacity: 0.5; cursor: not-allowed; }
.app-dialog-close:hover { background: #ede9fe; color: #5A1188; }

.app-dialog-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
  min-height: 0;
}

.app-dialog-footer {
  padding: 1.25rem 2rem 2rem 2rem;
  border-top: 1px solid #ede9fe;
  background: #f9fafb;
  flex-shrink: 0;
}

@media (prefers-color-scheme: dark) {
  .app-dialog-footer {
    background: #181124;
    border-top-color: #2A183D;
  }
}

.app-dialog-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

@media (max-width: 700px) {
  .app-dialog-sm,
  .app-dialog-md,
  .app-dialog-lg,
  .app-dialog-xl {
    width: 100vw;
    max-width: 100vw;
    border-radius: 1.1rem;
  }
  .app-dialog-content,
  .app-dialog-header,
  .app-dialog-footer {
    padding: 1rem !important;
  }
  .app-dialog-footer {
    flex-direction: column;
    gap: 0.8rem;
  }
}

.dialog-enter-active, .dialog-leave-active {
  transition: all 0.32s cubic-bezier(0.4, 0, 0.2, 1);
}
.dialog-enter-from, .dialog-leave-to {
  opacity: 0;
  transform: scale(0.98) translateY(30px);
}

.app-dialog-content::-webkit-scrollbar {
  width: 8px;
}
.app-dialog-content::-webkit-scrollbar-thumb {
  background: #ede9fe;
  border-radius: 4px;
}
.app-dialog-content::-webkit-scrollbar-thumb:hover {
  background: #9D38CF;
}
</style>
