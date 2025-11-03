<?php $__env->startSection('title', 'Notification Test Page'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                🔔 Notification Test Page
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Test notification system and components
            </p>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                🍞 Toast Notification Test
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="window.toast?.success('Success toast test!')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    ✓ Success
                </button>
                <button onclick="window.toast?.error('Error toast test!')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    ✕ Error
                </button>
                <button onclick="window.toast?.warning('Warning toast test!')" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    ⚠ Warning
                </button>
                <button onclick="window.toast?.info('Info toast test!')" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ℹ Info
                </button>
            </div>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                ⚡ AJAX Helper Test
            </h2>
            <button onclick="testAjaxHelper()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Test AJAX Helper
            </button>
            <pre id="ajax-result" class="mt-4 p-4 bg-gray-100 dark:bg-gray-900 rounded-lg text-sm overflow-auto max-h-60"></pre>
        </div>

        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                🎨 UI Helpers Test
            </h2>
            <div class="space-y-3">
                <button onclick="window.smoothScroll?.('test-target')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Smooth Scroll Test
                </button>
                <button onclick="testConfirmDialog()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Confirm Dialog Test
                </button>
                <div id="test-target" class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    🎯 Scroll Target
                </div>
            </div>
        </div>

    </div>
</div>

<script>
async function testAjaxHelper() {
    const result = document.getElementById('ajax-result');
    result.textContent = 'Testing...';
    
    if (!window.AjaxHelper) {
        result.textContent = '❌ AjaxHelper not loaded!';
        return;
    }
    
    const response = await window.AjaxHelper.get('/api/health');
    result.textContent = JSON.stringify(response, null, 2);
    
    if (response.success) {
        window.toast?.success('AJAX test başarılı!');
    }
}

async function testConfirmDialog() {
    if (!window.confirmDialog) {
        alert('confirmDialog not loaded!');
        return;
    }
    
    const confirmed = await window.confirmDialog('Bu bir test mesajıdır. Onaylıyor musunuz?');
    window.toast?.[confirmed ? 'success' : 'info'](confirmed ? 'Onaylandı!' : 'İptal edildi');
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanemlakwarp/resources/views/admin/notifications/test.blade.php ENDPATH**/ ?>