<?php
<?php

namespace App\Services;

use App\Models\Question;

class QuestionImportService
{
    public function import($file)
    {
        $data = array_map('str_getcsv', file($file->getRealPath()));
        array_shift($data); // Remove header

        foreach ($data as $row) {
            if (count($row) >= 6) {
                Question::create([
                    'question' => $row[0],
                    'option_a' => $row[1],
                    'option_b' => $row[2],
                    'option_c' => $row[3],
                    'option_d' => $row[4],
                    'correct_option' => $row[5],
                    'is_active' => true,
                ]);
            }
        }
    }
}