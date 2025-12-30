<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
body {
    font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
    font-size: 13px;                 /* was 11px */
    line-height: 1.6;
    color: #111;
    margin: 32px;
}

/* ---------- TITLE ---------- */
h1 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 6px;
}

/* ---------- META ---------- */
.muted {
    color: #555;
    font-size: 12px;
    margin-bottom: 14px;
}

/* ---------- TABLE ---------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 18px;
    font-size: 12.5px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px 10px;
    text-align: left;
    vertical-align: middle;
}

/* Header row */
th {
    background: #f4f4f4;
    font-weight: 700;
    font-size: 12.5px;
}

/* Zebra rows for readability */
tbody tr:nth-child(even) {
    background: #fafafa;
}

/* Tighten numeric columns */
td:nth-child(1),
td:nth-child(6),
td:nth-child(7),
td:nth-child(8),
td:nth-child(9) {
    text-align: center;
    white-space: nowrap;
}

/* Dates */
td:nth-child(4),
td:nth-child(5) {
    white-space: nowrap;
}

    </style>
</head>

<body>

<h1>Assessment Summary Report</h1>
<p class="muted">
    Generated on {{ now()->format('M d, Y · H:i') }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Started At</th>
            <th>Submitted At</th>
            <th>Total Qs</th>
            <th>Score</th>
            <th>%</th>
            <th>Violations</th>
            <th>Type</th>
        </tr>
    </thead>

    <tbody>
        @foreach($attempts as $i => $attempt)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $attempt->user->name ?? 'N/A' }}</td>
            <td>{{ $attempt->user->email ?? 'N/A' }}</td>
            <td>{{ $attempt->started_at?->format('M d, H:i') }}</td>
            <td>{{ $attempt->submitted_at?->format('M d, H:i') ?? 'N/A' }}</td>
            <td>{{ count($attempt->questions ?? []) }}</td>
            <td>{{ $attempt->score }}</td>
            <td>{{ number_format($attempt->percentage,1) }}%</td>
            <td>{{ $attempt->violations }}</td>
            <td>{{ $attempt->violations >= 5 ? 'Auto' : 'Manual' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
