<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assessment</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin:0;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    color:#fff;
    font-family:'Inter',sans-serif;
    -webkit-user-select: none;
    user-select: none;
}

/* MAIN LAYOUT */
.wrapper {
    display: grid;
    grid-template-columns: 140px 1fr 200px;
    gap: 36px;
    max-width: 1280px;
    margin: 60px auto;
    padding: 0 24px;
}

/* QUESTION LIST */
.q-list {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px);
    border-radius: 24px;
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    align-content: start;
    box-shadow:
        0 30px 60px rgba(0,0,0,.6),
        inset 0 1px 0 rgba(255,255,255,.06);
}

.q-num {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    background: rgba(255,255,255,.06);
    color: #9b9ba0;
    border: 2px solid transparent;
    transition: all .15s ease;
}

.q-num:hover {
    background: rgba(255,255,255,.12);
}

.q-num.active {
    border-color: #fff;
    color:#fff;
    box-shadow: 0 0 0 1px rgba(255,255,255,.3);
}

.q-num.answered {
    background: linear-gradient(
        135deg,
        rgba(74,222,128,.85),
        rgba(74,222,128,.55)
    );
    color:#000;
    font-weight:600;
    box-shadow:
        0 0 0 1px rgba(74,222,128,.45),
        0 10px 24px rgba(74,222,128,.35);
}

/* QUESTION CARD */
.card {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px);
    padding: 48px;
    border-radius: 28px;
    box-shadow:
        0 40px 80px rgba(0,0,0,.75),
        inset 0 1px 0 rgba(255,255,255,.06);
}

.progress {
    font-size:13px;
    color:#9b9ba0;
    margin-bottom:14px;
}

.question {
    font-size:20px;
    font-weight:600;
    margin-bottom:28px;
    line-height:1.55;
}

/* OPTIONS */
.options label {
    display:block;
    margin:12px 0;
    padding:14px 16px;
    border-radius:16px;
    font-size:14px;
    cursor:pointer;
    background: rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.12);
    transition: all .15s ease;
}

.options label:hover {
    background: rgba(255,255,255,.08);
    box-shadow: 0 6px 18px rgba(255,255,255,.08);
}

.options input {
    margin-right:10px;
}

/* NAV BUTTONS */
.nav {
    display:flex;
    justify-content:space-between;
    margin-top:32px;
}

button {
    padding:12px 22px;
    border-radius:16px;
    border:none;
    cursor:pointer;
    font-weight:500;
}

.btn-secondary {
    background:#1f1f25;
    color:#fff;
}

.btn-primary {
    width:100%;
    margin-top:34px;
    background:#fff;
    color:#000;
    font-size:15px;
    box-shadow: 0 12px 30px rgba(255,255,255,.35);
}

/* TIMER */
.timer-box {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px);
    border-radius:22px;
    padding:22px;
    height:fit-content;
    text-align:center;
    box-shadow:
        0 30px 60px rgba(0,0,0,.6),
        inset 0 1px 0 rgba(255,255,255,.06);
}

.timer-icon {
    font-size:30px;
}

.timer {
    color:#f87171;
    font-size:22px;
    font-weight:600;
    margin-top:6px;
}

/* VIOLATION BANNER */
#violationBanner {
    box-shadow: 0 14px 40px rgba(248,113,113,.5);
}
</style>

</head>

<body oncopy="return false" onpaste="return false" oncontextmenu="return false">

<div style="position:fixed;top:20px;right:20px;z-index:999; display:flex; align-items:center; gap:10px;">
    <div class="profile" style="background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.2); padding:8px 14px; border-radius:12px; backdrop-filter: blur(12px); font-size:14px; cursor:pointer;">
        👤 {{ Auth::user()->name }}
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button id="logoutBtn" style="background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.2); padding:8px 14px; border-radius:12px; cursor:pointer; backdrop-filter: blur(12px);">
            Logout
        </button>
    </form>
</div>

<div class="wrapper">


<div id="violationBanner" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#f87171; color:#fff; padding:12px 24px; border-radius:12px; z-index:1000; font-weight:600; font-size:14px;">
    ⚠️ Violation detected (1 / 5). Further violations will auto-submit your exam.
</div>


<div class="q-list" id="qList"></div>


<div class="card">
    <div class="progress" id="progress"></div>

    <form method="POST"
          action="{{ route('assessment.submit', $attempt->id) }}"
          id="assessmentForm">
    @csrf

    <div class="question" id="questionText"></div>
    <div class="options" id="options"></div>

    <div class="nav">
        <button type="button" class="btn-secondary" onclick="prev()">Previous</button>
        <button type="button" class="btn-secondary" onclick="next()">Next</button>
    </div>

    <button class="btn-primary" type="submit">
        Submit Assessment
    </button>
    </form>
</div>


<div class="timer-box">
    <div class="timer-icon">⏱</div>
    <div class="timer" id="timer"></div>
</div>

</div>

<script>
const questions = @json($questions);

// Timer variables
let seconds = {{ $remaining }};
const timer = document.getElementById('timer');
const assessmentForm = document.getElementById('assessmentForm');

