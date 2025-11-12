<?php
include '../includes/db_connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

$successMessage = $errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

 
    $query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $reset_code = rand(100000, 999999);

        
        $update = "UPDATE users SET reset_code = ? WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "ss", $reset_code, $email);
        mysqli_stmt_execute($stmt);


        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'studybuddynoreply.1@gmail.com';
            $mail->Password   = 'elma hicz mnfs qsym';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('studybuddynoreply.1@gmail.com', 'StudyBuddy');
            $mail->addAddress($email);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "To reset your password, use this code: $reset_code";

            $mail->send();

            header("Location: verify_reset_code.php?email=" . urlencode($email));
            exit();
        } catch (Exception $e) {
            $errorMessage = "❌ Failed to send reset code. Please try again.";
        }
    } else {
        $errorMessage = "❌ Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f1f7fe;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    h2 {
      color: #205285;
      margin-bottom: 20px;
    }
    input[type="email"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background-color: #205285;
      color: #fff;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
    }
    .message {
      margin-top: 15px;
    }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Forgot Password</h2>
    <?php if ($successMessage): ?>
      <p class="message success"><?= $successMessage ?></p>
    <?php elseif ($errorMessage): ?>
      <p class="message error"><?= $errorMessage ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Reset Code</button>
    </form>
  </div>
</body>
</html>
