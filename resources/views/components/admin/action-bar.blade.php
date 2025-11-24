<div class="mb-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex-1">
            @if(isset($title))
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $title }}</h1>
            @endif
            @if(isset($subtitle))
                <p class="text-gray-600 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-3">
            {{ $slot }}
        </div>
    </div>
</div>