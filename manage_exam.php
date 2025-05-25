<?php
session_start();
require 'db.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "teacher") {
    header("Location: login.php");
    exit;
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['add'])) {
        $question_text = $_POST['question_text'];
        $options = $_POST['options'];
        $correct_option = $_POST['correct'];
        $exam_id = $_POST['exam_id'];

        // Validate inputs
        if (empty($question_text) || count($options) != 4 || !isset($correct_option) || !in_array($correct_option, [0, 1, 2, 3])) {
            $message = "Error: Please provide a question and exactly 4 options with one marked as correct.";
            error_log("Invalid input: question_text=$question_text, options_count=" . count($options) . ", correct_option=$correct_option");
        } else {
            // Insert question
            $stmt = $conn->prepare("INSERT INTO questions (question_text, exam_id) VALUES (?, ?)");
            $stmt->bind_param("si", $question_text, $exam_id);
            if ($stmt->execute()) {
                $question_id = $conn->insert_id;
                // Insert options
                $letters = ['A', 'B', 'C', 'D'];
                foreach ($options as $index => $option_text) {
                    if (empty($option_text)) {
                        $message = "Error: All options must have text.";
                        error_log("Empty option for question_id=$question_id at index=$index");
                        break;
                    }
                    $is_correct = ($correct_option == $index) ? 1 : 0;
                    $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct, option_letter) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isis", $question_id, $option_text, $is_correct, $letters[$index]);
                    $stmt->execute();
                }
                $message = "Question added successfully with ID: $question_id.";
                error_log("Question added: id=$question_id, exam_id=$exam_id, text=$question_text");
            } else {
                $message = "Error adding question: " . $conn->error;
                error_log("Error adding question: " . $conn->error);
            }
        }
    } elseif (isset($_POST['delete'])) {
        $question_id = $_POST['question_id'];
        // Validate question_id
        if (!is_numeric($question_id) || $question_id <= 0) {
            $message = "Error: Invalid question ID.";
            error_log("Invalid question_id on delete: $question_id");
        } else {
            // Start transaction to ensure all deletes succeed
            $conn->begin_transaction();
            try {
                // Delete related data
                $stmt = $conn->prepare("DELETE FROM student_answers WHERE question_id = ?");
                $stmt->bind_param("i", $question_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM exam_access WHERE question_id = ?");
                $stmt->bind_param("i", $question_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM options WHERE question_id = ?");
                $stmt->bind_param("i", $question_id);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
                $stmt->bind_param("i", $question_id);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $conn->commit();
                        $message = "Question ID $question_id deleted successfully.";
                        error_log("Question deleted: id=$question_id");
                    } else {
                        $conn->rollback();
                        $message = "Error: Question ID $question_id not found.";
                        error_log("Question not found: id=$question_id");
                    }
                } else {
                    throw new Exception("Error deleting question: " . $conn->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error deleting question: " . $e->getMessage();
                error_log("Error deleting question ID $question_id: " . $e->getMessage());
            }
        }
    }
}

$exams = $conn->query("SELECT * FROM exams");
$questions = $conn->query("SELECT q.*, e.name as exam_name FROM questions q JOIN exams e ON q.exam_id = e.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Exam</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbar">
        <h2>Edit Exam</h2>
        <div class="nav-links">
            <a href="teacher_dashboard.php">Back to Dashboard</a>
        </div>
    </div>
    <?php if ($message): ?>
        <p style="color: <?= strpos($message, 'Error') === false ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <h3>Add Question</h3>
    <form method="post">
        <label>Exam:</label><br>
        <select name="exam_id" required>
            <?php while ($e = $exams->fetch_assoc()): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
            <?php endwhile; ?>
        </select><br>
        <label>Question:</label><br>
        <textarea name="question_text" required></textarea><br>
        <label>Options (mark one as correct):</label><br>
        <?php $letters = ['A', 'B', 'C', 'D']; ?>
        <?php for ($i = 0; $i < 4; $i++): ?>
            <input type="text" name="options[]" required placeholder="Option <?= $letters[$i] ?>">
            <label>
                <input type="radio" name="correct" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
                Correct
            </label><br>
        <?php endfor; ?>
        <button name="add">Add Question</button>
    </form>
    <h3>Existing Questions</h3>
    <table border="1">
        <tr><th>ID</th><th>Exam</th><th>Question</th><th>Options</th><th>Action</th></tr>
        <?php while ($q = $questions->fetch_assoc()): ?>
            <tr>
                <td><?= $q['id'] ?></td>
                <td><?= htmlspecialchars($q['exam_name']) ?></td>
                <td><?= htmlspecialchars($q['question_text']) ?></td>
                <td>
                    <?php
                    $stmt = $conn->prepare("SELECT option_text, is_correct, option_letter FROM options WHERE question_id = ? ORDER BY option_letter");
                    $stmt->bind_param("i", $q['id']);
                    $stmt->execute();
                    $options = $stmt->get_result();
                    while ($opt = $options->fetch_assoc()) {
                        echo htmlspecialchars($opt['option_letter'] . ': ' . $opt['option_text']) . ($opt['is_correct'] ? " (Correct)" : "") . "<br>";
                    }
                    ?>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                        <button name="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>