@extends('layout')

@section('title', 'Создать домен')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Создать домен</h1>

        <div class="bg-white p-8 rounded-lg shadow-md">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('domains.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="domain" class="block text-gray-700 font-bold mb-2">Домен</label>
                    <input type="text"
                           name="domain"
                           id="domain"
                           value="{{ old('domain') }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                           placeholder="example.com">
                </div>

                <hr class="my-6 border-gray-200">
                <h2 class="text-lg font-bold text-gray-700 mb-4">Настройки мониторинга</h2>

                <div class="mb-4">
                    <label for="check_interval_minutes" class="block text-gray-700 font-bold mb-2">Интервал проверки (мин.)</label>
                    <input type="number" name="check_interval_minutes" id="check_interval_minutes"
                           value="{{ old('check_interval_minutes', 5) }}" min="1" max="1440"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="request_timeout_seconds" class="block text-gray-700 font-bold mb-2">Таймаут запроса (сек.)</label>
                    <input type="number" name="request_timeout_seconds" id="request_timeout_seconds"
                           value="{{ old('request_timeout_seconds', 10) }}" min="1" max="30"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="check_method" class="block text-gray-700 font-bold mb-2">HTTP метод</label>
                    <select name="check_method" id="check_method"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                        <option value="GET" {{ old('check_method') === 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="HEAD" {{ old('check_method') === 'HEAD' ? 'selected' : '' }}>HEAD</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="hidden" name="auto_checks_enabled" value="0">
                        <input type="checkbox" name="auto_checks_enabled" value="1"
                               {{ old('auto_checks_enabled', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 mr-2">
                        <span class="text-gray-700 font-bold">Автоматические проверки</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('domains.index') }}" class="text-gray-600 hover:text-gray-800">
                        Отмена
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                        Создать
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection