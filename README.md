# Senteur

Senteur is a perfume-focused e-commerce portfolio application built with plain PHP, MySQL, Stripe test-mode checkout, and Docker. The codebase uses a lightweight MVC structure with custom routing, service and repository layers, and server-rendered PHP views.

## Highlights

- Public storefront with catalogue search, filters, scent-note filtering, and product detail pages
- Session cart, saved addresses, checkout flow, and Stripe test payments
- Customer profile, order history, and order detail pages
- Admin workspaces for orders, products, fragrance notes, and catalog metadata
- Dockerized local setup with Nginx, PHP-FPM, and MySQL

## Stack

| Layer | Technology |
| --- | --- |
| Language | PHP 8.3 |
| Web server | Nginx |
| Runtime | PHP-FPM |
| Database | MySQL 8 |
| Payments | Stripe Checkout + `stripe/stripe-php` |
| Local environment | Docker Compose |

## Portfolio Visuals

Add image here: storefront home page hero.
Suggested path: `docs/screenshots/storefront/home-desktop.png`

Add image here: product detail page with gallery and reviews.
Suggested path: `docs/screenshots/storefront/product-detail.png`

Add image here: admin workspace such as product editing or catalog data management.
Suggested path: `docs/screenshots/admin/product-editor.png`

For the full screenshot checklist, see `docs/PORTFOLIO_ASSETS.md`.

## Architecture

- Request flow: Nginx -> `public/index.php` -> `src/bootstrap.php` -> router -> controller -> service -> repository -> view
- Controllers stay focused on HTTP concerns
- Services own validation, orchestration, and business rules
- Repositories encapsulate PDO queries and transactions
- Views are plain PHP templates rendered through the shared controller base class

## Run Locally

Prerequisites:

- Docker with Compose support
- Port `8080` available for the app
- Port `3306` available for MySQL unless you change the mapping

1. Copy the environment template:

   ```bash
   cp .env.example .env
   ```

2. Start the stack:

   ```bash
   docker compose up --build
   ```

3. Open `http://localhost:8080`

4. Stop the stack when you are done:

   ```bash
   docker compose down
   ```

## Demo Accounts

Seed data in `docker/mysql/seed.sql` includes:

- Customer: `mario@example.com` / `password123`
- Customer: `giulia@example.com` / `password123`
- Admin: `admin@example.com` / `password123`

## Stripe Test Setup

Required `.env` values:

```env
APP_URL=http://localhost:8080
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Recommended local webhook workflow:

1. Install the Stripe CLI.
2. Authenticate:

   ```bash
   stripe login
   ```

3. Forward webhook events to the app:

   ```bash
   stripe listen --forward-to localhost:8080/webhooks/stripe
   ```

4. Copy the returned `whsec_...` value into `.env`.

Stripe test card:

```text
4242 4242 4242 4242
any future date
any CVC
```

## Optional Cloudflare Quick Tunnel

To expose the app temporarily without changing the normal Docker workflow:

1. Set `COMPOSE_PROFILES=cloudflare` in `.env`
2. Start the stack with `docker compose up --build`
3. Read the public `trycloudflare.com` URL from the `cloudflared` logs
4. If you want Stripe redirects to use that URL, update `APP_URL` after the tunnel starts

## Project Layout

```text
senteur/
├── docker/        # PHP, Nginx, and MySQL container setup
├── docs/          # Architecture notes, page docs, screenshots, and diagrams
├── public/        # Web root, CSS, JS, and runtime uploads
├── resources/     # Demo image assets copied into public/uploads
├── src/           # Core app code: Controllers, Services, Models, Views
├── composer.json
├── docker-compose.yml
└── README.md
```

## Documentation

- Repository map: `docs/PROJECT_STRUCTURE.md`
- Screenshot checklist: `docs/PORTFOLIO_ASSETS.md`
- Page docs: `docs/pages/`
- Architecture/schema diagrams: `docs/diagrams/`

## Notes

- `docker/php/entrypoint.sh` copies demo assets into `public/uploads/` when containers start.
- `docker-compose.override.example.yml` is included for SELinux-friendly local setups.
- This repository is presented as a finished portfolio project and reference build.
