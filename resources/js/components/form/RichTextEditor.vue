<!--
  Project: Corporate Healthcare Platform UI Library
  Component: RichTextEditor
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="rich-text-editor" ref="editorRef">
    <!-- Toolbar -->
    <div class="rich-text-toolbar">
      <div class="rich-text-toolbar-group">
        <button
          type="button"
          class="rich-text-toolbar-button"
          :class="{ 'rich-text-toolbar-button-active': isFormatActive('bold') }"
          @click="execCommand('bold')"
          title="Bold"
        >
          <Icon name="bold" size="sm" />
        </button>
        <button
          type="button"
          class="rich-text-toolbar-button"
          :class="{ 'rich-text-toolbar-button-active': isFormatActive('italic') }"
          @click="execCommand('italic')"
          title="Italic"
        >
          <Icon name="italic" size="sm" />
        </button>
        <button
          type="button"
          class="rich-text-toolbar-button"
          :class="{ 'rich-text-toolbar-button-active': isFormatActive('underline') }"
          @click="execCommand('underline')"
          title="Underline"
        >
          <Icon name="underline" size="sm" />
        </button>
      </div>
      <div class="rich-text-toolbar-group">
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="execCommand('insertUnorderedList')"
          title="Bullet List"
        >
          <Icon name="list-ul" size="sm" />
        </button>
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="execCommand('insertOrderedList')"
          title="Numbered List"
        >
          <Icon name="list-ol" size="sm" />
        </button>
      </div>
      <div class="rich-text-toolbar-group">
        <select
          class="rich-text-select"
          @change="execCommand('formatBlock', $event.target.value)"
        >
          <option value="p">Paragraph</option>
          <option value="h1">Heading 1</option>
          <option value="h2">Heading 2</option>
          <option value="h3">Heading 3</option>
          <option value="h4">Heading 4</option>
        </select>
      </div>
      <div class="rich-text-toolbar-group">
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="createLink"
          title="Insert Link"
        >
          <Icon name="link" size="sm" />
        </button>
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="insertImage"
          title="Insert Image"
        >
          <Icon name="image" size="sm" />
        </button>
      </div>
      <div class="rich-text-toolbar-group">
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="execCommand('undo')"
          title="Undo"
        >
          <Icon name="undo" size="sm" />
        </button>
        <button
          type="button"
          class="rich-text-toolbar-button"
          @click="execCommand('redo')"
          title="Redo"
        >
          <Icon name="redo" size="sm" />
        </button>
      </div>
    </div>

    <!-- Contenteditable Area -->
    <div
      ref="contentRef"
      class="rich-text-content"
      contenteditable="true"
      :placeholder="placeholder"
      :aria-disabled="disabled"
      @input="handleInput"
      @paste="handlePaste"
      @keydown="handleKeydown"
    ></div>

    <!-- Character Counter -->
    <div v-if="showCharCount" class="rich-text-footer">
      <span class="rich-text-char-count">{{ charCount }} character{{ charCount === 1 ? '' : 's' }}</span>
    </div>

    <!-- Link Modal -->
    <AppDialog
      v-model="showLinkModal"
      title="Insert Link"
      size="sm"
    >
      <div class="rich-text-link-form">
        <FormGroup label="URL">
          <InputText
            v-model="linkUrl"
            placeholder="https://example.com"
            type="url"
          />
        </FormGroup>
        <FormGroup label="Text">
          <InputText
            v-model="linkText"
            placeholder="Link text"
          />
        </FormGroup>
      </div>
      <template #footer>
        <AppButton variant="outline" @click="showLinkModal = false">
          Cancel
        </AppButton>
        <AppButton variant="primary" @click="insertLink">
          Insert
        </AppButton>
      </template>
    </AppDialog>

    <!-- Image Modal -->
    <AppDialog
      v-model="showImageModal"
      title="Insert Image"
      size="sm"
    >
      <div class="rich-text-image-form">
        <FormGroup label="Image URL">
          <InputText
            v-model="imageUrl"
            placeholder="https://example.com/image.jpg"
            type="url"
          />
        </FormGroup>
        <FormGroup label="Alt Text">
          <InputText
            v-model="imageAlt"
            placeholder="Image description"
          />
        </FormGroup>
      </div>
      <template #footer>
        <AppButton variant="outline" @click="showImageModal = false">
          Cancel
        </AppButton>
        <AppButton variant="primary" @click="insertImageUrl">
          Insert
        </AppButton>
      </template>
    </AppDialog>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref, computed, onMounted, watch } from 'vue'
