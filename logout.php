<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Logout</title>
  <style>
    body {
      background-color: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .logout-box {
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
      border: 2px solid #dc3545;
    }

    .logout-message {
      font-size: 20px;
      color: #dc3545;
      font-weight: bold;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }

    .back-link:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="logout-box">
    <div class="logout-message">Logout successfully!</div>
    <a href="login.php" class="back-link">Back to Login</a>
  </div>

</body>
</html>
