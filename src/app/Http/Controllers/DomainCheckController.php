<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\DomainCheckService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class DomainCheckController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DomainCheckService $domainCheckService,
    ) {}

    public function index(Domain $domain)
    {
        $this->authorize('update', $domain);

        $checks = $this->domainCheckService->getChecksForDomain($domain);

        return view('domains.checks', compact('domain', 'checks'));
    }

    public function store(Domain $domain)
    {
        $this->authorize('update', $domain);

        $this->domainCheckService->performCheck($domain);

        return redirect()->route('domains.checks', $domain)
            ->with('success', 'Проверка выполнена');
    }
}
