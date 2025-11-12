<?php
/**
 * User Login (Sign In) Page
 * 
 * This page handles user authentication for the Study Buddy platform.
 * It performs the following operations:
 * 1. Validates user credentials (email and password)
 * 2. Verifies that the account is email-verified
 * 3. Creates a session for the logged-in user
 * 4. Redirects to appropriate dashboard based on user role
 * 
 * User Roles and Redirects:
 * - Student → student-profile/student-profile-view.php
 * - Buddy → buddy-profile/buddy-profile-view.php
 * - Admin → admin/admin-dashboard.php
 * 
 * Security Features:
 * - Password verification using password_verify() against hashed passwords
 * - Prepared statements to prevent SQL injection
 * - Email verification check before allowing login
 * - Session-based authentication
 */

// Include database connection
include '../includes/db_connect.php';

// Start or resume session for storing user data
session_start();

/**
 * Process Login Form Submission
 * Only executes when form is submitted via POST method
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Retrieve and sanitize form inputs
    $email = trim($_POST['email']);        // Remove whitespace from email
    $password = $_POST['password'];         // Password (don't trim - may have intentional spaces)
    $remember = isset($_POST['remember']); // Check if "Remember Me" was checked (currently not used)

    /**
     * Query database for user with provided email
     * Using prepared statement to prevent SQL injection
     */
    $query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);  // "s" = string parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    /**
     * Check if user exists (exactly one matching email)
     * Email should be unique in database
     */
    if ($result && mysqli_num_rows($result) === 1) {
        // Fetch user data as associative array
        $user = mysqli_fetch_assoc($result);

        /**
         * Verify password matches hashed password in database
         * password_verify() securely compares plain text password with hash
         */
        if (password_verify($password, $user['Password'])) {
            
            /**
             * Check if user has verified their email
             * is_verified = 1 means email verification is complete
             * is_verified = 0 means user needs to verify email first
             */
            if ($user['is_verified'] != 1) {
                echo "<script>alert('❗ Please verify your email before logging in.'); window.history.back();</script>";
                exit();
            }

            /**
             * Create session variables for logged-in user
             * These variables persist across pages until logout or browser close
             */
            $_SESSION['User_ID'] = $user['User_ID'];  // Unique user identifier
            $_SESSION['Role'] = $user['Role'];        // User role (student/buddy/admin)

            /**
             * Redirect user to appropriate dashboard based on their role
             * Convert role to lowercase for consistent comparison
             */
            $role = strtolower($user['Role']);

            // Student Dashboard
            if ($role === 'student') {
                 header("Location: ../student-profile/student-profile-view.php");
                exit();
            } 
            // Buddy (Tutor) Dashboard
            elseif ($role === 'buddy') {
                header("Location:../buddy-profile/buddy-profile-view.php");
                exit();
            } 
            // Admin Dashboard
            elseif ($role === 'admin') {
                header("Location:../admin/admin-dashboard.php");
                exit();
            } 
            // Unknown role (should not happen)
            else {
                echo "<script>alert('❌ Unknown role!'); window.history.back();</script>";
                exit();
            }
        } 
        /**
         * Password does not match
         * Show error and return to login page
         */
        else {
            echo "<script>alert('❌ Incorrect password'); window.history.back();</script>";
            exit();
        }
    } 
    /**
     * Email not found in database
     * Show error and return to login page
     */
    else {
        echo "<script>alert('❌ Email not found'); window.history.back();</script>";
        exit();
    }
}
?>

<!-- 
  HTML Section: Login Form
  
  Simple login interface with:
  - Email input field
  - Password input field
  - Submit button
  - Forgot password link
  
  Layout: Two-column design
  - Left: Decorative illustration
  - Right: Login form
-->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In | StudyBuddy</title>
  <!-- Google Fonts for typography -->
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet" />
  <!-- External CSS stylesheet -->
  <link rel="stylesheet" href="signin.css" />
</head>
<body>

<main class="login-container">
  
  <!-- Left Column: Decorative illustration -->
  <section class="login-left">
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/aea720c50bf6acf1bcabe2d09f8d5f33c652bd26" alt="Illustration" class="login-illustration" />
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/88f5e6bbe705b96d66651660128254a53f7dbe32" alt="Logo" class="login-logo" />
  </section>

  <!-- Right Column: Login Form -->
  <section class="login-right">
    <!-- Form submits to same page (signin.php) via POST method -->
    <form class="login-form" method="post" action="">
      <h1 class="login-title">Login to Your Account</h1>

      <!-- Email Input Field -->
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-input" placeholder="Email Address" required />
      </div>

      <!-- Password Input Field -->
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" placeholder="Password" required />
      </div>

      <!-- Submit Button -->
      <button type="submit" class="sign-in-button">Sign In</button>
      <br><br>

      <!-- Forgot Password Link -->
      <div class="form-options">
        <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
