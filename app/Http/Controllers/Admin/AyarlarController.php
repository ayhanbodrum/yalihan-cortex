<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Validation\Rule;

/**
 * Ayarlar Controller - System Settings Management
 * Context7: Enhanced with validation, templates, and helper methods
 */
class AyarlarController extends AdminController
{
    /**
     * Available setting groups
     */
    const GROUPS = [
        'general' => 'Genel Ayarlar',
        'contact' => 'İletişim Bilgileri',
        'email' => 'Email Ayarları',
        'social' => 'Sosyal Medya',
        'seo' => 'SEO Ayarları',
        'currency' => 'Para Birimi',
        'ai' => 'AI Ayarları',
        'system' => 'Sistem Ayarları',
        'security' => 'Güvenlik',
        'performance' => 'Performans',
        'qrcode' => 'QR Kod Ayarları',
        'navigation' => 'Navigasyon Ayarları',
    ];

    public function index(Request $request)
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        $groups = self::GROUPS;

        // Get all settings as key-value array for easy access in view
        $settingsArray = Setting::all()->pluck('value', 'key')->toArray();

        // Try to use settings/index.blade.php first, fallback to ayarlar/index.blade.php
        if (view()->exists('admin.settings.index')) {
            return view('admin.settings.index', compact('settings', 'groups', 'settingsArray'))
                ->with('settings', $settingsArray);
        }

