<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    header("Location: login.php");
    exit;
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add"])) {
        $username = $_POST["username"];
        $password = md5($_POST["password"]);
        $filiere = $_POST["filiere"];

        // Validate filiere
        if (!in_array($filiere, ['DSE', 'Master', 'DS'])) {
            $message = "Error: Invalid or missing filiere.";
            error_log("Invalid filiere on student add: $filiere");
        } else {
            // Check for duplicate username
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $message = "Error: Username already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, password, role, filiere) VALUES (?, ?, 'student', ?)");
                $stmt->bind_param("sss", $username, $password, $filiere);
                if ($stmt->execute()) {
                    $message = "Student added successfully.";
                } else {
                    $message = "Error adding student: " . $conn->error;
                    error_log("Error adding student: " . $conn->error);
                }
            }
        }
    } elseif (isset($_POST["delete"])) {
        $id = $_POST["student_id"];
        $stmt = $conn->prepare("DELETE FROM exam_access WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Student deleted successfully.";
        } else {
            $message = "Error deleting student: " . $conn->error;
            error_log("Error deleting student: " . $conn->error);
        }
    } elseif (isset($_POST["add_access"])) {
        $student_id = $_POST["student_id"];
        $stmt = $conn->prepare("SELECT filiere FROM users WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $message = "Error: Student not found.";
            error_log("Student ID $student_id not found.");
        } else {
            $filiere = $result->fetch_assoc()['filiere'];
            if ($filiere) {
                $stmt = $conn->prepare("SELECT id FROM exams WHERE filiere = ? LIMIT 1");
                $stmt->bind_param("s", $filiere);
                $stmt->execute();
                $exam = $stmt->get_result()->fetch_assoc();
                
                if ($exam) {
                    $exam_id = $exam['id'];
                    $stmt = $conn->prepare("SELECT id FROM questions WHERE exam_id = ?");
                    $stmt->bind_param("i", $exam_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $question_count = 0;
                    while ($row = $result->fetch_assoc()) {
                        $stmt = $conn->prepare("INSERT INTO exam_access (student_id, question_id) VALUES (?, ?)");
                        $stmt->bind_param("ii", $student_id, $row['id']);
                        if ($stmt->execute()) {
                            $question_count++;
                        } else {
                            error_log("Failed to insert exam_access for student_id $student_id, question_id {$row['id']}: " . $conn->error);
                        }
                    }
                    if ($question_count > 0) {
                        $message = "Exam access granted for $filiere exam ($question_count questions).";
                    } else {
                        $message = "Error: No questions found for $filiere exam.";
                        error_log("No questions found for exam_id $exam_id, filiere $filiere.");
                    }
                } else {
                    $message = "Error: No exam found for filiere $filiere.";
                    error_log("No exam found for filiere $filiere.");
                }
            } else {
                $message = "Error: Student has no filiere assigned.";
                error_log("Student ID $student_id has no filiere assigned.");
            }
        }
        header("Location: manage_student.php?message=" . urlencode($message));
        exit;
    } elseif (isset($_POST["remove_access"])) {
        $student_id = $_POST["student_id"];
        $stmt = $conn->prepare("DELETE FROM exam_access WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $message = "Exam access removed.";
        header("Location: manage_student.php?message=" . urlencode($message));
        exit;
    }
}

$students_no_access = $conn->query("SELECT u.* FROM users u 
                                    LEFT JOIN exam_access ea ON u.id = ea.student_id 
                                    WHERE u.role = 'student' AND ea.student_id IS NULL");
$students_with_access = $conn->query("SELECT DISTINCT u.* FROM users u 
                                      JOIN exam_access ea ON u.id = ea.student_id 
                                      WHERE u.role = 'student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>Manage Students</h2>
        <div class="nav-links">
            <a href="teacher_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
    <?php if ($message || isset($_GET['message'])): ?>
        <p style="color: <?= strpos($message ?: $_GET['message'], 'Error') === false ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message ?: $_GET['message']) ?>
        </p>
    <?php endif; ?>
    <h3>Add Student</h3>
    <form method="post">
        <label>Username: <input name="username" required></label>
        <label>Password: <input name="password" type="password" required></label>
        <label>Filiere:
            <select name="filiere" required>
                <option value="DSE">Data & Software Engineering</option>
                <option value="Master">Master</option>
                <option value="DS">Data Science</option>
            </select>
        </label>
        <button name="add">Add Student</button>
    </form>
    <h3>Students Without Exam Access</h3>
    <table border="1">
        <tr><th>ID</th><th>Username</th><th>Filiere</th><th>Action</th></tr>
        <?php while ($s = $students_no_access->fetch_assoc()): ?>
            <tr>
                <td><?= $s["id"] ?></td>
                <td><?= htmlspecialchars($s["username"]) ?></td>
                <td><?= htmlspecialchars($s["filiere"] ?: 'None') ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="student_id" value="<?= $s["id"] ?>">
                        <button name="add_access">Grant Access</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <h3>Students With Exam Access</h3>
    <table border="1">
        <tr><th>ID</th><th>Username</th><th>Filiere</th><th>Action</th></tr>
        <?php while ($s = $students_with_access->fetch_assoc()): ?>
            <tr>
                <td><?= $s["id"] ?></td>
                <td><?= htmlspecialchars($s["username"]) ?></td>
                <td><?= htmlspecialchars($s["filiere"] ?: 'None') ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="student_id" value="<?= $s["id"] ?>">
                        <button name="remove_access">Remove Access</button>
                        <button name="delete">Delete Student</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>