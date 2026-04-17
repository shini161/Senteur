# Senteur

**Senteur** is a perfume-focused e-commerce portfolio project built with plain PHP, MySQL, and Docker. It uses a lightweight MVC structure with custom routing, service and repository layers, and a Stripe test-mode checkout flow.

The repo is designed to be easy to review: application code lives in `src/`, the public entrypoint is `public/index.php`, and the local environment is fully defined in Docker.

## Features

- Customer registration, login, logout, and profile pages
- Product catalogue with search, sorting, and advanced note filters
- Product detail pages with variants, scent notes, reviews, and related products
- Session-based cart and address book
- Checkout flow with Stripe Checkout integration
- Order history and order detail pages
- Admin authentication
- Admin order management
- Admin product management with product and variant image uploads
- Dockerized local environment with Nginx, PHP-FPM, and MySQL

## Stack

| Layer | Technology |
| --- | --- |
| Language | PHP 8.3 |
| Web server | Nginx |
| App runtime | PHP-FPM |
| Database | MySQL 8 |
| Payments | Stripe PHP SDK + Stripe Checkout |
| Containers | Docker Compose |

## Architecture

- Request flow: Nginx -> `public/index.php` -> `src/bootstrap.php` -> router -> controller -> service -> repository
- Controllers stay thin and focus on HTTP concerns
- Services own validation and business rules
- Repositories encapsulate PDO queries and transactions
- Views are plain PHP templates rendered through `App\Core\Controller`

## Getting started

Prerequisites: Docker with Compose, plus ports `8080` and `3306` available unless you change the mappings in `docker-compose.yml`.

1. Copy the environment template:

   ```bash
   cp .env.example .env
   ```

2. Build and start the stack:

   ```bash
   docker compose up --build
   ```

3. Open `http://localhost:8080`

4. Stop the stack when finished:

   ```bash
   docker compose down
   ```

## Optional Cloudflare Quick Tunnel

You can make the app publicly reachable through a free temporary Cloudflare Quick Tunnel without changing the normal `docker compose up --build` command.

1. Open `.env`.
2. Set:

   ```env
   COMPOSE_PROFILES=cloudflare
   ```

3. Start the stack as usual:

   ```bash
   docker compose up --build
   ```

4. Watch the `cloudflared` logs in the compose output, or in a second terminal:

   ```bash
   docker compose logs -f cloudflared
   ```

5. Open the generated `https://...trycloudflare.com` URL shown in those logs.

Notes:

- The tunnel is ephemeral, so the public URL changes each time the service starts.
- To disable it again, clear `COMPOSE_PROFILES` in `.env`.
- The tunnel forwards to `CLOUDFLARE_TUNNEL_ORIGIN`, which defaults to the internal Nginx service at `http://nginx:80`.
- Features that rely on absolute URLs from `APP_URL` such as Stripe Checkout redirects will still use whatever `APP_URL` is set to. For a temporary tunnel, update `APP_URL` manually after the tunnel URL appears if you want those external redirects to point at the tunnel instead of `localhost`.

## Demo accounts

The seed file includes development accounts from `docker/mysql/seed.sql`:

- Customer: `mario@example.com` / `password123`
- Customer: `giulia@example.com` / `password123`
- Admin: `admin@example.com` / `password123`

## Stripe test setup

Stripe is configured for development usage through `.env`.

Required variables:

```env
APP_URL=http://localhost:8080
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Recommended local workflow:

1. Install the Stripe CLI from Stripe's official releases for your platform.
2. Authenticate:

   ```bash
   stripe login
   ```

3. Start the webhook forwarder while the app is running:

   ```bash
   stripe listen --forward-to localhost:8080/webhooks/stripe
   ```

4. Copy the returned `whsec_...` value into `STRIPE_WEBHOOK_SECRET` in `.env`.

If you are testing through a temporary Cloudflare tunnel, set `APP_URL` to the current `trycloudflare.com` address before starting a Stripe Checkout session so the redirect URLs match the public tunnel.

Stripe test card:

```text
4242 4242 4242 4242
any future date
any CVC
```

## Fedora / SELinux note

If bind mounts fail with permission errors on Fedora or another SELinux-enabled system, create a local override file from `docker-compose.override.example.yml`:

```bash
cp docker-compose.override.example.yml docker-compose.override.yml
```

Then start the stack normally with `docker compose up --build`.

## Environment variables

See `.env.example` for the full template. The app currently expects:

- `APP_ENV`, `APP_DEBUG`, `APP_URL`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `STRIPE_SECRET_KEY`, `STRIPE_PUBLISHABLE_KEY`, `STRIPE_WEBHOOK_SECRET`
- Optional Compose/tunnel settings: `COMPOSE_PROFILES`, `CLOUDFLARE_TUNNEL_ORIGIN`

## Database

- Schema: `docker/mysql/init.sql`
- Demo data: `docker/mysql/seed.sql`
- Interactive diagram: https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6

## Project layout

```text
senteur/
├── docker/      # Dockerfiles, Nginx config, MySQL schema and seed files
├── docs/        # Structure docs, flow diagrams, page notes
├── public/      # Web root and compiled/static assets
├── resources/   # Demo upload assets copied into public/uploads by the container
├── src/         # Application source (Core, Controllers, Models, Services, Views)
├── .env.example
├── composer.json
├── docker-compose.yml
└── README.md
```

For a more detailed walkthrough of the repository tree, see `docs/PROJECT_STRUCTURE.md`.

## License

This project is provided for educational and portfolio purposes.
