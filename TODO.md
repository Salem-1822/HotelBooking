git pull# Single-Language English Conversion TODO

## Phase 1 - UI Stabilization (In Progress)

[ ] 1. layouts/app.blade.php - Remove lang switchers, static nav, fix lang/dir
[ ] 2. login.blade.php - Static EN labels  
[ ] 3. settings.blade.php - Hardcode EN options, remove RTL/lang
[ ] 4. SetLocale.php - Force app()->setLocale('en')
[ ] 5. web.php - Comment lang.switch route
[ ] 6. Update remaining views (dashboard, hotels, cities, admins, reservations)
[ ] 7. php artisan optimize:clear && test

## Phase 2 - Backend Cleanup

[ ] Remove LocaleController.php
[ ] Remove SetLocale middleware from Kernel
[ ] Delete lang/ dirs (optional)
[ ] Final validation

**Status: Starting Phase 1**
