# نظام فئات الخدمات (Categories System)

## نظرة عامة
تم إضافة نظام فئات شامل للخدمات يسمح بتصنيف وتنظيم الخدمات بشكل أفضل.

## المميزات ✨

### 1. إدارة الفئات الكاملة
- ✅ إنشاء فئات جديدة
- ✅ تعديل الفئات
- ✅ حذف الفئات (مع الحماية إذا كانت تحتوي على خدمات)
- ✅ تفعيل/تعطيل الفئات
- ✅ ترتيب الفئات
- ✅ دعم صور الفئات
- ✅ أيقونات FontAwesome
- ✅ ألوان مخصصة لكل فئة

### 2. ربط الفئات بالخدمات
- ✅ إضافة category_id للخدمات
- ✅ اختيار الفئة عند إنشاء/تعديل الخدمة
- ✅ عرض الفئة مع تفاصيل الخدمة
- ✅ حساب عدد الخدمات لكل فئة

### 3. واجهة Admin متقدمة
- ✅ قائمة الفئات مع الإحصائيات
- ✅ نماذج إنشاء/تعديل سهلة
- ✅ رابط في القائمة الجانبية
- ✅ عرض مرئي للأيقونات والألوان

## الملفات المُضافة/المُعدّلة 📁

### Migrations
1. **2025_10_11_143247_create_categories_table.php**
   - إنشاء جدول categories
   - الحقول: id, name, name_en, description, icon, color, image, order, is_active

2. **2025_10_11_143311_add_category_id_to_services_table.php**
   - إضافة category_id إلى جدول services
   - Foreign key مع onDelete('set null')

### Models
1. **app/Models/Category.php** (جديد)
   - العلاقات: hasMany('services')
   - Scopes: active(), ordered()
   - Accessor: active_services_count

2. **app/Models/Service.php** (محدّث)
   - إضافة category_id إلى fillable
   - إضافة علاقة belongsTo('category')

### Controllers
1. **app/Http/Controllers/Admin/CategoryController.php** (جديد)
   - index() - عرض جميع الفئات
   - create() - نموذج إنشاء فئة
   - store() - حفظ فئة جديدة
   - edit() - نموذج تعديل فئة
   - update() - تحديث فئة
   - destroy() - حذف فئة
   - toggleActive() - تفعيل/تعطيل فئة

### Views
1. **resources/views/admin/categories/index.blade.php** (جديد)
   - جدول الفئات مع الصور والأيقونات
   - أزرار التحكم (تعديل، حذف، تفعيل)
   - عداد الخدمات لكل فئة

2. **resources/views/admin/categories/create.blade.php** (جديد)
   - نموذج إضافة فئة جديدة
   - اختيار الأيقونة من FontAwesome
   - رفع صورة
   - معاينة الصورة
   - اختيار اللون

3. **resources/views/admin/categories/edit.blade.php** (جديد)
   - نموذج تعديل فئة
   - عرض الصورة الحالية
   - جميع الحقول قابلة للتعديل

4. **resources/views/admin/services/create.blade.php** (محدّث)
   - إضافة قائمة اختيار الفئة
   - عرض الفئات النشطة فقط

5. **resources/views/admin/services/edit.blade.php** (محدّث)
   - إضافة قائمة اختيار الفئة
   - عرض الفئة الحالية

6. **resources/views/layouts/admin.blade.php** (محدّث)
   - إضافة رابط "الفئات" في القائمة الجانبية
   - الأيقونة: fas fa-folder

### Routes
**routes/web.php** (محدّث)
```php
// Categories Management
Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
Route::patch('categories/{category}/toggle-active', [..., 'toggleActive'])->name('categories.toggle-active');
```

