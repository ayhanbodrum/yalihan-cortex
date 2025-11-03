@extends('admin.layouts.neo')

@section('title', 'CRM Dashboard')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">CRM Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Müşteri yönetimi ve analitikleri</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-users text-2xl opacity-80"></i>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ number_format($stats['total_customers']) }}</div>
                        <div class="text-blue-100 text-sm">Toplam Müşteri</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-user-check text-2xl opacity-80"></i>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ number_format($stats['active_customers']) }}</div>
                        <div class="text-green-100 text-sm">Aktif</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-clock text-2xl opacity-80"></i>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ number_format($stats['pending_followups']) }}</div>
                        <div class="text-yellow-100 text-sm">Bekleyen</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-2xl opacity-80"></i>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ number_format($stats['today_activities']) }}</div>
                        <div class="text-purple-100 text-sm">Bugünkü</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Recent Activities -->
            <div class="lg:col-span-8">
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200 h-full">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Son Aktiviteler</h3>
                        <a href="{{ route('admin.crm.customers.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 hover:scale-105 transition-all duration-200 text-sm px-4 py-2 inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 hover:scale-105 transition-all duration-200-primary touch-target-optimized touch-target-optimized">
                            Tümünü Gör
                        </a>
                    </div>
                    <div class="px-6 py-4 p-0 max-h-96 overflow-y-auto">
                        @forelse($recentActivities as $activity)
                            <div class="flex items-center p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    {{ strtoupper(substr($activity['kisi']['ad'], 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $activity['kisi']['ad'] }} {{ $activity['kisi']['soyad'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ Str::limit($activity['aciklama'], 60) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($activity['aktivite_tarihi'])->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs font-medium rounded-full">{{ $activity['aktivite_tipi'] }}</span>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <i class="fas fa-calendar-times text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg">Henüz aktivite bulunmuyor</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">İlk aktiviteyi eklemek için müşteri profili ziyaret edin</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hızlı İşlemler</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <a href="{{ route('admin.crm.customers.create') }}" class="w-full inline-flex items-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                            <i class="fas fa-plus mr-2"></i>Yeni Müşteri
                        </a>
                        <a href="{{ route('admin.crm.customers.index') }}" class="w-full inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 hover:scale-105 hover:shadow-lg active:scale-95 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all duration-200 touch-target-optimized touch-target-optimized">
                            <i class="fas fa-search mr-2"></i>Müşteri Ara
                        </a>
                        <button class="w-full inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all duration-200 touch-target-optimized touch-target-optimized" onclick="exportData()">
                            <i class="fas fa-download mr-2"></i>Dışa Aktar
                        </button>
                    </div>
                </div>

                <!-- Customer Distribution -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Müşteri Dağılımı</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        @foreach($customerSegments as $type => $count)
                            @php
                                $percentage = $stats['total_customers'] > 0 ? ($count / $stats['total_customers']) * 100 : 0;
                                $colors = [
                                    'alici' => 'bg-blue-500',
                                    'satici' => 'bg-green-500',
                                    'kiraci' => 'bg-yellow-500',
                                    'kiralayan' => 'bg-purple-500',
                                    'yatirimci' => 'bg-red-500',
                                    'ev_sahibi' => 'bg-indigo-500',
                                    'gorevli' => 'bg-gray-500'
                                ];
                                $color = $colors[$type] ?? 'bg-gray-500';
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full {{ $color }} mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white mr-2">{{ $count }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">%{{ number_format($percentage, 1) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Additions -->
                <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Son Eklenenler</h3>
                    </div>
                    <div class="px-6 py-4">
                        @php
                            $recentCustomers = App\Models\Kisi::latest()->take(3)->get();
                        @endphp
                        @forelse($recentCustomers as $customer)
                            <div class="flex items-center mb-3 last:mb-0">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                    {{ strtoupper(substr($customer->ad, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-white text-sm">
                                        {{ $customer->ad }} {{ $customer->soyad }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $customer->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs font-medium rounded-full text-xs px-2 py-1">{{ $customer->status }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">
                                Henüz müşteri eklenmemiş
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function exportData() {
                alert('Dışa aktarma özelliği yakında eklenecek!');
            }
        </script>
    @endpush
@endsection
