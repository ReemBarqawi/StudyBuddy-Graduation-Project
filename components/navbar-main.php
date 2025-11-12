<!-- navbar-main.php -->
<style>
  :root {
    --primary: #2e5b87;
    --white: #ffffff;
    --text-secondary: #565d6d;
  }

  .main-header {
    width: 100%;
    padding: 16px 32px;
    background-color: var(--white);
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-sizing: border-box;
    flex-wrap: wrap;
    z-index: 10;
  }

  .left-side {
    display: flex;
    align-items: center;
    gap: 44px;
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 9px;
  }

  .logo-icon {
    width: 45px;
    aspect-ratio: 0.69;
    object-fit: contain;
  }

  .logo-text {
    width: 199px;
    aspect-ratio: 4.42;
    object-fit: contain;
  }

  .main-nav {
    display: flex;
    align-items: center;
    font-size: 16px;
    gap: 16px;
  }

  .nav-link {
    padding: 8px 16px;
    text-decoration: none;
    color: var(--text-secondary);
  }

  .nav-link-active {
    color: var(--primary);
    font-weight: 700;
  }

  .auth-buttons {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .btn {
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    padding: 9px 24px;
    font-weight: 500;
  }

  .btn-register {
    background-color: var(--primary);
    color: var(--white);
  }

  .btn-signin {
    background-color: rgba(243, 247, 251, 1);
    color: var(--primary);
  }

  @media (max-width: 768px) {
    .main-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .left-side {
      flex-direction: column;
      align-items: flex-start;
      gap: 20px;
    }

    .auth-buttons {
      margin-top: 12px;
    }
  }
</style>

<header class="main-header">
  <div class="left-side">
    <div class="logo-container">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e1084f504b80b21ea37b239ae6b0ffba17fdba06" alt="Logo icon" class="logo-icon">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/91aaa7707268fd69fc21a53872c7b03b6dda9b2e" alt="Logo text" class="logo-text">
    </div>
    <nav class="main-nav">
      <a href="../landingpage/landingpage.php" class="nav-link nav-link-active">Home</a>
      <a href="../aboutus/aboutus.php" class="nav-link">About Us</a>
    </nav>
  </div>
  <div class="auth-buttons">
    <button class="btn btn-register" onclick="location.href='../Sign-up/signup.php'">Register</button>
    <button class="btn btn-signin" onclick="location.href='../Sign-in/signin.php'">Sign in</button>
  </div>
</header>