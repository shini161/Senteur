# Order Details

## Purpose
Shows one user-owned order using stored purchase snapshots so the page stays stable even if the product catalogue changes later.

---

## Route
```bash
GET /orders/{publicId}
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Parameters
| Param    | Type   | Required | Notes |
| -------- | ------ | -------- | ----- |
| publicId | string | ✅       | user-facing order id stored in `orders.public_id` |

---

## Page Data
- order summary
- stored order items
- shipping address snapshot via the linked address row

The rendered order includes:
- `public_id`
- `status`
- `subtotal_amount`
- `shipping_cost`
- `total_amount`
- `paid_at`
- `shipped_at`
- `delivered_at`
- `created_at`
- `full_name`
- `address_line`
- `city`
- `postal_code`
- `country`
- `items`

Each item includes:
- `product_name_snapshot`
- `brand_name_snapshot`
- `concentration_label_snapshot`
- `image_url_snapshot`
- `size_ml_snapshot`
- `quantity`
- `price_at_purchase`
- computed `line_total`

---

## Request Flow

![Order Details Flow](../../flows/user/order-details.png)

---

## Controller
```php
OrderController::show(string $publicId)
```

---

## Service Layer
```php
OrderService::getUserOrderByPublicId(int $userId, string $publicId): ?array
```

---

## Current Behavior
- The order is loaded by `public_id` and the current authenticated `user_id`.
- Order items come from `order_items` snapshot fields rather than live product tables.
- The service computes `line_total` for every item as `price_at_purchase * quantity`.
- The page shows the order status badge, placement timestamp, item list, totals, and shipping address.
- A back link returns to `/orders`.
- The page includes `Download receipt` and `Track order` buttons, but in the current repo they are display-only buttons with no route behind them.

---

## Persistence
Order details read from:
- `orders`
- `order_items`
- `user_addresses`

The main lookup is scoped to the current user:

```sql
SELECT
    o.id,
    o.public_id,
    o.status,
    o.subtotal_amount,
    o.shipping_cost,
    o.total_amount,
    o.paid_at,
    o.shipped_at,
    o.delivered_at,
    o.created_at,
    ua.full_name,
    ua.address_line,
    ua.city,
    ua.postal_code,
    ua.country
FROM orders o
LEFT JOIN user_addresses ua ON ua.id = o.shipping_address_id
WHERE o.public_id = :public_id
  AND o.user_id = :user_id
LIMIT 1;
```

---

## Responses
- Success: render `orders/show`
- Unknown or unauthorized order: `404 Order not found`
- Unauthenticated request: redirect to `/login`

---

## Security
- order ownership is enforced with `user_id`
- URLs expose only `public_id`, not internal numeric ids
- item and pricing data come from stored purchase snapshots