### Seeders
**database/seeders/CategorySeeder.php** (جديد)
- 6 فئات افتراضية:
  1. أفراح وزفاف (fas fa-ring, #ef4870)
  2. مؤتمرات وفعاليات (fas fa-briefcase, #1f144a)
  3. حفلات الأطفال (fas fa-birthday-cake, #f0c71d)
  4. مناسبات اجتماعية (fas fa-users, #2dbcae)
  5. معارض تجارية (fas fa-store, #7269b0)
  6. حفلات تخرج (fas fa-graduation-cap, #28a745)

## هيكل جدول categories

```sql
CREATE TABLE categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    name_en VARCHAR(255) NULL,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    color VARCHAR(20) DEFAULT '#1f144a',
    image VARCHAR(255) NULL,
    `order` INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(is_active),
    INDEX(`order`)
);
```

## Routes المتاحة

| Method | URI | Name | الوصف |
|--------|-----|------|-------|
| GET | /admin/categories | admin.categories.index | قائمة الفئات |
| GET | /admin/categories/create | admin.categories.create | نموذج إضافة فئة |
| POST | /admin/categories | admin.categories.store | حفظ فئة جديدة |
| GET | /admin/categories/{id}/edit | admin.categories.edit | نموذج تعديل فئة |
| PUT/PATCH | /admin/categories/{id} | admin.categories.update | تحديث فئة |
| DELETE | /admin/categories/{id} | admin.categories.destroy | حذف فئة |
| PATCH | /admin/categories/{id}/toggle-active | admin.categories.toggle-active | تفعيل/تعطيل |

## كيفية الاستخدام 🚀

### 1. الوصول إلى إدارة الفئات
```
لوحة التحكم > القائمة الجانبية > الفئات
أو
http://yourdomain.com/admin/categories
```

### 2. إضافة فئة جديدة
1. اضغط على "إضافة فئة جديدة"
2. أدخل اسم الفئة (عربي وإنجليزي)
3. أضف وصف الفئة
4. اختر أيقونة من [FontAwesome](https://fontawesome.com/icons)
5. اختر لون الفئة
6. ارفع صورة (اختياري)
7. حدد الترتيب
8. احفظ

### 3. ربط الخدمة بفئة
عند إنشاء/تعديل خدمة:
1. اختر الفئة من القائمة المنسدلة
2. أو اترك الحقل فارغاً (الفئة اختيارية)

### 4. حذف فئة
- يمكن حذف الفئة فقط إذا لم تحتوي على خدمات
- إذا كانت تحتوي على خدمات، ستظهر رسالة خطأ

## الإحصائيات والبيانات 📊

### الفئات الافتراضية (تم إضافتها):
```
✅ 6 فئات تم إنشاؤها
✅ جميعها نشطة
✅ مرتبة حسب الأولوية
✅ كل فئة لها أيقونة ولون مميز
```

### قاعدة البيانات:
```sql
-- عدد الفئات
SELECT COUNT(*) FROM categories;  -- النتيجة: 6

-- الفئات النشطة
SELECT COUNT(*) FROM categories WHERE is_active = 1;  -- النتيجة: 6

-- الخدمات حسب الفئة
SELECT 
    c.name, 
    COUNT(s.id) as services_count 
FROM categories c 
LEFT JOIN services s ON c.id = s.category_id 
GROUP BY c.id;
```

## أمثلة على الاستخدام 💡

### في Blade Templates
```php
// عرض جميع الفئات النشطة
@foreach(\App\Models\Category::active()->ordered()->get() as $category)
    <div class="category" style="color: {{ $category->color }}">
        <i class="{{ $category->icon }}"></i>
        <h3>{{ $category->name }}</h3>
        <p>{{ $category->description }}</p>
        <span>{{ $category->active_services_count }} خدمة</span>
    </div>
@endforeach

// عرض فئة الخدمة
@if($service->category)
    <span class="badge" style="background-color: {{ $service->category->color }}">
        <i class="{{ $service->category->icon }} me-1"></i>
        {{ $service->category->name }}
    </span>
@endif
```

### في Controllers
```php
// الحصول على الفئات مع عدد الخدمات
$categories = Category::withCount('services')->ordered()->get();

// الحصول على خدمات فئة معينة
$category = Category::find(1);
$services = $category->services()->active()->get();

// البحث في الخدمات حسب الفئة
$services = Service::where('category_id', $categoryId)->get();
```

## التحسينات المستقبلية 🔮

### مقترحات للتطوير:
- [ ] صفحة عامة لعرض الفئات للزوار
- [ ] فلترة الخدمات حسب الفئة في الصفحة العامة
- [ ] إحصائيات متقدمة لكل فئة
- [ ] تصدير/استيراد الفئات
- [ ] فئات فرعية (Sub-categories)
- [ ] ترتيب الفئات بالـ Drag & Drop

## الاختبار ✅

### تم اختباره:
```bash
# 1. Migration
php artisan migrate  ✅

# 2. Seeder
php artisan db:seed --class=CategorySeeder  ✅

# 3. Routes
php artisan route:list | grep categories  ✅

# 4. Database
mysql> SELECT * FROM categories;  ✅

# 5. الواجهة
- http://yourdomain.com/admin/categories  ✅
```

## الأوامر المفيدة 🛠️

```bash
# إعادة تشغيل Seeder
php artisan db:seed --class=CategorySeeder

# مسح الكاش
php artisan optimize:clear

# عرض الـ Routes
php artisan route:list | grep categories

# فحص قاعدة البيانات
mysql -u root -p your_events -e "SELECT * FROM categories;"
```

## الملاحظات المهمة ⚠️

1. **الفئات اختيارية:** الخدمات يمكن أن تكون بدون فئة (category_id = NULL)
2. **حماية الحذف:** لا يمكن حذف فئة تحتوي على خدمات
3. **الترتيب:** الفئات تُعرض حسب حقل `order` (الأرقام الأصغر أولاً)
4. **الصور:** يتم حفظ الصور في `storage/app/public/categories/`
5. **الأيقونات:** استخدم FontAwesome 6.x classes

## الدعم والمساعدة 📞

### في حالة المشاكل:
```bash
# مسح الكاش
php artisan optimize:clear

# التأكد من الـ Routes
php artisan route:clear
php artisan route:cache

# التأكد من الـ Storage Link
php artisan storage:link
```

---

**التاريخ:** 11 أكتوبر 2025  
**الحالة:** ✅ مكتمل وجاهز للاستخدام
**الإصدار:** 1.0