// Violation variables
let violationCount = 0;
const MAX_VIOLATIONS = 5;
let isExamSubmitted = false;
let lastViolationTime = 0;
const VIOLATION_COOLDOWN = 500; // ms

// Question navigation
let index = 0;
let answers = {};

// Initialize timer display
function renderTimer() {
    const m = String(Math.floor(seconds / 60)).padStart(2,'0');
    const s = String(seconds % 60).padStart(2,'0');
    timer.innerText = `${m}:${s}`;
}

renderTimer();

setInterval(() => {
    seconds--;
    renderTimer();

    if (seconds <= 0 && !isExamSubmitted) {
        isExamSubmitted = true;
        document.getElementById('assessmentForm').submit();
    }
}, 1000);

// Initialize question list
const qList = document.getElementById('qList');
questions.forEach((_, i) => {
    const div = document.createElement('div');
    div.className = 'q-num';
    div.innerText = i + 1;
    div.onclick = () => { index = i; render(); };
    qList.appendChild(div);
});

// Render current question
function render() {
    const q = questions[index];

    document.getElementById('progress').innerText =
        `Question ${index + 1} of ${questions.length}`;

    document.getElementById('questionText').innerText = q.question;

    const optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = '';

    ['a','b','c','d'].forEach(opt => {
        const checked = answers[q.id] === opt ? 'checked' : '';
        optionsDiv.innerHTML += `
            <label>
                <input type="radio"
                       name="question_${q.id}"
                       value="${opt}"
                       ${checked}
                       onchange="save(${q.id},'${opt}')">
                ${q['option_' + opt]}
            </label>
        `;
    });

    updateSidebar();
}

// Save answer
function save(qid, value) {
    if (answers[qid] === value) {
        delete answers[qid]; // Unselect
    } else {
        answers[qid] = value; // Select new
    }
    updateSidebar();
}

// Update sidebar indicators
function updateSidebar() {
    document.querySelectorAll('.q-num').forEach((el, i) => {
        el.classList.remove('active','answered');
        if (i === index) el.classList.add('active');
        if (answers[questions[i].id]) el.classList.add('answered');
    });
}

// Navigation functions
function next() {
    if (index < questions.length - 1) {
        index++;
        render();
    }
}

function prev() {
    if (index > 0) {
        index--;
        render();
    }
}

// Form submission
document.getElementById('assessmentForm').addEventListener('submit', () => {
    isExamSubmitted = true;
    for (const [qid, ans] of Object.entries(answers)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `answers[${qid}]`;
        input.value = ans;
        assessmentForm.appendChild(input);
    }
});

document.getElementById('logoutBtn').addEventListener('click', () => {
    isExamSubmitted = true;
});

// Violation handling
function handleViolation(reason) {
    if (isExamSubmitted) return;

    const now = Date.now();
    if (now - lastViolationTime < VIOLATION_COOLDOWN) return;

    lastViolationTime = now;
    violationCount++;

    // Update banner
    const banner = document.getElementById('violationBanner');
    banner.innerText = `⚠️ Violation detected (${violationCount} / ${MAX_VIOLATIONS}). Further violations will auto-submit your exam.`;
    banner.style.display = 'block';

    // Hide after 3s
    setTimeout(() => {
        banner.style.display = 'none';
    }, 3000);

    // Send to server
    fetch("{{ route('assessment.violation', $attempt->id) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    }).then(response => response.json()).then(data => {
        violationCount = data.violations;
    });

    if (violationCount >= MAX_VIOLATIONS) {
        // Disable all listeners
        document.removeEventListener('copy', copyHandler);
        document.removeEventListener('paste', pasteHandler);
        document.removeEventListener('contextmenu', contextHandler);
        document.removeEventListener('keydown', keydownHandler);
        window.removeEventListener('blur', blurHandler);

        // Auto-submit
        isExamSubmitted = true;
        document.getElementById('assessmentForm').submit();
    }
}

// Event handlers
function copyHandler(e) {
    e.preventDefault();
    handleViolation('Copying is not allowed.');
}

function pasteHandler(e) {
    e.preventDefault();
    handleViolation('Pasting is not allowed.');
}

function contextHandler(e) {
    e.preventDefault();
    handleViolation('Right-click is disabled.');
}

function keydownHandler(e) {
    if (
        e.ctrlKey || e.metaKey ||
        ['Alt','Tab','Escape','PrintScreen','F12'].includes(e.key)
    ) {
        e.preventDefault();
        handleViolation('Keyboard shortcut blocked.');
    }
}

function blurHandler() {
    handleViolation('Tab/window switch detected.');
}

// Add listeners
document.addEventListener('copy', copyHandler);
document.addEventListener('paste', pasteHandler);
document.addEventListener('contextmenu', contextHandler);
document.addEventListener('keydown', keydownHandler);
window.addEventListener('blur', blurHandler);

// Initial render
render();

// Prevent back/forward navigation
window.history.pushState(null, null, window.location.href);
window.onpopstate = function () {
    window.history.pushState(null, null, window.location.href);
};

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
