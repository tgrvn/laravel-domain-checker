<?php

namespace App\Jobs;

use App\Models\Domain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchDomainChecksJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Domain::query()
            ->whereHas('checkSetting', fn ($q) => $q->where('auto_checks_enabled', true))
            ->with('checkSetting')
            ->chunkById(100, function ($domains) {
                foreach ($domains as $domain) {
                    $interval = $domain->checkSetting->check_interval_minutes;

                    $isDue = $domain->last_checked_at === null
                        || $domain->last_checked_at->addMinutes($interval)->isPast();

                    if ($isDue) {
                        CheckDomainJob::dispatch($domain);
                    }
                }
            });
    }
}
