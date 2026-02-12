<?php

namespace App\Repositories;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DomainRepository
{
    public function getAllForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Domain::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(User $user, array $data): Domain
    {
        return $user->domains()->create($data);
    }

    public function update(Domain $domain, array $data): bool
    {
        return $domain->update($data);
    }

    public function delete(Domain $domain): bool
    {
        return $domain->delete();
    }

    public function findForUser(int $id, User $user): ?Domain
    {
        return Domain::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
    }
}