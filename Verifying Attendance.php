<?php
require_once __DIR__ . '/../config/db.php';
session_start();

function esc($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/*
  ✅ IMPORTANT:
  Use ONE correct table name.
  Your SELECT uses: event_attendance
  So we will also UPDATE: event_attendance
*/

/* ✅ HANDLE APPROVE */
if (isset($_POST['approve'], $_POST['attendance_id'])) {
  $id = (int)$_POST['attendance_id'];

  $stmt = mysqli_prepare($conn, "
    UPDATE event_attendance
    SET Status='Approved', VerifiedBy=1
    WHERE EventAttendanceID=?
  ");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
}

/* ✅ HANDLE REJECT */
if (isset($_POST['reject'], $_POST['attendance_id'])) {
  $id = (int)$_POST['attendance_id'];

  $stmt = mysqli_prepare($conn, "
    UPDATE event_attendance
    SET Status='Rejected', VerifiedBy=1
    WHERE EventAttendanceID=?
  ");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
}

/* ✅ FETCH PENDING ATTENDANCE */
$result = mysqli_query($conn, "
  SELECT EventAttendanceID, ProofImage
  FROM event_attendance
  WHERE Status='Pending'
  ORDER BY EventAttendanceID DESC
");

if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify Attendance | ReNewBee</title>

  <!-- ✅ merged css -->
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
    <h3>✅ Verify Attendance Proof</h3>

    <table class="styled-table">
      <thead>
        <tr>
          <th>Attendance Proof</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>

      <?php if (mysqli_num_rows($result) === 0): ?>
        <tr>
          <td colspan="2" style="text-align:center;padding:16px;">
            No pending attendance records.
          </td>
        </tr>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <?php
            // ✅ If ProofImage is only a filename, assume assets/uploads/
            $proof = $row['ProofImage'] ?? '';
            if ($proof !== '' && !preg_match('~^(https?://|/|\.{1,2}/)~', $proof)) {
              $proof = "../assets/uploads/" . $proof;
            }
          ?>
          <tr>
            <td>
              <?php if ($proof): ?>
                <img src="<?= esc($proof) ?>" width="80" style="border-radius:8px;">
              <?php else: ?>
                <span class="muted">No proof image</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" style="display:flex; gap:10px;">
                <input type="hidden" name="attendance_id" value="<?= (int)$row['EventAttendanceID'] ?>">
                <button type="submit" name="approve" class="ticket-btn">Approve</button>
                <button type="submit" name="reject" class="ticket-btn" style="background:#b00020;">Reject</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php endif; ?>

      </tbody>
    </table>

  </div>

</div>

</body>
</html>
