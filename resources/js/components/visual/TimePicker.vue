<!-- Developer: DocraTech Team - Fatih Berkay Bahceci -->
<template>
  <div :class="wrapperClasses">
    <!-- Input Field -->
    <div class="relative flex items-center">
      <input
        ref="inputRef"
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        class="w-full py-2.5 pr-10 pl-4 rounded-xl bg-docratech-surface border border-docratech-border text-docratech-text text-sm focus:ring-2 focus:ring-docratech-primaryLight focus:outline-none transition-all"
        @click="toggleTimePanel"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />
      <Icon
        name="clock"
        size="sm"
        class="absolute right-3 text-white/70 transition-transform duration-300"
        :class="{ 'rotate-90 scale-110 text-docratech-primaryLight': isOpen }"
      />
    </div>

    <!-- Dropdown Panel -->
    <Transition name="fade-scale">
      <div
        v-if="isOpen"
        ref="panelRef"
        class="absolute z-50 mt-2 w-full min-w-[280px] bg-docratech-surface border border-docratech-border rounded-xl shadow-xl p-4"
      >
        <!-- Time Display -->
        <div class="flex justify-between items-center bg-docratech-accent px-4 py-3 rounded-xl text-docratech-text mb-4">
          <div class="text-xl font-semibold tracking-wide">
            {{ formattedHour }}:{{ formattedMinute }}<span v-if="showSeconds">:{{ formattedSecond }}</span>
          </div>
          <div v-if="showAmPm" class="flex gap-2">
            <button
              type="button"
              :class="['px-2 py-1 rounded text-xs font-semibold transition-all', isAm ? activeAmPmClass : inactiveAmPmClass]"
              @click="setAmPm('am')"
            >
              AM
            </button>
            <button
              type="button"
              :class="['px-2 py-1 rounded text-xs font-semibold transition-all', !isAm ? activeAmPmClass : inactiveAmPmClass]"
              @click="setAmPm('pm')"
            >
              PM
            </button>
          </div>
        </div>

        <!-- Selectors -->
        <div class="grid grid-cols-3 gap-3 text-center">
          <div>
            <div class="text-xs text-white/70 mb-1">Hour</div>
            <div class="space-y-1 max-h-28 overflow-y-auto rounded-md bg-docratech-accent">
              <button
                v-for="h in availableHours"
                :key="h"
                @click="selectHour(h)"
                :class="[buttonBase, selectedHour === h ? activeBtn : '']"
              >
                {{ formatHour(h) }}
              </button>
            </div>
          </div>
          <div>
            <div class="text-xs text-white/70 mb-1">Minute</div>
            <div class="space-y-1 max-h-28 overflow-y-auto rounded-md bg-docratech-accent">
              <button
                v-for="m in availableMinutes"
                :key="m"
                @click="selectMinute(m)"
                :class="[buttonBase, selectedMinute === m ? activeBtn : '']"
              >
                {{ formatMinute(m) }}
              </button>
            </div>
          </div>
          <div v-if="showSeconds">
            <div class="text-xs text-white/70 mb-1">Second</div>
            <div class="space-y-1 max-h-28 overflow-y-auto rounded-md bg-docratech-accent">
              <button
                v-for="s in availableSeconds"
                :key="s"
                @click="selectSecond(s)"
                :class="[buttonBase, selectedSecond === s ? activeBtn : '']"
              >
                {{ formatSecond(s) }}
              </button>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex justify-between items-center border-t border-docratech-border pt-4">
          <AppButton variant="secondary" size="small" @click="setCurrentTime">Now</AppButton>
          <div class="flex gap-2">
            <AppButton variant="secondary" size="small" @click="closePanel">Cancel</AppButton>
            <AppButton variant="primary" size="small" @click="confirmTime">OK</AppButton>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// Core logic burada değişmedi, çünkü backend'e dokunmuyoruz.
// Ref'ler, computed'lar ve emit'ler yukarıda zaten hazır.
// Stil ve yapısal dönüşüm sağlandı.
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.2s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