import Icon from '../visual/Icon.vue'
import AppDialog from '../modal/AppDialog.vue'
import FormGroup from './FormGroup.vue'
import InputText from './InputText.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Write your text here...' },
  disabled: { type: Boolean, default: false },
  showCharCount: { type: Boolean, default: true },
  maxLength: { type: Number, default: null }
})

const emit = defineEmits(['update:modelValue', 'change'])

const editorRef = ref(null)
const contentRef = ref(null)
const showLinkModal = ref(false)
const showImageModal = ref(false)
const linkUrl = ref('')
const linkText = ref('')
const imageUrl = ref('')
const imageAlt = ref('')

const charCount = computed(() => {
  return contentRef.value?.textContent?.length || 0
})

function execCommand(command, value = null) {
  if (props.disabled) return
  document.execCommand(command, false, value)
  contentRef.value?.focus()
  updateValue()
}

function isFormatActive(command) {
  return document.queryCommandState(command)
}

function handleInput() {
  updateValue()
}

function handlePaste(event) {
  event.preventDefault()
  const text = event.clipboardData.getData('text/plain')
  document.execCommand('insertText', false, text)
}

function handleKeydown(event) {
  if (props.maxLength && charCount.value >= props.maxLength) {
    if (event.key.length === 1 && !event.ctrlKey && !event.metaKey) {
      event.preventDefault()
    }
  }
}

function updateValue() {
  const html = contentRef.value?.innerHTML || ''
  emit('update:modelValue', html)
  emit('change', html)
}

function createLink() {
  const selection = window.getSelection()
  if (selection && selection.toString()) {
    linkText.value = selection.toString()
    showLinkModal.value = true
  }
}

function insertLink() {
  if (linkUrl.value) {
    const link = `<a href="${linkUrl.value}" target="_blank" rel="noopener">${linkText.value || linkUrl.value}</a>`
    document.execCommand('insertHTML', false, link)
    updateValue()
  }
  showLinkModal.value = false
  linkUrl.value = ''
  linkText.value = ''
}

function insertImage() {
  showImageModal.value = true
}

function insertImageUrl() {
  if (imageUrl.value) {
    const img = `<img src="${imageUrl.value}" alt="${imageAlt.value}" style="max-width: 100%; height: auto;">`
    document.execCommand('insertHTML', false, img)
    updateValue()
  }
  showImageModal.value = false
  imageUrl.value = ''
  imageAlt.value = ''
}

// Initialize content
function initializeContent() {
  if (contentRef.value) {
    contentRef.value.innerHTML = props.modelValue
  }
}

onMounted(() => {
  initializeContent()
})
watch(() => props.modelValue, () => {
  if (contentRef.value && contentRef.value.innerHTML !== props.modelValue) {
    contentRef.value.innerHTML = props.modelValue
  }
})
</script>

<style scoped>
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-card: #181124;
  --color-label: #FFF;
  --color-border: #6D488D;
  --color-error: #ef4444;
  --color-desc: #B1A9C7;
  --color-placeholder: #7C7192;
}

/* Main wrapper */
.rich-text-editor {
  border: 2px solid var(--color-border);
  border-radius: 1.2rem;
  overflow: hidden;
  background: var(--color-bg-card);
  box-shadow: 0 6px 36px 0 rgba(90,17,136,0.10);
  transition: border 0.25s;
}

