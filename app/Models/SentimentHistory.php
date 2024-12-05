<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SentimentHistory extends Model
{
    use HasFactory, SoftDeletes;

    // The table associated with the model (if not following Laravel's naming convention)
    protected $table = 'sentiment_histories';

    // Fillable fields for mass assignment
    protected $fillable = ['user_id', 'text', 'analysis_result', 'emotion_scores', 'highlighted_text'];

    // Casts the emotion_scores to an array (since it's stored as JSON)
    protected $casts = [
        'analysis_result' => 'array',
        'emotion_scores' => 'array', // Automatically cast to an array when retrieved
    ];

    /**
     * Define the relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

