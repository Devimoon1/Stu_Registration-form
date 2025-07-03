<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("User ID missing");
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = $_POST['username'];
    $email     = $_POST['email'];
    $mobile    = $_POST['mobile'];
    $gender    = $_POST['gender'];
    $education = $_POST['education'];
    $skills    = $_POST['skills'] ?? [];

    $skillsStr = implode(',', $skills);

    $resumePath = null;
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
            mkdir($uploadDir, 0755, true);
        }

        $newFileName = uniqid('resume_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            die("Error: Resume upload failed.");
        }

        $resumePath = $uploadPath;
    }
    if (!$resumePath) {
        $stmt = $conn->prepare("SELECT resume FROM stu_details WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($resumePath);
        $stmt->fetch();
        $stmt->close();
    }
    $stmt = $conn->prepare("
        UPDATE stu_details SET
            username = ?,
            email = ?,
            mobile = ?,
            gender = ?,
            education = ?,
            skills = ?,
            resume = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssssssi", $username, $email, $mobile, $gender, $education, $skillsStr, $resumePath, $id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error updating record: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
$stmt = $conn->prepare("SELECT username, email, mobile, gender, education, skills, resume FROM stu_details WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    die("User not found");
}
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
$userSkills = explode(',', $user['skills']);
?>

<form method="post" enctype="multipart/form-data">
  <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></label><br>
  <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
  <label>Mobile: <input type="text" name="mobile" pattern="\d{10}" value="<?= htmlspecialchars($user['mobile']) ?>" required></label><br>

  <label>Gender:</label>
  <label><input type="radio" name="gender" value="Male" <?= ($user['gender'] === 'Male') ? 'checked' : '' ?>> Male</label>
  <label><input type="radio" name="gender" value="Female" <?= ($user['gender'] === 'Female') ? 'checked' : '' ?>> Female</label>
  <label><input type="radio" name="gender" value="Other" <?= ($user['gender'] === 'Other') ? 'checked' : '' ?>> Other</label><br><br>

  <label>Education Level:</label>
  <select name="education" required>
    <option value="">--Select--</option>
    <?php
    $educationLevels = ["High School", "Diploma", "Bachelor's", "Master's", "PhD"];
    foreach ($educationLevels as $level) {
        $selected = ($user['education'] === $level) ? 'selected' : '';
        echo "<option value=\"" . htmlspecialchars($level) . "\" $selected>$level</option>";
    }
    ?>
  </select><br><br>

  <label>Skills:</label><br>
  <?php
  $allSkills = ["PHP", "PYTHON", "JAVA", "C++"];
  foreach ($allSkills as $skill) {
      $checked = in_array($skill, $userSkills) ? 'checked' : '';
      echo "<label><input type=\"checkbox\" name=\"skills[]\" value=\"$skill\" $checked> $skill</label><br>";
  }
  ?><br>

  <label>Current Resume:</label>
  <?php if (!empty($user['resume']) && file_exists($user['resume'])): ?>
    <a href="<?= htmlspecialchars($user['resume']) ?>" target="_blank">View Resume</a><br><br>
  <?php else: ?>
    No resume uploaded.<br><br>
  <?php endif; ?>

  <label>Upload New Resume (optional):</label><br>
  <input type="file" name="resume" accept=".pdf,.doc,.docx"><br><br>

  <button type="submit">Update</button>
</form>
