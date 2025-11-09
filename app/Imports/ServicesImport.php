<?php

namespace App\Imports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Validators\Failure;

class ServicesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithUpserts, WithCustomCsvSettings
{
    use SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Helpers for sanitization
        $sanitizeString = function ($value) {
            if (!isset($value)) return null;
            $value = (string)$value;
            // Normalize to UTF-8
            $normalized = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            // Remove non-printable control chars but KEEP newlines (\n) and carriage returns (\r)
            $normalized = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $normalized);
            // Trim but preserve internal newlines
            $normalized = preg_replace("/^[\s\r\n]+|[\s\r\n]+$/u", '', $normalized);
            return $normalized === '' ? null : $normalized;
        };

        $sanitizeText = $sanitizeString; // same behavior for now

        $sanitizeInt = function ($value) {
            if (!isset($value)) return null;
            // Accept only numeric values
            return is_numeric($value) ? (int)$value : null;
        };

        $sanitizeDecimal = function ($value) {
            if (!isset($value) || $value === '') return null;
            return is_numeric($value) ? (float)$value : null;
        };

        // تحويل "نعم/لا" إلى boolean
        $isActive = isset($row['is_active']) ? 
            (in_array(trim(strtolower((string)$row['is_active'])), ['نعم', 'yes', '1', 'true']) ? 1 : 0) : 1;

        // Map and sanitize incoming data
        $name = $sanitizeString($row['name'] ?? '');
        $subtitle = $sanitizeString($row['subtitle'] ?? null);
        $description = $sanitizeText($row['description'] ?? '');
        $marketingDescription = $sanitizeText($row['marketing_description'] ?? null);
        $whatWeOffer = $sanitizeText($row['what_we_offer'] ?? null);
        $whyChooseUs = $sanitizeText($row['why_choose_us'] ?? null);
        $metaDescription = $sanitizeString($row['meta_description'] ?? null);
        // Respect 160 chars limit for meta
        if ($metaDescription !== null) {
            $metaDescription = mb_substr($metaDescription, 0, 160, 'UTF-8');
        }

        $price = $sanitizeDecimal($row['price'] ?? null);
        $duration = $sanitizeInt($row['duration'] ?? null);
        $type = $sanitizeString($row['type'] ?? null);
        $categoryId = $sanitizeInt($row['category_id'] ?? null);

        // Derive service type correctly (enum: simple | variable)
        $incomingServiceType = isset($row['service_type']) ? strtolower(trim((string)$row['service_type'])) : null;
        $hasVariationsFlag = isset($row['has_variations']) && in_array(trim(strtolower((string)$row['has_variations'])), ['نعم','yes','1','true']);
        $serviceType = ($incomingServiceType === 'variable' || $hasVariationsFlag) ? 'variable' : 'simple';
        $hasVariations = ($serviceType === 'variable');

        $data = [
            'name' => $name ?? '',
            'subtitle' => $subtitle,
            'category_id' => $categoryId,
            'description' => $description ?? '',
            'marketing_description' => $marketingDescription,
            'what_we_offer' => $whatWeOffer,
            'why_choose_us' => $whyChooseUs,
            'meta_description' => $metaDescription,
            'price' => $price,
            'duration' => $duration,
            'type' => $type,
            'is_active' => $isActive,
            'service_type' => $serviceType,
            'has_variations' => $hasVariations,
        ];

        // إذا كان هناك ID، قم بالتحديث، وإلا أنشئ خدمة جديدة
        if (isset($row['id']) && !empty($row['id'])) {
            $data['id'] = (int)$row['id'];
        }

        return new Service($data);
    }

    /**
     * The unique column name for upsert
     */
    public function uniqueBy()
    {
        return 'id';
    }

    /**
     * Rules for validation
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'meta_description' => 'nullable|string|max:160',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'اسم الخدمة مطلوب',
            'category_id.required' => 'الفئة مطلوبة',
            'category_id.exists' => 'الفئة غير موجودة',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'meta_description.max' => 'وصف SEO يجب ألا يتجاوز 160 حرفاً',
        ];
    }

    /**
     * The row number we're starting from
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Ensure CSVs are parsed as UTF-8 to support Arabic content
     */
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
        ];
    }
}
