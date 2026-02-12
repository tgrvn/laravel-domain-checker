<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainCheckSetting extends Model
{
    protected $fillable = [
        'domain_id',
        'check_interval_minutes',
        'request_timeout_seconds',
        'check_method',
        'auto_checks_enabled',
    ];

    protected function casts(): array
    {
        return [
            'auto_checks_enabled' => 'boolean',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
