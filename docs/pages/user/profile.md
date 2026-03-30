# Profile

## Purpose

Provide users with an overview of their account and quick access to account-related sections.

---

## Route

```bash
GET /profile
```

---

## Page Data

* user info:

  * username
  * email
  * phone (optional)
* quick links:

  * orders
  * addresses

---

## Request Flow

![Profile Flow](../../flows/user/profile.png)

---

## Controller

```php
ProfileController::index()
```

---

## Service Layer

```php
UserService::getById(int $userId): User
```

---

## Responsibilities

* retrieve user data
* display account overview
* provide navigation to user sections

---

## Database Actions

### Get user

```sql
SELECT username, email, phone
FROM users
WHERE id = ?
LIMIT 1;
```

---

## Response

### Success

* render profile page with:

  * user info
  * navigation links

---

## Security

* user must be authenticated
* only allow access to own profile

---

## Navigation

Links to:

```txt
/profile/orders
/profile/addresses
```

---

## UX Notes

* simple dashboard layout
* display key user info
* clear navigation to main sections

---

## Future Extensions

* edit profile info
* change password
* account settings
* profile picture

---

## View Requirements

* user info display
* navigation links
