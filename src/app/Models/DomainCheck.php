<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainCheck extends Model
{
    protected $fillable = [
        'domain_id',
        'is_success',
        'status_code',
        'response_time_ms',
        'error_message',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_success' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
