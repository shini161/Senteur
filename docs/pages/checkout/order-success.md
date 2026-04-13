# Payment Confirmation

## Purpose
Shows the post-Stripe confirmation state for the current user's order and reflects the local payment status stored by the app.

---

## Route
```bash
GET /checkout/success?session_id={CHECKOUT_SESSION_ID}
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Query Parameters
| Param      | Type   | Required | Notes |
| ---------- | ------ | -------- | ----- |
| session_id | string | ✅       | Stripe Checkout session id returned in the success URL |

---

## Page Data
- `orderPublicId`
- `paymentStatus`
- `error`

---

## Request Flow

![Order Success Flow](../../flows/checkout/order-success.png)

---

## Controller
```php
CheckoutController::success()
```

---

## Service Layer
```php
PaymentService::getCheckoutSuccessData(int $userId, string $stripeSessionId): ?array
CartService::clear(): void
```

---

## Current Behavior
- The page reads `session_id` from the query string.
- If `session_id` is missing, the app returns `400` and renders an error message.
- The payment lookup is done through the local `payments` table using `stripe_session_id`.
- The linked order must belong to the current authenticated user.
- If the payment record cannot be resolved for the current user, the app returns `404` and renders an error message.
- If the payment status is `paid`, the cart is cleared.
- The page shows a different confirmation message for `paid` vs still-pending payment states.
- When an order id is available, the page links to `/orders/{publicId}` and also offers a link back to `/products`.

---

## Payment Timing Notes
The success page reads the app's local payment status. Because Stripe webhook processing happens separately, a user can land on this page before the webhook has finished and temporarily see a pending payment state.

---

## Persistence
```sql
SELECT *
FROM payments
WHERE stripe_session_id = :stripe_session_id
LIMIT 1;
```

The linked order is then loaded and checked against the current `user_id`.

---

## Responses
- Success: render `checkout/success`
- Missing `session_id`: `400` with an error message
- Unknown or unauthorized payment/order pair: `404` with an error message

---

## Security
- authentication required
- order ownership verified before showing confirmation data
- the page does not trust an order id passed directly by the browser
