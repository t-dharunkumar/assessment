<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    color: #ffffff;
    -webkit-font-smoothing: antialiased;
}

/* GLASS CARD */
.glass-card {
    width: 380px;
    padding: 56px 48px;
    border-radius: 26px;
    text-align: center;
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border: 1px solid rgba(255,255,255,.08);
    box-shadow:
        0 40px 80px rgba(0,0,0,.8),
        inset 0 1px 0 rgba(255,255,255,.06);
    animation: fadeIn .4s ease-out;
}

/* TEXT */
.welcome-text {
    font-size: 30px;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: -0.4px;
}

.login-subtitle {
    font-size: 14px;
    color: #9b9ba0;
    margin-bottom: 36px;
}

/* ERROR */
.error {
    color: #f87171;
    font-size: 14px;
    margin-bottom: 18px;
}

/* GOOGLE BUTTON */
.google-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 14px 0;
    border-radius: 14px;
    background: #ffffff;
    color: #000000;
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    transition: transform .15s ease, box-shadow .15s ease;
}

.google-btn img {
    width: 18px;
    height: 18px;
}

.google-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(255,255,255,.28);
}

.google-btn:active {
    transform: scale(.98);
}

/* ANIMATION */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
</head>

<body>

<div class="glass-card">

    <div class="welcome-text">Welcome!</div>
    <div class="login-subtitle">Sign in to continue</div>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <a href="{{ route('google.redirect') }}" class="google-btn">
        <img
            src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
            alt="Google"
        >
        Login with Google
    </a>

</div>

</body>
</html>
