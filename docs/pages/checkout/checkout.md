# Checkout

## Purpose

Convert a user's cart into an order, ensuring stock consistency and payment processing.

---

## Route

```bash
GET  /checkout
POST /checkout
```

---

## Page Data

* cart items:

  * product name
  * size (ml)
  * price
  * quantity
  * subtotal
* total amount
* shipping address (selected or new)

---

## Request Flow

![Checkout Flow](../../flows/checkout/checkout.png)

---

## Controller

```php
CheckoutController::show()
CheckoutController::checkout()
```

---

## Service Layer

```php
CheckoutService::checkout(int $userId, int $addressId): Order
```

---

## Responsibilities

* validate cart
* validate stock
* create order
* create order items (snapshots)
* handle transaction
* create payment record
* clear cart

---

## Preconditions

* user must be authenticated
* cart must not be empty
* valid shipping address required

---

## Validation Rules

* address_id

> - required
> - belongs to user

---

## Database Actions (Transactional)

### Start transaction

```sql
START TRANSACTION;
```

---

### Lock variants (critical)

```sql
SELECT id, stock, price
FROM product_variants
WHERE id IN (?)
FOR UPDATE;
```

---

### Validate stock

* ensure requested quantity ≤ stock
* if not → rollback + return error

---

### Create order

```sql
INSERT INTO orders (
    public_id,
    user_id,
    shipping_address_id,
    status,
    subtotal_amount,
    shipping_cost,
    total_amount
) VALUES (?, ?, ?, 'pending', ?, ?, ?);
```

---

### Create order items (snapshots)

```sql
INSERT INTO order_items (
    order_id,
    product_variant_id,
    product_name_snapshot,
    size_ml_snapshot,
    quantity,
    price_at_purchase
) VALUES (?, ?, ?, ?, ?, ?);
```

---

### Decrease stock

```sql
UPDATE product_variants
SET stock = stock - ?
WHERE id = ?;
```

---

### Create payment

```sql
INSERT INTO payments (
    order_id,
    provider,
    status,
    amount,
    currency
) VALUES (?, ?, 'pending', ?, 'EUR');
```

---

### Commit

```sql
COMMIT;
```

---

### On error

```sql
ROLLBACK;
```

---

## Concurrency Handling

* use `SELECT ... FOR UPDATE`
* prevents overselling
* ensures atomic checkout

---

## Cart Handling

* clear cart after successful checkout

```sql
DELETE FROM cart_items WHERE cart_id = ?;
```

---

## Response

### Success

```txt
302 Redirect ⟶ /order/success
```

---

### Errors

Return to `/checkout` with:

* error message (e.g. "Product out of stock")
* updated cart state

---

## Security

* transaction required
* validate ownership of address
* never trust client-side prices
* CSRF protection required

---

## Payment Handling

* initial status: `pending`
* external provider updates status later (e.g. webhook)

---

## Future Extensions

* Stripe / PayPal integration
* webhook handling
* retry payments
* coupons / discounts
* shipping calculation

---

## View Requirements

* cart summary
* address selection
* total price
* confirm order button
