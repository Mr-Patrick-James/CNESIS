# CNESIS (Colegio de Naujan Enrollment System)

CNESIS is a comprehensive web-based enrollment and student information management system designed for Colegio de Naujan. It streamlines the admission process, student record management, and administrative reporting tasks.

---

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [Prerequisites](#-prerequisites)
- [Installation Guide](#-installation-guide)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Project Structure](#-project-structure)
- [Troubleshooting](#-troubleshooting)

---

## 🚀 Features

### Admin Portal
- **Dashboard**: Real-time overview of enrollment statistics, recent admissions, and system status.
- **Student Management**: Full CRUD capabilities for student records with search and filter options.
- **Admissions Management**: Review and process online admission applications (Approve/Reject/Pending).
- **Program Management**: Manage academic programs, curriculum details, and program heads.
- **Reports & Analytics**: Generate detailed reports for admissions, enrollment trends, and prospectus downloads.
  - **Export Options**: Support for PDF and Excel (XLSX) exports.
- **Settings**: System-wide configuration and user management.

### Student/Public Portal
- **Online Admission**: Web-based application form for new students.
- **Inquiry System**: Contact form for prospective students and parents.
- **Program Information**: View details about available courses and download prospectuses.
- **Admission Status**: Check the status of submitted applications.

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ (Native/Vanilla), PDO for Database Interaction
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5
- **Database**: MySQL 5.7+
- **Libraries & Tools**:
  - [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email notifications
  - [jsPDF](https://github.com/parallax/jsPDF) - PDF generation
  - [SheetJS (xlsx)](https://github.com/SheetJS/sheetjs) - Excel generation
  - [FontAwesome](https://fontawesome.com/) - Icons

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **Web Server**: Apache (via WAMP, XAMPP, or similar)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Composer** (Optional, for managing PHP dependencies)

---

## ⚙️ Installation Guide

### 1. Clone the Repository
Download the project files and place them in your web server's root directory.
- For **WAMP**: `C:\wamp64\www\CNESIS`
- For **XAMPP**: `C:\xampp\htdocs\CNESIS`

### 2. Database Setup
1. Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
2. Create a new database named `cnesis_db`.
3. Import the SQL file located at `database/cnesis_db.sql`.
   - If starting fresh, you can use `database/setup.sql`.

### 3. Create Admin User
To ensure you can log in, run the provided admin setup script.
1. Open your terminal/command prompt.
2. Navigate to the project directory:
   ```bash
   cd C:\wamp64\www\CNESIS
   ```
3. Run the setup script:
   ```bash
   php database/create_admin.php
   ```
   *This will create or reset the default admin user.*

---

## 🔧 Configuration

### Database Connection
Open `api/config/database.php` and verify your credentials:
```php
private $host = "localhost";
private $db_name = "cnesis_db";
private $username = "root";
private $password = ""; // Default for WAMP is empty
```

### Email Settings
The system uses **PHPMailer** for sending notifications.
1. You can configure email settings in the database table `email_configs`.
2. Alternatively, modify the fallback settings in `api/config/email_config.php`:
   ```php
   private function getFallbackConfig() {
       return [
           'smtp_host' => 'smtp.gmail.com',
           'smtp_port' => 587,
           'smtp_username' => 'your-email@gmail.com',
           'smtp_password' => 'your-app-password', // Use App Password for Gmail
           // ...
       ];
   }
   ```

---

## 💻 Usage

### Admin Login
- **URL**: `http://localhost/CNESIS/views/admin/auth/login.html` (or access via main page Login button)
- **Username**: `admin_demo@colegio.edu`
- **Password**: `password123`

### Student Admission
- **URL**: `http://localhost/CNESIS/`
- Click "Apply Now" to start a new application.

---

## 📡 API Documentation

Key API endpoints used by the frontend:

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login.php` | Authenticate users (Admin/Faculty) |
| GET | `/api/students/get-all.php` | Retrieve list of students |
| POST | `/api/admissions/create.php` | Submit new admission application |
| GET | `/api/reports/generate-report.php` | Generate JSON data for reports |
| POST | `/api/programs/track-prospectus-download.php` | Track prospectus downloads |

---

## 📂 Project Structure

```
CNESIS/
├── api/                # Backend API endpoints (PHP)
│   ├── config/         # Database and Email configuration
│   ├── auth/           # Authentication logic
│   ├── admissions/     # Admission processing
│   ├── students/       # Student CRUD operations
│   └── reports/        # Reporting logic
├── assets/             # Frontend static files
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   ├── img/            # Images
│   └── uploads/        # User uploaded files (docs, images)
├── database/           # SQL scripts and migrations
│   ├── setup.sql       # Initial database structure
│   └── create_admin.php # Admin user setup script
├── views/              # Frontend views (HTML/PHP)
│   ├── admin/          # Admin dashboard and features
│   └── user/           # Public/Student pages
├── vendor/             # Composer dependencies
└── index.php           # Landing page
```

---

## ❓ Troubleshooting

**Issue: "Database connection failed"**
- Check if MySQL service is running in WAMP/XAMPP.
- Verify credentials in `api/config/database.php`.
- Ensure database `cnesis_db` exists.

**Issue: "Login failed"**
- Run `php database/create_admin.php` to reset the admin password.
- Check if the `users` table exists in the database.

**Issue: Emails not sending**
- Verify SMTP settings in `api/config/email_config.php`.
- If using Gmail, ensure "Less Secure Apps" is enabled or use an App Password.

---

## � License

This project is proprietary software developed for Colegio de Naujan.
