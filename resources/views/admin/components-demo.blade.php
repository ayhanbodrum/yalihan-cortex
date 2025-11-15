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

                <x-admin.alert type="info" title="Info">
                    This is an informational message with title.
                </x-admin.alert>

                <x-admin.alert type="warning">
                    Please review the property details.
                </x-admin.alert>

                <x-admin.alert type="error" :dismissible="true">
                    An error occurred while saving. (Dismissible)
                </x-admin.alert>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.alert type="success" :dismissible="true"&gt;...&lt;/x-admin.alert&gt;
                </p>
            </div>
        </div>

        {{-- Badge Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Badge Component
            </h2>

            <div class="flex flex-wrap gap-3">
                <x-admin.badge color="indigo">Default</x-admin.badge>
                <x-admin.badge color="green">Active</x-admin.badge>
                <x-admin.badge color="red">Sold</x-admin.badge>
                <x-admin.badge color="yellow">Pending</x-admin.badge>
                <x-admin.badge color="gray">Draft</x-admin.badge>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.badge color="green"&gt;Active&lt;/x-admin.badge&gt;
                </p>
            </div>
        </div>

        {{-- Dropdown Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Dropdown Component
            </h2>

            <div class="flex gap-4">
                {{-- Basic Dropdown --}}
                <x-admin.dropdown align="right" width="w-48">
                    <x-slot:trigger>
                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium">
                            Actions ▼
                        </button>
                    </x-slot:trigger>

                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Edit
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Duplicate
                    </a>
                    <hr class="my-1 border-gray-200 dark:border-gray-700">
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Delete
                    </a>
                </x-admin.dropdown>

                {{-- Icon Dropdown --}}
                <x-admin.dropdown align="left" width="w-56">
                    <x-slot:trigger>
                        <button class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </x-slot:trigger>

                    <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        Property Options
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        View Details
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Share
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Export
                    </a>
                </x-admin.dropdown>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.dropdown align="right"&gt;...&lt;/x-admin.dropdown&gt;
                </p>
            </div>
        </div>

        {{-- Tabs Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Tabs Component
            </h2>

            <div x-data="{ activeTab: 1 }" class="space-y-4">
                {{-- Tab Navigation --}}
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex gap-4" aria-label="Tabs">
                        <button
                            @click="activeTab = 1"
                            :class="activeTab === 1 ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200"
                        >
                            General Info
                        </button>
                        <button
                            @click="activeTab = 2"
                            :class="activeTab === 2 ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200"
                        >
                            Features
                        </button>
                        <button
                            @click="activeTab = 3"
                            :class="activeTab === 3 ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="px-4 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200"
                        >
                            Pricing
                        </button>
                    </nav>
                </div>

                {{-- Tab Content --}}
                <div class="mt-4">
                    <div x-show="activeTab === 1" x-transition class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">General Information</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Property details, location, and basic information goes here.</p>
                    </div>
                    <div x-show="activeTab === 2" x-transition class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Features & Amenities</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Property features, amenities, and specifications.</p>
                    </div>
                    <div x-show="activeTab === 3" x-transition class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Pricing Details</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Price, payment terms, and financial information.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.tabs&gt;...&lt;/x-admin.tabs&gt;
                </p>
            </div>
        </div>

        {{-- Accordion Demo --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Accordion Component
            </h2>

            <div x-data="{ openItem: 1 }" class="space-y-2">
                {{-- Accordion Item 1 --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <button
                        @click="openItem = openItem === 1 ? null : 1"
                        class="w-full px-4 py-3 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">What is included in the property?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': openItem === 1 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div
                        x-show="openItem === 1"
                        x-transition
                        class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            The property includes 3 bedrooms, 2 bathrooms, a modern kitchen, and a spacious living room. Additional amenities include parking and a balcony.
                        </p>
                    </div>
                </div>

                {{-- Accordion Item 2 --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <button
                        @click="openItem = openItem === 2 ? null : 2"
                        class="w-full px-4 py-3 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">What are the payment options?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': openItem === 2 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div
                        x-show="openItem === 2"
                        x-transition
                        class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            We accept cash, bank transfer, and mortgage financing. Flexible payment plans are available for qualified buyers.
                        </p>
                    </div>
                </div>

                {{-- Accordion Item 3 --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <button
                        @click="openItem = openItem === 3 ? null : 3"
                        class="w-full px-4 py-3 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">Is the property ready to move in?</span>
                            <svg
                                class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': openItem === 3 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <div
                        x-show="openItem === 3"
                        x-transition
                        class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Yes, the property is in excellent condition and ready for immediate occupancy. All utilities are connected and functioning.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                    &lt;x-admin.accordion&gt;...&lt;/x-admin.accordion&gt;
                </p>
            </div>
        </div>

    </div>

    {{-- Component List --}}
    <div class="mt-8 bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            ✅ Available Components (12)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Modal</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.modal</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Checkbox</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-checkbox</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Radio</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-radio</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Toggle</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.toggle</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">File Upload</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.file-upload</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Alert</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.alert</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Badge</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.badge</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Dropdown</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.dropdown</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Tabs</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.tabs</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Accordion</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.accordion</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Input</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.input</span>
                </div>
            </div>
            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <span class="text-green-600 dark:text-green-400 text-xl">✓</span>
                <div>
                    <span class="text-gray-900 dark:text-white font-medium block">Select</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">x-admin.select</span>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">12</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Components</div>
            </div>
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">100%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Tailwind CSS</div>
            </div>
            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800 text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">100%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Dark Mode</div>
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
