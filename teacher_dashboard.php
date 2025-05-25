<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    header("Location: login.php");
    exit;
}

require_once "db.php";

// Fetch dashboard statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$total_exams = $conn->query("SELECT COUNT(*) as count FROM exams")->fetch_assoc()['count'];
$students_taken_exam = $conn->query("SELECT COUNT(DISTINCT student_id) as count FROM student_scores")->fetch_assoc()['count'];

// Fetch recent submissions (last 5)
$stmt = $conn->prepare("
    SELECT u.username, s.score, s.attempt_time, e.name as exam_name
    FROM student_scores s
    JOIN users u ON s.student_id = u.id
    JOIN exams e ON u.filiere = e.filiere
    ORDER BY s.attempt_time DESC
    LIMIT 5
");
$stmt->execute();
$recent_submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-content {
            background-color: #fff;
            border-radius: 12px;
            padding: 2em;
            box-shadow: 0 4px 6px rgba(160, 92, 123, 0.1);
            margin-top: 1em;
        }
        .dashboard-content h3 {
            color: #a05c7b;
            margin-bottom: 1em;
        }
        .dashboard-content p {
            margin: 0.5em 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1em;
            margin-bottom: 2em;
        }
        .stat-box {
            background-color: #FFFDEC;
            padding: 1em;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #FFCFCF;
        }
        .stat-box h4 {
            margin: 0;
            color: #a05c7b;
        }
        .stat-box p {
            font-size: 1.5em;
            color: #444;
            margin: 0.5em 0 0;
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
        <h2>Teacher Dashboard</h2>
        <div class="nav-links">
            <a href="manage_student.php">Manage Students</a>
            <a href="manage_exam.php">Edit Exam</a>
            <a href="view_scores.php">View Scores</a>
            <a href="logout.php" style="color: red;">logout</a>
        </div>
    </div>
    <div class="dashboard-content">
        <h3>Welcome, Teacher !</h3>
        <div class="stats-grid">
            <div class="stat-box">
                <h4>Total  Students</h4>
                <p><?= $total_students ?></p>
            </div>
            <div class="stat-box">
                <h4>Total Exams</h4>
                <p><?= $total_exams ?></p>
            </div>
            <div class="stat-box">
                <h4>Students had Passed Exam</h4>
                <p><?= $students_taken_exam ?></p>
            </div>
        </div>
        <h3>Submissions</h3>
        <?php if (empty($recent_submissions)): ?>
            <p>No submissions.</p>
        <?php else: ?>
            <table border="1">
                <tr>
                    <th>Username</th>
                    <th>Exam</th>
                    <th>Score</th>
                    <th>Submission Date</th>
                </tr>
                <?php foreach ($recent_submissions as $submission): ?>
                    <tr>
                        <td><?= htmlspecialchars($submission['username']) ?></td>
                        <td><?= htmlspecialchars($submission['exam_name']) ?></td>
                        <td><?= htmlspecialchars($submission['score']) ?></td>
                        <td><?= htmlspecialchars($submission['attempt_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>