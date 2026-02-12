@extends('layout')

@section('title', 'Мои домены')

@section('content')
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Мои домены</h1>
            <a href="{{ route('domains.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Добавить домен
            </a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Домен
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Статус
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Последняя проверка
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Создан
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Действия
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($domains as $domain)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $domain->domain }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($domain->last_check_success === null)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                    Не проверялся
                                </span>
                            @elseif($domain->last_check_success)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Доступен
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Недоступен
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($domain->last_checked_at)
                                <time datetime="{{ $domain->last_checked_at->toIso8601String() }}" data-local-time>
                                    {{ $domain->last_checked_at->format('d.m.Y H:i') }}
                                </time>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <time datetime="{{ $domain->created_at->toIso8601String() }}" data-local-time>
                                {{ $domain->created_at->format('d.m.Y H:i') }}
                            </time>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <form action="{{ route('domains.checks.store', $domain) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">
                                    Проверить
                                </button>
                            </form>
                            <a href="{{ route('domains.checks', $domain) }}" class="text-blue-600 hover:text-blue-900">
                                Логи
                            </a>
                            <a href="{{ route('domains.edit', $domain) }}" class="text-indigo-600 hover:text-indigo-900">
                                Изменить
                            </a>
                            <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Вы уверены?')">
                                    Удалить
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            У вас пока нет доменов.
                            <a href="{{ route('domains.create') }}" class="text-blue-500">Создать первый</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $domains->links() }}
        </div>
    </div>
@endsection
