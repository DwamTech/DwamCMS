# دليل إدارة المستخدمين والمشرفين

هذا المستند يشرح كيفية إدارة الحسابات (الإدارة والمشرفين) من خلال الـ API.

---

## 1. نظرة عامة

النظام يوفر:
- **إدارة الملف الشخصي**: لكل المستخدمين المسجلين (Admin, Editor, Author, Reviewer, User)
- **إدارة المستخدمين**: للأدمن فقط (عرض، إضافة، تعديل، حذف، تغيير كلمة المرور)

---

## 2. الأدوار المتاحة (Roles)

- `admin`: مدير النظام (صلاحيات كاملة)
- `editor`: محرر (يدير المحتوى)
- `author`: كاتب (ينشر مقالات)
- `reviewer`: مراجع (يراجع المحتوى)
- `user`: مستخدم عادي

---

## 3. إدارة الملف الشخصي (Profile Management)

### أ. عرض الملف الشخصي
**GET** `/api/profile`

**Headers:**
```
Authorization: Bearer {token}
```

**الاستجابة:**
```json
{
  "id": 1,
  "name": "أحمد محمد",
  "email": "admin@dwam.com",
  "role": "admin",
  "created_at": "2024-01-01T12:00:00"
}
```

### ب. تحديث الملف الشخصي
**PUT** `/api/profile`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**البيانات:**
```json
{
  "name": "أحمد محمد علي",
  "email": "ahmed@dwam.com",
  "current_password": "password", // إجباري لتغيير كلمة المرور
  "new_password": "newpassword123", // اختياري
  "new_password_confirmation": "newpassword123" // مطلوب مع new_password
}
```

**الاستجابة:**
```json
{
  "message": "تم تحديث الملف الشخصي بنجاح",
  "user": { ... }
}
```

**ملاحظات:**
- يمكن تحديث الاسم والإيميل بدون تغيير كلمة المرور
- لتغيير كلمة المرور، يجب إدخال كلمة المرور الحالية
- الإيميل يجب أن يكون فريد

---

## 4. إدارة المستخدمين (Admin Only)

### أ. قائمة المستخدمين
**GET** `/api/admin/users`

**Headers:**
```
Authorization: Bearer {admin_token}
```

**معلمات البحث (Query Params):**
- `page`: رقم الصفحة
- `role`: تصفية حسب الدور (`admin`, `editor`, `author`, etc.)
- `search`: بحث بالاسم أو الإيميل

**مثال:**
```
GET /api/admin/users?role=editor&search=أحمد
```

