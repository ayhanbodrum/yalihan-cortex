<?php

namespace App\Models;

use App\Modules\Auth\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int $role_id
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property string|null $profile_photo_path
 * @property string|null $phone_number
 * @property string|null $title
 * @property string|null $bio
 * @property string|null $office_address
 * @property string|null $whatsapp_number
 * @property string|null $instagram_profile
 * @property string|null $linkedin_profile
 * @property string|null $website
 * @property int $is_verified
 * @property string|null $expertise_summary
 * @property string|null $certificates_info
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ilan> $ilanlar
 * @property-read int|null $ilanlar_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kisi> $musteriler
 * @property-read int|null $musteriler_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Talep> $talepler
 * @property-read int|null $talepler_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCertificatesInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereExpertiseSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInstagramProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkedinProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWhatsappNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use \App\Models\Traits\EncryptsAttributes, HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'last_login_at',
        'last_activity_at',
        'profile_photo_path',
        'phone_number',
        'title',
        'bio',
        'office_address',
        'whatsapp_number',
        'instagram_profile',
        'linkedin_profile',
        'website',
        'facebook_profile',
        'twitter_profile',
        'telegram_username',
        'telegram_chat_id',
        'telegram_id',
        'telegram_pairing_code',
        'telegram_paired_at',
        'position',
        // 'department', // ❌ KALDIRILDI - Artık kullanılmıyor
        'employee_id',
        'hire_date',
        'termination_date',
        'emergency_contact',
        'notes',
        'expertise_summary',
        'certificates_info',
        'lisans_no',
        'uzmanlik_alani',
        'deneyim_yili',
        'uzmanlik_alanlari',
        'bolge_uzmanliklari',
        'diller',
        'calisma_saatleri',
        'iletisim_tercihleri',
        'office_phone',
        'youtube_channel',
        'tiktok_profile',
        'is_verified',
        'status_text',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'is_verified' => 'boolean',
        'uzmanlik_alanlari' => 'array',
        'bolge_uzmanliklari' => 'array',
        'diller' => 'array',
        'calisma_saatleri' => 'array',
        'iletisim_tercihleri' => 'array',
        'deneyim_yili' => 'integer',
    ];

    protected $encrypted = [
        'tc_kimlik',
        'iban',
    ];

    /**
     * Kullanıcının rolüne erişim için ilişki
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Not: danisman() relationship kaldırıldı.
     * Artık users tablosunda Spatie Permission role sistemi kullanılıyor.
     * Danışman kontrolü için: $user->hasRole('danisman') kullanın.
     */

    /**
     * Kullanıcının ilanlarına erişim için ilişki (danışman ise)
     */
    public function ilanlar()
    {
        return $this->hasMany(Ilan::class, 'danisman_id');
    }

    /**
     * Kullanıcının sorumlu olduğu müşterilere erişim için ilişki (danışman ise)
     */
    public function musteriler()
    {
        return $this->hasMany(Kisi::class, 'danisman_id');
    }

    /**
     * Kullanıcının sorumlu olduğu taleplere erişim için ilişki (danışman ise)
     */
    public function talepler()
    {
        return $this->hasMany(Talep::class, 'danisman_id');
    }

    /**
     * Danışman yorumları (danışman ise)
     * Context7 Compliance: danisman_yorumlari table
     */
    public function danismanYorumlari()
    {
        return $this->hasMany(DanismanYorumu::class, 'danisman_id');
    }

    /**
     * Onaylanmış danışman yorumları
     */
    public function onayliDanismanYorumlari()
    {
        return $this->hasMany(DanismanYorumu::class, 'danisman_id')->where('status', 'approved');
    }

    /**
     * Kullanıcının rollerini döndüren ilişki
     * Note: This is kept for backward compatibility,
     * Spatie Permission uses its own relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        // Use Spatie's relationship
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        );
    }

    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol eder
     * Bu metod, hem tekil role ilişkisini hem de çoklu roles ilişkisini kontrol eder
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        // Önce tekil role ilişkisini kontrol edelim
        if ($this->role) {
            if (is_array($roles)) {
                if (in_array($this->role->name, $roles)) {
                    return true;
                }
            } elseif (is_string($roles) && $this->role->name === $roles) {
                return true;
            }
        }

        // Eğer tekil role ilişkisinde bulunamadıysa ve roles ilişkisi varsa onu kontrol edelim
        if (method_exists($this, 'roles') && $this->roles && $this->roles->count() > 0) {
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if ($this->roles->contains('name', $role)) {
                        return true;
                    }
                }

                return false;
            }

            return $this->roles->contains('name', $roles);
        }

        return false;
    }

    /**
     * Kullanıcının süper admin olup olmadığını kontrol eder
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Kullanıcının danışman olup olmadığını kontrol eder
     *
     * @return bool
     */
    public function isDanisman()
    {
        return $this->hasRole('danisman');
    }

    /**
     * Kullanıcının içerik editörü olup olmadığını kontrol eder
     *
     * @return bool
     */
    public function isEditor()
    {
        return $this->hasRole('editor');
    }

    /**
     * Kullanıcının belirli rol(ler)den herhangi birine sahip olup olmadığını kontrol eder
     *
     * @param  array|string  $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    /**
     * Kullanıcının belirtilen tüm rollere sahip olup olmadığını kontrol eder
     *
     * @param  array|string  $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        $hasAllRoles = true;

        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                $hasAllRoles = false;
                break;
            }
        }

        return $hasAllRoles;
    }

    /**
     * Kullanıcının admin olup olmadığını kontrol eder
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Kullanıcı status mi kontrol eder
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Son giriş zamanını günceller
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();

        return $this;
    }

    /**
     * Son aktivite zamanını günceller
     */
    public function updateLastActivity()
    {
        $this->last_activity_at = now();
        $this->saveQuietly(); // Sessiz kayıt - updated_at değişmez

        return $this;
    }

    /**
     * Kullanıcının belirli bir süre içinde status olup olmadığını kontrol eder
     *
     * @param  int  $minutes  Son kaç dakika içinde status olduğu kontrol edilecek
     * @return bool
     */
    public function isActiveWithin($minutes = 5)
    {
        if (! $this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->diffInMinutes(now()) <= $minutes;
    }

    /**
     * Kullanıcının şu anda çevrimiçi olup olmadığını kontrol eder
     *
     * @return bool
     */
    public function isOnline()
    {
        return Cache::has('user-online-'.$this->id);
    }

    /**
     * Kullanıcının rolüne göre dashboard URL'ini döndürür
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        if ($this->userHasRole(['superadmin', 'admin'])) {
            return route('admin.dashboard.index');
        }

        return route('home');
    }

    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol eder
     *
     * @param  string|array  $roleName
     * @return bool
     */
    public function userHasRole($roleName)
    {
        if (! $this->role) {
            return false;
        }

        if (is_array($roleName)) {
            return in_array($this->role->name, $roleName);
        }

        return $this->role->name === $roleName;
    }

    /**
     * Kullanıcının bildirimleri
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Kullanıcının okunmamış bildirimleri
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Kullanıcının okunmuş bildirimleri
     */
    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    /**
     * Kullanıcının gönderdiği bildirimler
     */
    public function sentNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'sender_id');
    }

    /**
     * Kullanıcının takım üyesi kaydına erişim için ilişki
     */
    public function takimUyesi()
    {
        return $this->hasOne(\App\Modules\TakimYonetimi\Models\TakimUyesi::class, 'user_id');
    }
}
