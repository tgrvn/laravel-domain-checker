@extends('layout')

@section('title', 'Логи проверок — ' . $domain->domain)

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('domains.index') }}" class="text-blue-500 hover:text-blue-700 text-sm">&larr; Назад к доменам</a>
                <h1 class="text-3xl font-bold mt-1">Логи проверок: {{ $domain->domain }}</h1>
            </div>
            <form action="{{ route('domains.checks.store', $domain) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Проверить сейчас
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HTTP код</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Время ответа</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ошибка</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата проверки</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($checks as $check)
                    <tr class="{{ $check->is_success ? '' : 'bg-red-50' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($check->is_success)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">OK</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ошибка</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $check->status_code ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $check->response_time_ms !== null ? $check->response_time_ms . ' мс' : '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-red-600 max-w-xs truncate">
                            {{ $check->error_message ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <time datetime="{{ $check->checked_at->toIso8601String() }}" data-local-time>
                                {{ $check->checked_at->format('d.m.Y H:i:s') }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Проверок ещё не было. Нажмите «Проверить сейчас» для первой проверки.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $checks->links() }}
        </div>
    </div>
@endsection
