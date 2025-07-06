<!--
  DocraTech Medical Platform - Enterprise Tabs Component
  Light Mode Only - DocraTech Branding
-->
<template>
  <div class="flex flex-col w-full">
    <div
      class="relative flex bg-gray-50 rounded-2xl shadow-lg overflow-x-auto px-1 py-1"
      role="tablist"
      :aria-orientation="orientation"
    >
      <button
        v-for="(tab, idx) in tabs"
        :key="tab.value"
        type="button"
        class="relative flex items-center gap-2 px-6 py-3 rounded-xl transition-all duration-200 font-medium text-sm md:text-base
          focus:outline-none focus-visible:ring-2 focus-visible:ring-[#5A1188]"
        :class="[
          modelValue === tab.value
            ? 'bg-white text-[#5A1188] font-semibold shadow-sm scale-105 z-10'
            : 'text-gray-500 hover:text-[#5A1188] hover:bg-gray-100 scale-100'
        ]"
        :tabindex="modelValue === tab.value ? 0 : -1"
        role="tab"
        :aria-selected="modelValue === tab.value"
        @click="selectTab(tab.value)"
        @keydown.right="onKeydown(idx + 1)"
        @keydown.left="onKeydown(idx - 1)"
        @keydown.down="onKeydown(idx + 1)"
        @keydown.up="onKeydown(idx - 1)"
      >
        <i
          v-if="tab.icon"
          :class="[
            'bi',
            tab.icon,
            'transition-colors duration-150 text-lg',
            modelValue === tab.value ? 'text-[#5A1188]' : 'text-gray-400'
          ]"
        />
        <span>{{ tab.label }}</span>
        <span
          v-if="tab.badge"
          class="inline-block ml-2 px-2 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white animate-bounce"
        >
          {{ tab.badge }}
        </span>
      </button>
      <!-- DocraTech Brand Gradient Tab Indicator -->
      <div
        class="absolute bottom-1 h-1 rounded bg-gradient-to-r from-[#5A1188] to-[#9D38CF] transition-all duration-300 ease-[cubic-bezier(.22,1,.36,1)] shadow-md"
        :style="indicatorStyle"
      ></div>
    </div>
    <div class="w-full py-8">
      <transition name="tab-fade" mode="out-in">
        <div
          :key="modelValue"
          class="min-h-[48px] text-[1.04em] animate-fadein"
          role="tabpanel"
          :aria-labelledby="`tab-${modelValue}`"
        >
          <slot :name="modelValue" :tab="activeTab">
            {{ activeTab?.content }}
          </slot>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Tabs',
  props: {
    modelValue: [String, Number],
    tabs: {
      type: Array,
      required: true,
      validator: tabs =>
        tabs.every(tab =>
          typeof tab === 'object' &&
          tab.value &&
          tab.label
        )
    },
    orientation: {
      type: String,
      default: 'horizontal',
      validator: value => ['horizontal', 'vertical'].includes(value)
    }
  },
  computed: {
    activeTab() {
      return this.tabs.find(tab => tab.value === this.modelValue);
    },
    indicatorStyle() {
      // Basit hesap: width % ve translateX % (yatay için)
      const idx = this.tabs.findIndex(tab => tab.value === this.modelValue);
      if (idx === -1) return {};
      return {
        left: `calc(${(100 / this.tabs.length) * idx}% + 0.25rem)`,
        width: `calc(${100 / this.tabs.length}% - 0.5rem)`,
      };
    }
  },
  methods: {
    selectTab(value) {
      this.$emit('update:modelValue', value);
      this.$emit('change', value);
    },
    onKeydown(newIdx) {
      if (typeof newIdx !== 'number') return;
      let max = this.tabs.length - 1;
      if (newIdx < 0) newIdx = max;
      if (newIdx > max) newIdx = 0;
      this.selectTab(this.tabs[newIdx].value);
    }
  }
};
</script>

<style scoped>
.tab-fade-enter-active, .tab-fade-leave-active {
  transition: all 0.3s cubic-bezier(.22,1,.36,1);
}
.tab-fade-enter-from { opacity: 0; transform: translateY(12px);}
.tab-fade-leave-to { opacity: 0; transform: translateY(-12px);}
@media (max-width: 640px) {
  .tabs-header {
    overflow-x: auto;
    min-width: 320px;
  }
}
.animate-fadein {
  animation: fadein 0.33s cubic-bezier(.22,1,.36,1);
}
@keyframes fadein {
  0% { opacity: 0; transform: translateY(12px);}
  100% { opacity: 1; transform: translateY(0);}
}
</style>
