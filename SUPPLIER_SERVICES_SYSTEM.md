# نظام اختيار الفئات والخدمات الديناميكي - 8 ديسمبر 2025

## 📋 ملخص النظام

تم تطبيق نظام متقدم لتسجيل الموردين يسمح باختيار الفئات والخدمات ديناميكياً مع حماية كاملة ضد الأخطاء.

## ✨ الميزات الرئيسية

### 1. اختيار الفئات (Categories)
- عرض جميع الفئات النشطة كـ Checkboxes
- أيقونات وصور لكل فئة
- اختيار متعدد (فئة واحدة أو أكثر)
- إلزامي: فئة واحدة على الأقل

### 2. اختيار الخدمات الديناميكي
- عند اختيار فئة → ظهور خدمات تلك الفئة فقط
- تجميع الخدمات حسب الفئة بتصميم احترافي
- عرض سعر الخدمة
- اختيار متعدد (Multi-select) داخل الفئة
- إلزامي: خدمة واحدة على الأقل

### 3. الحماية والتحقق
- منع اختيار خدمات من فئات غير مختارة (من الجانب العميل)
- التحقق من الخادم: جميع الخدمات تنتمي للفئات المختارة
- رسائل خطأ واضحة ومحددة
- تمرير تلقائي للحقل الخاطئ

### 4. تخزين البيانات (Many-to-Many)
```
Supplier ↔ Service through SupplierServices
Supplier ↔ Category through SupplierServices
```

## 🗄️ هيكل قاعدة البيانات

### جدول SupplierServices

```sql
CREATE TABLE supplier_services (
    id BIGINT PRIMARY KEY,
    supplier_id BIGINT NOT NULL FK,
    service_id BIGINT NOT NULL FK,
    category_id BIGINT NOT NULL FK,
    base_price DECIMAL(10,2) NULL,
    notes TEXT NULL,
    is_available BOOLEAN DEFAULT true,
    priority INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(supplier_id, service_id),
    INDEX(supplier_id),
    INDEX(service_id),
    INDEX(category_id)
);
```

### العلاقات

```php
// In Supplier Model
$supplier->services()          // جميع الخدمات
$supplier->serviceCategories() // الفئات المختارة
$supplier->supplierServices()  // تفاصيل العلاقة

// In Service Model
$service->suppliers()          // الموردين

// In Category Model
$category->suppliers()         // الموردين
```

## 📝 الملفات المعدلة

### 1. Models (3 ملفات)

#### `app/Models/Supplier.php`
```php
// علاقة Many-to-Many مع Service
public function services()
{
    return $this->belongsToMany(
        Service::class,
        'supplier_services',
        'supplier_id',
        'service_id'
    )->withPivot('category_id', 'base_price', 'notes', 'is_available', 'priority')
     ->withTimestamps();
}

// علاقة Many-to-Many مع Category
public function serviceCategories()
{
    return $this->belongsToMany(
        Category::class,
        'supplier_services',
        'supplier_id',
        'category_id'
    )->distinct();
}

// الوصول إلى تفاصيل العلاقة
public function supplierServices()
{
    return $this->hasMany(SupplierService::class, 'supplier_id');
}
```

#### `app/Models/Service.php`
```php
// علاقة Many-to-Many مع Supplier
public function suppliers()
{
    return $this->belongsToMany(
        Supplier::class,
        'supplier_services',
        'service_id',
        'supplier_id'
    )->withPivot('category_id', 'base_price', 'notes', 'is_available', 'priority')
     ->withTimestamps();
}
```

#### `app/Models/Category.php`
```php
// علاقة Many-to-Many مع Supplier
public function suppliers()
{
    return $this->belongsToMany(
        Supplier::class,
        'supplier_services',
        'category_id',
        'supplier_id'
    )->distinct();
}
```

### 2. Controller (1 ملف)

#### `app/Http/Controllers/SupplierController.php`

**دالة create():**
```php
public function create()
{
    $categories = Category::active()->ordered()->get();
    $allServices = Service::where('is_active', true)
        ->with('category')
        ->get()
        ->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'category_id' => $service->category_id,
                'price' => $service->price,
            ];
        });

    return view('suppliers.register', compact('categories', 'allServices'));
}
```

