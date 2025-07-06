# DocRatech Medical Website Platform - AI Assistant Prompt

## Project Overview
You are an AI assistant working on the DocRatech Medical Website Platform, a modern enterprise-level SPA management panel built with Vue.js 3 and Laravel. The project features a comprehensive UI component library with advanced animations, brand-consistent design, and performance optimizations.

## Core Principles

### 1. Brand Consistency
- **Primary Color**: `#6D28D9` (Purple) - Use as main brand color
- **Secondary Color**: `#3B82F6` (Blue) - Supporting color
- **Typography**: Inter font family with proper weight hierarchy
- **Border Radius**: 0.75rem (default), 0.5rem (sm), 1rem (lg), 1.25rem (xl)
- **Shadows**: Use CSS variables for consistent shadow system
- **Spacing**: Follow 0.25rem (4px) grid system

### 2. Animation Standards
- **Micro Animations**: Hover effects, focus states, loading animations
- **Macro Animations**: Page transitions, component mounting, modal animations
- **Timing**: 0.3s cubic-bezier(0.4, 0, 0.2, 1) for smooth transitions
- **Performance**: Use transform and opacity for GPU acceleration
- **Accessibility**: Respect `prefers-reduced-motion` media query

### 3. Code Quality Standards
- **Clean Code**: Self-explanatory, well-structured, minimal comments
- **Consistent Indentation**: 2 spaces, no mixed tabs/spaces
- **Descriptive Names**: Avoid generic names like 'data', 'thing', 'stuff'
- **Mobile-First**: TailwindCSS utility class ordering
- **English Only**: All code, variables, and comments in English (US)
- **Laravel Best Practices**: Follow Laravel file structure and conventions

### 4. Performance Requirements
- **Bundle Size**: Keep components under 50KB each
- **Lazy Loading**: Use for heavy components
- **Virtual Scrolling**: For large datasets
- **Debounced Inputs**: Reduce API calls
- **Memory Management**: Proper cleanup and event handling

## Component Development Guidelines

### Component Structure
```vue
<template>
  <!-- Template with semantic HTML -->
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Icon from '../visual/Icon.vue'

export default {
  name: 'ComponentName',
  components: { Icon },
  props: {
    // Props with proper validation
  },
  emits: ['event-name'],
  setup(props, { emit }) {
    // Composition API setup
    return {
      // Return reactive data and methods
    }
  }
}
</script>

<style scoped>
/* Scoped styles with CSS variables */
.component {
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--border-radius);
  transition: var(--transition);
}

.component:hover {
  border-color: var(--color-primary);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}
</style>
```

### Animation Implementation
```css
/* Entrance animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: scale(0.3);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
  70% {
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Hover animations */
.hover-lift {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

/* Loading animations */
.pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.spin {
  animation: spin 1s linear infinite;
}
```

### CSS Variables Usage
```css
/* Always use CSS variables for consistency */
.component {
  /* Colors */
  color: var(--color-text);
  background: var(--color-background);
  border-color: var(--color-border);
  
  /* Typography */
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-medium);
  
  /* Spacing */
  padding: var(--spacing-4);
  margin: var(--spacing-6);
  
  /* Layout */
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-md);
  
  /* Transitions */
  transition: var(--transition);
}
```

## Component Categories & Requirements

### 1. Layout Components
- **AppLayout**: Main application wrapper
- **Container**: Responsive container
- **Grid/Flex**: Layout utilities
- **AppSection**: Content sections

### 2. Form Components
- **InputText**: Text inputs with validation
- **Select**: Dropdown selections
- **Checkbox/Radio**: Selection controls
- **DateRangePicker**: Date selection
- **Autocomplete**: Auto-completing inputs

### 3. Data Display
- **DataTable**: Advanced tables
- **DataGrid**: Grid layouts
- **Chart**: Data visualization
- **VirtualList**: Large datasets

### 4. Navigation
- **Sidebar**: Main navigation
- **Navbar**: Top navigation
- **Breadcrumb**: Page navigation
- **Pagination**: Page controls

