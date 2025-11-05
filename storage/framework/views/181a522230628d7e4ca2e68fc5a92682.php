<?php $__env->startSection('title', 'Özellik Kategorisi Düzenle'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Kategori Düzenle: <?php echo e($kategori->name); ?></h1>
        <a href="<?php echo e(route('admin.ozellikler.kategoriler.index')); ?>"
            class="px-4 py-2 neo-btn-outline  rounded-lg shadow-sm transition-colors duration-200 touch-target-optimized touch-target-optimized">
            <i class="fas fa-arrow-left mr-2"></i> Geri Dön
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="<?php echo e(route('admin.ozellikler.kategoriler.update', $kategori->id)); ?>" method="POST"
                x-data="{ showAdvanced: false }">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <?php if($errors->any()): ?>
                    <?php if (isset($component)) { $__componentOriginald888329b8246e32afd68d2decbd25cf1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald888329b8246e32afd68d2decbd25cf1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.alert','data' => ['type' => 'error']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'error']); ?>
                        <p class="font-semibold mb-1">Lütfen aşağıdaki hataları düzeltin:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald888329b8246e32afd68d2decbd25cf1)): ?>
<?php $attributes = $__attributesOriginald888329b8246e32afd68d2decbd25cf1; ?>
<?php unset($__attributesOriginald888329b8246e32afd68d2decbd25cf1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald888329b8246e32afd68d2decbd25cf1)): ?>
<?php $component = $__componentOriginald888329b8246e32afd68d2decbd25cf1; ?>
<?php unset($__componentOriginald888329b8246e32afd68d2decbd25cf1); ?>
<?php endif; ?>
                <?php endif; ?>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <?php if (isset($component)) { $__componentOriginal187e7f8ae725d0d7c586a97e85953c03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.input','data' => ['name' => 'name','label' => 'Kategori Adı','required' => true,'value' => $kategori->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => 'Kategori Adı','required' => true,'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kategori->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $attributes = $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $component = $__componentOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal694712473b787cd740db4e46be9da3f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal694712473b787cd740db4e46be9da3f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.textarea','data' => ['name' => 'description','label' => 'Açıklama','value' => $kategori->description,'rows' => '4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Açıklama','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kategori->description),'rows' => '4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal694712473b787cd740db4e46be9da3f9)): ?>
<?php $attributes = $__attributesOriginal694712473b787cd740db4e46be9da3f9; ?>
<?php unset($__attributesOriginal694712473b787cd740db4e46be9da3f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal694712473b787cd740db4e46be9da3f9)): ?>
<?php $component = $__componentOriginal694712473b787cd740db4e46be9da3f9; ?>
<?php unset($__componentOriginal694712473b787cd740db4e46be9da3f9); ?>
<?php endif; ?>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Uygulama Alanı
                            </label>
                            <select name="applies_to" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Tüm Emlak Türleri</option>
                                <option value="konut" <?php echo e(old('applies_to', $kategori->applies_to) == 'konut' ? 'selected' : ''); ?>>Konut</option>
                                <option value="arsa" <?php echo e(old('applies_to', $kategori->applies_to) == 'arsa' ? 'selected' : ''); ?>>Arsa</option>
                                <option value="yazlik" <?php echo e(old('applies_to', $kategori->applies_to) == 'yazlik' ? 'selected' : ''); ?>>Yazlık</option>
                                <option value="isyeri" <?php echo e(old('applies_to', $kategori->applies_to) == 'isyeri' ? 'selected' : ''); ?>>İşyeri</option>
                                <option value="konut,arsa" <?php echo e(old('applies_to', $kategori->applies_to) == 'konut,arsa' ? 'selected' : ''); ?>>Konut + Arsa</option>
                                <option value="konut,arsa,yazlik,isyeri" <?php echo e(old('applies_to', $kategori->applies_to) == 'konut,arsa,yazlik,isyeri' ? 'selected' : ''); ?>>Tüm Türler</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bu kategori hangi emlak türleri için geçerli olsun?</p>
                        </div>

                        <?php if (isset($component)) { $__componentOriginal187e7f8ae725d0d7c586a97e85953c03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.input','data' => ['name' => 'order','type' => 'number','label' => 'Sıra','value' => $kategori->order]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'order','type' => 'number','label' => 'Sıra','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kategori->order)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $attributes = $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $component = $__componentOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalab5a54c7a6b843251f5286ea214d67cb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab5a54c7a6b843251f5286ea214d67cb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.toggle','data' => ['name' => 'status','label' => 'Aktif','checked' => $kategori->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','label' => 'Aktif','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kategori->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab5a54c7a6b843251f5286ea214d67cb)): ?>
