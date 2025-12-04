@extends('admin.layouts.admin')

@php
    $propertyTypesSummary = $yayinTipleri
        ->map(function ($yayinTipi) use ($fieldDependencies) {
            $slug = $yayinTipi->slug ?? $yayinTipi->yayin_tipi;
            $yayinTipiId = $yayinTipi->id;

            // ✅ Field dependencies'den bu yayın tipine ait alan sayısını hesapla
        $count = 0;
        if (is_array($fieldDependencies)) {
            foreach ($fieldDependencies as $fieldSlug => $fieldData) {
                if (isset($fieldData['yayin_tipleri'][$yayinTipiId]) && $fieldData['yayin_tipleri'][$yayinTipiId]) {
                    $count++;
                }
            }
        }

        return [
            'id' => $yayinTipiId,
            'slug' => $slug,
            'name' => $yayinTipi->yayin_tipi,
            'count' => $count,
            ];
        })
        ->values();

    $defaultPropertyType = $propertyTypesSummary->first();
@endphp

@section('title', 'Özellik Yönetimi - ' . $kategori->name)

@section('styles')
    <style>
        /* ✅ Context7: Alpine.js yüklenmeden önce tab içeriklerini gizle */
        [x-cloak] {
            display: none !important;
        }

        /* Tab içerikleri için özel x-cloak kuralı */
        [x-show][x-cloak] {
            display: none !important;
        }

        .tab-button {
            @apply border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600;
        }

        .tab-button.active {
            @apply border-blue-500 text-blue-600 dark:text-blue-400;
        }

        .feature-card {
            @apply transition-all duration-300 hover:shadow-lg;
        }

        .feature-card:hover {
            @apply -translate-y-1;
        }
    </style>
@endsection

