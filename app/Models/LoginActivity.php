<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'successful',
        'method', // e.g., password, otp
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
