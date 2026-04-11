# Project Structure

This document mirrors the current repository layout so a reviewer can move through the project quickly and understand what each part is responsible for.

## Top level

| Path | Role |
| --- | --- |
| `docker/` | Container build and runtime files for PHP-FPM, Nginx, and MySQL. |
| `docs/` | Repository documentation, diagrams, flows, and page notes. |
| `public/` | Web document root served by Nginx. |
| `resources/` | Demo images copied into `public/uploads/` at container startup. |
| `src/` | Application source code. |
| `.env` | Local environment values used at runtime. |
| `.env.example` | Template for local environment setup. |
| `composer.json` | PHP dependency manifest. |
| `docker-compose.yml` | Default local development stack. |
| `docker-compose.override.example.yml` | Optional SELinux-friendly bind-mount override. |
| `README.md` | Main project overview and run instructions. |

## Docker and infrastructure

| Path | Role |
| --- | --- |
| `docker/php/Dockerfile` | Builds the PHP 8.3 FPM application image and installs Composer dependencies. |
| `docker/php/entrypoint.sh` | Prepares upload directories and copies demo assets before starting PHP-FPM. |
| `docker/nginx/default.conf` | Nginx vhost that serves `public/` and forwards PHP execution to the `app` container. |
| `docker/mysql/init.sql` | Base schema loaded when the MySQL volume initializes. |
| `docker/mysql/seed.sql` | Demo data used for local development, screenshots, and review. |

## `public/`

| Path | Role |
| --- | --- |
| `public/index.php` | Front controller for the application. |
| `public/assets/css/app.css` | Shared base stylesheet for layout, forms, admin, and profile pages. |
| `public/assets/css/pages/` | Page-focused CSS split out for products, cart, and orders. |
| `public/assets/css/responsive.css` | Shared responsive overrides and utility rules loaded after page styles. |
| `public/assets/js/` | Browser-side scripts organized by feature/page. |
| `public/assets/images/` | Logo and static branding assets. |
| `public/uploads/` | Runtime upload destination exposed by the web server. |

## `src/`

| Path | Role |
| --- | --- |
| `src/bootstrap.php` | Request bootstrap: custom autoloading, helper loading, env loading, exception handling, and router dispatch. |
| `src/routes.php` | Flat route table for public, checkout, order, review, and admin endpoints. |
| `src/helpers.php` | Global helper functions used in views. |
| `src/Core/` | Shared framework-like primitives such as auth, CSRF, DB access, request parsing, routing, and the base controller. |
| `src/Controllers/` | HTTP-layer classes that validate request intent and delegate business logic to services. |
| `src/Services/` | Business rules, orchestration, validation, and payment/checkout coordination. |
| `src/Models/` | Repository classes containing PDO queries and transactions. |
| `src/Models/Concerns/` | Internal repository traits used to keep large data-access classes split by responsibility. |
| `src/Views/` | Plain PHP templates grouped by feature area. |

## `src/Views/`

| Path | Role |
| --- | --- |
| `src/Views/layouts/` | Shared page chrome used by rendered templates. |
| `src/Views/home/` | Landing page. |
| `src/Views/products/` | Catalogue listing and product detail pages. |
| `src/Views/products/partials/` | Reusable product-page fragments such as review and related-product sections. |
| `src/Views/cart/` | Session-cart review page. |
| `src/Views/auth/` | Customer login and registration pages. |
| `src/Views/user/` | Profile and address-book pages. |
| `src/Views/checkout/` | Checkout, success, and cancellation pages. |
| `src/Views/orders/` | Customer order history and order details. |
| `src/Views/admin/` | Admin login, product management, and order management views. |

## `docs/`

| Path | Role |
| --- | --- |
| `docs/PROJECT_STRUCTURE.md` | This file. |
| `docs/diagrams/` | Architecture and schema diagrams. |
| `docs/flows/` | Excalidraw and exported flow diagrams for user/admin journeys. |
| `docs/pages/` | Page-level notes and documentation snapshots. |

## Request flow

1. A browser sends a request to Nginx.
2. Nginx serves a static file from `public/` when one exists.
3. Otherwise, Nginx forwards the request to `public/index.php`.
4. `public/index.php` loads Composer dependencies and then `src/bootstrap.php`.
5. Bootstrap loads helpers and environment values, creates the request object, loads `src/routes.php`, and dispatches through the router.
6. The router resolves the target controller and action.
7. Controllers call services.
8. Services use repositories in `src/Models/` to query or update MySQL.
9. Controllers render a PHP view or return a redirect / error response.

## Functional areas currently present

- Public storefront and home page
- Product catalogue with advanced note filters
- Product reviews gated by purchase history
- Session-backed cart
- User profile and saved addresses
- Checkout and Stripe integration
- Customer order history
- Admin login
- Admin order management
- Admin product management and image uploads

## Database

The database artifacts currently live here:

- `docker/mysql/init.sql` for the base schema
- `docker/mysql/seed.sql` for demo data

Available diagrams:

- PNG: `docs/diagrams/SenteurSQLDiagram.png`
- SVG: `docs/diagrams/SenteurSQLDiagram.svg`
- Interactive: https://dbdiagram.io/d/SenteurSQLDiagram-69c288c3fb2db18e3bf07cf6
