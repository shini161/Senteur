# Admin - Users

## Purpose

Allow administrators to view and manage users, including roles, status, and basic account actions.

---

## Route

```bash
GET    /admin/users
PATCH  /admin/users/{id}      # update role/status
DELETE /admin/users/{id}      # optional (or soft delete)
```

---

## Query Parameters

| Param | Type   | Required | Notes                    |
| ----- | ------ | -------- | ------------------------ |
| page  | int    | ❌        | pagination page          |
| q     | string | ❌        | search by username/email |
| role  | string | ❌        | filter by role           |

---

## Page Data

* list of users:

  * id
  * username
  * email
  * role (user, admin)
  * created_at
* pagination info
* active filters

---

## Request Flow

### Users Listing

![Users Listing Flow](../../flows/admin/user-listing.png)

---

### Update User

![Update User Flow](../../flows/admin/user-update.png)

---

### Delete User (optional)

![Delete User Flow](../../flows/admin/user-delete.png)

---

## Controller

```php
Admin\\UserController::index()
Admin\\UserController::update(int $id)
Admin\\UserController::delete(int $id)
```

---

## Service Layer

```php
UserService::getAll(array $filters): array
UserService::update(int $id, array $data): void
UserService::delete(int $id): void
```

---

## Responsibilities

* retrieve users
* manage roles (user/admin)
* handle account status (optional)
* filter and search users

---

## Validation Rules

* role

> - required
> - one of: user, admin

---

## Database Actions

### Base query

```sql
SELECT id, username, email, role, created_at
FROM users
```

---

### Search (optional)

```sql
WHERE username LIKE ? OR email LIKE ?
```

---

### Role filter (optional)

```sql
AND role = ?
```

---

### Sorting

```sql
ORDER BY created_at DESC
```

---

### Pagination

```sql
LIMIT ? OFFSET ?
```

---

### Update user

```sql
UPDATE users
SET role = ?
WHERE id = ?;
```

---

### Delete user

```sql
DELETE FROM users
WHERE id = ?;
```

---

## Response

### Success

```text
302 Redirect → /admin/users
```

---

### Errors

* user not found
* invalid role
* restricted action (self-modification)

---

## Security

* admin authentication required
* role-based access (admin only)
* prevent privilege escalation issues
* sanitize input

---

## UX Notes

* table of users
* role badges
* search bar
* filter by role
* edit role action

---

## Future Extensions

* account status (active/banned)
* password reset by admin
* audit logs
* user activity tracking

---

## View Requirements

* users table
* filters & search UI
* pagination controls
* role edit action
