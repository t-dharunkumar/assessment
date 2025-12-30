<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Test Report Details</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    color: #ffffff;
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
}

.container {
    max-width: 1200px;
    margin: 48px auto;
    padding: 0 24px;
}

/* GLASS */
.glass {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border-radius: 26px;
    border: 1px solid rgba(255,255,255,.08);
    box-shadow:
        0 40px 80px rgba(0,0,0,.7),
        inset 0 1px 0 rgba(255,255,255,.06);
}

/* HEADER */
.header {
    padding: 44px;
    text-align: center;
    margin-bottom: 40px;
}

.header h1 {
    margin: 0 0 8px;
    font-size: 32px;
    font-weight: 600;
    letter-spacing: -0.4px;
}

.header p {
    margin: 0 0 14px;
    font-size: 14px;
    color: #9b9ba0;
}

.back-link {
    font-size: 14px;
    color: #cfcfd4;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

/* SUMMARY */
.summary {
    padding: 32px;
    margin-bottom: 42px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
}

.summary-item {
    text-align: center;
}

.summary-value {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 4px;
}

.summary-item div:last-child {
    font-size: 13px;
    color: #9b9ba0;
}

/* SECTION TITLE */
.section-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 26px;
}

/* QUESTIONS */
.questions-scroll {
    max-height: 540px;
    overflow-y: auto;
    padding-right: 8px;
    scroll-behavior: smooth;
}

/* Scrollbar */
.questions-scroll::-webkit-scrollbar {
    width: 8px;
}
.questions-scroll::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.18);
    border-radius: 8px;
}
.questions-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.28);
}

/* QUESTION CARD */
.question {
    padding: 26px;
    margin-bottom: 24px;
    transition: transform .15s ease, box-shadow .15s ease;
}

.question:hover {
    transform: translateY(-1px);
    box-shadow:
        0 14px 34px rgba(0,0,0,.45),
        inset 0 1px 0 rgba(255,255,255,.06);
}

.question-text {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    line-height: 1.55;
    max-width: 900px;
}

/* OPTIONS */
.options {
    margin-bottom: 14px;
}

.option {
    margin: 8px 0;
    padding: 13px 16px;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,.12);
    font-size: 14px;
    background: rgba(255,255,255,.03);
}

/* Correct */
.option.correct {
    background: linear-gradient(
        135deg,
        rgba(74, 222, 128, 0.22),
        rgba(74, 222, 128, 0.10)
    );
    border-color: rgba(74, 222, 128, 0.65);
    box-shadow:
        0 0 0 1px rgba(74, 222, 128, 0.28),
        0 10px 26px rgba(74, 222, 128, 0.18);
}

/* Selected but wrong */
.option.selected:not(.correct) {
    background: linear-gradient(
        135deg,
        rgba(248, 113, 113, 0.22),
        rgba(248, 113, 113, 0.10)
    );
    border-color: rgba(248, 113, 113, 0.55);
    box-shadow:
        0 0 0 1px rgba(248, 113, 113, 0.25),
        0 10px 26px rgba(248, 113, 113, 0.20);
}

/* Selected and correct */
.option.correct.selected {
    background: linear-gradient(
        135deg,
        rgba(74, 222, 128, 0.32),
        rgba(74, 222, 128, 0.16)
    );
    border-color: rgba(74, 222, 128, 0.85);
    box-shadow:
        0 0 0 1px rgba(74, 222, 128, 0.45),
        0 14px 36px rgba(74, 222, 128, 0.35);
}

/* STATUS */
.status {
    margin-top: 12px;
    font-size: 14px;
    font-weight: 500;
}

.status-correct {
    color: #4ade80;
}

.status-wrong {
    color: #f87171;
}

.status-not-answered {
    color: #9b9ba0;
}

/* LOGOUT */
.logout-btn {
    background: rgba(255,255,255,.1);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.2);
    padding: 8px 16px;
    border-radius: 12px;
    cursor: pointer;
    backdrop-filter: blur(12px);
}
</style>
</head>

