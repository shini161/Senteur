# Orders

## Purpose
Shows the authenticated user's order history with status filtering and pagination.

---

## Route
```bash
GET /orders
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Query Parameters
| Param  | Type   | Required | Notes |
| ------ | ------ | -------- | ----- |
| page   | int    | ❌       | 1-based page number |
| status | string | ❌       | allowed values: `pending`, `processing`, `shipped`, `delivered` |

---

## Page Data
- paginated order summaries
- current status filter
- current page
- page size
- total order count
- total page count

Each order summary includes:
- `public_id`
- `status`
- `created_at`
- `items_count`
- `total_amount`

---

## Request Flow

![Orders Flow](../../flows/user/orders-list.png)

---

## Controller
```php
OrderController::index()
```

---

## Service Layer
```php
OrderService::getUserOrdersPaginated(
    int $userId,
    ?string $status,
    int $page,
    int $perPage
): array
```

---

## Current Behavior
- The orders page uses `5` orders per page.
- Invalid or unsupported `status` values are ignored and treated as no filter.
- Results are sorted by `created_at DESC`.
- Each order card links to `/orders/{publicId}`.
- The filter bar offers `All`, `Pending`, `Processing`, `Shipped`, and `Delivered`.
- Pagination preserves the selected status filter.
- Empty results show a `Continue shopping` link back to `/products`.

---

## Persistence
The page reads from `orders` and aggregates `order_items` quantities into `items_count`.

```sql
SELECT
    o.id,
    o.public_id,
    o.status,
    o.subtotal_amount,
    o.shipping_cost,
    o.total_amount,
    o.created_at,
    COALESCE(SUM(oi.quantity), 0) AS items_count
FROM orders o
LEFT JOIN order_items oi ON oi.order_id = o.id
WHERE o.user_id = :user_id
  AND (:status IS NULL OR o.status = :status)
GROUP BY ...
ORDER BY o.created_at DESC
LIMIT :limit OFFSET :offset;
```

---

## Responses
- Success: render `orders/index`
- Unauthenticated request: redirect to `/login`

---

## Security
- order queries are scoped to the authenticated `user_id`
- unsupported status filters are discarded before querying
