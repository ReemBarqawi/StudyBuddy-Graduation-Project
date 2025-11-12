<p align="center">
  <img src="uploads/content/logo.png" alt="Study Buddy Logo" width="200">
</p>

<h1 align="center">🎓 Study Buddy – Student & Tutor Platform</h1>

## 📘 Overview
**Study Buddy** is a **web-based learning management system (LMS)** that connects students with tutors (called **Buddies**) to create a collaborative learning environment.  
Tutors can design and publish their own courses, while students can explore, enroll, and learn at their own pace.

---

## 🚀 Key Features

### 👤 User Management
- Separate registration for **Students**, **Buddies**, and **Admins**
- **Email verification** on sign-up
- **Secure login** and **password recovery** with email-based verification codes

### 🎓 Learning System
- **Course Management**: Buddies can create, edit, and publish courses with thumbnails, descriptions, and pricing  
- **Course Content**: Upload videos, PDFs, and other learning materials  
- **Enrollment System**: Students can enroll in courses (payment simulation included)  
- **Admin Approval**: Courses must be reviewed and approved before publication

### ⚙️ Platform Management
- **Role-Based Access**: Distinct dashboards and permissions for each user role  
- **Profile Management**: Users can edit their bio, personal details, and profile picture  
- **Security**: Uses PHP’s built-in `password_hash()` and SQL prepared statements  

---

## 🧠 Technology Stack

| Layer | Technologies |
|-------|---------------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla JS) |
| **Backend** | PHP (Vanilla, no frameworks) |
| **Database** | MySQL / MariaDB |
| **Email Service** | PHPMailer (SMTP-based verification) |
| **File Storage** | Local server directories |
| **Hosting** | InfinityFree (free web hosting) |

---

## 🗂️ Project Structure

```
study-buddy/
├── Sign-in/              # Login & password recovery
├── Sign-up/              # Registration & email verification
├── aboutus/              # About page
├── assets/               # Icons, images, and static files
├── buddy-profile/        # Tutor profile pages
├── components/           # Shared UI components (e.g., navbar)
├── course-path/          # Student course interface
├── creatcourse/          # Course creation & management
├── editpage/             # Profile editing functionality
├── includes/             # Shared backend files (DB, PHPMailer)
├── landingpage/          # Homepage & course browsing
├── student-profile/      # Student dashboard & profiles
└── uploads/              # Uploaded user files (images, videos, docs)
```

---

## 🧩 Database Schema

### Main Tables
| Table | Description |
|--------|-------------|
| **users** | Stores all registered users (students, buddies, admins) |
| **courses** | Contains course details created by buddies |
| **enrollment** | Links students to their enrolled courses |
| **course_content** | Holds lessons, videos, and materials for each course |

### Relationships
- **User → Courses** → (One-to-Many)  
- **Courses → Course Content** → (One-to-Many)  
- **Students ↔ Courses** → (Many-to-Many via Enrollment)

---

## ⚙️ Installation Guide

### 🔧 Prerequisites
- PHP 7.4 or higher  
- MySQL / MariaDB  
- Apache or Nginx web server  
- SMTP-enabled email account (for PHPMailer)


## 👥 User Roles

### 🧑‍🎓 Student
- Browse and enroll in approved courses  
- Access learning materials (videos, documents)  
- Track enrolled courses and update personal profile  

### 🧑‍🏫 Buddy (Tutor)
- Create, manage, and publish courses  
- Upload course content (videos, PDFs, etc.)  
- Manage enrolled students and course pricing  

### 🧑‍💼 Admin
- Approve or reject submitted courses  
- Manage users and platform content  
- Ensure system integrity and compliance  

---

## 🔒 Security Features
- Passwords hashed with `password_hash()`  
- Email verification for account activation  
- Secure password reset (with time-limited codes)  
- Prepared statements to prevent SQL injection  
- Role-based access to restrict unauthorized actions  

---

## ⚠️ Known Limitations

1. No real payment integration (currently simulated)  
2. Lacks real-time messaging  
3. No student progress tracking  
4. Limited search and filtering options  
5. No API endpoints for mobile apps  
6. Basic front-end without modern frameworks  

---

## 💡 Future Enhancements

| Feature | Description |
|----------|-------------|
| 💳 Payment Gateway | Integrate Stripe, PayPal, or local methods |
| 💬 Real-time Chat | WebSocket or Firebase-based messaging |
| 📈 Progress Tracking | Monitor course completion and quiz results |
| 🔍 Advanced Filters | Sort by category, price, duration, or rating |
| 🔔 Notifications | Email and in-app alerts for updates |
| 📱 Mobile App | Flutter or React Native integration |
| 📊 Analytics Dashboard | For tutors to monitor course performance |
| ⭐ Course Reviews | Students can rate and review courses |
| 🎓 Certificates | Generate completion certificates |
| 🎥 Video Streaming | Integrate with Vimeo or AWS S3 |

---

## 📤 File Upload Guidelines

| File Type | Formats | Max Size | Notes |
|------------|----------|----------|-------|
| Profile Images | JPG, PNG | 5 MB | Square format recommended |
| Course Thumbnails | JPG, PNG | 5 MB | Recommended: 1280x720 |
| Course Content | MP4, AVI, PDF, DOCX | 100 MB | For lessons and resources |


---

## 🧾 License
This project was developed as part of an academic graduation project.  
All rights reserved © 2025.

---

## 👩‍💻 Credits
**Developed by:** Reem Jamal Barqawi  
**Institution:** Yarmouk University  
**Year:** 2025  
