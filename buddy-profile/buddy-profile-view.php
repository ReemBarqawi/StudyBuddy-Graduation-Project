<?php
session_start();
include '../includes/db_connect.php'; // Adjust as needed

// ✅ Block access if user is not logged in
if (!isset($_SESSION['User_ID']) || !isset($_SESSION['Role'])) {
    header("Location: ../signin.php");
    exit;
}

// ✅ Redirect student to their profile view (not allowed to access buddy page)
if ($_SESSION['Role'] === 'student') {
    header("Location: ../student-profile/student-profile-view.php");
    exit;
}

// ✅ Determine which profile to show
if (isset($_GET['id'])) {
    $userId = (int) $_GET['id']; // viewing other buddy
} else {
    $userId = $_SESSION['User_ID']; // logged-in buddy viewing own profile
}

// ✅ Fetch buddy info
$buddyQuery = $conn->prepare("SELECT * FROM users WHERE User_ID = ? AND Role = 'buddy'");
$buddyQuery->bind_param("i", $userId);
$buddyQuery->execute();
$buddy = $buddyQuery->get_result()->fetch_assoc();

if (!$buddy) {
    die("Buddy not found.");
}

$profileImagePath = (!empty($buddy['Image']))
    ? htmlspecialchars($buddy['Image'])
    : 'https://www.pngmart.com/files/23/Profile-PNG-Photo.png';


// ✅ Total Courses
$stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE User_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$total_courses = $stmt->get_result()->fetch_row()[0];

// ✅ Approved / Pending / Rejected Counts
$approvedCount = $conn->query("SELECT COUNT(*) FROM courses WHERE User_ID = $userId AND status = 'approved'")->fetch_row()[0];
$pendingCount = $conn->query("SELECT COUNT(*) FROM courses WHERE User_ID = $userId AND status = 'pending'")->fetch_row()[0];
$rejectedCount = $conn->query("SELECT COUNT(*) FROM courses WHERE User_ID = $userId AND status = 'rejected'")->fetch_row()[0];