**الاستجابة:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "أحمد محمد",
      "email": "ahmed@dwam.com",
      "role": "editor",
      "created_at": "2024-01-01T12:00:00"
    }
  ],
  "current_page": 1,
  "per_page": 20
}
```

### ب. عرض تفاصيل مستخدم
**GET** `/api/admin/users/{id}`

**الاستجابة:**
```json
{
  "id": 1,
  "name": "أحمد محمد",
  "email": "ahmed@dwam.com",
  "role": "editor",
  "created_at": "2024-01-01T12:00:00",
  "updated_at": "2024-01-15T10:30:00"
}
```

### ج. إنشاء مستخدم جديد
**POST** `/api/admin/users`

**البيانات:**
```json
{
  "name": "محمد علي",
  "email": "mohamed@dwam.com",
  "password": "password123",
  "role": "editor"
}
```

**الاستجابة:**
```json
{
  "message": "تم إنشاء الحساب بنجاح",
  "user": { ... }
}
```

### د. تحديث مستخدم
**PUT** `/api/admin/users/{id}`

**البيانات:**
```json
{
  "name": "محمد علي أحمد",
  "email": "mohamed.ali@dwam.com",
  "role": "author"
}
```

**ملاحظات:**
- كل الحقول اختيارية
- لا يمكن تغيير كلمة المرور من هنا (استخدم endpoint منفصل)

**الاستجابة:**
```json
{
  "message": "تم تحديث المستخدم بنجاح",
  "user": { ... }
}
```

### هـ. تغيير كلمة مرور مستخدم
**POST** `/api/admin/users/{id}/change-password`

**البيانات:**
```json
{
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

**الاستجابة:**
```json
{
  "message": "تم تغيير كلمة المرور بنجاح"
}
```

**ملاحظات:**
- الأدمن يستطيع تغيير كلمة مرور أي مستخدم بدون معرفة كلمة المرور القديمة
- يجب أن تكون كلمة المرور 8 أحرف على الأقل

### و. حذف مستخدم
**DELETE** `/api/admin/users/{id}`

**الاستجابة:**
```json
{
  "message": "تم حذف المستخدم بنجاح"
}
```

**ملاحظات:**
- لا يمكن للأدمن حذف حسابه الخاص
- الحذف نهائي ولا يمكن التراجع عنه

---

## 5. أمثلة للاستخدام

### مثال 1: تحديث الملف الشخصي (أي مستخدم)
```javascript
fetch('/api/profile', {
  method: 'PUT',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'أحمد محمد الجديد',
    email: 'ahmed.new@dwam.com'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

### مثال 2: تغيير كلمة المرور الشخصية
```javascript
fetch('/api/profile', {
  method: 'PUT',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    current_password: 'oldpassword',
    new_password: 'newpassword123',
    new_password_confirmation: 'newpassword123'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

### مثال 3: الأدمن يعرض كل المحررين
```javascript
fetch('/api/admin/users?role=editor', {
  headers: {
    'Authorization': 'Bearer ADMIN_TOKEN'
  }
})
.then(res => res.json())
.then(data => console.log(data));
```

### مثال 4: الأدمن يغير كلمة مرور مستخدم
```javascript
fetch('/api/admin/users/5/change-password', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ADMIN_TOKEN',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    new_password: 'newpassword123',
    new_password_confirmation: 'newpassword123'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

### مثال 5: الأدمن يحذف مستخدم
```javascript
fetch('/api/admin/users/5', {
  method: 'DELETE',
  headers: {
    'Authorization': 'Bearer ADMIN_TOKEN'
  }
})
.then(res => res.json())
.then(data => console.log(data));
```

---

## 6. الصلاحيات والأمان

### Profile Management:
- ✅ متاح لـ: **جميع المستخدمين المسجلين** (admin, editor, author, reviewer, user)
- ✅ يمكن تعديل: الاسم، الإيميل، كلمة المرور الخاصة
- ❌ لا يمكن تعديل: الدور (Role)

### User Management:
- ✅ متاح لـ: **Admin فقط**
- ✅ يمكن: عرض، إضافة، تعديل، حذف، تغيير كلمة مرور أي مستخدم
- ❌ الاستثناء: لا يمكن للأدمن حذف حسابه الخاص

---

## 7. رموز الحالة (Status Codes)

- `200`: نجاح العملية
- `201`: تم الإنشاء بنجاح
- `401`: غير مصرح (Token غير صحيح أو منتهي)
- `403`: ممنوع (ليس لديك صلاحية - مثلاً حذف حسابك الخاص)
- `404`: المستخدم غير موجود
- `422`: خطأ في التحقق من البيانات

---

## 8. ملاحظات مهمة

1. **كل الـ endpoints تتطلب توكن** (`Authorization: Bearer {token}`)
2. **Admin endpoints** تحتاج دور `admin` فقط
3. **Profile endpoints** متاحة لأي مستخدم مسجل
4. **تغيير كلمة المرور الشخصية** يتطلب كلمة المرور الحالية
5. **تغيير كلمة مرور مستخدم (Admin)** لا يتطلب كلمة المرور القديمة
6. **الإيميلات** يجب أن تكون فريدة
7. **كلمة المرور** يجب أن تكون 8 أحرف على الأقل
