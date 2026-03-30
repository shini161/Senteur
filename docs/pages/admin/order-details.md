# Admin - Order Details

## Purpose

Allow administrators to view and manage a specific order, including items, status, and customer information.

---

## Route

```bash
GET   /admin/orders/{public_id}
PATCH /admin/orders/{public_id}   # update status
```

---

## Parameters

| Param     | Type   | Required | Notes                   |
| --------- | ------ | -------- | ----------------------- |
| public_id | string | ✅        | unique order identifier |

---

## Page Data

* order:

  * public_id
  * status
  * subtotal_amount
  * shipping_cost
  * total_amount
  * created_at
* customer:

  * username
  * email
* shipping address:

  * full_name
  * address_line
  * city
  * postal_code
  * country
* items:

  * product_name_snapshot
  * size_ml_snapshot
  * quantity
  * price_at_purchase
  * subtotal (computed)

---

## Request Flow

### View Order

![View Order Flow](../../flows/admin/order-view.png)

---

### Update Status

![Update Status Flow](../../flows/admin/order-update-status.png)

---

## Controller

```php
Admin\\OrderController::show(string $publicId)
Admin\\OrderController::updateStatus(string $publicId)
```

---

## Service Layer

```php
OrderService::getByPublicId(string $publicId): Order
OrderService::updateStatus(string $publicId, string $status): void
```

---

## Responsibilities

* retrieve order by public_id
* load related data (items, address, user)
* update order status
* enforce valid status transitions

---

## Validation Rules

* public_id

> - required
> - exists in `orders`

* status

> - required
> - one of: pending, processing, shipped, delivered, cancelled

---

## Status Transitions

Allowed transitions:

```text
pending → processing → shipped → delivered
pending → cancelled
processing → cancelled
```

---

## Database Actions

### Get order

```sql
SELECT *
FROM orders
WHERE public_id = ?
LIMIT 1;
```

---

### Get order items

```sql
SELECT
    product_name_snapshot,
    size_ml_snapshot,
    quantity,
    price_at_purchase
FROM order_items
WHERE order_id = ?;
```

---

### Get shipping address

```sql
SELECT
    full_name,
    address_line,
    city,
    postal_code,
    country
FROM user_addresses
WHERE id = ?;
```

---

### Get customer

```sql
SELECT username, email
FROM users
WHERE id = ?;
```

---

### Update status

```sql
UPDATE orders
SET status = ?
WHERE public_id = ?;
```

---

## Response

### Success

* render order details page
* or redirect after update

```text
302 Redirect → /admin/orders/{public_id}
```

---

### Errors

* 404 if not found
* invalid status transition

---

## Security

* admin authentication required
* role-based access (admin only)
* validate status transitions server-side

---

## UX Notes

* display order status clearly
* allow status update via dropdown or buttons
* highlight important states (pending, cancelled)
* show customer info for context

---

## Future Extensions

* refund handling
* payment status management
* order notes (internal)
* shipment tracking integration

---

## View Requirements

* order summary
* items list
* customer info block
* address block
* status control (update)
