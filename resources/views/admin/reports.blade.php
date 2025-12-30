<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Test Reports</title>

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
    max-width: 1600px;
    padding: 48px 40px;
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
    padding: 44px;
    text-align: center;
    margin-bottom: 36px;
}

.header h1 {
    margin: 0 0 6px;
    font-size: 30px;
    font-weight: 600;
    letter-spacing: -0.3px;
}

.back-link {
    color: #cfcfd4;
    font-size: 14px;
    text-decoration: none;
}

/* FILTERS */
.filters {
    padding: 32px;
    margin-bottom: 30px;
}

.filters form {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr 1fr 1fr;
    gap: 18px;
    align-items: end;
}

.filters input,
.filters select {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    padding: 10px 12px;
    border-radius: 12px;
    font-size: 13px;
}

.filters input::placeholder {
    color: #9b9ba0;
}

.filters select {
    appearance: none;
    background-color: rgba(255,255,255,.08);
    color: #ffffff;
    cursor: pointer;
}

.filters select option {
    background-color: #1c1c21;
    color: #ffffff;
}

.filters select:focus {
    outline: none;
    border-color: rgba(255,255,255,.35);
    box-shadow: 0 0 0 2px rgba(255,255,255,.12);
}

.filters button {
    grid-column: 1 / -1;
    justify-self: center;
    width: 260px;
    padding: 14px;
    border-radius: 16px;
    border: none;
    background: #ffffff;
    color: #000;
    font-weight: 500;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}

.filters button:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(255,255,255,.28);
}

/* SUMMARY DOWNLOAD */
.download-summary {
    text-align: center;
    margin-bottom: 26px;
}

.download-summary a {
    display: inline-block;
    padding: 14px 28px;
    border-radius: 16px;
    background: #ffffff;
    color: #000;
    text-decoration: none;
    font-weight: 500;
    transition: transform .15s ease, box-shadow .15s ease;
}

.download-summary a:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(255,255,255,.28);
}

/* TABLE */
.table-wrapper {
    padding: 26px;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th, td {
    padding: 14px 12px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,.08);
    white-space: nowrap;
}

th {
    color: #9b9ba0;
    font-weight: 500;
}

tbody tr {
    transition: background .15s ease;
}

tbody tr:hover {
    background: rgba(255,255,255,.04);
}

/* STATUS */
.status-pass {
    color: #4ade80;
    font-weight: 600;
}

.status-fail {
    color: #f87171;
    font-weight: 600;
}

/* ACTIONS */
.actions {
    display: flex;
    gap: 14px;
}

.action-link {
    color: #cfcfd4;
    text-decoration: none;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 10px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    transition: all .15s ease;
}

.action-link:hover {
    background: rgba(255,255,255,.14);
    text-decoration: none;
}

/* PAGINATION */
.pagination-card {
    margin-top: 34px;
    padding: 22px;
    display: flex;
    justify-content: center;
}

.pager {
    display: flex;
    align-items: center;
    gap: 12px;
}

.pager-btn,
.pager-num {
    min-width: 44px;
    height: 44px;
    padding: 0 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-size: 14px;
    text-decoration: none;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    color: #cfcfd4;
    transition: transform .15s ease, box-shadow .15s ease;
}

.pager-btn:hover,
.pager-num:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(255,255,255,.15);
}

.pager-num.active {
    background: #ffffff;
    color: #000;
    font-weight: 600;
}

.pager-btn.disabled {
    opacity: .4;
    pointer-events: none;
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
        <h1>Test Reports</h1>
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
    </div>

    <div class="glass-card filters">
        <form method="GET" action="{{ route('admin.reports') }}">
            <input type="text" name="search" placeholder="Search name or email" value="{{ request('search') }}">
            <select name="status">
                <option value="">All Status</option>
                <option value="pass" {{ request('status') === 'pass' ? 'selected' : '' }}>Pass</option>
                <option value="fail" {{ request('status') === 'fail' ? 'selected' : '' }}>Fail</option>
            </select>
            <input type="number" name="violations" placeholder="Min violations" value="{{ request('violations') }}">
            <input type="date" name="date_from" value="{{ request('date_from') }}">
            <input type="date" name="date_to" value="{{ request('date_to') }}">
            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <div class="download-summary">
        <a href="{{ route('admin.reports.download.summary') }}">
            Download Summary Report
        </a>
    </div>

    <div class="glass-card table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Score</th>
                    <th>%</th>
                    <th>Status</th>
                    <th>Violations</th>
                    <th>Started</th>
                    <th>Submitted</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($attempts as $attempt)
                <tr>
                    <td>{{ $attempt->user->name ?? 'N/A' }}</td>
                    <td>{{ $attempt->user->email ?? 'N/A' }}</td>
                    <td>{{ $attempt->score }}/{{ count($attempt->answers ?? []) }}</td>
                    <td>{{ number_format($attempt->percentage,1) }}%</td>
                    <td class="{{ $attempt->percentage >= 30 ? 'status-pass' : 'status-fail' }}">
                        {{ $attempt->percentage >= 30 ? 'Pass' : 'Fail' }}
                    </td>
                    <td>{{ $attempt->violations }}</td>
                    <td>{{ $attempt->started_at->format('M d, H:i') }}</td>
                    <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, H:i') : 'N/A' }}</td>
                    <td>{{ $attempt->violations >= 5 ? 'Auto' : 'Manual' }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.report.detail', $attempt) }}" class="action-link">View</a>
                            <a href="{{ route('admin.report.download', $attempt) }}" class="action-link" style="opacity:.75;">
                                PDF
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="glass-card pagination-card">
        <div class="pager">

            @if ($attempts->onFirstPage())
                <span class="pager-btn disabled">‹ Prev</span>
            @else
                <a href="{{ $attempts->previousPageUrl() }}" class="pager-btn">‹ Prev</a>
            @endif

            @foreach ($attempts->getUrlRange(
                max(1, $attempts->currentPage() - 3),
                min($attempts->lastPage(), $attempts->currentPage() + 3)
            ) as $page => $url)
                @if ($page == $attempts->currentPage())
                    <span class="pager-num active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pager-num">{{ $page }}</a>
                @endif
            @endforeach

            @if ($attempts->hasMorePages())
                <a href="{{ $attempts->nextPageUrl() }}" class="pager-btn">Next ›</a>
            @else
                <span class="pager-btn disabled">Next ›</span>
            @endif

        </div>
    </div>

</div>

<script>
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
