<?php
// صفحة إرسال كود إعادة تعيين كلمة المرور
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password Request</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f7fe;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #fff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
      text-align: center;
    }

    h2 {
      color: #205285;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type="email"] {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button {
      padding: 12px;
      border: none;
      background-color: #205285;
      color: #fff;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    .message {
      margin-top: 15px;
      font-size: 14px;
      color: red;
    }

    .success {
      color: green;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Your Password?</h2>
    <p>Enter your email and we’ll send you a verification code.</p>
    <form method="post" action="send_reset_code.php">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Code</button>
    </form>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'notfound'): ?>
      <p class="message">❌ Email not found.</p>
    <?php elseif (isset($_GET['success'])): ?>
      <p class="message success">✅ Code sent to your email. Check your inbox.</p>
    <?php endif; ?>
  </div>
</body>
</html>
<?php
// صفحة إرسال كود إعادة تعيين كلمة المرور
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password Request</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f7fe;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #fff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 420px;
      text-align: center;
    }

    h2 {
      color: #205285;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type="email"] {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button {
      padding: 12px;
      border: none;
      background-color: #205285;
      color: #fff;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    .message {
      margin-top: 15px;
      font-size: 14px;
      color: red;
    }

    .success {
      color: green;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Your Password?</h2>
    <p>Enter your email and we’ll send you a verification code.</p>
    <form method="post" action="send_reset_code.php">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Code</button>
    </form>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'notfound'): ?>
      <p class="message">❌ Email not found.</p>
    <?php elseif (isset($_GET['success'])): ?>
      <p class="message success">✅ Code sent to your email. Check your inbox.</p>
    <?php endif; ?>
  </div>
</body>
</html>
