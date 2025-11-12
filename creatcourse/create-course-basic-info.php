<?php
/**
 * Course Creation - Basic Information Page
 * 
 * This page allows Buddies (tutors) to create new courses by providing:
 * - Course thumbnail image (optional, defaults to media_pic.jpeg)
 * - Course name/title
 * - Course number (e.g., WEB101, CS201)
 * - Course description
 * - Course price (in dollars)
 * 
 * After submitting this form, the buddy is redirected to add course content
 * (lessons, videos, documents) on the next page.
 * 
 * Course Status Flow:
 * 1. Course is created with status = 'pending'
 * 2. Admin reviews and approves/rejects the course
 * 3. Only approved courses appear on the public landing page
 * 
 * Database Table: courses
 * Columns used: name, number, description, price, User_ID, thumbnail, status
 */

// Start or resume the session to access user data
session_start();

// Enable error reporting for development (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include '../includes/db_connect.php';

/**
 * Process Form Submission
 * Only executes when the form is submitted via POST method
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    /**
     * Retrieve form data from POST request
     * All data is sent from the HTML form at the bottom of this file
     */
    $courseName = $_POST['Course_name'];      // Course title (e.g., "Introduction to Web Development")
    $courseNumber = $_POST['Course_number'];  // Course code (e.g., "WEB101")
    $description = $_POST['Description'];     // Detailed course description
    $price = $_POST['Price'];                 // Course price in dollars
    
    /**
     * Get User ID from session
     * User_ID identifies which buddy is creating this course
     * Fallback to user ID 3 if session is not set (for testing purposes)
     * 
     * TODO: Remove fallback in production and require valid session
     */
    $userId = $_SESSION['User_ID'] ?? 3;

    /**
     * Handle Thumbnail Upload
     * Process uploaded image file or use default thumbnail
     */
    $defaultThumbnail = "media_pic.jpeg";  // Default image if no upload
    $thumbnail = $defaultThumbnail;

    /**
     * Check if thumbnail file was uploaded
     * $_FILES['thumbnail']['error'] === 0 means upload was successful
     */
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        
        // Get original filename (remove any path information for security)
        $fileName = basename($_FILES["thumbnail"]["name"]);
        
        // Define upload directory
        $uploadDir = "uploads/thumbnails/";
        
        // Full path where file will be saved
        $targetPath = $uploadDir . $fileName;

        /**
         * Create upload directory if it doesn't exist
         * 0777 = full read/write/execute permissions
         * true = create nested directories if needed
         */
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        /**
         * Move uploaded file from temporary location to permanent location
         * tmp_name = temporary file path created by PHP during upload
         */
        if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetPath)) {
            $thumbnail = $fileName;  // Store just the filename, not full path
        }
        // If move fails, $thumbnail remains as default image
    }

    /**
     * Insert new course into database
     * Using prepared statement to prevent SQL injection
     * 
     * Initial status is 'pending' - admin must approve before course is visible
     */
    $stmt = $conn->prepare("INSERT INTO courses (name, number, description, price, User_ID, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");

    /**
     * Check if prepared statement was created successfully
     * If not, database connection or SQL syntax may be incorrect
     */
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    /**
     * Bind parameters to prepared statement
     * Parameter types:
     * s = string (name, number, description, thumbnail)
     * d = double/decimal (price)
     * i = integer (User_ID)
     */
    $stmt->bind_param("sssdis", $courseName, $courseNumber, $description, $price, $userId, $thumbnail);

    /**
     * Execute the insert query
     * If successful, redirect to content addition page
     */
    if ($stmt->execute()) {
        /**
         * Get the ID of the newly inserted course
         * This ID is used to link course content (lessons) to this course
         */
        $newCourseId = $conn->insert_id;
        
        /**
         * Redirect to content addition page
         * Pass course_id and created=1 flag in URL
         * created=1 can be used to show a success message
         */
        header("Location: create-course-add-content.php?course_id=" . $newCourseId . "&created=1");
        exit;  // Always exit after redirect to prevent further code execution
    } else {
        /**
         * Display error if insert fails
         * In production, log error and show user-friendly message
         */
        echo "<div style='color:red;'>❌ Error: " . $stmt->error . "</div>";
    }
}
?>


