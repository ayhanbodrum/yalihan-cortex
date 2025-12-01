<?php

namespace App\Modules\Crm\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Kisi|null $kisi
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KisiNot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KisiNot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KisiNot query()
 *
 * @mixin \Eloquent
 */
class KisiNot extends Model
{
    use HasFactory;

    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'kisi_notlar';

    /**
     * Toplu atanabilir alanlar
     *
     * @var array
     */
    protected $fillable = [
        'kisi_id', 'aciklama', 'user_id',
    ];

    /**
     * İlişkili kişi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Notu yazan kullanıcı
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
