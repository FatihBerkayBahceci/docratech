
<!-- 
Developer: DocraTech Team - Fatih Berkay Bahceci
Website: https://docratech.com
Language & Locale: American English (US)
Region Focus: Designed for the United States Healthcare Market 
-->

# 🟣 DocraTech Medical WebSite Builder – Project Definition

DocraTech is an end-to-end SaaS platform built specifically for the U.S. healthcare market, empowering doctors and clinics to create professional websites and manage patient processes without technical expertise. It offers a modern alternative to WordPress with features like content management, SEO automation, advanced appointment tools, and a modular license/theme system.

---

## ⚙️ Tech Stack

- **Backend:** Laravel 12 (PHP 8.3+)
  - RESTful API + Sanctum
  - Modular architecture (Service Layer + Repository)
- **Frontend:** Vue.js 3 + Tailwind CSS
  - Vue Router (Lazy loading)
  - Pinia for global state
  - ✅ **Now uses TailwindCSS** (previously Bootstrap-based)
  - Vue Transitions & IntersectionObserver for animations
- **Database:** MySQL 8+
- **Storage & Email:** SMTP (queued) + public media storage (`/storage/app/public/media`)
- **Build Tool:** Vite

---

## 📁 Project Structure

```plaintext
docratech/
├── app/
├── database/
├── routes/
├── public/
│   └── media/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   └── router/
│   ├── css/
│   │   └── main.css
│   └── index.html
├── storage/
├── .env
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🛠️ Development Principles

- Fully SPA-based (Single Page Application)
- All interactions via **AJAX + async/await**
- Extensive **micro animations** for transitions, buttons, modals, and feedback
- Vue 3 + TailwindCSS as the standard UI stack
- Components follow Atomic Design and are scoped
- Frontend & Backend validation for all forms
- Accessibility: WCAG 2.1 AA compliance
- Responsive and mobile-first

---

## 🚀 Startup Commands

```bash
# Backend
composer install
php artisan migrate --seed
php artisan serve

# Frontend
npm install
npm run dev
```

---

## 🎯 SaaS Goal

DocraTech aims to be the most powerful and intuitive SaaS for content & patient management in the U.S. medical sector.

### Key Differentiators:

- More modular and license-based than PatientPop, NexHealth, etc.
- Theme + module marketplace (coming soon)
- Visual builder tools for pages, forms, and widgets
- Full SEO + analytics integration
- Regulatory compliance (HIPAA, KVKK, GDPR)

---

## 🎨 Brand Identity & UI Guidelines (Tailwind Based)

DocraTech blends trusted medical professionalism with smooth modern UI behavior. All components follow a unified design system using **TailwindCSS**.

### 🎨 Color Palette

```js
// tailwind.config.js → theme.extend.colors

{
  primary: '#5A1188',
  primaryAccent: '#9D38CF',
  darkPurple: '#2A183D',
  softPurple: '#6D488D',
  bgDark: '#181124',
  white: '#ffffff',
  grayLight: '#E5E7EB'
}
```

### 🅰️ Typography

- Font: `Inter`, sans-serif
- Headings: `text-4xl`, `text-2xl`, `text-xl`, `text-lg`
- Body: `text-base`, `leading-relaxed`, `tracking-wide`
- Weights: `light`, `regular`, `medium`, `semibold`

### 🧱 UI Components

| Component      | Tailwind Rules |
|----------------|----------------|
| **Button**     | `bg-gradient-to-r from-primary to-primaryAccent`, `rounded-2xl`, `transition`, `hover:scale-105` |
| **Form Input** | `rounded-xl`, `bg-darkPurple`, `text-white`, `placeholder-gray-400`, `focus:ring-primary` |
| **Card**       | `rounded-2xl`, `shadow-lg`, `bg-darkPurple`, `hover:shadow-primaryAccent` |
| **Modal**      | Transition: `scale-95` → `scale-100`, backdrop: `bg-black/50` with blur |
| **Loading**    | `animate-pulse`, `bg-gray-700/40`, spinner with `border-t-primaryAccent` |

---

## ✨ Animations (Micro + Macro)

- **Page Transitions:** `fade-slide`, `scale-fade`
- **Modals:** `blur + scale-in`, `opacity fade-out`
- **Forms:** `shake` on error, `pulse` on success
- **Buttons:** `hover:scale-105`, `active:opacity-80`
- **Skeletons:** `animate-pulse`, `shimmer` placeholder structure

---

## ✅ Consistency & Performance

- Vue 3 Composition API + `<script setup>` format
- Tailwind utility-first styling
- Fully dark mode compatible (`dark:` variant support)
- Lazy loading, dynamic imports, tree-shaking enabled

---

## 🧩 Module Development Order

1. `01_auth_roles` → Authentication & Role Management  
2. `02_profile_management` → Doctor/Clinic Profile Management  
3. `03_page_blog` → CMS: Page & Blog Builder  
4. `04_patients_appointments` → Patient & Appointment Control  
5. `05_clinic_services` → Clinic Services + Staff  
6. `06_forms_media` → Form Builder & Media Manager  
7. `07_settings_menus` → Advanced Settings & Navigation  
8. `08_theme_plugin_system` → Plugin + Theme Engine

---

## 📦 Final Notes

- All components must follow the design system
- TailwindCSS config is centralized
- UI development = **strictly frontend-focused**
- API & backend structure is **not to be modified**

---

> 💡 **Need help creating base components (button, card, modal, etc.) with this new standard? Let me know – I can generate Vue 3 + Tailwind-compatible templates.**