**دالة store():**
- التحقق من البيانات (Validation)
- حفظ بيانات المورد
- حفظ الخدمات في SupplierServices
- إرسال OTP للتحقق من البريد

```php
// تحقق من أن جميع الخدمات تنتمي للفئات المختارة
$services = Service::whereIn('id', $selectedServiceIds)->get();
$invalidServices = $services->whereNotIn('category_id', $selectedCategoryIds);

if ($invalidServices->count() > 0) {
    return back()->withInput()->withErrors([
        'services' => 'بعض الخدمات المختارة لا تنتمي إلى الفئات المحددة'
    ]);
}

// حفظ الخدمات في جدول SupplierServices
foreach ($selectedServiceIds as $serviceId) {
    $service = Service::find($serviceId);
    if ($service) {
        SupplierService::create([
            'supplier_id' => $supplier->id,
            'service_id' => $service->id,
            'category_id' => $service->category_id,
            'is_available' => true,
        ]);
    }
}
```

### 3. View (1 ملف)

#### `resources/views/suppliers/register.blade.php`

**الخطوة 1: اختيار الفئات**
```blade
@foreach($categories as $category)
<div class="col-md-6">
    <div class="form-check border rounded-3 p-3 h-100 category-checkbox">
        <input class="form-check-input category-checkbox-input" 
               type="checkbox" 
               name="categories[]" 
               value="{{ $category->id }}" 
               id="category_{{ $category->id }}"
               data-category-id="{{ $category->id }}">
        <label class="form-check-label w-100 cursor-pointer" for="category_{{ $category->id }}">
            {{-- عرض الفئة مع الأيقونة --}}
        </label>
    </div>
</div>
@endforeach
```

**الخطوة 2: اختيار الخدمات (ديناميكي)**
```blade
<div id="servicesContainer">
    <!-- يتم ملء هذا الجزء بواسطة JavaScript -->
</div>
```

## 🔄 كود JavaScript

### البيانات الأولية
```javascript
const allServices = @json($allServices ?? []);
const selectedCategories = @json(old('categories', []));
const selectedServices = @json(old('services', []));
```

### مستمعات التغيير
```javascript
document.querySelectorAll('.category-checkbox-input').forEach(checkbox => {
    checkbox.addEventListener('change', updateServices);
});
```

### دالة updateServices()
```javascript
function updateServices() {
    // 1. الحصول على الفئات المختارة
    const selected = Array.from(document.querySelectorAll('.category-checkbox-input:checked'))
        .map(c => parseInt(c.value));

    if (selected.length === 0) {
        // عرض رسالة "اختر فئات أولاً"
        return;
    }

    // 2. فلترة الخدمات
    const filteredServices = allServices.filter(service => 
        selected.includes(service.category_id)
    );

    // 3. تجميع حسب الفئة
    const servicesByCategory = {};
    filteredServices.forEach(service => {
        if (!servicesByCategory[service.category_id]) {
            servicesByCategory[service.category_id] = [];
        }
        servicesByCategory[service.category_id].push(service);
    });

    // 4. بناء HTML ديناميكي
    let html = '';
    selected.forEach(categoryId => {
        const services = servicesByCategory[categoryId] || [];
        if (services.length > 0) {
            html += `<div class="mb-4">
                <h6 class="text-muted mb-3 pb-2 border-bottom">
                    الفئة: ${categoryName}
                </h6>
                <div class="row g-3">`;
            
            services.forEach(service => {
                html += `
                    <div class="col-md-6">
                        <div class="form-check border rounded-3 p-3 service-checkbox">
                            <input class="form-check-input service-checkbox-input" 
                                   type="checkbox" 
                                   name="services[]" 
                                   value="${service.id}" 
                                   data-category-id="${service.category_id}">
                            <label class="form-check-label">
                                ${service.name}
                                ${service.price ? `<small>${service.price} ريال</small>` : ''}
                            </label>
                        </div>
                    </div>
                `;
            });
            html += '</div></div>';
        }
    });

    document.getElementById('servicesContainer').innerHTML = html;
    validateServices();
}
```

