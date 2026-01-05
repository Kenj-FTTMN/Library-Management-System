# Library Management System - Directory Structure

## Overview
This document describes the restructured directory organization for the Library Management System project.

## Assets Directory Structure

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
├── images/
│   ├── icons/                      # Application icons
│   │   ├── favicon.png
│   │   └── apple-touch-icon.png
│   ├── logos/                      # Logo files
│   │   └── logo.webp
│   ├── placeholders/               # Placeholder images
│   └── template/                   # Template/demo images
│       ├── blog/                   # Blog-related images
│       ├── education/              # Education-related images
│       └── person/                 # Person/avatar images
├── js/
│   ├── main.js                     # Template JavaScript
│   └── library.js                  # Library system JavaScript
├── scss/                           # SCSS source files (if used)
└── vendor/                         # Third-party libraries
    ├── bootstrap/
    ├── bootstrap-icons/
    ├── aos/
    ├── swiper/
    └── ... (other vendors)
```

## Database Directory Structure

```
database/
├── backups/                        # Database backup files
├── migrations/                     # Database migration files
│   └── librarysystem.sql          # Main database schema
└── seeds/                          # Database seed files (for test data)
```

## Key Changes

### Assets Reorganization
1. **Images**: Moved from `assets/img/` to `assets/images/` with better categorization:
   - `icons/` - Application icons (favicon, apple-touch-icon)
   - `logos/` - Logo files
   - `placeholders/` - Placeholder images
   - `template/` - Template/demo images (blog, education, person)

2. **CSS**: Already well-organized with components and pages separation

3. **JavaScript**: Organized with main.js (template) and library.js (system-specific)

### Database Reorganization
1. **Migrations**: Main SQL file moved to `database/migrations/`
2. **Backups**: Created `database/backups/` for backup files
3. **Seeds**: Created `database/seeds/` for seed/test data files

## Path Configuration

The `ASSETS_PATH` constant in `config/config.php` is now dynamically determined from `SITE_URL`, making it easier to deploy to different environments.

## File References Updated

- `includes/head.php` - Updated favicon paths
- `includes/header.php` - Updated logo path (commented, ready for use)
- `config/config.php` - Dynamic ASSETS_PATH calculation

## Benefits

1. **Better Organization**: Images are categorized by purpose
2. **Scalability**: Easy to add new image categories or database files
3. **Maintainability**: Clear separation of concerns
4. **Deployment**: Dynamic path configuration works across environments
5. **Backup Management**: Dedicated folder for database backups

## Migration Notes

- Old `assets/img/` directory has been reorganized into `assets/images/`
- Database SQL file moved to `database/migrations/`
- All file references have been updated automatically
- No manual changes required in existing code