/* Toolbar */
.rich-text-toolbar {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  padding: 0.85rem 1.2rem 0.85rem 1.2rem;
  background: #20153b;
  border-bottom: 1.5px solid #2A183D;
  flex-wrap: wrap;
}
.rich-text-toolbar-group {
  display: flex;
  align-items: center;
  gap: 0.24rem;
  padding-right: 0.6rem;
  border-right: 1.2px solid #2A183D;
}
.rich-text-toolbar-group:last-child {
  border-right: none;
  padding-right: 0;
}
.rich-text-toolbar-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.2rem;
  height: 2.2rem;
  background: #291952;
  border: 2px solid #22153b;
  border-radius: 0.65rem;
  color: var(--color-desc);
  cursor: pointer;
  transition: all 0.18s;
  font-size: 1rem;
  box-shadow: 0 1.5px 4px 0 rgba(157,56,207,0.07);
}
.rich-text-toolbar-button:hover:not(:disabled),
.rich-text-toolbar-button:focus-visible:not(:disabled) {
  background: var(--color-accent);
  color: #fff;
  border-color: var(--color-accent);
  outline: none;
  transform: scale(1.08);
}
.rich-text-toolbar-button:disabled {
  opacity: 0.55;
  pointer-events: none;
}
.rich-text-toolbar-button-active {
  background: #3f2561;
  color: var(--color-accent);
  border-color: var(--color-accent);
  box-shadow: 0 2px 8px 0 rgba(157,56,207,0.13);
}
.rich-text-select {
  padding: 0.2rem 0.7rem;
  background: #291952;
  border: 2px solid #22153b;
  border-radius: 0.5rem;
  font-size: 0.97rem;
  color: var(--color-desc);
  cursor: pointer;
  transition: border 0.15s;
}
.rich-text-select:focus {
  border-color: var(--color-accent);
  outline: none;
}
.rich-text-content {
  min-height: 220px;
  padding: 1.1rem 1.2rem;
  background: var(--color-bg-card);
  color: var(--color-label);
  font-size: 1.07rem;
  font-family: Inter, system-ui, sans-serif;
  line-height: 1.65;
  outline: none;
  border: none;
  overflow-y: auto;
  border-radius: 0 0 1.2rem 1.2rem;
  transition: background 0.22s;
}
.rich-text-content:empty:before {
  content: attr(placeholder);
  color: var(--color-placeholder);
  pointer-events: none;
  opacity: 0.85;
  font-size: 1.05rem;
}
.rich-text-content:focus {
  outline: none;
  background: #22153b;
  box-shadow: 0 0 0 4px rgba(157,56,207,0.10);
}
.rich-text-content h1, .rich-text-content h2, .rich-text-content h3, .rich-text-content h4 {
  margin: 1rem 0 0.5rem 0;
  font-weight: 600;
  line-height: 1.25;
  color: var(--color-accent);
}
.rich-text-content h1 { font-size: 1.5rem; }
.rich-text-content h2 { font-size: 1.25rem; }
.rich-text-content h3 { font-size: 1.1rem; }
.rich-text-content h4 { font-size: 1rem; }
.rich-text-content p { margin: 0.5rem 0; }
.rich-text-content ul, .rich-text-content ol {
  margin: 0.5rem 0;
  padding-left: 1.5rem;
}
.rich-text-content li { margin: 0.25rem 0; }
.rich-text-content a {
  color: var(--color-accent);
  text-decoration: underline;
  transition: color 0.15s;
}
.rich-text-content a:hover {
  color: #fff;
  background: var(--color-accent);
  border-radius: 4px;
  padding: 0 3px;
}
.rich-text-content img {
  max-width: 100%;
  height: auto;
  margin: 0.5rem 0;
  border-radius: 0.45rem;
  box-shadow: 0 1px 5px rgba(157,56,207,0.08);
}
/* Footer / Char Counter */
.rich-text-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0.6rem 1.2rem;
  background: #20153b;
  border-top: 1.2px solid #2A183D;
  border-radius: 0 0 1.2rem 1.2rem;
}
.rich-text-char-count {
  font-size: 0.93rem;
  color: var(--color-desc);
  letter-spacing: 0.01em;
}
/* Modal Forms */
.rich-text-link-form,
.rich-text-image-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
/* Scrollbar */
.rich-text-content::-webkit-scrollbar { width: 4px;}
.rich-text-content::-webkit-scrollbar-track { background: transparent;}
.rich-text-content::-webkit-scrollbar-thumb { background: #31214d; border-radius: 2px;}
.rich-text-content::-webkit-scrollbar-thumb:hover { background: #4e327a;}
/* Animations */
.rich-text-toolbar-button,
.rich-text-toolbar-group,
.rich-text-content,
.rich-text-footer {
  will-change: background, color, border, box-shadow, transform;
}
/* Responsive */
@media (max-width: 640px) {
  .rich-text-toolbar { flex-direction: column; align-items: stretch; gap: 0.45rem; padding: 0.7rem;}
  .rich-text-toolbar-group { justify-content: center; border-right: none; border-bottom: 1.5px solid #2A183D; padding-right: 0; padding-bottom: 0.35rem;}
  .rich-text-toolbar-group:last-child { border-bottom: none; padding-bottom: 0;}
  .rich-text-content, .rich-text-footer { padding-left: 0.65rem; padding-right: 0.65rem;}
}
</style>
