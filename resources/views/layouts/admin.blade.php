<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Yönetim Paneli')</title>
    {{-- Removed: neo-unified.css (file doesn't exist, using Vite build instead) --}}
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="neo-container neo-mx-auto neo-py-3 neo-flex neo-items-center neo-justify-between">
            <a href="/admin" class="neo-text-xl neo-font-bold neo-text-blue-700">Yönetim Paneli</a>
            <nav class="neo-flex neo-gap-6">
                <a href="/admin/dashboard"
                    class="neo-text-gray-700 hover:neo-text-blue-600 neo-font-medium">Dashboard</a>
                <a href="/admin/tkgm-parsel" class="neo-text-gray-700 hover:neo-text-blue-600 neo-font-medium">TKGM
                    Parsel</a>
                <a href="/admin/ai-monitor" class="neo-text-gray-700 hover:neo-text-blue-600 neo-font-medium">AI
                    Monitoring</a>
                <a href="/admin/ai-settings" class="neo-text-gray-700 hover:neo-text-blue-600 neo-font-medium">AI
                    Ayarları</a>
                <a href="/admin" class="neo-text-gray-700 hover:neo-text-blue-600 neo-font-medium">Ana Panel</a>
            </nav>
        </div>
    </header>
    <main class="flex-1 neo-container neo-mx-auto neo-py-8">
        @yield('content')
    </main>
    <footer class="bg-white border-t border-gray-200 neo-py-6 neo-mt-8">
        <div class="neo-container neo-mx-auto neo-text-center neo-text-gray-500 neo-text-sm">
            © {{ date('Y') }} Yalıhan Emlak Yönetim Paneli
        </div>
    </footer>
    @stack('scripts')
</body>

</html>
