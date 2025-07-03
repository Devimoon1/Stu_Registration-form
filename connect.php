<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $mobile     = trim($_POST['mobile'] ?? '');
    $gender     = $_POST['gender'] ?? '';
    $education  = $_POST['education'] ?? '';
    $skills     = $_POST['skills'] ?? [];

    if (strlen($password) < 8) die('Error: >=8 characters required');
    if (!preg_match('/[a-z]/', $password)) die('Error: at least one lowercase');
    if (!preg_match('/[A-Z]/', $password)) die('Error: at least one uppercase');
    if (!preg_match('/\d/', $password))    die('Error: at least one number');
    if (!preg_match('/[^\w]/', $password)) die('Error: at least one special char');

    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        die('Error: Mobile must be exactly 10 digits');
    }

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
        $allowedTypes = ['pdf', 'doc', 'docx'];
        $fileName = $_FILES['resume']['name'];
        $fileTmp = $_FILES['resume']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedTypes)) {
            die("Error: Invalid file type. Only PDF, DOC, DOCX allowed.");
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create if it doesn't exist
        }

        $newFileName = uniqid('resume_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            die("Error: Resume upload failed.");
        }
    } else {
        die("Error: No resume uploaded.");
    }

    $skillsStr = implode(',', $skills);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT id FROM stu_details WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) die('Error: Username or email taken');
    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO stu_details (username, email, password, mobile, gender, education, skills, resume)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssss", $username, $email, $hash, $mobile, $gender, $education, $skillsStr, $uploadPath);

    if ($stmt->execute()) {
        header("Location:login.php");
        exit;
    } else {
        echo "DB Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>
