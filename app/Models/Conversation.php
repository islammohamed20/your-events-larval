<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'assigned_to',
        'status',
        'last_message',
        'last_message_at',
        'unread_count',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'unread_count' => 'integer',
        ];
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class)->orderBy('id');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class)->latest('id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForInboxFilter($query, ?string $filter, ?int $userId)
    {
        return match ($filter) {
            'my' => $query->where('assigned_to', $userId),
            'unassigned' => $query->whereNull('assigned_to'),
            default => $query,
        };
    }
}