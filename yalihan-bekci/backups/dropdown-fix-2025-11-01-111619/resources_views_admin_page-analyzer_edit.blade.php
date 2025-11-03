@extends('admin.layouts.neo')

@section('title', 'Edit Analysis Session - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    Edit Analysis Session
                </h1>
                <p class="text-lg text-gray-600 mt-2">Update analysis session configuration</p>
            </div>
            <a href="{{ route('admin.page-analyzer.index') }}" class="neo-btn neo-btn neo-btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Sessions
            </a>
        </div>
    </div>

    <div class="px-6">
        <div class="neo-card p-6 max-w-2xl">
            <form action="{{ route('admin.page-analyzer.update', $config['id'] ?? 1) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <!-- Session Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Session Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $config['name'] ?? 'Unnamed Session') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter session name"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter analysis description">{{ old('description', $config['description'] ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Analysis Type -->
                <div class="mb-6">
                    <label for="analysis_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Analysis Type <span class="text-red-500">*</span>
                    </label>
                    <select id="analysis_type"
                            name="analysis_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('analysis_type') border-red-500 @enderror"
                            required>
                        <option value="">Select Analysis Type</option>
                        <option value="complete" {{ old('analysis_type', $config['type'] ?? '') === 'complete' ? 'selected' : '' }}>Complete Analysis</option>
                        <option value="performance" {{ old('analysis_type', $config['type'] ?? '') === 'performance' ? 'selected' : '' }}>Performance Analysis</option>
                        <option value="security" {{ old('analysis_type', $config['type'] ?? '') === 'security' ? 'selected' : '' }}>Security Analysis</option>
                        <option value="partial" {{ old('analysis_type', $config['type'] ?? '') === 'partial' ? 'selected' : '' }}>Partial Analysis</option>
                        <option value="single" {{ old('analysis_type', $config['type'] ?? '') === 'single' ? 'selected' : '' }}>Single Page Analysis</option>
                    </select>
                    @error('analysis_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Pages -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Target Pages
                    </label>
                    <div class="space-y-2">
                        @php
                            $targetPages = old('target_pages', $config['target_pages'] ?? ['all']);
                        @endphp

                        <label class="flex items-center">
                            <input type="checkbox" name="target_pages[]" value="all"
                                   {{ in_array('all', $targetPages) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">All Admin Pages</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_pages[]" value="ilan"
                                   {{ in_array('ilan', $targetPages) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">İlan Management Pages</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_pages[]" value="crm"
                                   {{ in_array('crm', $targetPages) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">CRM Pages</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_pages[]" value="settings"
                                   {{ in_array('settings', $targetPages) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Settings Pages</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_pages[]" value="analytics"
                                   {{ in_array('analytics', $targetPages) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Analytics Pages</span>
                        </label>
                    </div>
                </div>

                <!-- Analysis Options -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Analysis Options</h3>

                    @php
                        $options = old('options', $config['options'] ?? ['check_methods', 'check_views', 'check_routes']);
                    @endphp

                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="options[]" value="check_methods"
                                   {{ in_array('check_methods', $options) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Check CRUD Methods</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="options[]" value="check_views"
                                   {{ in_array('check_views', $options) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Check View Files</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="options[]" value="check_routes"
                                   {{ in_array('check_routes', $options) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Check Routes</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="options[]" value="check_performance"
                                   {{ in_array('check_performance', $options) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Performance Analysis</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="options[]" value="check_security"
                                   {{ in_array('check_security', $options) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Security Check</span>
                        </label>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Status</h3>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Last Analysis:</span>
                                <span class="text-gray-600">{{ $config['last_analysis'] ?? 'Never' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Average Score:</span>
                                <span class="font-semibold
                                    {{ ($config['average_score'] ?? 0) >= 8 ? 'text-green-600' :
                                       (($config['average_score'] ?? 0) >= 6 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($config['average_score'] ?? 0, 1) }}/10
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Pages Analyzed:</span>
                                <span class="text-gray-600">{{ $config['pages_count'] ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Issues Found:</span>
                                <span class="text-red-600">{{ $config['issues_count'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.page-analyzer.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
    submitBtn.disabled = true;

    // Re-enable after 3 seconds as fallback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Auto-update session name based on type
document.getElementById('analysis_type').addEventListener('change', function() {
    const type = this.value;
    const nameField = document.getElementById('name');

    if (type && nameField.value.includes('Analysis')) {
        const timestamp = new Date().toLocaleString('tr-TR');
        nameField.value = `${type.charAt(0).toUpperCase() + type.slice(1)} Analysis - ${timestamp}`;
    }
});
</script>
@endpush
