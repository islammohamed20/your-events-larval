# نظام الخدمات المتغيرة (Variable Services System)

## نظرة عامة
تم تطوير نظام الخدمات ليصبح مشابهاً لـ WooCommerce مع دعم:
- **خدمات بسيطة (Simple Services)**: لها سعر ثابت واحد
- **خدمات متغيرة (Variable Services)**: لها أسعار متعددة حسب الخصائص المختارة

---

## 🗄️ بنية قاعدة البيانات

### 1. جدول `services`
**الأعمدة الجديدة:**
- `service_type`: enum('simple', 'variable') - نوع الخدمة
- `price`: السعر (للخدمات البسيطة فقط)

### 2. جدول `attributes` - الخصائص
```sql
- id
- name: اسم الخاصية (عدد الأشخاص، المدينة)
- slug: اسم نظيف للاستخدام في الكود
- type: نوع الحقل (select, radio, checkbox)
- order: ترتيب الظهور
- is_active: نشط/غير نشط
```

### 3. جدول `attribute_values` - قيم الخصائص
```sql
- id
- attribute_id: معرف الخاصية
- value: القيمة (50-100 شخص، الرياض)
- slug: اسم نظيف
- order: ترتيب الظهور
- is_active: نشط/غير نشط
```

### 4. جدول `attribute_service` - ربط الخدمات بالخصائص
```sql
- id
- service_id: معرف الخدمة
- attribute_id: معرف الخاصية
- order: ترتيب ظهور الخاصية في الخدمة
```

### 5. جدول `service_variations` - التنويعات
```sql
- id
- service_id: معرف الخدمة
- sku: كود التعريف
- attributes: JSON - {"guests": "50-100", "city": "riyadh"}
- price: السعر للتركيبة
- sale_price: سعر الخصم (اختياري)
- stock: الكمية المتاحة (اختياري)
- is_active: نشط/غير نشط
```

---

## 📦 Models المضافة/المحدثة

### Attribute Model
```php
// العلاقات:
- values() -> hasMany(AttributeValue)
- services() -> belongsToMany(Service)

// Scopes:
- active()
- ordered()
```

### AttributeValue Model
```php
// العلاقات:
- attribute() -> belongsTo(Attribute)

// Scopes:
- active()
- ordered()
```

### ServiceVariation Model
```php
// العلاقات:
- service() -> belongsTo(Service)

// Accessors:
- active_price: السعر الفعال (sale_price أو price)
- on_sale: هل هناك خصم؟
- formatted_attributes: الخصائص بشكل منسق للعرض
```

### Service Model (محدث)
```php
// Methods جديدة:
- isVariable(): هل الخدمة متغيرة؟
- isSimple(): هل الخدمة بسيطة؟
- getMinPriceAttribute(): أقل سعر (للخدمات المتغيرة)
- getMaxPriceAttribute(): أعلى سعر (للخدمات المتغيرة)
- getPriceRangeAttribute(): نطاق السعر منسق

// العلاقات الجديدة:
- attributes() -> belongsToMany(Attribute)
- variations() -> hasMany(ServiceVariation)
```

---

## 🎛️ Controllers

### AttributeController (محدث)
**Routes:**
```php
- GET    /admin/attributes              -> index
- GET    /admin/attributes/create       -> create
- POST   /admin/attributes              -> store
- GET    /admin/attributes/{id}/edit    -> edit
- PUT    /admin/attributes/{id}         -> update
- DELETE /admin/attributes/{id}         -> destroy

// Attribute Values
- POST   /admin/attributes/{id}/values           -> storeValue
- PUT    /admin/attributes/{id}/values/{value}   -> updateValue
- DELETE /admin/attributes/{id}/values/{value}   -> destroyValue
```

**التحسينات:**
- إضافة حقول `type` و `order`
- توليد slug تلقائي
- ترتيب النتائج حسب order

### ServiceController (يحتاج تحديث)
**التحديثات المطلوبة:**
```php
1. في create/edit: إضافة حقل service_type
2. عند اختيار "variable":
   - عرض قائمة الخصائص المتاحة للربط
   - إمكانية إنشاء التنويعات يدوياً
   - زر "توليد تلقائي" للتنويعات
3. عند save:
   - حفظ الخصائص المختارة في attribute_service
   - حفظ التنويعات في service_variations
```

