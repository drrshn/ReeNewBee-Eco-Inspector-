<?php
require_once __DIR__ . '/../config/db.php';
session_start();

function esc($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/*
  ✅ TEMP USER LOGIC (demo-friendly)
  - session UserID → use it
  - ?uid= → use it
  - fallback → 1
*/
$userID = 1;
if (!empty($_SESSION['UserID'])) {
  $userID = (int)$_SESSION['UserID'];
} elseif (!empty($_GET['uid']) && is_numeric($_GET['uid'])) {
  $userID = (int)$_GET['uid'];
}

/* ✅ Fetch user data safely */
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
  <title>My Profile | Eco Inspector</title>

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
    <h3>👤 My Profile</h3>

    <p><strong>Name:</strong> <?= esc($user['UserName'] ?? '') ?></p>
    <p><strong>Email:</strong> <?= esc($user['UserEmail'] ?? '') ?></p>

    <br>
    <a href="edit_profile.php?uid=<?= (int)$userID ?>" class="ticket-btn">Edit Profile</a>
  </div>
</div>

</body>
</html>
