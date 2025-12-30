<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    background: radial-gradient(circle at top, #1a1a1f, #0b0b0e 60%);
    color: #fff;
    -webkit-font-smoothing: antialiased;
}

.page {
    width: 100%;
    max-width: 1040px;
    padding: 64px 28px;
}

/* GLASS */
.glass-card {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 24px;
    box-shadow:
        0 40px 80px rgba(0,0,0,.8),
        inset 0 1px 0 rgba(255,255,255,.06);
}

/* HEADER */
.header {
    padding: 46px;
    text-align: center;
    margin-bottom: 40px;
}

.header h1 {
    margin: 0;
    font-size: 30px;
    font-weight: 600;
    letter-spacing: -0.4px;
}

/* STATS */
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px,1fr));
    gap: 20px;
    margin-bottom: 42px;
}

.stat {
    padding: 26px;
    text-align: center;
    transition: transform .15s ease, box-shadow .15s ease;
}

.stat:hover {
    transform: translateY(-2px);
    box-shadow:
        0 30px 60px rgba(0,0,0,.9),
        inset 0 1px 0 rgba(255,255,255,.06);
}

.stat-value {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 6px;
}

.stat-label {
    font-size: 13px;
    color: #9b9ba0;
}

/* ACTIONS */
.actions {
    text-align: center;
    margin-bottom: 44px;
}

.action-btn {
    display: inline-block;
    padding: 14px 26px;
    border-radius: 16px;
    background: #ffffff;
    color: #000;
    text-decoration: none;
    font-weight: 500;
    transition: transform .15s ease, box-shadow .15s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(255,255,255,.28);
}

/* SECTION */
.section {
    padding: 44px;
}

.section h2 {
    font-size: 22px;
    margin: 0 0 6px;
}

.section p {
    font-size: 14px;
    color: #9b9ba0;
    margin-bottom: 26px;
}

/* FILE UPLOAD */
.file-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,.08);
    border: 1px dashed rgba(255,255,255,.28);
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 22px;
}

.file-wrapper input {
    display: none;
}

.file-btn {
    background: #fff;
    color: #000;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
}

.file-name {
    font-size: 13px;
    color: #cfcfd4;
    flex: 1;
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* PRIMARY */
.btn-primary {
    width: 100%;
    padding: 14px 0;
    border-radius: 16px;
    background: #fff;
    color: #000;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(255,255,255,.28);
}

/* FEEDBACK */
.success {
    background: rgba(74,222,128,.15);
    color: #4ade80;
    padding: 14px;
    border-radius: 12px;
    font-size: 14px;
    margin-bottom: 18px;
}

.error {
    background: rgba(248,113,113,.15);
    color: #f87171;
    padding: 14px;
    border-radius: 12px;
    font-size: 14px;
    margin-bottom: 18px;
}

/* LOGOUT */
.logout-btn {
    background: rgba(255,255,255,.1);
    color: #fff;
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
    <div class="profile" style="background:rgba(255,255,255,.1); color:#fff; border:1px solid rgba(255,255,255,.2); padding:8px 14px; border-radius:12px; backdrop-filter: blur(12px); font-size:14px; cursor:pointer;">
        👤 {{ Auth::user()->name }}
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button class="logout-btn">Logout</button>
    </form>
</div>

<div class="page">

    <div class="glass-card header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="stats">
        <a href="{{ route('admin.reports') }}" style="text-decoration:none;color:inherit;">
            <div class="glass-card stat">
                <div class="stat-value">{{ $totalTests }}</div>
                <div class="stat-label">Total Tests</div>
            </div>
        </a>

        <a href="{{ route('admin.users') }}" style="text-decoration:none;color:inherit;">
            <div class="glass-card stat">
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Active Users</div>
            </div>
        </a>

        <div class="glass-card stat">
            <div class="stat-value">{{ number_format($averageScore,1) }}%</div>
            <div class="stat-label">Average Score</div>
        </div>

        <div class="glass-card stat">
            <div class="stat-value">{{ number_format($averageViolations,1) }}</div>
            <div class="stat-label">Avg Violations</div>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('admin.reports') }}" class="action-btn">
            View Reports
        </a>
    </div>

    <div class="glass-card section">
        <h2>Question Management</h2>
        <p>Upload questions using a CSV file</p>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @foreach($errors->all() as $error)
            <div class="error">{{ $error }}</div>
        @endforeach

        <form method="POST" action="{{ route('admin.questions.upload') }}" enctype="multipart/form-data">
            @csrf

            <div class="file-wrapper">
                <label for="csvInput" class="file-btn">Choose File</label>
                <span class="file-name" id="csvName">No file chosen</span>
                <input type="file" id="csvInput" name="csv_file" accept=".csv" required>
            </div>

            <button class="btn-primary" type="submit">
                Upload Questions
            </button>
        </form>
    </div>

</div>

<script>
const csvInput = document.getElementById('csvInput');
const csvName = document.getElementById('csvName');

csvInput.addEventListener('change', () => {
    csvName.textContent = csvInput.files.length
        ? csvInput.files[0].name
        : 'No file chosen';
});

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
