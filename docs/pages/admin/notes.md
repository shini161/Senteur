# Admin - Notes

## Purpose

Allow administrators to manage fragrance notes used in products (e.g., top, middle, base notes).

---

## Route

```bash
GET    /admin/notes
POST   /admin/notes
PATCH  /admin/notes/{id}
DELETE /admin/notes/{id}
```

---

## Page Data

* list of notes:

  * id
  * name
  * image_url (optional)
  * created_at

---

## Request Flow

### Create / Update Note

![Create or Update Note Flow](../../flows/admin/note-save.png)

---

### Delete Note

![Delete Note Flow](../../flows/admin/note-delete.png)

---

## Controller

```php
Admin\\NoteController::index()
Admin\\NoteController::store()
Admin\\NoteController::update(int $id)
Admin\\NoteController::delete(int $id)
```

---

## Service Layer

```php
NoteService::getAll(): array
NoteService::create(array $data): void
NoteService::update(int $id, array $data): void
NoteService::delete(int $id): void
```

---

## Responsibilities

* manage notes (CRUD)
* ensure unique names
* handle image uploads
* prevent deletion if in use

---

## Validation Rules

* name

> - required
> - max: 100

* image (optional)

> - valid image file (jpg, png, webp)
> - max size limit (e.g., 2MB)

---

## Database Actions

### Get notes

```sql
SELECT id, name, image_url, created_at
FROM notes
ORDER BY created_at DESC;
```

---

### Create note

```sql
INSERT INTO notes (name, image_url)
VALUES (?, ?);
```

---

### Update note

```sql
UPDATE notes
SET name = ?, image_url = ?
WHERE id = ?;
```

---

### Delete note

```sql
DELETE FROM notes
WHERE id = ?;
```

---

## Image Handling

* store files in filesystem (e.g., `public/uploads/notes/`)
* store only `image_url` in DB
* validate file type and size
* generate unique filename

---

## Security

* admin authentication required
* role-based access (admin only)
* CSRF protection required
* validate file uploads

---

## Response

### Success

```text
302 Redirect → /admin/notes
```

---

### Errors

* validation errors
* duplicate name
* note in use

---

## UX Notes

* list of notes with images
* create/edit form
* image preview
* delete with confirmation

---

## Future Extensions

* categorize notes (citrus, woody, etc.)
* search notes
* drag & drop ordering

---

## View Requirements

* notes list
* create/edit form
* image upload input
* delete action
