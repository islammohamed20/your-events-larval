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
        'faalwa_user_ns',
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

    public function scopeForInboxFilter($query, ?string $filter, ?int $userId, bool $canViewAllAssigned = false, ?string $status = null)
    {
        // All closed conversations are visible to everyone (even non-admins)
        // when the "all" filter is active. This lets agents review completed chats.
        if ($status === 'closed' && ($filter === 'all' || $filter === '' || $filter === null)) {
            return $query;
        }

        return match ($filter) {
            'my' => $query->where('assigned_to', $userId),
            'unassigned' => $query->whereNull('assigned_to'),
            default => $canViewAllAssigned ? $query : $query->where(function ($inner) use ($userId) {
                $inner->whereNull('assigned_to');

                if ($userId) {
                    $inner->orWhere('assigned_to', $userId);
                }
            }),
        };
    }
}