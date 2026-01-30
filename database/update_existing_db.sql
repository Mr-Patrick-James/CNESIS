-- ============================================
-- INCREMENTAL DATABASE UPDATE
-- Safe update script for existing CNESIS database
-- This adds missing tables and fixes issues without affecting existing data
-- ============================================

-- Check and add missing tables only if they don't exist

-- ============================================
-- TABLE: settings (if not exists)
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE COMMENT 'Setting identifier',
    setting_value TEXT DEFAULT NULL COMMENT 'Setting value',
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string' COMMENT 'Data type of setting',
    description TEXT DEFAULT NULL COMMENT 'Setting description',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Whether setting is accessible via API',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DEFAULT SETTINGS (only if not exists)
-- ============================================
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES 
('school_name', 'Colegio De Naujan', 'string', 'School name', true),
('school_address', 'Naujan, Oriental Mindoro', 'string', 'School address', true),
('school_phone', '(043) 123-4567', 'string', 'School phone number', true),
('school_email', 'info@cnesis.edu.ph', 'string', 'School email', true),
('current_academic_year', '2025-2026', 'string', 'Current academic year', true),
('enrollment_status', 'open', 'string', 'Current enrollment status', true);

-- ============================================
-- CHECK AND FIX EXISTING TABLES
-- ============================================

-- Check if admissions table has all required columns
ALTER TABLE admissions 
ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) DEFAULT NULL COMMENT 'Middle name',
ADD COLUMN IF NOT EXISTS notes TEXT DEFAULT NULL COMMENT 'Additional notes';

-- Check if students table has correct column names
ALTER TABLE students 
CHANGE COLUMN IF EXISTS birthdate birth_date DATE DEFAULT NULL COMMENT 'Date of birth',
CHANGE COLUMN IF EXISTS yearlevel yearlevel INT DEFAULT NULL COMMENT 'Year level',
ADD COLUMN IF NOT EXISTS middle_name VARCHAR(100) DEFAULT NULL COMMENT 'Middle name';

-- Check if users table exists (create if not)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    full_name VARCHAR(255) NOT NULL COMMENT 'Full name',
    role ENUM('admin', 'staff', 'teacher') NOT NULL DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL COMMENT 'Last login timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user if not exists
INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@cnesis.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- ============================================
-- CREATE ARCHIVE TABLES (if not exists)
-- ============================================

-- Archive Admissions Table
CREATE TABLE IF NOT EXISTS archive_admissions (
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
CREATE TABLE IF NOT EXISTS archive_programs (
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
CREATE TABLE IF NOT EXISTS archive_program_heads (
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

-- Archive Students Table (FIXED VERSION)
CREATE TABLE IF NOT EXISTS archive_students (
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
CREATE TABLE IF NOT EXISTS archive_settings (
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
-- CREATE UNIFIED ARCHIVE VIEW
-- ============================================
CREATE OR REPLACE VIEW all_archived_items AS
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
