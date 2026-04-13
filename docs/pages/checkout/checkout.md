# Checkout

## Purpose
Lets an authenticated user review the current cart, choose a saved shipping address, create a pending order snapshot, and continue into Stripe Checkout.

---

## Routes
```bash
GET  /checkout
POST /checkout
```

Related return pages in the same flow:

```bash
GET /checkout/success
GET /checkout/cancel
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Page Data
- checkout cart items from `CartService`
- total amount
- saved shipping addresses for the current user
- selected `shipping_address_id` on validation errors

---

## Request Flow

![Checkout Flow](../../flows/checkout/checkout.png)

---

## Controller
```php
CheckoutController::index()
CheckoutController::store()
CheckoutController::success()
CheckoutController::cancel()
```

---

## Service Layer
```php
CheckoutService::getCheckoutData(int $userId): array
CheckoutService::placeOrder(int $userId, int $addressId): string
CheckoutService::getPlacedOrder(int $userId, string $publicId): ?array
PaymentService::createCheckoutSession(array $order): string
```

---

## Current Behavior
- `GET /checkout` loads the current cart snapshot and the user's saved addresses.
- If the cart is empty, the page renders with the error `Your cart is empty.`
- If the user has no saved addresses, the page renders with the error `You need at least one saved address before checkout.`
- `POST /checkout` requires a valid CSRF token and a `shipping_address_id` that belongs to the current user.
- `CheckoutService::placeOrder()` validates the cart snapshot, confirms stock is still sufficient, computes totals, and inserts the order plus order-item snapshots with status `pending`.
- Shipping is currently fixed at `0.00`, so `total_amount = subtotal_amount`.
- After the order is created, `PaymentService::createCheckoutSession()` creates or reuses a local pending `payments` row and opens a Stripe Checkout session.
- The browser is redirected to the Stripe-hosted checkout URL returned by Stripe.

---

## Order And Payment Lifecycle
- order creation happens before payment confirmation
- stock is not decremented during `POST /checkout`
- Stripe webhook processing marks the payment as `paid`, moves the order to `processing`, and decrements stock in a transaction
- the cart is not cleared during `POST /checkout`
- the cart is cleared later on `/checkout/success` only when the local payment status is already `paid`

---

## Persistence
Checkout writes to:
- `orders`
- `order_items`
- `payments`

The current order snapshot includes:
- `public_id`
- `user_id`
- `shipping_address_id`
- `status`
- `subtotal_amount`
- `shipping_cost`
- `total_amount`

Each order item stores purchase-time snapshots such as:
- product name
- brand name
- concentration label
- image URL
- size
- quantity
- unit price

---

## Responses
- Success: `302` redirect to the Stripe Checkout session URL
- Validation or business-rule failure: re-render `checkout/index` with the current cart, addresses, error message, and old `shipping_address_id`
- Invalid CSRF: `403 Invalid CSRF token`
- Cancelled Stripe flow: Stripe returns the user to `/checkout/cancel?order={publicId}`

---

## Security
- authentication required
- CSRF protection on `POST /checkout`
- shipping address ownership is enforced
- cart data is revalidated server-side before order creation
- Stripe credentials are loaded from `.env`