@section('content')
    <script>
        // ✅ Context7: Alpine.js data tanımını global fonksiyon olarak hazırla
        window.fieldDependenciesManager = function fieldDependenciesManagerFactory() {
            return {
                propertyTypes: {!! json_encode($propertyTypesSummary) !!},
                activeTab: {!! json_encode($defaultPropertyType['slug'] ?? '') !!},
                selectedPropertyTypeId: {!! json_encode($defaultPropertyType['id'] ?? null) !!},
                selectedPropertyTypeName: {!! json_encode($defaultPropertyType['name'] ?? '') !!},
                showAddFeatureModal: false,
                selectedFeatures: [],

                init() {
                    // ✅ Context7: Alpine.js yüklenmeden önce içeriklerin görünmesini önle
                    if (!this.activeTab && this.propertyTypes.length > 0) {
                        this.activeTab = this.propertyTypes[0].slug;
                        this.selectedPropertyTypeId = this.propertyTypes[0].id;
                        this.selectedPropertyTypeName = this.propertyTypes[0].name;
                    }
                    this.ensureSelection();
                },

                ensureSelection() {
                    if (!this.activeTab && this.propertyTypes.length) {
                        this.setPropertyType(this.propertyTypes[0].slug);
                    }
                },

                setPropertyType(slug) {
                    const match = this.propertyTypes.find((type) => type.slug === slug);
                    if (match) {
                        this.activeTab = match.slug;
                        this.selectedPropertyTypeId = match.id;
                        this.selectedPropertyTypeName = match.name;
                    }
                },

                toggleFeatureSelection(featureId) {
                    const index = this.selectedFeatures.indexOf(featureId);
                    if (index > -1) {
                        this.selectedFeatures.splice(index, 1);
                    } else {
                        this.selectedFeatures.push(featureId);
                    }
                },

                async assignSelectedFeatures() {
                    if (!this.selectedPropertyTypeId || this.selectedFeatures.length === 0) {
                        if (window.showToast) {
                            window.showToast('Önce yayın tipini ve en az bir özelliği seçin ⛔', 'warning');
                        } else {
                            alert('Önce yayın tipini ve en az bir özelliği seçin ⛔');
                        }
                        return;
                    }

                    try {
                        const response = await fetch(
                            `/admin/property-type-manager/property-type/${this.selectedPropertyTypeId}/sync-features`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    feature_ids: this.selectedFeatures,
                                }),
                            });

                        const data = await response.json();
                        if (data.success) {
                            if (window.showToast) {
                                window.showToast('Özellikler başarıyla atandı ✅', 'success');
                            } else {
                                alert('Özellikler başarıyla atandı ✅');
                            }
                            window.location.reload();
                        } else {
                            if (window.showToast) {
                                window.showToast(data.message || 'Özellik atama başarısız ❌', 'error');
                            } else {
                                alert(data.message || 'Özellik atama başarısız ❌');
                            }
                        }
                    } catch (error) {
                        if (window.showToast) {
                            window.showToast('Özellikler atanırken hata oluştu ❌', 'error');
                        } else {
                            alert('Özellikler atanırken hata oluştu ❌');
                        }
                    }
                },

                async toggleAssignment(assignmentId, field, value) {
                    try {
                        const response = await fetch('/admin/property-type-manager/toggle-feature-assignment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                assignment_id: assignmentId,
                                field,
                                value,
                            }),
                        });

                        const data = await response.json();
                        if (data.success) {
                            if (window.showToast) {
                                window.showToast('Özellik güncellendi ✅', 'success');
                            } else {
                                alert('Özellik güncellendi ✅');
                            }
                        } else {
                            if (window.showToast) {
                                window.showToast(data.message || 'Güncelleme başarısız ❌', 'error');
                            } else {
                                alert(data.message || 'Güncelleme başarısız ❌');
                            }
                        }
                    } catch (error) {
                        if (window.showToast) {
                            window.showToast('Özellik güncellenirken hata oluştu ❌', 'error');
                        } else {
                            alert('Özellik güncellenirken hata oluştu ❌');
                        }
                    }
                },

                async unassignFeature(propertyTypeId, featureId) {
                    let confirmed = false;
                    if (window.showConfirm) {
                        try {
                            confirmed = await window.showConfirm(
                                'Bu özelliği kaldırmak istediğinizden emin misiniz?',
                                'Özellik Kaldır',
                                'warning'
                            );
                        } catch (e) {
                            confirmed = false;
                        }
                    } else {
                        confirmed = window.confirm('Bu özelliği kaldırmak istediğinizden emin misiniz?');
                    }

                    if (!confirmed) {
                        return;
                    }

                    try {
                        const response = await fetch(
                            `/admin/property-type-manager/property-type/${propertyTypeId}/unassign-feature`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    feature_id: featureId,
                                }),
                            });

                        const data = await response.json();
                        if (data.success) {
                            if (window.showToast) {
                                window.showToast('Özellik başarıyla kaldırıldı ✅', 'success');
                            } else {
                                alert('Özellik başarıyla kaldırıldı ✅');
                            }
                            window.location.reload();
                        } else {
                            if (window.showToast) {
                                window.showToast(data.message || 'Özellik kaldırma başarısız ❌', 'error');
                            } else {
                                alert(data.message || 'Özellik kaldırma başarısız ❌');
                            }
                        }
                    } catch (error) {
                        if (window.showToast) {
                            window.showToast('Özellik kaldırılırken hata oluştu ❌', 'error');
                        } else {
                            alert('Özellik kaldırılırken hata oluştu ❌');
                        }
                    }
                },
            };
        };
    </script>
    <div class="container mx-auto px-4 py-8" x-data="fieldDependenciesManager()">
        <div class="max-w-7xl mx-auto">
            <!-- Selection Summary -->
            <div class="mb-8">
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl shadow-sm p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-blue-800 dark:text-blue-200">
                                Kategori & Yayın Tipi Seçimi
                            </h2>
                            <p class="mt-2 text-sm text-blue-700 dark:text-blue-300" x-show="selectedPropertyTypeId">
                                Seçili yayın tipi:
                                <span class="font-semibold" x-text="selectedPropertyTypeName"></span>.
                                Bu kombinasyona ait zorunlu ve isteğe bağlı tüm özellikler aşağıdaki listede yer alır.
                            </p>
                            <p class="mt-2 text-sm text-blue-700 dark:text-blue-300" x-show="!selectedPropertyTypeId">
                                Başlamak için aşağıdaki yayın tiplerinden birini seçin; seçim yaptıktan sonra
                                ilgili özellikler otomatik olarak gösterilecektir.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            <div
                                class="bg-white dark:bg-gray-900 rounded-lg border border-blue-100 dark:border-blue-800 px-4 py-3">
                                <p class="text-xs uppercase tracking-wide text-blue-500 dark:text-blue-300">Ana Kategori</p>
                                <p class="mt-1 font-semibold text-blue-900 dark:text-blue-100">{{ $kategori->name }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-blue-100 dark:border-blue-800 px-4 py-3"
                                x-show="propertyTypes.length > 0">
                                <p class="text-xs uppercase tracking-wide text-blue-500 dark:text-blue-300">Alt Kategori</p>
                                <p class="mt-1 font-semibold text-blue-900 dark:text-blue-100">
                                    Seçim sırasında otomatik belirlenir
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-900 rounded-lg border border-blue-100 dark:border-blue-800 px-4 py-3"
                                x-show="propertyTypes.length > 0">
                                <p class="text-xs uppercase tracking-wide text-blue-500 dark:text-blue-300">Yayın Tipi</p>
                                <p class="mt-1 font-semibold text-blue-900 dark:text-blue-100"
                                    x-text="selectedPropertyTypeName || 'Henüz seçilmedi'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.property_types.index') }}"
                                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                                    Yayın Tipi Yöneticisi
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ route('admin.property_types.show', $kategori->id) }}"
                                        class="ml-1 text-gray-700 dark:text-gray-300 hover:text-blue-600">{{ $kategori->name }}</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="ml-1 text-gray-500 dark:text-gray-400">Özellik Yönetimi</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Özellik Yönetimi
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        <span class="font-semibold">{{ $kategori->name }}</span> kategorisi için özellik atamalarını yönetin
                    </p>
                    <div class="mt-3 flex items-center gap-2 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">💡 İpucu:</span>
                        <a href="{{ route('admin.ozellikler.features.index') }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline font-medium">
                            Özellikleri Oluştur/Düzenle
                        </a>
                        <span class="text-gray-400">•</span>
                        <a href="{{ route('admin.ozellikler.kategoriler.index') }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 underline font-medium">
                            Özellik Kategorilerini Yönet
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.ozellikler.features.index') }}"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Özellikleri Yönet
                    </a>
                    <a href="{{ route('admin.property_types.show', $kategori->id) }}"
                        class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Geri Dön
                    </a>
                </div>
            </div>

            <!-- Property Type Tabs -->
            @if ($yayinTipleri->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 overflow-x-auto">
                        <nav class="-mb-px flex space-x-8 min-w-max" aria-label="Tabs">
                            @foreach ($yayinTipleri as $index => $yayinTipi)
                                @php
                                    $typeSlug = $yayinTipi->slug ?? $yayinTipi->yayin_tipi;
                                @endphp
                                <button @click="setPropertyType('{{ $typeSlug }}')"
                                    :class="activeTab === '{{ $typeSlug }}' ? 'active' : ''"
                                    class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ $yayinTipi->yayin_tipi }}
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full"
                                        :class="activeTab === '{{ $typeSlug }}' ?
                                            'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' :
                                            'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
                                        {{ isset($fieldDependencies[$typeSlug]) ? $fieldDependencies[$typeSlug]->count() : 0 }}
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Tab Contents -->
                    @foreach ($yayinTipleri as $index => $yayinTipi)
                        @php
                            $typeSlug = $yayinTipi->slug ?? $yayinTipi->yayin_tipi;
                        @endphp
                        <div x-show="activeTab === '{{ $typeSlug }}'" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="p-6">

                            <!-- Header with Add Feature Button -->
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ $yayinTipi->yayin_tipi }} - Atanmış Özellikler
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ isset($fieldDependencies[$typeSlug]) ? $fieldDependencies[$typeSlug]->count() : 0 }}
                                        özellik atandı
                                    </p>
                                </div>
                                <button @click="setPropertyType('{{ $typeSlug }}'); showAddFeatureModal = true"
                                    class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 active:scale-95">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Özellik Ekle
                                </button>
                            </div>

                            <!-- Assigned Features List -->
                            @php
                                $assignments = isset($fieldDependencies[$typeSlug])
                                    ? $fieldDependencies[$typeSlug]
                                    : collect([]);
                            @endphp

                            @if ($assignments && $assignments->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($assignments as $assignment)
                                        @php
                                            $feature = $assignment->feature;
                                        @endphp
                                        <div
                                            class="feature-card bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                                            <!-- Header -->
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex-1">
                                                    <h4
                                                        class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                                        @if ($feature->field_icon)
                                                            <span class="text-lg">{{ $feature->field_icon }}</span>
                                                        @endif
                                                        {{ $feature->name }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <span class="font-mono">{{ $feature->slug }}</span>
                                                        <span class="mx-1">•</span>
                                                        <span class="capitalize">{{ $feature->field_type }}</span>
                                                    </p>
                                                </div>

                                                <!-- Delete Button -->
                                                <button
                                                    @click="unassignFeature({{ $yayinTipi->id }}, {{ $feature->id }})"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Category -->
                                            @if ($feature->category)
                                                <div class="mb-3">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                        {{ $feature->category->name }}
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Toggle Switches -->
                                            <div class="space-y-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                                                <!-- Visible Toggle -->
                                                <label class="flex items-center justify-between cursor-pointer group">
                                                    <span
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                        Görünür
                                                    </span>
                                                    <div class="relative">
                                                        <input type="checkbox" class="sr-only peer"
                                                            {{ $assignment->is_visible ? 'checked' : '' }}
                                                            @change="toggleAssignment({{ $assignment->id }}, 'is_visible', $event.target.checked)">
                                                        <div
                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                                        </div>
                                                    </div>
                                                </label>

                                                <!-- Required Toggle -->
                                                <label class="flex items-center justify-between cursor-pointer group">
                                                    <span
                                                        class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors flex items-center gap-2">
                                                        Zorunlu
                                                        @if ($assignment->is_required)
                                                            <span class="text-red-500">*</span>
                                                        @endif
                                                    </span>
                                                    <div class="relative">
                                                        <input type="checkbox" class="sr-only peer"
                                                            {{ $assignment->is_required ? 'checked' : '' }}
                                                            @change="toggleAssignment({{ $assignment->id }}, 'is_required', $event.target.checked)">
                                                        <div
                                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600">
                                                        </div>
                                                    </div>
                                                </label>

                                                <!-- Group Name (if set) -->
                                                @if ($assignment->group_name)
                                                    <div
                                                        class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                        </svg>
                                                        {{ $assignment->group_name }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- AI Features (if any) -->
                                            @php
                                                // ✅ Context7: hasAiCapabilities() method kontrolü (stdClass için)
                                                $hasAiCapabilities = method_exists($feature, 'hasAiCapabilities')
                                                    ? $feature->hasAiCapabilities()
                                                    : ($feature->ai_auto_fill ?? false) ||
                                                        ($feature->ai_suggestion ?? false) ||
                                                        ($feature->ai_calculation ?? false);
                                            @endphp
                                            @if ($hasAiCapabilities)
                                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex flex-wrap gap-1">
                                                        @if ($feature->ai_auto_fill ?? false)
                                                            <span
                                                                class="px-2 py-0.5 text-xs rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                                                                🤖 Otomatik
                                                            </span>
                                                        @endif
                                                        @if ($feature->ai_suggestion ?? false)
                                                            <span
                                                                class="px-2 py-0.5 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                                💡 Öneri
                                                            </span>
                                                        @endif
                                                        @if ($feature->ai_calculation ?? false)
                                                            <span
                                                                class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                                                ⚡ Hesaplama
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Empty State -->
                                <div
                                    class="text-center py-12 bg-gray-50 dark:bg-gray-900 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Henüz özellik
                                        atanmamış</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Bu yayın tipine özellik ekleyerek başlayın
                                    </p>
                                    <div class="mt-6">
                                        <button
                                            @click="setPropertyType('{{ $typeSlug }}'); showAddFeatureModal = true"
                                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            İlk Özelliği Ekle
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Property Types -->
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Henüz yayın tipi tanımlanmamış
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Özellik yönetimi için önce yayın tipleri (örn: "Satılık", "Kiralık") eklemelisiniz.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.property-type-manager.show', $kategori->id) }}"
                                    class="text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100">
                                    Yayın Tipi Yöneticisi'ne Git →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Add Feature Modal -->
        <div x-show="showAddFeatureModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="display: none;">

            <div @click.away="showAddFeatureModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Özellik Ekle
                    </h3>
                    <button @click="showAddFeatureModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[70vh]">
                    @foreach ($availableFeatures as $categoryName => $features)
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $categoryName }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($features as $feature)
                                    <label
                                        class="flex items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-colors border border-transparent hover:border-blue-300 dark:hover:border-blue-700">
                                        <input type="checkbox"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600"
                                            :checked="selectedFeatures.includes({{ $feature->id }})"
                                            @change="toggleFeatureSelection({{ $feature->id }})">
                                        <span class="ml-3 flex-1">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                @if ($feature->field_icon)
                                                    {{ $feature->field_icon }}
                                                @endif
                                                {{ $feature->name }}
                                            </span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">
                                                {{ $feature->slug }} • {{ $feature->field_type }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span x-text="selectedFeatures.length"></span> özellik seçildi
                    </div>
                    <div class="flex gap-3">
                        <button @click="showAddFeatureModal = false"
                            class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200">
                            İptal
                        </button>
                        <button @click="assignSelectedFeatures()" :disabled="selectedFeatures.length === 0"
                            :class="selectedFeatures.length === 0 ? 'opacity-50 cursor-not-allowed' :
                                'hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 active:scale-95'"
                            class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Özellikleri Ekle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
