# Admin - Product Create

## Purpose

Allow administrators to create a new product with variants, images, and notes.

---

## Route

```bash
GET  /admin/products/create
POST /admin/products
```

---

## Page Data

* form fields:

  * name
  * slug (auto-generated or editable)
  * brand_id
  * fragrance_type_id (optional)
  * gender
  * description
* variants (dynamic list):

  * size_ml
  * price
  * stock
* notes (selection):

  * note_id
  * note_type (general, top, heart, base)
* images (upload, ordered)

---

## Request Flow

### Create Product

![Create Product Flow](../../flows/admin/product-create.png)

---

## Controller

```php
Admin\\ProductController::create()
Admin\\ProductController::store()
```

---

## Service Layer

```php
ProductService::create(array $data): void
```

---

## Responsibilities

* validate product data
* ensure unique slug
* create product and related entities
* handle transaction
* manage file uploads

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

* images (optional)

> - valid image files
> - max size limit

---

## Database Actions (Transactional)

### Create product

```sql
INSERT INTO products (
    name,
    slug,
    brand_id,
    fragrance_type_id,
    gender,
    description
) VALUES (?, ?, ?, ?, ?, ?);
```

---

### Create variants

```sql
INSERT INTO product_variants (product_id, size_ml, price, stock)
VALUES (?, ?, ?, ?);
```

---

### Attach notes

```sql
INSERT INTO product_notes (product_id, note_id, note_type)
VALUES (?, ?, ?);
```

---

### Insert images

```sql
INSERT INTO product_images (product_id, image_url, position)
VALUES (?, ?, ?);
```

---

## Image Handling

* store in `public/uploads/products/`
* generate unique filenames
* maintain order via `position`

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
* transaction failure (rollback)

---

## Security

* admin authentication required
* role-based access (admin only)
* CSRF protection required
* validate file uploads

---

## UX Notes

* dynamic variant fields
* image preview + ordering
* slug auto-generation from name
* grouped notes selection (top/heart/base)

---

## Future Extensions

* bulk variant creation
* drag & drop image ordering
* live slug preview
* draft products

---

## View Requir
