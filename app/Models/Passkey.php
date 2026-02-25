<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passkey extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'credential_id',
        'public_key',
        'user_handle',
        'device_name',
        'sign_count',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'sign_count'   => 'integer',
    ];

    public function scopeForUser($query, int $userId, string $type)
    {
        return $query->where('user_id', $userId)->where('user_type', $type);
    }
}
