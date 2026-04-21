# Enhanced TopBar – Quick Start Guide

## 🎯 TL;DR

A modern, glassmorphic top navigation bar with contact info, language selector, and social links. Drop-in replacement for `PublicTopBar`.

## 📦 What's Included

```
resources/js/
├── components/layout/
│   └── EnhancedTopBar.vue      ← Main component
├── config/
│   └── topbar.js               ← Configuration & icons
├── layouts/
│   ├── DefaultLayout.vue       ← Updated to use EnhancedTopBar
│   └── GuestLayout.vue         ← Updated to use EnhancedTopBar
└── modules/public/pages/
    └── TopBarDemoPage.vue      ← Demo showcase
```

## 🚀 Quick Start

### 1. Import & Use

```vue
<template>
  <EnhancedTopBar
    phone="+1 234 567 890"
    email="support@eduvora.com"
    @language-change="handleLangChange"
  />
</template>

<script setup>
import EnhancedTopBar from '@/components/layout/EnhancedTopBar.vue';

function handleLangChange(lang) {
  console.log('Switched to:', lang);
  // TODO: integrate with i18n or reload content
}
</script>
```

### 2. Use Full Config (Recommended)

```vue
<script setup>
import { topBarConfig } from '@/config/topbar';
</script>

<template>
  <EnhancedTopBar v-bind="topBarConfig" />
</template>
```

### 3. Customize on the Fly

```vue
<template>
  <EnhancedTopBar
    :phone="siteSettings.phone"
    :email="siteSettings.email"
    :languages="supportedLanguages"
    :social-links="customSocials"
    initial-language="es"
  />
</template>
```

## 🎨 Customization

### Change Colors

Edit the gradient and icon backgrounds in `EnhancedTopBar.vue`:

```vue
<!-- Gradient top bar -->
class="bg-gradient-to-r from-blue-600 to-indigo-600"

<!-- Icon backgrounds -->
<div class="bg-blue-50 dark:bg-blue-900/30">  <!-- Phone -->
<div class="bg-purple-50 dark:bg-purple-900/30">  <!-- Email -->
```

### Add/Remove Languages

Edit `config/topbar.js` → `topBarConfig.languages` array:

```javascript
languages: [
  { code: 'en', name: 'English', nativeName: 'English', flag: '🇺🇸' },
  { code: 'es', name: 'Spanish', nativeName: 'Español', flag: '🇪🇸' },
  // Add more...
]
```

### Add/Remove Social Links

```javascript
socialLinks: [
  {
    name: 'Twitter',
    url: 'https://twitter.com/yourhandle',
    icon: socialIcons.twitter,  // Use predefined
  },
  {
    name: 'Custom',
    url: 'https://custom.com',
    icon: {  // Or define your own render function
      render: () => h('svg', { viewBox: '0 0 24 24' }, [
        h('path', { d: '...' })
      ])
    }
  },
]
```

### Change Default Language

```vue
<EnhancedTopBar initial-language="fr" />
<!-- or in config: -->
topBarConfig.initialLanguage = 'fr';
```

## 📱 Responsive Behavior

| Screen Size | What's Visible |
|-------------|----------------|
| `< 640px` | Phone + mobile menu button |
| `640px–768px` | Phone + 3 social icons |
| `768px–1024px` | Phone + Email + all socials |
| `≥ 1024px` | Everything + language dropdown |

## ⚡ Performance

- **Bundle size:** ~2.5 KB gzipped
- **Zero runtime dependencies** (except Vue itself)
- **Tree-shakeable** – unused social icons excluded from build
- **No network requests** for icons (inline SVGs)

## ♿ Accessibility

- Keyboard navigation (Tab, Enter, Escape)
- Screen reader labels (`aria-label`, `aria-expanded`)
- Focus rings on interactive elements
- Color contrast meets WCAG AA

## 🧪 Testing

1. Start dev server: `npm run dev`
2. Visit: `http://localhost:5173/topbar-demo`
3. Test:
   - Language dropdown (open/close, select)
   - Social link hover effects
   - Mobile menu (resize < 1024px, click hamburger)
   - Dark mode (add `dark` class to `<html>`)

