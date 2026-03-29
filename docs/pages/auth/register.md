# Register
Allows a user to create an account.

---

## Route
```bash
GET /register
POST /register
```

---

## Form Fields
| Field            | Type   | Required | Notes                       |
| ---------------- | ------ | -------- | --------------------------- |
| username         | string | ✅        | unique                      |
| email            | string | ✅        | unique, valid email format  |
| phone            | string | ❌        | optional, normalized format |
| password         | string | ✅        | min 8 chars                 |
| confirm_password | string | ✅        | must match password         |

---

## Additional UI Elements

- Link to `/login`

---

## Validation Rules
- username:
> - required
> - min: 3
> - max: 50
> - unique

- email:
> - required
> - valid email
> - max: 255
> - unique

- phone:
> - optional
> - valid format (E.164 recommended)

- password:
> - required
> - min: 8
> - at least one capital letter
> - at least one lowercase letter
> - at least a number
> - at least a special symbol

- confirm_password:
> - must match password

---

## Request Flow

![Register Flow](../../flows/register.png)

---

## Controller
```php
AuthController::showRegister()
AuthController::register()
```

---

## Service Layer
```php
AuthService::register(array $data): User
```

---

## Responsabilities
- validate business rules
- hash password
- create user
- handle edge cases

---

## Database Actions
### Insert into `users`
```sql
INSERT INTO users (
    public_id,
    username,
    email,
    phone,
    password_hash
) VALUES (?, ?, ?, ?, ?);
```

### Notes
- `username UNIQUE index`
- `email UNIQUE index`
- `handle duplicate key exception (race condition safety)`
- `public_id` → random 10-char string (cryptographically secure)
- `email_verified_at` → `NULL`

--- 

## Session Handling
After successful registration:
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
Return to `/register` with:
- old input
- validation errors

---

## Security
- Use `password_hash()`
- Use PDO prepared statements
- CSRF token required
- Rate limit (optional)

---

## Future Extensions
- email verification
- phone verification
- captcha
- anti-bot/rate-limiting

---

## View Requirements
- form
- error display
- old input persistence