@extends('admin.layouts.neo')

@section('title', 'Bildirim Detayı')

@section('content')
    <div class="content-header mb-6">
        <div class="container-fluid">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="admin-h1">Bildirim Detayı</h1>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('admin.notifications.index') }}" class="text-gray-500 hover:text-gray-700">
                                    Bildirimler
                                </a>
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </li>
                            <li class="text-gray-700">{{ $notification->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center space-x-3">
                    @if ($notification->isUnread())
                        <button onclick="markAsRead({{ $notification->id }})" class="inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Okundu İşaretle
                        </button>
                    @else
                        <button onclick="markAsUnread({{ $notification->id }})" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            Okunmadı İşaretle
                        </button>
                    @endif
                    <button onclick="deleteNotification({{ $notification->id }})" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 hover:scale-105 active:scale-95 transition-all duration-200 touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ana İçerik -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex items-center space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @if ($notification->type === 'success')
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @elseif($notification->type === 'warning')
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    </div>
                                @elseif($notification->type === 'error')
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Başlık ve Durum -->
                            <div class="flex-1">
                                <h2 class="text-xl font-semibold text-gray-900">{{ $notification->title }}</h2>
                                <div class="flex items-center space-x-2 mt-1">
                                    @if ($notification->isUnread())
                                        <x-neo.status-badge value="Okunmamış" />
                                    @else
                                        <x-neo.status-badge value="Okunmuş" />
                                    @endif
                                    <x-neo.status-badge :value="ucfirst($notification->priority)" category="priority" />
                                    <x-neo.status-badge :value="ucfirst($notification->type)" category="type" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $notification->message }}</p>
                        </div>

                        @if ($notification->data && count($notification->data) > 0)
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Ek Bilgiler</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yan Panel -->
            <div class="space-y-6">
                <!-- Bildirim Bilgileri -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900">Bildirim Bilgileri</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gönderen</dt>
                                <dd class="text-sm text-gray-900">
                                    @if ($notification->sender)
                                        {{ $notification->sender->name }}
                                        <span class="text-gray-500">({{ $notification->sender->email }})</span>
                                    @else
                                        <span class="text-gray-500">Sistem</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gönderilme Tarihi</dt>
                                <dd class="text-sm text-gray-900">{{ $notification->created_at->format('d.m.Y H:i') }}
                                </dd>
                            </div>

                            @if ($notification->read_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Okunma Tarihi</dt>
                                    <dd class="text-sm text-gray-900">{{ $notification->read_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>
                            @endif

                            @if ($notification->sent_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Gönderilme Tarihi</dt>
                                    <dd class="text-sm text-gray-900">{{ $notification->sent_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>
                            @endif

                            @if ($notification->expires_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Son Geçerlilik</dt>
                                    <dd class="text-sm text-gray-900">{{ $notification->expires_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>
                            @endif

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kanal</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($notification->channel) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Durum</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($notification->status) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- İlgili Varlık -->
                @if ($notification->related)
                    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                            <h3 class="text-lg font-medium text-gray-900">İlgili Varlık</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ class_basename($notification->related_type) }}</p>
                                    <p class="text-sm text-gray-500">ID: {{ $notification->related_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Hızlı İşlemler -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-medium text-gray-900">Hızlı İşlemler</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if ($notification->isUnread())
                                <button onclick="markAsRead({{ $notification->id }})" class="w-full inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Okundu İşaretle
                                </button>
                            @else
                                <button onclick="markAsUnread({{ $notification->id }})" class="w-full inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    Okunmadı İşaretle
                                </button>
                            @endif

                            <button onclick="deleteNotification({{ $notification->id }})" class="w-full inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 hover:scale-105 active:scale-95 transition-all duration-200 touch-target-optimized touch-target-optimized">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Bildirimi Sil
                            </button>

                            <a href="{{ route('admin.notifications.index') }}" class="w-full inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function markAsRead(notificationId) {
            fetch(`/admin/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Hata: ' + data.message);
                    }
                });
        }

        function markAsUnread(notificationId) {
            fetch(`/admin/notifications/${notificationId}/mark-unread`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Hata: ' + data.message);
                    }
                });
        }

        function deleteNotification(notificationId) {
            if (confirm('Bu bildirimi silmek istediğinizden emin misiniz?')) {
                fetch(`/admin/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '/admin/notifications';
                        } else {
                            alert('Hata: ' + data.message);
                        }
                    });
            }
        }
    </script>
@endpush
