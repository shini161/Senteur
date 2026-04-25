# Admin Product Create

## Purpose

Create a new catalog entry with identity fields, fragrance-note assignments, and at least one variant.

## Routes

```bash
GET  /admin/products/create
POST /admin/products
```

## Current Behavior

- The create screen is split into identity, notes, and variants sections.
- Notes can be assigned as a general list or across top, heart, and base stages.
- At least one variant is required before saving.
- Product media is intentionally handled after the first save on the edit screen, once stable ids exist.
- Client-side helpers handle slug generation and dynamic variant rows.

## Key Fields

- Brand
- Fragrance type
- Family name
- Product name
- Concentration label
- Gender
- Slug
- Description
- Note assignments
- Variant size, price, and stock

## Validation Highlights

- brand, name, slug, and gender are required
- slugs must use lowercase letters, numbers, and hyphens
- selected notes must still exist
- at least one variant is required

## Visual Reference

Add image here: create product screen with the identity and notes sections visible.
Suggested path: `docs/screenshots/admin/product-create.png`
