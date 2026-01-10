# دليل نظام إدارة الملفات (Document Management)

هذا المستند يشرح كيفية استخدام نظام إدارة الملفات والمستندات من خلال الـ API.

---

## 1. نظرة عامة

نظام إدارة الملفات يسمح بـ:
- رفع ملفات متنوعة (PDF, Word, Excel, PowerPoint, ZIP, etc.)
- إضافة وصف وكلمات دلالية لكل ملف
- رفع كفر (صورة توضيحية) للملف
- تتبع المشاهدات والتحميلات
- ربط الملف بمستخدم (المالك)

---

## 2. API Endpoints

### أ. قائمة الملفات (Public)
**GET** `/api/documents`

**معلمات البحث (Query Params):**
- `page`: رقم الصفحة
- `search`: بحث في العنوان، الوصف، والكلمات الدلالية
- `file_type`: تصفية حسب نوع الملف (pdf, docx, xlsx, etc.)

**الاستجابة:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "لائحة العمل 2024",
      "description": "اللائحة الداخلية للعمل",
      "source_type": "file",
      "file_path": "documents/files/...",
      "cover_path": "documents/covers/...",
      "keywords": ["لوائح", "عمل"],
      "file_type": "pdf",
      "file_size": 2048576,
      "views_count": 150,
      "downloads_count": 45,
      "user": {
        "id": 1,
        "name": "Admin"
      },
      "created_at": "2024-01-01T12:00:00"
    }
  ],
  "current_page": 1,
  "per_page": 20
}
```

### ب. تفاصيل ملف (Public)
**GET** `/api/documents/{id}`

يعرض تفاصيل الملف ويزيد عداد المشاهدات تلقائياً.

### ج. تسجيل تحميل (Public)
**POST** `/api/documents/{id}/download`

يزيد عداد التحميلات ويرجع رابط التحميل.

**الاستجابة:**
```json
{
  "message": "تم تسجيل التحميل",
  "download_url": "https://domain.com/storage/documents/files/filename.pdf"
}
```

---

## 3. Admin Management

### أ. إضافة ملف جديد (Admin)
**POST** `/api/admin/documents`

**البيانات المطلوبة:**
```json
{
  "title": "اسم الملف",
  "description": "وصف الملف",
  "source_type": "file", // أو "link"
  "file_path": "<FILE>", // إذا source_type = file
  "source_link": "https://...", // إذا source_type = link
  "cover_type": "upload", // أو "auto"
  "cover_path": "<IMAGE>", // إذا cover_type = upload
  "keywords": ["كلمة1", "كلمة2"]
}
```

**الاستجابة:**
```json
{
  "message": "تم إضافة الملف بنجاح",
  "data": { ... }
}
```

### ب. تحديث ملف (Admin)
**PUT** `/api/admin/documents/{id}`

نفس البيانات المطلوبة في الإضافة، لكن كلها اختيارية.

### ج. حذف ملف (Admin)
**DELETE** `/api/admin/documents/{id}`

يحذف الملف وملفاته من السيرفر.

---

## 4. هيكل البيانات

### الحقول الأساسية:
- `id`: معرف الملف
- `title`: اسم الملف
- `description`: وصف الملف
- `source_type`: نوع المصدر (`file` أو `link`)
- `file_path`: مسار الملف المرفوع
- `source_link`: رابط خارجي (إذا كان المصدر رابط)
- `cover_type`: نوع الكفر (`auto` أو `upload`)
- `cover_path`: مسار صورة الكفر
- `keywords`: كلمات دلالية (Array)
- `file_type`: امتداد الملف (pdf, docx, etc.)
- `file_size`: حجم الملف بالبايت
- `views_count`: عدد المشاهدات
- `downloads_count`: عدد التحميلات
- `user_id`: معرف المالك
- `created_at`: تاريخ الإنشاء

---

## 5. أنواع الملفات المدعومة

- **PDF**: `.pdf`
- **Word**: `.doc`, `.docx`
- **Excel**: `.xls`, `.xlsx`
- **PowerPoint**: `.ppt`, `.pptx`
- **Text**: `.txt`
- **Archives**: `.zip`, `.rar`

الحد الأقصى لحجم الملف: **100 MB**

---

## 6. الصلاحيات

- **Public Routes**: يمكن لأي شخص عرض الملفات وتحميلها.
- **Admin Routes**: تتطلب توكن أدمن (`Bearer Token`) للإضافة والتعديل والحذف.

---

## 7. أمثلة للاستخدام

### مثال 1: رفع لائحة (Admin)
```javascript
const formData = new FormData();
formData.append('title', 'لائحة الموارد البشرية');
formData.append('description', 'اللائحة الداخلية لإدارة الموارد البشرية');
formData.append('source_type', 'file');
formData.append('file_path', pdfFile);
formData.append('cover_type', 'upload');
formData.append('cover_path', imageFile);
formData.append('keywords', JSON.stringify(['لوائح', 'موارد بشرية']));

fetch('/api/admin/documents', {
  method: 'POST',
  headers: { 'Authorization': 'Bearer TOKEN' },
  body: formData
});
```

### مثال 2: عرض قائمة الملفات
```javascript
fetch('/api/documents?search=لائحة&file_type=pdf')
  .then(res => res.json())
  .then(data => console.log(data));
```

### مثال 3: تحميل ملف
```javascript
fetch('/api/documents/1/download', { method: 'POST' })
  .then(res => res.json())
  .then(data => {
    window.open(data.download_url, '_blank');
  });
```

---

## 8. ملاحظات

- الملفات تُحفظ في: `storage/app/public/documents/files/`
- الأكفر تُحفظ في: `storage/app/public/documents/covers/`
- يمكن استخدام هذا النظام لعرض محتوى في أقسام مثل "اللوائح" و "أخبار الوقف"
- الكلمات الدلالية تساعد في تحسين البحث والتصنيف
