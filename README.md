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
* Orders system
* Stripe payment integration (test mode)

---

# What this repo contains

* A **Docker** stack: **Nginx** (web server) → **PHP-FPM** (app) → **MySQL** (database).
* A **custom “mini framework”** folder under `src/Core/` (router, request/response, database helper).
* **MVC-style folders**: controllers, models, services, views.
* A **database schema** in `docker/mysql/init.sql`.

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

3. Start containers:

   ```bash
   docker compose up --build
   ```

4. Open **[http://localhost:8080](http://localhost:8080)**

5. Stop:

   ```bash
   docker compose down
   ```

---

## Stripe (Payments – Development)

Stripe is configured in **test mode** for development.

### 1. Add environment variables

In `.env`:

```env
APP_URL=http://localhost:8080
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=
```

---

### 2. Install Stripe CLI

Download and install from Stripe releases (Linux RPM recommended for Fedora):

```bash
sudo dnf install ./stripe_*.rpm
```

---

### 3. Authenticate

```bash
stripe login
```

---

### 4. Start webhook listener

Run this **while your app is running**:

```bash
stripe listen --forward-to localhost:8080/webhooks/stripe
```

You will get:

```text
whsec_...
```

Add it to `.env`:

```env
STRIPE_WEBHOOK_SECRET=whsec_...
```

---

### 5. Test payments

Use Stripe test card:

```
4242 4242 4242 4242
any future date
any CVC
```

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

* `APP_ENV`, `APP_DEBUG`
* `DB_*`
* `STRIPE_*`

---

## Database diagram

* Interactive: [https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6](https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6)

---

## Application architecture

* Router → Controller → Service → Repository → DB
* Thin controllers
* Business logic in services
* PDO for DB access

---

## Project layout

```
senteur/
├── docker/
├── public/
├── src/
│   ├── Core/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Views/
│   ├── routes.php
│   └── bootstrap.php
├── docs/
├── .env.example
├── docker-compose.yml
└── README.md
```

---

## License

This project is provided for educational and portfolio purposes.
