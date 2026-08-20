---
paths:
  - 'resources/views/components/hub-pulse.blade.php'
  - 'resources/views/vendor/pulse/**'
  - 'tests/Feature/PulseAuthorizationTest.php'
---

# Pulse dashboard layout

Do not wrap the published Pulse dashboard in `<x-pulse>`. Laravel Pulse registers an anonymous component path under the `pulse` prefix, so `<x-pulse>` resolves to the package layout (`vendor/laravel/pulse/.../components/pulse.blade.php`), not `resources/views/components/pulse.blade.php`.

Use a distinctly named app component such as `<x-hub-pulse>` (file: `resources/views/components/hub-pulse.blade.php`) from `resources/views/vendor/pulse/dashboard.blade.php` when adding Hub chrome (for example a Dashboard back link).
