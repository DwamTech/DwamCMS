# Book Management API Guide (For Frontend)

This guide provides the necessary endpoints and structures for managing Books and Book Series (Library).

## 1. Prerequisites (Form Data Preparation)

Before rendering the "Add Book" form, allow the admin to view/select from existing **Series** and **Authors**.

### A. Get All Book Series (Sections)
**Endpoint:** `GET /api/admin/library/series`
**Usage:** Populate the "Series / Category" dropdown.
**Response:**
```json
[
    { "id": 1, "name": "Islamic Studies", "description": "..." },
    { "id": 2, "name": "History", "description": "..." }
]
```

### B. Get Existing Authors (Autocomplete)
**Endpoint:** `GET /api/admin/library/books/authors`
**Usage:** Use for an Autocomplete/Combobox field.
- If the user selects an existing name -> Send that string.
- If the user types a NEW name -> Send the new string.
**Response:** `["Ibn Khaldun", "Dr. Ahmed", ...]`

---

## 2. Add New Book
**Endpoint:** `POST /api/admin/library/books`
**Auth:** Admin Only
**Content-Type:** `multipart/form-data`

**Form Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | String | Yes | Book Title |
| `description` | String | Yes | Description or Abstract |
| `author_name` | String | Yes | String value (selected or typed) |
| `source_type` | Enum | Yes | `file`, `link`, or `embed` |
| `file_path` | File | Conditional | **Required if source_type=file**. The book file (PDF, ePUB, etc.) |
| `source_link` | String | Conditional | **Required if source_type=link or embed**. The URL or iframe code. |
| `cover_type` | Enum | Yes | `auto` (default placeholder) or `upload` |
| `cover_path` | File | Conditional | **Required if cover_type=upload**. The cover image. |
| `type` | Enum | Yes | `single` (Standalone) or `part` (Part of a series) |
| `book_series_id` | Integer| Conditional | **Required if type=part**. The ID of the series from step 1A. |
| `keywords` | Array | No | e.g. `keywords[0]=history`, `keywords[1]=islam` |

**Example Request (FormData):**
```
title: "History of the World"
description: "A great book..."
author_name: "Generic Author"
source_type: "file"
file_path: (Binary File)
cover_type: "upload"
cover_path: (Binary Image)
type: "part"
book_series_id: 1
```

---

## 3. Manage Book Series (Sections)

If the user wants to add a NEW Series on the fly (or manage them):

- **List:** `GET /api/admin/library/series`
- **Create:** `POST /api/admin/library/series`
    - Body: `{ "name": "New Series", "description": "..." }`
- **Update:** `PUT /api/admin/library/series/{id}`
- **Delete:** `DELETE /api/admin/library/series/{id}`
