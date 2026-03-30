# Product List

## Purpose

Display a list of products with support for search, filtering, sorting, and pagination.

---

## Route

```bash
GET /products
```

---

## Query Parameters

| Param     | Type   | Required | Notes                           |
| --------- | ------ | -------- | ------------------------------- |
| q         | string | ❌        | search query (name/description) |
| brand_id  | int    | ❌        | filter by brand                 |
| gender    | enum   | ❌        | male, female, unisex            |
| min_price | number | ❌        | minimum price                   |
| max_price | number | ❌        | maximum price                   |
| sort      | string | ❌        | price_asc, price_desc, newest   |
| page      | int    | ❌        | pagination page                 |

---

## Page Data

* list of products:

  * name
  * slug
  * brand
  * main image
  * lowest price (from variants)
* pagination info
* active filters

---

## Request Flow

![Product List Flow](../../flows/products/product-list.png)

---

## Controller

```php
ProductController::index()
```

---

## Service Layer

```php
ProductService::getProducts(array $filters): array
```

---

## Responsibilities

* apply filters
* handle search
* sort results
* paginate results
* return minimal product data (optimized)

---

## Validation Rules

* page

> - integer ≥ 1

* min_price / max_price

> - numeric
> - min ≤ max

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
    b.name AS brand_name,
    MIN(pv.price) AS lowest_price
FROM products p
JOIN brands b ON p.brand_id = b.id
JOIN product_variants pv ON pv.product_id = p.id
WHERE p.deleted_at IS NULL
```

---

### Search (optional)

```sql
AND MATCH(p.name, p.description) AGAINST (? IN BOOLEAN MODE)
```

---

### Filters (optional)

```sql
AND p.brand_id = ?
AND p.gender = ?
AND pv.price BETWEEN ? AND ?
```

---

### Grouping

```sql
GROUP BY p.id
```

---

### Sorting

```sql
ORDER BY lowest_price ASC | DESC
```

---

### Pagination

```sql
LIMIT ? OFFSET ?
```

---

## Images Handling

### Get main image (position = 0)

```sql
LEFT JOIN product_images pi 
ON pi.product_id = p.id AND pi.position = 0
```

---

## Response

### Success

* render product list with:

  * products
  * filters
  * pagination

---

## Security

* sanitize query parameters
* prevent SQL injection (PDO prepared statements)

---

## Performance Considerations

* use indexes:

  * `products(brand_id)`
  * `products(gender)`
  * `product_variants(product_id, price)`
* avoid N+1 queries (use joins)
* limit selected columns

---

## Future Extensions

* faceted filters (counts per filter)
* sorting by popularity
* caching results
* infinite scroll

---

## View Requirements

* product grid
* filter sidebar
* search bar
* sorting options
* pagination controls
