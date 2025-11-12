
<head>
    <style>
 .header {
  background-color: #fff;
  box-shadow: 0px 0px 2px rgba(255, 255, 255, 0.12);
  padding: 23px 40px 13px;
  width: 100%;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 13px;
}

.logo-icon {
  width: 45px;
  object-fit: contain;
}

.logo-text {
  width: 199px;
  margin-top: 5px;
  object-fit: contain;
}

.main-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-grow: 1;
  margin-left: 40px;
}

.nav-menu {
  display: flex;
  list-style: none;
  gap: 0;
  margin: 0;
  padding: 0;
}

.nav-link {
  color: #565d6d;
  font-size: 16px;
  font-weight: 700;
  line-height: 2;
  padding: 15px 24px;
  text-decoration: none;
  display: block;
}

.nav-link.active {
  color: #2e5b87;
}

.nav-link:hover {
  color: #3a444f;
  text-decoration: none;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-left: auto; /* pushes actions to the far right */
}

.profile-menu-icon {
  width: 33px;
  margin-top: 7px;
}

.logout-btn {
  background-color: #b3d9f9;
  color: white;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 6px;
  text-decoration: none;
  transition: background 0.3s;
}

.logout-btn:hover {
  background-color: #2e5b87;
}

  </style>
<head>
<header class="header">
  <div class="header-container">
    <div class="logo-section">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e1084f504b80b21ea37b239ae6b0ffba17fdba06" alt="Logo Icon" class="logo-icon" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/91aaa7707268fd69fc21a53872c7b03b6dda9b2e" alt="Logo Text" class="logo-text" />
    </div>
    <nav class="main-nav">
<nav class="main-nav">
  <ul class="nav-menu">
    <li><a href="../landingpage/home.php" class="nav-link">Home</a></li>
    <li><a href="../aboutus/aboutus.php" class="nav-link">About Us</a></li>
    <li><a href="../buddy-profile/buddy-profile-view.php" class="nav-link active">My Profile</a></li>
  </ul>
        <!-- Put these in their own container so they align right -->
  <div class="nav-actions">
        <a href="../messages/messages.php">
          <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/46f3cc461aac63e06d38ebc85be83ae154ff0931" alt="Messages" class="profile-menu-icon" />
        </a>
        <a href="../logout.php" class="logout-btn">Sign Out</a>
  </div>
</nav>
  </div>
</header>