        return view('admin.ayarlar.index', compact('settings', 'groups', 'settingsArray'));
    }

    public function create()
    {
        $groups = self::GROUPS;
        $templates = $this->getTemplates();

        return view('admin.ayarlar.create', compact('groups', 'templates'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'key' => [
                'required',
                'string',
                'regex:/^[a-z][a-z0-9_]*$/', // snake_case validation
                'unique:settings,key',
            ],
            'value' => 'nullable|string',
            'type' => ['required', Rule::in(['string', 'integer', 'boolean', 'json'])],
            'group' => ['required', 'string', Rule::in(array_keys(self::GROUPS))],
            'description' => 'nullable|string|max:500',
        ], [
            'key.regex' => 'Ayar anahtarı sadece küçük harf, rakam ve alt çizgi içerebilir (snake_case)',
            'group.in' => 'Geçersiz grup seçildi',
        ]);

        // Additional validation for JSON type
        if ($validated['type'] === 'json' && !empty($validated['value'])) {
            json_decode($validated['value']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['value' => 'Geçersiz JSON formatı'])->withInput();
            }
        }

        // Create setting
        $setting = Setting::create($validated);

        // Clear cache
        Setting::clearCache();

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar başarıyla oluşturuldu!');
    }

    /**
     * Get predefined templates
     * Context7: Quick templates for common settings
     */
    private function getTemplates()
    {
        return [
            // General
            'site_name' => [
                'key' => 'site_name',
                'value' => 'Yalıhan Emlak',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Sitenin ana başlığı',
                'icon' => '🏠',
                'category' => 'general',
            ],
            'site_description' => [
                'key' => 'site_description',
                'value' => 'Bodrum\'da güvenilir emlak danışmanlığı',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Site açıklaması (meta description)',
                'icon' => '📝',
                'category' => 'general',
            ],
            'default_language' => [
                'key' => 'default_language',
                'value' => 'tr',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Varsayılan dil (tr, en, de, ru)',
                'icon' => '🌍',
                'category' => 'general',
            ],

            // System
            'maintenance_mode' => [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Site bakım modunda mı?',
                'icon' => '🔧',
                'category' => 'system',
            ],
            'max_upload_size' => [
                'key' => 'max_upload_size',
                'value' => '10',
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Maksimum dosya yükleme boyutu (MB)',
                'icon' => '📁',
                'category' => 'system',
            ],
            'session_lifetime' => [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Oturum süresi (dakika)',
                'icon' => '⏰',
                'category' => 'system',
            ],

            // Social
            'social_media' => [
                'key' => 'social_media',
                'value' => json_encode([
                    'facebook' => 'https://facebook.com/yalihanemlak',
                    'instagram' => 'https://instagram.com/yalihanemlak',
                    'twitter' => 'https://twitter.com/yalihanemlak',
                    'linkedin' => 'https://linkedin.com/company/yalihanemlak',
                ], JSON_PRETTY_PRINT),
                'type' => 'json',
                'group' => 'social',
                'description' => 'Sosyal medya hesap linkleri',
                'icon' => '📱',
                'category' => 'social',
            ],

            // Email
            'smtp_host' => [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP sunucu adresi',
                'icon' => '📧',
                'category' => 'email',
            ],

            // AI
            'ai_provider' => [
                'key' => 'ai_provider',
                'value' => 'ollama',
                'type' => 'string',
                'group' => 'ai',
                'description' => 'Varsayılan AI provider (ollama, openai, gemini, claude)',
                'icon' => '🤖',
                'category' => 'ai',
            ],

            // Currency
            'default_currency' => [
                'key' => 'default_currency',
                'value' => 'TRY',
                'type' => 'string',
                'group' => 'currency',
                'description' => 'Varsayılan para birimi (TRY, USD, EUR)',
                'icon' => '💰',
                'category' => 'currency',
            ],

            // Security
            'force_https' => [
                'key' => 'force_https',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'HTTPS zorunluluğu',
                'icon' => '🔒',
                'category' => 'security',
            ],

            // SEO
            'google_analytics_id' => [
                'key' => 'google_analytics_id',
                'value' => 'G-XXXXXXXXXX',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Google Analytics ID',
                'icon' => '📊',
                'category' => 'seo',
            ],
        ];
    }

    public function show($id)
    {
        $setting = Setting::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($setting);
        }

        return view('admin.ayarlar.show', compact('setting'));
    }

    public function edit($id)
    {
        $ayar = Setting::findOrFail($id);
        return view('admin.ayarlar.edit', compact('ayar'));
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $validated = $request->validate([
            'value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar güncellendi!');
    }

    public function destroy($id)
    {
        Setting::findOrFail($id)->delete();
        Setting::clearCache();

        return redirect()->route('admin.ayarlar.index')
            ->with('success', 'Ayar silindi!');
    }

    /**
     * Bulk update settings (for settings form)
     * Context7: Handle form submission from settings page
     */
    public function bulkUpdate(Request $request)
    {
        try {
            $settingsToUpdate = $request->except(['_token', '_method']);

            foreach ($settingsToUpdate as $key => $value) {
                // Handle checkboxes (they don't send value if unchecked)
                if (in_array($key, ['qrcode_enabled', 'qrcode_show_on_cards', 'qrcode_show_on_detail',
                                    'navigation_enabled', 'navigation_show_similar',
                                    'email_notifications', 'sms_notifications',
                                    'ai_auto_description', 'ai_smart_tags',
                                    'user_registration', 'password_strength'])) {
                    $value = $request->has($key) ? 'true' : 'false';
                }

                // Determine type based on key
                $type = 'string';
                if (in_array($key, ['qrcode_default_size', 'navigation_similar_limit', 'max_upload_size', 'session_lifetime'])) {
                    $type = 'integer';
                } elseif (in_array($key, ['qrcode_enabled', 'qrcode_show_on_cards', 'qrcode_show_on_detail',
                                          'navigation_enabled', 'navigation_show_similar',
                                          'email_notifications', 'sms_notifications',
                                          'ai_auto_description', 'ai_smart_tags',
                                          'user_registration', 'password_strength', 'maintenance_mode'])) {
                    $type = 'boolean';
                }

                // Determine group based on key prefix
                $group = 'general';
                if (str_starts_with($key, 'qrcode_')) {
                    $group = 'qrcode';
                } elseif (str_starts_with($key, 'navigation_')) {
                    $group = 'navigation';
                } elseif (str_starts_with($key, 'ai_') || str_starts_with($key, 'openai_') || str_starts_with($key, 'deepseek_') || str_starts_with($key, 'google_') || str_starts_with($key, 'anthropic_') || str_starts_with($key, 'ollama_')) {
                    $group = 'ai';
                } elseif (str_starts_with($key, 'email_') || str_starts_with($key, 'smtp_')) {
                    $group = 'email';
                } elseif (str_starts_with($key, 'social_')) {
                    $group = 'social';
                } elseif (str_starts_with($key, 'seo_') || str_starts_with($key, 'google_analytics')) {
                    $group = 'seo';
                } elseif (str_starts_with($key, 'currency_') || str_starts_with($key, 'default_currency') || str_starts_with($key, 'price_')) {
                    $group = 'currency';
                } elseif (str_starts_with($key, 'user_') || str_starts_with($key, 'password_')) {
                    $group = 'system';
                } elseif (str_starts_with($key, 'maintenance_') || str_starts_with($key, 'max_upload_') || str_starts_with($key, 'session_')) {
                    $group = 'system';
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'type' => $type,
                        'group' => $group,
                    ]
                );
            }

            // Clear cache
            Setting::clearCache();

            // Clear QR Code and Navigation caches (only if cache driver supports tagging)
            try {
                $cacheStore = \Illuminate\Support\Facades\Cache::getStore();
                if (method_exists($cacheStore, 'tags')) {
                    \Illuminate\Support\Facades\Cache::tags(['qrcode', 'navigation'])->flush();
                } else {
                    // Fallback: Clear all cache if tagging is not supported
                    \Illuminate\Support\Facades\Cache::flush();
                }
            } catch (\Exception $e) {
                // Cache tagging not supported, use regular flush
                \Illuminate\Support\Facades\Cache::flush();
            }

            return redirect()->route('admin.ayarlar.index')
                ->with('success', 'Ayarlar başarıyla güncellendi!');
        } catch (\Exception $e) {
            \App\Services\Logging\LogService::error('Settings bulk update failed', [], $e);

            return redirect()->back()
                ->with('error', 'Ayarlar güncellenirken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get template groups for bulk creation
     * Context7: Grouped templates for related settings
     */
    public function getTemplateGroups()
    {
        return [
            'email_smtp' => [
                'name' => 'Email SMTP Ayarları',
                'icon' => '📧',
                'description' => 'Email gönderimi için gerekli tüm SMTP ayarları',
                'settings' => [
                    [
                        'key' => 'smtp_host',
                        'value' => 'smtp.gmail.com',
                        'type' => 'string',
                        'group' => 'email',
                        'description' => 'SMTP sunucu adresi',
                    ],
                    [
                        'key' => 'smtp_port',
                        'value' => '587',
                        'type' => 'integer',
                        'group' => 'email',
                        'description' => 'SMTP port numarası (587 TLS, 465 SSL)',
                    ],
                    [
                        'key' => 'smtp_username',
                        'value' => '',
                        'type' => 'string',
                        'group' => 'email',
                        'description' => 'SMTP kullanıcı adı (email)',
                    ],
                    [
                        'key' => 'smtp_password',
                        'value' => '',
                        'type' => 'string',
                        'group' => 'email',
                        'description' => 'SMTP şifresi',
                    ],
                    [
                        'key' => 'smtp_encryption',
                        'value' => 'tls',
                        'type' => 'string',
                        'group' => 'email',
                        'description' => 'Şifreleme tipi (tls veya ssl)',
                    ],
                ],
            ],

            'ai_complete' => [
                'name' => 'AI Provider Tam Kurulum',
                'icon' => '🤖',
                'description' => 'AI sistemini kullanmaya hazır hale getir',
                'settings' => [
                    [
                        'key' => 'ai_enabled',
                        'value' => 'true',
                        'type' => 'boolean',
                        'group' => 'ai',
                        'description' => 'AI özellikleri aktif mi?',
                    ],
                    [
                        'key' => 'ai_provider',
                        'value' => 'ollama',
                        'type' => 'string',
                        'group' => 'ai',
                        'description' => 'Varsayılan AI provider',
                    ],
                    [
                        'key' => 'ollama_url',
                        'value' => 'http://localhost:11434',
                        'type' => 'string',
                        'group' => 'ai',
                        'description' => 'Ollama sunucu URL',
                    ],
                    [
                        'key' => 'ollama_model',
                        'value' => 'gemma2:2b',
                        'type' => 'string',
                        'group' => 'ai',
                        'description' => 'Ollama model adı',
                    ],
                ],
            ],

            'security_basic' => [
                'name' => 'Temel Güvenlik Ayarları',
                'icon' => '🔒',
                'description' => 'Minimum güvenlik gereksinimleri',
                'settings' => [
                    [
                        'key' => 'force_https',
                        'value' => 'true',
                        'type' => 'boolean',
                        'group' => 'security',
                        'description' => 'HTTPS zorunluluğu',
                    ],
                    [
                        'key' => 'csrf_protection',
                        'value' => 'true',
                        'type' => 'boolean',
                        'group' => 'security',
                        'description' => 'CSRF koruması aktif',
                    ],
                    [
                        'key' => 'max_login_attempts',
                        'value' => '5',
                        'type' => 'integer',
                        'group' => 'security',
                        'description' => 'Maksimum giriş denemesi',
                    ],
                    [
                        'key' => 'login_lockout_time',
                        'value' => '15',
                        'type' => 'integer',
                        'group' => 'security',
                        'description' => 'Giriş engelleme süresi (dakika)',
                    ],
                ],
            ],
        ];
    }

    /**
     * Bulk create settings
     * Context7: Create multiple related settings at once
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|unique:settings,key|regex:/^[a-z][a-z0-9_]*$/',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'required|in:string,integer,boolean,json',
            'settings.*.group' => 'required|string',
            'settings.*.description' => 'nullable|string',
        ]);

        $created = [];
        foreach ($validated['settings'] as $settingData) {
            $created[] = Setting::create($settingData);
        }

        Setting::clearCache();

        return response()->json([
            'success' => true,
            'message' => count($created) . ' ayar oluşturuldu!',
            'created' => $created,
        ]);
    }

    /**
     * Clear all caches
     */
    public function clearCaches()
    {
        Setting::clearCache();

        return redirect()->back()
            ->with('success', 'Tüm ayar cache\'leri temizlendi!');
    }
}
