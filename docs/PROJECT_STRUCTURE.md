# Project structure

This file mirrors the layout of the repository so a reviewer (for example a teacher) can open the tree and know what each part is for.

## Top level

| Path | Role |
|------|------|
| `docker/` | Images and config used only by Docker. |
| `docker/nginx/default.conf` | Nginx virtual host: web root, URL rewriting to `public/index.php`, FastCGI to PHP. |
| `docker/php/Dockerfile` | PHP-FPM image (extensions, workdir). |
| `docker/mysql/init.sql` | Runs on **first** MySQL container init: creates DB, tables and seed data. |
| `public/` | **Document root** — only files here are directly reachable by URL (+ what Nginx maps). |
| `public/index.php` | Front controller: every non-file request should end up here so the app can route. |
| `public/assets/` | Static CSS, JS, images committed with the project. |
| `public/uploads/` | User or admin uploaded files (often gitignored). |
| `src/` | Application code. |
| `.env` | Local secrets and environment (copy from `.env.example`). |
| `docker-compose.yml` | Defines `app` (PHP-FPM), `nginx`, `mysql` and how they connect. |

## `src/` layout
| Path | Role |
|------|------|
| `src/bootstrap.php` | Runs once per request: load config, autoloading, DB connection and dispatch router. |
| `src/routes.php` | Declares URL → controller/action (or closure) mapping. |
| `src/Core/` | Small "framework" pieces shared by the app: HTTP, routing, DB access, base controller. |
| `src/Controllers/` | One class per area of the site; handles request/response and calls services. |
| `src/Models/` | Data shapes and DB access for one table or aggregate. |
| `src/Services/` | Business rules: "list products", "create order" — keeps controllers thin. |
| `src/Views/` | PHP templates (HTML); `layouts/` for shared chrome, subfolders per feature. |

## Request path

1. Browser asks Nginx for a URL.
2. Nginx serves a static file from `public/` if it exists; otherwise forwards to `public/index.php`.
3. `index.php` loads `src/bootstrap.php`.
4. Bootstrap loads routes, matches the path, runs the right controller method.
5. Controller may call a **service**, which uses **models** and returns data.
6. Controller renders a **view** (HTML) or returns JSON, redirect, etc.

## Database

The database schema is defined in:

* `docker/mysql/init.sql` → full schema used by Docker

### Diagram

The current entity-relationship diagram is available:

* PNG: `docs/diagrams/SQLDiaSenteurSQLDiagramgram.png`
* SVG: `docs/diagrams/SenteurSQLDiagram.svg` (recommended for zoom)

You can also explore it interactively:

* https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6

The diagram reflects the current production schema (kept in sync with `init.sql`).
