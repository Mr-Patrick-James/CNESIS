-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 30, 2026 at 12:53 AM
-- Server version: 8.3.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cnesis_db`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_create_program_head`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_program_head` (IN `p_employee_id` VARCHAR(50), IN `p_first_name` VARCHAR(100), IN `p_middle_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_email` VARCHAR(255), IN `p_phone` VARCHAR(20), IN `p_department` VARCHAR(100), IN `p_specialization` TEXT, IN `p_hire_date` DATE, IN `p_status` VARCHAR(20))   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `sp_delete_program_head`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_program_head` (IN `head_id` INT)   BEGIN
    DELETE FROM program_heads WHERE id = head_id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_program_details`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_program_details` (IN `program_id` INT)   BEGIN
    SELECT * FROM programs WHERE id = program_id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_program_head_details`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_program_head_details` (IN `head_id` INT)   BEGIN
    SELECT * FROM program_heads WHERE id = head_id;
END$$

DROP PROCEDURE IF EXISTS `sp_get_prospectus_download_count`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_prospectus_download_count` (IN `program_id` INT)   BEGIN
    SELECT COALESCE(COUNT(*), 0) as download_count
    FROM prospectus_downloads
    WHERE prospectus_downloads.program_id = program_id;
END$$

DROP PROCEDURE IF EXISTS `sp_track_prospectus_download`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_track_prospectus_download` (IN `p_program_id` INT, IN `p_user_ip` VARCHAR(45), IN `p_user_agent` TEXT)   BEGIN
    INSERT INTO prospectus_downloads (program_id, user_ip, user_agent)
    VALUES (p_program_id, p_user_ip, p_user_agent);
END$$

DROP PROCEDURE IF EXISTS `sp_update_program_head`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_program_head` (IN `p_id` INT, IN `p_employee_id` VARCHAR(50), IN `p_first_name` VARCHAR(100), IN `p_middle_name` VARCHAR(100), IN `p_last_name` VARCHAR(100), IN `p_email` VARCHAR(255), IN `p_phone` VARCHAR(20), IN `p_department` VARCHAR(100), IN `p_specialization` TEXT, IN `p_hire_date` DATE, IN `p_status` VARCHAR(20))   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `sp_update_program_status`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_program_status` (IN `program_id` INT, IN `new_status` VARCHAR(20))   BEGIN
    UPDATE programs 
    SET status = new_status, updated_at = CURRENT_TIMESTAMP
    WHERE id = program_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

