<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/db_connect.php';

if (!isset($_SESSION['User_ID']) || !isset($_GET['course_id'])) {
    header("Location: ../signin.php");
    exit;
}

$userId = $_SESSION['User_ID'];
$courseId = intval($_GET['course_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentName = $_POST['content_name'];
    $contentType = $_POST['content_type'];

    if (!empty($contentName) && !empty($contentType) && isset($_FILES['uploaded_file'])) {
        $uploadDir = '../uploads/content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['uploaded_file']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO content (Course_ID, Content_name, Content_type, Content_link) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $courseId, $contentName, $contentType, $fileName);

            if (!$stmt->execute()) {
                $message = "<div style='color:red;'>❌ Error saving content.</div>";
            }

            if (isset($_POST['publish_course'])) {
                $conn->query("UPDATE courses SET status = 'pending' WHERE course_id = $courseId");
                echo "<script>alert('✅ Your course was published successfully. Waiting for admin approval.'); window.location.href='../buddy-profile/buddy-profile-view.php';</script>";
                exit;
            } else {
                echo "<script>window.location.href='create-course-add-content.php?course_id=$courseId&created=1';</script>";
                exit;
            }
        } else {
            $message = "<div style='color:red;'>❌ Upload failed.</div>";
        }
    } else {
        $message = "<div style='color:red;'>❌ All fields are required.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Content</title>
    <link rel="stylesheet" href="add-content-style.css">
</head>
<body>
<header class="site-header">
  <div class="header-container">
    <div class="logo-wrapper">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e1084f504b80b21ea37b239ae6b0ffba17fdba06" class="logo-icon" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/91aaa7707268fd69fc21a53872c7b03b6dda9b2e" class="logo-text" />
    </div>
    <div class="header-right">
      <nav class="main-nav">
        <a href="#" class="nav-link nav-link--home">Home</a>
        <a href="#" class="nav-link">search</a>
        <a href="#" class="nav-link">about us</a>
        <a href="#" class="nav-link nav-link--profile">my profile</a>
      </nav>
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/46f3cc461aac63e06d38ebc85be83ae154ff0931" alt="Profile" class="profile-icon" />
    </div>
  </div>
</header>

<div class="course-creator">
  <div class="course-content">
    <aside class="sidebar">
      <a href="../buddy-profile/buddy-profile-view.php" class="back-button">← My profile</a>
      <h1 class="page-title">Create Course</h1>
    </aside>

    <section class="content-section">
      <div class="content-card">
        <div class="section-header">
          <div class="section-indicator"></div>
          <h2 class="section-title">Upload files</h2>
        </div>
        <hr class="divider" />
        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data" class="form-section">
          <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($courseId); ?>" />

          <div class="input-row">
            <div class="input-group">
              <label class="content-label">Content Name</label>
              <input type="text" name="content_name" class="content-input" required />
            </div>
            <div class="input-group">
              <label class="content-label">Content Type</label>
              <select name="content_type" class="content-input" required>
                <option value="" disabled selected>Select a type</option>
                <option value="pdf">PDF</option>
                <option value="video">Video</option>
                <option value="ppt">PPT</option>
                <option value="link">Link</option>
                <option value="image">Image</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="file-upload-area" ondrop="handleDrop(event)" ondragover="event.preventDefault()">
            <div class="upload-content">
              <img src="https://cdn-icons-png.flaticon.com/512/833/833524.png" class="upload-icon" alt="upload" />
              <p class="upload-text">Drop files here</p>
              <div class="upload-divider">OR</div>
              <label for="uploaded_file" class="browse-button">Browse files</label>
              <input type="file" id="uploaded_file" name="uploaded_file" style="display: none;" required onchange="previewFileName(event)" />
              <p id="file-preview" style="margin-top: 10px; font-size: 14px; color: #2e5b87;"></p>
            </div>
          </div>

          <div class="form-actions">
            <button class="action-button" type="submit" name="add_content">Add Content</button>
            <button type="submit" name="publish_course" class="action-button">Publish Course</button>
          </div>
        </form>
      </div>
    </section>
  </div>
</div>

<script>
function handleDrop(event) {
    event.preventDefault();
    const fileInput = document.getElementById("uploaded_file");
    if (event.dataTransfer.files.length > 0) {
        const dt = new DataTransfer();
        dt.items.add(event.dataTransfer.files[0]);
        fileInput.files = dt.files;
        document.getElementById("file-preview").textContent = "Selected: " + dt.files[0].name;
    }
}
function previewFileName(event) {
    const fileInput = event.target;
    if (fileInput.files.length > 0) {
        document.getElementById("file-preview").textContent = "Selected: " + fileInput.files[0].name;
    }
}
</script>
</body>
</html>
