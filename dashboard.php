<?php
// RENEWBEE/Eco_Inspector/dashboard.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* Start session safely */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* 🔒 Protect Eco Inspector dashboard (must be logged in + correct role) */
if (!isset($_SESSION["user_id"]) || ($_SESSION["user_role"] ?? "") !== "Eco_Inspector") {
    header("Location: ../login.php");
    exit;
}

/*  DB connection (keep your existing config) */
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Eco Inspector Dashboard | ReNewBee</title>

  <!--  Correct merged CSS path -->
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
  <!--  REAL LOGOUT (destroys session + redirects to login) -->
  <a href="../logout.php">🚪 Logout</a>
</div>

<div class="dashboard-container">

  <div class="profile-card">
    <img src="../assets/image/logo.png" alt="ReNewBee Logo">
    <div class="profile-info">
      <h2>Eco Inspector</h2>
      <p class="role">Environmental Compliance</p>
    </div>
  </div>

  <div class="dashboard-cards">

    <div class="dashboard-card">
      <div style="font-size:32px; margin-bottom:10px;"></div>
      <h3>Verify Attendance</h3>
      <p>Review photo and video proof.</p>
      <a href="verify_attendance.php" class="ticket-btn">Go</a>
    </div>

    <div class="dashboard-card">
      <div style="font-size:32px; margin-bottom:10px;">♻️</div>
      <h3>Validate Submissions</h3>
      <p>Check authenticity of recycling projects.</p>
      <a href="review_submissions.php" class="ticket-btn">Go</a>
    </div>

    <div class="dashboard-card">
      <div style="font-size:32px; margin-bottom:10px;">👤</div>
      <h3>My Profile</h3>
      <p>View and manage your account details.</p>
      <a href="profile.php" class="ticket-btn">Go</a>
    </div>

  </div>

</div>

</body>
</html>
