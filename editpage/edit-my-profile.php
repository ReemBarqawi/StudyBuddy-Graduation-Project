<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../includes/db_connect.php';

if (!isset($_SESSION['User_ID'])) {
    header("Location: ../signin.php");
    exit;
}

$user_id = $_SESSION['User_ID'];
$role = $_SESSION['Role'] ?? null;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['First_name']);
    $lastName = trim($_POST['Last_name']);
    $bio = trim($_POST['Bio']);

    // Handle file upload
    if (!empty($_FILES['Profile_image']['name'])) {
        $targetDir = "../uploads/profile/";
        $fileName = time() . "_" . basename($_FILES['Profile_image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['Profile_image']['tmp_name'], $targetFile)) {
            $updateQuery = "UPDATE users SET First_name = ?, Last_name = ?, Bio = ?, Image = ? WHERE User_ID = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ssssi", $firstName, $lastName, $bio, $targetFile, $user_id);
        } else {
            // Upload failed, update without image
            $updateQuery = "UPDATE users SET First_name = ?, Last_name = ?, Bio = ? WHERE User_ID = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssi", $firstName, $lastName, $bio, $user_id);
        }
    } else {
        // No file uploaded
        $updateQuery = "UPDATE users SET First_name = ?, Last_name = ?, Bio = ? WHERE User_ID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $firstName, $lastName, $bio, $user_id);
    }

    $stmt->execute();

    // Redirect based on role
    if ($role === 'buddy') {
        header("Location: ../buddy-profile/buddy-profile-view.php?updated=1");
    } elseif ($role === 'student') {
        header("Location: ../student-profile/student-profile-view.php?updated=1");
    } else {
        header("Location: edit-my-profile.php?error=missing-role");
    }
    exit;
}

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallback image
$defaultProfile = 'https://www.pngmart.com/files/23/Profile-PNG-Photo.png';
$profileImage = (!empty($user['Profile_image']) && file_exists($user['Profile_image']))
    ? $user['Profile_image']
    : $defaultProfile;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit My Profile</title>
  <style>
    body {
      font-family: Inter, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .profile-edit-container {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }
    .profile-form-section {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0px 2px 4px rgba(0,0,0,0.1);
    }
    .profile-picture-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      margin-bottom: 30px;
    }
    .profile-picture {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ccc;
      cursor: pointer;
    }
    #profile-image-upload {
      display: none;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      font-weight: bold;
      display: block;
      margin-bottom: 6px;
    }
    .form-input, textarea {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid #379ae6;
      border-radius: 6px;
      font-size: 15px;
    }
    textarea {
      resize: vertical;
      min-height: 100px;
    }
    .save-button {
      width: 100%;
      background-color: #379ae6;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .save-button:hover {
      background-color: #2e5b87;
    }
  </style>
</head>
<body>

<div class="profile-edit-container">
  <form class="profile-form-section" method="POST" enctype="multipart/form-data">
    <div class="profile-picture-wrapper">
      <label for="profile-image-upload">
        <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Picture" class="profile-picture" id="preview-img">
      </label>
      <input type="file" name="Profile_image" id="profile-image-upload" onchange="previewImage(event)">
    </div>

    <div class="form-group">
      <label>First Name:</label>
      <input type="text" name="First_name" class="form-input" value="<?= htmlspecialchars($user['First_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label>Last Name:</label>
      <input type="text" name="Last_name" class="form-input" value="<?= htmlspecialchars($user['Last_name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label>Bio:</label>
      <textarea name="Bio"><?= htmlspecialchars($user['Bio'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="save-button">Save</button>
  </form>
</div>

<script>
  function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
      document.getElementById('preview-img').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
  }
</script>

</body>
</html>
