<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ilan_id',
        'path',
        'thumbnail',
        'category',
        'is_featured',
        'order',
        'views',
        'size',
        'mime_type',
        'width',
        'height',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'order' => 'integer',
        'views' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Fotoğraf silindiğinde dosyaları da sil
        static::deleting(function ($photo) {
            if ($photo->isForceDeleting()) {
                // Hard delete - dosyaları sil
                Storage::delete($photo->path);
                if ($photo->thumbnail) {
                    Storage::delete($photo->thumbnail);
                }
            }
        });
    }

    /**
     * Relationship: İlan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    /**
     * Scope: Öne çıkan fotoğraflar
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Sıralı fotoğraflar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Accessor: Fotoğraf URL'i
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Accessor: Thumbnail URL'i
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail 
            ? Storage::url($this->thumbnail) 
            : $this->url;
    }

    /**
     * Accessor: Dosya boyutu (human readable)
     */
    public function getFormattedSizeAttribute()
    {
        if (!$this->size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $this->size > 0 ? floor(log($this->size, 1024)) : 0;
        
        return number_format($this->size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Accessor: Görüntülenme sayısı (formatted)
     */
    public function getFormattedViewsAttribute()
    {
        if ($this->views >= 1000000) {
            return number_format($this->views / 1000000, 1) . 'M';
        } elseif ($this->views >= 1000) {
            return number_format($this->views / 1000, 1) . 'K';
        }
        
        return (string) $this->views;
    }

    /**
     * Helper: Görüntülenme sayısını artır
     */
    public function incrementViews()
    {
        $this->increment('views');
        return $this;
    }

    /**
     * Helper: Öne çıkarılmış mı?
     */
    public function isFeatured()
    {
        return $this->is_featured;
    }

    /**
     * Helper: Öne çıkar
     */
    public function setAsFeatured()
    {
        // Önce bu ilanın diğer fotoğraflarını featured'dan çıkar
        static::where('ilan_id', $this->ilan_id)
            ->where('id', '!=', $this->id)
            ->update(['is_featured' => false]);

        $this->update(['is_featured' => true]);
        return $this;
    }

    /**
     * Helper: Featured'dan çıkar
     */
    public function unsetAsFeatured()
    {
        $this->update(['is_featured' => false]);
        return $this;
    }

    /**
     * Get image URL (alias for url attribute)
     * Used in views for consistency
     */
    public function getImageUrl()
    {
        return $this->url;
    }

    /**
     * Get thumbnail URL (alias for thumbnail_url attribute)
     * Used in views for consistency
     */
    public function getThumbnailImageUrl()
    {
        return $this->thumbnail_url;
    }
}
