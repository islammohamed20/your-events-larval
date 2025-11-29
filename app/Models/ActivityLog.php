<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'actor_type',
        'actor_id',
        'action',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function actor()
    {
        return $this->morphTo();
    }

    /**
     * سجل نشاط بشكل مبسط
     */
    public static function record($subject, string $action, ?string $description = null, array $properties = [], $actor = null)
    {
        $actor = $actor ?? Auth::user();

        return self::create([
            'subject_type' => get_class($subject),
            'subject_id' => $subject->getKey(),
            'actor_type' => $actor ? get_class($actor) : null,
            'actor_id' => $actor ? $actor->getKey() : null,
            'action' => $action,
            'description' => $description,
            'properties' => $properties ?: null,
        ]);
    }
}

