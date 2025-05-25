<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = md5($_POST["password"] ?? '');
    $selected_role = $_GET["role"] ?? $_POST["role"] ?? '';
    $filiere = $_POST["filiere"] ?? $_GET["filiere"] ?? '';

    // Debugging: Log the input values
    error_log("Login attempt: username=$username, password_hash=$password, role=$selected_role, filiere=$filiere");

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo "Database error. Please try again later.";
        exit;
    }
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $row = $res->fetch_assoc();
        if ($selected_role && $row["role"] != $selected_role) {
            echo "Selected role does not match user role.";
        } else {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["role"] = $row["role"];
            if ($row["role"] == "student" && $filiere) {
                $stmt = $conn->prepare("UPDATE users SET filiere = ? WHERE id = ?");
                $stmt->bind_param("si", $filiere, $row["id"]);
                $stmt->execute();
            }
            error_log("Login successful for user: $username, role: {$row['role']}");
            if ($row["role"] == "student") {
                header("Location: student_dashboard.php");
            } else {
                header("Location: teacher_dashboard.php");
            }
            exit;
        }
    } else {
        // Debugging: Check why login failed
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            error_log("Login failed: Username $username not found");
            echo "Login failed: Username not found.";
        } else {
            $db_password = $res->fetch_assoc()['password'];
            error_log("Login failed: Password mismatch for $username. Input hash: $password, DB hash: $db_password");
            echo "Login failed: Incorrect password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #FFE2E2, #FFFDEC);
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Login</h2>
        <div class="nav-links">
            <a href="index.php">Back</a>
        </div>
    </div>
    <form method="post">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <input type="hidden" name="role" value="<?= htmlspecialchars($_GET['role'] ?? '') ?>">
        <input type="hidden" name="filiere" value="<?= htmlspecialchars($_GET['filiere'] ?? '') ?>">
        <button type="submit">Login</button>
    </form>
</body>
</html>