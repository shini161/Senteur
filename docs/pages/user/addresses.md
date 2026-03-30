# Addresses

## Purpose

Allow users to manage their shipping addresses for checkout.

---

## Route

```bash
GET    /profile/addresses
POST   /profile/addresses        # create
PATCH  /profile/addresses/{id}   # update
DELETE /profile/addresses/{id}   # delete
```

---

## Page Data

* list of user addresses:

  * full_name
  * address_line
  * city
  * postal_code
  * country
  * is_default

---

## Request Flow

### Create / Update Address

![Address Save Flow](../../flows/user/address-save.png)

---

### Delete Address

![Address Delete Flow](../../flows/user/address-delete.png)


---

## Controller

```php
AddressController::index()
AddressController::store()
AddressController::update(int $id)
AddressController::delete(int $id)
```

---

## Service Layer

```php
AddressService::getAll(int $userId): array
AddressService::create(int $userId, array $data): void
AddressService::update(int $userId, int $addressId, array $data): void
AddressService::delete(int $userId, int $addressId): void
```

---

## Responsibilities

* retrieve user addresses
* create/update/delete addresses
* ensure ownership (user_id)
* manage default address

---

## Validation Rules

* full_name

> - required
> - max: 150

* address_line

> - required
> - max: 255

* city

> - required
> - max: 100

* postal_code

> - required
> - max: 20

* country

> - required
> - max: 100

---

## Database Actions

### Get addresses

```sql
SELECT *
FROM user_addresses
WHERE user_id = ?
ORDER BY is_default DESC, created_at DESC;
```

---

### Create address

```sql
INSERT INTO user_addresses (
    user_id,
    full_name,
    address_line,
    city,
    postal_code,
    country,
    is_default
) VALUES (?, ?, ?, ?, ?, ?, ?);
```

---

### Update address

```sql
UPDATE user_addresses
SET full_name = ?, address_line = ?, city = ?, postal_code = ?, country = ?
WHERE id = ? AND user_id = ?;
```

---

### Delete address

```sql
DELETE FROM user_addresses
WHERE id = ? AND user_id = ?;
```

---

## Default Address Logic

* only one address can be `is_default = TRUE`

### When setting a new default:

```sql
UPDATE user_addresses
SET is_default = FALSE
WHERE user_id = ?;

UPDATE user_addresses
SET is_default = TRUE
WHERE id = ? AND user_id = ?;
```

---

## Response

### GET /profile/addresses

* render address list

---

### POST /profile/addresses

```txt
302 Redirect ⟶ /profile/addresses
```

---

### PATCH /profile/addresses/{id}

```txt
302 Redirect ⟶ /profile/addresses
```

---

### DELETE /profile/addresses/{id}

```txt
302 Redirect ⟶ /profile/addresses
```

---

## Security

* user must be authenticated
* enforce `user_id` ownership on all operations
* CSRF protection required

---

## UX Notes

* allow selecting default address
* show clear address formatting
* allow editing inline or via form
* highlight default address

---

## Future Extensions

* multiple address types (billing/shipping)
* address validation (API)
* country dropdown / normalization

---

## View Requirements

* address list
* create/edit form
* delete button
* default selector
