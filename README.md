# hascode-task-laravel

Laravel 9 API application with Hash CRUD and Auth endpoints.

## Overview

A REST API built on Laravel 9 providing two resource areas:
- **Hash** — CRUD operations on hash records (SHA1-based hashing)
- **Auth** — User registration and login with token-based authentication

The application code lives under `api/` (not the repo root). All API routes are prefixed with `/api`.

## Stack

- PHP ^8.0.2, Laravel 9.11
- Laravel Passport (API tokens) — `HasApiTokens` trait on User model
- Laravel Sanctum (in composer.json but not used for auth)
- PHPUnit 10 (tests under `api/tests/`)
- MySQL (configured in `api/config/database.php`)

## Structure

```
api/
  app/
    Http/Controllers/API/
      HashController.php     — index, store, show, update, destroy
      AuthController.php     — register, login
    Models/
      Hash.php               — fillable: data
      User.php               — Passport tokens
    Providers/               — Standard Laravel providers
  routes/
    api.php                  — /register, /login, /hash resource, /user
  tests/
    Feature/
      HashTest.php           — 5 tests (CRUD on hash)
      AuthTest.php           — 2 tests (register, login)
    Unit/
      ExampleTest.php        — 1 trivial test
  database/
    migrations/              — users, hashes, plus defaults
    seeders/
```

## API Endpoints

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | /api/register | no | Register user, return token |
| POST | /api/login | no | Login, return token |
| GET | /api/hash | yes | List all hashes |
| POST | /api/hash | yes | Create hash |
| GET | /api/hash/{id} | yes | Show hash |
| PUT | /api/hash/{id} | yes | Update hash |
| DELETE | /api/hash/{id} | yes | Delete hash |
| GET | /api/user | yes | Current user |

## Findings

### Password hashing: SHA1 instead of bcrypt
`AuthController::register()` stores passwords via `hash('sha1', $request->password)` (line 28). SHA1 is cryptographically unsuitable for password storage — bcrypt (or argon2) is the Laravel standard and is what `AuthTest::testLogin()` uses for test data (`bcrypt('123456789')`). This is a real security gap, not a stale finding.

### Test bug in HashTest::authenticate()
The `authenticate()` helper creates a user with password `secret9874` (hashed) but attempts login with `'secret1234'` (line 26). These do not match — `auth()->attempt()` will fail. The tests as written cannot pass without fixing the password mismatch.

### README: Laravel boilerplate only
Both `README.md` (root) and `api/README.md` contain the default Laravel framework description — neither documents this project's actual API endpoints, structure, or setup. This README replaces both.

### Sanctum in composer.json, Passport in code
`laravel/sanctum` is in `require-dev` but the User model uses `Laravel\Passport\HasApiTokens`. Sanctum is not referenced in code. Passport is the active auth library.

## Run Instructions

```bash
cd api
cp .env.example .env
# Configure DB connection in .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
# or via Docker (if docker-compose exists)
```

## Tests

```bash
cd api
vendor/bin/phpunit
```

Note: HashTest and AuthTest require a running database with correct credentials configured in `.env`.

## License

MIT (Laravel default).
