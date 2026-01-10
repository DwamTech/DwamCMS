# 📋 دليل ربط نظام التقييمات والمقترحات - Frontend Integration Guide

---

## 1️⃣ تقييم رضا المستفيدين (Platform Rating)

⚠️ **ملاحظة هامة:** هذا الـ Endpoint **عام (Public)** ولا يحتاج إلى Token مصادقة.

### Endpoint
```
POST /api/platform-rating
Content-Type: application/json
```

### Request Body
```json
{
    "rating": 5  // رقم صحيح من 1 إلى 5
}
```

### Response Cases

#### ✅ Success (200 OK)
يعيد الإحصائيات المحدثة مباشرة لتحديث الواجهة.
```json
{
    "average_rating": 4.5,
    "rating_count": 152,
    "max_rating": 5
}
```

#### ❌ Validation Error (422)
```json
{
    "errors": {
        "rating": ["The rating field is required."]
    }
}
```

#### ⏳ Rate Limit (429)
إذا حاول نفس المستخدم التقييم مرة أخرى خلال دقيقة واحدة.
```json
{
    "message": "عذراً، لقد قمت بالتقييم مؤخراً. يرجى الانتظار قليلاً."
}
```

---

## 2️⃣ صندوق الاقتراحات (Suggestion Box)

⚠️ **ملاحظة:** هذا الـ Endpoint **عام (Public)**.
يجب استخدام `FormData` لأن النموذج قد يحتوي على مرفقات.

### Endpoint
```
POST /api/feedback
Content-Type: multipart/form-data
```

### Request Body (FormData)
| Key | Type | Value / Note |
|-----|------|--------------|
| `type` | string | **`suggestion`** (مهم جداً) |
| `name` | string | اسم المستخدم |
| `email` | string | البريد الإلكتروني |
| `message` | string | نص الاقتراح |
| `phone_number` | string | (اختياري) |
| `attachment_path` | file | (اختياري) ملف مرفق (PDF, JPG, PNG) |

### Response Cases

#### ✅ Success (201 Created)
```json
{
    "message": "شكراً لمقترحك، نسعد بمساهمتك."
}
```
**Action:** عرض رسالة شكر وتفريغ النموذج.

#### ❌ Validation Error (422)
```json
{
    "errors": {
        "email": ["البريد الإلكتروني غير صالح"],
        "message": ["حقل الرسالة مطلوب"]
    }
}
```

---

## 3️⃣ صندوق الشكاوى (Complaint Box)

⚠️ **ملاحظة:** يستخدم نفس الـ Endpoint الخاص بالاقتراحات ولكن مع تغيير الـ `type`.

### Endpoint
```
POST /api/feedback
Content-Type: multipart/form-data
```

### Request Body (FormData)
| Key | Type | Value / Note |
|-----|------|--------------|
| `type` | string | **`complaint`** (مهم جداً) |
| `name` | string | اسم المستخدم |
| `email` | string | البريد الإلكتروني |
| `message` | string | نص الشكوى |
| `phone_number` | string | (اختياري) |
| `attachment_path` | file | (اختياري) ملف مرفق (PDF, JPG, PNG) |

### Response Cases

#### ✅ Success (201 Created)
```json
{
    "message": "تم استلام شكواك وسنعمل على حلها قريباً."
}
```
**Action:** عرض رسالة تأكيد وتفريغ النموذج.

---

## 📊 عرض متوسط التقييم (للـ Footer أو الواجهة الرئيسية)

### Endpoint
```
GET /api/platform-rating
```

### Response
```json
{
    "average_rating": 4.5,
    "rating_count": 152,
    "max_rating": 5
}
```
