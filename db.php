<?php
$conn = new mysqli("localhost", "root", "MyStrongP@ssword123", "qcm");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
