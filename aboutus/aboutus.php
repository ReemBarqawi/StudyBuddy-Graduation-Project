<?php
session_start();

$role = $_SESSION['Role'] ?? null;

if ($role === 'buddy') {
    include '../components/navbar-buddy.php';
} elseif ($role === 'student') {
    include '../components/navbar-student.php';
} else {
    include '../components/navbar-main.php';
}
?>

?>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us | StudyBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      overflow-x: hidden;
      background-color: #f9fbff;
      font-family: 'Inter', sans-serif;
    }



    .nav-links a {
      margin: 0 15px;
      text-decoration: none;
      font-weight: 600;
      color: #1a3e5d;
    }

    .nav-links a.active {
      color: #0a66c2;
    }

    .about-wrapper {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 40px;
    }

    .about-wrapper img {
      flex: 1 1 350px;
      max-width: 100%;
      border-radius: 12px;
    }

    .about-text {
      flex: 2 1 600px;
      font-size: 16px;
      color: #0c2238;
      line-height: 1.8;
    }

    .about-text h2 {
      font-family: 'Archivo', sans-serif;
      font-size: 36px;
      color: #004080;
      margin-bottom: 10px;
    }

    .about-text p {
      margin-bottom: 16px;
    }

    .about-text strong {
      display: block;
      font-size: 18px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .about-wrapper {
        flex-direction: column;
        padding: 20px;
      }
    }
  </style>
</head>
<body>


<section class="about-wrapper">
  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/ad5db91fa74ef08512bd54597f1e18d26c66665a?placeholderIfAbsent=true&apiKey=ed5621f216c84d7fbcce34923f81e4c9"style="width: 100%; max-width: 420px; height: auto; border-radius: 12px;" alt="Study group" />
  <div class="about-text">
    <h2>About Us!</h2>
    <strong>Together we learn, together we succeed!</strong>
    <p>At StudyBuddy, we believe that learning thrives through connection. We're a group of passionate students who came together with a single goal: to make academic support more accessible, personalized, and empowering.</p>
    <p>Our platform connects learners with high-achieving peers "buddies" who provide help, guidance, and clarity on tough topics. Whether you're stuck on an assignment or just need someone to explain a concept differently, your buddy is here to support you.</p>
    <p>This project was created by a dedicated team of four students, working side-by-side through design, development, and testing. From brainstorming features to building the database, everything you see reflects our belief in collaborative growth.</p>
  </div>
</section>

</body>
</html>
