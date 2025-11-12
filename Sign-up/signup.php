<?php
/**
 * User Registration (Sign Up) Page
 * 
 * This page handles new user registration for both Students and Buddies (tutors).
 * It performs the following operations:
 * 1. Validates user input (name, email, password)
 * 2. Checks if email already exists in database
 * 3. Generates a 6-digit verification code
 * 4. Stores user data in database with hashed password
 * 5. Sends verification code via email using PHPMailer
 * 6. Redirects to verification page
 * 
 * Security Features:
 * - Password hashing using PHP's password_hash() function
 * - Prepared statements to prevent SQL injection
 * - Email verification to confirm user identity
 * - Input sanitization with trim()
 */

// Enable error reporting for development (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection file
include '../includes/db_connect.php';

// Import PHPMailer classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer library files
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

/**
 * Initialize form variables with empty values
 * These variables store form data and error states
 */
$first_name = $last_name = $email = $role = '';
$showPasswordError = $showRoleError = $showSuccess = false;
$passwordError = $roleError = $successMessage = '';

/**
 * Process form submission when user clicks "Sign Up" button
 * This block only executes when the form is submitted via POST method
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve and sanitize form inputs
    // trim() removes leading/trailing whitespace
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $password    = $_POST['password'];  // Don't trim password (may contain intentional spaces)
    $confirm_password = $_POST['confirm_password'];
    $role        = $_POST['role'];  // Either 'student' or 'buddy'

    /**
     * Validation Step 1: Check if user selected a role
     * If no role selected, redirect back with error message
     */
    if (!$role) {
        header("Location: signup.php?error=role");
        exit();
    }

    /**
     * Validation Step 2: Verify password and confirm password match
     * If passwords don't match, redirect back with error message
     */
    if ($password !== $confirm_password) {
        header("Location: signup.php?error=password");
        exit();
    }

    /**
     * Security: Hash the password before storing in database
     * PASSWORD_DEFAULT uses bcrypt algorithm (currently the most secure option)
     * This ensures passwords are never stored in plain text
     */
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    /**
     * Check if email already exists in database
     * Using prepared statement to prevent SQL injection attacks
     */
    $check_query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);  // "s" means string parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    /**
     * If email already exists, redirect with error
     * This prevents duplicate accounts with same email
     */
    if (mysqli_num_rows($result) > 0) {
        header("Location: signup.php?error=email");
        exit();
    }

    /**
     * Generate random 6-digit verification code
     * This code will be sent to user's email for account verification
     * Range: 100000 to 999999 (ensures always 6 digits)
     */
    $verification_code = rand(100000, 999999);

    /**
     * Insert new user into database
     * User is initially unverified (is_verified = 0)
     * Using prepared statement for security
     */
    $insert_query = "INSERT INTO users (First_name, Last_name, Email, Password, Role, verification_code, is_verified)
                     VALUES (?, ?, ?, ?, ?, ?, 0)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $email, $hashed_password, $role, $verification_code);

    /**
     * If user was successfully inserted into database,
     * send verification email using PHPMailer
     */
    if (mysqli_stmt_execute($stmt)) {
      
      // Create new PHPMailer instance
      $mail = new PHPMailer(true);
      
      try {
          /**
           * Configure SMTP settings for Gmail
           * SMTP = Simple Mail Transfer Protocol (standard for sending emails)
           */
          $mail->isSMTP();                                     // Use SMTP protocol
          $mail->Host = 'smtp.gmail.com';                      // Gmail SMTP server
          $mail->SMTPAuth   = true;                            // Enable authentication
          $mail->Username   = 'studybuddynoreply.1@gmail.com'; // Gmail account (sender)
          $mail->Password   = 'elma hicz mnfs qsym';          // App-specific password (not regular Gmail password)
          $mail->SMTPSecure = 'ssl';                           // Use SSL encryption
          $mail->Port = 465;                                   // SSL port for Gmail
          
          /**
           * Set email details
           */
          $mail->setFrom('studybuddynoreply.1@gmail.com', 'StudyBuddy');  // Sender address and name
          $mail->addAddress($email);                                        // Recipient (the new user)
          $mail->Subject = 'StudyBuddy Verification Code';                 // Email subject
          $mail->Body    = "Your verification code is: $verification_code"; // Email content
          
          /**
           * Send the email
           * If successful, redirect to verification page
           */
          $mail->send();
          
          // Redirect to verification page with email as URL parameter
          // urlencode() ensures email is safely passed in URL
          header("Location: verify.php?email=" . urlencode($email));
          exit();
      } 
      catch (Exception $e) {
          /**
           * If email sending fails, log error and show message
           * In production, show user-friendly error instead of technical details
           */
          error_log("PHPMailer Error: " . $mail->ErrorInfo);
          echo "Mailer Error: " . $mail->ErrorInfo;
          exit();
      }
  }
  
}
?>


