<!DOCTYPE html>
<style>
  .top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 32px;
    background-color: #fff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    width: 100%;
  }

  .header-content {
    display: flex;
    align-items: center;
    gap: 40px;
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .main-logo {
    width: 40px;
    height: auto;
  }

  .brand-logo {
    width: 160px;
    height: auto;
  }

  .main-nav {
    display: flex;
    gap: 24px;
    font-family: Inter, sans-serif;
    font-size: 16px;
  }

  .nav-link {
    text-decoration: none;
    color: #2e5b87;
    font-weight: 500;
    position: relative;
  }

  .nav-link:hover {
    color: #1f3e60;
  }

  .nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #2e5b87;
  }

  .header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
  }
.header-content {
  display: flex;
  align-items: center;
  gap: 32px;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.main-logo {
  width: 45px;
  aspect-ratio: 0.69;
  object-fit: contain;
}

.brand-logo {
  width: 199px;
  aspect-ratio: 4.42;
  object-fit: contain;
  margin-top: 5px;
}

.main-nav {
  display: flex;
  font-size: 16px;
  font-weight: 700;
  line-height: 2;
}

.nav-link {
  padding: 15px 24px;
  text-decoration: none;
  color: #565d6d;
}

.nav-link-home {
  color: #9095a1;
}

.nav-link-profile {
  color: #2e5b87;
}

.header-actions {
  display: flex;
  gap: 22px;
  align-items: center;
}

.notification-icon,
.user-menu-icon {
  width: 36px;
  aspect-ratio: 1;
  object-fit: contain;
}

  .notification-icon,
  .user-menu-icon {
    width: 24px;
    height: 24px;
    cursor: pointer;
  }

.nav-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-left: auto; /* aligns the container to the far right */
}

.logout-btn {
  background-color: #b3d9f9;
  color: white;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 6px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  background-color: #2e5b87;
}

.notification-icon,
.profile-menu-icon {
  width: 24px;
  height: 24px;
  object-fit: contain;
  cursor: pointer;
}


</style>

<header class="top-header">
  <div class="header-content">
    <div class="logo-container">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e1084f504b80b21ea37b239ae6b0ffba17fdba06" alt="Logo" class="main-logo" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/91aaa7707268fd69fc21a53872c7b03b6dda9b2e" alt="Brand name" class="brand-logo" />
    </div>
    <nav class="main-nav">
      <a href="../landingpage/home.php" class="nav-link nav-link-home">Home</a>
      <a href="#" class="nav-link nav-link-search">Search</a>
      <a href="../aboutus/aboutus.php" class="nav-link nav-link-about">About Us</a>
      <a href="../student-profile/student-profile-view.php" class="nav-link nav-link-profile">My Profile</a>
    </nav>
  </div>
<!-- Actions aligned to the right -->

<div class="nav-actions">
  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/564684adc0dfa3ea11a464d388756b4cab57a2be" alt="Notification" class="notification-icon" />
  
  <a href="../messages/messages.php">
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/46f3cc461aac63e06d38ebc85be83ae154ff0931" alt="Messages" class="profile-menu-icon" />
  </a>

  <a href="../logout.php" class="logout-btn">Sign Out</a>
</div>


</header>
