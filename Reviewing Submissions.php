<?php
require_once __DIR__ . '/../config/db.php';
session_start();

function esc($v) {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/* ✅ HANDLE APPROVE */
if (isset($_POST['approve'], $_POST['submission_id'])) {
  $id = (int)$_POST['submission_id'];

  $stmt = mysqli_prepare($conn, "
    UPDATE submission
    SET Status='Approved', VerifiedBy=1
    WHERE SubmissionID=?
  ");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
}

/* ✅ HANDLE FLAG */
if (isset($_POST['flag'], $_POST['submission_id'])) {
  $id = (int)$_POST['submission_id'];

  $stmt = mysqli_prepare($conn, "
    UPDATE submission
    SET Status='Flagged', VerifiedBy=1
    WHERE SubmissionID=?
  ");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
}

/* ✅ FETCH PENDING SUBMISSIONS */
$result = mysqli_query($conn, "
  SELECT SubmissionID, ProofImage
  FROM submission
  WHERE Status='Pending'
  ORDER BY SubmissionID DESC
");

if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Validate Recycling Submissions | ReNewBee</title>

  <!-- ✅ Merged CSS path -->
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
    <h3>♻️ Validate Recycling Submissions</h3>

    <table class="styled-table">
      <thead>
        <tr>
          <th>Project Proof</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>

      <?php if (mysqli_num_rows($result) === 0): ?>
        <tr>
          <td colspan="2" style="text-align:center;padding:16px;">
            No pending submissions.
          </td>
        </tr>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <?php
            // ✅ If ProofImage is stored as full path, keep it.
            // ✅ If it's only a filename, we assume it is inside assets/uploads/
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
                <span class="muted">No image</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="post" style="display:flex; gap:10px;">
                <input type="hidden" name="submission_id" value="<?= (int)$row['SubmissionID'] ?>">
                <button type="submit" name="approve" class="ticket-btn">Approve</button>
                <button type="submit" name="flag" class="ticket-btn" style="background:#b00020;">Flag</button>
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