<!-- 
  HTML Section: User Registration Form
  
  This section provides a two-column layout:
  - Left column: Registration form with input fields
  - Right column: Decorative illustration
  
  Form Fields:
  - First Name, Last Name, Email (text inputs)
  - Password, Confirm Password (password inputs)
  - Role selection (Student or Buddy buttons)
-->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup Page</title>
  <!-- Link to external CSS file for styling -->
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <main class="signup-page">
    <div class="signup-layout">
      
      <!-- Left Column: Registration Form -->
      <section class="form-column">
        <div class="form-wrapper">
          <div class="form-content">
            
            <!-- Page Title and Description -->
            <h1 class="signup-title">Create Your Account</h1>
            <p class="signup-description">
              Be part of a platform where students learn and buddies lead.<br />Get started today!
            </p>
            
            <!-- Registration Form - submits to same page via POST method -->
            <form method="POST" action="" class="signup-form">
              
              <!-- First Name Input Field -->
              <div class="form-field">
                <label class="field-label">First Name</label>
                <input type="text" class="text-input" name="first_name" placeholder="First name" required value="<?= htmlspecialchars($first_name) ?>" />
              </div>
              
              <!-- Last Name Input Field -->
              <div class="form-field">
                <label class="field-label">Last Name</label>
                <input type="text" class="text-input" name="last_name" placeholder="Last name" required value="<?= htmlspecialchars($last_name) ?>" />
              </div>
              
              <!-- Email Input Field -->
              <div class="form-field">
                <label class="field-label">Email Address</label>
                <input type="email" class="text-input" name="email" placeholder="Email Address" required value="<?= htmlspecialchars($email) ?>" />
              </div>
              
              <!-- Password Fields (side by side) -->
              <div class="password-fields">
                <!-- Password Input -->
                <div class="form-field" style="flex:1">
                  <label class="field-label">Password</label>
                  <input type="password" class="text-input" name="password" placeholder="Password" required />
                </div>
                
                <!-- Confirm Password Input -->
                <div class="form-field" style="flex:1">
                  <label class="field-label">Confirm Password</label>
                  <input type="password" class="text-input" name="confirm_password" placeholder="Confirm Password" required />
                </div>
              </div>
              
              <!-- Display password error if exists -->
              <?php if ($showPasswordError): ?>
                <p class="form-error-message"><?= $passwordError ?></p>
              <?php endif; ?>
              
              <!-- Role Selection Section -->
              <h2 class="role-title">Choose your role to get started</h2>
              <div class="role-buttons">
                <!-- Student Role Button -->
                <button type="button" class="role-btn role-btn-student <?= $role === 'student' ? 'selected' : '' ?>">Student</button>
                
                <!-- Buddy (Tutor) Role Button -->
                <button type="button" class="role-btn role-btn-buddy <?= $role === 'buddy' ? 'selected' : '' ?>">Buddy</button>
              </div>
              
              <!-- Display role error if exists -->
              <?php if ($showRoleError): ?>
                <p class="role-error-message"><?= $roleError ?></p>
              <?php endif; ?>
              
              <!-- Display success message if exists -->
              <?php if ($showSuccess): ?>
                <p class="form-success-message"><?= $successMessage ?></p>
              <?php endif; ?>
              
              <!-- Hidden input to store selected role (submitted with form) -->
              <input type="hidden" name="role" id="roleInput" value="<?= htmlspecialchars($role) ?>" />
              
              <!-- Sign In Link (for existing users) -->
              <div class="signin-prompt">
                <span class="signin-text">Already have an account?</span>
                <a href="../Sign-in/signin.php" class="signin-link">Sign in</a>
              </div>
              
              <!-- Submit Button -->
              <button type="submit" class="signup-btn">Sign Up</button>
            </form>

          </div>
        </div>
      </section>
      
      <!-- Right Column: Illustration (decorative) -->
      <section class="illustration-column">
        <div class="illustration-wrapper">
          <div class="illustration-content">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/4437d09e5a8f5b9bf8667e304ab45ad2c4987466" alt="Illustration" class="illustration-main" />
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/84dcba31c728bd7b46ae5b841973594dbe7fc6aa" alt="Logo" class="illustration-logo" />
          </div>
        </div>
      </section>
      
    </div>
  </main>
  
