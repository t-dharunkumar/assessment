<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<style>
body {
    font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
    font-size: 13.5px;           /* was 12px */
    line-height: 1.7;
    color: #111;
    margin: 32px;
}

/* ---------- HEADER ---------- */
.header {
    margin-bottom: 22px;
}

.title {
    font-size: 24px;            
    font-weight: 700;
    margin-bottom: 6px;
}

.meta {
    font-size: 12.5px;
    color: #555;
}

.divider {
    margin: 20px 0;
    border-bottom: 1px solid #ddd;
}


.summary {
    margin-bottom: 30px;
}

.summary-item {
    font-size: 13.5px;
    margin-bottom: 6px;
}


.question {
    margin-bottom: 30px;
    padding-bottom: 18px;
    border-bottom: 1px solid #e3e3e3;
}

.question-text {
    font-size: 15px;             
    font-weight: 600;
    margin-bottom: 14px;
}


.option {
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 6px;
    border: 1px solid #ddd;
    font-size: 13.5px;
}


.option.correct {
    background: #e6f4ea;
    border-color: #15803d;
    color: #15803d;
    font-weight: 600;
}


.option.selected.wrong {
    background: #fdecea;
    border-color: #b91c1c;
    color: #b91c1c;
    font-weight: 600;
}


.option.selected.correct {
    background: #d1fae5;
    border-color: #15803d;
    color: #15803d;
    font-weight: 700;
}

.option.neutral {
    background: #fafafa;
}


.status {
    margin-top: 10px;
    font-size: 12.5px;
    font-weight: 600;
}

.status.correct { color: #15803d; }
.status.wrong { color: #b91c1c; }
.status.na { color: #555; }


</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="title">Assessment Report</div>
    <div class="meta">
        User: {{ $attempt->user->name }} ({{ $attempt->user->email }})<br>
        Generated on: {{ now()->format('M d, Y · H:i') }}
    </div>
</div>

<div class="divider"></div>

<!-- SUMMARY -->
<div class="summary">
    <div class="summary-item"><strong>Score:</strong> {{ $attempt->score }} / {{ count($attempt->questions) }}</div>
    <div class="summary-item"><strong>Percentage:</strong> {{ number_format($attempt->percentage,1) }}%</div>
    <div class="summary-item"><strong>Violations:</strong> {{ $attempt->violations }}</div>
    <div class="summary-item"><strong>Submission:</strong> {{ $attempt->violations >= 5 ? 'Auto Submitted' : 'Manual Submission' }}</div>
    <div class="summary-item"><strong>Start Time:</strong> {{ optional($attempt->started_at)->format('M d, Y · H:i') }}</div>
    <div class="summary-item"><strong>Submit Time:</strong> {{ optional($attempt->submitted_at)->format('M d, Y · H:i') }}</div>
</div>

<div class="divider"></div>

<!-- QUESTIONS -->
@foreach($answers as $index => $answer)

@php
$question = $questions->get($answer['question_id']);
$options = [
    'a' => $question->option_a,
    'b' => $question->option_b,
    'c' => $question->option_c,
    'd' => $question->option_d,
];

$selected = $answer['selected_option'];
$correct = $question->correct_option;
@endphp

<div class="question">

    <div class="question-text">
        {{ $index + 1 }}. {{ $question->question }}
    </div>

    @foreach($options as $key => $text)
        @php
            $isCorrect = $key === $correct;
            $isSelected = $key === $selected;
            $class =
                $isCorrect && $isSelected ? 'option selected correct' :
                ($isCorrect ? 'option correct' :
                ($isSelected ? 'option selected wrong' : 'option neutral'));
        @endphp

        <div class="{{ $class }}">
            {{ strtoupper($key) }}) {{ $text }}
        </div>
    @endforeach

    <div class="status
        {{ $answer['is_correct'] ? 'correct' : ($answer['selected_option'] ? 'wrong' : 'na') }}">
        Status:
        {{ $answer['is_correct'] ? 'Correct' : ($answer['selected_option'] ? 'Wrong' : 'Not Answered') }}
    </div>

</div>

@endforeach

</body>
</html>
