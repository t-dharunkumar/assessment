<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentAnswer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the answers column if not already added
        Schema::table('assessment_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_attempts', 'answers')) {
                $table->json('answers')->nullable()->after('questions');
            }
        });

        // Migrate existing data
        AssessmentAttempt::chunk(100, function ($attempts) {
            foreach ($attempts as $attempt) {
                $answers = AssessmentAnswer::where('assessment_attempt_id', $attempt->id)->get()->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'selected_option' => $answer->selected_option,
                        'is_correct' => $answer->is_correct,
                    ];
                })->toArray();

                $attempt->update(['answers' => $answers]);
            }
        });

        // Drop the assessment_answers table
        Schema::dropIfExists('assessment_answers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the assessment_answers table
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->enum('selected_option', ['a','b','c','d'])->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // Migrate data back
        AssessmentAttempt::whereNotNull('answers')->chunk(100, function ($attempts) {
            foreach ($attempts as $attempt) {
                foreach ($attempt->answers as $answerData) {
                    AssessmentAnswer::create([
                        'assessment_attempt_id' => $attempt->id,
                        'question_id' => $answerData['question_id'],
                        'selected_option' => $answerData['selected_option'],
                        'is_correct' => $answerData['is_correct'],
                    ]);
                }
            }
        });

        // Drop the answers column
        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->dropColumn('answers');
        });
    }
};