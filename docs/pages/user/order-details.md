# Order Details

## Purpose

Display detailed information about a specific order, including items, pricing, status, and shipping information.

---

## Route

```bash
GET /profile/orders/{public_id}
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

![Order Details Flow](../../flows/user/order-details.png)

---

## Controller

```php
OrderController::show(string $publicId)
```

---

## Service Layer

```php
OrderService::getByPublicId(int $userId, string $publicId): Order
```

---

## Responsibilities

* retrieve order by public_id
* ensure order belongs to user
* load related items
* load shipping address
* structure data for view

---

## Validation Rules

* public_id

> - required
> - exists in `orders`

---

## Database Actions

### Get order

```sql
SELECT *
FROM orders
WHERE public_id = ? AND user_id = ?
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

## Response

### Success

* render order details page with:

  * order info
  * items list
  * shipping address

---

### Errors

If not found or unauthorized:

```txt
404 Not Found
```

---

## Security

* enforce ownership (`user_id`)
* do not expose internal order IDs
* use `public_id` for URL
* CSRF not required (GET request)

---

## Status Handling

Possible values:

```txt
pending
processing
shipped
delivered
cancelled
```

---

## UX Notes

* show order status clearly
* display items with quantities and prices
* show total breakdown (subtotal + shipping)
* allow navigation back to orders list

---

## Future Extensions

* tracking number / shipment tracking
* invoice download
* reorder button
* status timeline visualization

---

## View Requirements

* order summary
* items list
* address block
* status display