<?php $attributes = $__attributesOriginalab5a54c7a6b843251f5286ea214d67cb; ?>
<?php unset($__attributesOriginalab5a54c7a6b843251f5286ea214d67cb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab5a54c7a6b843251f5286ea214d67cb)): ?>
<?php $component = $__componentOriginalab5a54c7a6b843251f5286ea214d67cb; ?>
<?php unset($__componentOriginalab5a54c7a6b843251f5286ea214d67cb); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <div class="mb-4">
                            <button type="button" @click="showAdvanced=!showAdvanced"
                                class="text-sm text-indigo-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Gelişmiş Alanlar
                            </button>
                        </div>
                        <div x-show="showAdvanced" x-cloak>
                            <div>
                                <?php if (isset($component)) { $__componentOriginal187e7f8ae725d0d7c586a97e85953c03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.input','data' => ['name' => 'slug','label' => 'Slug','value' => $kategori->slug,'help' => 'Boş ise addan türetilir']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('admin.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'slug','label' => 'Slug','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kategori->slug),'help' => 'Boş ise addan türetilir']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $attributes = $__attributesOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__attributesOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03)): ?>
<?php $component = $__componentOriginal187e7f8ae725d0d7c586a97e85953c03; ?>
<?php unset($__componentOriginal187e7f8ae725d0d7c586a97e85953c03); ?>
<?php endif; ?>
                                <p id="slug-feedback" class="mt-1 text-xs"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="<?php echo e(route('admin.ozellikler.kategoriler.index')); ?>"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow-sm text-sm">İptal</a>
                    <button type="submit"
                        class="px-5 py-2 neo-btn neo-btn-primary rounded-lg text-sm font-medium touch-target-optimized touch-target-optimized">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kategori İstatistikleri -->
    <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kategori İstatistikleri</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Özellik Sayısı</h3>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?php echo e($kategori->features->count()); ?>

                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Oluşturulma Tarihi</h3>
                    <p class="text-md font-medium text-gray-600 dark:text-gray-400">
                        <?php echo e($kategori->created_at->format('d.m.Y H:i')); ?></p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <h3 class="admin-h3">Son Güncelleme</h3>
                    <p class="text-md font-medium text-gray-600 dark:text-gray-400">
                        <?php echo e($kategori->updated_at->format('d.m.Y H:i')); ?></p>
                </div>
            </div>

            <div class="mt-4">
                <a href="<?php echo e(route('admin.ozellikler.kategoriler.ozellikler', $kategori->id)); ?>"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:text-indigo-100 dark:bg-indigo-900 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i> Bu Kategorideki Özellikleri Görüntüle
                </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adInput = document.getElementById('ad');
            const slugInput = document.getElementById('slug');
            const slugFeedback = document.getElementById('slug-feedback');
            let lastCheckedSlug = '';

            function generateSlug(t) {
                return t.toLowerCase().replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's').replace(/ı/g, 'i')
                    .replace(/ö/g, 'o').replace(/ç/g, 'c').replace(/[^a-z0-9]+/g, '-').replace(/-+/g, '-').replace(
                        /^-|-$/g, '');
            }

            function debounce(fn, wait = 400) {
                let to;
                return (...a) => {
                    clearTimeout(to);
                    to = setTimeout(() => fn(...a), wait);
                }
            }

            function setFeedback(state, msg) {
                if (!slugFeedback) return;
                slugFeedback.textContent = msg || '';
                slugFeedback.className = 'mt-1 text-xs ' + (state === 'ok' ? 'text-green-600 dark:text-green-400' :
                    state === 'err' ? 'text-red-600 dark:text-red-400' : 'text-gray-400');
                if (slugInput) {
                    slugInput.classList.remove('border-red-500', 'border-green-500');
                    if (state === 'ok') slugInput.classList.add('border-green-500');
                    if (state === 'err') slugInput.classList.add('border-red-500');
                }
            }
            adInput?.addEventListener('input', () => {
                if (slugInput && !slugInput.value) {
                    slugInput.value = generateSlug(adInput.value);
                }
            });
            const checkSlug = debounce(() => {
                if (!slugInput) return;
                const val = slugInput.value.trim();
                if (!val) {
                    setFeedback('neutral', '');
                    return;
                }
                if (val === lastCheckedSlug) return;
                lastCheckedSlug = val;
                setFeedback('neutral', 'Kontrol ediliyor...');
                fetch(
                        `<?php echo e(route('admin.ozellikler.kategoriler.slug.check')); ?>?slug=${encodeURIComponent(val)}&ignore=<?php echo e($kategori->id); ?>`
                        )
                    .then(r => r.json()).then(d => {
                        if (d.unique) {
                            setFeedback('ok', 'Uygun');
                        } else {
                            setFeedback('err', 'Slug kullanımda');
                        }
                    })
                    .catch(() => setFeedback('err', 'Kontrol hatası'));
            }, 500);
            slugInput?.addEventListener('input', checkSlug);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ozellikler/kategoriler/edit.blade.php ENDPATH**/ ?>