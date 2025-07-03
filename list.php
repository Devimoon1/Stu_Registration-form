<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$result = $conn->query("SELECT id, username, email, mobile, gender, education, skills, resume FROM stu_details ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User List</title>
<style>
  body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background-color: #f0f2f5;
  }

  h2 {
    color: #FF0000;
    text-align: center;
  }

  table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background-color: #fff;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
  }

  th {
    background-color: #f2f2f2;
  }

  button, .resume-link {
    padding: 5px 10px;
    margin: 0 2px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
  }

  .edit-btn {
    background-color: #4CAF50;
    color: white;
  }

  .delete-btn {
    background-color: #f44336;
    color: white;
  }

  .resume-link {
    background-color: #007BFF;
    color: white;
  }

  .logout-container {
    text-align: center;
    margin-top: 30px;
  }

  .logout-btn {
    background-color: #dc3545;
    color: white;
    padding: 10px 25px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .logout-btn:hover {
    background-color: #c82333;
  }
</style>
</head>
<body>

<h2>Users List</h2>

<table>
  <thead>
    <tr>
      <th>Sl. No</th>
      <th>Username</th>
      <th>Email</th>
      <th>Mobile</th>
      <th>Gender</th>
      <th>Education</th>
      <th>Skills</th>
      <th>Resume</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php $serial = 1; ?>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $serial++ ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['mobile']) ?></td>
      <td><?= htmlspecialchars($row['gender']) ?></td>
      <td><?= htmlspecialchars($row['education']) ?></td>
      <td><?= htmlspecialchars($row['skills']) ?></td>
      <td>
        <?php if (!empty($row['resume']) && file_exists($row['resume'])): ?>
          <a href="<?= htmlspecialchars($row['resume']) ?>" target="_blank" class="resume-link">View Resume</a>
        <?php else: ?>
          No Resume
        <?php endif; ?>
      </td>
      <td>
        <a href="edit.php?id=<?= $row['id'] ?>"><button class="edit-btn">Edit</button></a>
        <form action="delete.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure to delete this user?');">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <button type="submit" class="delete-btn">Delete</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<div class="logout-container">
  <form action="logout.php" method="post">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
