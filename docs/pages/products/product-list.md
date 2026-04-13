# Product List

## Purpose
Displays the public catalogue with search, structured filters, advanced note filters, sorting, and server-rendered pagination.

---

## Route
```bash
GET /products
```

---

## Query Parameters
| Param               | Type        | Required | Notes |
| ------------------- | ----------- | -------- | ----- |
| search              | string      | ❌       | matches name, family, concentration, description, brand, and fragrance type |
| brand_id            | int         | ❌       | `0` means all brands |
| fragrance_type_id   | int         | ❌       | `0` means all types |
| gender              | string      | ❌       | `male`, `female`, or `unisex` |
| sort                | string      | ❌       | `newest`, `name_asc`, `price_asc`, `price_desc` |
| top_note_ids[]      | int[]       | ❌       | advanced top-note filter |
| middle_note_ids[]   | int[]       | ❌       | advanced middle-note filter |
| base_note_ids[]     | int[]       | ❌       | advanced base-note filter |
| page                | int         | ❌       | 1-based page number |

---

## Page Data
- paginated product cards
- normalized filter state
- brand options
- fragrance type options
- note list used by the advanced note picker
- gender options
- sort options
- total result count

Each product card includes:
- `name`
- `slug`
- `brand_name`
- `concentration_label`
- `price` as the minimum active variant price
- `image_url`
- `is_sellable`

---

## Request Flow

![Product List Flow](../../flows/products/product-list.png)

---

## Controller
```php
ProductController::index()
```

---

## Service Layer
```php
ProductService::normalizePublicFilters(array $input): array
ProductService::getPublicFilterMeta(): array
ProductService::countAll(array $filters = []): int
ProductService::getAll(array $filters = [], int $limit = 12, int $offset = 0): array
```

---

## Current Behavior
- Query-string input is normalized before any repository call.
- Free-text search is split into tokens, and every token must match one of the searchable product fields.
- Brand, fragrance type, and gender filters are applied only when they hold allowed values.
- Advanced note filters use separate groups for top, middle, and base notes.
- Within one note group, selected ids are matched with `IN (...)`; across groups, conditions are combined with `AND`.
- Results are paginated at `12` products per page.
- If the requested page is greater than the last available page, the controller clamps it to the final page.
- Product cards show `In stock` when at least one variant has stock; otherwise they show `Unavailable`.
- The advanced note picker is enhanced on the client by `public/assets/js/products/catalog.js`.

---

## Persistence
The catalogue query joins:
- `products`
- `brands`
- `fragrance_types`
- `product_variants`
- `product_images`

It returns:
- the minimum variant price for each product
- variant counts
- whether any variant is in stock
- the primary product image

---

## Responses
- Success: render `products/index`
- No results: render the page with the current filters and an empty-state message

---

## Security
- sorting is whitelisted before becoming SQL
- filters are normalized before querying
- repository queries use prepared statements for dynamic values
