# Cart

## Purpose
Provides the session-backed shopping cart used by both guests and authenticated users before checkout.

---

## Routes
```bash
GET  /cart
POST /cart/add
POST /cart/update
POST /cart/remove
```

---

## Access Rules
- public page
- works for guests and signed-in users

---

## Page Data
- cart lines enriched from product and variant data
- total price derived from current cart lines
- per-line quantity cap based on `min(stock, 5)`

Each rendered line includes:
- `variant_id`
- `product_slug`
- `product_name`
- `brand_name`
- `concentration_label`
- `size_ml`
- `price`
- `stock`
- `image_url`
- `quantity`
- `max_quantity`
- `subtotal`

---

## Request Flow

### Add Item
![Cart Add Flow](../../flows/cart/cart-add-item-flow.png)

### Update Item
![Cart Update Flow](../../flows/cart/cart-update-item-flow.png)

### Remove Item
![Cart Remove Flow](../../flows/cart/cart-delete-item-flow.png)

---

## Controller
```php
CartController::index()
CartController::add()
CartController::update()
CartController::remove()
```

---

## Service Layer
```php
CartService::getItems(): array
CartService::getTotal(): float
CartService::addItem(int $variantId, int $quantity): void
CartService::updateItem(int $variantId, int $quantity): void
CartService::removeItem(int $variantId): void
CartService::clear(): void
```

---

## Current Behavior
- The cart is stored in `$_SESSION['cart']` as a `variant_id => quantity` map.
- `GET /cart` reloads product data from the database instead of trusting anything stored in the session except ids and quantities.
- Adding an item increments the existing quantity for that variant.
- Updating an item replaces the stored quantity; quantities less than or equal to zero remove the line.
- Quantities are capped to the lower of stock and `5`.
- Variants that no longer exist or no longer have stock are ignored or removed when the cart is normalized.
- The page includes quantity steppers, update forms, remove forms, a summary card, and a link to `/checkout`.

---

## Persistence
The current repo does not persist carts in database tables. It only queries product data needed to enrich the session cart.

```sql
SELECT
    v.id AS variant_id,
    v.size_ml,
    v.price,
    v.stock,
    p.id AS product_id,
    p.slug AS product_slug,
    p.name AS product_name,
    b.name AS brand_name,
    p.concentration_label,
    COALESCE(pvi.image_url, pi.image_url) AS image_url
FROM product_variants v
INNER JOIN products p ON p.id = v.product_id
INNER JOIN brands b ON b.id = p.brand_id
LEFT JOIN product_variant_images pvi ON pvi.product_variant_id = v.id AND pvi.position = 0
LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.position = 0
WHERE v.id IN (...);
```

---

## Responses
- `GET /cart`: render `cart/index`
- `POST /cart/add`: `302` redirect to `/cart`
- `POST /cart/update`: `302` redirect to `/cart`
- `POST /cart/remove`: `302` redirect to `/cart`
- invalid cart input: `400 Invalid cart input`
- invalid CSRF: `403 Invalid CSRF token`

---

## Security
- CSRF protection on every mutating request
- prices and stock are reloaded from the database
- cart mutations only accept positive numeric variant ids
