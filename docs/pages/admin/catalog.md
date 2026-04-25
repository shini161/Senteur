# Admin Catalog Data

## Purpose

Manage shared catalog metadata used by the product form and storefront filters.

## Routes

```bash
GET  /admin/catalog
POST /admin/catalog/brands
POST /admin/catalog/brands/{id}
POST /admin/catalog/brands/{id}/delete
POST /admin/catalog/fragrance-types
POST /admin/catalog/fragrance-types/{id}
POST /admin/catalog/fragrance-types/{id}/delete
```

## Current Behavior

- The page contains two editable metadata sections: brands and fragrance types.
- Each section has its own search box and pagination state.
- The right sidebar shows fixed gender buckets with product counts.
- Create and edit happen inline on the same screen.
- Delete is disabled when a brand or fragrance type is still attached to products.

## Data Shown

- Brand name and linked product count
- Fragrance type name and linked product count
- Gender buckets with counts
- Per-section pagination and search state

## Validation And Rules

- brand names are required, unique, and limited to 100 characters
- fragrance type names are required, unique, and limited to 50 characters
- referenced entries cannot be deleted

## Visual Reference

Add image here: catalog data workspace showing both metadata sections and the gender sidebar.
Suggested path: `docs/screenshots/admin/catalog-data.png`
