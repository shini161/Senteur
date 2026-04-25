# Admin Product Edit

## Purpose

Update a saved product, preserve variant identity, and manage product-level or variant-level imagery.

## Routes

```bash
GET  /admin/products/{id}/edit
POST /admin/products/{id}
POST /admin/products/{id}/image
POST /admin/variants/{id}/image
```

## Current Behavior

- The edit screen adds summary cards for variants, stock, price range, primary image state, and note count.
- The main form reuses the same identity, notes, and variants sections as create.
- Product-level image upload is handled in the sidebar.
- Variant-level primary image uploads are listed in a dedicated section below the form.
- When validation fails, existing variant images are preserved while the form re-renders.

## Media Rules

- product images are uploaded after the product already exists
- variant image uploads are scoped to one variant at a time
- image upload errors are shown inline without leaving the edit screen

## Visual Reference

Add image here: edit product screen with summary cards, main form, and image upload panels.
Suggested path: `docs/screenshots/admin/product-edit.png`
