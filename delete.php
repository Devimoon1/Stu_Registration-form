<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM stu_details WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error deleting user.";
    }
    $stmt->close();
} else {
    header("Location: list.php");
    exit;
}
