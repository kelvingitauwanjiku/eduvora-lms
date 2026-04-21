# EnhancedTopBar Component

A modern, feature-rich top navigation bar for Eduvora. Inspired by Academy-LMS but elevated with contemporary design patterns.

## Features

### 1. Contact Information Display
- **Phone**: Clickable `tel:` link with icon
- **Email**: Clickable `mailto:` link with icon
- Responsive: email hidden on mobile, phone always visible
- Hover effects with color transitions

### 2. Language Selector
- Desktop: Dropdown with flag emojis and native language names
- Mobile: Grid of clickable language buttons
- Persists selection in `localStorage`
- Emits `language-change` event for parent components
- Keyboard accessible with ARIA attributes

### 3. Social Media Links
- Configurable via `socialLinks` prop
- Icons as render functions (no external icon library needed)
- Hover scale & color animations
- Accessible with `aria-label`

### 4. Mobile Experience
- Slide-in panel with smooth animations
- Contains all contact info, language selector, and social links
- Touch-optimized button sizes
- Dismissible via backdrop click

### 5. Modern Design
- **Glassmorphism**: `backdrop-blur-md` with semi-transparent background
- **Gradient accents**: Blue-indigo color scheme
- **Subtle borders**: Low-contrast borders for depth
- **Smooth transitions**: All state changes animated (200-300ms)
- **Adaptive to dark mode**: Full dark theme support

## Usage

### Basic Implementation

```vue
<template>
  <EnhancedTopBar
    phone="+1 555 123 4567"
    email="support@eduvora.com"
    @language-change="handleLanguageChange"
  />
</template>

<script setup>
import EnhancedTopBar from '../components/layout/EnhancedTopBar.vue';

function handleLanguageChange(langCode) {
  console.log('Language changed to:', langCode);
  // Integrate with i18n or API
}
</script>
```

### With Custom Configuration

```vue
<template>
  <EnhancedTopBar
    :phone="config.phone"
    :email="config.email"
    :languages="config.languages"
    :social-links="config.socialLinks"
    initial-language="es"
    @language-change="changeLanguage"
  />
</template>

<script setup>
import { ref } from 'vue';
import EnhancedTopBar from '../components/layout/EnhancedTopBar.vue';
import { topBarConfig } from '../config/topbar';

const config = ref(topBarConfig);

function changeLanguage(lang) {
  // Save to backend, reload content, etc.
}
</script>
```

### In Layouts

```vue
<!-- resources/js/layouts/DefaultLayout.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <EnhancedTopBar
      phone="+1 234 567 890"
      email="support@eduvora.com"
      @language-change="handleLanguageChange"
    />
    <AppHeader />
    <main><router-view /></main>
    <AppFooter />
  </div>
</template>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `phone` | `String` | `'+1 (555) 123-4567'` | Contact phone number |
| `email` | `String` | `'support@eduvora.com'` | Contact email address |
| `languages` | `Array` | See below | Array of language objects |
| `socialLinks` | `Array` | See below | Array of social link objects |
| `initialLanguage` | `String` | `'en'` | Default language code |

### Language Object Structure

```javascript
{
  code: 'en',          // ISO language code
  name: 'English',     // Display name
  nativeName: 'English',// Native name
  flag: '🇺🇸'           // Emoji flag
}
```

### Social Link Object Structure

```javascript
{
  name: 'Twitter',
  url: 'https://twitter.com/eduvora',
  icon: {
    render: () => h('svg', { ... }, [ ... ])
  }
}
```

**Note**: Icons are Vue render functions. Helper function `getSocialIcon()` provided in `config/topbar.js`.

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `language-change` | `String` (langCode) | Emitted when user selects a new language |

## Exposed Methods

Using `ref` on component:

```vue
<template>
  <EnhancedTopBar ref="topBarRef" />
</template>

<script setup>
import { ref } from 'vue';

const topBarRef = ref(null);

// Access current language
console.log(topBarRef.value.currentLanguage);

// Programmatically change language
topBarRef.value.selectLanguage('fr');
</script>
```

## Styling & Customization

### Tailwind Classes
The component uses utility-first Tailwind classes directly. To customize:

1. **Colors**: Edit gradient in `bg-gradient-to-r from-blue-600 to-indigo-600`
2. **Height**: Adjust `h-12` (collapsed) and `h-auto` (expanded) classes
3. **Blur intensity**: Modify `backdrop-blur-md` (try `blur-sm`, `blur-lg`, etc.)
4. **Border**: Change `border-gray-200/50` to suit your theme

### Dark Mode
All classes include `dark:` variants. Ensure your `tailwind.config.js` has `darkMode: 'class'` and your app wraps content with `dark` class on `<html>` or `<body>`.

### Custom Icons
Replace social link icons by passing your own render functions:

```javascript
const customIcons = {
  twitter: {
    render: () => h('svg', { viewBox: '0 0 24 24' }, [
      h('path', { d: '...' })
    ])
  }
};
```

## Responsive Behavior

| Breakpoint | Layout |
|------------|--------|
| `sm` (640px+) | Phone + 1 social icon |
| `md` (768px+) | Phone + Email + 3 social icons |
| `lg` (1024px+) | All features visible |
| `xl` (1280px+) | Spacious layout |

- **< 1024px**: Mobile menu button appears (slide-in panel)
- **≥ 1024px**: Full desktop layout

## Future Enhancements (Roadmap)

1. **Scroll Collapse**: Shrink to compact mode on scroll down
2. **Online Indicator**: Animated green dot when support is online
3. **Address Display**: Optional address field from config
4. **A/B Testing**: Feature flag to test different layouts
5. **Backend Config**: Fetch settings from API endpoint
6. **i18n Integration**: Full Vue i18n support for language switching
7. **Search Bar**: Direct search input in topbar (desktop)
8. **Notification Badge**: Unread count for notifications

## Performance Considerations

- **No external dependencies**: Uses Vue built-ins only
- **Tree-shakeable**: Icons only included if used
- **Minimal reactivity**: Only `isLangOpen` and `showMobileMenu` are reactive refs
- **Lazy imports**: Languages can be fetched async if needed

## Accessibility

- `aria-haspopup`, `aria-expanded` on language dropdown
- `aria-label` on icon-only buttons
- Keyboard navigation supported
- Focus styles (via Tailwind `focus:` utilities)
- Color contrast meets WCAG AA standards

## Troubleshooting

### Icons not showing
Ensure Vue 3's `h` function is imported: `import { h } from 'vue'`

### Language selector stays open on selection
Check that `@click.stop` is on dropdown menu container to prevent bubbling.

### Dark mode not applying
Confirm your app has `dark` class on root element and `tailwind.config.js` has `darkMode: 'class'`.

### Mobile menu not closing
The `@click.self` on panel backdrop should close it. If not, check event propagation.

## Related Components

- `AppHeader.vue` - Main site header (logo, nav, search, user menu)
- `PublicTopBar.vue` - Legacy component (can be removed after migration)
- `DefaultLayout.vue` - Layout wrapper using EnhancedTopBar

## Changelog

**v1.0.0** (Current)
- Initial release
- Glassmorphic design
- Language selector with 7 languages
- 5 social platforms (Twitter, LinkedIn, Facebook, Instagram, YouTube)
- Fully responsive mobile menu
- Dark mode support
