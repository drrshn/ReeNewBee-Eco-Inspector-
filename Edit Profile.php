<?php
require_once __DIR__ . '/../config/db.php';
session_start();

function esc($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/*
  ✅ TEMP USER LOGIC (same style as your other modules)
  - If session has UserID → use it
  - Else if URL has ?uid= → use it
  - Else fallback to UserID = 1 (demo)
*/
$userID = 1;
if (!empty($_SESSION['UserID'])) {
  $userID = (int)$_SESSION['UserID'];
} elseif (!empty($_GET['uid']) && is_numeric($_GET['uid'])) {
  $userID = (int)$_GET['uid'];
}

/* ✅ UPDATE PROFILE (safe prepared statement) */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');

  if ($name === '' || strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters.";
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
  }

  if (!$errors) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET UserName=?, UserEmail=? WHERE UserID=?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $userID);
    mysqli_stmt_execute($stmt);

    header("Location: profile.php?msg=updated");
    exit();
  }
}

/* ✅ FETCH USER DATA */
$stmt = mysqli_prepare($conn, "SELECT UserName, UserEmail FROM users WHERE UserID=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $userID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);
if (!$user) {
  die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile | Eco Inspector</title>

  <!-- ✅ Correct merged CSS path -->
  <link rel="stylesheet" href="../assets/css/eco.css">
</head>
<body>

<input type="checkbox" id="menu-toggle">
<label for="menu-toggle" class="menu-icon">☰</label>

<div class="side-menu">
  <a href="dashboard.php">🏠 Dashboard</a>
  <a href="verify_attendance.php">✅ Verify Attendance</a>
  <a href="review_submissions.php">♻️ Review Submissions</a>
  <a href="profile.php">👤 Profile</a>
  <hr>
  <a href="../index.php">🚪 Logout</a>
</div>

<div class="dashboard-container">
  <div class="dashboard-card">
    <h3>Edit Profile</h3>

    <?php if ($errors): ?>
      <div class="alert error" style="margin-bottom:14px;">
        <ul style="margin:0;padding-left:18px;">
          <?php foreach ($errors as $e): ?>
            <li><?= esc($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" style="max-width:420px;">

      <div style="margin-bottom:15px;">
        <label>Name</label>
        <input
          type="text"
          name="name"
          value="<?= esc($user['UserName'] ?? '') ?>"
          required
          style="width:100%; padding:8px;"
        >
      </div>

      <div style="margin-bottom:20px;">
        <label>Email</label>
        <input
          type="email"
          name="email"
          value="<?= esc($user['UserEmail'] ?? '') ?>"
          required
          style="width:100%; padding:8px;"
        >
      </div>

      <button type="submit" name="update" class="ticket-btn">
        Save Changes
      </button>

      <a href="profile.php" style="margin-left:10px;">Cancel</a>

    </form>
  </div>
</div>

</body>
</html>
