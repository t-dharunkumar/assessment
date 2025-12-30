<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resume Upload</title>

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
    user-select: none;
}

/* GLASS CARD */
.glass-card {
    width: 480px;
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

/* HEADER */
.icon {
    font-size: 56px;
    margin-bottom: 10px;
}

.title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: -0.4px;
}

.subtitle {
    font-size: 14px;
    color: #9b9ba0;
    margin-bottom: 34px;
    line-height: 1.6;
}

/* FILE PICKER */
.file-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,.08);
    border: 1px dashed rgba(255,255,255,.28);
    border-radius: 14px;
    padding: 12px 14px;
    margin-bottom: 26px;
}

.file-wrapper input {
    display: none;
}

.file-btn {
    background: #ffffff;
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
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    text-align: left;
}

/* PRIMARY BUTTON */
.btn-primary {
    width: 100%;
    padding: 14px 0;
    border-radius: 14px;
    background: #ffffff;
    color: #000;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(255,255,255,.28);
}

/* ERROR */
.error {
    background: rgba(248,113,113,.15);
    color: #f87171;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 18px;
    font-size: 14px;
}

/* RESULT */
.result {
    margin-top: 34px;
    animation: fadeIn .35s ease-out;
}

.percentage {
    font-size: 56px;
    font-weight: 600;
    margin-bottom: 8px;
}

.success { color: #4ade80; }
.danger { color: #f87171; }

.skills {
    font-size: 14px;
    color: #cfcfd4;
    line-height: 1.6;
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

/* ANIMATION */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
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

<div class="glass-card">

    <div class="icon">📄</div>
    <div class="title">Upload Your Resume</div>
    <div class="subtitle">
        We analyze your resume to match you with the right assessment.<br>
        PDF only · Max 2MB
    </div>

    <div id="errorBox" class="error" style="display:none;"></div>

    <form id="resumeForm" enctype="multipart/form-data">
        @csrf

        <div class="file-wrapper">
            <label for="resumeInput" class="file-btn">Choose File</label>
            <span class="file-name" id="fileName">No file selected</span>
            <input
                type="file"
                id="resumeInput"
                name="resume"
                accept="application/pdf"
                required
            >
        </div>

        <button type="submit" class="btn-primary">
            Analyze Resume
        </button>
    </form>

    <div id="resultBox" class="result" style="display:none;"></div>

</div>

<script>
const resumeInput = document.getElementById('resumeInput');
const fileName = document.getElementById('fileName');

resumeInput.addEventListener('change', () => {
    fileName.textContent = resumeInput.files.length
        ? resumeInput.files[0].name
        : 'No file selected';
});

document.getElementById('resumeForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const errorBox = document.getElementById('errorBox');
    const resultBox = document.getElementById('resultBox');

    errorBox.style.display = 'none';
    resultBox.style.display = 'none';

    const formData = new FormData(this);

    try {
        const response = await fetch("{{ route('resume.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: formData
        });

        if (!response.ok) throw new Error();

        const data = await response.json();

        resultBox.style.display = 'block';
        resultBox.innerHTML = `
            <div class="percentage ${data.eligible ? 'success' : 'danger'}">
                ${data.percentage}%
            </div>

            <div class="skills">
                <strong>Matched Skills:</strong><br>
                ${data.matched_skills.length ? data.matched_skills.join(', ') : 'None'}
            </div>

            <div style="margin-top:22px;">
                ${
                    data.eligible
                    ? `<p class="success">You are eligible for the assessment.</p>
                       <a href="{{ route('assessment.rules') }}"
                          class="btn-primary"
                          style="display:block;margin-top:14px;">
                          Take Assessment
                       </a>`
                    : `<p class="danger">Resume does not meet requirements.</p>`
                }
            </div>
        `;
    } catch {
        errorBox.style.display = 'block';
        errorBox.textContent = 'Something went wrong. Please try again.';
    }
});

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
