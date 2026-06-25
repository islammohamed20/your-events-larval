<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'type',
        'faalwa_namespace',
        'language_code',
        'params_schema',
    ];

    protected function casts(): array
    {
        return [
            'params_schema' => 'array',
        ];
    }
}