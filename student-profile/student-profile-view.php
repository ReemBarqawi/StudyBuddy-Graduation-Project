<?php
session_start();


include '../includes/db_connect.php';
include '../components/navbar-student.php';

if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'student') {
    header("Location: ../signin.php");
    exit;
}

$student_id = $_SESSION['User_ID']; // now dynamic


// Placeholder image if thumbnail is missing
$fallbackImage = 'https://thumbs.dreamstime.com/b/media-photo-file-vector-icon-picture-image-fill-symbol-image-file-media-photo-file-vector-icon-267312726.jpg';

// Fetch student info
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$studentResult = $stmt->get_result();
$student = $studentResult->fetch_assoc();

// Count enrolled courses
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM enrollment WHERE User_ID = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$courseCountResult = $stmt->get_result();
$coursesEnrolled = $courseCountResult->fetch_assoc()['total'];

// Count topics completed
$topicsQuery = "
  SELECT COUNT(*) AS total 
  FROM content_completion cc 
  JOIN content ct ON cc.Content_ID = ct.Content_ID 
  WHERE cc.User_ID = ?
";
$stmt = $conn->prepare($topicsQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$topicsResult = $stmt->get_result();
$topicsCompleted = $topicsResult->fetch_assoc()['total'];


// Fetch enrolled courses with progress and thumbnails
$query = "
  SELECT 
 
  c.course_id AS id,
  c.name AS course_name,
    c.thumbnail,
    (
      SELECT COUNT(*) FROM content WHERE Course_ID = c.course_id
    ) AS total_lessons,
    (
      SELECT COUNT(*) 
      FROM content_completion cc
      JOIN content ct ON cc.Content_ID = ct.Content_ID
      WHERE cc.User_ID = ? AND ct.Course_ID = c.course_id
    ) AS completed_lessons
  FROM courses c
  JOIN enrollment e ON c.course_id = e.Course_ID
  WHERE e.User_ID = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  
  <title>StudyBuddy</title>


  <link rel="stylesheet" href="student-style.css">
</head>
<body>

<section class="welcome-section">
  <h1 class="welcome-heading">Welcome Back, <?= htmlspecialchars($student['First_name'] . ' ' . $student['Last_name']) ?>!</h1>
  <button class="create-course-btn">View Recommended Topics</button>
</section>

<div class="dashboard-layout">

  <!-- Summary Section -->
  <section class="summary-section">
    <h2 class="section-title">Your Learning Summary</h2>
    <div class="summary-cards">
      <div class="summary-card">
        <div class="summary-number highlight-blue"><?= $coursesEnrolled ?></div>
        <div class="summary-label">Courses Enrolled</div>
      </div>
      <div class="summary-card">
        <div class="summary-number highlight-orange"><?= $topicsCompleted ?></div>
        <div class="summary-label">Topics Completed</div>
      </div>
    </div>
  </section>

  <!-- Profile Section -->
  <section class="profile-section">
    <div class="profile-card">
    <?php
$defaultProfile = 'https://www.pngmart.com/files/23/Profile-PNG-Photo.png';

$profileImage = (!empty($student['Image']))
    ? '../' . $student['Image']
    : $defaultProfile;
?>
<img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="profile-picture">



      <div class="profile-details">
        <div class="profile-header">
          <h2 class="profile-name"><?= htmlspecialchars($student['First_name'] . ' ' . $student['Last_name']) ?></h2>
                <button class="edit-profile-btn" onclick="location.href='../editpage/edit-my-profile.php'"" >
                  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/bf333920d1b93f1aa08b0dc4a01d8cefa6a34f22?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Edit" class="edit-icon" />
                  <span>Edit Profile</span>
                </button>
        </div>
        <h3 class="profile-bio-title">About Me</h3>
        <p class="profile-bio-text"><?= htmlspecialchars($student['Bio'] ?? 'No bio provided.') ?></p>
      </div>
    </div>
  </section>


<!-- Chart Section -->
<section class="performance-section">
  <div class="chart-headers">
    <div>Course Progress</div>
    <div></div>
  </div>
  <div class="charts-container">
    <div class="enrollment-chart">
      <canvas id="courseProgressChart" width="400" height="200"></canvas>
    </div>
    <div class="pie-chart" style="display: none;"></div>
  </div>
</section>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('courseProgressChart').getContext('2d');
  const courseLabels = <?= json_encode(array_column($courses, 'course_name')) ?>;
  const courseProgress = <?= json_encode(array_map(function($c) {
    return ($c['total_lessons'] > 0) 
      ? round(($c['completed_lessons'] / $c['total_lessons']) * 100) 
      : 0;
  }, $courses)) ?>;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: courseLabels,
      datasets: [{
        label: 'Progress (%)',
        data: courseProgress,
        backgroundColor: '#379ae6',
        borderRadius: 6,
        borderSkipped: false,
        barThickness: 40,
      }]
    },
    options: {
      indexAxis: 'y',
      scales: {
        x: {
          beginAtZero: true,
          max: 100,
          ticks: {
            callback: function(value) {
              return value + '%';
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.parsed.x + '% complete';
            }
          }
        }
      }
    }
  });
</script>


  <!-- My Courses Section -->
  <section class="courses-section">
    <h2 class="section-title">My Courses</h2>
    <div class="courses-grid">
    <?php foreach ($courses as $course): 
  $progress = ($course['total_lessons'] > 0)
    ? round(($course['completed_lessons'] / $course['total_lessons']) * 100)
    : 0;

  // Final image logic
  $imgPath = (!empty($course['thumbnail']) && file_exists(__DIR__ . "/../creatcourse/uploads/thumbnails/" . $course['thumbnail']))
    ? "https://studybuddy.wuaze.com/creatcourse/uploads/thumbnails/" . urlencode($course['thumbnail'])
    : $fallbackImage;

?>
 <a href="../course-path/course-path.php?course_id=<?= $course['id'] ?>" class="course-link">
   <?= $course['id'] ?> <!-- for debug -->
   <div class="course-card">
    <img src="<?= htmlspecialchars($imgPath) ?>" class="course-image" alt="Course Image">
    <div class="course-details">
      <h3 class="course-title"><?= htmlspecialchars($course['course_name']) ?></h3>
      <div class="course-progress">
        <span><?= $progress ?>% Complete</span>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?= $progress ?>%;"></div>
        </div>
      </div>
    </div>
   </div>
 </a>
<?php endforeach; ?>


    </div>
  </section>

</div> <!-- end dashboard-layout -->

</body>
</html>
