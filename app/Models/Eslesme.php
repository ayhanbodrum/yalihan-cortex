<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eslesme extends Model
{
    use HasFactory;

    protected $table = 'eslesmeler';
    protected $fillable = [
        'kisi_id','ilan_id','talep_id','danisman_id','skor','one_cikan','notlar','status','eslesme_tarihi','son_guncelleme'
    ];

    public function kisi() { return $this->belongsTo(Kisi::class, 'kisi_id'); }
    public function ilan() { return $this->belongsTo(Ilan::class, 'ilan_id'); }
    public function talep() { return $this->belongsTo(Talep::class, 'talep_id'); }
    public function danisman() { return $this->belongsTo(User::class, 'danisman_id'); }
    public function etiketler() { return $this->belongsToMany(Etiket::class, 'eslesme_etiket', 'eslesme_id', 'etiket_id'); }
}
