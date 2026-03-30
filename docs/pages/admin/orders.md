# Admin - Orders

## Purpose

Allow administrators to view and manage all orders in the system, with filtering, status monitoring, and access to order details.

---

## Route

```bash
GET /admin/orders
```

---

## Query Parameters

| Param  | Type   | Required | Notes                             |
| ------ | ------ | -------- | --------------------------------- |
| page   | int    | ❌        | pagination page                   |
| status | string | ❌        | filter by order status            |
| q      | string | ❌        | search by public_id or user email |

---

## Page Data

* list of orders:

  * public_id
  * user (username/email)
  * status
  * total_amount
  * created_at
* pagination info
* active filters

---

## Request Flow

### Orders Listing

![Orders Listing Flow](../../flows/admin/orders-list.png)

---

## Controller

```php
Admin\\OrderController::index()
```

---

## Service Layer

```php
OrderService::getAll(array $filters): array
```

---

## Responsibilities

* retrieve all orders
* apply filters (status)
* handle search (public_id / email)
* paginate results
* return minimal order data

---

## Validation Rules

* page

> - integer ≥ 1

* status

> - one of: pending, processing, shipped, delivered, cancelled

* q

> - string (sanitized)

---

## Database Actions

### Base query

```sql
SELECT
    o.public_id,
    o.status,
    o.total_amount,
    o.created_at,
    u.username,
    u.email
FROM orders o
JOIN users u ON u.id = o.user_id
```

---

### Search (optional)

```sql
WHERE o.public_id LIKE ?
   OR u.email LIKE ?
```

---

### Status filter (optional)

```sql
AND o.status = ?
```

---

### Sorting

```sql
ORDER BY o.created_at DESC
```

---

### Pagination

```sql
LIMIT ? OFFSET ?
```

---

## Response

### Success

* render orders list with:

  * orders
  * filters
  * pagination

---

## Navigation

Each order links to:

```txt
/admin/orders/{public_id}
```

---

## Security

* admin authentication required
* role-based access (admin only)
* sanitize search input
* use prepared statements

---

## Performance Considerations

* indexes:

  * `orders(created_at)`
  * `orders(status)`
  * `orders(public_id)`
  * `users(email)`
* avoid N+1 queries (use JOIN)
* limit selected columns

---

## UX Notes

* table of orders
* status badges
* search bar
* filter by status
* pagination controls
* quick access to order details

---

## Future Extensions

* date range filtering
* bulk actions (update status)
* export (CSV)
* advanced search

---

## View Requirements

* orders table
* filters (status, search)
* pagination controls
