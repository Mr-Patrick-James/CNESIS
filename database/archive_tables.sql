-- Archive Tables for Recycle Bin Functionality
-- These tables will store deleted records for recovery purposes

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
    deleted_by VARCHAR(100), -- Admin who deleted it
    delete_reason TEXT,
    notes TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_application_id (application_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_status (status),
    INDEX idx_admission_type (admission_type)
);

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
);

-- Archive Program Heads Table
CREATE TABLE IF NOT EXISTS archive_program_heads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    program_id INT,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    hire_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_email (email),
    INDEX idx_deleted_at (deleted_at)
);

-- Archive Students Table
CREATE TABLE IF NOT EXISTS archive_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_id INT NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    birth_date DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    department VARCHAR(100),
    section_id INT,
    yearlevel INT,
    status ENUM('active', 'inactive', 'graduated') DEFAULT 'inactive',
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_by VARCHAR(100),
    delete_reason TEXT,
    
    INDEX idx_original_id (original_id),
    INDEX idx_student_id (student_id),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_status (status)
);

-- Archive Settings Table (to track archive configuration)
CREATE TABLE IF NOT EXISTS archive_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(100)
);

-- Insert default archive settings
INSERT IGNORE INTO archive_settings (setting_key, setting_value, description) VALUES
('auto_cleanup_days', '365', 'Automatically delete archive records older than this many days'),
('enable_auto_cleanup', 'false', 'Enable automatic cleanup of old archive records'),
('default_delete_reason', 'Manual deletion by administrator', 'Default reason when no specific reason provided'),
('archive_notifications', 'true', 'Send notifications when items are archived');

-- Create a view for easy access to all archived items
CREATE OR REPLACE VIEW all_archived_items AS
SELECT 
    'admission' as item_type,
    id,
    original_id,
    application_id as reference_id,
    CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as item_name,
    email,
    status,
    deleted_at,
    deleted_by,
    delete_reason,
    'admissions' as source_table
FROM archive_admissions

UNION ALL

SELECT 
    'program' as item_type,
    id,
    original_id,
    program_code as reference_id,
    program_title as item_name,
    NULL as email,
    status,
    deleted_at,
    deleted_by,
    delete_reason,
    'programs' as source_table
FROM archive_programs

UNION ALL

SELECT 
    'program_head' as item_type,
    id,
    original_id,
    CONCAT(first_name, ' ', last_name) as reference_id,
    CONCAT(first_name, ' ', last_name) as item_name,
    email,
    status,
    deleted_at,
    deleted_by,
    delete_reason,
    'program_heads' as source_table
FROM archive_program_heads

UNION ALL

SELECT 
    'student' as item_type,
    id,
    original_id,
    student_id as reference_id,
    CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as item_name,
    email,
    status,
    deleted_at,
    deleted_by,
    delete_reason,
    'students' as source_table
FROM archive_students

ORDER BY deleted_at DESC;
