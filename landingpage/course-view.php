<?php
// course-view.php (temporary placeholder)
$courseId = $_GET['course_id'] ?? 'not set';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course View</title>
</head>
<body>
  <h2 style="text-align:center; padding:100px;">
    📘 Course ID: <?= htmlspecialchars($courseId) ?>
  </h2>
  <p style="text-align:center;">(This is a placeholder. You'll design this page later.)</p>
</body>
</html>