---

## 🖥️ الواجهات المطلوبة

### 1. لوحة التحكم - إدارة الخصائص

#### `resources/views/admin/attributes/index.blade.php`
```html
- جدول بكل الخصائص
- أعمدة: الاسم، النوع، عدد القيم، الحالة، الترتيب، الإجراءات
- أزرار: إضافة خاصية جديدة، تعديل، حذف
```

#### `resources/views/admin/attributes/create.blade.php`
```html
نموذج إنشاء خاصية:
- اسم الخاصية (مطلوب)
- Slug (اختياري - يتم توليده تلقائياً)
- النوع: select/radio/checkbox
- الترتيب
- نشط/غير نشط
```

#### `resources/views/admin/attributes/edit.blade.php`
```html
نموذج تعديل الخاصية + إدارة القيم:
- نفس حقول create
- قسم "قيم الخاصية":
  - جدول بكل القيم (القيمة، Slug، الترتيب، الحالة، الإجراءات)
  - نموذج إضافة قيمة جديدة سريع
  - إمكانية تعديل/حذف القيم
```

### 2. لوحة التحكم - إدارة الخدمات (محدث)

#### `resources/views/admin/services/create.blade.php` & `edit.blade.php`
```html
التحديثات المطلوبة:

1. إضافة حقل "نوع الخدمة":
   ( ) خدمة بسيطة - سعر ثابت
   ( ) خدمة متغيرة - خيارات متعددة

2. إذا كانت "بسيطة":
   - عرض حقل السعر فقط

3. إذا كانت "متغيرة":
   أ) قسم اختيار الخصائص:
      ☑ عدد الأشخاص
      ☑ المدينة
      ☐ نوع الحفلة
      (مع إمكانية ترتيب الخصائص)

   ب) قسم التنويعات:
      - عرض جدول التنويعات الحالية
      - زر "توليد التنويعات تلقائياً" (ينشئ كل التركيبات)
      - إمكانية إضافة تنويعة يدوياً
      - كل تنويعة تحتوي:
        * قوائم منسدلة للخصائص
        * السعر
        * سعر الخصم (اختياري)
        * الكمية (اختياري)
        * SKU (اختياري)
        * نشط/غير نشط
```

**مثال على جدول التنويعات:**
```
عدد الأشخاص | المدينة  | السعر    | سعر الخصم | الكمية | الإجراءات
50-100       | الرياض   | 5000 ر.س |           | 10     | تعديل حذف
50-100       | جدة      | 5500 ر.س | 5000 ر.س  | 5      | تعديل حذف
100-200      | الرياض   | 8000 ر.س |           | 8      | تعديل حذف
```

### 3. الواجهة الأمامية - عرض الخدمة

#### `resources/views/services/show.blade.php` (محدث)
```html
التحديثات:

1. إذا كانت الخدمة بسيطة:
   <div class="price">
       <h3>5000 ر.س</h3>
       <button>أضف للسلة</button>
   </div>

2. إذا كانت الخدمة متغيرة:
   <div class="variations">
       <!-- قائمة منسدلة لكل خاصية -->
       <select name="guests" id="guests-select">
           <option value="">اختر عدد الأشخاص</option>
           <option value="50-100">50-100 شخص</option>
           <option value="100-200">100-200 شخص</option>
       </select>

       <select name="city" id="city-select">
           <option value="">اختر المدينة</option>
           <option value="riyadh">الرياض</option>
           <option value="jeddah">جدة</option>
       </select>

       <!-- عرض السعر الديناميكي -->
       <div class="price-container">
           <h3 id="current-price">اختر الخيارات لعرض السعر</h3>
       </div>

       <button id="add-to-cart" disabled>أضف للسلة</button>
   </div>

   <script>
   // عند تغيير أي قائمة، نرسل طلب AJAX لجلب السعر
   $('select[name="guests"], select[name="city"]').on('change', function() {
       let guests = $('#guests-select').val();
       let city = $('#city-select').val();

       if (guests && city) {
           $.ajax({
               url: '/api/services/{{ $service->id }}/get-price',
               method: 'POST',
               data: {
                   guests: guests,
                   city: city,
                   _token: '{{ csrf_token() }}'
               },
               success: function(response) {
                   $('#current-price').text(response.price + ' ر.س');
                   $('#add-to-cart').prop('disabled', false)
                                   .data('variation-id', response.variation_id);
               }
           });
       }
   });
   </script>
```

