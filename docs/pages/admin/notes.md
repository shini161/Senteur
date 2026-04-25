# Admin Notes

## Purpose

Manage the reusable fragrance-note library used across product editing.

## Routes

```bash
GET  /admin/notes
POST /admin/notes
POST /admin/notes/{id}
POST /admin/notes/{id}/delete
```

## Current Behavior

- The page combines filtering, note browsing, editing, and creation in one workspace.
- Search supports free-text note lookup plus a usage filter for `used` and `unused`.
- Pagination is built into the notes list.
- Creating a note requires a name and an uploaded image.
- Editing a note keeps the existing image unless a replacement file is provided.
- Notes that are still attached to products cannot be deleted.
- When a note is selected for editing, the sidebar also shows the products currently linked to it.

## Data Shown

- Note name and slug
- Usage count
- Note image preview
- Linked products for the active note
- Current page / total pages

## Validation And Rules

- `name` is required and limited to 100 characters
- slugs are generated from the name and must stay unique
- note images must be JPG, PNG, or WEBP and 2MB or smaller
- delete is blocked while the note is referenced by products

## Visual Reference

Add image here: notes library grid with one note selected in the sidebar.
Suggested path: `docs/screenshots/admin/notes-library.png`
