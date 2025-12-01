@extends('admin.layouts.admin')

@section('title', 'Etiketler')

@section('content')
    <div class="prose max-w-none p-6">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Etiket Yönetimi</h1>
                    <p class="text-gray-600 mt-2">Müşteri kategorileri için etiketleri yönetin</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.etiketler.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized touch-target-optimized">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Yeni Etiket
                    </a>
                </div>
            </div>
        </div>

        <!-- Başarı Mesajları -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Etiket Tablosu -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead class="admin-table-header">
                        <tr>
                            <th scope="col" class="admin-table-th">
                                Etiket Adı
                            </th>
                            <th scope="col" class="admin-table-th">
                                Renk
                            </th>
                            <th scope="col" class="admin-table-th">
                                Müşteri Sayısı
                            </th>
                            <th scope="col" class="admin-table-th">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="admin-table-body">
                        @forelse($etiketler as $etiket)
                            <tr class="admin-table-row">
                                <td class="admin-table-td">
                                    @php
                                        $defaultColor = '#E5E7EB';
                                        $defaultTextColorClass = 'text-gray-800';
                                        $customTextColorClass = 'text-white';

                                        $etiketRenkTrimmed = trim($etiket->renk ?? '');
                                        $isValidHex = preg_match(
                                            '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i',
                                            $etiketRenkTrimmed,
                                        );

                                        $finalBgColor = $isValidHex ? $etiketRenkTrimmed : $defaultColor;
                                        $finalTextColorClass = $isValidHex
                                            ? $customTextColorClass
                                            : $defaultTextColorClass;

                                        $etiketAdStyles = ["background-color: {$finalBgColor}"];
                                    @endphp
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $finalTextColorClass }}"
                                        @style($etiketAdStyles)>
                                        {{ $etiket->ad }}
                                    </span>
                                </td>
                                <td class="admin-table-td">
                                    @if ($isValidHex)
                                        <div class="flex items-center">
                                            @php
                                                $etiketRenkPreviewStyles = ["background-color: {$finalBgColor}"];
                                            @endphp
                                            <div class="w-6 h-6 rounded-full mr-2" @style($etiketRenkPreviewStyles)></div>
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $etiket->renk }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Renk Yok</span>
                                    @endif
                                </td>
                                <td class="admin-table-td">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $etiket->kisiler->count() }}
                                    </div>
                                </td>
                                <td class="admin-table-td">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.etiketler.edit', $etiket) }}"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.etiketler.destroy', $etiket) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                onclick="return confirm('Bu etiketi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <x-neo.empty-state title="Henüz etiket bulunmuyor" description="Yeni bir etiket ekleyebilirsiniz." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
