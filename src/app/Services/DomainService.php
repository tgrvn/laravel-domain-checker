<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\User;
use App\Repositories\DomainRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class DomainService
{
    public function __construct(
        private DomainRepository $domainRepository
    ) {}

    public function getAllForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->domainRepository->getAllForUser($user, $perPage);
    }

    public function create(User $user, array $domainData, array $checkSettingData = []): Domain
    {
        $domain = $this->domainRepository->create($user, $domainData);
        $domain->checkSetting()->create($checkSettingData);

        return $domain;
    }

    public function update(Domain $domain, array $domainData, array $checkSettingData = []): bool
    {
        if (!empty($checkSettingData)) {
            $domain->checkSetting()
                ->updateOrCreate(['domain_id' => $domain->id], $checkSettingData);
        }

        return $this->domainRepository->update($domain, $domainData);
    }

    public function delete(Domain $domain): bool
    {
        return $this->domainRepository->delete($domain);
    }
}