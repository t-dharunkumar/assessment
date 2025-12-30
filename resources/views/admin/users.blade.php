<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users</title>

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
}

.page {
    width: 100%;
    max-width: 1600px;
    padding: 40px;
}

/* GLASS */
.glass-card {
    background: rgba(28,28,33,.65);
    backdrop-filter: blur(22px) saturate(140%);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 22px;
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
    font-size: 28px;
    font-weight: 600;
}

.back-link {
    color: #cfcfd4;
    font-size: 14px;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

/* INVITE */
.invite-box {
    padding: 34px;
    margin-bottom: 32px;
    text-align: center;
}

.invite-box form {
    display: flex;
    gap: 14px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.invite-box input {
    width: 360px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    padding: 12px 14px;
    border-radius: 12px;
    font-size: 14px;
}

.invite-box input::placeholder {
    color: #9b9ba0;
}

.invite-box button {
    padding: 12px 26px;
    border-radius: 12px;
    border: none;
    background: #ffffff;
    color: #000;
    font-weight: 500;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}

.invite-box button:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(255,255,255,.25);
}

/* TABLE */
.table-wrapper {
    padding: 26px;
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
    background: rgba(255,255,255,.03);
}

/* BADGES */
.badge {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
}

.badge-green {
    background: rgba(74,222,128,.22);
    color: #4ade80;
}

.badge-red {
    background: rgba(248,113,113,.22);
    color: #f87171;
}

.badge-gray {
    background: rgba(255,255,255,.14);
    color: #cfcfd4;
}

/* ACTIONS */
.action-link {
    color: #cfcfd4;
    text-decoration: none;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.action-link:hover {
    text-decoration: underline;
}

/* LOGOUT */
.logout-btn {
    background: rgba(255,255,255,.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,.2);
    padding: 8px 16px;
    border-radius: 12px;
    cursor: pointer;
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

    <!-- HEADER -->
    <div class="glass-card header">
        <h1>Users</h1>
        <a href="{{ route('dashboard') }}" class="back-link">← Back to Dashboard</a>
    </div>

    <!-- INVITE -->
    <div class="glass-card invite-box">
        <form method="POST" action="{{ route('admin.users.invite') }}">
            @csrf
            <input type="email" name="email" placeholder="Invite user by email" required>
            <button type="submit">Send Invite</button>
        </form>

        @error('email')
            <div style="color:#f87171;font-size:14px;margin-top:12px;">
                {{ $message }}
            </div>
        @enderror

        @if(session('success'))
            <div style="color:#4ade80;font-size:14px;margin-top:12px;">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <!-- USERS TABLE -->
    <div class="glass-card table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Invited</th>
                    <th>Can Take Test</th>
                    <th>Attempts</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name ?? '—' }}</td>
                    <td>{{ $user->email }}</td>

                    <td>
                        <span class="badge {{ $user->is_invited ? 'badge-green' : 'badge-gray' }}">
                            {{ $user->is_invited ? 'Invited' : 'No' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $user->can_take_test ? 'badge-green' : 'badge-red' }}">
                            {{ $user->can_take_test ? 'Allowed' : 'Blocked' }}
                        </span>
                    </td>

                    <td>{{ $user->assessmentAttempts()->count() }}</td>

                    <td style="display:flex; gap:18px;">
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                            @csrf
                            <button type="submit" class="action-link">
                                Toggle Access
                            </button>
                        </form>

                        <a href="{{ route('admin.reports', ['user' => $user->id]) }}" class="action-link">
                            View Tests
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
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
