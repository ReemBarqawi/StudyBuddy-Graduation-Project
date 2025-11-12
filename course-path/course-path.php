<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db_connect.php';

if (!isset($_GET['course_id']) || !isset($_SESSION['User_ID'])) {
    die("Missing course ID or not logged in.");
}

include '../components/navbar-student.php';

$course_id = intval($_GET['course_id']);
$user_id = intval($_SESSION['User_ID']);

// Get course info
$stmt = $conn->prepare("SELECT name FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course_result = $stmt->get_result();
$course = $course_result->fetch_assoc();

// Get course content
$content_query = $conn->prepare("SELECT * FROM content WHERE Course_ID = ?");
$content_query->bind_param("i", $course_id);
$content_query->execute();
$contents = $content_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Auto-mark current content as completed if not already done
$current_content_id = isset($_GET['content_id']) ? intval($_GET['content_id']) : ($contents[0]['Content_ID'] ?? null);
if ($current_content_id) {
    $check = $conn->prepare("SELECT * FROM content_completion WHERE User_ID = ? AND Content_ID = ?");
    $check->bind_param("ii", $user_id, $current_content_id);
    $check->execute();
    $exists = $check->get_result()->num_rows;
    if (!$exists) {
        $insert = $conn->prepare("INSERT INTO content_completion (User_ID, Content_ID, Completed) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $current_content_id);
        $insert->execute();
    }
}

// Get completed content IDs
$completed_ids = [];
$done_query = $conn->prepare("SELECT Content_ID FROM content_completion WHERE User_ID = ? AND Completed = 1");
$done_query->bind_param("i", $user_id);
$done_query->execute();
$done_result = $done_query->get_result();
while ($row = $done_result->fetch_assoc()) {
    $completed_ids[] = $row['Content_ID'];
}

// Get current content object
$current_content = null;
foreach ($contents as $c) {
    if ($c['Content_ID'] == $current_content_id) {
        $current_content = $c;
        break;
    }
}

// Save comment if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && count($completed_ids) === count($contents)) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $update = $conn->prepare("UPDATE enrollment SET Comment = ? WHERE User_ID = ? AND Course_ID = ?");
        $update->bind_param("sii", $comment, $user_id, $course_id);
        $update->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($course['name']) ?> - Course Path</title>
  <link rel="stylesheet" href="course-path-style.css">
</head>
<body>
<div class="main-content">
  <h1><?= htmlspecialchars($course['name']) ?></h1>
  <div class="content-layout">
    <div class="course-content">
      <div class="content-placeholder">
        <?php if ($current_content): ?>
          <h3><?= htmlspecialchars($current_content['Content_name']) ?></h3>
          <p>Type: <?= htmlspecialchars($current_content['Content_type']) ?></p>

          <?php
            $link = '../uploads/content/' . $current_content['Content_link'];
            $type = strtolower($current_content['Content_type']);

            if ($type === 'pdf') {
                echo "<iframe src='$link' width='100%' height='500px'></iframe>";
                echo "<br><a href='$link' download>📥 Download PDF</a>";
            } elseif ($type === 'video') {
                echo "<video controls width='100%'><source src='$link' type='video/mp4'></video>";
            } elseif ($type === 'image') {
                echo "<img src='$link' alt='image' style='max-width:100%;'><br>";
                echo "<a href='$link' download>📥 Download Image</a>";
            } elseif ($type === 'link') {
                if (strpos($link, 'youtube') !== false) {
                    preg_match('/(?:v=|youtu\\.be\/)([a-zA-Z0-9_-]+)/', $link, $matches);
                    if (isset($matches[1])) {
                        $videoId = $matches[1];
                        echo "<iframe width='100%' height='400' src='https://www.youtube.com/embed/$videoId' frameborder='0' allowfullscreen></iframe>";
                    } else {
                        echo "<a href='$link' target='_blank'>Watch on YouTube</a>";
                    }
                } else {
                    echo "<a href='$link' target='_blank'>Open External Link</a>";
                }
            } else {
                echo "<a href='$link' download>📥 Download File</a>";
            }
          ?>
        <?php else: ?>
          <p style="color:red;">⚠ No content available.</p>
        <?php endif; ?>
      </div>
    </div>

    <aside class="progress-sidebar">
      <h2>Lessons</h2>
      <div class="progress-items">
        <?php foreach ($contents as $c): ?>
          <div class="progress-item">
            <a href="?course_id=<?= $course_id ?>&content_id=<?= $c['Content_ID'] ?>">
              <?= htmlspecialchars($c['Content_name']) ?>
            </a>
            <img src="../assets/<?= in_array($c['Content_ID'], $completed_ids) ? 'tick-icon.png' : 'gray-tick-icon.png' ?>" class="complete-icon" alt="status">
          </div>
        <?php endforeach; ?>
      </div>
    </aside>
  </div>

  <?php if (count($completed_ids) === count($contents)): ?>
    <div class="comment-section">
      <h3>Leave a Review</h3>
      <form method="POST">
        <textarea name="comment" placeholder="Write your comment..."></textarea>
        <br>
        <button type="submit">Submit</button>
      </form>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