### دالة validateServices()
```javascript
function validateServices() {
    const selected = Array.from(document.querySelectorAll('.category-checkbox-input:checked'))
        .map(c => parseInt(c.value));

    // منع اختيار خدمات من فئات غير مختارة
    document.querySelectorAll('.service-checkbox-input').forEach(checkbox => {
        const categoryId = parseInt(checkbox.getAttribute('data-category-id'));
        const isAllowed = selected.includes(categoryId);
        
        if (!isAllowed && checkbox.checked) {
            checkbox.checked = false;
        }
    });
}
```

### التحقق عند الإرسال
```javascript
form.addEventListener('submit', (e) => {
    const servicesChecked = document.querySelectorAll('.service-checkbox-input:checked').length > 0;
    const categoriesChecked = document.querySelectorAll('.category-checkbox-input:checked').length > 0;

    if (!categoriesChecked) {
        e.preventDefault();
        alert('يرجى اختيار فئة واحدة على الأقل');
        return false;
    }

    if (!servicesChecked) {
        e.preventDefault();
        alert('يرجى اختيار خدمة واحدة على الأقل');
        return false;
    }
});
```

## 📊 مثال على البيانات المحفوظة

### بعد تسجيل مورد

**جدول Supplier:**
```
id: 1
name: "شركة الأحمري للديكور"
supplier_type: "company"
email: "info@ahmari.com"
status: "pending"
```

**جدول SupplierServices:**
```
ID  | supplier_id | service_id | category_id | is_available
----|-------------|------------|-------------|-------------
1   | 1           | 5          | 2           | true
2   | 1           | 6          | 2           | true
3   | 1           | 12         | 4           | true
4   | 1           | 13         | 4           | true
```

## 🧪 الاختبار

1. **اختبار اختيار الفئات:**
   ```
   ✓ اختر فئة واحدة
   ✓ اختر عدة فئات
   ✓ ألغ اختيار فئة → الخدمات تختفي تلقائياً
   ```

2. **اختبار اختيار الخدمات:**
   ```
   ✓ اختر خدمات من الفئة المختارة
   ✓ اختر خدمات من عدة فئات
   ✓ حاول اختيار خدمة من فئة غير مختارة → تُزال تلقائياً
   ```

3. **اختبار الإرسال:**
   ```
   ✓ أرسل بدون فئات → خطأ
   ✓ أرسل بفئات بدون خدمات → خطأ
   ✓ أرسل بفئات وخدمات صحيحة → نجح
   ```

## 🔐 الأمان

- **Validation على الخادم:** التحقق من أن جميع الخدمات تنتمي للفئات المختارة
- **Unique Constraint:** لا يمكن لنفس المورد إضافة نفس الخدمة مرتين
- **Foreign Keys:** حذف تلقائي عند حذف المورد أو الخدمة
- **Indexes:** بحث سريع على supplier_id, service_id, category_id

## 📱 التصميم

- **واجهة عربية بالكامل:** جميع التسميات والرسائل بالعربية
- **Design Responsive:** يعمل على جميع الأجهزة
- **UX محسّن:** تصميم بديهي وسهل الاستخدام
- **Accessibility:** عناصر قابلة للوصول

## 🚀 الاستخدام

```php
// الوصول إلى خدمات المورد
$supplier = Supplier::find(1);
$supplier->services; // جميع الخدمات
$supplier->serviceCategories; // الفئات المختارة
$supplier->supplierServices; // التفاصيل

// الوصول إلى الموردين الذين يقدمون خدمة معينة
$service = Service::find(5);
$service->suppliers; // جميع الموردين

// الوصول إلى الموردين في فئة معينة
$category = Category::find(2);
$category->suppliers; // جميع الموردين
```

## 📝 ملاحظات

- النظام يدعم إضافة/تعديل الخدمات من لوحة التحكم لاحقاً
- يمكن تعديل الأسعار والملاحظات لكل خدمة بعد التسجيل
- النظام يدعم تفعيل/تعطيل الخدمات بشكل مستقل

## ✅ الحالة

✅ تم التطبيق بالكامل وجاهز للاستخدام الفوري

---

**آخر تحديث:** 8 ديسمبر 2025
