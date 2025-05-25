<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION["user_id"];

// Check if student has already taken the exam
$stmt = $conn->prepare("SELECT COUNT(*) as attempt_count FROM student_scores WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attempt_count = $stmt->get_result()->fetch_assoc()['attempt_count'];

if ($attempt_count > 0) {
    // Check for approved retake request
    $stmt = $conn->prepare("SELECT approved FROM retake_requests WHERE student_id = ? AND approved = 1");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $retake_approved = $stmt->get_result()->num_rows > 0;

    if (!$retake_approved) {
        header("Location: view_student_score.php?error=already_taken");
        exit;
    }
    // Clear previous answers and scores for retake
    $stmt = $conn->prepare("DELETE FROM student_answers WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt = $conn->prepare("DELETE FROM student_scores WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    // Clear retake request
    $stmt = $conn->prepare("DELETE FROM retake_requests WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
}

$answers = $_POST["answers"] ?? [];
$score = 0;
$total_questions = 0;

foreach ($answers as $question_id => $selected_option_id) {
    // Verify the selected option is valid and correct
    $stmt = $conn->prepare("SELECT is_correct FROM options WHERE id = ? AND question_id = ?");
    $stmt->bind_param("ii", $selected_option_id, $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        error_log("Invalid option ID $selected_option_id for question ID $question_id by student ID $student_id");
        continue; // Skip invalid options
    }
    $option = $result->fetch_assoc();
    $is_correct = $option['is_correct'] ?? 0;
    $score += $is_correct ? 1 : -1; // Canadian scoring: +1 for correct, -1 for incorrect
    $total_questions++;

    // Store the answer in student_answers
    $stmt = $conn->prepare("INSERT INTO student_answers (student_id, question_id, selected_option_id, is_correct) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $student_id, $question_id, $selected_option_id, $is_correct);
    $stmt->execute();
}

// Store the total score in student_scores
if ($total_questions > 0) {
    $stmt = $conn->prepare("INSERT INTO student_scores (student_id, score, attempt_time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $student_id, $score);
    $stmt->execute();
} else {
    error_log("No valid answers submitted by student ID $student_id");
}

$stmt->close();
header("Location: view_student_score.php");
exit;
?>