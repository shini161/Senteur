# Admin - Product Edit

## Purpose

Allow administrators to update an existing product, including its variants, images, and notes.

---

## Route

```bash
GET   /admin/products/{id}/edit
PATCH /admin/products/{id}
```

---

## Parameters

| Param | Type | Required | Notes      |
| ----- | ---- | -------- | ---------- |
| id    | int  | ✅        | product ID |

---

## Page Data

* product:

  * name
  * slug
  * brand_id
  * fragrance_type_id
  * gender
  * description
* variants:

  * id
  * size_ml
  * price
  * stock
* notes:

  * note_id
  * note_type
* images:

  * id
  * image_url
  * position

---

## Request Flow

### Update Product

![Update Product Flow](../../flows/admin/product-update.png)

---

## Controller

```php
Admin\\ProductController::edit(int $id)
Admin\\ProductController::update(int $id)
```

---

## Service Layer

```php
ProductService::update(int $id, array $data): void
```

---

## Responsibilities

* retrieve product data
* validate updates
* ensure slug uniqueness
* sync related entities (variants, notes, images)
* handle transaction

---

## Validation Rules

* name

> - required
> - max: 150

* brand_id

> - required
> - exists in `brands`

* gender

> - required
> - one of: male, female, unisex

* variants

> - at least one required
> - size_ml > 0
> - price > 0
> - stock ≥ 0

---

## Database Actions (Transactional)

### Update product

```sql
UPDATE products
SET name = ?, slug = ?, brand_id = ?, fragrance_type_id = ?, gender = ?, description = ?
WHERE id = ?;
```

---

### Variants sync

```sql
-- update existing
UPDATE product_variants
SET size_ml = ?, price = ?, stock = ?
WHERE id = ? AND product_id = ?;

-- insert new
INSERT INTO product_variants (product_id, size_ml, price, stock)
VALUES (?, ?, ?, ?);

-- delete removed
DELETE FROM product_variants
WHERE id = ? AND product_id = ?;
```

---

### Notes sync

```sql
DELETE FROM product_notes
WHERE product_id = ?;

INSERT INTO product_notes (product_id, note_id, note_type)
VALUES (?, ?, ?);
```

---

### Images handling

```sql
-- delete removed
DELETE FROM product_images
WHERE id = ? AND product_id = ?;

-- insert new
INSERT INTO product_images (product_id, image_url, position)
VALUES (?, ?, ?);

-- update positions
UPDATE product_images
SET position = ?
WHERE id = ? AND product_id = ?;
```

---

## Image Handling

* store in `public/uploads/products/`
* delete files when removed
* maintain ordering via `position`

---

## Response

### Success

```text
302 Redirect → /admin/products
```

---

### Errors

* validation errors
* duplicate slug
* transaction failure

---

## Security

* admin authentication required
* role-based access (admin only)
* CSRF protection required

---

## UX Notes

* pre-filled form
* dynamic variants editing
* image preview and reorder
* ability to remove images and variants

---

## Future Extensions

* versioning / history
* draft updates
* audit log

---

## View Requirements

* edit form
* variants editor
* notes selector
* image management UI
