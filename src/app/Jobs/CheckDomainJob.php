<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\DomainCheckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckDomainJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 60;

    public function __construct(
        private readonly Domain $domain,
    ) {}

    public function handle(DomainCheckService $domainCheckService): void
    {
        $domainCheckService->performCheck($this->domain);
    }
}
