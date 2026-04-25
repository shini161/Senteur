# Project Structure

This file is the quickest way to understand how the repository is organized in its current portfolio-ready state.

## Top Level

| Path | Role |
| --- | --- |
| `docker/` | Container definitions and runtime setup for PHP-FPM, Nginx, and MySQL. |
| `docs/` | Project documentation, diagrams, page notes, and screenshot placeholders. |
| `public/` | Web root served by Nginx. |
| `resources/` | Demo upload assets copied into `public/uploads/` at startup. |
| `src/` | Application source code. |
| `.env.example` | Environment template for local setup. |
| `composer.json` | PHP dependency manifest. |
| `docker-compose.yml` | Default local development stack. |
| `README.md` | Main overview, setup instructions, and portfolio summary. |

## Docker And Infrastructure

| Path | Role |
| --- | --- |
| `docker/php/Dockerfile` | Builds the PHP 8.3 FPM image and installs Composer dependencies. |
| `docker/php/entrypoint.sh` | Prepares upload folders and copies demo assets before PHP starts. |
| `docker/nginx/default.conf` | Nginx virtual host serving `public/` and forwarding PHP requests. |
| `docker/mysql/init.sql` | Base database schema. |
| `docker/mysql/seed.sql` | Demo data for local review and screenshots. |

## Public Assets

| Path | Role |
| --- | --- |
| `public/index.php` | Front controller for every dynamic request. |
| `public/assets/css/app.css` | Shared base styles, tokens, layout chrome, forms, and admin scaffolding. |
| `public/assets/css/pages/home.css` | Home-page-only presentation styles. |
| `public/assets/css/pages/account.css` | Auth, profile, addresses, and checkout page styles. |
| `public/assets/css/pages/products.css` | Catalogue, product detail, reviews, and note-filter styles. |
| `public/assets/css/pages/cart.css` | Cart layout and cart-summary styles. |
| `public/assets/css/pages/orders.css` | Customer and admin order screen styles. |
| `public/assets/css/responsive.css` | Shared responsive tweaks that still apply across multiple features. |
| `public/assets/js/layout/` | Shared storefront UI scripts such as the mobile navbar. |
| `public/assets/js/products/` | Catalogue and product-detail enhancements. |
| `public/assets/js/admin/` | Admin filters and product-form interactions. |
| `public/assets/images/` | Static branding assets such as the logo and favicon. |
| `public/uploads/` | Runtime upload destination for product and note images. |

## Application Code

| Path | Role |
| --- | --- |
| `src/bootstrap.php` | Request bootstrap: helpers, env loading, error handling, session start, and router dispatch. |
| `src/routes.php` | Flat route table for storefront, checkout, order, and admin endpoints. |
| `src/helpers.php` | Shared helper functions used in templates. |
| `src/Core/` | Lightweight framework primitives such as auth, CSRF, request parsing, DB access, and routing. |
| `src/Controllers/` | HTTP-layer classes that validate intent and delegate to services. |
| `src/Services/` | Business rules, validation, orchestration, and payment/catalog logic. |
| `src/Models/` | Repository classes with PDO queries and transactions. |
| `src/Models/Concerns/` | Traits used to split larger repository responsibilities. |
| `src/Support/` | Reusable support classes such as fragrance-note helpers. |
| `src/Views/` | Plain PHP templates grouped by feature area. |

## View Areas

| Path | Role |
| --- | --- |
| `src/Views/layouts/` | Shared storefront layout and page chrome. |
| `src/Views/home/` | Landing page. |
| `src/Views/products/` | Public catalogue and product detail pages. |
| `src/Views/cart/` | Cart page. |
| `src/Views/auth/` | Customer login and registration pages. |
| `src/Views/user/` | Profile and address-book pages. |
| `src/Views/checkout/` | Checkout, success, and cancellation pages. |
| `src/Views/orders/` | Customer order history and order details. |
| `src/Views/admin/` | Admin authentication plus order, product, note, and catalog management views. |

## Documentation

| Path | Role |
| --- | --- |
| `docs/PROJECT_STRUCTURE.md` | This file. |
| `docs/PORTFOLIO_ASSETS.md` | Screenshot and README asset checklist. |
| `docs/pages/` | Page-level notes for current application screens. |
| `docs/diagrams/` | Architecture and database diagrams. |
| `docs/flows/` | Flow sketches and Excalidraw exports kept as supporting references. |
| `docs/screenshots/` | Empty folders reserved for future portfolio screenshots. |

## Current Admin Docs

- `docs/pages/admin/catalog.md`
- `docs/pages/admin/notes.md`
- `docs/pages/admin/orders.md`
- `docs/pages/admin/order-details.md`
- `docs/pages/admin/products.md`
- `docs/pages/admin/product-create.md`
- `docs/pages/admin/product-edit.md`

## Request Flow

1. Nginx serves static assets from `public/` when possible.
2. Dynamic requests are routed into `public/index.php`.
3. `src/bootstrap.php` loads helpers, environment values, sessions, and routes.
4. `App\Core\Router` matches the request and resolves the controller.
5. Controllers call services.
6. Services use repositories for database access.
7. Controllers render a PHP view or return a redirect/error response.
