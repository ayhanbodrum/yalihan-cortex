@extends('admin.layouts.neo')

@section('title', 'Feature Category Details - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    {{ $featureCategory->name ?? 'Feature Category' }}
                </h1>
                <p class="text-lg text-gray-600 mt-2">Category details and information</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.feature-categories.edit', $featureCategory) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Category
                </a>
                <a href="{{ route('admin.feature-categories.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $featureCategory->name ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <div class="text-sm text-gray-600 bg-gray-50 px-4 py-2.5 rounded">{{ $featureCategory->slug ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <div class="flex items-center">
                                @if($featureCategory->icon)
                                    <i class="{{ $featureCategory->icon }} text-2xl text-blue-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ $featureCategory->icon }}</span>
                                @else
                                    <i class="fas fa-tag text-2xl text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-500">No icon set</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $featureCategory->display_order ?? 0 }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            @if($featureCategory->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-pause-circle mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Features Count</label>
                            <div class="text-lg font-semibold text-blue-600">{{ $featureCategory->features_count ?? 0 }} features</div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($featureCategory->description)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Description</h2>
                        <p class="text-gray-700 leading-relaxed">{{ $featureCategory->description }}</p>
                    </div>
                @endif

                <!-- Property Types -->
                @if($featureCategory->applies_to)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Applies To Property Types</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $featureCategory->applies_to) as $type)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ trim($type) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SEO Information -->
                @if($featureCategory->meta_title || $featureCategory->meta_description || $featureCategory->seo_keywords)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">SEO Information</h2>

                        @if($featureCategory->meta_title)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                <div class="text-sm text-gray-700">{{ $featureCategory->meta_title }}</div>
                            </div>
                        @endif

                        @if($featureCategory->meta_description)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                <div class="text-sm text-gray-700">{{ $featureCategory->meta_description }}</div>
                            </div>
                        @endif

                        @if($featureCategory->seo_keywords)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">SEO Keywords</label>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $featureCategory->seo_keywords) as $keyword)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ trim($keyword) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.feature-categories.edit', $featureCategory) }}"
                           class="w-full flex items-center justify-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Category
                        </a>

                        <button onclick="deleteCategory({{ $featureCategory->id }})"
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Category
                        </button>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistics</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Features Count</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $featureCategory->features_count ?? 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Display Order</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $featureCategory->display_order ?? 0 }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status</span>
                            @if($featureCategory->status === 'active')
                                <span class="text-sm text-green-600 font-medium">Active</span>
                            @else
                                <span class="text-sm text-gray-600 font-medium">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Timestamps</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-600">Created</span>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $featureCategory->created_at ? $featureCategory->created_at->format('d.m.Y H:i') : 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <span class="text-sm text-gray-600">Updated</span>
                            <div class="text-sm font-medium text-gray-900">
                                {{ $featureCategory->updated_at ? $featureCategory->updated_at->format('d.m.Y H:i') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        fetch(`/admin/feature-categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.feature-categories.index") }}';
            } else {
                alert('Error deleting category: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting category');
        });
    }
}
</script>
@endpush
