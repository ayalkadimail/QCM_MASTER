<?php
session_start();
require 'db.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION["user_id"];
$username = $conn->query("SELECT username FROM users WHERE id = $student_id")->fetch_assoc()['username'];
$filiere = $conn->query("SELECT filiere FROM users WHERE id = $student_id")->fetch_assoc()['filiere'] ?? 'Not Selected';
$exam_status = $conn->query("SELECT COUNT(*) as attempt_count FROM student_scores WHERE student_id = $student_id")->fetch_assoc()['attempt_count'] > 0 ? 'Taken' : 'Not Taken';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        
        .dashboard-content {
            background-color: #fff;
            border-radius: 12px;
            padding: 2em;
            box-shadow: 0 4px 6px rgba(160, 92, 123, 0.1);
            margin-top: 1em;
        }
        .dashboard-content p {
            margin: 0.5em 0;
        }
        h3 {
            color: #a05c7b; /* Marron doux / terre */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Student Dashboard</h2>
        <div class="nav-links">
            <a href="take_exam.php">Take Exam</a>
            <a href="view_student_score.php">View Score</a>
            <a href="logout.php" style="color: red;">Logout</a>
        </div>
    </div>
    <div class="dashboard-content">
        <h3>Welcome, <?= htmlspecialchars($username) ?>!</h3>
        <p><strong>Filiere:</strong> <?= htmlspecialchars($filiere) ?></p>
        <p><strong>Exam Status:</strong> <?= $exam_status ?></p>
        <p>Explore the options above to take your exam or view your scores.</p>
    </div>
</body>
</html>