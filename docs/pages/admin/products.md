# Admin Products

## Purpose

Review the catalog at a glance and jump into detailed product editing.

## Route

```bash
GET /admin/products
```

## Current Behavior

- The page shows a searchable product table with inventory and pricing context.
- Summary cards highlight visible product counts, stock coverage, missing images, and average variant density.
- The current UI exposes search, gender, and inventory filters.
- Each product row links straight to the edit screen.
- The table collapses into a stacked card layout on smaller screens.

## Filters

- `q`
- `gender`
- `inventory`
- `page`

## Data Shown

- Product id, slug, name, and brand
- Positioning tags such as concentration, type, and gender
- Variant count and total stock
- Price range
- Primary image presence

## Visual Reference

Add image here: product list with summary cards and a few rows showing pricing and stock pills.
Suggested path: `docs/screenshots/admin/products-list.png`
