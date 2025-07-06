/**
 * Performance optimization utilities
 */

// Debounce function for expensive operations
export function debounce(func, wait, immediate = false) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

// Throttle function for scroll/resize events
export function throttle(func, limit) {
  let inThrottle
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

// Intersection Observer for lazy loading
export function createIntersectionObserver(callback, options = {}) {
  const defaultOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1,
    ...options
  }
  
  return new IntersectionObserver(callback, defaultOptions)
}

// Virtual scrolling utilities
export function calculateVisibleRange(containerHeight, itemHeight, scrollTop, overscan = 5) {
  const startIndex = Math.max(0, Math.floor(scrollTop / itemHeight) - overscan)
  const endIndex = Math.min(
    Math.ceil((scrollTop + containerHeight) / itemHeight) + overscan,
    Math.ceil(containerHeight / itemHeight)
  )
  
  return { startIndex, endIndex }
}

// Memory management
export function cleanupEventListeners(element, events = []) {
  events.forEach(({ event, handler }) => {
    element.removeEventListener(event, handler)
  })
}

// Image lazy loading
export function lazyLoadImage(imgElement, src) {
  const observer = createIntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target
        img.src = src
        img.classList.remove('lazy')
        observer.unobserve(img)
      }
    })
  })
  
  observer.observe(imgElement)
}

// Component caching
export const componentCache = new Map()

export function cacheComponent(key, component) {
  componentCache.set(key, component)
}

export function getCachedComponent(key) {
  return componentCache.get(key)
}

// Animation frame optimization
export function requestAnimationFrame(callback) {
  return window.requestAnimationFrame(callback)
}

export function cancelAnimationFrame(id) {
  return window.cancelAnimationFrame(id)
}

// DOM manipulation optimization
export function batchDOMUpdates(updates) {
  requestAnimationFrame(() => {
    updates.forEach(update => update())
  })
}

// Event delegation
export function createEventDelegator(container, selector, eventType, handler) {
  container.addEventListener(eventType, (event) => {
    const target = event.target.closest(selector)
    if (target && container.contains(target)) {
      handler(event, target)
    }
  })
}

// Resize observer for responsive components
export function createResizeObserver(callback) {
  return new ResizeObserver(debounce(callback, 16))
}

// Mutation observer for dynamic content
export function createMutationObserver(callback, options = {}) {
  const defaultOptions = {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: ['class', 'style'],
    ...options
  }
  
  return new MutationObserver(debounce(callback, 16))
}

// Performance monitoring
export function measurePerformance(name, fn) {
  const start = performance.now()
  const result = fn()
  const end = performance.now()
  
  console.log(`${name} took ${end - start}ms`)
  return result
}

// Async operation queue
export class AsyncQueue {
  constructor() {
    this.queue = []
    this.processing = false
  }
  
  add(task) {
    return new Promise((resolve, reject) => {
      this.queue.push({ task, resolve, reject })
      this.process()
    })
  }
  
  async process() {
    if (this.processing || this.queue.length === 0) return
    
    this.processing = true
    
    while (this.queue.length > 0) {
      const { task, resolve, reject } = this.queue.shift()
      
      try {
        const result = await task()
        resolve(result)
      } catch (error) {
        reject(error)
      }
    }
    
    this.processing = false
  }
}

// Component lifecycle optimization
export function optimizeComponentLifecycle(component) {
  // Memoize computed properties
  if (component.computed) {
    Object.keys(component.computed).forEach(key => {
      const original = component.computed[key]
      component.computed[key] = function() {
        if (!this._memoized) this._memoized = {}
        if (!this._memoized[key]) {
          this._memoized[key] = original.call(this)
        }
        return this._memoized[key]
      }
    })
  }
  
  // Optimize watchers
  if (component.watch) {
    Object.keys(component.watch).forEach(key => {
      const original = component.watch[key]
      component.watch[key] = {
        ...original,
        handler: debounce(original.handler, 16)
      }
    })
  }
  
  return component
}

// CSS-in-JS optimization
export function createOptimizedStyles(styles) {
  const styleElement = document.createElement('style')
  styleElement.textContent = styles
  document.head.appendChild(styleElement)
  
  return () => {
    document.head.removeChild(styleElement)
  }
}

// Bundle size optimization
export function createChunkLoader(chunkName) {
  return () => import(/* webpackChunkName: "[request]" */ `../components/${chunkName}.vue`)
}

// Memory leak prevention
export function createCleanupManager() {
  const cleanups = []
  
  return {
    add(cleanup) {
      cleanups.push(cleanup)
    },
    cleanup() {
      cleanups.forEach(cleanup => cleanup())
      cleanups.length = 0
    }
  }
}

// Export all utilities
export default {
  debounce,
  throttle,
  createIntersectionObserver,
  calculateVisibleRange,
  cleanupEventListeners,
  lazyLoadImage,
  componentCache,
  cacheComponent,
  getCachedComponent,
  requestAnimationFrame,
  cancelAnimationFrame,
  batchDOMUpdates,
  createEventDelegator,
  createResizeObserver,
  createMutationObserver,
  measurePerformance,
  AsyncQueue,
  optimizeComponentLifecycle,
  createOptimizedStyles,
  createChunkLoader,
 