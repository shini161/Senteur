# Addresses

## Purpose
Lets authenticated users manage the saved shipping addresses used during checkout.

---

## Routes
```bash
GET  /addresses
POST /addresses
POST /addresses/default
POST /addresses/delete
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Page Data
- all saved addresses for the current user
- `can_add_address` flag
- validation error message for the creation form
- old form values after a failed create request

Each address row includes:
- `id`
- `full_name`
- `address_line`
- `city`
- `postal_code`
- `country`
- `is_default`
- `created_at`

---

## Request Flow

### Create Address
![Address Save Flow](../../flows/user/address-save.png)

### Delete Address
![Address Delete Flow](../../flows/user/address-delete.png)

---

## Controller
```php
AddressController::index()
AddressController::store()
AddressController::setDefault()
AddressController::delete()
```

---

## Service Layer
```php
AddressService::getAllForUser(int $userId): array
AddressService::createForUser(int $userId, array $data): void
AddressService::setDefaultForUser(int $id, int $userId): void
AddressService::deleteForUser(int $id, int $userId): void
```

---

## Current Behavior
- The page shows saved addresses on the left and the creation form on the right.
- Addresses are ordered with the default address first, then newest first.
- Users can save up to `10` addresses.
- The form requires `full_name`, `address_line`, `city`, `postal_code`, and `country`.
- There is no edit route in the current repo; addresses can be created, deleted, or promoted to default.
- The first saved address automatically becomes the default.
- Checking `Set as default address` during creation clears the previous default and makes the new row the sole default.
- Deleting the current default automatically promotes the oldest remaining address to default.

---

## Validation Rules
- all address fields are required
- `full_name` max length: `150`
- `address_line` max length: `255`
- `city` max length: `100`
- `postal_code` max length: `20`
- `country` max length: `100`
- maximum addresses per user: `10`

---

## Persistence
The address book is backed by `user_addresses`.

```sql
SELECT
    id,
    user_id,
    full_name,
    address_line,
    city,
    postal_code,
    country,
    is_default,
    created_at
FROM user_addresses
WHERE user_id = :user_id
ORDER BY is_default DESC, created_at DESC, id DESC;
```

Default changes are handled by clearing the user's current default before setting the new one.

---

## Responses
- `GET /addresses`: render `user/addresses`
- `POST /addresses`: `302` redirect to `/addresses` on success, otherwise re-render the page with an error and old input
- `POST /addresses/default`: `302` redirect to `/addresses`
- `POST /addresses/delete`: `302` redirect to `/addresses`
- invalid CSRF on any POST route: `403 Invalid CSRF token`

---

## Security
- authentication required on every route
- address ownership is enforced server-side
- CSRF protection on create, set-default, and delete actions
