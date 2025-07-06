<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: Pagination (Enterprise - Brandkit - UX)
-->

<template>
  <nav
    class="pagination"
    :class="[`pagination-${size}`, `pagination-${color}`]"
    role="navigation"
    aria-label="Sayfalandırma"
  >
    <div v-if="showInfo" class="pagination-info">
      <span class="pagination-text" aria-live="polite">
        {{ startItem }}-{{ endItem }} / {{ totalItems }} öğe
      </span>
    </div>
    
    <ul class="pagination-nav" role="list">
      <!-- Önceki -->
      <li>
        <button
          class="pagination-btn pagination-prev"
          :disabled="currentPage <= 1"
          @click="goToPage(currentPage - 1)"
          aria-label="Önceki sayfa"
        >
          <i class="bi bi-chevron-left"></i>
          <span class="pagination-btn-text">Önceki</span>
        </button>
      </li>
      
      <!-- Sayfa numaraları -->
      <li class="pagination-pages" role="presentation">
        <button
          v-for="page in visiblePages"
          :key="page"
          class="pagination-btn pagination-page"
          :class="{ active: page === currentPage, ellipsis: page === '...' }"
          :disabled="page === '...'"
          @click="page !== '...' && goToPage(page)"
          :aria-current="page === currentPage ? 'page' : null"
          tabindex="0"
        >
          <transition name="page-pop" mode="out-in">
            <span :key="page">{{ page }}</span>
          </transition>
        </button>
      </li>
      
      <!-- Sonraki -->
      <li>
        <button
          class="pagination-btn pagination-next"
          :disabled="currentPage >= totalPages"
          @click="goToPage(currentPage + 1)"
          aria-label="Sonraki sayfa"
        >
          <span class="pagination-btn-text">Sonraki</span>
          <i class="bi bi-chevron-right"></i>
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true,
      validator: value => value > 0
    },
    totalPages: {
      type: Number,
      required: true,
      validator: value => value > 0
    },
    totalItems: {
      type: Number,
      default: 0
    },
    perPage: {
      type: Number,
      default: 10
    },
    showInfo: {
      type: Boolean,
      default: true
    },
    size: {
      type: String,
      default: 'md',
      validator: value => ['sm', 'md', 'lg'].includes(value)
    },
    color: {
      type: String,
      default: 'primary',
      validator: value => ['primary', 'secondary', 'gray'].includes(value)
    }
  },
  computed: {
    startItem() {
      return (this.currentPage - 1) * this.perPage + 1;
    },
    endItem() {
      return Math.min(this.currentPage * this.perPage, this.totalItems);
    },
    visiblePages() {
      const pages = [];
      const maxVisible = 7;
      
      if (this.totalPages <= maxVisible) {
        for (let i = 1; i <= this.totalPages; i++) {
          pages.push(i);
        }
      } else {
        if (this.currentPage <= 4) {
          for (let i = 1; i <= 5; i++) {
            pages.push(i);
          }
          pages.push('...');
          pages.push(this.totalPages);
        } else if (this.currentPage >= this.totalPages - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = this.totalPages - 4; i <= this.totalPages; i++) {
            pages.push(i);
          }
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = this.currentPage - 1; i <= this.currentPage + 1; i++) {
            pages.push(i);
          }
          pages.push('...');
          pages.push(this.totalPages);
        }
      }
      
      return pages;
    }
  },
  methods: {
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.$emit('update:currentPage', page);
        this.$emit('change', page);
      }
    }
  }
};
</script>

