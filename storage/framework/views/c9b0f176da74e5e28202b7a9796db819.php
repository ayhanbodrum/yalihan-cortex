<?php $__env->startSection('title', 'Kullanıcı Yönetimi'); ?>
<?php $__env->startSection('page-title', 'Kullanıcılar'); ?>

<?php $__env->startSection('content'); ?>

    
    <?php if(session('success')): ?>
        <div
            class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-800">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-green-800 dark:text-green-200 font-medium"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div
            class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-2 border-red-200 dark:border-red-800">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-red-800 dark:text-red-200 font-medium"><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>

    <?php
        $duplicateEmails = \App\Models\User::select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email')
            ->toArray();
        $passiveCount = \App\Models\User::where('status', 0)->count();
        $passiveEmails = \App\Models\User::where('status', 0)
            ->whereNotNull('email')
            ->limit(5)
            ->pluck('email')
            ->toArray();
    ?>

    <div class="space-y-6">
        
        <div
            class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-2xl border-2 border-indigo-200 dark:border-indigo-800 p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-indigo-900 dark:text-indigo-100 text-lg">AI Kullanıcı Analizi</div>
                        <div class="text-sm text-indigo-700 dark:text-indigo-300">Mükerrer e‑posta ve pasif hesap önerileri
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if(count($duplicateEmails) > 0): ?>
                        <span
                            class="px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-xs font-semibold rounded-full">⚠️
                            Mükerrer: <?php echo e(count($duplicateEmails)); ?></span>
                    <?php else: ?>
                        <span
                            class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs font-semibold rounded-full">✅
                            Temiz</span>
                    <?php endif; ?>
                    <span
                        class="px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 text-xs font-semibold rounded-full">💤
                        Pasif: <?php echo e($passiveCount); ?></span>
                </div>
            </div>
            <?php if(count($duplicateEmails) > 0 || $passiveCount > 0): ?>
                <div class="mt-4 space-y-2 text-xs">
                    <?php if(count($duplicateEmails) > 0): ?>
                        <div
                            class="flex items-start gap-2 text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <span class="font-semibold">Mükerrer e-postalar:</span>
                                <?php echo e(implode(', ', array_slice($duplicateEmails, 0, 3))); ?><?php if(count($duplicateEmails) > 3): ?>
                                    ve <?php echo e(count($duplicateEmails) - 3); ?> tane daha...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($passiveCount > 0): ?>
                        <div
                            class="flex items-start gap-2 text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <span class="font-semibold">Pasif hesaplar:</span>
                                <?php echo e(implode(', ', $passiveEmails)); ?><?php if($passiveCount > count($passiveEmails)): ?>
                                    ve <?php echo e($passiveCount - count($passiveEmails)); ?> tane daha...
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div
            class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
            
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Kullanıcılar</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($users->total()); ?> kullanıcı</p>
                    </div>
                </div>
                <button type="button" onclick="exportUsersCsv()"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg
                           border border-gray-300 dark:border-gray-600
                           bg-gray-50 dark:bg-gray-800
                           text-gray-900 dark:text-white text-sm font-medium
                           hover:bg-gray-50 dark:hover:bg-gray-700
                           transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    CSV
                </button>
            </div>

            
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    
                    <form method="GET" class="relative flex-1 max-w-md">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                                  bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white
                                  placeholder-gray-500 dark:placeholder-gray-400
                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  transition-colors"
                            placeholder="İsim veya email ara..." value="<?php echo e(request('search')); ?>"
                            onchange="this.form.submit()">
                    </form>

                    
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('admin.kullanicilar.index')); ?>"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                              <?php echo e(!request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>">
                            Tümü
                        </a>
                        <a href="<?php echo e(route('admin.kullanicilar.index', ['status' => '1'])); ?>"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                              <?php echo e(request('status') == '1' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>">
                            Aktif
                        </a>
                        <a href="<?php echo e(route('admin.kullanicilar.index', ['status' => '0'])); ?>"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                              <?php echo e(request('status') == '0' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'); ?>">
                            Pasif
                        </a>
                    </div>

                    
                    <form method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                        <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                        <select style="color-scheme: light dark;" name="role" onchange="this.form.submit()"
                            class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                                   bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-white
                                   focus:ring-2 focus:ring-blue-500 cursor-pointer transition-colors">
                            <option value="">Tüm Roller</option>
                            <option value="superadmin" <?php echo e(request('role') == 'superadmin' ? 'selected' : ''); ?>>Super Admin
                            </option>
                            <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>Admin</option>
                            <option value="danisman" <?php echo e(request('role') == 'danisman' ? 'selected' : ''); ?>>Danışman</option>
                            <option value="editor" <?php echo e(request('role') == 'editor' ? 'selected' : ''); ?>>Editor</option>
                            <option value="musteri" <?php echo e(request('role') == 'musteri' ? 'selected' : ''); ?>>Müşteri</option>
                        </select>
                    </form>

                    
                    <a href="<?php echo e(route('admin.kullanicilar.create')); ?>"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg
                          bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700
                          text-white font-semibold text-sm
                          shadow-lg hover:shadow-xl
                          transform hover:scale-105 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Yeni
                    </a>
                </div>
            </div>

            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => request('sort') === 'id_asc' ? 'id_desc' : 'id_asc'])); ?>"
                                    class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    ID
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                    </svg>
                                </a>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => request('sort') === 'name_asc' ? 'name_desc' : 'name_asc'])); ?>"
                                    class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    İsim
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                    </svg>
                                </a>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                E‑posta</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                Rol</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                Durum</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => request('sort') === 'date_asc' ? 'date_desc' : 'date_asc'])); ?>"
                                    class="flex items-center gap-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    Kayıt Tarihi
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                    </svg>
                                </a>
                            </th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-colors duration-150">
                                
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold text-sm shadow-lg">
                                        <?php echo e($user->id); ?>

                                    </span>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <a href="<?php echo e(route('admin.kullanicilar.edit', $user)); ?>"
                                        class="flex items-center gap-3 group">
                                        
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-white dark:ring-gray-700">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            <?php echo e($user->name); ?>

                                        </span>
                                    </a>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <a href="mailto:<?php echo e($user->email); ?>"
                                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <?php echo e($user->email); ?>

                                    </a>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $roleMapping = [
                                            'superadmin' => 'Super Admin',
                                            'admin' => 'Admin',
                                            'danisman' => 'Danışman',
                                            'editor' => 'Editor',
                                            'musteri' => 'Müşteri',
                                        ];
                                        $roleName = $user->roles->pluck('name')->first() ?? null;
                                        $primaryRole = $roleName
                                            ? $roleMapping[$roleName] ?? ucfirst($roleName)
                                            : 'Rol Yok';

                                        $roleColors = [
                                            'Super Admin' =>
                                                'bg-gradient-to-br from-purple-100 to-fuchsia-100 dark:from-purple-900/30 dark:to-fuchsia-900/30 text-purple-800 dark:text-purple-400 border border-purple-300 dark:border-purple-700',
                                            'Admin' =>
                                                'bg-gradient-to-br from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30 text-red-800 dark:text-red-400 border border-red-300 dark:border-red-700',
                                            'Danışman' =>
                                                'bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 text-blue-800 dark:text-blue-400 border border-blue-300 dark:border-blue-700',
                                            'Editor' =>
                                                'bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-800 dark:text-green-400 border border-green-300 dark:border-green-700',
                                            'Müşteri' =>
                                                'bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/30 dark:to-orange-900/30 text-amber-800 dark:text-amber-400 border border-amber-300 dark:border-amber-700',
                                            'Kullanıcı' =>
                                                'bg-gradient-to-br from-gray-100 to-slate-100 dark:from-gray-900/30 dark:to-slate-900/30 text-gray-800 dark:text-gray-400 border border-gray-300 dark:border-gray-700',
                                            'Rol Yok' =>
                                                'bg-gradient-to-br from-gray-100 to-slate-100 dark:from-gray-900/30 dark:to-slate-900/30 text-gray-600 dark:text-gray-500 border border-gray-300 dark:border-gray-700 border-dashed', // ✅ Context7: Rol Yok için özel stil
                                        ];
                                        $roleColor = $roleColors[$primaryRole] ?? $roleColors['Kullanıcı'];
                                    ?>
                                    <div class="flex items-center justify-center gap-2">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full <?php echo e($roleColor); ?>">
                                            <?php echo e($primaryRole); ?>

                                        </span>
                                        <?php if(!$roleName): ?>
                                            <a href="<?php echo e(route('admin.kullanicilar.edit', $user)); ?>"
                                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all duration-200"
                                                title="Rol Ata">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-full
                                             <?php echo e($user->status
                                                 ? 'bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-800 dark:text-green-400 border border-green-300 dark:border-green-700'
                                                 : 'bg-gradient-to-br from-red-100 to-rose-100 dark:from-red-900/30 dark:to-rose-900/30 text-red-800 dark:text-red-400 border border-red-300 dark:border-red-700'); ?>">
                                        <span
                                            class="w-2 h-2 rounded-full <?php echo e($user->status ? 'bg-green-500 animate-pulse' : 'bg-red-500'); ?>"></span>
                                        <?php echo e($user->status ? 'Aktif' : 'Pasif'); ?>

                                    </span>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($user->created_at->format('d.m.Y')); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($user->created_at->format('H:i')); ?>

                                    </div>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?php echo e(route('admin.kullanicilar.edit', $user)); ?>"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                              bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                                              hover:bg-blue-200 dark:hover:bg-blue-900/50
                                              transition-all duration-200 group"
                                            title="Düzenle">
                                            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="<?php echo e(route('admin.kullanicilar.destroy', $user)); ?>"
                                            class="inline-block"
                                            onsubmit="return confirm('⚠️ Bu kullanıcıyı silmek istediğinizden emin misiniz?\n\nKullanıcı: <?php echo e($user->name); ?>\nE-posta: <?php echo e($user->email); ?>')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                                       bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400
                                                       hover:bg-red-200 dark:hover:bg-red-900/50
                                                       transition-all duration-200 group"
                                                title="Sil">
                                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Henüz
                                            kullanıcı bulunmuyor</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">İlk kullanıcıyı eklemek
                                            için "Yeni Kullanıcı" butonuna tıklayın</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($users->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <?php echo e($users->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Quick Filter Status (Vanilla JS)
        function quickFilterStatus(value) {
            const url = new URL(window.location.href);
            if (value === '') url.searchParams.delete('status');
            else url.searchParams.set('status', value);
            window.location.href = url.toString();
        }

        // Export to CSV (Vanilla JS)
        function exportUsersCsv() {
            const rows = Array.from(document.querySelectorAll('table tbody tr'));
            let csv = 'ID,İsim,E-posta,Rol,Durum,Kayıt Tarihi\n';

            rows.forEach(r => {
                const cells = r.querySelectorAll('td');
                if (cells.length > 1) {
                    const id = cells[0].innerText.trim();
                    const name = cells[1].innerText.trim();
                    const email = cells[2].innerText.trim();
                    const role = cells[3].innerText.trim();
                    const status = cells[4].innerText.trim().replace(/\n/g, ' ').replace(/\s+/g, ' ');
                    const date = cells[5].innerText.trim().replace(/\n/g, ' ');
                    csv += `"${id}","${name}","${email}","${role}","${status}","${date}"\n`;
                }
            });

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'kullanicilar_' + (new Date().toISOString().split('T')[0]) + '.csv';
            document.body.appendChild(a);
            a.click();
            a.remove();

            window.toast?.success('✅ Kullanıcılar CSV olarak indirildi');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.neo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/macbookpro/Projects/yalihanai /resources/views/admin/users/index.blade.php ENDPATH**/ ?>