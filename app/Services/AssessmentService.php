<?php

namespace App\Services;

use App\Models\AssessmentAttempt;

class AssessmentService
{
    public function evaluate(AssessmentAttempt $attempt)
    {
        $answers = $attempt->answers ?? [];
        $total = count($answers);
        $correct = collect($answers)->where('is_correct', true)->count();

        $percentage = $total > 0 ? round(($correct / $total) * 100) : 0;

        $attempt->update([
            'score' => $correct,
            'percentage' => $percentage,
        ]);

        return $percentage;
    }
}
