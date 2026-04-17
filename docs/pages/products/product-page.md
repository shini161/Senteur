# Product Page

## Purpose
Shows a public product detail page with variant selection, gallery updates, scent profile data, reviews, and related-product recommendations.

---

## Routes
```bash
GET  /products/{slug}
POST /products/{slug}/reviews
```

---

## Access Rules
- product view is public
- review submission requires authentication
- only customers who purchased the product in an order with status `processing`, `shipped`, or `delivered` can submit a review

---

## Parameters
| Param | Type   | Required | Notes |
| ----- | ------ | -------- | ----- |
| slug  | string | ✅       | public product slug |

---

## Page Data
- product metadata
- product-level primary image
- variants ordered by price
- variant-specific image galleries
- grouped scent notes
- product categories
- review summary
- review list
- current user's review, when present
- review eligibility flag
- related products from the same family line
- additional related recommendations

Each variant includes:
- `id`
- `size_ml`
- `price`
- `stock`
- `image_url`
- `images`

---

## Request Flow

![Product Page Flow](../../flows/products/product-page.png)

---

## Controller
```php
ProductController::show(string $slug)
ReviewController::store(string $slug)
```

---

## Service Layer
```php
ProductService::getBySlug(string $slug): ?array
ReviewService::getProductReviewData(int $productId, ?int $userId): array
ReviewService::saveByProductSlug(int $userId, string $slug, array $data): void
```

---

## Current Behavior
- The product lookup only returns non-deleted products.
- Variants are ordered by price and the first variant is selected when the page loads.
- Variant buttons carry price, stock, and image data so the page can swap gallery state client-side without another request.
- The add-to-cart form posts the selected `variant_id` and `quantity` to `/cart/add`.
- The scent profile section renders categories plus either `Fragrance Notes` or a staged top/heart/base pyramid, depending on the data available for the perfume.
- Review summary shows the average rating and total review count when reviews exist.
- Review submission is an upsert, so one user can create or update one review per product.
- The current user's review can be edited inline from the reviews list.
- Related products are split into two blocks:
  - products from the same `family_name`
  - broader recommendations ranked by brand, fragrance type, and gender similarity

---

## Review Submission Rules
Review input fields:

| Field   | Type     | Required | Notes |
| ------- | -------- | -------- | ----- |
| rating  | int      | ✅       | must be between 1 and 5 |
| title   | text     | ❌       | stored as `NULL` when empty |
| comment | textarea | ❌       | stored as `NULL` when empty |

Review submission requires:
- authenticated user
- valid CSRF token
- successful product lookup by slug
- at least one qualifying purchase of the product

---

## Persistence
The page reads from:
- `products`
- `brands`
- `fragrance_types`
- `product_variants`
- `product_variant_images`
- `product_images`
- `product_categories`
- `categories`
- `product_notes`
- `notes`
- `reviews`
- `users`

Review eligibility is determined by checking whether the user previously bought a variant that belongs to the product.

---

## Responses
- Unknown slug on `GET /products/{slug}`: `404 Product not found`
- Successful review submission: `302` redirect to `/products/{slug}#reviews`
- Review submission error: `400` with the exception message

---

## Security
- dynamic data is loaded server-side and escaped in the view
- review submission requires authentication and CSRF protection
- purchase-gating is enforced server-side, not just in the UI