### 5. Feedback
- **Alert**: Notifications
- **Toast**: Temporary messages
- **ProgressBar**: Loading indicators
- **Skeleton**: Loading states

### 6. Interactive
- **Modal**: Dialog windows
- **Drawer**: Slide-out panels
- **Accordion**: Collapsible content
- **Tabs**: Tabbed interfaces

## Accessibility Requirements

### ARIA Support
- Use semantic HTML elements
- Add proper ARIA labels and roles
- Implement keyboard navigation
- Support screen readers

### Color & Contrast
- Maintain WCAG AA/AAA compliance
- Use non-color-dependent indicators
- Support high contrast modes

### Motion & Animation
- Respect `prefers-reduced-motion`
- Provide alternative interactions
- Ensure animations don't cause seizures

## Performance Guidelines

### Component Optimization
- Use `v-memo` for expensive renders
- Implement proper `key` attributes
- Avoid unnecessary re-renders
- Use `shallowRef` for large objects

### Bundle Optimization
- Lazy load heavy components
- Use dynamic imports
- Implement code splitting
- Minimize bundle size

### Runtime Performance
- Debounce user inputs
- Use virtual scrolling for large lists
- Optimize animations with `will-change`
- Implement proper cleanup

## Testing & Quality

### Code Quality
- Follow ESLint rules
- Use Prettier formatting
- Implement TypeScript (optional)
- Write meaningful tests

### Browser Support
- Modern browsers (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
- Progressive enhancement
- Graceful degradation
- Feature detection

## Development Workflow

### 1. Component Creation
1. Create component file with proper structure
2. Implement props with validation
3. Add proper TypeScript types (if using)
4. Write comprehensive tests
5. Add to documentation

### 2. Animation Implementation
1. Define animation keyframes
2. Use CSS variables for timing
3. Implement entrance/exit animations
4. Add hover/focus states
5. Test with reduced motion

### 3. Performance Optimization
1. Analyze bundle size
2. Implement lazy loading
3. Optimize re-renders
4. Add performance monitoring
5. Test on low-end devices

### 4. Accessibility Testing
1. Test with screen readers
2. Verify keyboard navigation
3. Check color contrast
4. Validate ARIA attributes
5. Test with assistive technologies

## Common Patterns

### Event Handling
```javascript
// Debounced search
const debouncedSearch = debounce((query) => {
  emit('search', query)
}, 300)

// Throttled scroll
const throttledScroll = throttle((event) => {
  handleScroll(event)
}, 16)
```

### State Management
```javascript
// Reactive state
const isOpen = ref(false)
const selectedItems = ref([])

// Computed properties
const filteredItems = computed(() => {
  return items.value.filter(item => 
    item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Watchers
watch(searchQuery, (newQuery) => {
  if (newQuery.length >= minChars) {
    performSearch(newQuery)
  }
})
```

### Animation Integration
```javascript
// Transition hooks
const onEnter = (el) => {
  el.style.transform = 'translateY(-10px) scale(0.95)'
  el.style.opacity = '0'
  
  requestAnimationFrame(() => {
    el.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
    el.style.transform = 'translateY(0) scale(1)'
    el.style.opacity = '1'
  })
}
```

## Error Handling

### Component Errors
- Implement proper error boundaries
- Show user-friendly error messages
- Log errors for debugging
- Provide fallback UI

### API Errors
- Handle network failures gracefully
- Show appropriate error states
- Implement retry mechanisms
- Cache successful responses

## Documentation Standards

### Component Documentation
- Clear prop descriptions
- Usage examples
- Event documentation
- Slot documentation
- Accessibility notes

### Code Comments
- Explain complex logic
- Document performance considerations
- Note browser compatibility
- Reference related components

## Final Notes

Remember to:
- Always prioritize user experience
- Maintain brand consistency
- Implement proper accessibility
- Optimize for performance
- Write clean, maintainable code
- Test thoroughly across devices
- Document everything clearly
- Follow Vue.js best practices
- Use modern JavaScript features
- Implement proper error handling

This prompt should guide you in creating high-quality, performant, and accessible components that align with the DocRatech brand and technical requirements. 