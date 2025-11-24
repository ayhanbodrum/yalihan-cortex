@extends('admin.layouts.neo')

@section('title', 'Bildirim Oluştur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bildirim Oluştur</h1>
        <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Geri Dön</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-100">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Başlık</label>
                    <input id="title" name="title" type="text" required value="{{ old('title') }}" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                </div>

                <div class="md:col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mesaj</label>
                    <textarea id="message" name="message" rows="6" required class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">{{ old('message') }}</textarea>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tür</label>
                    <select id="type" name="type" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>Bilgi</option>
                        <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>Başarılı</option>
                        <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Uyarı</option>
                        <option value="error" {{ old('type') === 'error' ? 'selected' : '' }}>Hata</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Öncelik</label>
                    <select id="priority" name="priority" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Yüksek</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Acil</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Düşük</option>
                    </select>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol (opsiyonel)</label>
                    <input id="role" name="role" type="text" value="{{ old('role') }}" placeholder="ör. danisman" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı (opsiyonel)</label>
                    <input id="user_id" name="user_id" type="number" value="{{ old('user_id') }}" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Oluştur</button>
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection