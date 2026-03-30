# Cart

## Purpose

Allow users (guest or authenticated) to manage products before checkout.

---

## Route

```bash
GET    /cart
POST   /cart/items        # add item
PATCH  /cart/items        # update quantity
DELETE /cart/items        # remove item
```

---

## Page Data

* cart items:

  * product name
  * size (ml)
  * price
  * quantity
  * subtotal
* total price

---

## Request Flo

### Add Item

![Cart Add Flow](../../flows/cart/cart-add-item-flow.png)

---

### Update Item

![Cart Update Flow](../../flows/cart/cart-update-item-flow.png)

---

### Remove Item

![Cart Remove Flow](../../flows/cart/cart-delete-item-flow.png)

---

## Controller

```php
CartController::show()
CartController::addItem()
CartController::updateItem()
CartController::removeItem()
```

---

## Service Layer

```php
CartService::getCart(): Cart
CartService::addItem(int $variantId, int $quantity): void
CartService::updateItem(int $variantId, int $quantity): void
CartService::removeItem(int $variantId): void
```

---

## Responsibilities

* retrieve cart (session or user)
* add/update/remove items
* validate stock availability
* calculate totals
* merge guest cart on login

---

## Cart Ownership Logic

* guest ⟶ identified by `session_id`
* logged user ⟶ linked via `user_id`

Rules:

* guest adds items ⟶ stored using session cart
* on login ⟶ merge guest cart into user cart

---

## Validation Rules

* variant_id

> - required
> - exists in `product_variants`

* quantity

> - required
> - integer ≥ 1

---

## Database Actions

### Get cart

```sql
SELECT * FROM carts 
WHERE user_id = ? OR session_id = ?
LIMIT 1;
```

---

### Get cart items

```sql
SELECT ci.*, pv.price, pv.size_ml, p.name
FROM cart_items ci
JOIN product_variants pv ON ci.product_variant_id = pv.id
JOIN products p ON pv.product_id = p.id
WHERE ci.cart_id = ?;
```

---

### Add item

```sql
INSERT INTO cart_items (cart_id, product_variant_id, quantity)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity);
```

---

### Update item

```sql
UPDATE cart_items
SET quantity = ?
WHERE cart_id = ? AND product_variant_id = ?;
```

---

### Remove item

```sql
DELETE FROM cart_items
WHERE cart_id = ? AND product_variant_id = ?;
```

---

## Stock Handling

* check stock before adding/updating
* do NOT reserve stock at cart stage
* final validation happens at checkout

---

## Session Handling

* store `session_id` in cookie
* use it to retrieve guest cart
* after login → associate cart with `user_id`

---

## Response

### GET /cart

* render cart page

---

### POST /cart/items

```
302 Redirect ⟶ /cart
```

---

### PATCH /cart/items

```
200 OK (or redirect)
```

---

### DELETE /cart/items

```
200 OK (or redirect)
```

---

## Security

* validate ownership of cart
* CSRF protection required
* never trust client-side price

---

## Future Extensions

* AJAX cart updates
* persistent carts (long-lived)

---

## View Requirements

* list of items
* quantity controls
* remove button
* total price
* checkout button
