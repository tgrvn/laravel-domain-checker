<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\DomainCheck;
use App\Notifications\DomainDownNotification;
use App\Notifications\DomainUpNotification;
use App\Repositories\DomainCheckRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

readonly class DomainCheckService
{
    public function __construct(
        private DomainCheckRepository $domainCheckRepository,
    ) {}

    public function getChecksForDomain(Domain $domain, int $perPage = 20): LengthAwarePaginator
    {
        return $this->domainCheckRepository->getForDomain($domain, $perPage);
    }

    public function performCheck(Domain $domain): DomainCheck
    {
        $setting = $domain->checkSetting;
        $timeout = $setting?->request_timeout_seconds ?? 10;
        $method = strtolower($setting?->check_method ?? 'GET');

        $url = $this->normalizeUrl($domain->domain);

        $startTime = microtime(true);
        $isSuccess = false;
        $statusCode = null;
        $errorMessage = null;

        try {
            $response = Http::timeout($timeout)->$method($url);
            $statusCode = $response->status();
            $isSuccess = $response->successful();
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
        }

        $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        $check = $this->domainCheckRepository->create($domain, [
            'is_success' => $isSuccess,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'error_message' => $errorMessage,
            'checked_at' => now(),
        ]);

        $previousStatus = $domain->last_check_success;

        $domain->update([
            'last_check_success' => $isSuccess,
            'last_checked_at' => now(),
        ]);

        $this->detectStatusChange($domain, $previousStatus, $isSuccess, $errorMessage);

        return $check;
    }

    private function normalizeUrl(string $domain): string
    {
        if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
            return 'https://' . $domain;
        }

        return $domain;
    }

    private function detectStatusChange(Domain $domain, ?bool $previousStatus, bool $currentStatus, ?string $errorMessage): void
    {
        if ($previousStatus === null) {
            return;
        }

        if ($previousStatus === true && $currentStatus === false) {
            $domain->user->notify(new DomainDownNotification($domain, $errorMessage));
        }

        if ($previousStatus === false && $currentStatus === true) {
            $domain->user->notify(new DomainUpNotification($domain));
        }
    }
}
