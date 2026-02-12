<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <a href="/" class="text-xl font-bold text-gray-800">Laravel</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('domains.index') }}" class="text-gray-700 hover:text-gray-900">Домены</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Вход</a>
                    <a href="{{ route('register') }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Регистрация
                    </a>
                @endauth
            </div>

            @auth
                <div class="flex items-center gap-5">
                    <div class="relative" id="notification-bell">
                        <button onclick="toggleNotifications()"
                                class="relative text-gray-700 hover:text-gray-900 p-1 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notification-badge"
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 items-center justify-center font-bold {{ ($unreadCount = auth()->user()->unreadNotifications()->count()) > 0 ? 'flex' : 'hidden' }}">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        </button>

                        <div id="notification-dropdown"
                             class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Уведомления</h3>
                                <button onclick="markAllRead()" class="text-xs text-blue-500 hover:text-blue-700">
                                    Прочитать
                                    все
                                </button>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto">
                                <div class="px-4 py-8 text-center text-gray-400 text-sm">Загрузка...</div>
                            </div>
                        </div>
                    </div>


                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            Выход
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>

<main class="py-10">
    @yield('content')
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('time[data-local-time]').forEach(function (el) {
            const utc = new Date(el.getAttribute('datetime'));
            el.textContent = utc.toLocaleString('ru-RU', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        });
    });
</script>

@auth
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            if (isHidden) loadNotifications();
        }

        document.addEventListener('click', function (e) {
            const bell = document.getElementById('notification-bell');
            if (bell && !bell.contains(e.target)) {
                document.getElementById('notification-dropdown').classList.add('hidden');
            }
        });

        async function loadNotifications() {
            const res = await fetch('{{ route("notifications.index") }}', {
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken}
            });
            const data = await res.json();
            renderNotifications(data.notifications);
            updateBadge(data.unread_count);
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notification-list');
            if (!notifications.length) {
                list.innerHTML = '<div class="px-4 py-8 text-center text-gray-400 text-sm">Уведомлений пока нет</div>';
                return;
            }
            list.innerHTML = notifications.map(n => {
                const isDown = n.type.includes('DomainDown');
                const isUnread = !n.read_at;
                const icon = isDown
                    ? `<span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 flex-shrink-0"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></span>`
                    : `<span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-100 flex-shrink-0"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span>`;
                const time = new Date(n.created_at).toLocaleString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                const errorLine = n.data.error_message ? `<p class="text-xs text-red-500 mt-0.5 truncate">${n.data.error_message}</p>` : '';
                const readBtn = isUnread ? `<button onclick="markRead('${n.id}')" class="text-xs text-blue-500 hover:text-blue-700 whitespace-nowrap ml-2 flex-shrink-0">Прочитано</button>` : '';
                return `<div class="flex items-start px-4 py-3 hover:bg-gray-50 ${isUnread ? 'bg-blue-50 border-l-4 border-blue-500' : 'border-l-4 border-transparent'}">
                ${icon}
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm text-gray-900">${n.data.message}</p>
                    ${errorLine}
                    <p class="text-xs text-gray-400 mt-0.5">${time}</p>
                </div>
                ${readBtn}
            </div>`;
            }).join('');
        }

        function updateBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        async function markRead(id) {
            await fetch(`/notifications/${id}/read`, {
                method: 'PATCH',
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken}
            });
            loadNotifications();
        }

        async function markAllRead() {
            await fetch('{{ route("notifications.read-all") }}', {
                method: 'POST',
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken}
            });
            loadNotifications();
        }
    </script>
@endauth
</body>
</html>
