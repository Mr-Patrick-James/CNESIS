-- ============================================
-- COLEGIO DE NAUJAN DATABASE SETUP
-- Complete Database Setup for WAMP Server
-- Copy and paste this entire file into phpMyAdmin SQL tab
-- ============================================

-- Drop database if exists (CAUTION: This will delete existing data)
DROP DATABASE IF EXISTS cnesis_db;

-- Create database
CREATE DATABASE cnesis_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE cnesis_db;

-- ============================================
-- TABLE: programs
-- Stores all academic programs information
-- ============================================
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE COMMENT 'Program code (e.g., BSIS, BPA)',
    title VARCHAR(255) NOT NULL COMMENT 'Full program name',
    short_title VARCHAR(100) NOT NULL COMMENT 'Short display name',
    category ENUM('undergraduate', 'technical') NOT NULL DEFAULT 'undergraduate',
    department VARCHAR(100) NOT NULL COMMENT 'Department name',
    description TEXT NOT NULL COMMENT 'Program description',
    duration VARCHAR(50) NOT NULL COMMENT 'Program duration (e.g., 4 Years)',
    units VARCHAR(50) NOT NULL COMMENT 'Total units (e.g., 158 Units)',
    image_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to program image',
    prospectus_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to prospectus file',
    enrolled_students INT DEFAULT 0 COMMENT 'Number of enrolled students',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    highlights TEXT DEFAULT NULL COMMENT 'JSON array of program highlights',
    career_opportunities TEXT DEFAULT NULL COMMENT 'JSON array of career opportunities',
    admission_requirements TEXT DEFAULT NULL COMMENT 'JSON array of admission requirements',
    program_head_id INT DEFAULT NULL COMMENT 'Foreign key to program_heads table',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_code (code),
    INDEX idx_program_head_id (program_head_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: program_heads
-- Stores program head information and assignments
-- ============================================
CREATE TABLE program_heads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL UNIQUE COMMENT 'Employee ID number',
    first_name VARCHAR(100) NOT NULL COMMENT 'First name',
    middle_name VARCHAR(100) DEFAULT NULL COMMENT 'Middle name',
    last_name VARCHAR(100) NOT NULL COMMENT 'Last name',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email address',
    phone VARCHAR(20) NOT NULL COMMENT 'Phone number',
    department VARCHAR(100) NOT NULL COMMENT 'Department assignment',
    specialization VARCHAR(150) DEFAULT NULL COMMENT 'Area of specialization',
    hire_date DATE NOT NULL COMMENT 'Date of hiring',
    status ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Employment status',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_employee_id (employee_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint for program head relationship
ALTER TABLE programs 
ADD CONSTRAINT fk_program_head 
FOREIGN KEY (program_head_id) REFERENCES program_heads(id) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- TABLE: admissions
-- Stores student admission applications
-- ============================================
CREATE TABLE admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique application ID',
    student_id VARCHAR(20) DEFAULT NULL COMMENT 'Existing student ID (NULL for new freshmen)',
    program_id INT DEFAULT NULL COMMENT 'Foreign key to programs table',
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    birthdate DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    address TEXT DEFAULT NULL,
    high_school VARCHAR(255) DEFAULT NULL COMMENT 'High school (for freshmen)',
    last_school VARCHAR(255) DEFAULT NULL COMMENT 'Last school attended (for old students)',
    year_graduated INT DEFAULT NULL COMMENT 'Graduation year',
    gwa DECIMAL(3,2) DEFAULT NULL COMMENT 'General Weighted Average',
    entrance_exam_score INT DEFAULT NULL COMMENT 'Entrance exam score',
    admission_type ENUM('freshman', 'transferee', 'returnee', 'shifter') NOT NULL DEFAULT 'freshman',
    previous_program VARCHAR(100) DEFAULT NULL COMMENT 'Previous program (for transferees/shifters)',
    status ENUM('pending', 'approved', 'rejected', 'enrolled') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL DEFAULT NULL,
    reviewed_by INT DEFAULT NULL COMMENT 'Admin user ID who reviewed',
    notes TEXT DEFAULT NULL COMMENT 'Admin notes',
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_program (program_id),
    INDEX idx_submitted (submitted_at),
    INDEX idx_student_id (student_id),
    INDEX idx_admission_type (admission_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: users
-- Stores admin and staff user accounts
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'faculty', 'program_head') NOT NULL DEFAULT 'staff',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: students
-- Stores student information
-- ============================================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    section_id INT DEFAULT NULL,
    yearlevel INT DEFAULT NULL,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_email (email),
    INDEX idx_department (department),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: departments
-- Stores department information
-- ============================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_code VARCHAR(20) NOT NULL UNIQUE,
    department_name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    head_of_department VARCHAR(100) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_department_code (department_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: sections
-- Stores class sections
-- ============================================
CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_code VARCHAR(20) NOT NULL UNIQUE,
    section_name VARCHAR(100) NOT NULL,
    department_code VARCHAR(20) NOT NULL,
    year_level INT NOT NULL,
    capacity INT DEFAULT 40,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_section_code (section_code),
    INDEX idx_department_code (department_code),
    INDEX idx_year_level (year_level),
    INDEX idx_status (status),
    FOREIGN KEY (department_code) REFERENCES departments(department_code)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: violations
-- Stores student violations
-- ============================================
CREATE TABLE violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    violation_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('minor', 'major', 'critical') DEFAULT 'minor',
    date_occurred DATE NOT NULL,
    reported_by VARCHAR(100) DEFAULT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    resolution_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_violation_type (violation_type),
    INDEX idx_date_occurred (date_occurred),
    INDEX idx_status (status),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: prospectus_downloads
-- Tracks prospectus file downloads
-- ============================================
CREATE TABLE prospectus_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL COMMENT 'Foreign key to programs table',
    user_ip VARCHAR(45) NOT NULL COMMENT 'IP address of downloader',
    user_agent TEXT DEFAULT NULL COMMENT 'Browser information',
    download_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    INDEX idx_program_download (program_id, download_date),
    INDEX idx_ip_download (user_ip, download_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: student_document_requests
-- Stores document requests from existing students
-- ============================================
CREATE TABLE student_document_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(20) NOT NULL UNIQUE COMMENT 'Unique request ID (e.g., REQ-2024-001)',
    student_id VARCHAR(20) NOT NULL COMMENT 'Existing student ID',
    document_type ENUM('coe', 'tor', 'certificate', 'good_moral', 'transferee', 'others') NOT NULL,
    document_name VARCHAR(100) NOT NULL COMMENT 'Specific document name',
    purpose TEXT NOT NULL COMMENT 'Purpose of document request',
    urgency_level ENUM('normal', 'urgent', 'rush') DEFAULT 'normal',
    status ENUM('pending', 'processing', 'ready', 'claimed', 'cancelled') DEFAULT 'pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    target_date DATE COMMENT 'Expected completion date',
    completion_date TIMESTAMP NULL COMMENT 'Actual completion date',
    claimed_date TIMESTAMP NULL COMMENT 'When document was claimed',
    processing_fee DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Processing fee if applicable',
    payment_status ENUM('unpaid', 'paid', 'waived') DEFAULT 'unpaid',
    notes TEXT DEFAULT NULL COMMENT 'Additional notes or special instructions',
    processed_by VARCHAR(100) DEFAULT NULL COMMENT 'Staff who processed the request',
    attachment_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to generated document',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_request_id (request_id),
    INDEX idx_student_id (student_id),
    INDEX idx_document_type (document_type),
    INDEX idx_status (status),
    INDEX idx_request_date (request_date),
    FOREIGN KEY (student_id) REFERENCES students(student_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ADD FOREIGN KEY CONSTRAINTS
-- ============================================

-- Add foreign key for students
ALTER TABLE students 
ADD FOREIGN KEY (department) REFERENCES departments(department_code)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
ADD FOREIGN KEY (section_id) REFERENCES sections(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert default admin user
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin_demo@colegio.edu', 'admin@colegio.edu', MD5('demo123'), 'Admin User', 'admin', 'active');

-- Insert sample departments
INSERT INTO departments (department_code, department_name, description) VALUES
('CS', 'Computer Science', 'Department of Computer Science and Information Technology'),
('BPA', 'Business Administration', 'Department of Business and Public Administration'),
('TVET', 'Technical-Vocational Education', 'Department of Technical and Vocational Education'),
('GED', 'General Education', 'Department of General Education and Humanities');

-- Insert sample sections
INSERT INTO sections (section_code, section_name, department_code, year_level, capacity) VALUES
('CS-1A', 'Computer Science 1-A', 'CS', 1, 35),
('CS-2A', 'Computer Science 2-A', 'CS', 2, 35),
('CS-3A', 'Computer Science 3-A', 'CS', 3, 35),
('CS-4A', 'Computer Science 4-A', 'CS', 4, 35),
('BPA-1A', 'Business Admin 1-A', 'BPA', 1, 40),
('BPA-2A', 'Business Admin 2-A', 'BPA', 2, 40),
('BPA-3A', 'Business Admin 3-A', 'BPA', 3, 40),
('BPA-4A', 'Business Admin 4-A', 'BPA', 4, 40),
('TVET-1A', 'Technical-Voc 1-A', 'TVET', 1, 30),
('TVET-2A', 'Technical-Voc 2-A', 'TVET', 2, 30);

-- Insert sample program heads
INSERT INTO program_heads (
    employee_id,
    first_name,
    middle_name,
    last_name,
    email,
    phone,
    department,
    specialization,
    hire_date,
    status
) VALUES
(
    'PH001',
    'Juan',
    'Santos',
    'Garcia',
    'juan.garcia@colegiodenaujan.edu.ph',
    '09123456789',
    'Computer Science',
    'Software Engineering & Database Systems',
    '2020-01-15',
    'active'
),
(
    'PH002', 
    'Maria',
    'Reyes',
    'Lopez',
    'maria.lopez@colegiodenaujan.edu.ph',
    '09123456790',
    'Business Administration',
    'Accounting & Financial Management',
    '2019-06-20',
    'active'
),
(
    'PH003',
    'Jose',
    'Cruz',
    'Martinez',
    'jose.martinez@colegiodenaujan.edu.ph',
    '09123456791',
    'Technical-Vocational',
    'Welding Technology & Fabrication',
    '2021-03-10',
    'active'
);

-- Insert sample programs
INSERT INTO programs (
    code, 
    title, 
    short_title, 
    category, 
    department, 
    description, 
    duration, 
    units, 
    image_path, 
    prospectus_path, 
    enrolled_students, 
    status, 
    highlights, 
    career_opportunities, 
    admission_requirements,
    program_head_id
) VALUES
(
    'BSIS', 
    'Bachelor of Science in Information Systems', 
    'BS Information Systems', 
    'undergraduate', 
    'Computer Science', 
    'A comprehensive program focusing on information systems analysis, design, and implementation for modern business environments.', 
    '4 Years', 
    '150 Units', 
    '../../assets/img/programs/bsis.jpg', 
    '../../assets/prospectus/bsis-prospectus.pdf', 
    45, 
    'active', 
    '["Database Management", "Software Development", "System Analysis", "Network Administration"]',
    '["Software Developer", "Database Administrator", "Systems Analyst", "IT Consultant"]',
    '["High School Diploma", "Passing Entrance Exam", "Good Moral Character"]',
    1
),
(
    'BPA', 
    'Bachelor of Public Administration', 
    'BS Public Administration', 
    'undergraduate', 
    'Business Administration', 
    'Program designed to develop competent public administrators and managers for government service and public sector organizations.', 
    '4 Years', 
    '140 Units', 
    '../../assets/img/programs/bpa.jpg', 
    '../../assets/prospectus/bpa-prospectus.pdf', 
    38, 
    'active', 
    '["Public Policy", "Administrative Law", "Fiscal Management", "Public Ethics"]',
    '["Government Administrator", "Policy Analyst", "Public Relations Officer", "Community Development Officer", "Local Government Unit Staff"]',
    '["High school diploma or equivalent", "Passing entrance examination score", "Good moral character", "Medical certificate"]',
    2
),
(
    'WFT', 
    'Welding and Fabrication Technology', 
    'Welding Technology', 
    'technical', 
    'Technical-Vocational', 
    'Hands-on program providing comprehensive training in various welding techniques and fabrication processes for industrial applications.', 
    '2 Years', 
    '80 Units', 
    '../../assets/img/programs/wft.jpg', 
    '../../assets/prospectus/wft-prospectus.pdf', 
    25, 
    'active', 
    '["Arc Welding", "MIG Welding", "TIG Welding", "Fabrication"]',
    '["Welder", "Fabricator", "Welding Inspector", "Metal Worker"]',
    '["High School Diploma", "Physical Fitness", "Good Eyesight"]',
    3
);

-- ============================================
-- CREATE VIEWS FOR REPORTING
-- ============================================

-- View: Active Programs Summary
CREATE VIEW v_active_programs AS
SELECT 
    id,
    code,
    short_title,
    category,
    department,
    enrolled_students,
    CASE WHEN prospectus_path IS NOT NULL THEN 'Yes' ELSE 'No' END AS has_prospectus
FROM programs
WHERE status = 'active';

-- View: Programs with Program Heads
CREATE VIEW v_programs_with_heads AS
SELECT 
    p.id,
    p.code,
    p.short_title,
    p.category,
    p.department,
    p.enrolled_students,
    p.program_head_id,
    CONCAT(ph.first_name, ' ', ph.last_name) as program_head_name,
    ph.email as program_head_email,
    ph.phone as program_head_phone,
    p.status
FROM programs p
LEFT JOIN program_heads ph ON p.program_head_id = ph.id
WHERE p.status = 'active';
-- ============================================

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure: Get Program Details
DELIMITER //
CREATE PROCEDURE sp_get_program_details(IN program_id INT)
BEGIN
    SELECT * FROM programs WHERE id = program_id;
END //
DELIMITER ;

-- Procedure: Update Program Status
DELIMITER //
CREATE PROCEDURE sp_update_program_status(
    IN program_id INT,
    IN new_status VARCHAR(20)
)
BEGIN
    UPDATE programs 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = program_id;
END //
DELIMITER ;

-- Procedure: Track Prospectus Download
DELIMITER //
CREATE PROCEDURE sp_track_prospectus_download(
    IN p_program_id INT,
    IN p_user_ip VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO prospectus_downloads (program_id, user_ip, user_agent)
    VALUES (p_program_id, p_user_ip, p_user_agent);
END //
DELIMITER ;

-- Procedure: Get Prospectus Download Count
DELIMITER //
CREATE PROCEDURE sp_get_prospectus_download_count(IN program_id INT)
BEGIN
    SELECT COALESCE(COUNT(*), 0) as download_count
    FROM prospectus_downloads
    WHERE prospectus_downloads.program_id = program_id;
END //
DELIMITER ;

-- View: Program Downloads Summary
CREATE VIEW v_program_download_stats AS
SELECT 
    p.id,
    p.code,
    p.short_title,
    p.category,
    p.status,
    COALESCE(download_counts.download_count, 0) as download_count,
    p.enrolled_students
FROM programs p
LEFT JOIN (
    SELECT 
        program_id,
        COUNT(*) as download_count
    FROM prospectus_downloads
    GROUP BY program_id
) download_counts ON p.id = download_counts.program_id
ORDER BY p.category, p.short_title;

-- ============================================
-- PROGRAM HEADS STORED PROCEDURES
-- ============================================

-- Procedure: Get Program Head Details
DELIMITER //
CREATE PROCEDURE sp_get_program_head_details(IN head_id INT)
BEGIN
    SELECT * FROM program_heads WHERE id = head_id;
END //
DELIMITER ;

-- Procedure: Create Program Head
DELIMITER //
CREATE PROCEDURE sp_create_program_head(
    IN p_employee_id VARCHAR(50),
    IN p_first_name VARCHAR(100),
    IN p_middle_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_phone VARCHAR(20),
    IN p_department VARCHAR(100),
    IN p_specialization TEXT,
    IN p_hire_date DATE,
    IN p_status VARCHAR(20)
)
BEGIN
    INSERT INTO program_heads (
        employee_id,
        first_name,
        middle_name,
        last_name,
        email,
        phone,
        department,
        specialization,
        hire_date,
        status
    ) VALUES (
        p_employee_id,
        p_first_name,
        p_middle_name,
        p_last_name,
        p_email,
        p_phone,
        p_department,
        p_specialization,
        p_hire_date,
        p_status
    );
    
    SELECT LAST_INSERT_ID() as new_id;
END //
DELIMITER ;

-- Procedure: Update Program Head
DELIMITER //
CREATE PROCEDURE sp_update_program_head(
    IN p_id INT,
    IN p_employee_id VARCHAR(50),
    IN p_first_name VARCHAR(100),
    IN p_middle_name VARCHAR(100),
    IN p_last_name VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_phone VARCHAR(20),
    IN p_department VARCHAR(100),
    IN p_specialization TEXT,
    IN p_hire_date DATE,
    IN p_status VARCHAR(20)
)
BEGIN
    UPDATE program_heads SET
        employee_id = p_employee_id,
        first_name = p_first_name,
        middle_name = p_middle_name,
        last_name = p_last_name,
        email = p_email,
        phone = p_phone,
        department = p_department,
        specialization = p_specialization,
        hire_date = p_hire_date,
        status = p_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_id;
END //
DELIMITER ;

-- Procedure: Delete Program Head
DELIMITER //
CREATE PROCEDURE sp_delete_program_head(IN head_id INT)
BEGIN
    DELETE FROM program_heads WHERE id = head_id;
END //
DELIMITER ;

-- View: Program Heads Summary
CREATE VIEW v_program_heads_summary AS
SELECT 
    id,
    employee_id,
    CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) as full_name,
    email,
    phone,
    department,
    specialization,
    hire_date,
    status
FROM program_heads
ORDER BY last_name, first_name;

-- ============================================
-- GRANT PERMISSIONS (Optional - for security)
-- ============================================
-- Uncomment and modify if you want to create a specific database user
-- CREATE USER 'cnesis_user'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT ALL PRIVILEGES ON cnesis_db.* TO 'cnesis_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- VERIFICATION QUERIES
-- Run these to verify setup was successful
-- ============================================

-- Check if tables were created
/* SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'cnesis_db'
ORDER BY TABLE_NAME;

-- Check programs data
SELECT 
    code,
    short_title,
    category,
    enrolled_students,
    status
FROM programs;

-- Check admin user
SELECT 
    username,
    email,
    full_name,
    role,
    status
FROM users;
 */
-- ============================================
-- SETUP COMPLETE!
-- ============================================
-- Database: cnesis_db
-- Tables Created: programs, admissions, users, inquiries
-- Initial Data: 4 programs, 1 admin user
-- Views: v_active_programs, v_admission_stats
-- Stored Procedures: sp_get_program_details, sp_update_program_status
-- 
-- Next Steps:
-- 1. Update database connection in PHP files
-- 2. Test API endpoints
-- 3. Login to admin dashboard
-- ============================================

-- TABLE: email_configs
-- Stores email configuration settings for SMTP
-- ============================================
CREATE TABLE email_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_name VARCHAR(100) NOT NULL DEFAULT 'default' COMMENT 'Configuration name',
    smtp_host VARCHAR(255) NOT NULL COMMENT 'SMTP server hostname',
    smtp_port INT NOT NULL DEFAULT 587 COMMENT 'SMTP port',
    smtp_username VARCHAR(255) NOT NULL COMMENT 'SMTP username',
    smtp_password VARCHAR(255) NOT NULL COMMENT 'SMTP password (encrypted)',
    encryption_type ENUM('none', 'tls', 'ssl') DEFAULT 'tls' COMMENT 'Encryption type',
    from_email VARCHAR(255) NOT NULL COMMENT 'From email address',
    from_name VARCHAR(255) NOT NULL COMMENT 'From display name',
    reply_to_email VARCHAR(255) DEFAULT NULL COMMENT 'Reply-to email address',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether this config is active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_name (config_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email configuration
INSERT INTO email_configs (
    config_name,
    smtp_host,
    smtp_port,
    smtp_username,
    smtp_password,
    encryption_type,
    from_email,
    from_name,
    reply_to_email,
    is_active
) VALUES (
    'default',
    'smtp.gmail.com',
    587,
    'belugaw6@gmail.com',
    'klotmfurniohmmjo',
    'tls',
    'belugaw6@gmail.com',
    'Colegio De Naujan',
    'belugaw6@gmail.com',
    TRUE
);

-- TABLE: email_logs
-- Logs all email sending attempts for tracking and debugging
-- ============================================
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL COMMENT 'Email address of recipient',
    subject VARCHAR(255) NOT NULL COMMENT 'Email subject',
    sent_successfully BOOLEAN NOT NULL COMMENT 'Whether email was sent successfully',
    error_message TEXT DEFAULT NULL COMMENT 'Error message if sending failed',
    email_type VARCHAR(50) DEFAULT NULL COMMENT 'Type of email (admission, acceptance, etc.)',
    admission_id INT DEFAULT NULL COMMENT 'Related admission ID if applicable',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When email was sent',
    INDEX idx_recipient_email (recipient_email),
    INDEX idx_sent_successfully (sent_successfully),
    INDEX idx_sent_at (sent_at),
    INDEX idx_admission_id (admission_id),
    FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: email_templates
-- Stores email templates for different types of communications
-- ============================================
CREATE TABLE email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Template identifier',
    template_type ENUM('admission', 'acceptance', 'rejection', 'reminder', 'payment', 'general') NOT NULL,
    subject VARCHAR(255) NOT NULL COMMENT 'Email subject template',
    html_body TEXT NOT NULL COMMENT 'HTML email body template',
    text_body TEXT DEFAULT NULL COMMENT 'Plain text email body template',
    variables TEXT DEFAULT NULL COMMENT 'JSON string of available variables',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether template is active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_template_name (template_name),
    INDEX idx_template_type (template_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO email_templates (template_name, template_type, subject, html_body, text_body, variables) VALUES
('application_received', 'admission', 
 'Application Received - Colegio De Naujan',
 '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Received</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1a365d; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { background: #2c5282; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; padding: 10px 20px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Colegio De Naujan</h1>
            <p>Application Confirmation</p>
        </div>
        <div class="content">
            <h2>Dear {{first_name}} {{last_name}},</h2>
            <p>Thank you for your interest in <strong>{{program_title}}</strong> at Colegio De Naujan.</p>
            <p>We have received your application and it is currently under review. Your application ID is: <strong>{{application_id}}</strong></p>
            
            <h3>Next Steps:</h3>
            <ul>
                <li>Our admissions committee will review your application</li>
                <li>You will receive an email notification within 3-5 business days</li>
                <li>Please prepare the following documents for submission:</li>
            </ul>
            
            <h3>Required Documents:</h3>
            <ul>
                <li>Birth Certificate (NSO)</li>
                <li>Form 138 (Report Card)</li>
                <li>Certificate of Good Moral Character</li>
                <li>2x2 ID Pictures (2 copies)</li>
                <li>Parent/Guardian Valid ID</li>
            </ul>
            
            <p>For any inquiries, please contact our admissions office at:</p>
            <p>
                📧 Email: admissions@colegiodenaujan.edu.ph<br>
                📱 Phone: (043) 123-4567<br>
                📍 Address: Colegio De Naujan, Naujan, Oriental Mindoro
            </p>
        </div>
        <div class="footer">
            <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>',
 'Dear {{first_name}} {{last_name}},

Thank you for your interest in {{program_title}} at Colegio De Naujan.

We have received your application and it is currently under review. Your application ID is: {{application_id}}

Colegio De Naujan',
 '{"first_name": "Student first name", "last_name": "Student last name", "program_title": "Applied program", "application_id": "Application ID"}');

-- TABLE: admission_status_log
-- Logs all admission status changes for audit trail
-- ============================================
CREATE TABLE admission_status_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_id INT NOT NULL COMMENT 'Related admission ID',
    old_status VARCHAR(20) DEFAULT NULL COMMENT 'Previous status',
    new_status VARCHAR(20) NOT NULL COMMENT 'New status',
    action_by INT NOT NULL COMMENT 'Admin user ID who made the change',
    notes TEXT DEFAULT NULL COMMENT 'Notes about the status change',
    email_sent BOOLEAN DEFAULT FALSE COMMENT 'Whether email was sent',
    email_error TEXT DEFAULT NULL COMMENT 'Email error if sending failed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When the change was made',
    INDEX idx_admission_id (admission_id),
    INDEX idx_action_by (action_by),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE: email_documents
-- Stores uploaded documents for email attachments
-- ============================================
CREATE TABLE email_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_name VARCHAR(255) NOT NULL COMMENT 'Original file name',
    file_path VARCHAR(500) NOT NULL COMMENT 'Server file path',
    file_size INT NOT NULL COMMENT 'File size in bytes',
    file_type VARCHAR(100) NOT NULL COMMENT 'MIME type',
    category ENUM('application-form', 'requirements', 'policies', 'general', 'templates', 'other') DEFAULT 'general' COMMENT 'Document category',
    description TEXT DEFAULT NULL COMMENT 'Document description',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Whether document is available',
    uploaded_by INT NOT NULL COMMENT 'Admin user ID who uploaded',
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When file was uploaded',
    download_count INT DEFAULT 0 COMMENT 'How many times downloaded',
    last_used TIMESTAMP NULL COMMENT 'Last used in email',
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_upload_date (upload_date),
    INDEX idx_uploaded_by (uploaded_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ARCHIVE TABLES FOR RECYCLE BIN FUNCTIONALITY
-- ============================================

-- Archive Admissions Table
CREATE TABLE archive_admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    application_id VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    birthdate DATE,
    gender VARCHAR(10),
    address TEXT,
    admission_type ENUM('freshman', 'transferee', 'returnee', 'shifter'),
    program_id INT,
    status ENUM('pending', 'approved', 'rejected', 'processing', 'enrolled'),
    student_id VARCHAR(50),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    notes TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_application_id (application_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_status (status),
    INDEX idx_admission_type (admission_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Archive Programs Table
CREATE TABLE archive_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    program_code VARCHAR(20) NOT NULL,
    program_title VARCHAR(200) NOT NULL,
    description TEXT,
    department VARCHAR(100),
    duration_years INT,
    tuition_fee DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_program_code (program_code),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Archive Program Heads Table
CREATE TABLE archive_program_heads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    employee_id VARCHAR(50) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    specialization VARCHAR(150),
    hire_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Archive Students Table
CREATE TABLE archive_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    address TEXT DEFAULT NULL,
    department VARCHAR(100) DEFAULT NULL,
    section_id INT DEFAULT NULL,
    yearlevel INT DEFAULT NULL,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'inactive',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_student_id (student_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Archive Settings Table
CREATE TABLE archive_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_setting_key (setting_key),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================

-- Add foreign key for program_heads in programs table
ALTER TABLE programs ADD CONSTRAINT fk_program_head 
FOREIGN KEY (program_head_id) REFERENCES program_heads(id) ON DELETE SET NULL;

-- Add foreign key for program_id in admissions table
ALTER TABLE admissions ADD CONSTRAINT fk_admission_program 
FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE RESTRICT;

-- Add foreign key for department_id in sections table
ALTER TABLE sections ADD CONSTRAINT fk_section_department 
FOREIGN KEY (department_id) REFERENCES departments(id);

-- Add foreign key for uploaded_by in email_documents table
ALTER TABLE email_documents ADD CONSTRAINT fk_email_document_user 
FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL;

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@cnesis.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert default departments
INSERT INTO departments (department_code, department_name, description) VALUES 
('CS', 'Computer Studies', 'Department of Computer Studies'),
('BA', 'Business Administration', 'Department of Business Administration'),
('EDUC', 'Education', 'Department of Education');

-- Insert default email configuration
INSERT INTO email_configs (smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, from_email, from_name) VALUES 
('smtp.gmail.com', 587, 'belugaw6@gmail.com', 'klotmfurniohmmjo', 'tls', 'belugaw6@gmail.com', 'Colegio De Naujan');

-- Insert default email templates
INSERT INTO email_templates (template_name, template_type, subject, html_content) VALUES 
('application_received', 'admission', 'Application Received', '<h2>Application Received</h2><p>Dear {{name}},</p><p>Thank you for your application to Colegio De Naujan.</p>'),
('application_approved', 'admission', 'Application Approved', '<h2>Congratulations!</h2><p>Dear {{name}},</p><p>Your application has been approved.</p>'),
('application_rejected', 'admission', 'Application Status Update', '<h2>Application Status Update</h2><p>Dear {{name}},</p><p>Your application status has been updated.</p>');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES 
('school_name', 'Colegio De Naujan', 'string', 'School name', true),
('school_address', 'Naujan, Oriental Mindoro', 'string', 'School address', true),
('school_phone', '(043) 123-4567', 'string', 'School phone number', true),
('school_email', 'info@cnesis.edu.ph', 'string', 'School email', true),
('current_academic_year', '2025-2026', 'string', 'Current academic year', true),
('enrollment_status', 'open', 'string', 'Current enrollment status', true);

-- ============================================
-- CREATE VIEWS
-- ============================================

-- View for active programs with heads
CREATE VIEW active_programs AS
SELECT 
    p.id, p.code, p.title, p.short_title, p.category, p.department,
    p.description, p.duration, p.units, p.enrolled_students, p.status,
    ph.first_name as head_first_name, ph.last_name as head_last_name, ph.email as head_email,
    p.created_at, p.updated_at
FROM programs p
LEFT JOIN program_heads ph ON p.program_head_id = ph.id
WHERE p.status = 'active';

-- View for active students with program info
CREATE VIEW active_students AS
SELECT 
    s.id, s.student_id, s.first_name, s.middle_name, s.last_name, s.email, s.phone,
    s.birth_date, s.gender, s.address, s.department, s.yearlevel, s.status,
    s.created_at, s.updated_at
FROM students s
WHERE s.status = 'active';

-- ============================================
-- CREATE UNIFIED ARCHIVE VIEW
-- ============================================

CREATE VIEW all_archived_items AS
SELECT 
    'admission' as item_type,
    'archive_admissions' as source_table,
    id, original_id, application_id as identifier, first_name, last_name, email,
    status, deleted_at, deleted_by, delete_reason, notes,
    admission_type as sub_type
FROM archive_admissions

UNION ALL

SELECT 
    'program' as item_type,
    'archive_programs' as source_table,
    id, original_id, program_code as identifier, program_title as name, '' as email,
    status, deleted_at, deleted_by, delete_reason, description as notes,
    department as sub_type
FROM archive_programs

UNION ALL

SELECT 
    'student' as item_type,
    'archive_students' as source_table,
    id, original_id, student_id as identifier, first_name, last_name, email,
    status, deleted_at, deleted_by, delete_reason, address as notes,
    department as sub_type
FROM archive_students

UNION ALL

SELECT 
    'program_head' as item_type,
    'archive_program_heads' as source_table,
    id, original_id, employee_id as identifier, first_name, last_name, email,
    status, deleted_at, deleted_by, delete_reason, specialization as notes,
    department as sub_type
FROM archive_program_heads

UNION ALL

SELECT 
    'setting' as item_type,
    'archive_settings' as source_table,
    id, original_id, setting_key as identifier, setting_value as name, '' as email,
    'archived' as status, deleted_at, deleted_by, delete_reason, description as notes,
    setting_type as sub_type
FROM archive_settings;

-- ============================================
-- COMPLETE!
-- ============================================
