@extends('admin.layouts.neo')

@section('title', 'Edit Etiket - Yalıhan Emlak Pro')

@section('content')
    <div class="content-header mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center text-gray-800">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-white text-xl"></i>
                    </div>
                    Edit Etiket
                </h1>
                <p class="text-lg text-gray-600 mt-2">Update etiket information</p>
            </div>
            <a href="{{ route('admin.etiket.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg border border-gray-700 hover:border-gray-600 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Etiketler
            </a>
        </div>
    </div>

    <div class="px-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 max-w-2xl">
            <form action="{{ route('admin.etiket.update', $etiket->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Etiket Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $etiket->name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter etiket name"
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
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter etiket description">{{ old('description', $etiket->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div class="mb-6">
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Color
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="color"
                               id="color"
                               name="color"
                               value="{{ old('color', $etiket->color ?? '#3B82F6') }}"
                               class="w-16 h-10 border border-gray-300 rounded cursor-pointer @error('color') border-red-500 @enderror">
                        <input type="text"
                               id="color_text"
                               value="{{ old('color', $etiket->color ?? '#3B82F6') }}"
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="#3B82F6"
                               pattern="^#[0-9A-Fa-f]{6}$">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Choose a color for this etiket</p>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div class="mb-6">
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                        Icon Class
                    </label>
                    <input type="text"
                           id="icon"
                           name="icon"
                           value="{{ old('icon', $etiket->icon) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('icon') border-red-500 @enderror"
                           placeholder="fas fa-tag">
                    <p class="mt-1 text-sm text-gray-500">FontAwesome icon class (e.g., fas fa-tag, fas fa-star)</p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select style="color-scheme: light dark;" id="status"
                            name="status"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror transition-all duration-200"
                            required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $etiket->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $etiket->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preview
                    </label>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <div class="flex items-center">
                            <div id="previewIcon" class="text-lg mr-2">
                                <i class="{{ $etiket->icon ?? 'fas fa-tag' }}"></i>
                            </div>
                            <span id="previewName" class="font-medium">{{ $etiket->name }}</span>
                            <div id="previewColor" class="w-4 h-4 rounded-full ml-2" style="background-color: {{ $etiket->color ?? '#3B82F6' }};"></div>
                        </div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Status</h3>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Etiket ID:</span>
                                <span class="text-gray-600">#{{ $etiket->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Status:</span>
                                <span class="{{ $etiket->status === 'active' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ ucfirst($etiket->status) }}
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Usage Count:</span>
                                <span class="text-blue-600 font-semibold">{{ $etiket->kisiler_count ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Created:</span>
                                <span class="text-gray-600">{{ $etiket->created_at ? $etiket->created_at->format('d.m.Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.etiket.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Etiket
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Color picker synchronization
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
    document.getElementById('previewColor').style.backgroundColor = this.value;
});

document.getElementById('color_text').addEventListener('input', function() {
    if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
        document.getElementById('color').value = this.value;
        document.getElementById('previewColor').style.backgroundColor = this.value;
    }
});

// Icon preview
document.getElementById('icon').addEventListener('input', function() {
    const previewIcon = document.getElementById('previewIcon');
    const iconClass = this.value || 'fas fa-tag';
    previewIcon.innerHTML = `<i class="${iconClass}"></i>`;
});

// Name preview
document.getElementById('name').addEventListener('input', function() {
    document.getElementById('previewName').textContent = this.value || 'Etiket Name';
});
</script>
@endpush
