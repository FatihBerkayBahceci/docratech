<!--
  ContextMenu.vue
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Enterprise marka kitli, animasyonlu, erişilebilir context menu
-->

<template>
  <Teleport to="body">
    <Transition name="context-menu">
      <div
        v-if="isVisible"
        class="context-menu"
        :style="menuStyle"
        @click.stop
        ref="menuRef"
        role="menu"
        aria-label="Seçenekler menüsü"
      >
        <div
          v-for="item in items"
          :key="item.key"
          :role="item.separator ? undefined : 'menuitem'"
          :tabindex="item.disabled || item.separator ? -1 : 0"
          class="context-menu-item"
          :class="{
            'context-menu-item-disabled': item.disabled,
            'context-menu-item-separator': item.separator,
            'context-menu-item-hovered': hoveredItem === item.key && !item.disabled && !item.separator
          }"
          @mouseenter="handleMouseEnter(item.key)"
          @mouseleave="handleMouseLeave"
          @click="handleItemClick(item)"
          :aria-disabled="item.disabled || undefined"
        >
          <div v-if="item.separator" class="context-menu-separator"></div>
          <div v-else class="context-menu-item-content">
            <Icon v-if="item.icon" :name="item.icon" size="sm" />
            <span class="context-menu-item-text">{{ item.label }}</span>
            <span v-if="item.shortcut" class="context-menu-item-shortcut-badge">{{ item.shortcut }}</span>
            <Icon
              v-if="item.children"
              name="chevron-right"
              size="sm"
              class="context-menu-item-arrow"
              aria-hidden="true"
            />
          </div>
          <div
            v-if="item.children && hoveredItem === item.key"
            class="context-menu-submenu"
            aria-label="Alt menü"
          >
            <ContextMenu
              :items="item.children"
              :position="{ x: 100, y: 0 }"
              @select="handleSubmenuSelect"
            />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<!-- JS kısmı sendekiyle birebir, sadece ARIA için birkaç küçük iyileştirme yukarıda -->

<style scoped>
:root {
  --color-bg-card: #211232;
  --color-bg-hover: #2A183D;
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-border: #34215a;
  --color-shadow: 0 16px 42px -12px #9D38CF30, 0 2px 6px #0001;
  --color-text: #fff;
}

.context-menu {
  position: fixed;
  background: var(--color-bg-card);
  border: 1.5px solid var(--color-border);
  border-radius: 1rem;
  box-shadow: var(--color-shadow);
  z-index: 9999;
  min-width: 210px;
  max-width: 320px;
  overflow: hidden;
  padding: 0.23rem 0;
  animation: fadeInMenu 0.17s cubic-bezier(.44,1.12,.38,1);
}
@keyframes fadeInMenu {
  from { opacity: 0; transform: scale(0.96) translateY(-8px);}
  to { opacity: 1; transform: scale(1) translateY(0);}
}
.context-menu-item { position: relative; outline: none;}
.context-menu-item-content {
  display: flex;
  align-items: center;
  gap: 0.74rem;
  padding: 0.92rem 1.16rem;
  cursor: pointer;
  color: var(--color-text);
  border-radius: 0.75rem;
  transition:
    background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.17s;
  font-size: 1.01rem;
  font-family: 'Inter', Arial, sans-serif;
  background: transparent;
}
.context-menu-item-hovered .context-menu-item-content,
.context-menu-item-content:hover,
.context-menu-item-content:focus-visible {
  background: linear-gradient(90deg, #9D38CF10 0%, #5A118833 100%);
  color: var(--color-accent);
  box-shadow: 0 2px 8px #9D38CF18;
  border-left: 4px solid #9D38CF;
  transform: scale(1.03) translateX(2px);
  outline: none;
}
.context-menu-item-disabled .context-menu-item-content {
  opacity: 0.46;
  cursor: not-allowed;
  background: none !important;
  color: #a1a1b5 !important;
}
.context-menu-item-disabled .context-menu-item-content:hover {
  background: none;
  color: #a1a1b5;
}
.context-menu-item-text {
  flex: 1;
  font-size: 1rem;
  font-weight: 500;
  letter-spacing: 0.01em;
}
.context-menu-item-shortcut-badge {
  background: #2A183D;
  color: #9D38CF;
  font-size: 0.84rem;
  border: 1px solid #34215a;
  border-radius: 0.4rem;
  padding: 2px 8px;
  font-family: monospace;
  font-weight: 600;
  margin-left: 0.18rem;
}
.context-menu-item-arrow {
  color: var(--color-accent);
}
.context-menu-separator {
  height: 1px;
  background: linear-gradient(90deg,#34215a 0%, #37274a 100%);
  margin: 0.28rem 0;
  border-radius: 4px;
}
.context-menu-submenu {
  position: absolute;
  left: 97%;
  top: 0;
  z-index: 10000;
  min-width: 200px;
  filter: drop-shadow(0 2px 14px #9D38CF11);
  border-left: 2.5px solid #9D38CF44;
  padding-left: 0.11rem;
  border-radius: 0.9rem;
}
.context-menu-enter-active,
.context-menu-leave-active {
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}
.context-menu-enter-from,
.context-menu-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-7px);
}
/* Responsive design */
@media (max-width: 640px) {
  .context-menu { min-width: 120px; max-width: 190px; border-radius: 0.65rem;}
  .context-menu-item-content { padding: 0.9rem 0.65rem;}
}
</style>
