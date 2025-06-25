<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mobile   = trim($_POST['mobile'] ?? '');
    $gender   = $_POST['gender'] ?? '';

    // 1. Password validation (rules broken into clear steps)
    if (strlen($password) < 8) die('Error: >=8 characters required');
    if (!preg_match('/[a-z]/', $password)) die('Error: at least one lowercase');
    if (!preg_match('/[A-Z]/', $password)) die('Error: at least one uppercase');
    if (!preg_match('/\d/', $password))    die('Error: at least one number');
    if (!preg_match('/[^\w]/', $password)) die('Error: at least one special char');

    // 2. Mobile validation
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        die('Error: Mobile must be exactly 10 digits');
    }

    // Hash the password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Duplicate check
    $stmt = $conn->prepare("SELECT id FROM stu_details WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) die('Error: Username or email taken');
    $stmt->close();

    // Insert user
    $stmt = $conn->prepare("
      INSERT INTO stu_details (username, email, password, mobile, gender)
      VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $username, $email, $hash, $mobile, $gender);
    if ($stmt->execute()) {
        header("Location: success.php");
        exit;
    } else {
        echo "DB Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
$conn->close();
