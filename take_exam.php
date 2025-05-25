<?php
session_start();
require 'db.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION["user_id"];
$filiere = $conn->query("SELECT filiere FROM users WHERE id = $student_id")->fetch_assoc()['filiere'] ?? '';

if (!$filiere) {
    $message = "Please select a filiere in your profile.";
    error_log("Student ID $student_id has no filiere set.");
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) as access_count FROM exam_access WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $access_count = $stmt->get_result()->fetch_assoc()['access_count'];
    
    if ($access_count == 0) {
        $message = "You do not have access to any exams.";
        error_log("Student ID $student_id has no exam access in exam_access table.");
    } else {
        $stmt = $conn->prepare("SELECT q.* FROM questions q 
                                JOIN exam_access ea ON q.id = ea.question_id 
                                JOIN exams e ON q.exam_id = e.id 
                                WHERE ea.student_id = ? AND e.filiere = ?");
        $stmt->bind_param("is", $student_id, $filiere);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        if (empty($questions)) {
            $message = "No questions available for your filiere.";
            error_log("No questions found for student ID $student_id, filiere $filiere.");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Exam</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>Take Exam</h2>
        <div class="nav-links">
            <a href="student_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
    <?php if (isset($message)): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php else: ?>
        <form action="submit_exam.php" method="post">
            <?php foreach ($questions as $q): ?>
                <p><strong><?= htmlspecialchars($q['question_text']) ?></strong></p>
                <?php
                $stmt = $conn->prepare("SELECT id, option_text, option_letter FROM options WHERE question_id = ? ORDER BY option_letter");
                $stmt->bind_param("i", $q['id']);
                $stmt->execute();
                $options_result = $stmt->get_result();
                $option_count = $options_result->num_rows;
                if ($option_count == 0) {
                    error_log("No options found for question ID {$q['id']}, question_text: {$q['question_text']}");
                    echo "<p style='color: red;'>Error: No options available for question ID {$q['id']}.</p>";
                    continue; // Skip to the next question
                }
                while ($opt = $options_result->fetch_assoc()): ?>
                    <label>
                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" required>
                        <?= htmlspecialchars($opt['option_letter'] . ': ' . $opt['option_text']) ?>
                    </label><br>
                <?php endwhile; ?>
                <br>
            <?php endforeach; ?>
            <?php if (count($questions) > 0): ?>
                <button type="submit">Submit Exam</button>
            <?php else: ?>
                <p>No questions available for your filiere.</p>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</body>
</html>