<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Footer
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, mikro animasyonlu, gelişmiş footer bileşeni.
-->
<template>
  <footer :class="footerClasses">
    <div class="max-w-screen-xl mx-auto flex flex-wrap justify-between items-center gap-4 px-6 py-8">
      <!-- Logo and Copyright -->
      <div class="flex items-center gap-3">
        <img v-if="logo" :src="logo" :alt="logoAlt" class="h-8 w-auto object-contain" />
        <span class="text-sm text-gray-400">&copy; {{ new Date().getFullYear() }} {{ brandName }}. All Rights Reserved</span>
      </div>

      <!-- Links -->
      <nav class="flex gap-6">
        <a v-for="link in links" :key="link.href" :href="link.href" class="text-gray-400 hover:text-docratech-primaryLight transition duration-300 font-medium" target="_blank" rel="noopener">
          {{ link.label }}
        </a>
      </nav>

      <!-- Social Icons -->
      <div class="flex gap-4">
        <a
          v-for="icon in social"
          :key="icon.name"
          :href="icon.href"
          class="text-gray-400 hover:text-docratech-primaryLight transform hover:scale-125 transition duration-300"
          target="_blank"
          rel="noopener"
          :aria-label="icon.name"
        >
          <Icon :name="icon.name" size="md" />
        </a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  logo: String,
  logoAlt: { type: String, default: 'Logo' },
  brandName: { type: String, default: 'DocraTech - Medical Website Platform' },
  links: {
    type: Array,
    default: () => [
      { label: 'Privacy', href: '#' },
      { label: 'Terms Of Use', href: '#' },
      { label: 'Contact', href: '#' }
    ]
  },
  social: {
    type: Array,
    default: () => [
      { name: 'twitter', href: '#' },
      { name: 'linkedin', href: '#' },
      { name: 'github', href: '#' }
    ]
  },
  variant: { type: String, default: 'dark' },
  animated: { type: Boolean, default: true }
})

const footerClasses = computed(() => [
  props.variant === 'dark' ? 'bg-docratech-background border-t border-docratech-secondary' : 'bg-white border-t border-gray-200',
  'transition-colors duration-300'
])
</script>

<style scoped>
footer {
  font-family: Inter, sans-serif;
}

@media (max-width: 768px) {
  .max-w-screen-xl {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  nav {
    justify-content: center;
  }
}
</style>
