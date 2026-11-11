<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'completed',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed' => 'boolean',
        ];
    }
}
