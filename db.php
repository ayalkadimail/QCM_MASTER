<?php
$conn = new mysqli("localhost", "root", "", "qcm");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
