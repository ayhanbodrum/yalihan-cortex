@extends('admin.layouts.neo')

@section('content')
    <div class="neo-container">
        <div class="neo-page-header">
            <h1 class="neo-page-title">⚙️ Sistem Ayarları</h1>
            <a href="{{ route('admin.ayarlar.create') }}" class="neo-btn neo-btn neo-btn-primary touch-target-optimized touch-target-optimized">
                Yeni Ayar Ekle
            </a>
        </div>

        @if (session('success'))
            <div class="neo-alert neo-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @foreach ($settings as $group => $groupSettings)
            <div class="neo-card mb-6">
                <div class="neo-card-header">
                    <h2 class="neo-card-title">{{ ucfirst($group) }}</h2>
                </div>
                <div class="neo-card-body">
                    <div class="neo-table-responsive">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>Anahtar</th>
                                    <th>Değer</th>
                                    <th>Tip</th>
                                    <th>Açıklama</th>
                                    <th class="text-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupSettings as $setting)
                                    <tr>
                                        <td><code class="neo-code">{{ $setting->key }}</code></td>
                                        <td>
                                            @if (strlen($setting->value ?? '') > 50)
                                                {{ substr($setting->value, 0, 50) }}...
                                            @else
                                                {{ $setting->value ?? '-' }}
                                            @endif
                                        </td>
                                        <td><span class="neo-badge">{{ $setting->type }}</span></td>
                                        <td>{{ $setting->description ?? '-' }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.ayarlar.edit', $setting->id) }}"
                                                class="neo-btn neo-btn-sm neo-btn neo-btn-secondary touch-target-optimized touch-target-optimized">
                                                Düzenle
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500">
                                            Bu grupta ayar bulunamadı
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
