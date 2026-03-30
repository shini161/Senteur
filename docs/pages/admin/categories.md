# Admin - Categories

## Purpose

Allow administrators to manage product categories used to organize products.

---

## Route

```bash
GET    /admin/categories
POST   /admin/categories
PATCH  /admin/categories/{id}
DELETE /admin/categories/{id}
```

---

## Page Data

* list of categories:

  * id
  * name
  * slug
  * created_at

---

## Request Flow

### Create / Update Category

![Create or Update Category](../../flows/admin/category_save.png)

---

### Delete Category

![Delete Category](../../flows/admin/category_delete.png)

---

## Controller

```php
Admin\\CategoryController::index()
Admin\\CategoryController::store()
Admin\\CategoryController::update(int $id)
Admin\\CategoryController::delete(int $id)
```

---

## Service Layer

```php
CategoryService::getAll(): array
CategoryService::create(array $data): void
CategoryService::update(int $id, array $data): void
CategoryService::delete(int $id): void
```

---

## Responsibilities

* manage categories (CRUD)
* ensure unique slug
* prevent deletion if in use
* normalize category data

---

## Validation Rules

* name

> - required
> - max: 150

---

## Database Actions

### Get categories

```sql
SELECT id, name, slug, created_at
FROM categories
ORDER BY created_at DESC;
```

---

### Create category

```sql
INSERT INTO categories (name, slug)
VALUES (?, ?);
```

---

### Update category

```sql
UPDATE categories
SET name = ?, slug = ?
WHERE id = ?;
```

---

### Delete category

```sql
DELETE FROM categories
WHERE id = ?;
```

---

## Slug Handling

* generated from name (lowercase, hyphens)
* must be unique

Example:

```text
"Summer Scents" → "summer-scents"
```

---

## Security

* admin authentication required
* role-based access (admin only)
* CSRF protection required

---

## Response

### Success

```text
302 Redirect → /admin/categories
```

---

### Errors

* validation errors
* duplicate slug
* category in use

---

## UX Notes

* simple table of categories
* create/edit form
* delete button with confirmation

---

## Future Extensions

* hierarchical categories (parent_id)
* category images
* drag & drop ordering

---

## View Requirements

* categories list
* create/edit form
* delete action
