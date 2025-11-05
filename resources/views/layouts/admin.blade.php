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
        <div class="container mx-auto neo-mx-auto neo-py-3 flex items-center justify-between">
            <a href="/admin" class="text-xl neo-font-bold text-blue-700">Yönetim Paneli</a>
            <nav class="flex gap-6">
                <a href="/admin/dashboard"
                    class="text-gray-700 hover:text-blue-600 neo-font-medium">Dashboard</a>
                <a href="/admin/tkgm-parsel" class="text-gray-700 hover:text-blue-600 neo-font-medium">TKGM
                    Parsel</a>
                <a href="/admin/ai-monitor" class="text-gray-700 hover:text-blue-600 neo-font-medium">AI
                    Monitoring</a>
                <a href="/admin/ai-settings" class="text-gray-700 hover:text-blue-600 neo-font-medium">AI
                    Ayarları</a>
                <a href="/admin" class="text-gray-700 hover:text-blue-600 neo-font-medium">Ana Panel</a>
            </nav>
        </div>
    </header>
    <main class="flex-1 container mx-auto neo-mx-auto neo-py-8">
        @yield('content')
    </main>
    <footer class="bg-white border-t border-gray-200 neo-py-6 neo-mt-8">
        <div class="container mx-auto neo-mx-auto text-center text-gray-500 text-sm">
            © {{ date('Y') }} Yalıhan Emlak Yönetim Paneli
        </div>
    </footer>
    @stack('scripts')
</body>

</html>
