# Article Management API Guide (For Frontend)

This guide provides the necessary endpoints and structures for managing articles (Create, List, Update, Toggle Status, Delete).

## 1. List All Articles (With Pagination & Filters)
**Endpoint:** `GET /articles` (Public) or `GET /admin/dashboard/articles` (if you want a specific admin view, but reusing `/articles` with filters is common).

**Filters (Query Params):**
- `status`: `draft` | `published` | `archived` (e.g., `?status=published`)
- `author`: Search by author name string (e.g., `?author=Ahmed`)
- `date`: Filter by Gregorian date `YYYY-MM-DD`
- `page`: Pagination page number (e.g., `?page=2`)

**Response:**
Returns a paginated JSON object.
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Article Title",
            "status": "published",
            "author_name": "Admin User",
            ...
        }
    ],
    ...
}
```

## 2. Create Article
**Endpoint:** `POST /api/articles`
**Auth:** Required (Admin or Author Token)
**Content-Type:** `multipart/form-data` (to handle image upload)

**Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | String | Yes | Article title |
| `slug` | String | Yes | Unique URL slug |
| `content` | String (HTML) | Yes | The body of the article |
| `author_name` | String | Yes | Name of the author to display |
| `status` | String | Yes | `draft` or `published`. If `published`, it is live immediately. |
| `featured_image` | File | No | Image file (jpg, png, webp) |
| `section_id` | Integer | No | ID of the section (defaults to 'general' if empty) |
| `excerpt` | String | No | Short summary |
| `gregorian_date` | String | No | Custom date string |
| `keywords` | String | No | SEO keywords |

## 3. Toggle Publish Status (Quick Action)
**Endpoint:** `POST /api/articles/{id}/toggle-status`
**Auth:** Required (Admin)
**Note:** Does NOT require sending any body data.

**Behavior:**
- If current status is `draft` -> Switches to `published`.
- If current status is `published` -> Switches to `draft`.

**Response:**
```json
{
    "message": "Article status updated successfully",
    "status": "published", // The new status
    "article": { ... }
}
```

## 4. Edit Article
**Endpoint:** `POST /api/articles/{id}` 
**Method Override:** You MUST append `_method=PUT` to the FormData if you are uploading files, or use `PUT` request if no files. It's safest to always use **POST** with `_method="PUT"` in FormData when dealing with files in Laravel.

**Fields:**
- Send only the fields you want to update.
- Same fields as "Create Article".

## 5. Delete Article
**Endpoint:** `DELETE /api/articles/{id}`
**Auth:** Required (Admin)

**Response:**
```json
{
    "message": "Article deleted successfully"
}
```