DROP TABLE IF EXISTS `admissions`;
CREATE TABLE IF NOT EXISTS `admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `application_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique application ID',
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Existing student ID (NULL for new freshmen)',
  `program_id` int NOT NULL COMMENT 'Foreign key to programs table',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `high_school` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'High school (for freshmen)',
  `last_school` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Last school attended (for old students)',
  `year_graduated` int DEFAULT NULL COMMENT 'Graduation year',
  `gwa` decimal(3,2) DEFAULT NULL COMMENT 'General Weighted Average',
  `entrance_exam_score` int DEFAULT NULL COMMENT 'Entrance exam score',
  `admission_type` enum('freshman','transferee','returnee','shifter') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'freshman',
  `previous_program` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Previous program (for transferees/shifters)',
  `status` enum('pending','approved','rejected','enrolled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int DEFAULT NULL COMMENT 'Admin user ID who reviewed',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Admin notes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_id` (`application_id`),
  KEY `idx_status` (`status`),
  KEY `idx_program` (`program_id`),
  KEY `idx_submitted` (`submitted_at`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_admission_type` (`admission_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admission_status_log`
--

DROP TABLE IF EXISTS `admission_status_log`;
CREATE TABLE IF NOT EXISTS `admission_status_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admission_id` int NOT NULL COMMENT 'Related admission ID',
  `old_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Previous status',
  `new_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'New status',
  `action_by` int NOT NULL COMMENT 'Admin user ID who made the change',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Notes about the status change',
  `email_sent` tinyint(1) DEFAULT '0' COMMENT 'Whether email was sent',
  `email_error` text COLLATE utf8mb4_unicode_ci COMMENT 'Email error if sending failed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the change was made',
  PRIMARY KEY (`id`),
  KEY `idx_admission_id` (`admission_id`),
  KEY `idx_action_by` (`action_by`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `all_archived_items`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `all_archived_items`;
CREATE TABLE IF NOT EXISTS `all_archived_items` (
`delete_reason` mediumtext
,`deleted_at` timestamp
,`deleted_by` varchar(100)
,`email` varchar(150)
,`id` int
,`item_name` varchar(302)
,`item_type` varchar(12)
,`original_id` int
,`reference_id` varchar(201)
,`source_table` varchar(13)
,`status` varchar(10)
);

-- --------------------------------------------------------

--
-- Table structure for table `archive_admissions`
--

DROP TABLE IF EXISTS `archive_admissions`;
CREATE TABLE IF NOT EXISTS `archive_admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `application_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `admission_type` enum('freshman','transferee','returnee','shifter') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `status` enum('pending','approved','rejected','processing','enrolled') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_original_id` (`original_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  KEY `idx_status` (`status`),
  KEY `idx_admission_type` (`admission_type`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `archive_admissions`
--

INSERT INTO `archive_admissions` (`id`, `original_id`, `application_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `birthdate`, `gender`, `address`, `admission_type`, `program_id`, `status`, `student_id`, `submitted_at`, `updated_at`, `deleted_at`, `deleted_by`, `delete_reason`, `notes`) VALUES
(2, 4, 'APP-2026-1415', 'Romasanta Patrick James Vital', NULL, '', 'patrickmontero833@gmail.com', '', '0000-00-00', 'male', '', 'freshman', 1, 'pending', NULL, '2026-01-29 23:52:18', NULL, '2026-01-30 00:31:48', 'Administrator', 'Manual deletion by administrator', 'penge 2500');

-- --------------------------------------------------------

--
-- Table structure for table `archive_programs`
--

DROP TABLE IF EXISTS `archive_programs`;
CREATE TABLE IF NOT EXISTS `archive_programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `program_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_years` int DEFAULT NULL,
  `tuition_fee` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_original_id` (`original_id`),
  KEY `idx_program_code` (`program_code`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_program_heads`
--

DROP TABLE IF EXISTS `archive_program_heads`;
CREATE TABLE IF NOT EXISTS `archive_program_heads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `hire_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_original_id` (`original_id`),
  KEY `idx_email` (`email`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_settings`
--

DROP TABLE IF EXISTS `archive_settings`;
CREATE TABLE IF NOT EXISTS `archive_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `archive_settings`
--

INSERT INTO `archive_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`, `updated_by`) VALUES
(1, 'auto_cleanup_days', '365', 'Automatically delete archive records older than this many days', '2026-01-29 23:42:18', NULL),
(2, 'enable_auto_cleanup', 'false', 'Enable automatic cleanup of old archive records', '2026-01-29 23:42:18', NULL),
(3, 'default_delete_reason', 'Manual deletion by administrator', 'Default reason when no specific reason provided', '2026-01-29 23:42:18', NULL),
(4, 'archive_notifications', 'true', 'Send notifications when items are archived', '2026-01-29 23:42:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `archive_students`
--

DROP TABLE IF EXISTS `archive_students`;
CREATE TABLE IF NOT EXISTS `archive_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `yearlevel` int DEFAULT NULL,
  `status` enum('active','inactive','graduated') COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `archive_students`
--

INSERT INTO `archive_students` (`id`, `original_id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `birth_date`, `gender`, `address`, `department`, `section_id`, `yearlevel`, `status`, `avatar`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`, `delete_reason`) VALUES
(3, 3, '2023-0208', 'patrick', 'test', 'Romasanta', 'test2@gmail.com', '099877656741', '2026-01-13', 'male', 'hsxuiassgad', 'CS', NULL, 3, 'active', NULL, '2026-01-30 00:16:29', '2026-01-30 00:16:29', '2026-01-30 00:31:24', 'Administrator', 'Manual deletion by administrator');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `department_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `head_of_department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_code` (`department_code`),
  KEY `idx_department_code` (`department_code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_code`, `department_name`, `description`, `head_of_department`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CS', 'Computer Science', 'Department of Computer Science and Information Technology', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(2, 'BPA', 'Business Administration', 'Department of Business and Public Administration', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(3, 'TVET', 'Technical-Vocational Education', 'Department of Technical and Vocational Education', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(4, 'GED', 'General Education', 'Department of General Education and Humanities', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `email_configs`
--

DROP TABLE IF EXISTS `email_configs`;
CREATE TABLE IF NOT EXISTS `email_configs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default' COMMENT 'Configuration name',
  `smtp_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP server hostname',
  `smtp_port` int NOT NULL DEFAULT '587' COMMENT 'SMTP port',
  `smtp_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP username',
  `smtp_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP password (encrypted)',
  `encryption_type` enum('none','tls','ssl') COLLATE utf8mb4_unicode_ci DEFAULT 'tls' COMMENT 'Encryption type',
  `from_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'From email address',
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'From display name',
  `reply_to_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reply-to email address',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Whether this config is active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_config_name` (`config_name`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_configs`
--

INSERT INTO `email_configs` (`id`, `config_name`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `encryption_type`, `from_email`, `from_name`, `reply_to_email`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'default', 'smtp.gmail.com', 587, 'belugaw6@gmail.com', 'klotmfurniohmmjo', 'tls', 'belugaw6@gmail.com', 'Colegio De Naujan', 'belugaw6@gmail.com', 1, '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `email_documents`
--

DROP TABLE IF EXISTS `email_documents`;
CREATE TABLE IF NOT EXISTS `email_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Original file name',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Server file path',
  `file_size` int NOT NULL COMMENT 'File size in bytes',
  `file_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'MIME type',
  `category` enum('application-form','requirements','policies','general','templates','other') COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'Document category',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Document description',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Whether document is available',
  `uploaded_by` int NOT NULL COMMENT 'Admin user ID who uploaded',
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When file was uploaded',
  `download_count` int DEFAULT '0' COMMENT 'How many times downloaded',
  `last_used` timestamp NULL DEFAULT NULL COMMENT 'Last used in email',
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_upload_date` (`upload_date`),
  KEY `idx_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recipient_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email address of recipient',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email subject',
  `sent_successfully` tinyint(1) NOT NULL COMMENT 'Whether email was sent successfully',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Error message if sending failed',
  `email_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of email (admission, acceptance, etc.)',
  `admission_id` int DEFAULT NULL COMMENT 'Related admission ID if applicable',
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When email was sent',
  PRIMARY KEY (`id`),
  KEY `idx_recipient_email` (`recipient_email`),
  KEY `idx_sent_successfully` (`sent_successfully`),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_admission_id` (`admission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Template identifier',
  `template_type` enum('admission','acceptance','rejection','reminder','payment','general') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email subject template',
  `html_body` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTML email body template',
  `text_body` text COLLATE utf8mb4_unicode_ci COMMENT 'Plain text email body template',
  `variables` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON string of available variables',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Whether template is active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`),
  KEY `idx_template_name` (`template_name`),
  KEY `idx_template_type` (`template_type`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `template_type`, `subject`, `html_body`, `text_body`, `variables`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'application_received', 'admission', 'Application Received - Colegio De Naujan', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <title>Application Received</title>\r\n    <style>\r\n        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }\r\n        .container { max-width: 600px; margin: 0 auto; padding: 20px; }\r\n        .header { background: #1a365d; color: white; padding: 20px; text-align: center; }\r\n        .content { padding: 20px; background: #f9f9f9; }\r\n        .footer { background: #2c5282; color: white; padding: 15px; text-align: center; font-size: 12px; }\r\n        .btn { display: inline-block; padding: 10px 20px; background: #d4af37; color: white; text-decoration: none; border-radius: 5px; }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"container\">\r\n        <div class=\"header\">\r\n            <h1>Colegio De Naujan</h1>\r\n            <p>Application Confirmation</p>\r\n        </div>\r\n        <div class=\"content\">\r\n            <h2>Dear {{first_name}} {{last_name}},</h2>\r\n            <p>Thank you for your interest in <strong>{{program_title}}</strong> at Colegio De Naujan.</p>\r\n            <p>We have received your application and it is currently under review. Your application ID is: <strong>{{application_id}}</strong></p>\r\n            \r\n            <h3>Next Steps:</h3>\r\n            <ul>\r\n                <li>Our admissions committee will review your application</li>\r\n                <li>You will receive an email notification within 3-5 business days</li>\r\n                <li>Please prepare the following documents for submission:</li>\r\n            </ul>\r\n            \r\n            <h3>Required Documents:</h3>\r\n            <ul>\r\n                <li>Birth Certificate (NSO)</li>\r\n                <li>Form 138 (Report Card)</li>\r\n                <li>Certificate of Good Moral Character</li>\r\n                <li>2x2 ID Pictures (2 copies)</li>\r\n                <li>Parent/Guardian Valid ID</li>\r\n            </ul>\r\n            \r\n            <p>For any inquiries, please contact our admissions office at:</p>\r\n            <p>\r\n                📧 Email: admissions@colegiodenaujan.edu.ph<br>\r\n                📱 Phone: (043) 123-4567<br>\r\n                📍 Address: Colegio De Naujan, Naujan, Oriental Mindoro\r\n            </p>\r\n        </div>\r\n        <div class=\"footer\">\r\n            <p>&copy; 2026 Colegio De Naujan. All rights reserved.</p>\r\n            <p>This is an automated message. Please do not reply to this email.</p>\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', 'Dear {{first_name}} {{last_name}},\r\n\r\nThank you for your interest in {{program_title}} at Colegio De Naujan.\r\n\r\nWe have received your application and it is currently under review. Your application ID is: {{application_id}}\r\n\r\nColegio De Naujan', '{\"first_name\": \"Student first name\", \"last_name\": \"Student last name\", \"program_title\": \"Applied program\", \"application_id\": \"Application ID\"}', 1, '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inquiry_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `inquiry_type` enum('general','admission','program','requirements','other') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `status` enum('new','responded','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  `responded_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_id` (`inquiry_id`),
  KEY `program_id` (`program_id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_inquiry_type` (`inquiry_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `inquiry_id`, `full_name`, `email`, `phone`, `program_id`, `program_name`, `question`, `inquiry_type`, `status`, `notes`, `created_at`, `responded_at`, `responded_by`) VALUES
(1, 'INQ-2026-3123', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:47:48', NULL, NULL),
(2, 'INQ-2026-6709', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:50:06', NULL, NULL),
(3, 'INQ-2026-8362', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:50:07', NULL, NULL),
(4, 'INQ-2026-2503', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:50:07', NULL, NULL),
(5, 'INQ-2026-6014', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:50:08', NULL, NULL),
(6, 'INQ-2026-8904', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sada', 'admission', 'new', NULL, '2026-01-29 14:50:08', NULL, NULL),
(7, 'INQ-2026-0859', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sa', 'admission', 'new', NULL, '2026-01-29 14:51:59', NULL, NULL),
(8, 'INQ-2026-0598', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sa', 'admission', 'new', NULL, '2026-01-29 14:52:03', NULL, NULL),
(9, 'INQ-2026-6788', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'sdadsa', 'admission', 'new', NULL, '2026-01-29 14:55:35', NULL, NULL),
(10, 'INQ-2026-7187', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'dada', 'admission', 'new', NULL, '2026-01-29 14:57:39', NULL, NULL),
(11, 'INQ-2026-9824', 'Test User', 'test@example.com', NULL, 1, 'Bachelor of Science in Information Systems', 'Test inquiry', 'admission', 'new', NULL, '2026-01-29 14:59:52', NULL, NULL),
(12, 'INQ-2026-5284', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 'test', 'admission', 'new', NULL, '2026-01-29 15:01:54', NULL, NULL),
(13, 'INQ-2026-7778', 'Romasanta Patrick James Vitalsa', 'patrickmontero@gmail.com', NULL, 1, 'Bachelor of Science in Information Systems', 's', 'admission', 'new', NULL, '2026-01-29 15:03:36', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
CREATE TABLE IF NOT EXISTS `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program code (e.g., BSIS, BPA)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Full program name',
  `short_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Short display name',
  `category` enum('undergraduate','technical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undergraduate',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Department name',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program description',
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program duration (e.g., 4 Years)',
  `units` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Total units (e.g., 158 Units)',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to program image',
  `prospectus_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to prospectus file',
  `enrolled_students` int DEFAULT '0' COMMENT 'Number of enrolled students',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `highlights` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of program highlights',
  `career_opportunities` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of career opportunities',
  `admission_requirements` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of admission requirements',
  `program_head_id` int DEFAULT NULL COMMENT 'Foreign key to program_heads table',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_code` (`code`),
  KEY `idx_program_head_id` (`program_head_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `code`, `title`, `short_title`, `category`, `department`, `description`, `duration`, `units`, `image_path`, `prospectus_path`, `enrolled_students`, `status`, `highlights`, `career_opportunities`, `admission_requirements`, `program_head_id`, `created_at`, `updated_at`) VALUES
(1, 'BSIS', 'Bachelor of Science in Information Systems', 'BS Information Systems', 'undergraduate', 'Computer Science', 'A comprehensive program focusing on information systems analysis, design, and implementation for modern business environments.', '4 Years', '150 Units', '../../assets/img/programs/bsis.jpg', '../../assets/prospectus/bsis-prospectus.pdf', 45, 'active', '[\"Database Management\", \"Software Development\", \"System Analysis\", \"Network Administration\"]', '[\"Software Developer\", \"Database Administrator\", \"Systems Analyst\", \"IT Consultant\"]', '[\"High School Diploma\", \"Passing Entrance Exam\", \"Good Moral Character\"]', NULL, '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(2, 'BPA', 'Bachelor of Public Administration', 'BS Public Administration', 'undergraduate', 'Business Administration', 'Program designed to develop competent public administrators and managers for government service and public sector organizations.', '4 Years', '140 Units', '../../assets/img/programs/bpa.jpg', '../../assets/prospectus/bpa-prospectus.pdf', 38, 'active', '[\"Public Policy\", \"Administrative Law\", \"Fiscal Management\", \"Public Ethics\"]', '[\"Government Administrator\", \"Policy Analyst\", \"Public Relations Officer\", \"Community Development Officer\", \"Local Government Unit Staff\"]', '[\"High school diploma or equivalent\", \"Passing entrance examination score\", \"Good moral character\", \"Medical certificate\"]', 2, '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(3, 'WFT', 'Welding and Fabrication Technology', 'Welding Technology', 'technical', 'Technical-Vocational', 'Hands-on program providing comprehensive training in various welding techniques and fabrication processes for industrial applications.', '2 Years', '80 Units', '../../assets/img/programs/wft.jpg', '../../assets/prospectus/wft-prospectus.pdf', 25, 'active', '[\"Arc Welding\", \"MIG Welding\", \"TIG Welding\", \"Fabrication\"]', '[\"Welder\", \"Fabricator\", \"Welding Inspector\", \"Metal Worker\"]', '[\"High School Diploma\", \"Physical Fitness\", \"Good Eyesight\"]', 3, '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `program_heads`
--

DROP TABLE IF EXISTS `program_heads`;
CREATE TABLE IF NOT EXISTS `program_heads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Employee ID number',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First name',
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Middle name',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last name',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email address',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Phone number',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Department assignment',
  `specialization` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Area of specialization',
  `hire_date` date NOT NULL COMMENT 'Date of hiring',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active' COMMENT 'Employment status',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_department` (`department`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_heads`
--

INSERT INTO `program_heads` (`id`, `employee_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `department`, `specialization`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 'PH002', 'Maria', 'Reyes', 'Lopez', 'maria.lopez@colegiodenaujan.edu.ph', '09123456790', 'Business Administration', 'Accounting & Financial Management', '2019-06-20', 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(3, 'PH003', 'Jose', 'Cruz', 'Martinez', 'jose.martinez@colegiodenaujan.edu.ph', '09123456791', 'Technical-Vocational', 'Welding Technology & Fabrication', '2021-03-10', 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(4, '122', 'Habibi', 'Ahlah', 'Bombay', 'test9@gmail.com', '099877656741', 'Technical-Vocational', 'kakupalan', '2026-01-13', 'active', '2026-01-30 00:36:01', '2026-01-30 00:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `prospectus_downloads`
--

DROP TABLE IF EXISTS `prospectus_downloads`;
CREATE TABLE IF NOT EXISTS `prospectus_downloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `program_id` int NOT NULL COMMENT 'Foreign key to programs table',
  `user_ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP address of downloader',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Browser information',
  `download_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_program_download` (`program_id`,`download_date`),
  KEY `idx_ip_download` (`user_ip`,`download_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year_level` int NOT NULL,
  `capacity` int DEFAULT '40',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_code` (`section_code`),
  KEY `idx_section_code` (`section_code`),
  KEY `idx_department_code` (`department_code`),
  KEY `idx_year_level` (`year_level`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_code`, `section_name`, `department_code`, `year_level`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CS-1A', 'Computer Science 1-A', 'CS', 1, 35, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(2, 'CS-2A', 'Computer Science 2-A', 'CS', 2, 35, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(3, 'CS-3A', 'Computer Science 3-A', 'CS', 3, 35, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(4, 'CS-4A', 'Computer Science 4-A', 'CS', 4, 35, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(5, 'BPA-1A', 'Business Admin 1-A', 'BPA', 1, 40, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(6, 'BPA-2A', 'Business Admin 2-A', 'BPA', 2, 40, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(7, 'BPA-3A', 'Business Admin 3-A', 'BPA', 3, 40, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(8, 'BPA-4A', 'Business Admin 4-A', 'BPA', 4, 40, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(9, 'TVET-1A', 'Technical-Voc 1-A', 'TVET', 1, 30, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(10, 'TVET-2A', 'Technical-Voc 2-A', 'TVET', 2, 30, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `yearlevel` int DEFAULT NULL,
  `status` enum('active','inactive','graduated') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_email` (`email`),
  KEY `idx_department` (`department`),
  KEY `idx_status` (`status`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `address`, `birth_date`, `gender`, `department`, `section_id`, `yearlevel`, `status`, `avatar`, `created_at`, `updated_at`) VALUES
(4, '2023-0208', 'patricka', 'testa', 'Romasantaa', 'test3@gmail.com', '099877656741', 'putainaaaaa', '2026-01-13', 'male', 'BPA', NULL, 2, 'graduated', NULL, '2026-01-30 00:32:34', '2026-01-30 00:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `student_document_requests`
--

DROP TABLE IF EXISTS `student_document_requests`;
CREATE TABLE IF NOT EXISTS `student_document_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique request ID (e.g., REQ-2024-001)',
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Existing student ID',
  `document_type` enum('coe','tor','certificate','good_moral','transferee','others') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Specific document name',
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Purpose of document request',
  `urgency_level` enum('normal','urgent','rush') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `status` enum('pending','processing','ready','claimed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `target_date` date DEFAULT NULL COMMENT 'Expected completion date',
  `completion_date` timestamp NULL DEFAULT NULL COMMENT 'Actual completion date',
  `claimed_date` timestamp NULL DEFAULT NULL COMMENT 'When document was claimed',
  `processing_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'Processing fee if applicable',
  `payment_status` enum('unpaid','paid','waived') COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes or special instructions',
  `processed_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Staff who processed the request',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to generated document',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_id` (`request_id`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_document_type` (`document_type`),
  KEY `idx_status` (`status`),
  KEY `idx_request_date` (`request_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','email','phone','textarea','file','video','number','select') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `setting_label` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`),
  KEY `idx_setting_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `setting_label`, `setting_group`, `description`, `is_required`, `created_at`, `updated_at`) VALUES
(1, 'institution_name', 'Colegio De Naujan', 'text', 'Institution Name', 'general', 'The official name of the institution', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(2, 'contact_email', 'info@colegiodenaujan.edu.ph', 'email', 'Contact Email', 'general', 'Main contact email address', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(3, 'contact_phone', '(043) 123-4567', 'phone', 'Contact Phone', 'general', 'Main contact phone number', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(4, 'address', 'Brgy. Sta. Cruz, Naujan, Oriental Mindoro', 'textarea', 'Address', 'general', 'Physical address of the institution', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(5, 'academic_year', '2025-2026', 'select', 'Academic Year', 'general', 'Current academic year', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(6, 'home_video', 'assets/videos/landingvid.mp4', 'video', 'Home Page Video', 'media', 'Background video for the home page hero section', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(7, 'admin_username', 'admin_demo', 'text', 'Admin Username', 'account', 'Administrator username', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(8, 'admin_email', 'admin_demo@colegio.edu', 'email', 'Admin Email', 'account', 'Administrator email address', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed password',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff','faculty','program_head') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin_demo@colegio.edu', 'admin@colegio.edu', '62cc2d8b4bf2d8728120d052163a77df', 'Admin User', 'admin', 'active', NULL, '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
CREATE TABLE IF NOT EXISTS `violations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `violation_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('minor','major','critical') COLLATE utf8mb4_unicode_ci DEFAULT 'minor',
  `date_occurred` date NOT NULL,
  `reported_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_violation_type` (`violation_type`),
  KEY `idx_date_occurred` (`date_occurred`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_programs`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_active_programs`;
CREATE TABLE IF NOT EXISTS `v_active_programs` (
`category` enum('undergraduate','technical')
,`code` varchar(20)
,`department` varchar(100)
,`enrolled_students` int
,`has_prospectus` varchar(3)
,`id` int
,`short_title` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_programs_with_heads`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_programs_with_heads`;
CREATE TABLE IF NOT EXISTS `v_programs_with_heads` (
`category` enum('undergraduate','technical')
,`code` varchar(20)
,`department` varchar(100)
,`enrolled_students` int
,`id` int
,`program_head_email` varchar(150)
,`program_head_id` int
,`program_head_name` varchar(201)
,`program_head_phone` varchar(20)
,`short_title` varchar(100)
,`status` enum('active','inactive')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_program_download_stats`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_program_download_stats`;
CREATE TABLE IF NOT EXISTS `v_program_download_stats` (
`category` enum('undergraduate','technical')
,`code` varchar(20)
,`download_count` bigint
,`enrolled_students` int
,`id` int
,`short_title` varchar(100)
,`status` enum('active','inactive')
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_program_heads_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_program_heads_summary`;
CREATE TABLE IF NOT EXISTS `v_program_heads_summary` (
`department` varchar(100)
,`email` varchar(150)
,`employee_id` varchar(50)
,`full_name` varchar(302)
,`hire_date` date
,`id` int
,`phone` varchar(20)
,`specialization` varchar(150)
,`status` enum('active','inactive')
);

-- --------------------------------------------------------

--
-- Structure for view `all_archived_items`
--
DROP TABLE IF EXISTS `all_archived_items`;

DROP VIEW IF EXISTS `all_archived_items`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all_archived_items`  AS SELECT 'admission' AS `item_type`, `archive_admissions`.`id` AS `id`, `archive_admissions`.`original_id` AS `original_id`, `archive_admissions`.`application_id` AS `reference_id`, concat(`archive_admissions`.`first_name`,' ',coalesce(`archive_admissions`.`middle_name`,''),' ',`archive_admissions`.`last_name`) AS `item_name`, `archive_admissions`.`email` AS `email`, `archive_admissions`.`status` AS `status`, `archive_admissions`.`deleted_at` AS `deleted_at`, `archive_admissions`.`deleted_by` AS `deleted_by`, `archive_admissions`.`delete_reason` AS `delete_reason`, 'admissions' AS `source_table` FROM `archive_admissions`union all select 'program' AS `item_type`,`archive_programs`.`id` AS `id`,`archive_programs`.`original_id` AS `original_id`,`archive_programs`.`program_code` AS `reference_id`,`archive_programs`.`program_title` AS `item_name`,NULL AS `email`,`archive_programs`.`status` AS `status`,`archive_programs`.`deleted_at` AS `deleted_at`,`archive_programs`.`deleted_by` AS `deleted_by`,`archive_programs`.`delete_reason` AS `delete_reason`,'programs' AS `source_table` from `archive_programs` union all select 'program_head' AS `item_type`,`archive_program_heads`.`id` AS `id`,`archive_program_heads`.`original_id` AS `original_id`,concat(`archive_program_heads`.`first_name`,' ',`archive_program_heads`.`last_name`) AS `reference_id`,concat(`archive_program_heads`.`first_name`,' ',`archive_program_heads`.`last_name`) AS `item_name`,`archive_program_heads`.`email` AS `email`,`archive_program_heads`.`status` AS `status`,`archive_program_heads`.`deleted_at` AS `deleted_at`,`archive_program_heads`.`deleted_by` AS `deleted_by`,`archive_program_heads`.`delete_reason` AS `delete_reason`,'program_heads' AS `source_table` from `archive_program_heads` union all select 'student' AS `item_type`,`archive_students`.`id` AS `id`,`archive_students`.`original_id` AS `original_id`,`archive_students`.`student_id` AS `reference_id`,concat(`archive_students`.`first_name`,' ',coalesce(`archive_students`.`middle_name`,''),' ',`archive_students`.`last_name`) AS `item_name`,`archive_students`.`email` AS `email`,`archive_students`.`status` AS `status`,`archive_students`.`deleted_at` AS `deleted_at`,`archive_students`.`deleted_by` AS `deleted_by`,`archive_students`.`delete_reason` AS `delete_reason`,'students' AS `source_table` from `archive_students` order by `deleted_at` desc  ;

-- --------------------------------------------------------

--
-- Structure for view `v_active_programs`
--
DROP TABLE IF EXISTS `v_active_programs`;

DROP VIEW IF EXISTS `v_active_programs`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_programs`  AS SELECT `programs`.`id` AS `id`, `programs`.`code` AS `code`, `programs`.`short_title` AS `short_title`, `programs`.`category` AS `category`, `programs`.`department` AS `department`, `programs`.`enrolled_students` AS `enrolled_students`, (case when (`programs`.`prospectus_path` is not null) then 'Yes' else 'No' end) AS `has_prospectus` FROM `programs` WHERE (`programs`.`status` = 'active') ;

-- --------------------------------------------------------

--
-- Structure for view `v_programs_with_heads`
--
DROP TABLE IF EXISTS `v_programs_with_heads`;

DROP VIEW IF EXISTS `v_programs_with_heads`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_programs_with_heads`  AS SELECT `p`.`id` AS `id`, `p`.`code` AS `code`, `p`.`short_title` AS `short_title`, `p`.`category` AS `category`, `p`.`department` AS `department`, `p`.`enrolled_students` AS `enrolled_students`, `p`.`program_head_id` AS `program_head_id`, concat(`ph`.`first_name`,' ',`ph`.`last_name`) AS `program_head_name`, `ph`.`email` AS `program_head_email`, `ph`.`phone` AS `program_head_phone`, `p`.`status` AS `status` FROM (`programs` `p` left join `program_heads` `ph` on((`p`.`program_head_id` = `ph`.`id`))) WHERE (`p`.`status` = 'active') ;

-- --------------------------------------------------------

--
-- Structure for view `v_program_download_stats`
--
DROP TABLE IF EXISTS `v_program_download_stats`;

DROP VIEW IF EXISTS `v_program_download_stats`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_program_download_stats`  AS SELECT `p`.`id` AS `id`, `p`.`code` AS `code`, `p`.`short_title` AS `short_title`, `p`.`category` AS `category`, `p`.`status` AS `status`, coalesce(`download_counts`.`download_count`,0) AS `download_count`, `p`.`enrolled_students` AS `enrolled_students` FROM (`programs` `p` left join (select `prospectus_downloads`.`program_id` AS `program_id`,count(0) AS `download_count` from `prospectus_downloads` group by `prospectus_downloads`.`program_id`) `download_counts` on((`p`.`id` = `download_counts`.`program_id`))) ORDER BY `p`.`category` ASC, `p`.`short_title` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_program_heads_summary`
--
DROP TABLE IF EXISTS `v_program_heads_summary`;

DROP VIEW IF EXISTS `v_program_heads_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_program_heads_summary`  AS SELECT `program_heads`.`id` AS `id`, `program_heads`.`employee_id` AS `employee_id`, concat(`program_heads`.`first_name`,' ',ifnull(`program_heads`.`middle_name`,''),' ',`program_heads`.`last_name`) AS `full_name`, `program_heads`.`email` AS `email`, `program_heads`.`phone` AS `phone`, `program_heads`.`department` AS `department`, `program_heads`.`specialization` AS `specialization`, `program_heads`.`hire_date` AS `hire_date`, `program_heads`.`status` AS `status` FROM `program_heads` ORDER BY `program_heads`.`last_name` ASC, `program_heads`.`first_name` ASC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `admissions_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `admission_status_log`
--
ALTER TABLE `admission_status_log`
  ADD CONSTRAINT `admission_status_log_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_program_head` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `prospectus_downloads`
--
ALTER TABLE `prospectus_downloads`
  ADD CONSTRAINT `prospectus_downloads_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`department_code`) REFERENCES `departments` (`department_code`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`department`) REFERENCES `departments` (`department_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `student_document_requests`
--
ALTER TABLE `student_document_requests`
  ADD CONSTRAINT `student_document_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `violations`
--
ALTER TABLE `violations`
  ADD CONSTRAINT `violations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
