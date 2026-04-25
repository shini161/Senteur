# Admin Orders

## Purpose

Monitor incoming orders, filter the fulfillment queue, and open individual orders for operational updates.

## Route

```bash
GET /admin/orders
```

## Current Behavior

- Orders are listed newest-first with search and status filtering.
- Summary cards highlight matching orders, attention-needed orders, delivered volume, and visible order value.
- Search is aimed at order ids, customer details, and related order text.
- The table collapses into cards on smaller screens.
- Each row links directly to the order detail page.

## Filters

- `q`
- `status`
- `page`

## Data Shown

- Public order id
- Customer name and email
- Status badge
- Item count
- Total amount
- Created timestamp

## Visual Reference

Add image here: admin order list with the filter drawer open and summary cards visible.
Suggested path: `docs/screenshots/admin/orders-list.png`
