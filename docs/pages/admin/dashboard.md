# Admin - Dashboard

## Purpose

Provide administrators with a high-level overview of store performance and quick access to key management areas.

---

## Route

```bash
GET /admin
```

---

## Page Data

* metrics:

  * total_orders
  * total_revenue
  * orders_today
  * revenue_today
  * pending_orders_count
* recent orders (latest N):

  * public_id
  * status
  * total_amount
  * created_at
* low stock products (optional):

  * product_name
  * variant (size_ml)
  * stock

---

## Request Flow

### Dashboard Load

![Dashboard Load](../../flows/admin/dashboard.png)

---

## Controller

```php
Admin\\DashboardController::index()
```

---

## Service Layer

```php
DashboardService::getMetrics(): array
DashboardService::getRecentOrders(int $limit = 10): array
DashboardService::getLowStock(int $threshold = 5): array
```

---

## Responsibilities

* aggregate key business metrics
* provide recent activity snapshot
* highlight operational issues (e.g., low stock)
* keep queries efficient (read-heavy)

---

## Database Actions

### Metrics

```sql
-- total orders
SELECT COUNT(*) AS total_orders FROM orders;

-- total revenue (paid/processing as needed)
SELECT COALESCE(SUM(total_amount), 0) AS total_revenue
FROM orders
WHERE status IN ('processing','shipped','delivered');

-- orders today
SELECT COUNT(*) AS orders_today
FROM orders
WHERE DATE(created_at) = CURRENT_DATE;

-- revenue today
SELECT COALESCE(SUM(total_amount), 0) AS revenue_today
FROM orders
WHERE DATE(created_at) = CURRENT_DATE
  AND status IN ('processing','shipped','delivered');

-- pending orders
SELECT COUNT(*) AS pending_orders_count
FROM orders
WHERE status = 'pending';
```

---

### Recent Orders

```sql
SELECT public_id, status, total_amount, created_at
FROM orders
ORDER BY created_at DESC
LIMIT ?;
```

---

### Low Stock (optional)

```sql
SELECT p.name AS product_name, pv.size_ml, pv.stock
FROM product_variants pv
JOIN products p ON p.id = pv.product_id
WHERE pv.stock <= ?
ORDER BY pv.stock ASC
LIMIT 20;
```

---

## Response

### Success

* render dashboard with:

  * metrics cards
  * recent orders list
  * low stock alerts (optional)

---

## Security

* admin authentication required
* role-based access (admin only)
* read-only operations (no mutations)

---

## Performance Considerations

* prefer aggregated queries over per-row calculations
* add indexes:

  * `orders(created_at)`
  * `orders(status)`
  * `product_variants(stock)`
* consider caching metrics (short TTL)

---

## UX Notes

* cards for key metrics
* table for recent orders
* highlight pending orders
* visual indicator for low stock
* quick links to:

  * `/admin/orders`
  * `/admin/products`
  * `/admin/categories`

---

## Future Extensions

* charts (orders/revenue over time)
* top products / top categories
* conversion rate
* average order value (AOV)
* date range filtering

---

## View Requirements

* metrics cards
* recent orders table
* alerts section (low stock)
* navigation links
