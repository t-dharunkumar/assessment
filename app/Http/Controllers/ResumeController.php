<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Services\ResumeParserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResumeController extends Controller
{
    public function show()
    {
        return view('resume.upload');
    }

    public function store(Request $request, ResumeParserService $parser)
    {
        try {
            $request->validate([
                'resume' => 'required|mimes:pdf|max:2048',
            ]);

            $data = $parser->parse($request->file('resume'));

            Resume::create([
                'user_id' => auth()->id(),
                'file_path' => $data['path'],
                'extracted_text' => $data['text'],
                'is_valid' => $data['eligible'],
            ]);

            if ($data['eligible']) {
                session(['has_valid_resume' => true]);
            }

            return response()->json([
                'percentage' => $data['percentage'],
                'eligible' => $data['eligible'],
                'matched_skills' => $data['matched_skills'],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors()['resume'][0] ?? 'Invalid file',
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Resume parse failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Something went wrong while processing resume.',
            ], 500);
        }
    }
}
