<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Component Library Demo - Yalıhan Emlak</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Custom Styles --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen">

{{-- Header --}}
<header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🧩 Component Library</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">Yalıhan Emlak Warp - Modern Tailwind Components</p>
            </div>
            <div class="flex items-center gap-4">
                <button 
                    @click="darkMode = !darkMode"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 
                           bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200
                           flex items-center gap-2"
                >
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                    <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
                <a href="/admin" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    ← Back to Admin
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    {{-- Welcome Banner --}}
    <div class="mb-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
        <h2 class="text-3xl font-bold mb-2">🚀 Welcome to Component Library</h2>
        <p class="text-blue-100 mb-4">Modern, accessible, and reusable Blade components built with Tailwind CSS & Alpine.js</p>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-2xl">✅</span>
                <span>12+ Components</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎨</span>
                <span>100% Tailwind</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">🌙</span>
                <span>Dark Mode</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">♿</span>
                <span>Accessible</span>
            </div>
        </div>
    </div>

    {{-- Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Modal Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Modal Component
            </h2>

            <div x-data="{ showModal: false, showScrollableModal: false }" class="space-y-3">
                {{-- Basic Modal Button --}}
                <button 
                    @click="showModal = true"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg 
                           transition-colors duration-200 font-medium"
                >
                    Açık Basic Modal
                </button>

                {{-- Scrollable Modal Button --}}
                <button 
                    @click="showScrollableModal = true"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg 
                           transition-colors duration-200 font-medium ml-2"
                >
                    Açık Scrollable Modal
                </button>

                {{-- Basic Modal --}}
                <x-admin.modal 
                    title="Edit User" 
                    size="lg" 
                    bind="showModal"
                >
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Name</label>
                            <input type="text" value="John Doe" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Email</label>
                            <input type="email" value="john@example.com" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                    </div>
                    
                    <x-slot:footer>
                        <div class="flex items-center justify-end gap-3">
                            <button 
                                @click="showModal = false"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200"
                            >
                                Cancel
                            </button>
                            <button 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
                            >
                                Save Changes
                            </button>
                        </div>
                    </x-slot:footer>
                </x-admin.modal>

                {{-- Scrollable Modal --}}
                <x-admin.modal 
                    title="Long Content Modal" 
                    size="md" 
                    bind="showScrollableModal"
                    :scrollable="true"
                >
                    <div class="space-y-4">
                        @for($i = 1; $i <= 20; $i++)
                        <p class="text-gray-600 dark:text-gray-400">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Paragraph {{ $i }}.
                        </p>
                        @endfor
                    </div>
                    
                    <x-slot:footer>
                        <button 
                            @click="showScrollableModal = false"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200"
                        >
                            Close
                        </button>
                    </x-slot:footer>
                </x-admin.modal>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.modal title="..." size="lg" bind="showModal"&gt;
                </p>
            </div>
        </div>

        {{-- Checkbox Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Checkbox Component
            </h2>

            <div class="space-y-4">
                <x-checkbox
                    name="featured"
                    label="Featured Listing"
                    :checked="true"
                    help="This listing will appear on the homepage"
                />

                <x-checkbox
                    name="verified"
                    label="Verified Property"
                    help="Property has been verified by our team"
                />

                <x-checkbox
                    name="premium"
                    label="Premium Listing"
                    :disabled="true"
                    help="Requires premium subscription"
                />

                <x-checkbox
                    name="error_example"
                    label="With Error"
                    error="This field is required"
                />
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-checkbox name="..." label="..." :checked="true" /&gt;
                </p>
            </div>
        </div>

        {{-- Radio Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Radio Component
            </h2>

            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">Property Status</p>
                    
                    <x-radio
                        name="status"
                        label="Active"
                        value="active"
                        :checked="true"
                        help="Property is live and visible"
                    />

                    <div class="mt-3">
                        <x-radio
                            name="status"
                            label="Pending"
                            value="pending"
                            help="Waiting for approval"
                        />
                    </div>

                    <div class="mt-3">
                        <x-radio
                            name="status"
                            label="Sold"
                            value="sold"
                            help="Property has been sold"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-radio name="status" label="Active" value="active" /&gt;
                </p>
            </div>
        </div>

        {{-- File Upload Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                File Upload Component
            </h2>

            <div class="space-y-6">
                {{-- Single File --}}
                <x-admin.file-upload
                    name="document"
                    label="Upload Document"
                    accept=".pdf,.doc,.docx"
                    help="PDF or DOC format, max 10MB"
                />

                {{-- Multiple Images --}}
                <x-admin.file-upload
                    name="photos[]"
                    label="Property Photos"
                    :multiple="true"
                    accept="image/*"
                    :maxSize="5"
                    :maxFiles="10"
                    help="Upload up to 10 photos (max 5MB each)"
                />
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.file-upload name="photos[]" :multiple="true" /&gt;
                </p>
            </div>
        </div>

        {{-- Toggle Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Toggle Component
            </h2>

            <div class="space-y-4">
                <x-admin.toggle
                    name="notifications"
                    label="Enable Notifications"
                    :checked="true"
                    help="Receive email notifications for new listings"
                />

                <x-admin.toggle
                    name="dark_mode"
                    label="Dark Mode"
                    help="Switch to dark theme"
                />

                <x-admin.toggle
                    name="auto_save"
                    label="Auto Save"
                    :checked="true"
                    help="Automatically save changes"
                />
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.toggle name="..." label="..." :checked="true" /&gt;
                </p>
            </div>
        </div>

        {{-- Alert Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Alert Component
            </h2>

            <div class="space-y-4">
                <x-admin.alert type="success">
                    Property successfully saved!
                </x-admin.alert>

                <x-admin.alert type="info">
                    This is an informational message.
                </x-admin.alert>

                <x-admin.alert type="warning">
                    Please review the property details.
                </x-admin.alert>

                <x-admin.alert type="error">
                    An error occurred while saving.
                </x-admin.alert>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.alert type="success"&gt;Message&lt;/x-admin.alert&gt;
                </p>
            </div>
        </div>

    </div>

    {{-- Component List --}}
    <div class="mt-8 bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            Available Components
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Modal</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Checkbox</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Radio</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Toggle</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">File Upload</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Alert</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Badge</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Input</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Select</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Textarea</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Dropdown</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-500">✓</span>
                <span class="text-gray-700 dark:text-gray-300">Accordion</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="mt-12 text-center pb-8">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-400 mb-2">
                📚 Full Documentation: <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-sm">COMPONENT-LIBRARY-GUIDE.md</code>
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Built with ❤️ by Yalıhan Bekçi AI System • 5 Kasım 2025
            </p>
        </div>
    </footer>
</div>

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
// Simple toast function
window.toast = function(type, message) {
    const container = document.getElementById('toast-container');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const toast = document.createElement('div');
    toast.className = `${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in`;
    toast.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">✕</button>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};
</script>

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>

</body>
</html>

