# Login

## Purpose
Allows users to authenticate and access their account.

---

## Route
```bash
GET /login
POST /login
```

---

## Form Fields
| Field            | Type   | Required | Notes                       |
| ---------------- | ------ | -------- | --------------------------- |
| email            | string | ✅       | valid email format          |
| password         | string | ✅       |                             |

---

## Additional UI Elements
- Link to `/register`
- Link to password reset

---

## Validation Rules
- email
> - required
> - valid email

- password
> - required

---

## Request Flow

![Login Flow](../../flows/login.png)

---

## Authentication Flow

- retrieve user by email
- if not found → treat as invalid credentials
- verify password using `password_verify`
- on success → return user

---

## Controller

```php
AuthController::showLogin()
AuthController::login()
```

---

## Service Layer
```php
AuthService::login(string $email, string $password): User
```

---

## Responsibilities
- validate input
- verify credentials
- create session
- handle errors
- prevent user enumeration (generic errors)

---

## Database Actions
### Select user
```sql
SELECT * FROM users WHERE email = ? LIMIT 1;
```

### Notes
- use index on `email`
- always return generic error on failure

---

## Session Handling
On success:
```php
$_SESSION['user_id'] = $user->id;
```

---

## Response
### Success
```
302 Redirect ⟶ /
```

### Errors
Return to `/login` with:
- old input (email only)
- generic error message ("Invalid credentials")

---

## Security
- Use `password_verify()`
- Don't reveal whether email exists
- CSRF token required
- Rate-limiting

---

## Future Extensions
- remember me (persistent cookie + token storage)
- password reset
- login with username

---

## View Requirements
- form
- error display
- old input persistence