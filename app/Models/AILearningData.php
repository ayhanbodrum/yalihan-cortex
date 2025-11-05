<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AI Learning Data Modeli
 * 
 * AI öğrenme verilerini saklar
 * Context7 Compliant
 */
class AILearningData extends Model
{
    use HasFactory;

    protected $table = 'ai_learning_data';

    // Fillable'lar insert kullanımına göre belirlenecek
    protected $guarded = [];

    public $timestamps = true;
}
