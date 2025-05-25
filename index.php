<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>QCM System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #FFE2E2, #FFFDEC);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            padding: 40px 50px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 2px solid #FFCFCF;
        }
        h2 {
            color: #86A788;
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            color: #444;
        }
        select {
            padding: 10px 15px;
            font-size: 16px;
            border: 2px solid #FFCFCF;
            border-radius: 8px;
            margin-top: 15px;
            width: 100%;
            max-width: 260px;
            background-color: #FFFDEC;
        }
        input[type="submit"] {
            margin-top: 25px;
            padding: 12px 30px;
            font-size: 16px;
            background-color: #86A788;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #6d8f6f;
        }
    </style>
    
</head>
<body>
    <div class="container">
        <h2>Welcome to the QCM System</h2>
        <form action="login.php" method="get">
            <label>Select your role:</label><br>
            <select name="role" required>
                <option value="">-- Choose your role --</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select><br>
            <label>Select your filiere (students only):</label><br>
            <select name="filiere">
                <option value="">-- Choose your filiere --</option>
                <option value="DSE">Data & Software Engineering</option>
                <option value="Master">Master</option>
                <option value="DS">Data Science</option>
            </select><br>
            <input type="submit" value="Continue">
        </form>
    </div>
</body>
</html>