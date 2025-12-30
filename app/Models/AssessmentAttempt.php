<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'questions',
        'answers',
        'started_at',
        'submitted_at',
        'score',
        'percentage',
        'status',
        'violations',
    ];

    protected $casts = [
        'questions' => 'array', 
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

use App\Models\AssessmentAttempt;

AssessmentAttempt::create([
    'user_id' => 1, // Your user ID
    'questions' => [1,2,3], // Sample question IDs
    'started_at' => now()->subMinutes(30),
    'submitted_at' => now(),
    'score' => 3, // e.g., 3 correct
    'percentage' => 100, // >=70
    'status' => 'completed',
]);
