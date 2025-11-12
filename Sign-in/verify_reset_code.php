<?php
include '../includes/db_connect.php';

$errorMessage = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);

    $query = "SELECT reset_code FROM users WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $db_code = $row['reset_code'];

        if ($code === $db_code) {
            $update = "UPDATE users SET reset_code = NULL WHERE Email = ?";
            $stmt = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } else {
            $errorMessage = "❌ Invalid verification code.";
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
  <title>Verify Reset Code</title>
  <style>
    body {
      font-family: "Inter", sans-serif;
      background-color: #f1f7fe;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .verify-container {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      text-align: center;
      color: #205285;
      margin-bottom: 25px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type="email"],
    input[type="text"] {
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      padding: 12px;
      background-color: #205285;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #163d63;
    }

    .error-message {
      color: red;
      text-align: center;
      margin-top: 10px;
      font-size: 15px;
    }
  </style>
</head>
<body>

  <div class="verify-container">
    <h2>Verify Reset Code</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="text" name="code" placeholder="Enter your code" required />
      <button type="submit">Verify Code</button>
      <?php if ($errorMessage): ?>
        <p class="error-message"><?= $errorMessage ?></p>
      <?php endif; ?>
    </form>
  </div>

</body>
</html>

