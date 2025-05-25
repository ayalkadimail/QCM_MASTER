<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    header("Location: login.php");
    exit;
}

// Fetch all students who have taken exams with their scores, filiere, and exam status
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.filiere, s.score, s.attempt_time
    FROM users u
    LEFT JOIN student_scores s ON u.id = s.student_id
    WHERE u.role = 'student' AND s.score IS NOT NULL
    ORDER BY s.attempt_time DESC
");
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Student Scores</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.8em;
            text-align: left;
        }
        th {
            background-color: #a05c7b;
            color: #FFFDEC;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Student Exam Results</h2>
        <div class="nav-links">
            <a href="teacher_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
    <?php if (empty($students)): ?>
        <p>No students have taken the exam yet.</p>
    <?php else: ?>
        <div class="score-details">
            <h3>Student Scores (Canadian System)</h3>
            <p><em>Note: The Canadian scoring system awards +1 for each correct answer and -1 for each incorrect answer.</em></p>
        </div>
        <table border="1">
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Filiere</th>
                <th>Exam Status</th>
                <th>Score</th>
                <th>Attempt Time</th>
            </tr>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['id']) ?></td>
                    <td><?= htmlspecialchars($student['username']) ?></td>
                    <td><?= htmlspecialchars($student['filiere'] ?: 'None') ?></td>
                    <td>Taken</td>
                    <td><?= htmlspecialchars($student['score']) ?></td>
                    <td><?= htmlspecialchars($student['attempt_time']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>