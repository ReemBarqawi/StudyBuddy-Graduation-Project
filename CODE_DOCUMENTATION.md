# Study Buddy - Code Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [File Structure](#file-structure)
3. [Key Files Explained](#key-files-explained)
4. [Database Schema](#database-schema)
5. [Common Functions & Patterns](#common-functions--patterns)
6. [Security Features](#security-features)
7. [TODO & Improvements](#todo--improvements)

---

## Project Overview

Study Buddy is a web-based learning management system built with PHP and MySQL. It connects students with tutors (called "Buddies") through a course-based learning platform.

**Core Technologies:**
- Backend: PHP 7.4+ (vanilla, no framework)
- Database: MySQL/MariaDB
- Frontend: HTML5, CSS3, Vanilla JavaScript
- Email: PHPMailer library
- Hosting: InfinityFree (free hosting)

---

## File Structure

```
study-buddy/
│
├── Sign-in/                    # User authentication
│   ├── signin.php              # Login page
│   ├── forgot_password.php     # Password reset request
│   ├── send_reset_code.php     # Send reset code via email
│   ├── verify_reset_code.php   # Verify reset code
│   └── reset_request.php       # Complete password reset
│
├── Sign-up/                    # User registration
│   ├── signup.php              # Registration form & processing
│   └── verify.php              # Email verification page
│
├── includes/                   # Shared PHP files
│   ├── db_connect.php          # Database connection
│   └── PHPMailer/              # Email sending library
│
├── components/                 # Reusable UI components
│   ├── navbar-main.php         # Public navigation bar
│   ├── navbar-student.php      # Student navigation bar
│   └── navbar-buddy.php        # Buddy navigation bar
│
├── landingpage/                # Public pages
│   ├── landingpage.php         # Homepage
│   ├── home.php                # Alternative homepage
│   └── course-view.php         # Individual course details
│
├── buddy-profile/              # Buddy profile pages
│   └── buddy-profile-view.php  # View/edit buddy profile
│
├── student-profile/            # Student profile pages
│   └── student-profile-view.php # View/edit student profile
│
├── creatcourse/                # Course creation
│   ├── create-course-basic-info.php  # Step 1: Basic details
│   ├── create-course-add-content.php # Step 2: Add lessons
│   └── uploads/                      # Course files storage
│       ├── thumbnails/               # Course thumbnail images
│       └── content/                  # Videos, PDFs, etc.
│
├── course-path/                # Student course viewing
│   └── course-path.php         # View enrolled course content
│
├── editpage/                   # Profile editing
│   └── edit-my-profile.php     # Edit user profile
│
├── aboutus/                    # About page
│   └── aboutus.php             # Platform information
│
├── assets/                     # Static assets
│   ├── tick-icon.png           # Checkmark icon
│   └── gray-tick-icon.png      # Gray checkmark
│
└── uploads/                    # User uploads
    ├── profile/                # Profile pictures
    └── content/                # Other user content
```

---

## Key Files Explained

### 1. includes/db_connect.php
**Purpose:** Establishes MySQL database connection for all pages

**Key Components:**
- Server credentials (hostname, username, password, database name)
- MySQLi connection object creation
- Error handling for connection failures

**Usage Example:**
```php
include '../includes/db_connect.php';
// $conn is now available for database queries
$result = $conn->query("SELECT * FROM users");
```

**Security Notes:**
- Credentials should be moved to environment variables in production
- Never commit real credentials to version control

---

### 2. Sign-up/signup.php
**Purpose:** User registration with email verification

**Process Flow:**
1. User fills registration form (name, email, password, role)
2. Server validates input (password match, role selected)
3. Checks if email already exists in database
4. Generates 6-digit verification code
5. Hashes password using `password_hash()`
6. Inserts user into database (unverified status)
7. Sends verification code via email using PHPMailer
8. Redirects to verification page

**Key Functions:**
- `password_hash()` - Securely hash passwords before storage
- `mysqli_prepare()` - Create prepared statements to prevent SQL injection
- `PHPMailer->send()` - Send verification email via SMTP

**Database Interaction:**
```php
// Check if email exists
$check_query = "SELECT * FROM users WHERE Email = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "s", $email);

// Insert new user
$insert_query = "INSERT INTO users (First_name, Last_name, Email, Password, Role, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
```

---

### 3. Sign-in/signin.php
**Purpose:** User authentication and login

**Process Flow:**
1. User enters email and password
2. Server retrieves user data from database
3. Verifies password using `password_verify()`
4. Checks if account is verified
5. Creates session and stores user data
6. Redirects based on role (student/buddy/admin)

**Key Security Features:**
- Password verification against hashed passwords
- Session management for persistent login
- Account verification check before allowing login

---

### 4. landingpage/landingpage.php
**Purpose:** Public homepage displaying platform features and courses

**Key Features:**
- Hero section with CTAs
- Features showcase
- "How it works" guide
- Testimonial section
- Recently added courses (dynamic from database)
- Footer with navigation

**Database Query:**
```sql
SELECT 
  c.course_id, c.name, c.thumbnail,
  u.First_name AS buddy_name,
  COUNT(e.User_ID) AS total_students
FROM courses c
JOIN users u ON c.User_ID = u.User_ID
LEFT JOIN enrollment e ON e.Course_ID = c.course_id AND e.Payment = 'completed'
WHERE c.status = 'approved'
GROUP BY c.course_id
ORDER BY c.course_id DESC
LIMIT 3
```

**Purpose of Query:**
- Shows only approved courses
- Counts enrolled students (with completed payment)
- Displays buddy name
- Limits to 3 most recent courses

---

### 5. creatcourse/create-course-basic-info.php
**Purpose:** First step in course creation - basic information

**Form Fields:**
- Thumbnail image (optional)
- Course name
- Course number (e.g., WEB101)
- Course description
- Course price

**File Upload Handling:**
```php
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
    $fileName = basename($_FILES["thumbnail"]["name"]);
    $uploadDir = "uploads/thumbnails/";
    $targetPath = $uploadDir . $fileName;
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $targetPath)) {
        $thumbnail = $fileName;
    }
}
```

**Next Steps:**
- After submission, redirects to `create-course-add-content.php`
- Course ID is passed in URL to link content to this course

---

### 6. creatcourse/create-course-add-content.php
**Purpose:** Second step - add lessons, videos, documents to course

**Functionality:**
- Upload video files
- Upload PDF documents
- Upload other course materials
- Each upload is linked to the course via course_id

**Database Table:** course_content
- Links content items to parent course
- Stores file paths, types, titles

---

### 7. buddy-profile/buddy-profile-view.php
**Purpose:** Display and edit buddy profile information

**Features:**
- View profile information
- View created courses
- See total enrolled students
- Edit profile (bio, profile picture)

**Typical Query:**
```sql
SELECT * FROM users WHERE User_ID = ? AND Role = 'buddy'
```

---

### 8. student-profile/student-profile-view.php
**Purpose:** Display and edit student profile information

**Features:**
- View enrolled courses
- Track learning progress
- Edit profile information

---

### 9. course-path/course-path.php
**Purpose:** Student's view of enrolled course content

**Features:**
- Display course lessons sequentially
- Show videos, PDFs, materials
- Track progress through course

**Access Control:**
- Only enrolled students can access
- Must have completed payment

---

## Database Schema

### Main Tables

#### users
Stores all user accounts (students, buddies, admins)

```sql
CREATE TABLE users (
  User_ID bigint PRIMARY KEY AUTO_INCREMENT,
  First_name varchar(50),
  Last_name varchar(50),
  Email varchar(100) UNIQUE,
  Password varchar(255),           -- Hashed password
  Role enum('student','buddy','admin'),
  Bio text,
  Image text,                      -- Profile picture filename
  verification_code varchar(255),  -- Email verification
  is_verified tinyint(1) DEFAULT 0,
  reset_code varchar(10)           -- Password reset code
);
```

#### courses
Stores all courses created by buddies

```sql
CREATE TABLE courses (
  course_id int PRIMARY KEY AUTO_INCREMENT,
  name varchar(255),
  number varchar(50),              -- Course code (WEB101)
  description text,
  price decimal(10,2),
  User_ID bigint,                  -- Foreign key to users (buddy)
  thumbnail varchar(255),          -- Image filename
  status enum('pending','approved','rejected') DEFAULT 'pending',
  FOREIGN KEY (User_ID) REFERENCES users(User_ID)
);
```

#### enrollment
Links students to courses they've enrolled in

```sql
CREATE TABLE enrollment (
  enrollment_id int PRIMARY KEY AUTO_INCREMENT,
  User_ID bigint,                  -- Foreign key to users (student)
  Course_ID int,                   -- Foreign key to courses
  enrollment_date datetime DEFAULT CURRENT_TIMESTAMP,
  Payment enum('pending','completed') DEFAULT 'pending',
  FOREIGN KEY (User_ID) REFERENCES users(User_ID),
  FOREIGN KEY (Course_ID) REFERENCES courses(course_id)
);
```

#### course_content
Stores individual lessons/materials within courses

```sql
CREATE TABLE course_content (
  content_id int PRIMARY KEY AUTO_INCREMENT,
  Course_ID int,                   -- Foreign key to courses
  title varchar(255),
  file_path varchar(500),          -- Path to video/PDF/file
  content_type enum('video','pdf','document','other'),
  upload_date datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (Course_ID) REFERENCES courses(course_id)
);
```

### Relationships
- Users (Buddy) → Courses (One-to-Many)
- Courses → Course Content (One-to-Many)
- Users (Student) → Enrollment → Courses (Many-to-Many)

---

## Common Functions & Patterns

### 1. Prepared Statements (SQL Injection Prevention)
```php
// Bad (vulnerable to SQL injection)
$query = "SELECT * FROM users WHERE email = '$email'";

// Good (using prepared statement)
$query = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
```

### 2. Password Hashing
```php
// During registration
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// During login
if (password_verify($input_password, $stored_hashed_password)) {
    // Password is correct
}
```

### 3. Session Management
```php
// Start session at beginning of protected pages
session_start();

// Store user data in session after login
$_SESSION['User_ID'] = $user_id;
$_SESSION['Role'] = $role;

// Check if user is logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: ../Sign-in/signin.php");
    exit();
}

// Destroy session on logout
session_destroy();
```

### 4. File Upload Handling
```php
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $fileName = basename($_FILES["file"]["name"]);
    $targetPath = "uploads/" . $fileName;
    
    // Validate file type (security)
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (in_array($ext, $allowed)) {
        move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath);
    }
}
```

### 5. Error Handling & Redirection
```php
// Redirect with error parameter
header("Location: signup.php?error=email");
exit();

// JavaScript checks URL parameters and shows alerts
const error = new URLSearchParams(window.location.search).get("error");
if (error === "email") {
    alert("Email already exists!");
}
```

---

## Security Features

### 1. Password Security
- **Hashing:** All passwords are hashed using `password_hash()` with bcrypt algorithm
- **Never stored in plain text**
- **One-way hashing** - cannot be reversed

### 2. SQL Injection Prevention
- **Prepared statements** used throughout
- **Parameter binding** for all user inputs
- **No direct string concatenation in SQL queries**

### 3. Email Verification
- **6-digit verification codes** sent to email
- **Account remains unverified** until code is entered
- **Unverified users cannot log in**

### 4. Session Management
- **Session-based authentication**
- **User ID and role stored in session**
- **Sessions expire after browser close or timeout**

### 5. File Upload Security
- **File type validation**
- **Sanitized filenames** using `basename()`
- **Restricted upload directories**

### 6. XSS Prevention
- **`htmlspecialchars()`** used to display user input
- **Escapes HTML special characters**
- **Prevents malicious script injection**

---

## TODO & Improvements

### High Priority
1. **Environment Variables**
   - Move database credentials to `.env` file
   - Remove hardcoded SMTP credentials
   - Add `.env.example` template

2. **Error Logging**
   - Log errors to file instead of displaying to users
   - Implement proper error handling for production

3. **Input Validation**
   - Add server-side validation for all form inputs
   - Validate file uploads (size, type, content)
   - Sanitize all user inputs

4. **Payment Integration**
   - Integrate PayPal or Stripe payment gateway
   - Currently payment status is hardcoded

### Medium Priority
5. **Role-Based Access Control**
   - Create middleware to check user roles
   - Prevent students from accessing buddy pages
   - Prevent unauthorized course editing

6. **Progress Tracking**
   - Track which lessons students have completed
   - Show progress bars on course pages
   - Certificate generation on completion

7. **Search & Filtering**
   - Add search functionality for courses
   - Filter by category, price, rating
   - Sort by popularity, date, price

8. **Admin Dashboard**
   - Create admin interface for course approval
   - User management panel
   - Analytics and statistics

### Low Priority
9. **Email Templates**
   - Create HTML email templates
   - Better formatting for verification codes
   - Welcome emails for new users

10. **API Development**
    - Create REST API for mobile apps
    - JSON responses for all data
    - JWT authentication

11. **Testing**
    - Unit tests for critical functions
    - Integration tests for user flows
    - Security testing

12. **Documentation**
    - API documentation
    - Deployment guide
    - User manual

---

## Debugging Tips

### Common Issues

**Issue:** "Connection failed: Access denied"
- **Solution:** Check database credentials in `db_connect.php`

**Issue:** "Headers already sent" error
- **Solution:** Ensure no output before `header()` calls
- **Solution:** Check for whitespace before `<?php` tags

**Issue:** Email not sending
- **Solution:** Verify SMTP credentials
- **Solution:** Check if Gmail "Less secure app access" is enabled
- **Solution:** Use App-specific password instead of regular password

**Issue:** File upload fails
- **Solution:** Check directory permissions (should be 755 or 777)
- **Solution:** Verify upload path exists
- **Solution:** Check PHP upload_max_filesize and post_max_size settings

**Issue:** Session data not persisting
- **Solution:** Ensure `session_start()` is called before accessing `$_SESSION`
- **Solution:** Check server session storage configuration

---

## Useful Commands

```bash
# Check PHP version
php -v

# Check MySQL connection
mysql -u username -p

# View PHP configuration
php -i | grep upload

# Set file permissions
chmod 755 directory_name
chmod 644 file_name

# Check error logs (Linux)
tail -f /var/log/apache2/error.log

# Clear sessions (development only)
rm -rf /tmp/sess_*
```

---

## Contact & Support

For questions or issues with this codebase:
1. Check this documentation first
2. Review comments in individual files
3. Contact the development team

**Last Updated:** May 2025
**Version:** 1.0
**Developer:** Study Buddy Team
