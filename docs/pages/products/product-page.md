# Product Page

## Purpose

Display detailed information about a single product, including images, variants, notes, and reviews.

---

## Route

```bash id="6n2k9f"
GET /product/{slug}
```

---

## Parameters

| Param | Type   | Required | Notes               |
| ----- | ------ | -------- | ------------------- |
| slug  | string | ✅        | unique product slug |

---

## Page Data

* product:

  * name
  * brand
  * description
  * fragrance type
  * gender
* images (ordered)
* variants:

  * size_ml
  * price
  * stock
* notes:

  * grouped by type (top, middle, base)
* reviews (optional)

---

## Request Flow

![Product Page Flow](../../flows/products/product-page.png)

---

## Controller

```php id="h3y7df"
ProductController::show(string $slug)
```

---

## Service Layer

```php id="p8k3mz"
ProductService::getBySlug(string $slug): Product
```

---

## Responsibilities

* retrieve product by slug
* load related data (variants, images, notes)
* structure data for view
* handle not found case

---

## Validation Rules

* slug

> - required
> - exists in `products`

---

## Database Actions

### Get product

```sql id="v7s4ax"
SELECT 
    p.id,
    p.name,
    p.slug,
    p.description,
    p.gender,
    b.name AS brand_name,
    ft.name AS fragrance_type
FROM products p
JOIN brands b ON p.brand_id = b.id
LEFT JOIN fragrance_types ft ON p.fragrance_type_id = ft.id
WHERE p.slug = ? AND p.deleted_at IS NULL
LIMIT 1;
```

---

### Get images

```sql id="q2k8zj"
SELECT image_url, position
FROM product_images
WHERE product_id = ?
ORDER BY position ASC;
```

---

### Get variants

```sql id="n9f3lz"
SELECT id, size_ml, price, stock
FROM product_variants
WHERE product_id = ?
ORDER BY size_ml ASC;
```

---

### Get notes

```sql id="z4p8xs"
SELECT n.name, n.image_url, pn.note_type
FROM product_notes pn
JOIN notes n ON pn.note_id = n.id
WHERE pn.product_id = ?;
```

---

### Get reviews (optional)

```sql id="b7m2ke"
SELECT r.rating, r.title, r.comment, u.username
FROM reviews r
JOIN users u ON r.user_id = u.id
WHERE r.product_id = ?
ORDER BY r.created_at DESC;
```

---

## Data Structuring

* group notes by:

  * top
  * middle
  * base

---

## Response

### Success

* render product page with:

  * product details
  * images
  * variants
  * notes
  * reviews

---

### Errors

If not found:

```txt id="k3z9lm"
404 Not Found
```

---

## Security

* validate slug input
* use prepared statements
* do not expose internal IDs unnecessarily

---

## Performance Considerations

* use indexed fields:

  * `products.slug`
  * `product_images.product_id`
  * `product_variants.product_id`
* avoid N+1 queries (use batched queries)
* cache product data (optional)

---

## UX Notes

* show default selected variant
* disable out-of-stock variants
* display "from €X" if multiple variants
* highlight notes visually

---

## Future Extensions

* related products
* average rating + rating count
* variant selection persistence
* lazy load reviews

---

## View Requirements

* image gallery
* variant selector (size)
* add to cart button
* notes section
* reviews section
