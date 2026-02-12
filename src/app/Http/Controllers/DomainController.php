<?php

namespace App\Http\Controllers;

use App\Http\Requests\Domain\DomainIndexRequest;
use App\Http\Requests\Domain\StoreDomainRequest;
use App\Http\Requests\Domain\UpdateDomainRequest;
use App\Models\Domain;
use App\Services\DomainService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class DomainController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DomainService $domainService
    ) {}

    public function index(DomainIndexRequest $request)
    {
        $domains = $this->domainService->getAllForUser(
            $request->user(),
            $request->perPage()
        );

        return view('domains.index', compact('domains'));
    }

    public function create()
    {
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request)
    {
        $this->domainService->create(
            $request->user(),
            $request->domainData(),
            $request->checkSettingData()
        );

        return redirect()->route('domains.index')->with('success', 'Домен успешно создан');
    }

    public function edit(Domain $domain)
    {
        $this->authorize('update', $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain)
    {
        $this->domainService->update(
            $domain,
            $request->domainData(),
            $request->checkSettingData()
        );

        return redirect()->route('domains.index')->with('success', 'Домен успешно обновлен');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);

        $this->domainService->delete($domain);

        return redirect()->route('domains.index')->with('success', 'Домен успешно удален');
    }
}