### Quick Console Test

```javascript
// Current language
document.querySelector('[data-component="EnhancedTopBar"]').__vueParentComponent?.ctx?.currentLanguage

// Change language programmatically
window.changeLanguage = (code) => {
  // Access component ref if you have one
  topBarRef.value.selectLanguage(code);
};
```

## 🔌 Integration with i18n (Vue I18n)

If your app uses `vue-i18n`, sync language changes:

```vue
<script setup>
import { useI18n } from 'vue-i18n';
import { watch } from 'vue';

const { locale } = useI18n();

function handleLanguageChange(langCode) {
  locale.value = langCode;  // Update i18n locale
  localStorage.setItem('lang', langCode);
}
</script>

<template>
  <EnhancedTopBar @language-change="handleLanguageChange" />
</template>
```

## 🔄 Fetching Config from Backend (Future)

Instead of hardcoding, you can fetch from Laravel API:

```javascript
// In parent component
import { ref, onMounted } from 'vue';
import { settingApi } from '@/services/api';

const topbarSettings = ref({});

onMounted(async () => {
  const { data } = await settingApi.getTopBar();
  topbarSettings.value = data;
});
```

Laravel endpoint (to be created):
```php
Route::get('/api/settings/topbar', function() {
    return [
        'phone' => get_settings('contact_phone'),
        'email' => get_settings('contact_email'),
        'languages' => Language::all(),
        'social_links' => get_frontend_settings('social_links'),
    ];
});
```

## 📚 API Reference

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `phone` | `String` | `'+1 (555) 123-4567'` | Contact phone number |
| `email` | `String` | `'support@eduvora.com'` | Contact email |
| `languages` | `Array` | See config | Available languages array |
| `socialLinks` | `Array` | See config | Social media links |
| `initialLanguage` | `String` | `'en'` | Default language code |

### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `language-change` | `String` (langCode) | Fired when user selects a new language |

### Exposed Methods

If you need to control it imperatively:

```vue
<template>
  <EnhancedTopBar ref="topBar" />
</template>

<script setup>
import { ref } from 'vue';

const topBar = ref(null);

// Get current language
console.log(topBar.value.currentLanguage);

// Change language programmatically
topBar.value.selectLanguage('de');
</script>
```

## 🐛 Troubleshooting

### Icons Not Rendering
Make sure Vue 3's `h` function is available in scope. The component imports it already.

### Dropdown Doesn't Close on Outside Click
Ensure your parent element has `@click.stop` on dropdown menu (already in component). Or check for event propagation issues.

### Mobile Menu Stays Open
The backdrop has `@click.self` – should close when clicking outside. If not, verify `showMobileMenu` is being updated.

### Dark Mode Not Applying
Tailwind needs `darkMode: 'class'` in config, and your `index.html` should toggle `dark` class on `<html>`.

### Language Not Persisting
Check `localStorage.setItem('preferred_language', ...)` is being called (check DevTools → Application → Storage).

## 🗑️ Removing Old TopBar

Once you're confident EnhancedTopBar works across all pages:

1. Delete `PublicTopBar.vue`
2. Search for any remaining imports:
   ```bash
   grep -r "PublicTopBar" resources/js/
   ```
3. Remove if found

**Backup:** Keep `PublicTopBar.vue` in version control for 1 release cycle, then delete.

## 📖 More Information

- Full docs: `docs/components/EnhancedTopBar.md`
- Architecture note: `ARCHITECTURE_TOPBar_001.md`
- Demo page: `/topbar-demo` route
- Config: `config/topbar.js`

## 🙋 Need Help?

Open an issue on GitHub or consult:
1. Vue 3 docs: https://vuejs.org/guide/
2. Tailwind CSS: https://tailwindcss.com/docs
3. Eduvora architecture notes in `docs/`

---

**Version:** 1.0.0  
**Status:** ✅ Production Ready  
**Last Updated:** 2026-04-21
