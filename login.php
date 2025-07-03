<?php

session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM stu_details WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header("Location: list.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Login</title>
<style>
body {
  background: #f4f7f8;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.login-box {
  background: #ADD8E6;
  padding: 40px 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  width: 350px;
}

h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #333;
}

label {
  font-weight: bold;
  display: block;
  margin: 10px 0 5px;
}

input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #007BFF;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s ease;
}

button:hover {
  background-color: #0056b3;
}

.error-msg {
  color: red;
  text-align: center;
  margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="login-box">
  <h2>Login</h2>
  <?php if (!empty($error)): ?>
    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form action="" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
  </form>
</div>

</body>
</html>
