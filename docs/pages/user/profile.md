# Profile

## Purpose
Provides the signed-in user's account overview together with links to address management, order history, and logout.

---

## Route
```bash
GET /profile
```

Related account action shown on the page:

```bash
POST /logout
```

---

## Access Rules
- authenticated users only
- unauthenticated requests are redirected to `/login`

---

## Page Data
The page renders the authenticated user injected by the shared controller layout flow:
- `username`
- `email`
- `phone`
- `created_at`

---

## Request Flow

![Profile Flow](../../flows/user/profile.png)

---

## Controller
```php
ProfileController::index()
```

---

## Data Source
There is no dedicated profile service in the current repo.

`ProfileController::index()` only renders the page, and `App\Core\Controller::render()` injects:

```php
Auth::user()
```

That user payload is loaded through:

```php
UserRepository::findById(int $id): ?array
```

---

## Current Behavior
- The page shows username, email, formatted phone number, and a human-readable "Member since" date.
- It links to `/addresses` and `/orders`.
- Logout is handled by a POST form that includes a CSRF token and submits to `/logout`.
- If the auth session exists but the user record cannot be loaded, the view falls back to `User not found.`

---

## Persistence
```sql
SELECT
    id,
    public_id,
    role,
    username,
    email,
    phone,
    email_verified_at,
    created_at,
    updated_at
FROM users
WHERE id = :id
LIMIT 1;
```

---

## Responses
- Success: render `user/profile`
- Unauthenticated request: redirect to `/login`

---

## Security
- authentication required
- logout form uses CSRF protection
- profile data comes from the authenticated session, not from a user id in the URL
