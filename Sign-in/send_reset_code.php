<?php
include '../includes/db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // الاتصال بقاعدة البيانات
    include('../includes/db.php');

    // التحقق من وجود المستخدم
    $check_query = "SELECT * FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $reset_code = rand(100000, 999999);

        // تحديث كود الاستعادة في قاعدة البيانات
        $update = "UPDATE users SET reset_code = ? WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "ss", $reset_code, $email);
        mysqli_stmt_execute($stmt);

        // إرسال الكود بالإيميل (تضيف هنا كود PHPMailer لاحقًا)
        // للتجربة فقط نعرض الكود مباشرة
        $successMessage = "✅ A verification code has been sent to your email (for now: $reset_code)";
        header("Location: verify_reset_code.php?email=" . urlencode($email));
        exit();
    } else {
        $errorMessage = "❌ Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <style>
    body { font-family: Arial; background: #f1f7fe; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
    h2 { color: #205285; text-align: center; }
    input[type="email"], button { width: 100%; padding: 12px; margin-top: 15px; border: 1px solid #ccc; border-radius: 6px; }
    button { background: #205285; color: white; border: none; cursor: pointer; }
    .message { margin-top: 15px; font-size: 14px; text-align: center; }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="box">
    <h2>Forgot Password</h2>

    <?php if ($successMessage): ?>
      <p class="message success"><?= $successMessage ?></p>
    <?php elseif ($errorMessage): ?>
      <p class="message error"><?= $errorMessage ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Code</button>
    </form>
  </div>
</body>
</html>
