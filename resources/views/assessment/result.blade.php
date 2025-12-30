<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assessment Result</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    font-family: 'Inter', sans-serif;
    color: #ffffff;
    -webkit-user-select: none;
    user-select: none;
}

/* GLASS CARD */
.glass-card {
    width: 720px;
    max-height: 85vh;
    padding: 44px;
    border-radius: 28px;
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border: 1px solid rgba(255,255,255,.08);
    box-shadow:
        0 40px 80px rgba(0,0,0,.8),
        inset 0 1px 0 rgba(255,255,255,.06);
    display: flex;
    flex-direction: column;
}

/* HEADER */
.header {
    text-align: center;
    margin-bottom: 24px;
}

.title {
    font-size: 30px;
    font-weight: 600;
    margin-bottom: 6px;
}

.score {
    font-size: 64px;
    font-weight: 600;
    margin-top: 8px;
}

.success { color: #4ade80; }
.danger  { color: #f87171; }

.subtext {
    font-size: 14px;
    color: #9b9ba0;
}

/* ANSWERS */
.answers-box {
    margin-top: 28px;
    padding-right: 8px;
    overflow-y: auto;
    flex: 1;
}

/* SCROLLBAR */
.answers-box::-webkit-scrollbar {
    width: 8px;
}
.answers-box::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,.18);
    border-radius: 8px;
}

/* ANSWER CARD */
.answer-item {
    padding: 20px;
    margin-bottom: 18px;
    border-radius: 20px;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.12);
}

.question {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 10px;
}

.answer-line {
    font-size: 14px;
    margin-top: 6px;
}

/* ANSWER STATES */
.answer-correct {
    color: #4ade80;
    font-weight: 600;
}

.answer-wrong {
    color: #f87171;
    font-weight: 600;
}

.answer-muted {
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

<div class="glass-card">

@if($attempt->status !== 'completed')
    <div class="header">
        <div class="title">Assessment In Progress</div>
        <div class="subtext">Your assessment has not been completed yet.</div>
    </div>
@else

    <!-- HEADER -->
    <div class="header">
        <div class="score {{ $attempt->percentage >= 30 ? 'success' : 'danger' }}">
            {{ $attempt->percentage }}%
        </div>
        <div class="subtext">
            Score: {{ $attempt->score }} / {{ count($attempt->questions) }}
        </div>
    </div>

    <hr style="margin: 26px 0; opacity: .2">

    <!-- ANSWERS -->
    <div class="answers-box">
        @foreach($answers as $ans)

            @php
                $question = $questions->get($ans['question_id']);
                $options = [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                ];

                $yourAnswerText = $ans['selected_option']
                    ? $options[$ans['selected_option']]
                    : null;

                $correctAnswerText = $options[$question->correct_option];
            @endphp

            <div class="answer-item">
                <div class="question">
                    {{ $question->question }}
                </div>

                <div class="answer-line">
                    <span class="answer-muted">Your answer:</span>
                    @if($yourAnswerText)
                        <span class="{{ $ans['is_correct'] ? 'answer-correct' : 'answer-wrong' }}">
                            {{ $yourAnswerText }}
                        </span>
                    @else
                        <span class="answer-muted">Not answered</span>
                    @endif
                </div>

                <div class="answer-line">
                    <span class="answer-muted">Correct answer:</span>
                    <span class="answer-correct">
                        {{ $correctAnswerText }}
                    </span>
                </div>
            </div>

        @endforeach
    </div>

@endif
</div>

<script>
/* Prevent back/forward navigation */
window.history.pushState(null, null, window.location.href);
window.onpopstate = function () {
    window.history.pushState(null, null, window.location.href);
};

/* Profile hover */
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