<!-- 
  JavaScript Section: Form Interactivity and Validation
  
  This script handles:
  1. Role button selection (Student vs Buddy)
  2. Client-side form validation
  3. Error message alerts based on URL parameters
-->
<script>
  /**
   * Wait for page to fully load before executing JavaScript
   * This ensures all DOM elements are available
   */
  document.addEventListener("DOMContentLoaded", () => {
    
    // Get references to DOM elements
    const studentBtn = document.querySelector(".role-btn-student");  // Student role button
    const buddyBtn = document.querySelector(".role-btn-buddy");      // Buddy role button
    const roleInput = document.getElementById("roleInput");          // Hidden input for role value
    const form = document.querySelector(".signup-form");             // The registration form
    const termsCheckbox = document.querySelector('.checkbox-input'); // Terms checkbox (if exists)

    /**
     * Handle Student Button Click
     * When clicked, set role to 'student' and update button styling
     */
    if (studentBtn) {
      studentBtn.addEventListener("click", () => {
        roleInput.value = "student";           // Set hidden input value
        studentBtn.classList.add("selected");  // Highlight student button
        buddyBtn.classList.remove("selected"); // Remove highlight from buddy button
      });
    }

    /**
     * Handle Buddy Button Click
     * When clicked, set role to 'buddy' and update button styling
     */
    if (buddyBtn) {
      buddyBtn.addEventListener("click", () => {
        roleInput.value = "buddy";             // Set hidden input value
        buddyBtn.classList.add("selected");    // Highlight buddy button
        studentBtn.classList.remove("selected");// Remove highlight from student button
      });
    }

    /**
     * Form Submission Validation
     * Performs client-side validation before submitting form
     */
    form.addEventListener("submit", function (e) {
      // Get password field values
      const password = document.querySelector('input[name="password"]');
      const confirmPassword = document.querySelector('input[name="confirm_password"]');

      /**
       * Validation 1: Check if passwords match
       * Prevent form submission if they don't match
       */
      if (password.value !== confirmPassword.value) {
        e.preventDefault();  // Stop form submission
        alert("❗ Password and Confirm Password do not match.");
        return;
      }

      /**
       * Validation 2: Check if role is selected
       * Prevent form submission if no role is chosen
       */
      if (!roleInput.value) {
        e.preventDefault();  // Stop form submission
        alert("❗ Please select a role!");
        return;
      }

    });

    /**
     * URL Parameter Handling
     * Check URL for success/error parameters and show appropriate alerts
     * These parameters are set by the PHP code after form processing
     */
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get("success");
    const error = urlParams.get("error");

    /**
     * Success Alert
     * Shows when user is successfully registered
     * Optional redirect to sign-in page after 1 second
     */
    if (success === "1") {
      alert("✅ User registered successfully!");

      const shouldRedirect = urlParams.get("redirect");
      if (shouldRedirect === "1") {
        setTimeout(() => {
          window.location.href = "../Sign-in/signin.html";
        }, 1000); // Wait 1 second before redirecting
      }
    }

    /**
     * Error Alerts
     * Show specific error messages based on error type
     */
    
    // Email already exists error
    if (error === "email") {
      alert("❗ This email is already registered!");
    }

    // Password mismatch error
    if (error === "password") {
      alert("❗ Passwords do not match!");
    }

    // No role selected error
    if (error === "role") {
      alert("❗ Please select a role!");
    }

    // Server/database error
    if (error === "server") {
      alert("❗ Something went wrong. Please try again.");
    }

    /**
     * Clean URL
     * Remove success/error parameters from URL after showing alert
     * This prevents alerts from showing again on page refresh
     */
    if (success || error) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  });
</script>

</body>
</html>
