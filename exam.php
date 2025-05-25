<?php
session_start();
include 'db.php';
if ($_SESSION["role"] != "student") { header("Location: login.php"); exit; }

$res = $conn->query("SELECT * FROM exams");
?>

<form method="post" action="submit_exam.php">
    <h3>QCM Exam</h3>
    <?php while ($row = $res->fetch_assoc()): ?>
        <p><?= $row["question"] ?></p>
        <label><input type="radio" name="q<?= $row["id"] ?>" value="A"> <?= $row["option_a"] ?></label><br>
        <label><input type="radio" name="q<?= $row["id"] ?>" value="B"> <?= $row["option_b"] ?></label><br>
        <label><input type="radio" name="q<?= $row["id"] ?>" value="C"> <?= $row["option_c"] ?></label><br>
        <label><input type="radio" name="q<?= $row["id"] ?>" value="D"> <?= $row["option_d"] ?></label><br><br>
    <?php endwhile; ?>
    <button type="submit">Submit</button>
</form>
<link rel="stylesheet" href="style.css">
