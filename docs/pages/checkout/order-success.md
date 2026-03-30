# Order Success

## Purpose

Confirm that the order has been successfully placed and provide basic order details.

---

## Route

```bash
GET /order/success
```

---

## Page Data

* order public_id
* total amount
* order status
* optional: summary of items

---

## Preconditions

* user must be authenticated
* must come from successful checkout

---

## Request Flow

![Order Success Flow](../../flows/checkout/order-success.png)

---

## Controller

```php
OrderController::success()
```

---

## Service Layer

```php
OrderService::getLastOrder(int $userId): Order
```

---

## Responsibilities

* retrieve last created order for user
* display confirmation details
* prevent direct access without valid order

---

## Database Actions

### Get last order

```sql
SELECT *
FROM orders
WHERE user_id = ?
ORDER BY created_at DESC
LIMIT 1;
```

---

## Response

### Success

* render confirmation page with:

  * order ID
  * total amount
  * status

---

### Errors

If no valid order:

```
302 Redirect ⟶ /
```

---

## Security

* ensure order belongs to user
* avoid exposing other users' orders
* do not rely on client-provided order ID

---

## UX Notes

* show success message ("Order placed successfully")
* show order reference (public_id)
* provide link to:

  * `/profile/orders`
  * continue shopping (`/`)

---

## Future Extensions

* email confirmation
* detailed order summary
* estimated delivery date

---

## View Requirements

* success message
* order summary
* navigation links