<style scoped>
/* Brandkit ve Enterprise: Mor tonlar, büyük radius, gölge, micro UX animasyonlar */
.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 18px;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
  font-family: 'Inter', sans-serif;
  user-select: none;
  padding: 0.5rem 0;
}
.pagination-info {
  display: flex;
  align-items: center;
}
.pagination-text {
  font-size: 0.95em;
  color: #6b7280;
  font-weight: 500;
  letter-spacing: 0.01em;
}
.pagination-nav {
  display: flex;
  align-items: center;
  gap: 12px;
  list-style: none;
  padding: 0;
  margin: 0;
}
.pagination-pages {
  display: flex;
  align-items: center;
  gap: 4px;
  transition: gap 0.2s;
}
.pagination-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  padding: 8px 14px;
  border: none;
  background: #f9fafb;
  color: #6b7280;
  font-weight: 500;
  cursor: pointer;
  border-radius: 16px;
  transition: all 0.2s cubic-bezier(0.22, 1, 0.36, 1);
  min-width: 40px;
  height: 42px;
  outline: none;
  position: relative;
  box-shadow: 0 1px 4px 0 rgba(154, 56, 207, 0.03);
}
.pagination-btn:focus-visible {
  outline: 2px solid #9D38CF;
  outline-offset: 2px;
}
.pagination-btn:hover:not(:disabled),
.pagination-btn:focus-visible:not(:disabled) {
  background: linear-gradient(90deg, #ede9fe 0%, #f3e8ff 100%);
  color: #9D38CF;
  transform: translateY(-1px) scale(1.03);
  box-shadow: 0 2px 12px 0 rgba(154, 56, 207, 0.09);
}
.pagination-btn.active {
  background: linear-gradient(135deg, #5A1188 0%, #9D38CF 100%);
  color: #fff;
  font-weight: 600;
  box-shadow: 0 4px 16px 0 rgba(154, 56, 207, 0.16);
  z-index: 2;
  animation: page-active 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
@keyframes page-active {
  0% { transform: scale(0.9); }
  50% { transform: scale(1.07); }
  100% { transform: scale(1); }
}
.pagination-btn:disabled {
  opacity: 0.44;
  cursor: not-allowed;
  background: #f4f4fa;
  color: #bbb;
  transform: none;
}
.pagination-btn.ellipsis {
  cursor: default;
  background: transparent;
  color: #a7a7b4;
  font-size: 1.15em;
  font-weight: 400;
  box-shadow: none;
}
.pagination-btn.ellipsis:hover {
  background: transparent;
  transform: none;
}
.pagination-prev, .pagination-next {
  min-width: 82px;
}
.pagination-btn-text {
  font-size: 0.94em;
}
.pagination-btn i {
  font-size: 1em;
  transition: transform 0.2s;
}
.pagination-btn:hover:not(:disabled) i {
  transform: scale(1.1);
}
/* Micro Animation for numbers */
.page-pop-enter-active, .page-pop-leave-active {
  transition: all 0.24s cubic-bezier(.22,1,.36,1);
}
.page-pop-enter-from {
  opacity: 0;
  transform: scale(0.75) translateY(8px);
}
.page-pop-leave-to {
  opacity: 0;
  transform: scale(0.92) translateY(-6px);
}
/* Size variants */
.pagination-sm .pagination-btn {
  padding: 6px 10px;
  min-width: 34px;
  height: 34px;
  font-size: 0.9em;
}
.pagination-sm .pagination-prev, .pagination-sm .pagination-next {
  min-width: 60px;
}
.pagination-lg .pagination-btn {
  padding: 12px 18px;
  min-width: 54px;
  height: 52px;
  font-size: 1.13em;
}
.pagination-lg .pagination-prev, .pagination-lg .pagination-next {
  min-width: 110px;
}
/* Color variants */
.pagination-secondary .pagination-btn:hover:not(:disabled),
.pagination-secondary .pagination-btn:focus-visible:not(:disabled) {
  color: #3b82f6;
  background: linear-gradient(90deg, #f0f9ff 0%, #e0f2fe 100%);
}
.pagination-secondary .pagination-btn.active {
  background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
  box-shadow: 0 4px 12px rgba(59,130,246,0.13);
}
.pagination-gray .pagination-btn:hover:not(:disabled),
.pagination-gray .pagination-btn:focus-visible:not(:disabled) {
  color: #374151;
  background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 100%);
}
.pagination-gray .pagination-btn.active {
  background: linear-gradient(135deg, #374151 0%, #6b7280 100%);
  box-shadow: 0 4px 12px rgba(55,65,81,0.11);
}
/* Mobile */
@media (max-width: 640px) {
  .pagination {
    flex-direction: column;
    gap: 13px;
  }
  .pagination-prev, .pagination-next {
    min-width: 58px;
    font-size: 0.93em;
  }
  .pagination-btn-text {
    display: none;
  }
}
</style>
