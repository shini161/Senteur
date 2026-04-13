# Register

## Purpose
Creates a storefront customer account and signs the new user in immediately after a successful registration.

---

## Routes
```bash
GET  /register
POST /register
```

---

## Access Rules
- Guest-only page
- Authenticated users are redirected to `/`

---

## Form Fields
| Field            | Type     | Required | Notes |
| ---------------- | -------- | -------- | ----- |
| username         | text     | ✅       | must be unique |
| email            | email    | ✅       | must be unique |
| phone            | text     | ❌       | stored as submitted |
| password         | password | ✅       | hashed before storage |
| confirm_password | password | ✅       | must match `password` |

---

## UI Elements
- single-page registration form
- error banner above the form
- link to `/login`

---

## Request Flow

![Register Flow](../../flows/auth/register.png)

---

## Controller
```php
AuthController::showRegister()
AuthController::register()
```

---

## Service Layer
```php
AuthService::register(array $data): array
```

---

## Current Behavior
- `POST /register` requires a valid CSRF token.
- The controller checks that `password` and `confirm_password` match before calling the service.
- The service rejects duplicate emails and duplicate usernames.
- A new `public_id` is generated from a 10-character hexadecimal string.
- Passwords are stored with `password_hash(..., PASSWORD_DEFAULT)`.
- Successful registration calls `Auth::login($user)` and redirects to `/`.

---

## Validation In The Current Repo
- `username` must be present and unique
- `email` must be present and unique
- `confirm_password` must match `password`
- `phone` is optional

The current implementation does not add extra server-side password complexity or phone-format validation beyond these checks.

---

## Persistence
```sql
INSERT INTO users (
    public_id,
    username,
    email,
    phone,
    password_hash
) VALUES (
    :public_id,
    :username,
    :email,
    :phone,
    :password_hash
);
```

---

## Session Handling
After the user is created, the auth helper stores the new account in the session:

```php
$_SESSION['user_id'] = $user['id'];
```

---

## Responses
- Success: `302` redirect to `/`
- Validation failure: re-render `auth/register` with old `username`, `email`, and `phone`
- Invalid CSRF: `403 Invalid CSRF token`

---

## Security
- CSRF protection on `POST /register`
- password hashes generated with PHP's `password_hash()`
- uniqueness checks for both email and username