---

## 🔌 API Endpoints المطلوبة

### 1. جلب السعر حسب الخصائص المختارة
```php
// Route
POST /api/services/{service}/get-price

// Controller Method
public function getVariationPrice(Request $request, Service $service)
{
    $attributes = $request->except('_token');
    
    // تحويل الخصائص إلى صيغة JSON للبحث
    $attributesJson = [];
    foreach ($attributes as $key => $value) {
        // تحويل slug إلى نفس الصيغة المخزنة
        $attribute = Attribute::where('slug', $key)->first();
        if ($attribute) {
            $attributesJson[$attribute->slug] = $value;
        }
    }
    
    // البحث عن التنويعة المطابقة
    $variation = ServiceVariation::where('service_id', $service->id)
                                 ->where('attributes', json_encode($attributesJson))
                                 ->active()
                                 ->first();
    
    if ($variation) {
        return response()->json([
            'success' => true,
            'variation_id' => $variation->id,
            'price' => number_format($variation->active_price, 2),
            'original_price' => $variation->price,
            'sale_price' => $variation->sale_price,
            'on_sale' => $variation->on_sale,
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'هذه التركيبة غير متاحة'
    ], 404);
}
```

### 2. توليد التنويعات تلقائياً
```php
// Route
POST /admin/services/{service}/generate-variations

// Controller Method
public function generateVariations(Service $service)
{
    if (!$service->isVariable()) {
        return back()->with('error', 'هذه الخدمة ليست متغيرة');
    }
    
    // جلب كل الخصائص المرتبطة بالخدمة
    $attributes = $service->attributes()->with('values')->get();
    
    if ($attributes->isEmpty()) {
        return back()->with('error', 'يجب ربط خصائص بالخدمة أولاً');
    }
    
    // توليد كل التركيبات الممكنة
    $combinations = $this->generateCombinations($attributes);
    
    $created = 0;
    foreach ($combinations as $combination) {
        // التحقق من عدم وجود التنويعة
        $exists = ServiceVariation::where('service_id', $service->id)
                                  ->where('attributes', json_encode($combination))
                                  ->exists();
        
        if (!$exists) {
            ServiceVariation::create([
                'service_id' => $service->id,
                'attributes' => $combination,
                'price' => 0, // يجب تعيين السعر يدوياً لاحقاً
                'is_active' => false, // غير نشط حتى يتم تعيين السعر
            ]);
            $created++;
        }
    }
    
    return back()->with('success', "تم إنشاء {$created} تنويعة جديدة");
}

private function generateCombinations($attributes, $index = 0, $current = [])
{
    if ($index == count($attributes)) {
        return [$current];
    }
    
    $results = [];
    $attribute = $attributes[$index];
    
    foreach ($attribute->values as $value) {
        $newCurrent = $current;
        $newCurrent[$attribute->slug] = $value->slug;
        $results = array_merge($results, $this->generateCombinations($attributes, $index + 1, $newCurrent));
    }
    
    return $results;
}
```

---

## 📝 Routes المطلوبة

### في `routes/web.php`
```php
// Admin - Attributes Management
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Attributes CRUD
    Route::resource('attributes', AttributeController::class);
    
    // Attribute Values
    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])
         ->name('attributes.values.store');
    Route::put('attributes/{attribute}/values/{value}', [AttributeController::class, 'updateValue'])
         ->name('attributes.values.update');
    Route::delete('attributes/{attribute}/values/{value}', [AttributeController::class, 'destroyValue'])
         ->name('attributes.values.destroy');
    
    // Services - Generate Variations
    Route::post('services/{service}/generate-variations', [ServiceController::class, 'generateVariations'])
         ->name('services.generate-variations');
    
    // Services - Store Variations
    Route::post('services/{service}/variations', [ServiceController::class, 'storeVariation'])
         ->name('services.variations.store');
    Route::put('services/{service}/variations/{variation}', [ServiceController::class, 'updateVariation'])
         ->name('services.variations.update');
    Route::delete('services/{service}/variations/{variation}', [ServiceController::class, 'destroyVariation'])
         ->name('services.variations.destroy');
});
```

