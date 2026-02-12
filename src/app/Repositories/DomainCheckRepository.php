<?php

namespace App\Repositories;

use App\Models\Domain;
use App\Models\DomainCheck;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DomainCheckRepository
{
    public function getForDomain(Domain $domain, int $perPage = 20): LengthAwarePaginator
    {
        return DomainCheck::where('domain_id', $domain->id)
            ->orderByDesc('checked_at')
            ->paginate($perPage);
    }

    public function create(Domain $domain, array $data): DomainCheck
    {
        return $domain->checks()->create($data);
    }
}
