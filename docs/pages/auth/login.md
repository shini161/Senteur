# Login

## Purpose
Authenticates storefront customers and starts the session used by the account area, checkout, and order pages.

---

## Routes
```bash
GET  /login
POST /login
```

---

## Access Rules
- Guest-only page
- Authenticated users are redirected to `/`

---

## Form Fields
| Field    | Type     | Required | Notes |
| -------- | -------- | -------- | ----- |
| email    | email    | ✅       | repopulated after a failed attempt |
| password | password | ✅       | not repopulated |

---

## UI Elements
- error banner for invalid credentials
- link to `/register`

---

## Request Flow

![Login Flow](../../flows/auth/login.png)

---

## Controller
```php
AuthController::showLogin()
AuthController::login()
```

---

## Service Layer
```php
AuthService::login(string $email, string $password): array
```

---

## Current Behavior
- `POST /login` requires a valid CSRF token.
- Empty `email` or `password` re-renders the form with the generic message `Invalid credentials`.
- Authentication looks up the user by email and verifies the stored hash with `password_verify()`.
- Successful login calls `Auth::login($user)` and redirects to `/`.
- Failed authentication keeps the response generic, whether the email is missing from the database or the password is wrong.

---

## Persistence
```sql
SELECT *
FROM users
WHERE email = :email
LIMIT 1;
```

---

## Session Handling
On success the auth helper stores the authenticated user id:

```php
$_SESSION['user_id'] = $user['id'];
```

---

## Responses
- Success: `302` redirect to `/`
- Invalid credentials: re-render `auth/login` with the old email value
- Invalid CSRF: `403 Invalid CSRF token`

---

## Security
- CSRF protection on `POST /login`
- generic credential errors to avoid user enumeration
- password validation uses PHP's `password_verify()`
