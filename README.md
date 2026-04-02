# Senteur

**Senteur** is a portfolio-level web application: a small e-commerce platform focused on **perfumes**, built with **plain PHP** (no full framework), **MySQL**, and **Docker**.

The project emphasizes clean architecture (MVC), explicit infrastructure setup, and reproducibility across environments.

> Originally developed in an academic context, but extended beyond requirements to demonstrate real-world backend architecture and deployment practices.

---

# Features

* User authentication (register / login / logout)
* Product catalog with filtering
* Product variants (size, concentration)
* Reviews and ratings system
* Admin panel for product and brand management
* Image uploads
* MVC architecture (custom lightweight framework)
* Dockerized environment (Nginx + PHP-FPM + MySQL)

---

# What this repo contains

* A **Docker** stack: **Nginx** (web server) → **PHP-FPM** (app) → **MySQL** (database).
* A **custom “mini framework”** folder under `src/Core/` (router, request/response, database helper).
* **MVC-style folders**: controllers, models, services, views.
* A **database schema** in `docker/mysql/init.sql` (brands, products, variants, images, users, reviews, etc.).

---

## Stack

| Layer      | Technology                            |
| ---------- | ------------------------------------- |
| Language   | PHP 8.3 (see `docker/php/Dockerfile`) |
| Web server | Nginx                                 |
| App server | PHP-FPM                               |
| Database   | MySQL 8                               |
| Containers | Docker Compose                        |

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

4. Open **[http://localhost:8080](http://localhost:8080)**

   * Nginx listens on host **8080** and forwards PHP to the `app` container.
   * If no routes are implemented yet, you may see an empty page with HTTP 200.

5. Stop: `Ctrl+C`, or in another terminal:

   ```bash
   docker compose down
   ```

**MySQL note:** `init.sql` runs only when MySQL initializes a **new** data volume. If you change the schema later, you may need to reset the volume or apply migrations manually — see `docs/PROJECT_STRUCTURE.md`.

---

## Fedora / SELinux note

On Fedora or other SELinux-enabled systems, Docker bind mounts may fail with permission errors.

If you encounter errors like:

```bash
Permission denied
```

create a local override file:

```bash
cp docker-compose.override.example.yml docker-compose.override.yml
```

Then run:

```bash
docker compose up --build
```

---

## Environment variables

Defined in `.env` (see `.env.example`):

* `APP_ENV`, `APP_DEBUG` — local development flags.
* `DB_*` — host `mysql` matches the Compose service name; use these when connecting with PDO in `Database.php`.

---

## Database diagram

Entity-relationship overview of the Senteur schema.

* Interactive: [https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6](https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6)
* Local files:

  * `docs/diagrams/SenteurSQLDiagram.png`
  * `docs/diagrams/SenteurSQLDiagram.svg`

> The diagram is generated from the current schema defined in `docker/mysql/init.sql`.

![Senteur Database Diagram](docs/diagrams/SenteurSQLDiagram.png)

---

## Application architecture

High-level MVC flow of the application.

* Router → Controller → Service → View
* Thin controllers, business logic in services
* Models handle DB interaction (PDO)

![Senteur MVC Architecture](docs/diagrams/architecture.png)

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

This project is provided for educational and portfolio purposes.
