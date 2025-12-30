<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assessments()
    {
        return $this->belongsToMany(AssessmentAttempt::class, 'assessment_attempts', 'questions');
    }
}

