# Library Management System - Modular CSS Structure

## Overview
All styles have been extracted from PHP files and organized into a modular CSS structure for better maintainability and reusability.

## Directory Structure

```
assets/
├── css/
│   ├── main.css                    # Original template CSS
│   ├── library.css                 # Main library CSS (imports all modules)
│   ├── components/                 # Reusable component styles
│   │   ├── animations.css          # Animation keyframes and utilities
│   │   ├── dashboard.css           # Dashboard card and stat styles
│   │   ├── forms.css               # Form component styles
│   │   └── tables.css              # Table component styles
│   └── pages/                      # Page-specific styles
│       ├── login.css               # Login page styles
│       └── dashboard.css           # Dashboard page styles
└── js/
    └── library.js                  # Modular JavaScript functions
```

## CSS Files

### Main Files

#### `library.css`
- Main entry point that imports all component and page CSS files
- Contains global utility classes
- Responsive utilities

#### `components/animations.css`
- `@keyframes bounceIn` - Bounce in animation
- `@keyframes fadeInUp` - Fade in from bottom
- `@keyframes fadeIn` - Simple fade in
- `@keyframes slideIn` - Slide in from left
- Animation utility classes

#### `components/dashboard.css`
- `.dashboard-card` - Dashboard card styling
- `.counter` - Counter number styling
- `.feature-icon` - Icon styling
- Hover effects and transitions

#### `components/forms.css`
- `.form-card` - Form card styling
- Form input focus states
- Modal styling
- Button enhancements

#### `components/tables.css`
- `.data-table` - Table styling
- Table hover effects
- Badge styling
- Empty state styling

#### `pages/login.css`
- Login page specific styles
- Login card styling
- Role info styling
- Button styling

#### `pages/dashboard.css`
- Dashboard welcome cards
- Quick actions styling
- Recent borrows card
- Stat icon colors
- Overdue badge animation

## JavaScript Files

### `library.js`
- Modular JavaScript functions
- `CounterAnimation` - Counter animation module
- `FormValidation` - Form validation helper
- Auto-initializes on DOM ready

## Usage

### In PHP Files
All PHP files now use the centralized CSS through `includes/head.php`:

```php
<?php include 'includes/head.php'; ?>
```

The head.php file automatically includes:
- Vendor CSS files
- main.css (template CSS)
- library.css (which imports all modules)

### JavaScript
All JavaScript is loaded through `includes/scripts.php`:

```php
<?php include 'includes/scripts.php'; ?>
```

This includes:
- Vendor JS files
- main.js (template JS)
- library.js (library system JS)

## Benefits

1. **Modularity**: Styles are separated by component/functionality
2. **Reusability**: Components can be reused across pages
3. **Maintainability**: Easy to find and update specific styles
4. **Performance**: CSS is cached by browser
5. **Separation of Concerns**: PHP files focus on logic, CSS handles styling
6. **Scalability**: Easy to add new components or pages

## Migration Notes

- All `<style>` tags have been removed from PHP files
- All inline JavaScript has been moved to `library.js`
- Page-specific styles are in `pages/` directory
- Component styles are in `components/` directory
- Login page now uses standard includes instead of standalone HTML

