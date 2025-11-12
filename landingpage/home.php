<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include '../includes/db_connect.php';
$query = "
  SELECT 
    c.course_id,
    c.name,
    c.thumbnail,
    u.First_name AS buddy_name,
    COUNT(e.User_ID) AS total_students
  FROM courses c
  JOIN users u ON c.User_ID = u.User_ID
  LEFT JOIN enrollment e ON e.Course_ID = c.course_id AND e.Payment = 'completed'
  WHERE c.status = 'approved'
  GROUP BY c.course_id
  ORDER BY c.course_id DESC
  LIMIT 3
";
$result = $conn->query($query);



if (isset($_SESSION['Role'])) {
    if ($_SESSION['Role'] === 'buddy') {
        include '../components/navbar-buddy.php';
    } elseif ($_SESSION['Role'] === 'student') {
        include '../components/navbar-student.php';
    } else {
        include '../components/navbar-main.php';
    }
} else {
    include '../components/navbar-main.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StudyBuddy - Share Expertise or Find Help</title>
  <link rel="stylesheet" href="landing-style.css" />

</head>
<body>
  <main class="page-container">

    <section class="hero-section">
      <div class="hero-container">
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/b2d04ace80b301d3b0c7bd9817b24fda32324cb3?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Hero background" class="hero-bg">
        <div class="hero-overlay">
        

          <h1 class="hero-title">Join <span class="highlight">StudyBuddy</span> Share Expertise<br>or Find Help</h1>
          <p class="hero-description"><strong>Empower</strong> thousands of learners with intuitive tools to schedule sessions, share knowledge, and track progress <strong>all in one place</strong>.</p>

          <div class="hero-cta">
          </div>
        </div>
      </div>
    </section>

    <section class="features-section">
      <div class="section-container">
        <p class="section-tag">Features</p>
        <h2 class="section-title">Why Choose StuddyBuddy?</h2>

        <div class="features-grid">
          <article class="feature-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/b9e1f4bd52d70f1bdf5ce3c549a53a43ca711238?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Expert Matching" class="feature-icon">
            <h3 class="feature-title">Expert Buddy Matching</h3>
            <p class="feature-description">Get paired with vetted, high-achieving buddies who specialize in your weak areas</p>
          </article>

          <article class="feature-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a320e41e421481b0ff4da3fb9cedbf98fdbfbfd9?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Real-time Help" class="feature-icon">
            <h3 class="feature-title">Real-Time Help</h3>
            <p class="feature-description">Instant 1:1 support via chat, video, or screen-sharing—day or night.</p>
          </article>

          <article class="feature-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/bbd35f00183bdaaa95c05778ae7865e765a80078?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Personalized Learning" class="feature-icon">
            <h3 class="feature-title">Personalized Learning<br>Support</h3>
            <p class="feature-description">Custom study road maps and adaptive sessions tailored to your goals.</p>
          </article>

          <article class="feature-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2a689e69d2bf815570ddb8e31d189a0af6809226?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Search Filters" class="feature-icon">
            <h3 class="feature-title">Advanced Search Filters</h3>
            <p class="feature-description">Find your perfect buddy by subject, language, schedule, or even learning style</p>
          </article>
        </div>
      </div>
    </section>

    <section class="how-it-works-section">
      <div class="section-container">
        <p class="section-tag">How It Works</p>
        <h2 class="section-title">Become a Buddy in 3 Steps</h2>

        <div class="steps-grid">
          <article class="step-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/0e7ea8f3ae3311846adbedba3849de88c7939d36?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Sign Up" class="step-icon">
            <h3 class="step-title">Sign Up</h3>
            <p class="step-description">Showcase your expertise<br>add your subjects, qualifications, and teaching style.</p>
          </article>

          <article class="step-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/6034fdb6905a74845bb65bb8cf25741bae9b4fd6?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Build Course" class="step-icon">
            <h3 class="step-title">Build Your Course</h3>
            <p class="step-description">Upload videos, create quizzes, and add supplemental materials like PDFs or presentations</p>
          </article>

          <article class="step-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/20f69553e47439cd3b2243fa67b993e148882237?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Start Earning" class="step-icon">
            <h3 class="step-title">Publish & Start Earning</h3>
            <p class="step-description">Launch your program to matched students, track their progress, and earn rewards!</p>
          </article>
        </div>
      </div>
    </section>

    <section class="testimonial-section">
      <div class="testimonial-container">
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/b46c1c6ecad9c603a83dd7da2bbf2d6b1d369ebc?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Testimonial background" class="testimonial-bg">
        <div class="testimonial-overlay">
          <div class="testimonial-card">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/9f504e408d5753a2d1721433209d95f16f8debe6?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Khalid Ahmed" class="testimonial-avatar">
            <h3 class="testimonial-author">Khalid Ahmed - <span>Computer Science Mentor</span></h3>
            <blockquote class="testimonial-quote">"StudyBuddy helped me turn my coding skills into a rewarding mentorship journey. I've guided 200+ students through Python and web development, all while finishing my degree.</blockquote>
          </div>
        </div>
      </div>
    </section>

<section class="courses-section">
  <div class="container">
    <h2 class="section-title">📚 Recently Added Courses</h2>
    <div class="courses-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($course = $result->fetch_assoc()): ?>
          <?php
            $thumbnailPath = '../creatcourse/uploads/thumbnails/';
            $defaultImage = 'https://thumbs.dreamstime.com/b/picture-silhouette-vector-icon-isolated-white-background-painting-furniture-web-mobile-apps-ui-design-print-262494234.jpg';

            $thumbnail = !empty($course['thumbnail']) && file_exists($thumbnailPath . $course['thumbnail'])
              ? $thumbnailPath . htmlspecialchars($course['thumbnail'])
              : $defaultImage;
          ?>
          <a href="course-view.php?course_id=<?= $course['course_id'] ?>" class="course-link">
            <article class="course-card">
              <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['name']) ?>" class="course-image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
              <div class="course-content">
                <span class="course-badge">New</span>
                <h3 class="course-title"><?= htmlspecialchars($course['name']) ?></h3>
                <div class="course-author">
                  <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Author" class="author-avatar" />
                  <span class="author-name">by <?= htmlspecialchars($course['buddy_name']) ?></span>
                </div>
                <div class="course-stats">
                  <span class="course-enrolled"><?= (int)$course['total_students'] ?> students enrolled</span>
                </div>
              </div>
            </article>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center;">No courses available.</p>
      <?php endif; ?>
    </div>
  </div>
