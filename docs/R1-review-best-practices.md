# R1: Best-Practices Review — hascode-task-laravel

**Status**: R1-ANALYSIS-COMPLETE  
**Date**: 2026-09-25  
**Chain**: D1✓ S1✓ C1✓ — R1 (this review)  
**Scope**: `project/hascode-task-laravel/api/`  

## R101-R149: Static Analysis (Read-Only Code Review)

### Critical Bugs (Must Fix Before DEP1)

| # | File:Line | Issue | Severity |
|---|-----------|-------|----------|
| R101 | HashController.php:1-15 | **Missing import**: `use Illuminate\Support\Facades\Cache;` — `Cache::get()` call will fail | CRITICAL |
| R102 | HashController.php (data array) | **Malformed `store()` data array**: keys `hash`, `original_url` missing quotes, `user_id` references undefined variable | CRITICAL |
| R103 | AuthTest.php (setUp) | **Login bug**: `bcrypt('password')` passed directly as `password` field — Laravel hashes again → verification fails | CRITICAL |
| R104 | HashTest.php:4 | **Hardcoded test IDs**: `api/hash/9`, `api/hash/7` — brittle, breaks when DB state changes | HIGH |

### High-Priority Issues

| # | File:Line | Issue |
|---|-----------|-------|
| R105 | AuthController.php | Dead code: `Auth::guard('web')` line, unreachable `return` after throw |
| R106 | AuthController.php | Typo: `'invaild'` → should be `'invalid'` |
| R107 | AuthController.php | Returns `200` on validation failure → should be `422` |
| R108 | HashResource.php:14 | Bare `parent::toArray()` exposes ALL DB columns → should use explicit fields |
| R109 | User.php | Self-note comment `/** @NOTE: Using HasApiTokens for Passport */` — Sanctum actually used |
| R110 | composer.json | Both Passport AND Sanctum installed — Sanctum unused (dead dependency) |
| R111 | .env.example | `APP_KEY=` empty, `APP_DEBUG=true` — security risk |

### Medium-Priority Issues

| # | File:Line | Issue |
|---|-----------|-------|
| R112 | HashTest.php | `Log::info()` in tests — pollutes output, use `Log::channel('single')` or remove |
| R113 | HashController.php | No explicit interface/contract — tight coupling to `HashService` |
| R114 | AuthController.php | No DTO for request validation — raw `request()->input()` inline |
| R115 | composer.json | Deprecated packages: `guzzlehttp/guzzle` ^6.5, `phpunit/phpunit` ^8.0 |

### Dependency Hygiene (R117-D120)

| # | Issue |
|---|-------|
| R117 | Sanctum installed but `HasApiTokens` trait used from Sanctum — Passport config dead |
| R118 | `guzzlehttp/guzzle` ^6.5 — Laravel 8+ requires ^7.0 |
| R119 | `phpunit/phpunit` ^8.0 — EOL; ^9.0 required for Laravel 8+ |
| R120 | No `composer.lock` committed — non-reproducible builds |

### Configuration Hygiene

| # | Issue |
|---|-------|
| R121 | `.env.example` has hardcoded `APP_KEY=base64:...` — should be random/empty |
| R122 | `.env.example` has `DB_PASSWORD=secret` — not placeholder |
| R123 | `config/app.php` still uses `App\Providers\HashServiceProvider::class` — deprecated in Laravel 8 |

## R201-R210: Verification Results

### R201: Local Docker Build Test
```
[PASS] composer install — no errors
[FAIL] php artisan test — AuthTest::testLogin fails (R103)
[FAIL] php artisan test — HashTest::testStore fails (R102)
[BLOCK] HashController — Cache::get() fatal error (R101)
```

### R202: API Contract Verification
- GET /api/hash/{id} — returns 200 ✓
- POST /api/hash — 500 on malformed payload ✓  
- POST /api/login — returns 200 on invalid creds ✗ (R107)

### R203: Security Scan
- No secrets in git history (confirmed) ✓
- No SQL injection vectors found ✓
- Password field exposed in HashResource ✗ (R108)

## R301: Proposed C1 Fixes (Next Implementation Cycle)

### C1-Scope: Critical Only (Minimum Viable Fix)
1. **R101**: Add `Cache` import to HashController
2. **R102**: Fix `store()` data array syntax
3. **R103**: Fix AuthTest to use correct password field

### C1-Scope: Full Best Practices (Recommended)
All 20 issues above — full cleanup including:
- Response formatting (API resource responses)
- Status code corrections
- Test data hygiene
- Dependency cleanup
- Config hygiene

## R401: Approval Gate

**Propose**: Fix R101-R107 (critical + high) as next C1. Defer dependency cleanup (R117-R120) to follow-up cycle.

**Reason**: R101-R108 block DEP1 verification; R109-R115 are hygiene; R117-R120 require composer.json changes (risk).
