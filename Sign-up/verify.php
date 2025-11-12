<?php
include '../includes/db_connect.php';


$successMessage = $errorMessage = '';
$emailValue = $_GET['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $code = trim($_POST['verification_code']);

 
    $query = "SELECT verification_code FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $db_code = $user['verification_code'];

        if ($code === $db_code) {

            $update = "UPDATE users SET is_verified = 1, verification_code = NULL WHERE Email = ?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $successMessage = "✅ Email verified successfully! You can now sign in.";
        } else {
            $errorMessage = "❌ Invalid verification code.";
        }
    }
    else {
        $errorMessage = "❌ Invalid email or code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
  <link rel="stylesheet" href="verify.css">
</head>
<body>
  <div class="verify-container">
    <h2 class="verify-title">Email Verification</h2>

    <?php if ($successMessage): ?>
      <p class="message success"><?= $successMessage ?></p>
      <a href="../Sign-in/signin.php" class="signin-link">Sign In</a>
    <?php else: ?>
      <?php if ($errorMessage): ?>
        <p class="message error"><?= $errorMessage ?></p>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($emailValue) ?>">
        </div>
        <div class="form-group">
          <label>Verification Code</label>
          <input type="text" name="verification_code" required>
        </div>
        <button type="submit" class="verify-btn">Verify</button>
      </form>
      
    <?php endif; ?>
  </div>
</body>
</html>
