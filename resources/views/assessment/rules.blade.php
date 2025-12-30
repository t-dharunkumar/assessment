<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assessment Rules</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    color: #ffffff;
    -webkit-user-select: none;
    user-select: none;
}

/* CONTAINER */
.rules-container {
    max-width: 560px;
    width: 100%;
    padding: 0 20px;
}

/* GLASS CARD */
.glass-card {
    padding: 52px;
    border-radius: 28px;
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border: 1px solid rgba(255,255,255,.08);
    box-shadow:
        0 40px 80px rgba(0,0,0,.75),
        inset 0 1px 0 rgba(255,255,255,.06);
}

/* HEADER */
h1 {
    font-size: 30px;
    font-weight: 600;
    margin-bottom: 18px;
    letter-spacing: -0.4px;
}

.subtitle {
    font-size: 14px;
    color: #9b9ba0;
    margin-bottom: 30px;
}

/* RULE LIST */
.rules {
    list-style: none;
    padding: 0;
    margin: 0;
}

.rules li {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 16px;
    margin-bottom: 12px;
    border-radius: 16px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.12);
    font-size: 14px;
    color: #cfcfd4;
}

/* ICON DOT */
.rules li::before {
    content: "•";
    font-size: 22px;
    line-height: 1;
    color: #ffffff;
    margin-top: -2px;
}

/* ERROR */
.error-box {
    background: linear-gradient(
        135deg,
        rgba(248,113,113,.18),
        rgba(248,113,113,.08)
    );
    border: 1px solid rgba(248,113,113,.4);
    padding: 14px;
    border-radius: 14px;
    margin-bottom: 24px;
    color: #f87171;
    font-size: 14px;
    text-align: center;
    box-shadow: 0 12px 30px rgba(248,113,113,.35);
}

/* START BUTTON */
.btn-primary {
    width: 100%;
    margin-top: 36px;
    padding: 16px 0;
    border-radius: 18px;
    background: #ffffff;
    color: #000;
    border: none;
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    box-shadow: 0 14px 40px rgba(255,255,255,.4);
    transition: transform .15s ease, box-shadow .15s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 20px 50px rgba(255,255,255,.55);
}
</style>
</head>

<body>

<div class="rules-container">

    @if(session('error'))
        <div class="error-box">
            {{ session('error') }}
        </div>
    @endif

    <div class="glass-card">
        <h1>Assessment Rules</h1>
        <div class="subtitle">
            Please read carefully before starting the assessment
        </div>

        <ul class="rules">
            <li>Total Questions: <strong>20</strong></li>
            <li>Time Limit: <strong>15 minutes</strong></li>
            <li>No copy, paste, right-click or tab switching</li>
            <li>Assessment auto-submits when time expires</li>
            <li>Only one attempt is allowed</li>
        </ul>

        <form method="POST" action="{{ route('assessment.start') }}">
            @csrf
            <button class="btn-primary">
                Start Assessment
            </button>
        </form>
    </div>

</div>
</body>
</html>