<body>

<div style="position:fixed;top:20px;right:20px;z-index:999; display:flex; align-items:center; gap:10px;">
<div class="profile" style="background:rgba(255,255,255,.1); color:#ffffff; border:1px solid rgba(255,255,255,.2); padding:8px 14px; border-radius:12px; backdrop-filter: blur(12px); font-size:14px; cursor:pointer;">
        👤 {{ Auth::user()->name }}
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button class="logout-btn">Logout</button>
    </form>
</div>

<div class="container">

    <!-- HEADER -->
    <div class="glass header">
        <h1>Test Report Details</h1>
        <p>
            User: {{ $attempt->user->name ?? 'N/A' }}
            ({{ $attempt->user->email ?? 'N/A' }})
        </p>
        <a href="{{ route('admin.reports') }}" class="back-link">
            ← Back to Reports
        </a>
    </div>

    <!-- SUMMARY -->
    <div class="glass summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">
                    {{ $attempt->score }}/{{ $questions->count() }}
                </div>
                <div>Score</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">
                    {{ number_format($attempt->percentage,1) }}%
                </div>
                <div>Percentage</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">
                    {{ $attempt->violations }}
                </div>
                <div>Violations</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">
                    {{ $attempt->violations >= 5 ? 'Auto' : 'Manual' }}
                </div>
                <div>Submission</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">
                    {{ $attempt->started_at->format('M d, H:i') }}
                </div>
                <div>Started</div>
            </div>

            <div class="summary-item">
                <div class="summary-value">
                    {{ $attempt->submitted_at
                        ? $attempt->submitted_at->format('M d, H:i')
                        : 'N/A' }}
                </div>
                <div>Submitted</div>
            </div>
        </div>
    </div>

    <!-- QUESTIONS -->
    <div class="section-title">Question Details</div>

    <div class="questions-scroll">
        @foreach($questions as $question)
            @php $answer = $answers->get($question->id); @endphp

            <div class="glass question">
                <div class="question-text">
                    {{ $question->question }}
                </div>

                <div class="options">
                    <div class="option {{ $question->correct_option === 'a' ? 'correct' : '' }} {{ $answer && $answer['selected_option'] === 'a' ? 'selected' : '' }}">
                        A) {{ $question->option_a }}
                    </div>
                    <div class="option {{ $question->correct_option === 'b' ? 'correct' : '' }} {{ $answer && $answer['selected_option'] === 'b' ? 'selected' : '' }}">
                        B) {{ $question->option_b }}
                    </div>
                    <div class="option {{ $question->correct_option === 'c' ? 'correct' : '' }} {{ $answer && $answer['selected_option'] === 'c' ? 'selected' : '' }}">
                        C) {{ $question->option_c }}
                    </div>
                    <div class="option {{ $question->correct_option === 'd' ? 'correct' : '' }} {{ $answer && $answer['selected_option'] === 'd' ? 'selected' : '' }}">
                        D) {{ $question->option_d }}
                    </div>
                </div>

                <div class="status
                    {{ $answer && $answer['is_correct']
                        ? 'status-correct'
                        : ($answer && $answer['selected_option']
                            ? 'status-wrong'
                            : 'status-not-answered') }}">
                    Status:
                    {{ $answer && $answer['is_correct']
                        ? 'Correct'
                        : ($answer && $answer['selected_option']
                            ? 'Wrong'
                            : 'Not Answered') }}
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
// Profile hover
const profile = document.querySelector('.profile');
if (profile) {
    const originalHTML = profile.innerHTML;
    const emailHTML = '📧 {{ Auth::user()->email }}';
    profile.addEventListener('mouseover', () => {
        profile.innerHTML = emailHTML;
    });
    profile.addEventListener('mouseout', () => {
        profile.innerHTML = originalHTML;
    });
}
</script>

</body>
</html>
