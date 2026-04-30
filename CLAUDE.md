# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Preguntero App** — a pure PHP (no framework) Q&A platform backed by MySQL. Users register/login and can perform full CRUD on questions (`preguntas`) and their associated answers (`respuestas`).

## Running the App

This is a PHP application with no build step. Requirements: PHP 8+, MySQL 8+.

1. Create the database and run the schema:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
2. Serve from `public/` as the document root:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Adjust DB credentials in [config/database.php](config/database.php) if needed.

There are no automated tests yet. Verification is done manually per the plan: registration, login, SQL injection, CSRF, XSS, cascade delete, and unauthorized access.

## Architecture

**Front controller** — `public/index.php` is the single entry point. All requests route through it.

**MVC layout:**
- [app/controllers/](app/controllers/) — `AuthController` (register/login) and `PreguntaController` (CRUD)
- [app/models/](app/models/) — `Usuario`, `Pregunta`, `Respuesta`; data access via PDO
- [app/views/](app/views/) — split into `auth/`, `layouts/` (header/footer), and `preguntas/`
- [app/core/](app/core/) — shared infrastructure: `Database` (PDO singleton), `Session`, `Csrf`, `Validator`

**Database** — three tables defined in [database/schema.sql](database/schema.sql):
- `usuarios` — PK is `dni_usuario CHAR(8)`; unique on `username` and `email`
- `preguntas` — auto-increment PK, `enunciado VARCHAR(500)`
- `respuestas` — FK to `preguntas` with `CASCADE DELETE`; composite index on `(id_pregunta, es_correcta)`

**Frontend** — Bootstrap + Font Awesome + Merriweather loaded from CDN; no custom CSS/JS yet. Static asset folders (`public/css`, `public/js`, `public/img`) are placeholders.

## Security Conventions

These are non-negotiable across the entire codebase:

- **PDO prepared statements** for every DB query — no string interpolation in SQL.
- **`password_hash()`** with Argon2id (fallback bcrypt); never store plain or MD5 passwords.
- **CSRF tokens** on every mutating form (insert/update/delete); validate in controller before processing.
- **Session hardening** — regenerate session ID on login (`session_regenerate_id(true)`); set strict session flags.
- **Output escaping** — wrap every user-supplied value in `htmlspecialchars()` before echoing.
- **Generic auth errors** — login always returns the same message regardless of whether username, email, or password was wrong, to prevent account enumeration.
- **Server-side validation** always; client-side is optional and supplementary only.

## Business Rules

- Question `enunciado` is mandatory and non-empty.
- Each question must have at least a minimum number of answers (defined in plan; TBD in implementation).
- Each question must have at least one answer marked `es_correcta = 1`.
- Answer text must be non-empty.
- Use **transactions** when writing a question together with its answers to avoid partial state.
- DNI is exactly 8 digits stored as `CHAR(8)` to preserve leading zeros.
- No roles or admin panel in v1 — any authenticated user can perform CRUD.