### في `routes/api.php`
```php
// Public API for getting variation price
Route::post('services/{service}/get-price', [ServiceController::class, 'getVariationPrice'])
     ->name('api.services.get-price');
```

---

## ✅ خطوات التنفيذ المتبقية

### 1. ✅ إنشاء Migrations (مكتمل)
- [x] تحديث جدول services
- [x] إنشاء جداول attributes, attribute_values, attribute_service, service_variations

### 2. ✅ تحديث Models (مكتمل)
- [x] Attribute Model
- [x] AttributeValue Model  
- [x] ServiceVariation Model
- [x] تحديث Service Model

### 3. ✅ تحديث AttributeController (مكتمل)
- [x] إضافة دعم type و order

### 4. ⏳ تحديث ServiceController (مطلوب)
- [ ] دعم حقل service_type
- [ ] ربط الخصائص بالخدمة
- [ ] إدارة التنويعات (CRUD)
- [ ] توليد التنويعات تلقائياً
- [ ] API للحصول على السعر

### 5. ⏳ إنشاء الواجهات (مطلوب)
- [ ] admin/attributes/index.blade.php
- [ ] admin/attributes/create.blade.php
- [ ] admin/attributes/edit.blade.php
- [ ] تحديث admin/services/create.blade.php
- [ ] تحديث admin/services/edit.blade.php
- [ ] تحديث services/show.blade.php (frontend)

### 6. ⏳ إضافة Routes (مطلوب)
- [ ] routes للخصائص
- [ ] routes للتنويعات
- [ ] API route لجلب السعر

### 7. ⏳ JavaScript/AJAX (مطلوب)
- [ ] سكريبت تحديث السعر الديناميكي
- [ ] سكريبت إدارة التنويعات في الإدارة

---

## 🎯 مثال عملي كامل

### السيناريو: خدمة "تنظيم حفلات الزفاف"

#### 1. إنشاء الخصائص:
```
خاصية 1: عدد الأشخاص
  - 50-100 شخص
  - 100-200 شخص
  - 200-300 شخص

خاصية 2: المدينة
  - الرياض
  - جدة
  - الدمام

خاصية 3: نوع القاعة
  - قاعة داخلية
  - حديقة خارجية
  - فندق 5 نجوم
```

#### 2. إنشاء الخدمة:
- الاسم: تنظيم حفلات الزفاف
- النوع: متغيرة
- الخصائص المرتبطة: عدد الأشخاص، المدينة، نوع القاعة
- عدد التنويعات الممكنة: 3 × 3 × 3 = 27 تنويعة

#### 3. التنويعات (أمثلة):
```
50-100 | الرياض | داخلية    → 8,000 ر.س
50-100 | الرياض | خارجية    → 10,000 ر.س
50-100 | الرياض | فندق 5*   → 15,000 ر.س
100-200 | جدة   | داخلية    → 12,000 ر.س
...
```

#### 4. في الواجهة الأمامية:
```
[عدد الأشخاص ▼] → اختر
[المدينة ▼]     → اختر
[نوع القاعة ▼]  → اختر

السعر: اختر الخيارات لعرض السعر
[أضف للسلة] (معطل)

↓ بعد اختيار الخيارات ↓

[عدد الأشخاص ▼] → 50-100
[المدينة ▼]     → الرياض
[نوع القاعة ▼]  → داخلية

السعر: 8,000 ر.س ✅
[أضف للسلة] (نشط)
```

---

## 📚 ملاحظات مهمة

1. **التوليد التلقائي للتنويعات**:
   - يجب على المدير مراجعة التنويعات وتحديد الأسعار قبل تفعيلها
   - التنويعات غير المسعرة تبقى غير نشطة

2. **التحقق من صحة البيانات**:
   - التأكد من وجود variation_id عند إضافة للسلة
   - التحقق من توفر الكمية (إذا كانت محددة)

3. **تحسينات مستقبلية**:
   - إمكانية رفع صور لكل تنويعة
   - ربط التنويعات بمواعيد محددة (bookings)
   - تقارير عن التنويعات الأكثر مبيعاً

---

تم إنشاء هذا التوثيق بتاريخ: 20 أكتوبر 2025
