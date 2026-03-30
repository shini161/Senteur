# Admin - Products

## Purpose

Allow administrators to view and manage all products, with filtering, search, and access to create/edit pages.

---

## Route

```bash
GET /admin/products
```

---

## Query Parameters

| Param    | Type   | Required | Notes                       |
| -------- | ------ | -------- | --------------------------- |
| page     | int    | ❌        | pagination page             |
| q        | string | ❌        | search by name/slug         |
| brand_id | int    | ❌        | filter by brand             |
| gender   | enum   | ❌        | male, female, unisex        |
| sort     | string | ❌        | newest, name_asc, name_desc |

---

## Page Data

* list of products:

  * id
  * name
  * slug
  * brand_name
  * gender
  * created_at
* pagination info
* active filters

---

## Request Flow

### Products Listing

![Products Listing Flow](../../flows/admin/product-list.png)

---

## Controller

```php
Admin\\ProductController::index()
```

---

## Service Layer

```php
ProductService::getAll(array $filters): array
```

---

## Responsibilities

* retrieve all products
* apply filters and search
* paginate results
* return minimal product data

---

## Validation Rules

* page

> - integer ≥ 1

* sort

> - one of: newest, name_asc, name_desc

* gender

> - one of: male, female, unisex

---

## Database Actions

### Base query

```sql
SELECT
    p.id,
    p.name,
    p.slug,
    p.gender,
    p.created_at,
    b.name AS brand_name
FROM products p
JOIN brands b ON b.id = p.brand_id
WHERE p.deleted_at IS NULL
```

---

### Search (optional)

```sql
AND (p.name LIKE ? OR p.slug LIKE ?)
```

---

### Filters (optional)

```sql
AND p.brand_id = ?
AND p.gender = ?
```

---

### Sorting

```sql
ORDER BY p.created_at DESC
-- or name ASC/DESC
```

---

### Pagination

```sql
LIMIT ? OFFSET ?
```

---

## Response

### Success

* render products list with:

  * products
  * filters
  * pagination

---

## Navigation

* create new product:

```txt
/admin/products/create
```

* edit product:

```txt
/admin/products/{id}/edit
```

---

## Security

* admin authentication required
* role-based access (admin only)
* sanitize input
* use prepared statements

---

## Performance Considerations

* indexes:

  * `products(brand_id)`
  * `products(gender)`
  * `products(created_at)`
* limit selected columns
* avoid N+1 queries

---

## UX Notes

* table of products
* search bar
* filters (brand, gender)
* sorting options
* pagination controls
* quick actions (edit)

---

## Future Extensions

* bulk actions (delete, publish)
* product status (draft/published)
* soft delete & restore
* advanced search

---

## View Requirements

* products table
* filters & search UI
* pagination controls
* create button
* edit actions