// ✅ Total Enrolled Students
$stmt = $conn->prepare("SELECT COUNT(*) FROM enrollment e JOIN courses c ON e.Course_ID = c.course_id WHERE c.User_ID = ? AND e.Payment = 'completed'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$total_students = $stmt->get_result()->fetch_row()[0];

// ✅ Total Earnings
$stmt = $conn->prepare("SELECT SUM(c.price) FROM enrollment e JOIN courses c ON e.Course_ID = c.course_id WHERE c.User_ID = ? AND e.Payment = 'completed'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$row = $stmt->get_result()->fetch_row();
$total_earnings = $row[0] ?? 0;

// ✅ All Courses
$stmt = $conn->prepare("SELECT * FROM courses WHERE User_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ✅ Course Stats (students, rating, review count)
$courseStats = [];
$statQuery = $conn->prepare("
    SELECT c.course_id, COUNT(e.User_ID) AS student_count, AVG(e.Rate) AS avg_rating, COUNT(e.Rate) AS review_count
    FROM courses c
    LEFT JOIN enrollment e ON e.Course_ID = c.course_id AND e.Payment = 'completed'
    WHERE c.User_ID = ?
    GROUP BY c.course_id
");
$statQuery->bind_param("i", $userId);
$statQuery->execute();
$results = $statQuery->get_result()->fetch_all(MYSQLI_ASSOC);
foreach ($results as $row) {
    $courseStats[$row['course_id']] = $row;
}

// ✅ Enrollment chart data
$enrollmentQuery = $conn->prepare("
    SELECT c.name AS course_name, COUNT(e.User_ID) AS total_enrolled 
    FROM enrollment e 
    JOIN courses c ON e.Course_ID = c.course_id 
    WHERE c.User_ID = ? AND c.status = 'approved' 
    GROUP BY e.Course_ID
");

$enrollmentQuery->bind_param("i", $userId);
$enrollmentQuery->execute();
$enrollments = $enrollmentQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// ✅ Paid vs Free chart data
$paidFreeQuery = $conn->prepare("SELECT 
    SUM(CASE WHEN price = 0 THEN 1 ELSE 0 END) AS free_courses,
    SUM(CASE WHEN price > 0 THEN 1 ELSE 0 END) AS paid_courses
    FROM courses WHERE User_ID = ? AND status = 'approved'");
$paidFreeQuery->bind_param("i", $userId);
$paidFreeQuery->execute();
$priceStats = $paidFreeQuery->get_result()->fetch_assoc();

// ✅ Reviews
$reviewStmt = $conn->prepare("
    SELECT e.Rate, e.Comment, e.Date, u.First_name, u.Last_name, u.Image
    FROM enrollment e
    JOIN courses c ON e.Course_ID = c.course_id
    JOIN users u ON e.User_ID = u.User_ID
    WHERE c.User_ID = ? AND e.Payment = 'completed' AND e.Comment IS NOT NULL AND e.Rate IS NOT NULL
    ORDER BY e.Date DESC LIMIT 5
");
$reviewStmt->bind_param("i", $userId);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ✅ Rating summary
$ratingSummary = $conn->prepare("SELECT AVG(e.Rate) as avg_rating, COUNT(*) as count FROM enrollment e JOIN courses c ON e.Course_ID = c.course_id WHERE c.User_ID = ? AND e.Payment = 'completed' AND e.Rate IS NOT NULL");
$ratingSummary->bind_param("i", $userId);
$ratingSummary->execute();
$ratingStats = $ratingSummary->get_result()->fetch_assoc();

 include '../components/navbar-buddy.php'; 

?>



<head>
  <link rel="stylesheet" href="buddy-style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>     <!-- Chart.js Library-->
</head>

  <script>
function scrollCourses() {
  const grid = document.getElementById('coursesGrid');
  grid.scrollBy({ left: 320, behavior: 'smooth' });
}
</script>

<body>
  <main class="dashboard-content">
    <section class="welcome-section">
      <div class="welcome-container">
      <h1 class="welcome-heading">Welcome back, <?= htmlspecialchars($buddy['First_name'] . ' ' . $buddy['Last_name']) ?>!</h1>

        <button class="create-course-btn" onclick="location.href='../creatcourse/create-course-basic-info.php'">
          Create New Course</button>

      </div>
<!-- Course Status Cards -->
<div style="display: flex; gap: 20px; margin-top: 20px;">

  <!-- Approved -->
  <div style="background-color: #e0f7e9; padding: 20px; border-radius: 8px; width: 180px; text-align: center;">
    <h2 style="margin: 0; color: #28a745;"><?php echo $approvedCount; ?></h2>
    <p style="margin: 5px 0; color: #155724;">Approved Courses</p>
  </div>

  <!-- Pending -->
  <div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; width: 180px; text-align: center;">
    <h2 style="margin: 0; color: #856404;"><?php echo $pendingCount; ?></h2>
    <p style="margin: 5px 0; color: #856404;">Pending Courses</p>
  </div>

  <!-- Rejected -->
  <div style="background-color: #f8d7da; padding: 20px; border-radius: 8px; width: 180px; text-align: center;">
    <h2 style="margin: 0; color: #721c24;"><?php echo $rejectedCount; ?></h2>
    <p style="margin: 5px 0; color: #721c24;">Rejected Courses</p>
  </div>

</div>
    </section>
  
    <div class="dashboard-layout">
      <div class="main-content">
        <section class="summary-section">
          <h2 class="section-title">Students Summary</h2>
          <div class="summary-cards">
            <div class="summary-card">
            <span class="summary-number highlight-blue"><?= $total_students ?></span>

              <p class="summary-label">Total Enrolled Students</p>
            </div>
            <div class="summary-card">
            <span class="summary-number highlight-orange">$<?= number_format($total_earnings, 2) ?></span>

              <p class="summary-label">Total Earnings</p>
            </div>
          </div>
        </section>
  
        <section class="profile-section">
          <h2 class="section-title">profile information</h2>
          <div class="profile-card">
          <?php
$defaultProfile = 'https://www.pngmart.com/files/23/Profile-PNG-Photo.png';

$profileImage = (!empty($buddy['Image']))
    ? '../' . $buddy['Image']
    : $defaultProfile;
?>
<img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="profile-picture">



            <div class="profile-details">
              <div class="profile-header">
              <h3 class="profile-name"><?= htmlspecialchars($buddy['First_name'] . ' ' . $buddy['Last_name']) ?></h3>

                <button class="edit-profile-btn" onclick="location.href='../editpage/edit-my-profile.php'"" >
                  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/bf333920d1b93f1aa08b0dc4a01d8cefa6a34f22?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Edit" class="edit-icon" />
                  <span>Edit Profile</span>
                </button>
              </div>
              <h4 class="profile-bio-title">Bio</h4>
              <p class="profile-bio-text"><?= nl2br(htmlspecialchars($buddy['Bio'])) ?: 'No bio added.' ?></p>

            </div>
          </div>
        </section>
  
        <section class="performance-section">
  <h2 class="section-title">Performance Overview</h2>
  <div class="chart-headers">
    <h3 class="chart-title">Total Student Enrollment by Course</h3>
    <h3 class="chart-title">Paid vs. Free Course Enrollment</h3>
  </div>
  <div class="charts-container">
    <!-- 📊 Bar Chart -->
    <div class="enrollment-chart">
      <canvas id="enrollmentChart" height="250"></canvas>
    </div>

    <!-- 🍩 Doughnut Chart -->
    <div class="pie-chart">
      <canvas id="paidFreeChart" height="250"></canvas>
    </div>
  </div>
</section>


  


<!-- ===================== My Courses Section ===================== -->
<section class="courses-section">
  <h2 class="section-title">My Courses</h2>
  <div class="courses-grid" id="coursesGrid">
    <?php foreach ($courses as $course): ?>
      <?php
        $course_id = $course['course_id'];
        $rawRating = $courseStats[$course_id]['avg_rating'] ?? null;
        $rating = is_numeric($rawRating) ? number_format($rawRating, 1) : '0.0';
        $reviewCount = $courseStats[$course_id]['review_count'] ?? 0;
        $students = $courseStats[$course_id]['student_count'] ?? 0;
        $thumbnail = !empty($course['thumbnail']) && file_exists("../creatcourse/uploads/thumbnails/" . $course['thumbnail']) 
        ? '../creatcourse/uploads/thumbnails/' . htmlspecialchars($course['thumbnail']) 
        : '../creatcourse/uploads/thumbnails/media-photo.webp'; // ✅ Custom placeholder
        ?>
      <article class="course-card">
        <div class="course-image-container">
          
           <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['name']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
        
        </div>
        <div class="course-details">
          <h3 class="course-title"><?= htmlspecialchars($course['name']) ?></h3>

          <div class="course-rating">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/991a32f70bf3a3faa772a63f05fdb4189962a568?placeholderIfAbsent=true" class="rating-icon" />
            <span class="rating-text"><?= $rating ?><span class="rating-count">(<?= $reviewCount ?>)</span></span>
          </div>

          <div class="course-stats">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/518a6a8f1e51047cca6c267ef1e0acce7572ddbb?placeholderIfAbsent=true" class="students-icon" />
            <p class="students-count">
              <span class="stats-label">Students Enrolled:</span>
              <span class="stats-value"><?= $students ?></span>
            </p>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===================== Reviews Section ===================== -->
<aside class="reviews-sidebar">
  <div class="reviews-container">
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/877ee75635bab45afd1ed3fcccbd89444b9a7cc5" alt="Reviews Divider" class="reviews-divider" />
    <h2 class="reviews-title">Reviews</h2>

    <?php if (!empty($reviews)): ?>
      <div class="reviews-summary">
        <div class="rating-header">
          <span class="rating-score"><?= number_format($ratingStats['avg_rating'], 1) ?>/5</span>
          <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d15c975b4aca860c77c54b30ff2612a174f3ef20" alt="Rating Stars" class="rating-stars" />
        </div>
        <p class="total-reviews">(<?= $ratingStats['count'] ?> reviews)</p>
      </div>

      <?php foreach ($reviews as $review): ?>
        <article class="review-card">
          <div class="reviewer-info">
          <img 
  src="<?= htmlspecialchars(!empty($review['Image']) && file_exists('../uploads/' . $review['Image']) ? '../uploads/' . $review['Image'] : 'https://www.pngmart.com/files/23/Profile-PNG-Photo.png') ?>" 
  alt="Reviewer" 
  class="reviewer-image" 
/>

            <h3 class="reviewer-name"><?= htmlspecialchars($review['First_name'] . ' ' . $review['Last_name']) ?></h3>
          </div>
          <p class="review-text"><?= htmlspecialchars($review['Comment']) ?></p>
          <p class="review-date"><?= htmlspecialchars(date('F j, Y', strtotime($review['Date']))) ?></p>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="review-text">No reviews available.</p>
    <?php endif; ?>
  </div>
</aside>

    </div>
  </main>
  

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 📊 Bar Chart - Enrollment by Course
const enrollmentLabels = <?= json_encode(array_column($enrollments, 'course_name')) ?>;
const enrollmentData = <?= json_encode(array_column($enrollments, 'total_enrolled')) ?>;

new Chart(document.getElementById('enrollmentChart'), {
  type: 'bar',
  data: {
    labels: enrollmentLabels,
    datasets: [{
      label: 'Enrolled Students',
      data: enrollmentData,
      backgroundColor: '#379ae6'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          precision: 0
        }
      }
    }
  }
});

// 🍩 Doughnut Chart - Paid vs Free Courses
new Chart(document.getElementById('paidFreeChart'), {
  type: 'doughnut',
  data: {
    labels: ['Free', 'Paid'],
    datasets: [{
      data: [<?= $priceStats['free_courses'] ?? 0 ?>, <?= $priceStats['paid_courses'] ?? 0 ?>],
      backgroundColor: ['#379ae6', '#faac35']
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'right',
        labels: { color: '#171a1f' }
      }
    }
  }
});
</script>




</body>
  