<?php $__env->startSection('title', 'Kategori Özellikleri: ' . $kategori->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800" x-data="{
        selectedIds: [],
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            const checkboxes = document.querySelectorAll('.feature-checkbox');
            this.selectedIds = this.selectAll ? Array.from(checkboxes).map(cb => cb.value) : [];
        },
        toggleSelect(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
            this.selectAll = this.selectedIds.length === <?php echo e($kategori->features->count()); ?>;
        },
        submitBulkAction(action) {
            if (this.selectedIds.length === 0) {
                alert('Lütfen en az bir özellik seçin!');
                return;
            }
            if (action === 'delete' && !confirm(this.selectedIds.length + ' özelliği silmek istediğinizden emin misiniz?')) {
                return;
            }
            document.getElementById('bulk-action').value = action;
            document.getElementById('bulk-ids').value = JSON.stringify(this.selectedIds);
            document.getElementById('bulk-form').submit();
        }
    }">
        <div class="p-6">
            <!-- Success Message -->
            <?php if(session('success')): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e(session('success')); ?>

                    </div>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-tag mr-2"></i> <?php echo e($kategori->name); ?> Kategorisindeki Özellikler
                </h1>
                <div class="flex space-x-2">
                    <a href="<?php echo e(route('admin.ozellikler.kategoriler.index')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-gray-500 transition-all duration-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 touch-target-optimized">
                        <i class="fas fa-arrow-left mr-2"></i> Kategorilere Dön
                    </a>
                    <a href="<?php echo e(route('admin.ozellikler.features.create')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hover:scale-105 active:scale-95 focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg touch-target-optimized">
                        <i class="fas fa-plus mr-2"></i> Yeni Özellik Ekle
                    </a>
                </div>
            </div>
            <!-- Kategori Bilgileri -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <div class="flex flex-col md:flex-row md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Kategori Bilgileri</h2>
                        <p class="text-blue-600 dark:text-blue-300 mt-1"><?php echo e($kategori->name); ?></p>
                    </div>
                    <div class="mt-3 md:mt-0">
                        <span
                            class="px-3 py-1 text-sm <?php echo e($kategori->status ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'); ?> rounded-full">
                            <?php echo e($kategori->status ? 'Aktif' : 'Pasif'); ?>

                        </span>
                        <span class="ml-2 text-gray-600 dark:text-gray-400 text-sm">Oluşturulma:
                            <?php echo e($kategori->created_at->format('d.m.Y')); ?></span>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Toolbar -->
            <?php if($kategori->features->count() > 0): ?>
                <div class="mb-4 p-4 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-lg border border-gray-200 dark:border-gray-700"
                    x-show="selectedIds.length > 0" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold">
                                <span x-text="selectedIds.length"></span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    <span x-text="selectedIds.length"></span> özellik seçildi
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Toplu işlem yapmak için bir aksiyon seçin</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="submitBulkAction('activate')"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Aktif Et
                            </button>
                            <button type="button" @click="submitBulkAction('deactivate')"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Pasif Et
                            </button>
                            <button type="button" @click="submitBulkAction('delete')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Sil
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Bulk Action Form (Hidden) -->
            <form id="bulk-form" method="POST" action="<?php echo e(route('admin.ozellikler.bulk-action')); ?>" style="display: none;">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="bulk-action" name="action">
                <input type="hidden" id="bulk-ids" name="ids">
            </form>

            <!-- Özellikler Tablosu -->
            <?php if($kategori->features->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-2.5 text-left w-12">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th scope="col"
                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    ID</th>
                                <th scope="col"
                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Özellik Adı</th>
                                <th scope="col"
                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tür</th>
                                <th scope="col"
                                    class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Durum</th>
                                <th scope="col"
                                    class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php $__currentLoopData = $kategori->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ozellik): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': selectedIds.includes('<?php echo e($ozellik->id); ?>') }">
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <input type="checkbox" value="<?php echo e($ozellik->id); ?>"
                                            class="feature-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            :checked="selectedIds.includes('<?php echo e($ozellik->id); ?>')"
                                            @change="toggleSelect('<?php echo e($ozellik->id); ?>')">
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <?php echo e($ozellik->id); ?></td>
                                    <td
                                        class="px-4 py-2.5 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($ozellik->name ?? 'İsimsiz Özellik'); ?>

                                        <?php if($ozellik->description): ?>
                                            <span
                                                class="block text-xs text-gray-500 dark:text-gray-400"><?php echo e(Str::limit($ozellik->description, 50)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            Metin
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($ozellik->enabled ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'); ?>">
                                            <?php echo e($ozellik->enabled ? 'Aktif' : 'Pasif'); ?>

                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-2.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                        <a href="<?php echo e(route('admin.ozellikler.features.edit', $ozellik->id)); ?>"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2"
                                            title="Düzenle">
                                            <i class="fas fa-edit"></i> Düzenle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal34874c6961c0544aaa07cd3c1d2e7465 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.neo.empty-state','data' => ['title' => 'Bu kategoride henüz özellik bulunmuyor','description' => 'Bir özellik ekleyerek başlayabilirsiniz','actionHref' => route('admin.ozellikler.features.create'),'actionText' => 'Yeni Özellik Ekle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('neo.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Bu kategoride henüz özellik bulunmuyor','description' => 'Bir özellik ekleyerek başlayabilirsiniz','actionHref' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.ozellikler.features.create')),'actionText' => 'Yeni Özellik Ekle']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465)): ?>
<?php $attributes = $__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465; ?>
<?php unset($__attributesOriginal34874c6961c0544aaa07cd3c1d2e7465); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34874c6961c0544aaa07cd3c1d2e7465)): ?>
<?php $component = $__componentOriginal34874c6961c0544aaa07cd3c1d2e7465; ?>
<?php unset($__componentOriginal34874c6961c0544aaa07cd3c1d2e7465); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ozellikler/kategoriler/ozellikler.blade.php ENDPATH**/ ?>