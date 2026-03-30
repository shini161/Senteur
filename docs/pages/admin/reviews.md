# Admin - Reviews

## Purpose

Allow administrators to moderate product reviews, including approving, rejecting, and deleting inappropriate content.

---

## Route

```bash
GET    /admin/reviews
PATCH  /admin/reviews/{id}    # update status
DELETE /admin/reviews/{id}    # delete review
```

---

## Query Parameters

| Param  | Type   | Required | Notes                          |
| ------ | ------ | -------- | ------------------------------ |
| page   | int    | ❌        | pagination page                |
| status | string | ❌        | filter by review status        |
| q      | string | ❌        | search by product/user content |

---

## Page Data

* list of reviews:

  * id
  * product_name
  * username
  * rating
  * title
  * status (pending, approved, rejected)
  * created_at
* pagination info
* active filters

---

## Request Flow

### Reviews Listing

![Reviews Listing Flow](../../flows/admin/reviews-listing.png)

---

### Update Review Status

![Update Review Status Flow](../../flows/admin/update-review-status.png)

---

### Delete Review

![Delete Review Flow](../../flows/admin/delete-review.png)

---

## Controller

```php
Admin\\ReviewController::index()
Admin\\ReviewController::update(int $id)
Admin\\ReviewController::delete(int $id)
```

---

## Service Layer

```php
ReviewService::getAll(array $filters): array
ReviewService::updateStatus(int $id, string $status): void
ReviewService::delete(int $id): void
```

---

## Responsibilities

* retrieve reviews
* moderate content (approve/reject)
* delete inappropriate reviews
* filter and search reviews

---

## Validation Rules

* status

> - required
> - one of: pending, approved, rejected

---

## Database Actions

### Base query

```sql
SELECT
    r.id,
    r.rating,
    r.title,
    r.comment,
    r.status,
    r.created_at,
    p.name AS product_name,
    u.username
FROM reviews r
JOIN products p ON p.id = r.product_id
JOIN users u ON u.id = r.user_id
```

---

### Search (optional)

```sql
WHERE r.title LIKE ? OR r.comment LIKE ?
```

---

### Status filter (optional)

```sql
AND r.status = ?
```

---

### Sorting

```sql
ORDER BY r.created_at DESC
```

---

### Pagination

```sql
LIMIT ? OFFSET ?
```

---

### Update status

```sql
UPDATE reviews
SET status = ?
WHERE id = ?;
```

---

### Delete review

```sql
DELETE FROM reviews
WHERE id = ?;
```

---

## Response

### Success

```text
302 Redirect → /admin/reviews
```

---

### Errors

* review not found
* invalid status

---

## Security

* admin authentication required
* role-based access (admin only)
* sanitize input
* use prepared statements

---

## UX Notes

* table of reviews
* status badges
* approve/reject actions
* delete with confirmation
* filters by status

---

## Future Extensions

* spam detection
* user ban integration
* review editing by admin
* bulk moderation actions

---

## View Requirements

* reviews table
* filters (status, search)
* pagination controls
* action buttons (approve/reject/delete)
