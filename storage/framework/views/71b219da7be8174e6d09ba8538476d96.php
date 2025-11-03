

<div class="neo-card mt-6" x-data="{ expanded: true }">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700 cursor-pointer" @click="expanded = !expanded">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        ✨ Yazlık Özellikleri & Amenities
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Yazlığınızın donanımlarını, manzarasını ve olanaklarını belirtin
                    </p>
                </div>
            </div>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6 transform transition-transform duration-200" 
                     :class="{'rotate-180': !expanded}" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="p-6" x-show="expanded" x-collapse>
        <?php
            $yazlikCategory = \App\Models\FeatureCategory::where('slug', 'yazlik-amenities')->first();
            $yazlikFeatures = $yazlikCategory ? $yazlikCategory->features()->orderBy('order')->get() : collect();
            $selectedFeatures = isset($ilan) ? $ilan->features->pluck('pivot.feature_id')->toArray() : [];
            $featureValues = isset($ilan) ? $ilan->features->pluck('pivot.value', 'id')->toArray() : [];
        ?>

        <?php if($yazlikFeatures->count() > 0): ?>
            
            <?php
                $featureGroups = [
                    'Temel Donanımlar' => $yazlikFeatures->whereBetween('order', [1, 10]),
                    'Manzara & Konum' => $yazlikFeatures->whereBetween('order', [11, 20]),
                    'Dış Mekan' => $yazlikFeatures->whereBetween('order', [21, 30]),
                    'Güvenlik & Ekstralar' => $yazlikFeatures->whereBetween('order', [31, 40]),
                ];
            ?>

            <?php $__currentLoopData = $featureGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $features): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($features->count() > 0): ?>
                    <div class="mb-8 last:mb-0">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                            <span class="w-1 h-6 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-3"></span>
                            <?php echo e($groupName); ?>

                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="feature-item">
                                    <?php if($feature->type === 'boolean'): ?>
                                        
                                        <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer group">
                                            <input type="checkbox" 
                                                   name="features[<?php echo e($feature->id); ?>]" 
                                                   value="1"
                                                   <?php echo e(in_array($feature->id, $selectedFeatures) ? 'checked' : ''); ?>

                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2">
                                            <div class="ml-3 flex-1">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    <?php echo e($feature->name); ?>

                                                </span>
                                                <?php if($feature->description): ?>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <?php echo e($feature->description); ?>

                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </label>

                                    <?php elseif($feature->type === 'select'): ?>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                <?php echo e($feature->name); ?>

                                                <?php if($feature->description): ?>
                                                    <span class="text-xs text-gray-500 block mt-1"><?php echo e($feature->description); ?></span>
                                                <?php endif; ?>
                                            </label>
                                            <select                                                     name="features[<?php echo e($feature->id); ?>]" 
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                                <option value="">Seçin...</option>
                                                <?php
                                                    $options = is_string($feature->options) 
                                                        ? json_decode($feature->options, true) 
                                                        : $feature->options;
                                                    $currentValue = $featureValues[$feature->id] ?? '';
                                                ?>
                                                <?php if(is_array($options)): ?>
                                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($option); ?>" <?php echo e($currentValue === $option ? 'selected' : ''); ?>>
                                                            <?php echo e($option); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                    <?php elseif($feature->type === 'number'): ?>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                                <?php echo e($feature->name); ?>

                                                <?php if($feature->unit): ?>
                                                    <span class="text-gray-500 text-xs">(<?php echo e($feature->unit); ?>)</span>
                                                <?php endif; ?>
                                                <?php if($feature->description): ?>
                                                    <span class="text-xs text-gray-500 block mt-1"><?php echo e($feature->description); ?></span>
                                                <?php endif; ?>
                                            </label>
                                            <input type="number" 
                                                   name="features[<?php echo e($feature->id); ?>]"
                                                   value="<?php echo e($featureValues[$feature->id] ?? ''); ?>"
                                                   placeholder="<?php echo e($feature->name); ?>"
                                                   step="any"
                                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800 dark:text-blue-300">
                        <p class="font-medium">💡 İpucu:</p>
                        <p class="mt-1">Seçtiğiniz özellikler ilan detay sayfasında ziyaretçilere gösterilecektir. Daha fazla özellik seçmek, ilanınızın görünürlüğünü artırabilir.</p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">Henüz yazlık özelliği eklenmemiş</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Yazlık özellikleri eklemek için önce feature seeder'ı çalıştırın:
                    <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs mt-2 inline-block">
                        php artisan db:seed --class=YazlikAmenitiesSeeder
                    </code>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Feature selection counter
document.addEventListener('DOMContentLoaded', () => {
    const featureCheckboxes = document.querySelectorAll('input[name^="features["][type="checkbox"]');
    
    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedCount = document.querySelectorAll('input[name^="features["][type="checkbox"]:checked').length;
            console.log(`Selected features: ${selectedCount}`);
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/ilanlar/partials/yazlik-features.blade.php ENDPATH**/ ?>