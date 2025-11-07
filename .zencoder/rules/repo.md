# Repository Guide

## Framework & Runtime
- **Framework**: Laravel (PHP)
- **Primary Language**: PHP with Blade templates
- **Frontend Stack**: Bootstrap-based styling with bespoke CSS tweaks and jQuery

## Conventions
- **Views**: Located under `resources/views`, organized by pages/components
- **Blade Templates**: Use compact, readable markup with localization-ready text
- **Styling**: Prefer scoped styles inside Blade views; leverage Bootstrap utility classes where possible

## Testing & Validation
- **Browser Testing**: Essential for UI tweaks—confirm layout in Chrome/Edge
- **Responsiveness**: Ensure components remain functional under 125% zoom and on ≤576px widths

## Key Scripts
- **Artisan**: `php artisan serve` for local development server
- **NPM**: `npm run dev` for asset compilation when SCSS/JS changes occur

## Tips
- **SweetAlert** overrides live in the relevant Blade files—keep selectors specific
- **Modal Layouts**: Maintain accessibility by ensuring buttons remain reachable and tabbable
- **Version Control**: Commit granular changes with descriptive messages reflecting UI adjustments