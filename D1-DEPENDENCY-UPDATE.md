# Dependency Update — D1 Documentation

> Generated: 2026-09-21 | Step: D1
> Source: user request ("all libraries need updating")
> Chain: D1→S1→C1→R1→DEP1 (dependency update sub-chain)
> Parent chain: hascode-task-laravel (D1-DOCUMENTATION COMPLETE, S1 SPEC COMPLETE, C1 BUGFIX COMPLETE)
> Status: D1 COMPLETE (this document) — pending user approval for S1

## 1. Current Dependency State

### 1.1 PHP Dependencies (composer.json + composer.lock)

**Direct require (production):**

| Package | Constraint | Locked | Risk |
|---------|-----------|--------|------|
| php | ^8.0.2 | 8.1.9 (runtime) | Minor |
| guzzlehttp/guzzle | ^7.2 | 7.4.4 | OK (within range) |
| laravel/framework | ^9.11 | v9.17.0 | OK (within range) |
| laravel/passport | ^10.4 | v10.4.1 | OK (within range) |
| laravel/sanctum | ^2.14.1 | v2.15.1 | OK (within range) |
| laravel/tinker | ^2.7 | v2.7.2 | OK (within range) |

**Direct require-dev:**

| Package | Constraint | Locked | Risk |
|---------|-----------|--------|------|
| fakerphp/faker | ^1.9.1 | v1.19.0 | OK |
| laravel/sail | ^1.0.1 | v1.14.9 | OK |
| mockery/mockery | ^1.4.4 | 1.5.0 | OK |
| nunomaduro/collision | ^6.1 | v6.2.0 | OK |
| phpunit/phpunit | ^10.0.0 | **9.5.20** | **CRITICAL — STALE LOCK** |
| spatie/laravel-ignition | ^1.0 | 1.2.4 | OK |

**Transitive (selected notable):**

| Package | Locked | Concern |
|---------|--------|---------|
| symfony/console | v6.1.0 | Laravel 9 pinned, update via Laravel |
| symfony/http-kernel | v6.2.6 | Laravel 9 pinned, update via Laravel |
| league/oauth2-server | 8.5.3 | Passport dep |
| phpseclib/phpseclib | 3.0.19 | Passport dep |
| nesbot/carbon | 2.58.0 | OK |
| vlucas/phpdotenv | v5.4.1 | OK |

### 1.2 JS Dependencies (package.json + package-lock.json)

**Direct devDependencies:**

| Package | Current | ncu Latest | Vulnerability |
|---------|---------|-----------|---------------|
| axios | ^1.0.0 | ^1.20.0 | — |
| laravel-mix | ^6.0.6 | ^6.0.49 | **HIGH (transitive: elliptic via node-libs-browser)** |
| lodash | ^4.17.19 | ^4.18.1 | — |
| postcss | ^8.1.14 | ^8.5.28 | — |

**npm audit (post `npm install`):**

| Package | Severity | Via | Fix Available |
|---------|----------|-----|---------------|
| elliptic | Low | laravel-mix → node-libs-browser → crypto-browserify → browserify-sign | **No** |
| uuid | Moderate | webpack-dev-server → sockjs; webpack-notifier → node-notifier | **No** |
| **Total** | **11 (5 low, 6 moderate)** | | **No automated fix** |

### 1.3 Missing / Incomplete

- **composer.lock**: PHPUnit version mismatch — lock has `9.5.20`, composer.json requires `^10.0.0`. Lock file is stale.
- **package-lock.json**: Originally absent (no lockfile). Created via `npm install` during D1 assessment.
- **No Renovate config** (renovate.json / config)
- **No Dependabot config** (.github/dependabot.yml)
- **No GitHub Actions workflow** for dependency scanning
- **No .gitignore at api/ level** (exists at repo root only)

## 2. Update Plan

### 2.1 npm packages (actionable — update package.json constraints)

| Package | Current | Target | Action |
|---------|---------|--------|--------|
| axios | ^1.0.0 | ^1.20.0 | Update constraint + npm install |
| laravel-mix | ^6.0.6 | ^6.0.49 | Update constraint + npm install (also addresses vulnerability surface) |
| lodash | ^4.17.19 | ^4.18.1 | Update constraint + npm install |
| postcss | ^8.1.14 | ^8.5.28 | Update constraint + npm install |

### 2.2 composer packages (constraint alignment)

- Run `composer update` after correcting phpunit constraint alignment
- Update composer.lock to match composer.json (^10.0.0 for PHPUnit)
- All other packages are within their semver constraints — no explicit version bumps needed

### 2.3 Security (npm audit)

- 11 vulnerabilities (5 low, 6 moderate) — all in laravel-mix transitive dependencies
- No automated fix available (elliptic, uuid — deprecated chains)
- Updating laravel-mix to ^6.0.49 may resolve some (newer version may have updated transitive deps)
- Long-term fix: migrate from laravel-mix to Vite (Laravel's recommended path) — out of scope for this task

## 3. Scope of Work

### 3.1 This chain (dependency update)

- Update package.json constraints to latest (ncu results above)
- Run npm install to regenerate package-lock.json
- Fix composer.lock staleness (PHPUnit version alignment)
- Update composer.lock via composer update (if available)
- Regenerate npm audit report post-update

### 3.2 Explicitly Out of Scope

- Migrating from laravel-mix to Vite (architectural change, separate task)
- Updating Laravel framework version (9.x → 10.x or 11.x) — separate major version migration
- Updating PHP version constraint (^8.0.2 → ^8.2+) — separate task
- Fixing the 11 npm audit vulnerabilities via dependency replacement (no fix available for elliptic/uuid chains)
- Adding Renovate/Dependabot automation (separate task)
- C1 bug fixes (bcrypt, HashTest, hasher swap — already COMPLETE in parent chain)

## 4. Risks

| Risk | Mitigation |
|------|-----------|
| npm package updates introduce breaking changes | Test after each update; constrain to same major |
| composer update may update multiple packages simultaneously | Run in dry-run first; review diff |
| Laravel 9 pinned dependencies (symfony/*) may have known CVEs | Update via `composer update` which respects Laravel 9 constraint |
| npm audit vulnerabilities persist after update | Document as known; flag for future Vite migration |
| package-lock.json was just generated — verify it matches package.json | Re-run npm ci to verify reproducibility |

## 5. Dependencies

- Parent C1 fixes must be committed first (bcrypt password hashing, HashTest fix, config/app.php hasher swap) — these are already COMPLETE
- npm install requires network access (confirmed working)
- composer update requires PHP + Composer (PHP available; Composer.phar not found locally — may need download)

## 6. Decision

DEC-D1-DEPENDENCY: D1 documents the dependency update scope. All findings verified by:
- Direct file reads (package.json, composer.json, composer.lock, package-lock.json)
- Real npm install + npm audit execution
- ncu output for latest versions

C1 (implementation of updates) requires explicit user approval per `/sdd` Behavior step 3 (wait for approval gate).

---

## R104
Appended: `/sdd` with "all libraries update" request — D1 phase for dependency update chain.
