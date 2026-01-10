# 📋 دليل استخدام نظام التقييمات والاقتراحات والشكاوى

## نظرة عامة

يتكون النظام من ثلاثة أقسام رئيسية:
1. **تقييم رضا المستفيدين** (Platform Rating) - تقييم المنصة بالنجوم
2. **صندوق الاقتراحات** (Suggestions) - استقبال اقتراحات المستخدمين
3. **صندوق الشكاوى** (Complaints) - استقبال شكاوى المستخدمين

---

## 1️⃣ تقييم رضا المستفيدين (Platform Rating)

### الوصف
نظام تقييم بسيط يسمح للمستخدمين بتقييم المنصة من 1 إلى 5 نجوم.

### 🌐 Public Endpoints (للمستخدمين)

#### عرض متوسط التقييم
```http
GET /api/platform-rating
```

**Response:**
```json
{
    "average_rating": 4.2,
    "rating_count": 150,
    "max_rating": 5
}
```

#### إضافة تقييم جديد
```http
POST /api/platform-rating
Content-Type: application/json

{
    "rating": 5
}
```

**Response (Success):**
```json
{
    "average_rating": 4.3,
    "rating_count": 151,
    "max_rating": 5
}
```

**Response (Rate Limited):**
```json
{
    "message": "عذراً، لقد قمت بالتقييم مؤخراً. يرجى الانتظار قليلاً."
}
```
Status: `429 Too Many Requests`

### 📊 للأدمن

حالياً لا يوجد endpoint خاص للأدمن لإدارة التقييمات.

**ملاحظة:** يمكن إضافة الـ endpoints التالية إذا لزم الأمر:
- `GET /api/admin/platform-ratings` - عرض كل التقييمات
- `GET /api/admin/platform-ratings/stats` - إحصائيات مفصلة
- `DELETE /api/admin/platform-ratings/{id}` - حذف تقييم

---

## 2️⃣ صندوق الاقتراحات (Suggestions)

### الوصف
نظام لاستقبال اقتراحات المستخدمين لتحسين الخدمة.

### 🌐 Public Endpoint (للمستخدمين)

#### إرسال اقتراح جديد
```http
POST /api/feedback
Content-Type: multipart/form-data

{
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "phone_number": "0123456789",        // اختياري
    "message": "أقترح إضافة خاصية البحث المتقدم",
    "attachment_path": [file],            // اختياري - PDF, JPG, PNG, DOC
    "type": "suggestion"                  // ⚠️ مهم جداً
}
```

**Response (Success):**
```json
{
    "message": "شكراً لمقترحك، نسعد بمساهمتك."
}
```
Status: `201 Created`

**Response (Validation Error):**
```json
{
    "errors": {
        "name": ["حقل الاسم مطلوب"],
        "email": ["البريد الإلكتروني غير صالح"],
        "type": ["نوع الرسالة يجب أن يكون suggestion أو complaint"]
    }
}
```
Status: `422 Unprocessable Entity`

### 👨‍💼 Admin Endpoints

#### عرض كل الاقتراحات
```http
GET /api/admin/feedback?type=suggestion
Authorization: Bearer {token}
```

**Query Parameters:**
| المعامل | النوع | الوصف |
|---------|-------|-------|
| `type` | string | فلتر حسب النوع: `suggestion` أو `complaint` |
| `page` | int | رقم الصفحة (افتراضي: 1) |

**Response:**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "phone_number": "0123456789",
            "message": "أقترح إضافة خاصية البحث المتقدم",
            "attachment_path": "feedback_attachments/abc123.pdf",
            "type": "suggestion",
            "created_at": "2026-01-05T22:00:00.000000Z",
            "updated_at": "2026-01-05T22:00:00.000000Z"
        }
    ],
    "last_page": 5,
    "per_page": 20,
    "total": 100
}
```

#### حذف اقتراح
```http
DELETE /api/admin/feedback/{id}
Authorization: Bearer {token}
```

**Response (Success):**
```json
{
    "message": "تم حذف الرسالة بنجاح"
}
```

**Response (Not Found):**
```json
{
    "message": "الرسالة غير موجودة"
}
```
Status: `404 Not Found`

---

## 3️⃣ صندوق الشكاوى (Complaints)

### الوصف
نظام لاستقبال شكاوى المستخدمين ومعالجتها.

### 🌐 Public Endpoint (للمستخدمين)

#### إرسال شكوى جديدة
```http
POST /api/feedback
Content-Type: multipart/form-data

{
    "name": "محمد علي",
    "email": "mohamed@example.com",
    "phone_number": "0987654321",        // اختياري
    "message": "واجهت مشكلة في تحميل الملفات",
    "attachment_path": [file],            // اختياري - PDF, JPG, PNG, DOC
    "type": "complaint"                   // ⚠️ مهم جداً
}
```

**Response (Success):**
```json
{
    "message": "تم استلام شكواك وسنعمل على حلها قريباً."
}
```
Status: `201 Created`

### 👨‍💼 Admin Endpoints

#### عرض كل الشكاوى
```http
GET /api/admin/feedback?type=complaint
Authorization: Bearer {token}
```

**Response:**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 10,
            "name": "محمد علي",
            "email": "mohamed@example.com",
            "phone_number": "0987654321",
            "message": "واجهت مشكلة في تحميل الملفات",
            "attachment_path": null,
            "type": "complaint",
            "created_at": "2026-01-05T23:00:00.000000Z",
            "updated_at": "2026-01-05T23:00:00.000000Z"
        }
    ],
    "last_page": 3,
    "per_page": 20,
    "total": 50
}
```

#### حذف شكوى
```http
DELETE /api/admin/feedback/{id}
Authorization: Bearer {token}
```

---

## 📊 ملخص الـ Endpoints

### Public (بدون Authentication)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/platform-rating` | عرض متوسط التقييم |
| POST | `/api/platform-rating` | إضافة تقييم جديد |
| POST | `/api/feedback` | إرسال اقتراح أو شكوى |

### Admin (يتطلب Authentication)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/admin/feedback` | عرض كل الاقتراحات والشكاوى |
| GET | `/api/admin/feedback?type=suggestion` | عرض الاقتراحات فقط |
| GET | `/api/admin/feedback?type=complaint` | عرض الشكاوى فقط |
| DELETE | `/api/admin/feedback/{id}` | حذف اقتراح أو شكوى |

---

## 📝 ملاحظات هامة

### المرفقات المسموح بها
- **الأنواع:** PDF, JPG, JPEG, PNG, DOC, DOCX
- **الحجم الأقصى:** 5MB

### الحماية من Spam
- تقييم المنصة: تقييم واحد فقط كل دقيقة من نفس الـ IP

### التخزين
- المرفقات تُحفظ في: `storage/app/public/feedback_attachments/`
- للوصول للمرفق: `{APP_URL}/storage/feedback_attachments/{filename}`

---

## 🔧 تشغيل الـ Seeders

```bash
# تشغيل Seeder للتقييمات
php artisan db:seed --class=PlatformRatingSeeder

# تشغيل Seeder للاقتراحات والشكاوى
php artisan db:seed --class=FeedbackSeeder
```
