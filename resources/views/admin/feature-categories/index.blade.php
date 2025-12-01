@extends('admin.layouts.admin')

@section('title', 'Feature Categories - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    Feature Categories
                </h1>
                <p class="text-lg text-gray-600 mt-2">Manage property feature categories</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.feature-categories.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Add Category
                </a>
            </div>
        </div>
    </div>

    <div class="px-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-600">{{ $categories->total() }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Total Categories</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-green-600">{{ $categories->where('status', 'active')->count() }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Active Categories</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-yellow-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-orange-600">{{ $categories->sum('features_count') }}</h3>
                        <p class="text-sm text-gray-600 font-medium">Total Features</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Feature Categories</h2>
                <div class="flex gap-2">
                    <input type="text" placeholder="Search categories..."
                           class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <select style="color-scheme: light dark;" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Icon</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Features</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Order</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $category->name ?? 'Unnamed' }}</div>
                                        @if($category->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} text-lg text-gray-600"></i>
                                    @else
                                        <i class="fas fa-tag text-lg text-gray-400"></i>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->features_count ?? 0 }} features
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    @if($category->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-sm text-gray-600">{{ $category->display_order ?? 0 }}</span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.feature-categories.show', $category) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.feature-categories.edit', $category) }}"
                                           class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteCategory({{ $category->id }})"
                                                class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-tags text-4xl mb-4"></i>
                                        <p class="text-lg">No feature categories found</p>
                                        <p class="text-sm">Create your first category to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
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
                location.reload();
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
