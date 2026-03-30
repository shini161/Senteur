# Orders

## Purpose

Display a list of the user’s orders with basic information and access to order details.

---

## Route

```bash
GET /profile/orders
```

---

## Query Parameters

| Param  | Type   | Required | Notes                  |
| ------ | ------ | -------- | ---------------------- |
| page   | int    | ❌        | pagination page        |
| status | string | ❌        | filter by order status |

---

## Page Data

* list of orders:

  * public_id
  * status
  * total_amount
  * created_at
* pagination info

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
OrderService::getUserOrders(int $userId, array $filters): array
```

---

## Responsibilities

* retrieve user orders
* apply filters (status)
* paginate results
* return minimal order data

---

## Validation Rules

* page

> - integer ≥ 1

* status

> - one of: pending, processing, shipped, delivered, cancelled

---

## Database Actions

### Get orders

```sql
SELECT 
    public_id,
    status,
    total_amount,
    created_at
FROM orders
WHERE user_id = ?
```

---

### Apply filter (optional)

```sql
AND status = ?
```

---

### Sorting

```sql
ORDER BY created_at DESC
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

  * order summaries
  * pagination

---

## Navigation

Each order links to:

```txt
/profile/orders/{public_id}
```

---

## Security

* enforce ownership (`user_id`)
* only return user’s own orders

---

## UX Notes

* show most recent orders first
* display clear status labels
* allow filtering by status
* show total amount prominently

---

## Future Extensions

* search by order ID
* date range filtering
* order status badges/colors
* quick reorder option

---

## View Requirements

* orders list (table or cards)
* status filter
* pagination controls
