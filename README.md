# Senteur

**Senteur** is a school project: a small web shop focused on **perfumes**, built with **plain PHP** (no full framework), **MySQL**, and **Docker** so the same environment runs everywhere.

The goal is to show you can structure a PHP app clearly (routing, controllers, models, views), persist data in a relational schema, and ship something a teacher can run with one command.

---

# What this repo contains

- A **Docker** stack: **Nginx** (web server) → **PHP-FPM** (app) → **MySQL** (database).
- A **custom “mini framework”** folder under `src/Core/` (router, request/response, database helper — to be wired as you implement features).
- **MVC-style folders**: controllers, models, services, views.
- A **database schema** in `docker/mysql/init.sql` (brands, products, variants, images, users, reviews, etc.).

---

## Stack

| Layer | Technology |
|--------|------------|
| Language | PHP 8.3 (see `docker/php/Dockerfile`) |
| Web server | Nginx |
| App server | PHP-FPM |
| Database | MySQL 8 |
| Containers | Docker Compose |

---

## How to run it

**Prerequisites:** Docker with Compose, ports **8080** and **3306** free (or change mappings in `docker-compose.yml`).

1. Clone the repository and enter the project directory.

2. Copy environment file:

   ```bash
   cp .env.example .env
   ```

   Adjust values if you change database credentials in Compose.

3. Start containers:

   ```bash
   docker compose up --build
   ```

4. Open **http://localhost:8080**  
   - Nginx listens on host **8080** and forwards PHP to the `app` container.  
   - Until routing and views are implemented, you may see an **empty page** with HTTP 200 — that still means Nginx and PHP-FPM are working.

5. Stop: `Ctrl+C`, or in another terminal: `docker compose down`.

**MySQL note:** `init.sql` runs only when MySQL initializes a **new** data volume. If you change the schema later, you may need to reset the volume or apply migrations manually — see `docs/PROJECT_STRUCTURE.md`.

---

## Environment variables

Defined in `.env` (see `.env.example`):

- `APP_ENV`, `APP_DEBUG` — local development flags (you can read them in `bootstrap.php` later).
- `DB_*` — host `mysql` matches the Compose service name; use these when you connect with PDO in `Database.php`.

---

## Database diagram

Entity-relationship overview

**[Open the Senteur schema on dbdiagram.io](https://dbdiagram.io/e/69c288c3fb2db18e3bf07cf6/69c66591fb2db18e3b1d2378)** (Old Schema)

*This section will be fixed later*

---

## Project layout

```
senteur/
├── docker/                 # Docker-only: Nginx config, PHP Dockerfile, MySQL init SQL
├── public/                 # Web root (index.php, assets, uploads)
├── src/
│   ├── Core/               # Router, Request, Response, Database, base Controller
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Views/
│   ├── routes.php
│   └── bootstrap.php
├── docs/
│   └── PROJECT_STRUCTURE.md   # Deeper folder-by-folder explanation
├── .env.example
├── docker-compose.yml
└── README.md
```

More detail: **[docs/PROJECT_STRUCTURE.md](docs/PROJECT_STRUCTURE.md)**

---

## License

This is a School project, so it has no License.