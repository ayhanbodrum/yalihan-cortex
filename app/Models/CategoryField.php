<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    protected $table = 'category_fields';

    protected $fillable = [
        'category_id',
        'name',
        'label',
        'type',
        'required',
        'options',
        'placeholder',
        'validation_rules',
        'order',
        'status',
    ];

    protected $casts = [
        'required' => 'boolean',
        'status' => 'boolean',
        'options' => 'array',
        'validation_rules' => 'array',
    ];

    /**
     * İlişki: Bu alanın ait olduğu kategori
     */
    public function category()
    {
        return $this->belongsTo(IlanKategori::class, 'category_id');
    }

    /**
     * Scope: Sadece status alanlar
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Sıraya göre
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
