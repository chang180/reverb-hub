---
paths:
  - '.github/workflows/**'
---

# Workflows

## CI uses PHP 8.4 and SQLite
composer.lock resolves Symfony 8.1, which requires PHP >= 8.4.1. GitHub Actions must use PHP 8.4 to match the Dockerfile. composer setup copies .env.example (Docker MySQL at DB_HOST=mysql), so CI must override DB_CONNECTION=sqlite and array/sync stores so migrate and later checks run without Docker services.