<head>
  <link rel="stylesheet" href="create-course-basic.CSS">
</head>
<header class="page-header">
    <div class="header-container">
      <div class="logo-section">
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e1084f504b80b21ea37b239ae6b0ffba17fdba06?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Logo Icon" class="logo-icon" />
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/91aaa7707268fd69fc21a53872c7b03b6dda9b2e?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Logo Text" class="logo-text" />
      </div>
      <div class="header-content">
        <nav class="main-navigation">
          <a href="#" class="nav-link">Home</a>
          <a href="#" class="nav-link">search</a>
          <a href="#" class="nav-link">about us</a>
          <a href="#" class="nav-link active">my profile</a>
        </nav>
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/46f3cc461aac63e06d38ebc85be83ae154ff0931?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="message" class="message-logo" />
      </div>
    </div>
  </header>

  
<body>
  <main class="main-content">
    <div class="content-wrapper">
      <aside class="sidebar">
        <button class="profile-button" onclick="location.href='../Buddy-profile/BUDDY-profile-view.php'">

          <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/315537cd8a8b01586400d48156fd889fd4a59885?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9" alt="Profile" class="button-icon" />
          <span>My profile</span>
        </button>
        <h1 class="page-title">Create Course</h1>
      </aside>

   

      <section class="course-form-section">
        <div class="form-container">
          <header class="form-header">
            <div class="header-indicator"></div>
            <h2 class="section-title">Basics</h2>
          </header>
  
          <hr class="section-divider" />
          <form class="course-form" action="create-course-basic-info.php" method="POST" enctype="multipart/form-data">
  <div class="form-content">
    <h3 class="form-section-title">Thumbnail</h3>

    <div class="form-layout">
      <div class="form-main">
        <!-- Thumbnail Upload -->
        <div class="thumbnail-upload">
          <label for="thumbnailInput" style="cursor: pointer;">
            <img
              id="thumbnailPreview"
              src="media_pic.jpeg"
              alt="Upload Thumbnail"
              class="upload-image"
            />
          </label>
          <input
            type="file"
            id="thumbnailInput"
            name="thumbnail"
            accept="image/*"
            style="display: none;"
            onchange="previewThumbnail(event)"
          />
        </div>

        <!-- Course Name -->
        <div class="input-group">
          <label class="input-label">Name</label>
          <input
            type="text"
            name="Course_name"
            class="text-input"
            placeholder="Course name"
          />
        </div>

        <!-- Course Number and Price -->
        <div class="input-row">
          <div class="input-group" style="margin-right: 50px;">
            <label class="input-label">Course Number</label>
            <input
              type="text"
              name="Course_number"
              class="text-input"
              placeholder="ex: web101"
            />
          </div>

          <div class="input-group">
            <label class="input-label">Course Price</label>
            <input
              type="number"
              name="Price"
              class="text-input"
              placeholder="Set a price"
              min="0"
              step="0.01"
            />
          </div>
        </div>

        <!-- Description -->
        <div class="input-group">
          <label class="input-label">Description</label>
          <div class="textarea-wrapper">
            <textarea
              name="Description"
              class="text-area"
              placeholder="Write a brief description about the course"
            ></textarea>
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/539a689bf84ed64b8f415a18dd99f4c3af806e96?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9"
              alt="Resize" class="resize-icon" />
          </div>
        </div>
      </div>
    </div>

    <hr class="section-divider" />

    <div class="button-wrapper">
      <button class="content-button" type="submit">Add Content</button>
    </div>
  </div>
</form>
        </div>
      </section>
    </form>
    </div>
  </main>
  <script>
    function previewThumbnail(event) {
      const file = event.target.files[0];
      if (file) {
        const preview = document.getElementById('thumbnailPreview');
        preview.src = URL.createObjectURL(file);
      }
    }
  </script>
</body>
  