<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: Breadcrumb (Enterprise - Brandkit - UX)
-->

<template>
  <nav
    class="breadcrumb"
    :class="[`breadcrumb-${size}`, `breadcrumb-${color}`]"
    aria-label="Breadcrumb"
  >
    <ol class="breadcrumb-list" role="list">
      <li
        v-for="(item, index) in items"
        :key="item.id || index"
        class="breadcrumb-item"
        :class="{ active: index === items.length - 1 }"
      >
        <!-- Aktif adım (son), <span> olarak göster -->
        <span
          v-if="index === items.length - 1"
          class="breadcrumb-link"
          aria-current="page"
        >
          <i v-if="item.icon" :class="['bi', item.icon, 'breadcrumb-icon']" />
          <span class="breadcrumb-text">{{ item.text || item.label }}</span>
        </span>
        <!-- Router link veya anchor link, erişilebilirlik ile -->
        <component
          v-else
          :is="item.to ? 'router-link' : (item.href ? 'a' : 'button')"
          :to="item.to"
          :href="item.href"
          class="breadcrumb-link focusable"
          tabindex="0"
          :aria-current="index === items.length - 1 ? 'page' : null"
          @click="handleClick(item, index, $event)"
          @keydown.enter="handleClick(item, index, $event)"
        >
          <i v-if="item.icon" :class="['bi', item.icon, 'breadcrumb-icon']" />
          <span class="breadcrumb-text">{{ item.text || item.label }}</span>
        </component>
        <!-- Separator, son adım hariç -->
        <i
          v-if="index < items.length - 1"
          :class="['bi', separatorIcon, 'breadcrumb-separator']"
          aria-hidden="true"
        />
      </li>
    </ol>
  </nav>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    items: {
      type: Array,
      required: true,
      validator: items => items.every(item =>
        typeof item === 'object' &&
        (item.text || item.label)
      )
    },
    separator: {
      type: String,
      default: 'chevron-right',
      validator: value =>
        ['chevron-right', 'chevron-down', 'slash', 'arrow-right'].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: value => ['sm', 'md', 'lg'].includes(value)
    },
    color: {
      type: String,
      default: 'primary',
      validator: value =>
        ['primary', 'secondary', 'gray', 'white'].includes(value)
    }
  },
  computed: {
    separatorIcon() {
      const icons = {
        'chevron-right': 'bi-chevron-right',
        'chevron-down': 'bi-chevron-down',
        'slash': 'bi-slash',
        'arrow-right': 'bi-arrow-right'
      }
      return icons[this.separator]
    }
  },
  methods: {
    handleClick(item, index, event) {
      // Default olarak native route/href çalışsın.
      // onClick varsa onu çağır ve gerekirse event.stop
      if (item.onClick) {
        event.preventDefault()
        item.onClick(item, index, event)
      }
    }
  }
}
</script>

<style scoped>
/* Brandkit + dark/light + UX/Animation */
.breadcrumb {
  display: flex;
  align-items: center;
  font-family: 'Inter', sans-serif;
  padding: 0.3rem 0;
  user-select: none;
}
.breadcrumb-list {
  display: flex;
  align-items: center;
  list-style: none;
  margin: 0;
  padding: 0;
  flex-wrap: wrap;
}
.breadcrumb-item {
  display: flex;
  align-items: center;
  gap: 4px;
  animation: breadcrumb-in 0.33s cubic-bezier(0.22,1,0.36,1);
}
@keyframes breadcrumb-in {
  0% { opacity: 0; transform: translateY(10px);}
  100% { opacity: 1; transform: translateY(0);}
}
.breadcrumb-link {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.18s, color 0.18s, transform 0.18s;
  color: #6b7280;
  font-weight: 500;
  background: none;
  cursor: pointer;
  outline: none;
  border: none;
}
.breadcrumb-link:hover,
.breadcrumb-link:focus,
.focusable:focus {
  color: #9D38CF;
  background: linear-gradient(90deg, #ede9fe 0%, #f3e8ff 100%);
  text-decoration: underline;
  outline: 2px solid #9D38CF;
  outline-offset: 1px;
  transform: translateY(-1px) scale(1.02);
}
.breadcrumb-item.active .breadcrumb-link,
.breadcrumb-item.active > span.breadcrumb-link {
  color: #5A1188;
  background: linear-gradient(90deg, #ede9fe 0%, #f3e8ff 100%);
  font-weight: 700;
  cursor: default;
  pointer-events: none;
  animation: fadeInBreadcrumb 0.36s cubic-bezier(.22,1,.36,1);
}
@keyframes fadeInBreadcrumb {
  0% { opacity: 0; transform: translateY(14px);}
  100% { opacity: 1; transform: translateY(0);}
}
.breadcrumb-icon {
  font-size: 1em;
  opacity: 0.8;
  transition: transform 0.2s;
  margin-right: 1px;
}
.breadcrumb-link:hover .breadcrumb-icon,
.breadcrumb-link:focus .breadcrumb-icon {
  transform: scale(1.1) rotate(-6deg);
}
.breadcrumb-text {
  font-size: 0.96em;
}
.breadcrumb-separator {
  color: #c2a3e6;
  font-size: 0.92em;
  margin: 0 2px;
  animation: separator-pulse 1.8s infinite;
}
@keyframes separator-pulse {
  0%, 100% { opacity: 0.56; }
  50% { opacity: 1; }
}

/* Size variants */
.breadcrumb-sm .breadcrumb-link { padding: 4px 8px; font-size: 0.88em; }
.breadcrumb-lg .breadcrumb-link { padding: 10px 16px; font-size: 1.13em; }

/* Brand color variants */
.breadcrumb-primary .breadcrumb-link:hover,
.breadcrumb-primary .breadcrumb-link:focus {
  color: #5A1188;
}
.breadcrumb-primary .breadcrumb-item.active .breadcrumb-link {
  color: #9D38CF;
}
.breadcrumb-secondary .breadcrumb-link:hover,
.breadcrumb-secondary .breadcrumb-link:focus {
  color: #3b82f6;
}
.breadcrumb-secondary .breadcrumb-item.active .breadcrumb-link {
  color: #3b82f6;
  background: #f0f9ff;
}
.breadcrumb-gray .breadcrumb-link:hover {
  color: #374151;
}
.breadcrumb-gray .breadcrumb-item.active .breadcrumb-link {
  color: #374151;
  background: #f3f4f6;
}
.breadcrumb-white .breadcrumb-link {
  color: rgba(255,255,255,0.8);
}
.breadcrumb-white .breadcrumb-link:hover {
  color: #fff;
  background: rgba(255,255,255,0.11);
}
.breadcrumb-white .breadcrumb-item.active .breadcrumb-link {
  color: #fff;
  background: rgba(255,255,255,0.23);
}
.breadcrumb-white .breadcrumb-separator {
  color: rgba(255,255,255,0.6);
}
@media (max-width: 700px) {
  .breadcrumb-list { gap: 1px;}
  .breadcrumb-text, .breadcrumb-link { font-size: 0.91em;}
}
</style>