</section>


    <section class="cta-section">
      <div class="cta-container">
        <h2 class="cta-title">Ready to Support Fellow Students?</h2>
        <p class="cta-description">Join a growing community of top-performing students helping others succeed. As a StudyBuddy, you can guide, mentor, and make a real impact on someone's academic journey, all while building your own confidence and leadership.</p>
      </div>
    </section>

    <footer class="main-footer">
      <div class="footer-container">
        <div class="footer-content">
          <div class="footer-nav">
            <div class="footer-col">
              <h4 class="footer-title">Product</h4>
              <a href="#" class="footer-link">Features</a>
            </div>
            <div class="footer-col">
              <div class="footer-brand">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/84ead9b160533a286593edb72956561008e3680d?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Footer logo" class="footer-logo">
                <div class="footer-links">
                  <h4 class="footer-title">Company</h4>
                  <a href="#" class="footer-link">About us</a>
                </div>
              </div>
              <div class="footer-pricing">
                <h4 class="footer-title">Pricing</h4>
                <a href="#" class="footer-link">Personal</a>
              </div>
            </div>
          </div>

          <hr class="footer-divider">

          <div class="footer-bottom">
            <div class="footer-legal">
              <span class="copyright">© 2025 StudyBuddy</span>
              <span class="separator">•</span>
              <span class="rights">All rights reserved</span>
            </div>
            <div class="footer-social">
              <a href="#" class="social-link"><img src="https://cdn.builder.io/api/v1/image/assets/TEMP/490763157c7bf3f51bac80e63a7c3bd64e21007b?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Social 1" class="social-icon"></a>
              <a href="#" class="social-link"><img src="https://cdn.builder.io/api/v1/image/assets/TEMP/62fe2ed90081a266058eb69a7dc5ce495893cad3?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Social 2" class="social-icon"></a>
              <a href="#" class="social-link"><img src="https://cdn.builder.io/api/v1/image/assets/TEMP/179cc826d5194ea43a704d23aa815d839836c356?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Social 3" class="social-icon"></a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </main>
</body>
</html>
