<?php

namespace App\Services;

use App\Models\Skill;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class ResumeParserService
{
    public function parse($file): array
    {
        
        $path = $file->store('resumes', 'local');

        $fullPath = Storage::disk('local')->path($path);

        
        if (!Storage::disk('local')->exists($path)) {
            throw new \Exception('Stored resume file not found at: ' . $fullPath);
        }

        
        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);
        $text = strtolower($pdf->getText());

        
        $dbSkills = Skill::where('is_active', true)
            ->pluck('name')
            ->map(fn ($s) => strtolower($s))
            ->toArray();

        
        $matchedSkills = [];
        foreach ($dbSkills as $skill) {
            if (str_contains($text, $skill)) {
                $matchedSkills[] = $skill;
            }
        }

       
        $percentage = count($dbSkills)
            ? (count($matchedSkills) / count($dbSkills)) * 100
            : 0;

        return [
            'percentage' => round($percentage),
            'eligible' => $percentage >= 30,
            'matched_skills' => array_values(array_unique($matchedSkills)),
            'text' => $text,
            'path' => $path,
        ];
    }
}
