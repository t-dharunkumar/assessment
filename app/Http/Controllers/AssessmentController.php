<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AssessmentController extends Controller
{
    public function index()
    {
        // Admin dashboard stats - only completed assessments by regular users
        $completedAttempts = AssessmentAttempt::where('status', 'completed')
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin');
            });

        $totalTests = $completedAttempts->count();
        $totalUsers = User::whereHas('assessmentAttempts', function ($q) {
            $q->where('status', 'completed')->whereHas('user', function ($sq) {
                $sq->where('role', '!=', 'admin');
            });
        })->count();
        $averageScore = $completedAttempts->avg('percentage') ?? 0;
        $averageViolations = $completedAttempts->avg('violations') ?? 0;
        $autoSubmitted = $completedAttempts->where('violations', '>=', 5)->count();
        $manualSubmitted = $totalTests - $autoSubmitted;

        return view('admin.dashboard', compact(
            'totalTests',
            'totalUsers',
            'averageScore',
            'averageViolations',
            'autoSubmitted',
            'manualSubmitted'
        ));
    }

    public function reports(Request $request)
    {
        $query = AssessmentAttempt::with('user')
            ->where('status', 'completed')
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin');
            });

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('status')) {
            if ($request->status === 'pass') {
                $query->where('percentage', '>=', 30);
            } elseif ($request->status === 'fail') {
                $query->where('percentage', '<', 30);
            }
        }

        if ($request->filled('violations')) {
            $query->where('violations', '>=', $request->violations);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $attempts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports', compact('attempts'));
    }

    public function reportDetail(AssessmentAttempt $attempt)
    {
        $questions = Question::whereIn('id', $attempt->questions)->get()->keyBy('id');
        $answers = collect($attempt->answers ?? [])->keyBy('question_id');

        return view('admin.report_detail', compact('attempt', 'questions', 'answers'));
    }

    public function uploadQuestions(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Remove header
        array_shift($data);

        $errors = [];
        $validQuestions = [];

        foreach ($data as $index => $row) {
            if (count($row) < 6) {
                $errors[] = "Row " . ($index + 2) . ": Insufficient columns";
                continue;
            }

            $question = trim($row[0]);
            $optionA = trim($row[1]);
            $optionB = trim($row[2]);
            $optionC = trim($row[3]);
            $optionD = trim($row[4]);
            $correct = strtolower(trim($row[5]));

            if (empty($question)) {
                $errors[] = "Row " . ($index + 2) . ": Question is empty";
                continue;
            }

            if (!in_array($correct, ['a', 'b', 'c', 'd'])) {
                $errors[] = "Row " . ($index + 2) . ": Invalid correct option '{$correct}'";
                continue;
            }

            $validQuestions[] = [
                'question' => $question,
                'option_a' => $optionA,
                'option_b' => $optionB,
                'option_c' => $optionC,
                'option_d' => $optionD,
                'correct_option' => $correct,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        if (empty($validQuestions)) {
            return back()->withErrors(['No valid questions to import']);
        }

        Question::insert($validQuestions);

        return back()->with('success', 'Imported ' . count($validQuestions) . ' questions successfully');
    }

    public function rules()
    {
        return view('assessment.rules');
    }

    public function start()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        }

        $existing = AssessmentAttempt::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return redirect()->route('assessment.test', $existing);
        }

        $totalQuestions = config('assessment.total_questions', 3); 
        $questionIds = Question::where('is_active', true)
            ->inRandomOrder()
            ->limit($totalQuestions)
            ->pluck('id')
            ->toArray();

        if (count($questionIds) < $totalQuestions) {
            return redirect()
                ->route('assessment.rules')
                ->with('error', 'Assessment is not ready yet. Not enough active questions available. Found: ' . count($questionIds) . ', Needed: ' . $totalQuestions); // Added debug info
        }

        $attempt = AssessmentAttempt::create([
            'user_id'    => Auth::id(),
            'questions'  => $questionIds,
            'started_at' => now(),
            'status'     => 'in_progress',
        ]);

        return redirect()->route('assessment.test', $attempt);
    }

    public function test(AssessmentAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            \Log::warning('403: User ' . Auth::id() . ' tried to access attempt ' . $attempt->id . ' owned by ' . $attempt->user_id);
            abort(403, 'Unauthorized access.');
        }
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('assessment.result', $attempt)->with('error', 'Assessment already completed.');
        }

        \Log::info('Test access attempt', [
            'attempt_id' => $attempt->id,
            'user_id' => $attempt->user_id,
            'questions' => $attempt->questions,
            'session_started' => session('assessment_started'),
        ]);

        if (empty($attempt->questions) || !is_array($attempt->questions)) {
            \Log::error('Empty questions for attempt ID: ' . $attempt->id);
            return redirect()
                ->route('assessment.rules')
                ->with('error', 'No questions available for this attempt.');
        }

        $questions = Question::whereIn('id', $attempt->questions)
            ->orderByRaw("FIELD(id, " . implode(',', $attempt->questions) . ")")
            ->get();

        if ($questions->isEmpty()) {
            \Log::error('No matching questions found in DB for attempt ID: ' . $attempt->id);
            return redirect()
                ->route('assessment.rules')
                ->with('error', 'Questions not found.');
        }

        $elapsed = now()->timestamp - $attempt->started_at->timestamp;
        $totalSeconds = config('assessment.duration') * 60;
        $remaining = max(0, $totalSeconds - $elapsed);

        return view('assessment.test', [
            'attempt'   => $attempt,
            'questions' => $questions,
            'remaining' => $remaining,
        ]);
    }

    public function submit(Request $request, AssessmentAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            \Log::warning('403: User ' . Auth::id() . ' tried to submit attempt ' . $attempt->id . ' owned by ' . $attempt->user_id);
            abort(403, 'Unauthorized access.');
        }
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('assessment.result', $attempt)->with('error', 'Assessment already submitted.');
        }

        if (empty($attempt->questions)) {
            return redirect()
                ->route('assessment.rules')
                ->with('error', 'No questions available.');
        }

        $request->validate([
            'answers' => 'nullable|array',
        ]);

        $duration = (int) config('assessment.duration', 30);
        $endTime = $attempt->started_at->copy()->addMinutes($duration);
        $timeExpired = now()->greaterThan($endTime);

        // Allow submission always, but mark if submitted due to expiry or violations
        $submittedDueToExpiry = $timeExpired;
        $submittedDueToViolations = $attempt->violations >= 5;

        $answers = [];
        foreach ($attempt->questions as $questionId) {
            $selected = $request->answers[$questionId] ?? null;
            $question = Question::find($questionId);
            if (!$question) continue; 

            $correct = $selected && $question->correct_option === $selected;

            $answers[] = [
                'question_id' => $questionId,
                'selected_option' => $selected,
                'is_correct' => $correct,
            ];
        }

        $attempt->update([
            'answers' => $answers,
        ]);

        $service = new AssessmentService();
        $service->evaluate($attempt);

        $attempt->update([
            'status' => 'completed',
            'submitted_at' => now(),
            'violations' => $attempt->violations,
        ]);

        return redirect()->route('assessment.result', $attempt);
    }

    public function result(AssessmentAttempt $attempt)
    {
        abort_if($attempt->user_id !== Auth::id(), 403);

        // Allow viewing result even if not completed, but show appropriate message
        $questions = Question::whereIn('id', $attempt->questions)->get()->keyBy('id');
        $answers = collect($attempt->answers ?? [])->keyBy('question_id');

        return view('assessment.result', compact('attempt','answers', 'questions'));
    }

    public function violations(Request $request)
    {
        $attempt = AssessmentAttempt::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            return response()->json(['error' => 'No active assessment'], 404);
        }

        $attempt->increment('violations');

        if ($attempt->violations >= 5) {
            // Auto-submit
            $this->autoSubmit($attempt);
        }

        return response()->json(['violations' => $attempt->violations]);
    }

    private function autoSubmit(AssessmentAttempt $attempt)
    {
        $answers = [];
        foreach ($attempt->questions as $questionId) {
            $answers[$questionId] = null; // No answer for auto-submit
        }

        $request = new Request();
        $request->merge(['answers' => $answers]);

        // Simulate submit
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'completed',
            'violations' => $attempt->violations,
        ]);

        $answers = [];
        foreach ($attempt->questions as $questionId) {
            $answers[] = [
                'question_id' => $questionId,
                'selected_option' => null,
                'is_correct' => false,
            ];
        }

        $attempt->update([
            'answers' => $answers,
        ]);

        $service = new AssessmentService();
        $service->evaluate($attempt);
    }

    public function violation(AssessmentAttempt $attempt)
    {
        abort_if($attempt->user_id !== Auth::id(), 403);

        if ($attempt->violations < 5) {
            $attempt->increment('violations');
        }

        return response()->json(['violations' => $attempt->violations]);
    }


    public function downloadReport(AssessmentAttempt $attempt)
    {
        $attempt->load('user');
        $questions = Question::whereIn('id', $attempt->questions)->get()->keyBy('id');
        $answers = collect($attempt->answers ?? [])->keyBy('question_id');

        $pdf = Pdf::loadView('admin.report_pdf', [
            'attempt' => $attempt,
            'answers' => $answers,
            'questions' => $questions,
        ]);

        return $pdf->download(
            'assessment-report-' . $attempt->id . '.pdf'
        );
    }

   private function baseReportsQuery(Request $request = null)
{
    $query = AssessmentAttempt::query()
        ->with('user')
        ->whereHas('user', function ($q) {
            $q->where('role', '!=', 'admin');
        });

    if ($request) {

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $request->status === 'pass'
                ? $query->where('percentage', '>=', 30)
                : $query->where('percentage', '<', 30);
        }

        if ($request->filled('violations')) {
            $query->where('violations', '>=', $request->violations);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }
    }

    return $query->orderBy('started_at', 'desc');
}

  


   public function downloadSummaryReport(Request $request)
{
    ini_set('memory_limit', '512M');

    $attempts = $this->baseReportsQuery($request)
    ->select('id','user_id','questions','score','percentage','violations','started_at','submitted_at','status')
    ->get();


    $stats = [
        'total_attempts' => $attempts->count(),
        'total_users' => $attempts->pluck('user_id')->unique()->count(),
        'avg_score' => round($attempts->avg('percentage'), 1),
        'avg_violations' => round($attempts->avg('violations'), 1),
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'admin.report_pdf_summary',
        compact('attempts', 'stats')
    )->setPaper('a4', 'landscape');

    return $pdf->download('assessment-summary-report.pdf');
}


}