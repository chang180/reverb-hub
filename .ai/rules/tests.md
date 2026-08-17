---
paths:
  - 'tests/**'
---

# Tests

## Write new tests in Pest
This application uses Pest 4 for new tests (pestphp/pest + pest-plugin-laravel). Existing PHPUnit classes remain valid. Feature Pest files inherit Tests\TestCase and LazilyRefreshDatabase via tests/Pest.php. Prefer Livewire::test(), Http::fake(), and model assertions over hitting real Reverb or asserting raw tables.
