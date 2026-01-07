# 📋 دليل ربط نماذج طلب الدعم - Frontend Integration Guide

---

## 1️⃣ طلب دعم الأفراد

### Endpoint
```
POST /api/support/individual/store
Content-Type: multipart/form-data
```

### الحقول المطلوبة
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| `full_name` | string | الاسم الكامل |
| `gender` | string | `male` أو `female` |
| `nationality` | string | |
| `city` | string | |
| `housing_type` | string | |
| `housing_type_other` | string | مطلوب إذا housing_type = "أخرى" |
| `identity_image_path` | file | صورة (jpg/png) |
| `birth_date` | date | YYYY-MM-DD |
| `identity_expiry_date` | date | YYYY-MM-DD |
| `phone_number` | string | |
| `whatsapp_number` | string | |
| `email` | string | |
| `academic_qualification_path` | file | pdf/jpg/png |
| `scientific_activity` | string | |
| `scientific_activity_other` | string | مطلوب إذا = "أخرى" |
| `cv_path` | file | pdf/doc/docx |
| `workplace` | string | |
| `support_scope` | string | `full` أو `partial` |
| `amount_requested` | number | |
| `support_type` | string | |
| `support_type_other` | string | مطلوب إذا = "أخرى" |
| `has_income` | boolean | 0 أو 1 |
| `income_source` | string | مطلوب إذا has_income = 1 |
| `marital_status` | string | `single` أو `married` |
| `family_members_count` | int | مطلوب إذا married |
| `recommendation_path` | file | اختياري |
| `bank_account_iban` | string | |
| `bank_name` | string | |

---

## 2️⃣ طلب دعم المؤسسات

### Endpoint
```
POST /api/support/institutional/store
Content-Type: multipart/form-data
```

### الحقول المطلوبة
| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| `institution_name` | string | |
| `license_number` | string | |
| `license_certificate_path` | file | pdf/jpg/png |
| `email` | string | |
| `support_letter_path` | file | pdf/jpg/png |
| `phone_number` | string | |
| `ceo_name` | string | |
| `ceo_mobile` | string | |
| `whatsapp_number` | string | |
| `city` | string | |
| `activity_type` | string | |
| `activity_type_other` | string | مطلوب إذا = "أخرى" |
| `project_name` | string | |
| `project_type` | string | |
| `project_type_other` | string | مطلوب إذا = "أخرى" |
| `project_file_path` | file | pdf/doc/docx |
| `project_manager_name` | string | |
| `project_manager_mobile` | string | |
| `goal_1` | string | |
| `goal_2` - `goal_4` | string | اختياري |
| `other_goals` | string | اختياري |
| `beneficiaries` | string | |
| `beneficiaries_other` | string | مطلوب إذا = "أخرى" |
| `project_cost` | number | |
| `project_outputs` | string | |
| `operational_plan_path` | file | pdf/doc/docx |
| `support_scope` | string | `full` أو `partial` |
| `amount_requested` | number | |
| `account_name` | string | |
| `bank_account_iban` | string | |
| `bank_name` | string | |
| `bank_certificate_path` | file | pdf/jpg/png |

---

## 📤 Response Cases

### ✅ Success (201)
```json
{
    "message": "تم استلام طلبك بنجاح",
    "request_number": "0001",
    "phone_number": "0501234567"
}
```
**Action:** عرض رسالة نجاح + رقم الطلب للمستخدم

---

### ❌ Validation Error (422)
```json
{
    "errors": {
        "full_name": ["حقل الاسم الكامل مطلوب"],
        "email": ["البريد الإلكتروني غير صالح"],
        "cv_path": ["يجب أن يكون الملف من نوع: pdf, doc, docx"]
    }
}
```
**Action:** عرض رسائل الخطأ تحت كل حقل

---

### 🚫 Service Disabled (403)
```json
{
    "message": "عذراً، التقديم على طلبات دعم الأفراد مغلق حالياً."
}
```
**Action:** عرض رسالة للمستخدم + تعطيل النموذج

---

### 💥 Server Error (500)
```json
{
    "message": "حدث خطأ أثناء معالجة الطلب",
    "error": "Error details..."
}
```
**Action:** عرض رسالة خطأ عامة + طلب المحاولة لاحقاً

---

## 🔍 الاستعلام عن حالة الطلب

### Endpoint
```
POST /api/support/individual/status
POST /api/support/institutional/status
Content-Type: application/json
```

### Request
```json
{
    "request_number": "0001",
    "phone_number": "0501234567"
}
```

### Response
```json
{
    "status": "pending",
    "message": "طلبك قيد المراجعة"
}
```

---

## ⚙️ التحقق من تفعيل الخدمة

### Endpoint
```
GET /api/support/settings
```

### Response
```json
{
    "individual_support_enabled": true,
    "institutional_support_enabled": true
}
```
**Action:** إذا `false` → تعطيل النموذج + عرض رسالة

---

## 📝 ملاحظات هامة

1. **Content-Type:** يجب استخدام `multipart/form-data` لأن هناك ملفات
2. **حجم الملفات:** الحد الأقصى 10MB لكل ملف
3. **أنواع الملفات:** pdf, jpg, jpeg, png, doc, docx
4. **التحقق من الخدمة:** تحقق من `/api/support/settings` قبل عرض النموذج
