/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/js/**/*.{vue,js,ts,jsx,tsx}',
    './resources/views/**/*.blade.php',
    './app/**/*.php',
    './config/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        // DocraTech Brand Colors (Light Mode Only - from docratech_project_docs.md)
        primary: '#5A1188',
        primaryAccent: '#9D38CF', 
        darkPurple: '#2A183D',
        softPurple: '#6D488D',
        white: '#ffffff',
        grayLight: '#E5E7EB',
        
        // Extended brand palette for UI components
        docratech: {
          primary: '#5A1188',
          primaryAccent: '#9D38CF',
          darkPurple: '#2A183D',
          softPurple: '#6D488D',
          white: '#ffffff',
          grayLight: '#E5E7EB',
          
          // Light mode surface colors
          background: '#F8F8FC',
          surface: '#FFFFFF',
          card: '#FFFFFF',
          border: '#E5E7EB',
          borderLight: '#F3F4F6',
          
          // Text colors for light mode
          text: '#181124',
          textSecondary: '#6B7280',
          textMuted: '#9CA3AF',
          textTertiary: '#D1D5DB',
          
          // Additional theme colors
          secondary: '#6D488D',
          primaryLight: '#9D38CF',
          primaryDark: '#2A183D',
          accent: '#2A183D',
          
          // Status colors
          success: '#059669',
          warning: '#D97706',
          error: '#DC2626',
          info: '#2563EB',
          
          // Interactive states
          hover: '#F9FAFB',
          focus: '#F3F4F6',
          active: '#E5E7EB'
        }
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
      },
      fontSize: {
        '2xs': ['0.625rem', { lineHeight: '0.75rem' }],
        'xs': ['0.75rem', { lineHeight: '1rem' }],
        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
        'base': ['1rem', { lineHeight: '1.5rem' }],
        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
        '5xl': ['3rem', { lineHeight: '1' }],
        '6xl': ['3.75rem', { lineHeight: '1' }],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
      },
      borderRadius: {
        '4xl': '2rem',
        '5xl': '2.5rem',
      },
      boxShadow: {
        'soft': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
        'medium': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'large': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        'glow': '0 0 20px rgba(157, 56, 207, 0.15)',
        'glow-lg': '0 0 40px rgba(157, 56, 207, 0.2)',
        'inner': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'fade-in-up': 'fadeInUp 0.5s ease-out',
        'slide-in-up': 'slideInUp 0.3s ease-out',
        'slide-in-down': 'slideInDown 0.3s ease-out',
        'slide-in-left': 'slideInLeft 0.3s ease-out',
        'slide-in-right': 'slideInRight 0.3s ease-out',
        'bounce-in': 'bounceIn 0.6s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'spin-slow': 'spin 3s linear infinite',
        'shake': 'shake 0.5s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideInUp: {
          '0%': { opacity: '0', transform: 'translateY(100%)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideInDown: {
          '0%': { opacity: '0', transform: 'translateY(-100%)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideInLeft: {
          '0%': { opacity: '0', transform: 'translateX(-100%)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
        slideInRight: {
          '0%': { opacity: '0', transform: 'translateX(100%)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
        bounceIn: {
          '0%': { opacity: '0', transform: 'scale(0.3)' },
          '50%': { opacity: '1', transform: 'scale(1.05)' },
          '70%': { transform: 'scale(0.9)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
        scaleIn: {
          '0%': { opacity: '0', transform: 'scale(0.9)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
        shake: {
          '0%, 100%': { transform: 'translateX(0)' },
          '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-2px)' },
          '20%, 40%, 60%, 80%': { transform: 'translateX(2px)' },
        },
      },
      backdropBlur: {
        xs: '2px',
      },
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
        '100': '100',
      },
      transitionTimingFunction: {
        'bounce-out': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    function({ addUtilities, addComponents, theme }) {
      // Custom utilities for DocraTech brand
      const newUtilities = {
        '.text-gradient': {
          background: 'linear-gradient(90deg, #5A1188 0%, #9D38CF 100%)',
          '-webkit-background-clip': 'text',
          '-webkit-text-fill-color': 'transparent',
          'background-clip': 'text',
        },
        '.bg-gradient-primary': {
          background: 'linear-gradient(90deg, #5A1188 0%, #9D38CF 100%)',
        },
        '.bg-gradient-primary-soft': {
          background: 'linear-gradient(90deg, rgba(90, 17, 136, 0.1) 0%, rgba(157, 56, 207, 0.1) 100%)',
        },
        '.hover-lift': {
          'transition': 'all 0.2s ease-in-out',
        },
        '.hover-lift:hover': {
          'transform': 'translateY(-2px)',
          'box-shadow': theme('boxShadow.large'),
        },
        '.interactive': {
          'transition': 'all 0.15s ease-in-out',
          'cursor': 'pointer',
        },
        '.interactive:hover': {
          'transform': 'scale(1.02)',
        },
        '.interactive:active': {
          'transform': 'scale(0.98)',
        },
        '.glass': {
          'background': 'rgba(255, 255, 255, 0.8)',
          'backdrop-filter': 'blur(12px)',
          'border': '1px solid rgba(255, 255, 255, 0.2)',
        },
        '.focus-ring': {
          '@apply focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2': {},
        },
      }
      
      // Custom components for consistent UI
      const newComponents = {
        '.btn': {
          '@apply inline-flex items-center justify-center px-6 py-3 rounded-2xl font-semibold transition-all duration-200 focus-ring': {},
        },
        '.btn-primary': {
          '@apply btn bg-gradient-to-r from-primary to-primaryAccent text-white shadow-md hover:scale-105 hover:shadow-lg': {},
        },
        '.btn-secondary': {
          '@apply btn bg-white text-primary border-2 border-primary hover:bg-primary hover:text-white': {},
        },
        '.btn-ghost': {
          '@apply btn text-primary hover:bg-docratech-hover': {},
        },
        '.card': {
          '@apply bg-white rounded-2xl shadow-soft border border-docratech-borderLight p-6 transition-all duration-200': {},
        },
        '.card-hover': {
          '@apply card hover:shadow-medium hover:-translate-y-1': {},
        },
        '.input': {
          '@apply w-full px-4 py-3 rounded-xl border border-docratech-border bg-white text-docratech-text focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200': {},
        },
        '.badge': {
          '@apply inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold': {},
        },
        '.badge-primary': {
          '@apply badge bg-gradient-to-r from-primary to-primaryAccent text-white': {},
        },
        '.badge-success': {
          '@apply badge bg-docratech-success text-white': {},
        },
        '.badge-warning': {
          '@apply badge bg-docratech-warning text-white': {},
        },
        '.badge-error': {
          '@apply badge bg-docratech-error text-white': {},
        },
      }
      
      addUtilities(newUtilities)
      addComponents(newComponents)
    }
  ],
}
