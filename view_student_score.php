<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION["user_id"];

// Fetch the latest score and attempt time
$stmt = $conn->prepare("SELECT score, attempt_time FROM student_scores WHERE student_id = ? ORDER BY attempt_time DESC LIMIT 1");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$score_data = $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Fetch student's answers for detailed breakdown
$answers = [];
$correct_count = 0;
$incorrect_count = 0;
$total_questions = 0;

if ($score_data) {
    $stmt = $conn->prepare("SELECT sa.question_id, q.question_text, sa.selected_option_id, o.option_text, o.option_letter, sa.is_correct 
                            FROM student_answers sa 
                            JOIN questions q ON sa.question_id = q.id 
                            JOIN options o ON sa.selected_option_id = o.id 
                            WHERE sa.student_id = ? AND sa.submitted_at = ?");
    $stmt->bind_param("is", $student_id, $score_data['attempt_time']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
        if ($row['is_correct']) {
            $correct_count++;
        } else {
            $incorrect_count++;
        }
        $total_questions++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Score</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .score-details {
            background-color: #fff;
            border-radius: 12px;
            padding: 1em;
            margin-bottom: 1em;
            box-shadow: 0 4px 6px rgba(160, 92, 123, 0.1);
        }
        .score-details p {
            margin: 0.5em 0;
        }
        h3 {
            color:rgb(192, 151, 109); /* Marron doux / terre */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Your Exam Results</h2>
        <div class="nav-links">
            <a href="student_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
    <?php if (isset($_GET['error']) && $_GET['error'] == ' скрипт уже выполнен'): ?>
        <p style="color: red;">You have already taken the exam. Contact your teacher for a retake.</p>
    <?php elseif ($score_data): ?>
        <div class="score-details">
            <h3>Your Score (Canadian System)</h3>
            <p><strong>Total Score:</strong> <?= $score_data['score'] ?> / <?= $total_questions ?></p>
            <p><strong>Correct Answers:</strong> <?= $correct_count ?> (  +1 point each)</p>
            <p><strong>Incorrect Answers:</strong> <?= $incorrect_count ?>  (  -1 point each)</p>
            <p><strong>Total Questions:</strong> <?= $total_questions ?> questions.</p>
            <p><strong>Attempt Time:</strong> <?= $score_data['attempt_time'] ?></p>
            <p><em>Note: The Canadian scoring system awards +1 for each correct answer and -1 for each incorrect answer.</em></p>
        </div>
        <h3>Your Answers:</h3>
        <table border="1">
            <tr><th>Question</th><th>Your Answer</th><th>Correct?</th></tr>
            <?php foreach ($answers as $answer): ?>
                <tr>
                    <td><?= htmlspecialchars($answer['question_text']) ?></td>
                    <td><?= htmlspecialchars($answer['option_letter'] . ': ' . $answer['option_text']) ?></td>
                    <td><?= $answer['is_correct'] ? 'Correct' : 'Incorrect' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have not taken the exam yet.</p>
    <?php endif; ?>
</body>
</html>