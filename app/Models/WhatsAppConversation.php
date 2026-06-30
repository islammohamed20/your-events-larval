<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'assigned_supplier_id',
        'customer_phone',
        'customer_name',
        'whatsapp_conversation_id',
        'status',
        'last_message_at',
        'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignedSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'assigned_supplier_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function markAsRead(): void
    {
        $this->update(['unread_count' => 0]);
        $this->messages()->where('direction', 'incoming')->update(['status' => 'read', 'read_at' => now()]);
    }

    /**
     * تعيين مورد للمحادثة
     */
    public function assignToSupplier(?Supplier $supplier): void
    {
        $this->update(['assigned_supplier_id' => $supplier ? $supplier->id : null]);
    }

    /**
     * التحقق مما إذا كانت المحادثة معينة لمورد معين
     */
    public function isAssignedTo(Supplier $supplier): bool
    {
        return $this->assigned_supplier_id === $supplier->id;
    }

    /**
     * التحقق مما إذا كانت المحادثة غير معينة
     */
    public function isUnassigned(): bool
    {
        return is_null($this->assigned_supplier_id);
    }
}
