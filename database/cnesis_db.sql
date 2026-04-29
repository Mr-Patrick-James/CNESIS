-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2026 at 03:11 AM
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
  `application_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique application ID',
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Existing student ID (NULL for new freshmen)',
  `program_id` int DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `high_school` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'High school (for freshmen)',
  `last_school` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Last school attended (for old students)',
  `year_graduated` int DEFAULT NULL COMMENT 'Graduation year',
  `gwa` decimal(3,2) DEFAULT NULL COMMENT 'General Weighted Average',
  `entrance_exam_score` int DEFAULT NULL COMMENT 'Entrance exam score',
  `admission_type` enum('freshman','transferee','returnee','shifter') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_program` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Previous program (for transferees/shifters)',
  `status` enum('pending','approved','rejected','enrolled','new','draft','verified','scheduled','examed','did not attend','reschedule','passed','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `exam_schedule_id` int DEFAULT NULL,
  `attachments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON object of file paths',
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Full JSON dump of form data',
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int DEFAULT NULL COMMENT 'Admin user ID who reviewed',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Admin notes',
  `assigned_department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_section_id` int DEFAULT NULL,
  `assigned_year_level` int DEFAULT NULL,
  `assigned_section_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_id` (`application_id`),
  KEY `idx_status` (`status`),
  KEY `idx_program` (`program_id`),
  KEY `idx_submitted` (`submitted_at`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_admission_type` (`admission_type`),
  KEY `fk_exam_schedule` (`exam_schedule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `application_id`, `student_id`, `program_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `birthdate`, `gender`, `address`, `high_school`, `last_school`, `year_graduated`, `gwa`, `entrance_exam_score`, `admission_type`, `previous_program`, `status`, `exam_schedule_id`, `attachments`, `form_data`, `submitted_at`, `reviewed_at`, `reviewed_by`, `notes`, `assigned_department`, `assigned_section_id`, `assigned_year_level`, `assigned_section_name`) VALUES
(108, 'APP-2026-04105', NULL, 3, 'Rosa', NULL, 'Castillo', 'rosa.castillo126@example.com', '09230100492', '2007-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'passed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-04-24 03:42:40', 1, '\n\n2026-02-27 06:25:08: Approved by admin\n\n2026-02-27 06:29:56: Rejected by admin\n\n2026-03-01 06:10:16: Status manually updated in exam scheduling modal to examed\n\n2026-03-23 10:28:12: PASSed during finalization\n\n2026-03-23 12:58:13: Status manually updated in exam scheduling modal to reschedule\n\n2026-03-23 13:00:58: PASSed during finalization\n\n2026-04-24 03:42:40: Passed in bulk by admin', NULL, NULL, NULL, NULL),
(111, 'APP-2026-78281', NULL, 3, 'Jorge', NULL, 'Salvador', 'jorge.salvador179@example.com', '09866002478', '2007-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-02-27 06:27:26: Approved by admin\n\n2026-03-23 13:01:04: PASSed during finalization', 'BTVTED-WFT', NULL, 1, 'BTVTED-WFT1'),
(112, 'APP-2026-76969', NULL, 3, 'Antonio', NULL, 'Flores', 'antonio.flores842@example.com', '09434105895', '2008-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-02-27 06:31:00: Approved by admin\n\n2026-03-23 13:01:11: PASSed during finalization', 'BTVTED-WFT', NULL, 1, 'BTVTED-WFT1'),
(113, 'APP-2026-69908', NULL, 1, 'Teresa', NULL, 'Dela Cruz', 'teresa.delacruz741@example.com', '09193098247', '2009-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-02-27 06:56:39: \n\n2026-03-23 13:01:17: PASSed during finalization', 'BSIS', NULL, 1, 'BSIS1'),
(114, 'APP-2026-62884', NULL, 3, 'Patricia', NULL, 'Cruz', 'patricia.cruz366@example.com', '09154469040', '2007-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-02-27 06:58:31: \n\n2026-03-23 13:01:23: PASSed during finalization', 'BTVTED-WFT', NULL, 1, 'BTVTED-WFT1'),
(115, 'APP-2026-79594', NULL, 2, 'Raquel', NULL, 'Miranda', 'raquel.miranda908@example.com', '09200268012', '2009-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'rejected', NULL, NULL, '{}', '2026-02-25 02:23:14', '2026-03-01 06:39:58', 1, '\n\n2026-03-01 06:39:58: Rejected by admin', NULL, NULL, NULL, NULL),
(116, 'APP-2026-57060', NULL, 3, 'Gloria', NULL, 'Nicolas', 'gloria.nicolas258@example.com', '09781490715', '2009-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-03-01 06:57:42: Approved by admin', NULL, NULL, NULL, NULL),
(117, 'APP-2026-39960', NULL, 3, 'Liza', NULL, 'Navarro', 'liza.navarro423@example.com', '09623649658', '2006-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-03-01 06:57:42: Approved by admin', NULL, NULL, NULL, NULL),
(118, 'APP-2026-36365', NULL, 2, 'Victor', NULL, 'Guevarra', 'victor.guevarra750@example.com', '09421473402', '2009-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-03-01 06:57:42: Approved by admin', NULL, NULL, NULL, NULL),
(119, 'APP-2026-46958', NULL, 1, 'Laura', NULL, 'Tolentino', 'laura.tolentino566@example.com', '09996841186', '2008-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-03-01 06:57:42: Approved by admin', NULL, NULL, NULL, NULL),
(120, 'APP-2026-06973', NULL, 3, 'Roberto', NULL, 'Ocampo', 'roberto.ocampo774@example.com', '09783959316', '2006-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'examed', 9, NULL, '{}', '2026-02-25 02:23:14', '2026-03-23 13:59:01', 1, '\n\n2026-03-11 01:14:37: Approved by admin', NULL, NULL, NULL, NULL),
(121, 'APP-2026-93284', NULL, 3, 'Elena', NULL, 'Perez', 'elena.perez538@example.com', '09953653574', '2009-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'scheduled', 10, NULL, '{}', '2026-02-25 02:23:14', '2026-04-24 03:50:15', 1, '\n\n2026-04-24 03:50:15: Approveed in bulk by admin', NULL, NULL, NULL, NULL),
(122, 'APP-2026-50499', NULL, 3, 'Andres', NULL, 'Lopez', 'andres.lopez569@example.com', '09908269228', '2006-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 'APP-2026-36342', NULL, 3, 'Eduardo', NULL, 'Cabrera', 'eduardo.cabrera551@example.com', '09107884546', '2007-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 'APP-2026-66528', NULL, 3, 'Gabriel', NULL, 'Abad', 'gabriel.abad716@example.com', '09518839641', '2009-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 'APP-2026-71788', NULL, 2, 'Diego', NULL, 'Rivera', 'diego.rivera125@example.com', '09901091956', '2008-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 'APP-2026-69650', NULL, 3, 'Laura', NULL, 'Guevarra', 'laura.guevarra116@example.com', '09138779618', '2008-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 'APP-2026-13253', NULL, 3, 'Javier', NULL, 'Torres', 'javier.torres969@example.com', '09263892873', '2009-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 'APP-2026-23902', NULL, 3, 'Beatriz', NULL, 'Rodriguez', 'beatriz.rodriguez948@example.com', '09937470102', '2008-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 'APP-2026-98186', NULL, 2, 'Laura', NULL, 'De Leon', 'laura.deleon726@example.com', '09996282095', '2006-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 'APP-2026-55096', NULL, 3, 'Margarita', NULL, 'Santiago', 'margarita.santiago682@example.com', '09754768517', '2009-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 'APP-2026-65548', NULL, 1, 'Pedro', NULL, 'Castillo', 'pedro.castillo578@example.com', '09743429411', '2009-02-25', 'female', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'APP-2026-87012', NULL, 2, 'Teresa', NULL, 'Cabrera', 'teresa.cabrera484@example.com', '09745527898', '2007-02-25', 'male', 'Sample Address, City, Province', 'Sample High School', 'Sample High School', 2025, 9.99, NULL, 'freshman', NULL, 'pending', NULL, NULL, '{}', '2026-02-25 02:23:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 'APP-2026-355384833', NULL, 2, 'lloyd', 'Pampolina', 'Cataring', 'lloydpc16@gmail.com', '09563725576', '0001-09-01', 'male', 'Purok 3, Concepcion, Naujan Oriental Mindoro, 5204', 'bsdgashd', 'bsdgashd', 2012, 9.99, NULL, 'freshman', '1', 'scheduled', 10, '{\"valid_id\":[\"assets/uploads/admissions/2026-04/valid_id-1777001355-00ccf8d4.jpg\"],\"shs_cert\":\"assets/uploads/admissions/2026-04/shs_cert-1777001355-61d6629b.jpg\",\"good_moral\":\"assets/uploads/admissions/2026-04/good_moral-1777001355-9404a09b.jpg\",\"diploma\":\"assets/uploads/admissions/2026-04/diploma-1777001355-6280e342.jpg\"}', '{\"gender\":\"male\",\"suffix\":\"\",\"civil_status\":\"single\",\"citizenship\":\"Fiipino\",\"birth_place\":\"Rizal, Laguna\",\"parents\":[{\"first_name\":\"Lerma \",\"middle_name\":\"Medular\",\"last_name\":\"Cataring\",\"extension\":\"\",\"age\":\"73\",\"relationship\":\"Mother\",\"education\":\"High school\",\"occupation\":\"Housewife\",\"income\":\"501\",\"contact\":\"09563725576\",\"street\":\"052 West Poblacion\",\"city\":\"Rizal Laguna\",\"is_emergency\":true}],\"schools\":[{\"name\":\"jkasnfdhsdfa,dfkla\",\"year\":\"2000\",\"level\":\"Elementary\",\"type\":\"PRIVATE\",\"city\":\"Naujan, Oriental Mindoro\"},{\"name\":\"Mena Garong Valecia Memoria High School\",\"year\":\"2008\",\"level\":\"Junior High School\",\"type\":\"PUBLIC\",\"city\":\"Naujan, Oriental Mindoro\"},{\"name\":\"bsdgashd\",\"year\":\"2012\",\"level\":\"Senior High School\",\"type\":\"PUBLIC\",\"city\":\"Naujan, Oriental Mindoro\"}],\"alternative_program\":\"1\",\"alternative_program_title\":\"Bachelor of Science in Information Systems (BSIS)\",\"shs_strand\":\"ABM\",\"latest_attainment\":\"Graduating Senior High School\",\"grade10_gpa\":\"85\",\"grade11_gpa\":\"99\",\"grade12_gpa\":\"99\",\"zip_code\":\"5204\",\"street_no\":\"Purok 3\",\"barangay\":\"Concepcion\",\"city_province\":\"Naujan Oriental Mindoro\",\"already_enrolled\":\"no\",\"shs_transfer\":\"no\",\"shs_transfer_from\":\"\",\"shs_transfer_year\":\"\"}', '2026-04-24 03:29:15', '2026-04-24 03:50:15', 1, '\n\n2026-04-24 03:50:15: Approveed in bulk by admin', NULL, NULL, NULL, NULL),
(147, 'DRAFT-1777378883', '', NULL, '', '', '', 'patrickmontero833@gmail.com', '', NULL, '', '', '', '', NULL, NULL, NULL, 'freshman', '', 'draft', NULL, NULL, '{\"extension_name\":\"\",\"civil_status\":\"\",\"citizenship\":\"\",\"birth_place\":\"\",\"street_no\":\"\",\"barangay\":\"\",\"city_province\":\"\",\"zip_code\":\"\",\"program_id_1\":\"\",\"program_id_2\":\"\",\"latest_attainment\":\"Graduating Senior High School\",\"gpa_rating\":\"\",\"shs_strand\":\"STEM\",\"grade10_gpa\":\"\",\"grade11_gpa\":\"\",\"grade12_gpa\":\"\",\"shs_transfer_from\":\"\",\"shs_transfer_year\":\"\",\"current_step\":\"aap\",\"parent_first_name\":[\"\"],\"parent_middle_name\":[\"\"],\"parent_last_name\":[\"\"],\"parent_extension\":[\"\"],\"parent_age\":[\"\"],\"parent_relationship\":[\"Father\"],\"parent_education\":[\"\"],\"parent_occupation\":[\"\"],\"parent_income\":[\"\"],\"parent_contact\":[\"\"],\"parent_street\":[\"\"],\"parent_city\":[\"\"],\"school_name\":[\"\",\"\",\"\"],\"school_year\":[\"\",\"\",\"\"],\"school_level\":[\"Elementary\",\"Junior High School\",\"Senior High School\"],\"school_type\":[\"PRIVATE\",\"PUBLIC\",\"PUBLIC\"],\"school_city\":[\"\",\"\",\"\"]}', '2026-04-28 12:21:23', NULL, NULL, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admission_status_log`
--

DROP TABLE IF EXISTS `admission_status_log`;
CREATE TABLE IF NOT EXISTS `admission_status_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admission_id` int NOT NULL COMMENT 'Related admission ID',
  `old_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Previous status',
  `new_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'New status',
  `action_by` int NOT NULL COMMENT 'Admin user ID who made the change',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Notes about the status change',
  `email_sent` tinyint(1) DEFAULT '0' COMMENT 'Whether email was sent',
  `email_error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Email error if sending failed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the change was made',
  PRIMARY KEY (`id`),
  KEY `idx_admission_id` (`admission_id`),
  KEY `idx_action_by` (`action_by`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admission_status_log`
--

INSERT INTO `admission_status_log` (`id`, `admission_id`, `old_status`, `new_status`, `action_by`, `notes`, `email_sent`, `email_error`, `created_at`) VALUES
(7, 18, 'pending', 'approved', 1, '', 0, NULL, '2026-02-23 03:48:24'),
(8, 85, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-25 04:00:58'),
(9, 136, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-25 04:00:58'),
(10, 87, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-25 04:00:58'),
(11, 134, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-25 04:00:58'),
(12, 86, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-25 04:00:58'),
(13, 138, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-26 05:42:17'),
(14, 88, 'pending', 'approved', 1, '', 0, NULL, '2026-02-26 06:15:11'),
(15, 92, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 03:20:14'),
(16, 90, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 03:20:14'),
(17, 94, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 03:20:14'),
(18, 89, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:16:44'),
(19, 91, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:19:59'),
(20, 108, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:25:08'),
(21, 109, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:25:42'),
(22, 110, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:25:42'),
(23, 111, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:27:26'),
(24, 108, 'scheduled', 'rejected', 1, 'Rejected by admin', 0, NULL, '2026-02-27 06:29:56'),
(25, 112, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-02-27 06:31:00'),
(26, 113, 'pending', 'approved', 1, '', 0, NULL, '2026-02-27 06:56:39'),
(27, 114, 'pending', 'approved', 1, '', 0, NULL, '2026-02-27 06:58:31'),
(28, 108, 'scheduled', 'examed', 1, 'Status manually updated in exam scheduling modal to examed', 0, NULL, '2026-03-01 06:10:16'),
(29, 115, 'pending', 'rejected', 1, 'Rejected by admin', 0, NULL, '2026-03-01 06:39:58'),
(30, 116, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-03-01 06:57:42'),
(31, 117, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-03-01 06:57:42'),
(32, 118, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-03-01 06:57:42'),
(33, 119, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-03-01 06:57:42'),
(34, 110, 'scheduled', 'examed', 1, 'Status manually updated in exam scheduling modal to examed', 0, NULL, '2026-03-03 00:36:11'),
(35, 109, 'scheduled', 'examed', 1, 'Status manually updated in exam scheduling modal to examed', 0, NULL, '2026-03-09 08:01:55'),
(36, 120, 'pending', 'approved', 1, 'Approved by admin', 0, NULL, '2026-03-11 01:14:37'),
(37, 108, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 10:28:12'),
(38, 109, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 10:36:00'),
(39, 110, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 11:04:26'),
(40, 108, '', 'reschedule', 1, 'Status manually updated in exam scheduling modal to reschedule', 0, NULL, '2026-03-23 12:58:13'),
(41, 108, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 13:00:58'),
(42, 111, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 13:01:04'),
(43, 112, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 13:01:11'),
(44, 113, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 13:01:17'),
(45, 114, 'examed', 'passed', 1, 'PASSed during finalization', 0, NULL, '2026-03-23 13:01:23'),
(46, 108, 'examed', 'passed', 1, 'Passed in bulk by admin', 0, NULL, '2026-04-24 03:42:40'),
(47, 146, 'pending', 'approved', 1, 'Approveed in bulk by admin', 0, NULL, '2026-04-24 03:50:15'),
(48, 121, 'pending', 'approved', 1, 'Approveed in bulk by admin', 0, NULL, '2026-04-24 03:50:15');

-- --------------------------------------------------------

--
-- Table structure for table `all_archived_items`
--

DROP TABLE IF EXISTS `all_archived_items`;
CREATE TABLE IF NOT EXISTS `all_archived_items` (
  `delete_reason` mediumtext,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `id` int DEFAULT NULL,
  `item_name` varchar(302) DEFAULT NULL,
  `item_type` varchar(12) DEFAULT NULL,
  `original_id` int DEFAULT NULL,
  `reference_id` varchar(201) DEFAULT NULL,
  `source_table` varchar(13) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_admissions`
--

DROP TABLE IF EXISTS `archive_admissions`;
CREATE TABLE IF NOT EXISTS `archive_admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `application_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admission_type` enum('freshman','transferee','returnee','shifter') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `status` enum('pending','approved','rejected','processing','enrolled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_original_id` (`original_id`),
  KEY `idx_application_id` (`application_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  KEY `idx_status` (`status`),
  KEY `idx_admission_type` (`admission_type`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `archive_admissions`
--

INSERT INTO `archive_admissions` (`id`, `original_id`, `application_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `birthdate`, `gender`, `address`, `admission_type`, `program_id`, `status`, `student_id`, `submitted_at`, `updated_at`, `deleted_at`, `deleted_by`, `delete_reason`, `notes`) VALUES
(9, 144, 'APP-2026-520460304', 'research', '', 'ewrw', 'patrickromasanta296@gmail.com', '09989134594', '1991-03-19', 'male', 'pinoy, pinoy, pinoy, 2502', 'freshman', 1, 'pending', NULL, '2026-03-19 05:18:40', '2026-03-25 00:07:36', '2026-03-25 00:07:36', 'Administrator', 'Manual deletion by administrator', '');

-- --------------------------------------------------------

--
-- Table structure for table `archive_programs`
--

DROP TABLE IF EXISTS `archive_programs`;
CREATE TABLE IF NOT EXISTS `archive_programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `program_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_years` int DEFAULT NULL,
  `tuition_fee` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `hire_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_students`
--

DROP TABLE IF EXISTS `archive_students`;
CREATE TABLE IF NOT EXISTS `archive_students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `yearlevel` int DEFAULT NULL,
  `status` enum('active','inactive','graduated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'inactive',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2710 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE IF NOT EXISTS `class_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int NOT NULL,
  `section_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `semester` int DEFAULT '1',
  `instructor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `subject_id`, `section_id`, `student_id`, `semester`, `instructor_name`, `day_of_week`, `start_time`, `end_time`, `room`, `created_at`) VALUES
(1, 3, 17, NULL, 1, 'Prof. Sample User', 'Monday', '08:00:00', '11:00:00', 'Room 101', '2026-03-18 13:54:08'),
(2, 1, 17, NULL, 1, 'Prof. Sample User', 'Tuesday', '08:00:00', '11:00:00', 'Room 101', '2026-03-18 13:54:08'),
(3, 2, 17, NULL, 1, 'Prof. Sample User', 'Wednesday', '08:00:00', '11:00:00', 'Room 101', '2026-03-18 13:54:08'),
(4, 4, 17, NULL, 1, 'Prof. Sample User', 'Thursday', '08:00:00', '11:00:00', 'Room 101', '2026-03-18 13:54:08'),
(8, 17, 28, NULL, 2, 'Ian louis Motol', 'Friday', '06:00:00', '12:00:00', 'Com Lab', '2026-03-19 03:29:09'),
(10, 10, 28, NULL, 1, 'Mr. Ian Louis Motol', 'Monday', '13:00:00', '14:00:00', 'Comlab', '2026-03-19 04:17:25'),
(13, 10, 28, 486, 1, 'Sir June Paul Anonuevo', 'Monday', '07:00:00', '08:30:00', 'COMLAB', '2026-03-19 07:35:12'),
(14, 25, 30, NULL, 1, 'Ma\'am Faye', 'Monday', '08:00:00', '09:00:00', 'AVR', '2026-03-30 08:09:47'),
(15, 8, 20, NULL, 1, 'Sir BJ', 'Monday', '08:00:00', '09:00:00', 'COMLAB', '2026-04-24 03:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `department_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `head_of_department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
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
(1, 'BSIS', 'Bachelor of Science in Information Systems', 'Department of Information Systems', NULL, 'active', '2026-01-29 14:47:27', '2026-02-23 10:54:34'),
(2, 'BPA', 'Business Administration', 'Department of Business and Public Administration', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27'),
(3, 'BTVTED', 'Bachelor of Technical-Vocational Teacher Education', 'Department of Technical-Vocational Teacher Education', NULL, 'active', '2026-01-29 14:47:27', '2026-02-23 10:54:34'),
(4, 'GED', 'General Education', 'Department of General Education and Humanities', NULL, 'active', '2026-01-29 14:47:27', '2026-01-29 14:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `email_configs`
--

DROP TABLE IF EXISTS `email_configs`;
CREATE TABLE IF NOT EXISTS `email_configs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default' COMMENT 'Configuration name',
  `smtp_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP server hostname',
  `smtp_port` int NOT NULL DEFAULT '587' COMMENT 'SMTP port',
  `smtp_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP username',
  `smtp_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SMTP password (encrypted)',
  `encryption_type` enum('none','tls','ssl') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'tls' COMMENT 'Encryption type',
  `from_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'From email address',
  `from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'From display name',
  `reply_to_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reply-to email address',
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
(1, 'default', 'smtp.gmail.com', 587, 'ventiletos@gmail.com', 'xnkdvyyukaydnrnf', 'tls', 'ventiletos@gmail.com', 'Colegio De Naujan', 'ventiletos@gmail.com', 1, '2026-01-29 14:47:27', '2026-04-24 00:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `email_documents`
--

DROP TABLE IF EXISTS `email_documents`;
CREATE TABLE IF NOT EXISTS `email_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Original file name',
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Server file path',
  `file_size` int NOT NULL COMMENT 'File size in bytes',
  `file_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'MIME type',
  `category` enum('application-form','requirements','policies','general','templates','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general' COMMENT 'Document category',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Document description',
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
  `recipient_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email address of recipient',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email subject',
  `sent_successfully` tinyint(1) NOT NULL COMMENT 'Whether email was sent successfully',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Error message if sending failed',
  `email_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type of email (admission, acceptance, etc.)',
  `admission_id` int DEFAULT NULL COMMENT 'Related admission ID if applicable',
  `sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When email was sent',
  PRIMARY KEY (`id`),
  KEY `idx_recipient_email` (`recipient_email`),
  KEY `idx_sent_successfully` (`sent_successfully`),
  KEY `idx_sent_at` (`sent_at`),
  KEY `idx_admission_id` (`admission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `recipient_email`, `subject`, `sent_successfully`, `error_message`, `email_type`, `admission_id`, `sent_at`) VALUES
(1, 'ventiletos12@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-17 08:19:10'),
(2, 'ventiletos1@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-23 22:36:46'),
(3, 'patrickmontero833@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-23 22:46:27'),
(4, 'user@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-24 12:29:21'),
(5, 'manhicshishi15@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-25 01:16:05'),
(6, 'anonuevoprincess09@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-25 01:25:47'),
(7, 'manhicshishi15@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-25 02:52:40'),
(8, 'jamaicavillena16@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-25 03:06:04'),
(9, 'ventiletos12@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-02-26 05:41:15'),
(10, 'feudoclariss@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-03-04 12:03:30'),
(11, 'monalizawaing41@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-03-08 07:09:39'),
(12, 'patrickromasanta296@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-03-19 05:18:45'),
(13, 'lloydpc16@gmail.com', 'Application Received - Colegio De Naujan', 1, '', NULL, NULL, '2026-04-24 03:29:25');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Template identifier',
  `template_type` enum('admission','acceptance','rejection','reminder','payment','general') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email subject template',
  `html_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTML email body template',
  `text_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Plain text email body template',
  `variables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON string of available variables',
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
-- Table structure for table `email_verifications`
--

DROP TABLE IF EXISTS `email_verifications`;
CREATE TABLE IF NOT EXISTS `email_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','verified','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  `portal_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_otp` (`otp_code`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_verifications`
--

INSERT INTO `email_verifications` (`id`, `email`, `password_hash`, `otp_code`, `status`, `created_at`, `expires_at`, `portal_token`, `token_expires_at`) VALUES
(1, 'ventiletos12@gmail.com', NULL, '850903', 'expired', '2026-02-12 09:41:03', '2026-02-12 01:56:03', NULL, NULL),
(2, 'ventiletos12@gmail.com', NULL, '110424', 'verified', '2026-02-12 09:47:54', '2026-02-12 02:02:54', NULL, NULL),
(3, 'ventiletos12@gmail.com', NULL, '267495', 'expired', '2026-02-12 09:49:10', '2026-02-12 02:04:10', NULL, NULL),
(4, 'ventiletos12@gmail.com', NULL, '660155', 'verified', '2026-02-12 09:55:33', '2026-02-12 02:10:33', '1feae81baa4ab173b9d3f303249cb48aedca276cb5a32be2250142b13841f8f8', '2026-02-13 09:56:04'),
(5, 'manhicshi715@gmail.com', NULL, '930347', 'verified', '2026-02-13 04:13:07', '2026-02-12 20:28:07', '894d4278ba2b28ad34af1eac3aca5d5de9c52963d552bf04e7aa4e864990a7fc', '2026-02-14 04:13:41'),
(6, 'anonuevoprincess09@gmail.com', NULL, '568777', 'verified', '2026-02-13 05:20:15', '2026-02-12 21:35:15', '5374422c2e139e70148c85980861cf88d1c2e784eaeb19e73464e90897a92218', '2026-02-14 05:21:19'),
(7, 'manhicshishi15@gmail.com', NULL, '211347', 'verified', '2026-02-13 05:29:42', '2026-02-12 21:44:42', '522cea937114f58eb93a21f7c26a8ecfd19e2e66f97d765bb1018b46eb16ae29', '2026-02-14 05:30:08'),
(8, 'manhic715@gmail.com', NULL, '462740', 'pending', '2026-02-13 05:58:37', '2026-02-12 22:13:37', NULL, NULL),
(9, 'ventiletos12@gmail.com', '$2y$10$YUZVmMTaKpREatLKtZBgzOKPB5lDhFWC./VLWusV/NmSZCqxuyYQG', '406036', 'verified', '2026-02-15 13:31:29', '2026-02-15 05:46:29', 'ce6e4cf86e0cd494933f1662f8ba349448667f57ebcaa4cdf1754a28319a8f70', '2026-02-16 13:32:12'),
(10, 'ventiletos12@gmail.com', '$2y$10$cmh/MkkM4/5nMtXdVuTUUeYs/Et.SbgTABYAeNcvNtAdeev9Ps/Qy', '265883', 'verified', '2026-02-17 01:51:02', '2026-02-16 18:06:02', 'f33f3f517c258c920f6d629c425cbd595fe7875df7f587a8435b7aa1d70a806c', '2026-02-18 01:51:30'),
(11, 'patrickmontero833@gmail.com', '$2y$10$QRW/BwuebWlfj6EIW3LdMeuq1xiUGL8W8hIHmgKOJfXNk9JbwpT8m', '570834', 'verified', '2026-02-17 05:53:54', '2026-02-16 22:08:54', '65fcbcb2dd6070089f9783037a4507f53b200dcd31bab7eac6701bf756139b22', '2026-02-18 05:55:05'),
(12, 'ventiletos12@gmail.com', '$2y$10$XmCkIIldkEH7YuCA5DL3AObj6bwHOk0qPpSsTl4qkGT44WzErTrWu', '301223', 'verified', '2026-02-23 03:16:31', '2026-02-22 19:31:30', 'af14c15803efbec2a0215e34a9a4df16f82ee4e2d2e72c139cbd744797072d26', '2026-02-24 03:18:47'),
(13, 'charlainedebelen086@gmail.com', '$2y$10$p.chLZCKfUDFQ7sKmh4w0OD2Fn5z5f1YDSv3/t5UNd22gwAow.7YW', '838777', 'verified', '2026-02-23 03:22:21', '2026-02-22 19:37:20', '34ebd302031d9659239d31c25979ff354e3b6fdcc27ff4a22eaed8c39e4c71e9', '2026-02-24 03:22:42'),
(14, 'geraldinegaran70@gmail.com', '$2y$10$.oEAH.zQPedguzuf6J79.ebteGY4xKu0qvUpbKHazjgH1xQO/tEhe', '566002', 'verified', '2026-02-23 12:22:55', '2026-02-23 04:37:55', '22a9104ea7fad28ee18a1d90bda16306fb29c082660901ffac93d3ab4d95e55f', '2026-02-24 12:24:16'),
(15, 'ventiletos1@gmail.com', '$2y$10$EJJFB2SpxAbaGP.83nH6NOZqzFPDUP4lLGTwNWJCp1OBIvlHVjleK', '251278', 'verified', '2026-02-23 22:33:55', '2026-02-23 14:48:55', 'bd83177912d5ad82d8c76fdb787f885222248dc4c4caa2f84291e48c63ed59cb', '2026-02-24 22:34:50'),
(16, 'patrickmontero833@gmail.com', '$2y$10$uOf35vkXRROeyvZKBSXRnuOw3KqLY0e/IkyblbwLC7vpl8aKl7zoW', '492175', 'verified', '2026-02-23 22:44:16', '2026-02-23 14:59:16', 'bf47db3aca9dd4655910e898407c2d4f0987ebc12d4290dc737f9402deca3055', '2026-02-24 22:44:49'),
(17, 'test@gmail.com', '$2y$10$2pVwSVl2n9Y8qARBplBRoe1lAa1z01664HHcyj4dGPXkqK4z.E/Qu', '238351', 'verified', '2026-02-24 11:04:56', '2026-02-24 03:19:56', 'd316977e5a91e68b9b0fd821792805fdc5574528658b7b12c325d7c0e91397e0', '2026-02-25 11:06:09'),
(18, 'patrick.james.romasanta@colegiodenaujan.edu.ph', '$2y$10$yspvLBOD4Wra6QfTRDjhh.AMa06VTVW.Y7oHfN3Jz.rOPIFg/tE2m', '594459', 'verified', '2026-02-24 11:50:09', '2026-02-24 04:05:09', '0ae7475a63755e2b94e400a89083bc013b63e9f98234b2def0c4ae299c04c8c0', '2026-02-25 12:02:32'),
(19, 'test1@gmail.com', '$2y$10$NK1dnGowa6RwSxRp8.EPxuG3eom8p0L2Siybqn5d.2zHCMWEgh3Wu', '517532', 'pending', '2026-02-24 12:04:08', '2026-02-24 04:19:08', NULL, NULL),
(20, 'user@gmail.com', '$2y$10$ThgGpWLstH8WVxiqu4VwDOy.g2isCIlwfWS179IKsC0uOyEiu9eDG', '762007', 'verified', '2026-02-24 12:22:57', '2026-02-24 04:37:57', '89864dfa77883eeadea20bfb093a4490db38ed5fe9d9f9f0c6bfa9677186d313', '2026-02-25 12:23:20'),
(21, 'ventiletos12@gmail.com', '$2y$10$EYyxThSM.j9Zjn07Dtcbt.3wWdrnNT4HmyCqmmsz.JGC3AZsdh1ki', '546125', 'expired', '2026-02-24 12:36:54', '2026-02-24 04:51:54', NULL, NULL),
(22, 'ventiletos12@gmail.com', '$2y$10$c/XROkUWsoErz0.5SSyQa.pNMvfwSVAU3pXjODscCZrNOscMVDGy6', '659867', 'verified', '2026-02-24 12:39:00', '2026-02-24 04:54:00', '9ac71bceef0651930c49e7a4f27b884efd2f13cf4f62e8fee458b3ce82f077d7', '2026-02-25 12:39:17'),
(23, 'manhicshishi15@gmail.com', '$2y$10$Lp9iWD7.hGfRCQLwsLpTYuo0nTEo5e4BfHuH29DJ4g9lEte3Z6awO', '979573', 'verified', '2026-02-25 00:57:04', '2026-02-24 17:12:04', '1d1ef41747319ee33607af24681ce32efe1d86f289069fcf8895251de84b19d1', '2026-02-26 00:57:41'),
(24, 'anonuevoprincess09@gmail.com', '$2y$10$OH0AeOXROaV4zFGfZiyfCe/2/SJypW67dWDqHRkEUXQE1SnhwoFyC', '587604', 'verified', '2026-02-25 01:17:57', '2026-02-24 17:32:57', 'ceb12360decbb71ca66a11cfadba2e90decdfcf762d5d52ac1e47d264ab4e317', '2026-02-26 01:18:19'),
(25, 'jamaicavillena16@gmail.com', '$2y$10$46RS4pZWr4U04DuOqBg3LOSi8qmnrR5zu4M7k2qVCiIWyuU6/Ms66', '124301', 'verified', '2026-02-25 03:02:47', '2026-02-24 19:17:47', '4035621a7051ab84150559118ea2eceeb0a57500772cc6e1a578a0b6eb21d573', '2026-02-26 03:03:31'),
(26, 'ventiletos12@gmail.com', '$2y$10$UEH9rjr4zoP.V3/gBvMqN.mA058UNXIs/Kqzx4N5HuqOmMveKV9DK', '245679', 'verified', '2026-02-26 05:37:08', '2026-02-25 21:52:07', '053ceb2bb4c639ce437d822da620c4b93ae3e798ce40564bf3fea1c8a26286e5', '2026-02-27 05:39:13'),
(27, 'feudoclariss@gmail.com', '$2y$10$rLNFzV12S2OqPmCdYjC.nuBbeycfSorrofzNcDhCPYb1aro8JZub2', '691635', 'verified', '2026-03-04 11:43:06', '2026-03-04 03:58:06', '2ddd8a656387988e88c73627bc6fac429cdea65ca6ba9bb34143caf3a1d6522f', '2026-03-05 11:43:40'),
(28, 'patrick.james.romasanta@colegiodenaujan.edu.ph', '$2y$10$RnOd3ClNDTHfxmaaZfxhQukXg3VjSaEMe7S1bLIQDqxg4k0IVepAW', '973847', 'pending', '2026-03-08 06:28:37', '2026-03-07 22:43:37', NULL, NULL),
(29, 'monalizawaing41@gmail.com', '$2y$10$IuogBAk8XhLaCsqD9gF9QOSCvrXJvo3alFSYfNjMPY48gm6fb8ap.', '849537', 'verified', '2026-03-08 06:30:11', '2026-03-07 22:45:11', 'aa6a36e3e7b0bb9f575110cc2acfcc56d5801066a1c391a2f8652d8398c57e1b', '2026-03-09 06:31:27'),
(30, 'patrickromasanta296@gmail.com', '$2y$10$XtYjSF98qfxyZEeXuIpC.utbj6yP3npiESlW.H9tF4O.zVPugtMJO', '847857', 'expired', '2026-03-10 12:51:56', '2026-03-10 05:06:55', NULL, NULL),
(31, 'patrickromasanta296@gmail.com', '$2y$10$ITJDfiuCKt4J.Z1HcuUzG.1hvX7xMzaj/bWjMrHoWvuaro.g5uVWW', '603411', 'expired', '2026-03-10 12:53:11', '2026-03-10 05:08:11', NULL, NULL),
(32, 'patrickromasanta296@gmail.com', '$2y$10$4vFAdV8fMMmDbJIMIKvLJegcmWDaIDHKF9sUKrTBySeqlKvJXttle', '337750', 'verified', '2026-03-10 12:55:09', '2026-03-10 05:10:09', 'd49477f380eca3108747ad7092337e920c923e0857413a9b70b595403719a129', '2026-03-11 12:56:27'),
(33, 'belugaw6@gmail.com', '$2y$10$4S1udASVcMLZ1T8MiBsHdOl.Ftu8vg2Lt6aLXigsj7CDxPaTlhNRe', '786054', 'verified', '2026-03-10 22:22:04', '2026-03-10 14:37:04', '3601ede06aedd7a54ee86370f8071d7b20a7d827f2518320ad9aa54204ba0ad5', '2026-03-11 22:23:00'),
(34, 'manhicshishi15@gmail.com', '$2y$10$zCnG.oGH5cNYTb1Dhs5.1uG42E/XsiDF/ZxdZas..lWiFTEwTsAcK', '560135', 'pending', '2026-03-11 01:30:54', '2026-03-10 17:45:54', NULL, NULL),
(35, 'patrickromasanta296@gmail.com', '$2y$10$5Y.xogdvfEfZ53D32UKSEOpvyLmm71RVnTtLVwktu8JlEckybWfWq', '666458', 'verified', '2026-03-15 23:42:28', '2026-03-15 15:57:27', '77a635daebd8364c24b3485a2c10da09a60c0798f361f75e94b57a2f1b5c9f77', '2026-03-16 23:43:05'),
(36, 'patrickromasanta296@gmail.com', '$2y$10$8q7Z3NHwPS/JlpE1PKovxeDRBQqmVi2hM2E5F/754NEm.bH/dW/CG', '195501', 'verified', '2026-03-19 05:14:28', '2026-03-18 21:29:28', '8948ccb1b576db7dfebacf6edf89f5aa0d0de30a0968f928c7453a30a4877e11', '2026-03-20 05:15:09'),
(37, 'patrickromasanta296@gmail.com', '$2y$10$Ip5Mvu9c.c6glhZPMWhQjuP0HlWXhV6L4cY686xHDbXEEiGeJDT3S', '950247', 'expired', '2026-04-24 00:46:30', '2026-04-23 17:01:30', NULL, NULL),
(38, 'patrickromasanta296@gmail.com', '$2y$10$j9m5/ytcpIHoMM8cluRDIehxIvXO5Ewij.5lowDv9.P6bZKV8Qgbu', '684500', 'expired', '2026-04-24 00:48:41', '2026-04-23 17:03:40', NULL, NULL),
(39, 'patrickromasanta296@gmail.com', '$2y$10$gQNTRx743lL6nJOpAJKaIe7mvZPr/yzR.M91XYo.eVHxnXce3Hrqq', '949969', 'pending', '2026-04-24 01:46:57', '2026-04-23 18:01:57', NULL, NULL),
(40, 'lloydpc16@gmail.com', '$2y$10$D4qOWiuU1F88adiFvQafiu2/m7MnQTfKibtd3X6F2MUaxpBN/a3bi', '009523', 'verified', '2026-04-24 03:06:06', '2026-04-23 19:21:06', '3133dd9c5f1b973186c216758662d8539ddd5b223277120ca2b6dc2613dd58c4', '2026-04-25 03:06:55'),
(41, 'patrickmontero833@gmail.com', '$2y$10$XVq22xIr9.tnkhBta6IEOeVc8XmOgFacyrkZ/csoNWH/eM.3e./2S', '729860', 'verified', '2026-04-28 11:38:52', '2026-04-28 03:53:52', '952712b052069322e5587cada9dfde8bd6c6d975db5d07bf6e958cfb135f1c51', '2026-04-29 11:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedules`
--

DROP TABLE IF EXISTS `exam_schedules`;
CREATE TABLE IF NOT EXISTS `exam_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `batch_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_slots` int NOT NULL,
  `current_slots` int DEFAULT '0',
  `status` enum('active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_schedules`
--

INSERT INTO `exam_schedules` (`id`, `batch_name`, `exam_date`, `start_time`, `end_time`, `venue`, `max_slots`, `current_slots`, `status`, `created_at`, `updated_at`) VALUES
(9, 'First Batch', '2026-03-23', '05:00:00', '16:00:00', 'Gym', 10, 10, 'active', '2026-03-23 12:58:58', '2026-03-23 13:00:10'),
(10, 'Second Batch', '2026-02-04', '08:00:00', '12:00:00', 'Gym', 30, 2, 'active', '2026-04-24 03:49:41', '2026-04-24 03:51:30');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inquiry_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question` text COLLATE utf8mb4_unicode_ci,
  `inquiry_type` enum('general','admission','program','requirements','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `status` enum('open','responded','closed','new') COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  `responded_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inquiry_id` (`inquiry_id`),
  KEY `program_id` (`program_id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_inquiry_type` (`inquiry_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `inquiry_id`, `full_name`, `email`, `phone`, `program_id`, `program_name`, `question`, `inquiry_type`, `status`, `notes`, `created_at`, `updated_at`, `responded_at`, `responded_by`) VALUES
(21, 'INQ-2026-2047', 'Romasanta Patrick James Vital', 'patrickromasanta296@gmail.com', NULL, 1, NULL, NULL, 'general', 'responded', NULL, '2026-04-24 00:44:57', '2026-04-24 00:45:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_messages`
--

DROP TABLE IF EXISTS `inquiry_messages`;
CREATE TABLE IF NOT EXISTS `inquiry_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inquiry_id` int NOT NULL,
  `sender_type` enum('student','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inquiry_id` (`inquiry_id`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inquiry_messages`
--

INSERT INTO `inquiry_messages` (`id`, `inquiry_id`, `sender_type`, `message`, `is_read`, `created_at`) VALUES
(34, 21, 'student', 'hi', 1, '2026-04-24 00:44:57'),
(35, 21, 'admin', 'yoh', 1, '2026-04-24 00:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
CREATE TABLE IF NOT EXISTS `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program code (e.g., BSIS, BPA)',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Full program name',
  `short_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Short display name',
  `category` enum('4-years','technical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '4-years',
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Department name',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program description',
  `duration` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Program duration (e.g., 4 Years)',
  `units` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Total units (e.g., 158 Units)',
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to program image',
  `prospectus_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to prospectus file',
  `enrolled_students` int DEFAULT '0' COMMENT 'Number of enrolled students',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `highlights` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of program highlights',
  `career_opportunities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of career opportunities',
  `admission_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of admission requirements',
  `program_head_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teachers` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of teacher names',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `code`, `title`, `short_title`, `category`, `department`, `description`, `duration`, `units`, `image_path`, `prospectus_path`, `enrolled_students`, `status`, `highlights`, `career_opportunities`, `admission_requirements`, `program_head_name`, `teachers`, `created_at`, `updated_at`) VALUES
(1, 'BSIS', 'Bachelor of Science in Information Systems', 'BS Information Systems', '4-years', 'BSIS', 'A comprehensive program focusing on information systems analysis, design, and implementation for modern business environments.', '4 Years', '150 Units', 'assets/img/programs/bsis.jpg', '../../assets/img/programs/prospectus/BSIS-prospectus-1773880533.xlsx', 151, 'active', '[]', '[]', '[]', 'JUNE PAUL ANONUEVO', '[]', '2026-01-29 14:47:27', '2026-03-19 06:55:20'),
(2, 'BPA', 'Bachelor of Public Administration', 'BS Public Administration', '4-years', 'BPA', 'Program designed to develop competent public administrators and managers for government service and public sector organizations.', '4 Years', '140 Units', 'assets/img/programs/BPA-1769735549.jpg', '../../assets/prospectus/BPA-and-BSIS-PROSPECTUS.xlsx', 142, 'active', '[]', '[]', '[]', 'CEDRICK H. ALMAREZ', '[]', '2026-01-29 14:47:27', '2026-03-19 07:45:18'),
(3, 'BTVTED-WFT', 'BTVTED-Welding and Fabrication Technology', 'Welding Fabrication Technology', '4-years', 'WFT', 'Hands-on program providing comprehensive training in various welding techniques and fabrication processes for industrial applications.', '4 Years', '80 Units', '../../assets/img/programs/BTVTED-WFT-1773903558.jpg', '../../assets/img/programs/prospectus/BTVTED-WFT-prospectus-1772174817.xlsx', 119, 'active', '[]', '[]', '[]', 'PAMELA FAYE D. GELENA', '[]', '2026-01-29 14:47:27', '2026-03-19 07:49:20'),
(5, 'BTVTED-CHS', 'BTVTED-Computer Hardware Servicing', 'Computer Hardware Servicing', '4-years', 'BTVTED', 'Focuses on installing, maintaining, troubleshooting, and repairing computer systems and networks; while also training students in teaching methods so they can pass on these skills in schools or training centers.', '4 Years', '150 Units', '../../assets/img/programs/BTVTED-CHS-1773903522.jpg', '../../assets/img/programs/prospectus/BTVTED-CHS-prospectus-1772174799.xlsx', 128, 'active', '[]', '[]', '[]', 'PAMELA FAYE D. GELENA', '[]', '2026-02-27 06:46:39', '2026-03-19 07:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `program_heads`
--

DROP TABLE IF EXISTS `program_heads`;
CREATE TABLE IF NOT EXISTS `program_heads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Employee ID number',
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'First name',
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Middle name',
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Last name',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email address',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Phone number',
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Department assignment',
  `specialization` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Area of specialization',
  `hire_date` date NOT NULL COMMENT 'Date of hiring',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active' COMMENT 'Employment status',
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
(2, '09', 'June Paul', 'M.', 'Anonuevo', 'junepaul@colegiodenaujan.edu.ph', '', 'Information Technology', NULL, '2019-06-20', 'active', '2026-01-29 14:47:27', '2026-01-30 03:21:18'),
(3, '109', 'Cedrick', 'H.', 'Almarez', 'cedrick@colegiodenaujan.edu.ph', '', 'Public Administration', NULL, '2021-03-10', 'active', '2026-01-29 14:47:27', '2026-01-30 03:21:03'),
(4, '122', 'Faye', 'D.', 'Datinguinoo', 'Faye Datinguino@gmail.com', '', 'Technical-Vocational', NULL, '2026-01-13', 'active', '2026-01-30 00:36:01', '2026-01-30 03:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `prospectus`
--

DROP TABLE IF EXISTS `prospectus`;
CREATE TABLE IF NOT EXISTS `prospectus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `program_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_level` int DEFAULT NULL,
  `semester` int DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_prog_year_sem_sub` (`program_code`,`year_level`,`semester`,`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prospectus`
--

INSERT INTO `prospectus` (`id`, `program_code`, `year_level`, `semester`, `subject_id`, `created_at`) VALUES
(50, 'BSIS', 1, 1, 7, '2026-03-19 03:04:02'),
(51, 'BSIS', 1, 1, 8, '2026-03-19 03:04:02'),
(52, 'BSIS', 1, 1, 3, '2026-03-19 03:04:02'),
(53, 'BSIS', 1, 2, 23, '2026-03-19 03:04:02'),
(54, 'BSIS', 1, 2, 24, '2026-03-19 03:04:02'),
(55, 'BSIS', 3, 1, 10, '2026-03-19 03:04:02'),
(56, 'BSIS', 3, 1, 11, '2026-03-19 03:04:02'),
(57, 'BSIS', 3, 1, 13, '2026-03-19 03:04:02'),
(58, 'BSIS', 3, 2, 17, '2026-03-19 03:04:02'),
(59, 'BSIS', 3, 2, 19, '2026-03-19 03:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `prospectus_downloads`
--

DROP TABLE IF EXISTS `prospectus_downloads`;
CREATE TABLE IF NOT EXISTS `prospectus_downloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `program_id` int NOT NULL COMMENT 'Foreign key to programs table',
  `user_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP address of downloader',
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Browser information',
  `download_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_program_download` (`program_id`,`download_date`),
  KEY `idx_ip_download` (`user_ip`,`download_date`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prospectus_downloads`
--

INSERT INTO `prospectus_downloads` (`id`, `program_id`, `user_ip`, `user_agent`, `download_date`) VALUES
(1, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:17'),
(2, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:20'),
(3, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:23'),
(4, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:24'),
(5, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:32'),
(6, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:45:46'),
(7, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:59:33'),
(8, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:59:53'),
(9, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 03:59:58'),
(10, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 10:11:30'),
(11, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-23 14:41:09'),
(12, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-25 03:21:54'),
(13, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-25 03:22:10'),
(14, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 11:41:18'),
(15, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 11:41:40'),
(16, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-19 00:34:45'),
(17, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-19 00:36:00'),
(18, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-19 05:59:34'),
(19, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-19 06:30:27'),
(20, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-19 07:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adviser` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year_level` int NOT NULL,
  `capacity` int DEFAULT '40',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_code` (`section_code`),
  KEY `idx_section_code` (`section_code`),
  KEY `idx_department_code` (`department_code`),
  KEY `idx_year_level` (`year_level`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_code`, `section_name`, `adviser`, `department_code`, `year_level`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(17, 'BTVTED-WFT1', 'BTVTED-WFT1', NULL, 'BTVTED', 1, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(18, 'BTVTED-CHS1', 'BTVTED-CHS1', NULL, 'BTVTED', 1, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(19, 'BPA1', 'BPA1', NULL, 'BPA', 1, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(20, 'BSIS1', 'BSIS1', '', 'BSIS', 1, 40, 'active', '2026-02-23 11:20:02', '2026-04-24 03:47:04'),
(21, 'BTVTED-WFT2', 'BTVTED-WFT2', NULL, 'BTVTED', 2, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(22, 'BTVTED-CHS2', 'BTVTED-CHS2', NULL, 'BTVTED', 2, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(23, 'BPA2', 'BPA2', NULL, 'BPA', 2, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(24, 'BSIS2', 'BSIS2', NULL, 'BSIS', 2, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(25, 'BTVTED-CHS3', 'BTVTED-CHS3', NULL, 'BTVTED', 3, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(26, 'BTVTED-WFT3', 'BTVTED-WFT3', NULL, 'BTVTED', 3, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(27, 'BPA3', 'BPA3', NULL, 'BPA', 3, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(28, 'BSIS3', 'BSIS3', 'Mr. Bluejam Grieta', 'BSIS', 3, 40, 'active', '2026-02-23 11:20:02', '2026-03-19 04:31:28'),
(29, 'BTVTED-CHS4', 'BTVTED-CHS4', NULL, 'BTVTED', 4, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02'),
(30, 'BTVTED-WFT4', 'BTVTED-WFT4', NULL, 'BTVTED', 4, 40, 'active', '2026-02-23 11:20:02', '2026-02-23 11:20:02');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `date_enrolled` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `yearlevel` int DEFAULT NULL,
  `enrollment_type` enum('regular','irregular') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `status` enum('active','inactive','graduated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `address`, `birth_date`, `date_enrolled`, `gender`, `department`, `section_id`, `yearlevel`, `enrollment_type`, `status`, `remarks`, `avatar`, `created_at`, `updated_at`) VALUES
(2706, '2025-0760', 'Jerlyn', 'M.', 'Aday', 'jerlyn.aday@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2707, '2025-0812', 'Althea', 'Nicole Shane M.', 'Dudas', 'althea.dudas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2708, '2025-0631', 'Jasmine', 'H.', 'Gelena', 'jasmine.gelena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2709, '2025-0714', 'Kyla', 'M.', 'Jacob', 'kyla.jacob@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2710, '2025-0706', 'Kylyn', 'M.', 'Jacob', 'kylyn.jacob@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2711, '2025-0607', 'Amaya', 'C.', 'Mañibo', 'amaya.ma.nibo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2712, '2025-0704', 'Keana', 'G.', 'Marquinez', 'keana.marquinez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2713, '2025-0792', 'Ashley', 'A.', 'Mendoza', 'ashley.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2714, '2025-0761', 'Ana', 'Marie A.', 'Quimora', 'ana.quimora@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2715, '2025-0707', 'Camille', 'M.', 'Tordecilla', 'camille.tordecilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2716, '2025-0630', 'Jonalyn', 'H.', 'Untalan', 'jonalyn.untalan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2717, '2025-0810', 'Lyra', 'Mae M.', 'Villanueva', 'lyra.villanueva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2718, '2025-0608', 'Rhaizza', 'D.', 'Villanueva', 'rhaizza.villanueva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2719, '2025-0687', 'John', 'Philip Montillana', 'Batarlo', 'john.batarlo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(2720, '2025-0807', 'Ace', 'Romar B.', 'Castillo', 'ace.castillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2721, '2025-0773', 'John', 'Lloyd B.', 'Castillo', 'john.castillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2722, '2025-0616', 'Jericho', 'M.', 'Del Mundo', 'jericho.del.mundo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2723, '2025-0799', 'Khyn', 'C.', 'Delos Reyes', 'khyn.delos.reyes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2724, '2025-0604', 'Gian', 'Dominic Riza', 'Dudas', 'gian.dudas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2725, '2025-0703', 'Mark', 'Neil V.', 'Fajil', 'mark.fajil@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2726, '2025-0602', 'Mark', 'Angelo Riza', 'Francisco', 'mark.francisco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2727, '2025-0363', 'Jhake', 'Perillo', 'Garan', 'jhake.garan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2728, '2025-0593', 'Jared', NULL, 'Gasic', 'jared.gasic@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2729, '2025-0603', 'Bobby', 'Jr. M.', 'Godoy', 'bobby.godoy@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2730, '2025-0795', 'Edward', 'John S.', 'Holgado', 'edward.holgado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2731, '2025-0794', 'Jaypee', 'G.', 'Jacob', 'jaypee.jacob@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2732, '2025-0746', 'Jhon', 'Loyd D.', 'Macapuno', 'jhon.macapuno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2733, '2025-0672', 'Paul', 'Tristan V.', 'Madla', 'paul.madla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2734, '2025-0594', 'Marlex', 'L.', 'Mendoza', 'marlex.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2735, '2025-0649', 'Ron-Ron', NULL, 'Montero', 'ron.ron.montero@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(2736, '2025-0757', 'John', 'Lord J.', 'Moreno', 'john.moreno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2737, '2025-0686', 'Johnwin', 'A.', 'Pastor', 'johnwin.pastor@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2738, '2025-0606', 'Jhon', 'Jake', 'Perez', 'jhon.perez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2739, '2025-0692', 'John', 'Kenneth', 'Perez', 'john.perez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2740, '2025-0534', 'Khim', 'M.', 'Tejada', 'khim.tejada@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 17, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2741, '2025-0784', 'Mary', 'Ann B.', 'Asi', 'mary.asi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2742, '2025-0797', 'Marydith', 'L.', 'Atienza', 'marydith.atienza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2743, '2025-0745', 'Charisma', 'M.', 'Banila', 'charisma.banila@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2744, '2025-0658', 'Myka', 'S.', 'Braza', 'myka.braza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2745, '2025-0676', 'Rhealyne', 'C.', 'Cardona', 'rhealyne.cardona@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2746, '2025-0758', 'Danica', 'Bea T.', 'Castillo', 'danica.castillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2747, '2025-0793', 'Marra', 'Jane V.', 'Cleofe', 'marra.cleofe@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2748, '2025-0637', 'Jocelyn', 'T.', 'De Guzman', 'jocelyn.de.guzman@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2749, '2025-0790', 'Anna', 'Nicole', 'De Leon', 'anna.de.leon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2750, '2025-0778', 'Shane', 'M.', 'Dudas', 'shane.dudas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2751, '2025-0754', 'Analyn', 'M.', 'Fajardo', 'analyn.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(2752, '2025-0668', 'Zean', 'Dane A.', 'Falcutila', 'zean.falcutila@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2753, '2025-0755', 'Sharmaine', 'G.', 'Fonte', 'sharmaine.fonte@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2754, '2025-0756', 'Crystal', 'E.', 'Gagote', 'crystal.gagote@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2755, '2025-0667', 'Janel', 'M.', 'Garcia', 'janel.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2756, '2025-0800', 'Aleah', 'G.', 'Gida', 'aleah.gida@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2757, '2025-0786', 'Bhea', 'Jane Y.', 'Gillado', 'bhea.gillado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2758, '2025-0805', 'Mae', 'M.', 'Hernandez', 'mae.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2759, '2025-0656', 'Arian', 'Bello', 'Maculit', 'arian.maculit@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2760, '2025-0771', 'Mikee', 'M.', 'Manay', 'mikee.manay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2761, '2025-0763', 'Lorain', 'B .', 'Medina', 'lorain.medina@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2762, '2025-0767', 'Lovely', 'Joy A.', 'Mercado', 'lovely.mercado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2763, '2025-0772', 'Romelyn', 'M.', 'Mongcog', 'romelyn.mongcog@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2764, '2025-0699', 'Lleyn', 'Angela J.', 'Olympia', 'lleyn.olympia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2765, '2025-0766', 'Althea', 'A.', 'Paala', 'althea.paala@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2766, '2025-0770', 'Ivy', 'Kristine A.', 'Petilo', 'ivy.petilo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2767, '2025-0789', 'Irish', 'Catherine M.', 'Ramos', 'irish.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2768, '2025-0796', 'Rubilyn', 'V.', 'Roxas', 'rubilyn.roxas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(2769, '2025-0718', 'Marie', 'Bernadette S.', 'Tolentino', 'marie.tolentino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2770, '2025-0643', 'Wyncel', 'A.', 'Tolentino', 'wyncel.tolentino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2771, '2025-0629', 'Felicity', 'O.', 'Villegas', 'felicity.villegas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2772, '2025-0705', 'Danilo', 'R. Jr', 'Cabiles', 'danilo.cabiles@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2773, '2025-0726', 'Aldrin', 'L.', 'Carable', 'aldrin.carable@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2774, '2025-0743', 'Daniel', 'A.', 'Franco', 'daniel.franco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2775, '2025-0636', 'Jarred', 'L.', 'Gomez', 'jarred.gomez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2776, '2025-0785', 'Jairus', 'M.', 'Macuha', 'jairus.macuha@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2777, '2025-0801', 'Mel', 'Gabriel N.', 'Magat', 'mel.magat@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2778, '2025-0762', 'Erwin', 'M.', 'Tejedor', 'erwin.tejedor@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2779, '2025-0747', 'Brix', 'Matthew', 'Velasco', 'brix.velasco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 18, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2780, '2025-0617', 'K-Ann', 'E.', 'Abela', 'k.ann.abela@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2781, '2024-381', 'Anne', 'M.', 'Acuzar', 'anne.acuzar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2782, '2025-0733', 'Shane', 'Ashley C.', 'Abendan', 'shane.abendan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2783, '2025-0619', 'Hanna', 'N.', 'Aborde', 'hanna.aborde@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2784, '2025-0765', 'Rysa', 'Mae G.', 'Alfante', 'rysa.alfante@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(2785, '2025-0809', 'Jeny', 'M.', 'Amado', 'jeny.amado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2786, '2025-0680', 'Jonah', 'Trisha D.', 'Asi', 'jonah.asi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2787, '2025-0646', 'Jhovelyn', 'G.', 'Bacay', 'jhovelyn.bacay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2788, '2025-0679', 'Alexa', 'Jane', 'Bon', 'alexa.bon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2789, '2025-0783', 'Lorraine', 'D.', 'Bonado', 'lorraine.bonado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2790, '2025-0638', 'Shiella', 'Mae A.', 'Bonifacio', 'shiella.bonifacio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2791, '2025-0711', 'Claren', 'I.', 'Carable', 'claren.carable@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2792, '2025-0727', 'Prences', 'Angel L.', 'Consigo', 'prences.consigo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2793, '2025-0742', 'Jamhyca', 'C.', 'De Chavez', 'jamhyca.de.chavez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2794, '2025-0673', 'Nicole', 'P.', 'Defeo', 'nicole.defeo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2795, '2025-0722', 'Sophia', 'Angela M.', 'Delos Reyes', 'sophia.delos.reyes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2796, '2025-0612', 'Romelyn', NULL, 'Elida', 'romelyn.elida@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2797, '2025-0611', 'Christina', 'Sofia Lie D.', 'Enriquez', 'christina.enriquez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2798, '2025-0688', 'Elayca', 'Mae E.', 'Fajardo', 'elayca.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2799, '2025-0657', 'Ailla', 'F.', 'Fajura', 'ailla.fajura@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2800, '2025-0618', 'Judith', 'B.', 'Fallarna', 'judith.fallarna@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(2801, '2025-0654', 'Jenelyn', 'R.', 'Fonte', 'jenelyn.fonte@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2802, '2025-0713', 'Katrice', 'I.', 'Garcia', 'katrice.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2803, '2025-0737', 'Shalemar', 'M.', 'Geroleo', 'shalemar.geroleo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2804, '2025-0655', 'Edlyn', 'M.', 'Hernandez', 'edlyn.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2805, '2025-0633', 'Angela', 'T.', 'Lotho', 'angela.lotho@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2806, '2025-0808', 'Remz', 'Ann Escarlet G.', 'Macapuno', 'remz.macapuno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2807, '2025-0609', 'Leslie', 'B.', 'Melgar', 'leslie.melgar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2808, '2025-0729', 'Camille', 'B.', 'Milambiling', 'camille.milambiling@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2809, '2025-0710', 'Erica', 'Mae B.', 'Motol', 'erica.motol@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2810, '2025-0728', 'Ma.', 'Teresa S.', 'Obando', 'ma.obando@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2811, '2025-0647', 'Argel', 'B.', 'Ocampo', 'argel.ocampo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2812, '2025-0779', 'Jea', 'Francine', 'Rivera', 'jea.rivera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2813, '2025-0788', 'Ashly', 'Nicole', 'Rana', 'ashly.rana@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2814, '2025-0741', 'Aimie', 'Jane M.', 'Reyes', 'aimie.reyes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2815, '2025-0734', 'Rhenelyn', 'A.', 'Sandoval', 'rhenelyn.sandoval@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2816, '2025-0777', 'Nicole', 'S.', 'Silva', 'nicole.silva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2817, '2025-0731', 'Jeane', 'T.', 'Sulit', 'jeane.sulit@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(2818, '2025-0723', 'Pauleen', 'H.', 'Villaruel', 'pauleen.villaruel@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2819, '2025-0806', 'Megan', 'Michaela M.', 'Visaya', 'megan.visaya@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2820, '2025-0684', 'Rodel', NULL, 'Arenas', 'rodel.arenas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2821, '2025-0690', 'Rexner', 'M.', 'Eguillon', 'rexner.eguillon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2822, '2025-0815', 'Reymart', 'P.', 'Elmido', 'reymart.elmido@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2823, '2025-0627', 'Kervin', 'B.', 'Garachico', 'kervin.garachico@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2824, '2025-0865', 'Zyris', 'A.', 'Guavez', 'zyris.guavez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2825, '2025-0740', 'Marjun', 'A', 'Linayao', 'marjun.linayao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2826, '2025-0660', 'John', 'Lloyd', 'Macapuno', 'john.macapuno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2827, '2025-0732', 'Helbert', 'F.', 'Maulion', 'helbert.maulion@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2828, '2025-0645', 'Dindo', 'S.', 'Tolentino', 'dindo.tolentino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 19, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2829, '2025-0621', 'Novelyn', 'D.', 'Albufera', 'novelyn.albufera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2830, '2025-0775', 'Angela', 'F.', 'Aldea', 'angela.aldea@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2831, '2025-0601', 'Maria', 'Fe C.', 'Aldovino', 'maria.aldovino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2832, '2025-0661', 'Aizel', 'M.', 'Alvarez', 'aizel.alvarez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2833, '2025-0752', 'Sherilyn', 'T.', 'Anyayahan', 'sherilyn.anyayahan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(2834, '2025-0623', 'Mika', 'Dean', 'Buadilla', 'mika.buadilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2835, '2025-0669', 'Daniela', 'Faye', 'Cabiles', 'daniela.cabiles@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2836, '2025-0599', 'Prinses', 'Gabriela Q.', 'Calaolao', 'prinses.calaolao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2837, '2025-0719', 'Deah', 'Angella S', 'Carpo', 'deah.carpo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2838, '2025-0802', 'Jedidiah', 'C.', 'Gelena', 'jedidiah.gelena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2839, '2025-0664', 'Aleyah', 'Janelle B.', 'Jara', 'aleyah.jara@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2840, '2025-0720', 'Charese', 'M.', 'Jolo', 'charese.jolo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2841, '2025-0682', 'Janice', 'G.', 'Lugatic', 'janice.lugatic@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2842, '2025-0739', 'Abegail', NULL, 'Malogueño', 'abegail.malogue.no@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2843, '2025-0708', 'Ericca', 'A.', 'Marquez', 'ericca.marquez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2844, '2025-0748', 'Arien', 'M.', 'Montesa', 'arien.montesa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2845, '2025-0653', 'Jasmine', 'Q.', 'Nuestro', 'jasmine.nuestro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2846, '2025-0738', 'Nicole', 'G.', 'Ola', 'nicole.ola@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2847, '2025-0628', 'Alyssa', 'Mae M.', 'Quintia', 'alyssa.quintia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2848, '2025-0774', 'Jona', 'Marie G.', 'Romero', 'jona.romero@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2849, '2025-0634', 'Marbhel', 'H.', 'Rucio', 'marbhel.rucio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(2850, '2025-0814', 'Lovely', 'K.', 'Torres', 'lovely.torres@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2851, '2025-0620', 'Rexon', 'E.', 'Abanilla', 'rexon.abanilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2852, '2025-0791', 'Ramfel', 'H.', 'Azucena', 'ramfel.azucena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2853, '2025-0632', 'Jeverson', 'M.', 'Bersoto', 'jeverson.bersoto@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2854, '2025-0626', 'Shervin', 'Jeral M.', 'Castro', 'shervin.castro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2855, '2025-0652', 'Daniel', 'D.', 'De Ade', 'daniel.de.ade@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2856, '2025-0782', 'Dave', 'Ruzzele D.', 'Despa', 'dave.despa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2857, '2025-0696', 'Alexander', 'R.', 'Ducado', 'alexander.ducado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2858, '2025-0595', 'Uranus', 'R.', 'Evangelista', 'uranus.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2859, '2025-0697', 'Joshua', 'M.', 'Gabon', 'joshua.gabon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2860, '2025-0681', 'John', 'Andrew R.', 'Gavilan', 'john.gavilan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2861, '2025-0715', 'Mc', 'Lenard A.', 'Gibo', 'mc.gibo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2862, '2025-0716', 'Dan', 'Kian A.', 'Hatulan', 'dan.hatulan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2863, '2025-0803', 'Benjamin', 'Jr. D', 'Hernandez', 'benjamin.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2864, '2025-0753', 'Renz', 'F.', 'Hernandez', 'renz.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2865, '2025-0662', 'Ralph', 'Adriane D.', 'Javier', 'ralph.javier@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2866, '2025-0598', 'Andrew', 'M.', 'Laredo', 'andrew.laredo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(2867, '2025-0663', 'Janryx', 'S.', 'Las Pinas', 'janryx.las.pinas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2868, '2025-0735', 'Bricks', 'M.', 'Lindero', 'bricks.lindero@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2869, '2025-0639', 'Luigi', 'B.', 'Lomio', 'luigi.lomio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2870, '2025-0596', 'John', 'Lemuel O.', 'Macalindol', 'john.macalindol@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2871, '2025-0781', 'Jandy', 'S.', 'Macapuno', 'jandy.macapuno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2872, '2025-0693', 'Cedrick', 'M.', 'Mandia', 'cedrick.mandia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2873, '2025-0650', 'Eric', 'John C.', 'Marinduque', 'eric.marinduque@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2874, '2025-0730', 'Jimrex', 'M.', 'Mayano', 'jimrex.mayano@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2875, '2025-0624', 'Hedyen', 'C.', 'Mendoza', 'hedyen.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2876, '2025-0625', 'Mark', 'Angelo E.', 'Montevirgen', 'mark.montevirgen@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2877, '2025-0651', 'JM', 'B.', 'Nas', 'jm.nas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2878, '2025-0725', 'Vhon', 'Jerick O', 'Ornos', 'vhon.ornos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2879, '2025-0659', 'Carl', 'Justine D.', 'Padua', 'carl.padua@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2880, '2025-0600', 'Patrick', 'Lanz', 'Paz', 'patrick.paz@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2881, '2025-0622', 'Mark', 'Justin C.', 'Pecolados', 'mark.pecolados@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2882, '2025-0764', 'Tristan', 'Jay M.', 'Plata', 'tristan.plata@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(2883, '2025-0776', 'Jude', 'Michael', 'Somera', 'jude.somera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2884, '2025-0695', 'Philip', 'Jhon N.', 'Tabor', 'philip.tabor@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2885, '2025-0597', 'Ivan', 'Lester D.', 'Ylagan', 'ivan.ylagan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 20, 1, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2886, '2024-0513', 'Kiana', 'Jane P.', 'Añonuevo', 'kiana.a.nonuevo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2887, '2024-0514', 'Kyla', NULL, 'Anonuevo', 'kyla.anonuevo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2888, '2024-0569', 'Katrice', 'F.', 'Antipasado', 'katrice.antipasado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2889, '2024-0591', 'Regine', NULL, 'Antipasado', 'regine.antipasado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2890, '2024-0550', 'Juneth', 'H.', 'Baliday', 'juneth.baliday@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2891, '2024-0546', 'Gielysa', 'C.', 'Concha', 'gielysa.concha@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2892, '2024-0506', 'Maecelle', 'V.', 'Fiedalan', 'maecelle.fiedalan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2893, '2024-0508', 'Lara', 'Mae E.', 'Garcia', 'lara.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2894, '2024-0459', 'Jade', 'S.', 'Garing', 'jade.garing@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2895, '2024-0446', 'Rica', 'D.', 'Glodo', 'rica.glodo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2896, '2024-0549', 'Danica', 'Mae N.', 'Hornilla', 'danica.hornilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2897, '2024-0473', 'Jenny', 'F.', 'Idea', 'jenny.idea@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2898, '2024-452', 'Kimberly', 'Joy C.', 'Illut', 'kimberly.illut@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2899, '2024-0487', 'Roma', 'L.', 'Mendoza', 'roma.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(2900, '2024-0535', 'Evangeline', 'V.', 'Mojica', 'evangeline.mojica@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2901, '2024-0570', 'Carla', 'G.', 'Nineria', 'carla.nineria@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2902, '2024-0516', 'Kyla', 'G.', 'Oliveria', 'kyla.oliveria@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2903, '2024-0457', 'Mikayla', 'M.', 'Paala', 'mikayla.paala@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2904, '2024-0442', 'Necilyn', 'B.', 'Ramos', 'necilyn.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2905, '2024-0469', 'Mischell', 'U.', 'Velasquez', 'mischell.velasquez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2906, '2024-0539', 'Emerson', 'M.', 'Adarlo', 'emerson.adarlo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2907, '2024-0491', 'Shim', 'Andrian L.', 'Adarlo', 'shim.adarlo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2908, '2024-0485', 'Cedrick', 'C.', 'Cardova', 'cedrick.cardova@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2909, '2024-0477', 'John', 'Paul M.', 'De Lemos', 'john.de.lemos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2910, '2024-0489', 'Reymar', 'G.', 'Faeldonia', 'reymar.faeldonia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2911, '2024-0500', 'John', 'Ray A.', 'Fegidero', 'john.fegidero@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2912, '2024-0488', 'John', 'Lester C.', 'Gaba', 'john.gaba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2913, '2024-0475', 'Antonio', 'Gabriel A.', 'Francisco', 'antonio.francisco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2914, '2024-0345', 'karl', 'Andrew R.', 'Hardin', 'karl.hardin@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2915, '2024-0499', 'Prince', 'L.', 'Geneta', 'prince.geneta@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(2916, '2024-0495', 'John', 'Reign A.', 'Laredo', 'john.laredo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2917, '2024-0490', 'Mc', 'Ryan', 'Masangkay', 'mc.masangkay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2918, '2025-0592', 'Aaron', 'Vincent R.', 'Manalo', 'aaron.manalo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2919, '2024-0494', 'Great', 'B.', 'Mendoza', 'great.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2920, '2024-0497', 'Jhon', 'Marc D.', 'Oliveria', 'jhon.oliveria@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2921, '2024-0455', 'Kevin', 'G.', 'Rucio', 'kevin.rucio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2922, '2024-483', 'Jhon', 'Loid B.', 'Reyes', 'jhon.reyes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 21, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2923, '2024-0445', 'Arhizza', 'Sheena R.', 'Abanilla', 'arhizza.abanilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2924, '2024-0503', 'Carla', 'Andrea C.', 'Azucena', 'carla.azucena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2925, '2024-0548', 'Angel', 'D.', 'Cason', 'angel.cason@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2926, '2024-0461', 'KC', 'May A.', 'De Guzman', 'kc.de.guzman@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2927, '2024-0531', 'Francene', NULL, 'Delos Santos', 'francene.delos.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2928, '2024-0470', 'Shane', 'Ayessa L.', 'Elio', 'shane.elio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2929, '2024-0502', 'Maria', 'Angela B.', 'Garcia', 'maria.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2930, '2024-0466', 'Shane', 'Mary C.', 'Gardoce', 'shane.gardoce@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2931, '2024-0441', 'Janah', 'M.', 'Glor', 'janah.glor@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(2932, '2024-0476', 'Catherine', 'R.', 'Gomez', 'catherine.gomez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2933, '2024-0554', 'April', 'Joy', 'Llamoso', 'april.llamoso@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2934, '2024-0440', 'Irene', 'Y.', 'Loto', 'irene.loto@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2935, '2024-0463', 'Angela', 'M.', 'Lumanglas', 'angela.lumanglas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2936, '2024-0464', 'Michelle', 'Micah M.', 'Lumanglas', 'michelle.lumanglas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2937, '2024-0545', 'Febelyn', 'M.', 'Magboo', 'febelyn.magboo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29');
INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `address`, `birth_date`, `date_enrolled`, `gender`, `department`, `section_id`, `yearlevel`, `enrollment_type`, `status`, `remarks`, `avatar`, `created_at`, `updated_at`) VALUES
(2938, '2024-0458', 'Chelo', 'Rose P.', 'Marasigan', 'chelo.marasigan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2939, '2024-0456', 'Joana', 'Marie L.', 'Paala', 'joana.paala@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2940, '2024-0538', 'Maria', 'Irene T.', 'Pasado', 'maria.pasado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2941, '2024-0563', 'Danica', NULL, 'Pederio', 'danica.pederio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2942, '2024-0444', 'Angela', 'Clariss P.', 'Teves', 'angela.teves@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2943, '2024-0454', 'Zairene', 'R.', 'Undaloc', 'zairene.undaloc@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2944, '2024-0449', 'John', 'Ivan P.', 'Cuasay', 'john.cuasay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2945, '2024-0505', 'Bert', 'B.', 'Ferrera', 'bert.ferrera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2946, '2024-0450', 'Rickson', 'C.', 'Ferry', 'rickson.ferry@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2947, '2024-0555', 'John', 'Mariol L.', 'Fransisco', 'john.fransisco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2948, '2024-0530', 'Allan', 'Y.', 'Loto', 'allan.loto@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(2949, '2024-0401', 'Jhon', 'Kenneth S.', 'Obando', 'jhon.obando@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2950, '2024-0462', 'Rodel', 'T.', 'Roldan', 'rodel.roldan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 22, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2951, '2024-0358', 'Ashlyn', 'Kieth V.', 'Abanilla', 'ashlyn.abanilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2952, '2024-0352', 'Patricia', 'Mae M.', 'Agoncillo', 'patricia.agoncillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2953, '2024-0378', 'Benelyn', 'D.', 'Aguho', 'benelyn.aguho@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2954, '2024-0504', 'Lynse', 'C.', 'Albufera', 'lynse.albufera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2955, '2024-0521', 'Lara', 'Mae M.', 'Altamia', 'lara.altamia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2956, '2024-0379', 'Crislyn', 'M.', 'Anyayahan', 'crislyn.anyayahan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2957, '2024-0360', 'Rocel', 'Liegh L.', 'Arañez', 'rocel.ara.nez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2958, '2024-0372', 'Katrice', 'Allaine A.', 'Atienza', 'katrice.atienza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2959, '2024-0354', 'Maica', 'C.', 'Bacal', 'maica.bacal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2960, '2024-0347', 'Cherylyn', 'C.', 'Bacsa', 'cherylyn.bacsa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2961, '2024-0364', 'Realyn', 'M.', 'Bercasi', 'realyn.bercasi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2962, '2024-0355', 'Elyza', 'M.', 'Buquis', 'elyza.buquis@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2963, '2024-0474', 'Kim', 'Ashley Nicole M.', 'Caringal', 'kim.caringal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2964, '2024-0351', 'Shane', 'B.', 'Dalisay', 'shane.dalisay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(2965, '2024-0369', 'Mariel', 'V.', 'Delos Santos', 'mariel.delos.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2966, '2024-0520', 'Angel', 'G.', 'Dimoampo', 'angel.dimoampo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2967, '2024-0374', 'Kristine', 'B.', 'Dris', 'kristine.dris@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2968, '2024-0367', 'Rexlyn', 'Joy M.', 'Eguillon', 'rexlyn.eguillon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2969, '2024-0363', 'Maricar', 'A.', 'Evangelista', 'maricar.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2970, '2024-0388', 'Chariz', 'M.', 'Fajardo', 'chariz.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2971, '2024-0366', 'Hazel', 'Ann B.', 'Feudo', 'hazel.feudo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2972, '2024-0385', 'Marie', 'Joy C.', 'Gado', 'marie.gado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2973, '2024-0371', 'Leah', 'M.', 'Galit', 'leah.galit@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2974, '2024-0507', 'Aiexa', 'Danielle A.', 'Guira', 'aiexa.guira@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2975, '2024-0375', 'Andrea', 'Mae M.', 'Hernandez', 'andrea.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2976, '2024-0501', 'Eslley', 'Ann T.', 'Hernandez', 'eslley.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2977, '2024-0376', 'Jazleen', NULL, 'Llamoso', 'jazleen.llamoso@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2978, '2024-0368', 'Joan', 'Kate G.', 'Lomio', 'joan.lomio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2979, '2024-0391', 'Kriselle', 'Ann T.', 'Mabuti', 'kriselle.mabuti@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2980, '2024-0387', 'Angel', 'Rose S.', 'Mascarinas', 'angel.mascarinas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(2981, '2024-0587', 'Hannah', 'A.', 'Melgar', 'hannah.melgar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2982, '2024-0586', 'Rexy', 'Mae D.', 'Mingo', 'rexy.mingo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2983, '2024-0349', 'Precious', 'Nicole N.', 'Moya', 'precious.moya@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2984, '2024-0377', 'Cherese', 'Gelyn C.', 'Nao', 'cherese.nao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2985, '2024-0384', 'Margie', 'N.', 'Nuñez', 'margie.nu.nez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2986, '2024-0350', 'Hazel', 'Ann F.', 'Panganiban', 'hazel.panganiban@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2987, '2024-0568', 'Angela', NULL, 'Papasin', 'angela.papasin@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2988, '2024-0359', 'Jasmine', 'A.', 'Prangue', 'jasmine.prangue@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2989, '2024-0380', 'Jeyzelle', 'G.', 'Rellora', 'jeyzelle.rellora@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2990, '2024-0264', 'Katrina', 'T', 'Rufino', 'katrina.rufino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2991, '2024-0382', 'Niña', 'Zyrene R.', 'Sanchez', 'ni.na.sanchez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2992, '2024-0509', 'Edcel', 'Jane B.', 'Santillan', 'edcel.santillan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2993, '2024-0451', 'Mary', 'Joy M.', 'Sara', 'mary.sara@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2994, '2024-0453', 'Cynthia', NULL, 'Torres', 'cynthia.torres@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2995, '2024-0556', 'Jolie', 'L.', 'Tugmin', 'jolie.tugmin@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2996, '2024-0356', 'Lesley', 'Ann M.', 'Villanueva', 'lesley.villanueva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2997, '2024-0365', 'Lany', 'G.', 'Ylagan', 'lany.ylagan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(2998, '2024-0373', 'Marvin', 'M.', 'Caraig', 'marvin.caraig@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(2999, '2024-0557', 'Denniel', 'C.', 'Delos Santos', 'denniel.delos.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3000, '2024-0389', 'Alex', 'T.', 'Magsisi', 'alex.magsisi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3001, '2024-0525', 'Jan', 'Carlo G.', 'Manalo', 'jan.manalo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3002, '2024-0386', 'AJ', 'M.', 'Masangkay', 'aj.masangkay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3003, '2024-0480', 'John', 'Paul M.', 'Roldan', 'john.roldan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3004, '2024-0523', 'Ronald', NULL, 'Tañada', 'ronald.ta.nada@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3005, '2024-0492', 'D-Jay', 'G.', 'Teriompo', 'd.jay.teriompo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 23, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3006, '2025-0816', 'Marsha', 'Lhee G.', 'Azucena', 'marsha.azucena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3007, '2024-0438', 'Melsan', 'G.', 'Aday', 'melsan.aday@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3008, '2024-0405', 'Jonice', 'P.', 'Alturas', 'jonice.alturas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3009, '2024-0411', 'Precious', 'S.', 'Apil', 'precious.apil@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3010, '2024-0418', 'Ludelyn', 'T.', 'Belbes', 'ludelyn.belbes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3011, '2024-0424', 'Princess', 'Hazel D.', 'Cabasi', 'princess.cabasi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3012, '2024-0342', 'Charlaine', 'M.', 'De Belen', 'charlaine.de.belen@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3013, '2024-0437', 'Arjean', 'Joy S.', 'De Castro', 'arjean.de.castro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(3014, '2024-0343', 'Precious', 'Cindy G.', 'De Guzman', 'precious.de.guzman@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3015, '2024-0404', 'Marina', 'M.', 'De Luzon', 'marina.de.luzon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3016, '2024-0417', 'Nesvita', 'V.', 'Dorias', 'nesvita.dorias@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3017, '2024-0432', 'Stella', 'Rey A.', 'Flores', 'stella.flores@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3018, '2024-0567', 'Arlene', 'S.', 'Gaba', 'arlene.gaba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3019, '2024-0422', 'Jay-Ann', 'G.', 'Jamilla', 'jay.ann.jamilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3020, '2024-0416', 'Mikaela', 'Joy M.', 'Layson', 'mikaela.layson@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3021, '2024-0427', 'Christine', 'Joy A.', 'Lomio', 'christine.lomio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3022, '2024-0544', 'Ariane', 'M.', 'Magboo', 'ariane.magboo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3023, '2024-0415', 'Nerissa', 'R.', 'Magsisi', 'nerissa.magsisi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3024, '2024-0472', 'Keycel', 'Joy M.', 'Manalo', 'keycel.manalo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3025, '2024-0412', 'Grace', 'Cell G.', 'Manibo', 'grace.manibo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3026, '2024-0571', 'Lovelyn', 'A.', 'Marcos', 'lovelyn.marcos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3027, '2024-0314', 'Shenna', 'Marie P.', 'Obando', 'shenna.obando@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3028, '2024-0348', 'Myzell', 'U.', 'Ramos', 'myzell.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3029, '2024-0582', 'Shella', 'Mae T.', 'Ramos', 'shella.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3030, '2024-0426', 'Desiree', 'G.', 'Raymundo', 'desiree.raymundo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(3031, '2023-0433', 'Romelyn', 'A.', 'Rocha', 'romelyn.rocha@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3032, '2023-0519', 'John', 'Michael', 'Bacsa', 'john.bacsa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3033, '2024-0043', 'John', 'Kenneth Joseph G.', 'Balansag', 'john.balansag@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3034, '2024-0398', 'Raphael', 'M.', 'Bugayong', 'raphael.bugayong@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3035, '2024-0572', 'Mark', 'Jayson D.', 'Bunag', 'mark.bunag@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3036, '2024-0561', 'Alvin', 'M.', 'Corona', 'alvin.corona@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3037, '2023-0407', 'Mark', 'Janssen C.', 'Cueto', 'mark.cueto@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3038, '2023-0447', 'Charles', 'Darwin S.', 'Dimailig', 'charles.dimailig@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3039, '2024-0413', 'Airon', 'R.', 'Evangelista', 'airon.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3040, '2024-0517', 'Gino', 'L.', 'Genabe', 'gino.genabe@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3041, '2024-0420', 'Miklo', 'M.', 'Lumanglas', 'miklo.lumanglas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3042, '2023-0151', 'Ramcil', 'M.', 'Macapuno', 'ramcil.macapuno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3043, '2024-0395', 'Florence', 'R.', 'Macalelong', 'florence.macalelong@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3044, '2023-0465', 'Patrick', 'T.', 'Matanguihan', 'patrick.matanguihan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3045, '2024-0478', 'Dranzel', 'L.', 'Miranda', 'dranzel.miranda@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3046, '2024-0394', 'Carlo', 'G.', 'Mondragon', 'carlo.mondragon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(3047, '2024-0410', 'John', 'Rexcel E.', 'Montianto', 'john.montianto@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3048, '2024-0428', 'Christian', 'M.', 'Moreno', 'christian.moreno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3049, '2024-0393', 'Amiel', 'Geronne M.', 'Pantua', 'amiel.pantua@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3050, '2024-0392', 'James', 'Lorence C.', 'Paradijas', 'james.paradijas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3051, '2024-0436', 'Jhezreel', 'P.', 'Pastorfide', 'jhezreel.pastorfide@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3052, '2024-0578', 'Matt', 'Raphael G.', 'Reyes', 'matt.reyes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3053, '2024-0580', 'Merwin', 'D.', 'Santos', 'merwin.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3054, '2024-0423', 'Benjamin', 'Jr. D.', 'Sarvida', 'benjamin.sarvida@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3055, '2024-0408', 'Jerus', 'B.', 'Savariz', 'jerus.savariz@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3056, '2024-0406', 'Gerson', 'C.', 'Urdanza', 'gerson.urdanza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3057, '2024-0397', 'Jyrus', 'M.', 'Ylagan', 'jyrus.ylagan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 24, 2, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3058, '2023-0304', 'Jonah', 'Rhyza N.', 'Anyayahan', 'jonah.anyayahan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3059, '2023-0337', 'Leica', 'M.', 'Banila', 'leica.banila@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3060, '2023-0327', 'Juvylyn', 'G.', 'Basa', 'juvylyn.basa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3061, '2022-0088', 'Rashele', 'M.', 'Delgaco', 'rashele.delgaco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, NULL, NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3062, '2023-0288', 'Cristal', 'Jean D.', 'De Chusa', 'cristal.de.chusa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(3063, '2023-0305', 'Jaime', 'Elizabeth L.', 'Evora', 'jaime.evora@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3064, '2023-0317', 'Jeanlyn', 'B.', 'Garcia', 'jeanlyn.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3065, '2023-0161', 'Baby', 'Anh Marie M.', 'Godoy', 'baby.godoy@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3066, '2023-0169', 'Herjane', 'F.', 'Gozar', 'herjane.gozar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3067, '2023-0200', 'Zyra', 'M.', 'Gutierrez', 'zyra.gutierrez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3068, '2023-0251', 'Angielene', 'C.', 'Landicho', 'angielene.landicho@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3069, '2023-0298', 'Laila', 'A.', 'Limun', 'laila.limun@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3070, '2023-0244', 'Jennie', 'Vee P.', 'Lopez', 'jennie.lopez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3071, '2023-0215', 'Judy', 'Ann M.', 'Madrigal', 'judy.madrigal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3072, '2023-0285', 'Maan', 'M.', 'Masangkay', 'maan.masangkay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3073, '2023-0225', 'Genesis', 'Mae M.', 'Mendoza', 'genesis.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3074, '2023-0224', 'Marian', 'L.', 'Mendoza', 'marian.mendoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3075, '2023-0173', 'Lailin', 'S.', 'Obando', 'lailin.obando@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3076, '2023-0303', 'Kyla', 'G.', 'Rucio', 'kyla.rucio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3077, '2023-0241', 'Lyn', 'C.', 'Velasquez', 'lyn.velasquez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3078, '2023-0336', 'Jhon', 'Jerald P.', 'Acojedo', 'jhon.acojedo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3079, '2023-0345', 'Sherwin', 'T.', 'Calibot', 'sherwin.calibot@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(3080, '2023-0233', 'Joriz', 'Cezar M.', 'Collado', 'joriz.collado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3081, '2023-1080', 'Mark', 'Lee C.', 'Dalay', 'mark.dalay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3082, '2023-0239', 'Adrian', 'C.', 'Dilao', 'adrian.dilao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3083, '2023-0167', 'Mc', 'Lowell F.', 'Fabellon', 'mc.fabellon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3084, '2023-0177', 'John', 'Paul M.', 'Fernandez', 'john.fernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3085, '2023-0249', 'Mark', 'Lyndon L.', 'Fransisco', 'mark.fransisco@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3086, '2023-0243', 'Kian', 'Vash N.', 'Gale', 'kian.gale@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3087, '2023-0332', 'Michael', 'B.', 'Magat', 'michael.magat@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3088, '2023-0308', 'John', 'Khim J.', 'Moreno', 'john.k.moreno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3089, '2023-0255', 'Jayson', 'A.', 'Ramos', 'jayson.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3090, '2023-0322', 'Joel', 'B.', 'Villena', 'joel.villena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 25, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3091, '2023-0248', 'Jazzle', 'Irish M.', 'Cudiamat', 'jazzle.cudiamat@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3092, '2023-0240', 'Jenny', 'M.', 'Fajardo', 'jenny.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3093, '2023-0299', 'Mary', 'Joy D.', 'Sim', 'mary.sim@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3094, '2023-0309', 'Jordan', 'V.', 'Abeleda', 'jordan.abeleda@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3095, '2023-0150', 'Ralf', 'Jenvher V.', 'Atienza', 'ralf.atienza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(3096, '2023-0284', 'Mon', 'Andrei M.', 'Bae', 'mon.bae@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3097, '2023-0261', 'John', 'Mark M.', 'Balmes', 'john.balmes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3098, '2023-0209', 'John', 'Russel G.', 'Bolaños', 'john.bola.nos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3099, '2023-0166', 'Justine', 'James A.', 'Dela Cruz', 'justine.dela.cruz@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3100, '2023-0313', 'Carl', 'John M.', 'Evangelista', 'carl.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3101, '2023-0274', 'Mon', 'Lester B.', 'Faner', 'mon.faner@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3102, '2023-0159', 'John', 'Paul', 'Freyra', 'john.freyra@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3103, '2023-0258', 'Ryan', 'I.', 'Garcia', 'ryan.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3104, '2023-0223', 'Jeshler', 'Clifford M.', 'Gervacio', 'jeshler.gervacio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3105, '2023-0333', 'Melvic', 'John A.', 'Magsino', 'melvic.magsino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3106, '2023-0213', 'Jerome', 'B.', 'Mauro', 'jerome.mauro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3107, '2023-0279', 'Jundell', 'M.', 'Morales', 'jundell.morales@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3108, '2023-0171', 'Adrian', 'R.', 'Pampilo', 'adrian.pampilo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3109, '2023-0300', 'John', 'Carl C.', 'Pedragoza', 'john.pedragoza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3110, '2023-0295', 'King', 'C.', 'Saranillo', 'king.saranillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3111, '2023-0260', 'Jhon', 'Laurence D.', 'Victoriano', 'jhon.victoriano@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 26, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3112, '2023-0210', 'Janelle', 'R.', 'Absin', 'janelle.absin@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(3113, '2023-0188', 'Jan', 'Ashley R.', 'Bonado', 'jan.bonado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3114, '2023-0202', 'Robelyn', 'D.', 'Bonado', 'robelyn.bonado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3115, '2023-0253', 'Princes', 'A.', 'Capote', 'princes.capote@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3116, '2023-0228', 'Joann', 'M.', 'Carandan', 'joann.carandan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3117, '2023-0272', 'Christine', 'Rose F.', 'Catapang', 'christine.catapang@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3118, '2023-0192', 'Arlyn', 'P.', 'Corona', 'arlyn.corona@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3119, '2023-0185', 'Stacy', 'Anne G.', 'Cortez', 'stacy.cortez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3120, '2023-0199', 'De', 'Claro Alexa Jane', 'C.', 'de.c@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3121, '2023-0266', 'Angel', 'Ann M.', 'De Lara', 'angel.de.lara@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3122, '2023-0172', 'Lorebel', 'A.', 'De Leon', 'lorebel.de.leon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3123, '2023-0257', 'Rocelyn', 'P.', 'Dela Rosa', 'rocelyn.dela.rosa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3124, '2023-0256', 'Ronalyn', 'Paulita', 'Dela Rosa', 'ronalyn.dela.rosa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3125, '2023-0137', 'Krisnah', 'Joy V.', 'Dorias', 'krisnah.dorias@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3126, '2023-0287', 'Ayessa', 'Jhoy M.', 'Gaba', 'ayessa.gaba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3127, '2023-0193', 'Margie', 'R.', 'Gatilo', 'margie.gatilo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3128, '2023-0296', 'Jasmine', 'C.', 'Gayao', 'jasmine.gayao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(3129, '2023-0197', 'Mikaela', 'M', 'Hernandez', 'mikaela.hernandez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3130, '2023-0189', 'Vanessa', 'Nicole', 'Latoga', 'vanessa.latoga@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3131, '2023-0262', 'Alwena', 'A.', 'Madrigal', 'alwena.madrigal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3132, '2023-0191', 'Maria', 'Eliza T.', 'Magsisi', 'maria.magsisi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3133, '2023-0227', 'Carla', 'Joy L.', 'Matira', 'carla.matira@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3134, '2023-0163', 'Allysa', 'Mae A.', 'Mirasol', 'allysa.mirasol@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3135, '2023-0247', 'Manilyn', 'G.', 'Narca', 'manilyn.narca@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3136, '2023-0211', 'Sharah', 'Mae P.', 'Ojales', 'sharah.ojales@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3137, '2023-0340', 'Geselle', 'C.', 'Rivas', 'geselle.rivas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3138, '2023-0184', 'Angel', 'Joy A.', 'Sanchez', 'angel.sanchez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3139, '2023-0341', 'Jamaica', 'Rose M.', 'Sarabia', 'jamaica.sarabia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3140, '2023-0194', 'Nicole', 'A.', 'Villafranca', 'nicole.villafranca@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3141, '2023-0203', 'Jennylyn', 'T.', 'Villanueva', 'jennylyn.villanueva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3142, '2023-0277', 'John', 'Lloyd David M.', 'Amido', 'john.amido@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3143, '2023-0290', 'Reniel', 'L.', 'Borja', 'reniel.borja@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3144, '2023-0179', 'John', 'Carlo G.', 'Chiquito', 'john.chiquito@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(3145, '2023-0301', 'Justin', 'S.', 'Como', 'justin.como@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3146, '2023-0236', 'Moises', 'G.', 'Delos Santos', 'moises.delos.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3147, '2023-0226', 'Philip', 'F.', 'Garcia', 'philip.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3148, '2023-0182', 'Bryan', 'A.', 'Peñaescosa', 'bryan.pe.naescosa@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3149, '2023-0297', 'John', 'Rick F.', 'Ramos', 'john.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BPA', 27, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3150, '2023-0220', 'Rezlyn', 'Jhoy S.', 'Aguba', 'rezlyn.aguba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3151, '2023-0153', 'Lyzel', 'G.', 'Bool', 'lyzel.bool@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3152, '2023-0219', 'Jesca', 'Mae M.', 'Chavez', 'jesca.chavez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3153, '2023-0270', 'Hiedie', 'H.', 'Claus', 'hiedie.claus@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3154, '2023-0155', 'KC', 'D.', 'Dela Roca', 'kc.dela.roca@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3155, '2023-0154', 'Bea', 'A.', 'Fajardo', 'bea.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3156, '2023-0320', 'Sherlyn', NULL, 'Festin', 'sherlyn.festin@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3157, '2023-0204', 'Clarissa', 'B.', 'Feudo', 'clarissa.feudo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3158, '2023-0156', 'Irish', 'Karyl G.', 'Magcamit', 'irish.magcamit@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3159, '2023-0216', 'Cristine', 'S.', 'Manalo', 'cristine.manalo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3160, '2023-0331', 'Geraldine', 'G.', 'Manalo', 'geraldine.manalo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3161, '2023-0198', 'Shiloh', 'G.', 'Manhic', 'shiloh.manhic@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(3162, '2023-0242', 'Shylyn', NULL, 'Mansalapus', 'shylyn.mansalapus@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3163, '2023-0291', 'Irish', 'May Roselle C.', 'Nao', 'irish.nao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3164, '2023-0208', 'Paulyn', 'Grace', 'Perez', 'paulyn.perez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3165, '2023-0181', 'Shane', 'T.', 'Ramos', 'shane.ramos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3166, '2023-0566', 'Andrea', 'Chel D.', 'Rivera', 'andrea.rivera@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3167, '2023-0344', 'Angel', 'Bellie G.', 'Vargas', 'angel.vargas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43');
INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `phone`, `address`, `birth_date`, `date_enrolled`, `gender`, `department`, `section_id`, `yearlevel`, `enrollment_type`, `status`, `remarks`, `avatar`, `created_at`, `updated_at`) VALUES
(3168, '2023-0221', 'Jamaica', 'Mickaela Y.', 'Villena', 'jamaica.villena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3169, '2023-0268', 'Monaliza', 'F.', 'Waing', 'monaliza.waing@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3170, '2023-0157', 'Jay', 'T.', 'Aguilar', 'jay.aguilar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3171, '2023-0263', 'Ken', 'Celwyn R.', 'Algaba', 'ken.algaba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3172, '2023-0273', 'Mark', 'Lester M.', 'Baes', 'mark.baes@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3173, '2023-0293', 'John', 'Albert C.', 'Bastida', 'john.bastida@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3174, '2025-818', 'Bryan', 'S.', 'Caguete', 'bryan.caguete@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3175, '2023-0218', 'Vitoel', 'G.', 'Curatcha', 'vitoel.curatcha@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3176, '2023-0286', 'Karl', 'Marion R.', 'De Leon', 'karl.de.leon@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3177, '2023-0212', 'Renzie', 'Carl C.', 'Escaro', 'renzie.escaro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(3178, '2023-0196', 'Nathaniel', 'C.', 'Falcunaya', 'nathaniel.falcunaya@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3179, '2023-0292', 'Kyzer', 'A.', 'Gonda', 'kyzer.gonda@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3180, '2023-0283', 'John', 'Dexter', 'Gonzales', 'john.gonzales@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3181, '2023-0319', 'Reniel', 'B.', 'Jara', 'reniel.jara@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3182, '2023-0158', 'Steven', 'Angelo', 'Legayada', 'steven.legayada@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3183, '2023-0152', 'Angelo', 'M.', 'Lumanglas', 'angelo.lumanglas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3184, '2023-0214', 'Jhon', 'Lester M.', 'Madrigal', 'jhon.madrigal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3185, '2023-0162', 'Rhaven', 'G.', 'Magmanlac', 'rhaven.magmanlac@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3186, '2023-0195', 'Jumyr', 'M.', 'Moreno', 'jumyr.moreno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3187, '2023-0176', 'Dan', 'Lloyd B.', 'Paala', 'dan.paala@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3188, '2023-0206', 'Patrick', 'James U.', 'Romasanta', 'patrick.romasanta@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3189, '2023-0186', 'Jereck', 'M.', 'Roxas', 'jereck.roxas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3190, '2023-0217', 'Jan', 'Denmark C.', 'Santos', 'jan.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3191, '2023-0267', 'John', 'Paolo N.', 'Torralba', 'john.torralba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', 'BSIS', 28, 3, 'irregular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3192, '2022-0079', 'Dianne', 'Christine Joy A.', 'Alulod', 'dianne.alulod@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3193, '2022-0080', 'Rechel', 'R.', 'Arenas', 'rechel.arenas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(3194, '2022-0081', 'Allyna', 'A.', 'Atienza', 'allyna.atienza@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3195, '2022-0130', 'Angela', 'A.', 'Bonilla', 'angela.bonilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3196, '2022-0082', 'Aira', 'F.', 'Cabulao', 'aira.cabulao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3197, '2022-0124', 'Janice', 'C.', 'Cadacio', 'janice.cadacio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3198, '2022-0083', 'Maries', 'D.', 'Cantos', 'maries.cantos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3199, '2022-0084', 'Veronica', 'C.', 'Cantos', 'veronica.cantos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3200, '2022-0139', 'Diana', 'G.', 'Caringal', 'diana.caringal@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3201, '2022-0085', 'Lorebeth', 'C.', 'Casapao', 'lorebeth.casapao@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3202, '2022-0086', 'Carla', 'Jane G.', 'Chiquito', 'carla.chiquito@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3203, '2022-0089', 'Melody', 'T.', 'Enriquez', 'melody.enriquez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3204, '2022-0090', 'Maricon', 'A.', 'Evangelista', 'maricon.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3205, '2022-0091', 'Mary', 'Ann D.', 'Fajardo', 'mary.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3206, '2022-0092', 'Kaecy', 'F.', 'Ferry', 'kaecy.ferry@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3207, '2022-0140', 'Zybel', 'V.', 'Garan', 'zybel.garan@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3208, '2022-0118', 'IC', 'Pamela M.', 'Gutierrez', 'ic.gutierrez@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3209, '2022-0096', 'Jane', 'Monica P.', 'Mansalapus', 'jane.mansalapus@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3210, '2022-0097', 'Hanna', 'Yesha Mae D.', 'Mercado', 'hanna.mercado@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(3211, '2022-0098', 'Abegail', 'D.', 'Moong', 'abegail.moong@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3212, '2022-0125', 'Laiza', 'Marie M.', 'Pole', 'laiza.pole@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3213, '2022-0142', 'Jarryfel', 'N.', 'Tembrevilla', 'jarryfel.tembrevilla@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3214, '2022-0136', 'Jay', 'Mark G.', 'Avelino', 'jay.avelino@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3215, '2022-0072', 'Jairus', 'A.', 'Cabales', 'jairus.cabales@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3216, '2022-0075', 'Jleo', 'Nhico Mari M.', 'Mazo', 'jleo.mazo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3217, '2022-0076', 'Mark', 'Cyrel F.', 'Panganiban', 'mark.panganiban@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3218, '2022-0117', 'Bernabe', 'Dave F.', 'Solas', 'bernabe.solas@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3219, '2022-0078', 'Mark', 'June G.', 'Villena', 'mark.villena@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 29, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3220, '2022-0122', 'Nhicel', 'M.', 'Bueno', 'nhicel.bueno@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3221, '2022-0135', 'Dianne', 'Mae R.', 'Cezar', 'dianne.cezar@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3222, '2022-0147', 'Princess', 'Joy P.', 'De Castro', 'princess.de.castro@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3223, '2022-0141', 'Shiela', 'Mae M.', 'Fajardo', 'shiela.fajardo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3224, '2022-0115', 'Shiela', 'Marie B.', 'Garcia', 'shiela.garcia@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3225, '2022-0129', 'Jessa', 'M.', 'Geneta', 'jessa.geneta@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3226, '2022-0094', 'Jee', 'Anne R.', 'Llamoso', 'jee.llamoso@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(3227, '2022-0123', 'Princess', 'Jenille A.', 'Santos', 'princess.santos@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3228, '2022-0099', 'Von', 'Lester R.', 'Algaba', 'von.algaba@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3229, '2022-0100', 'John', 'Aaron M.', 'Aniel', 'john.aniel@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3230, '2022-0101', 'Keil', 'John C.', 'Antenor', 'keil.antenor@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3231, '2022-0102', 'Mark', 'Joshua M.', 'Bacay', 'mark.bacay@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3232, '2022-0128', 'Michael', 'A.', 'De Guzman', 'michael.de.guzman@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3233, '2022-0107', 'Christian', NULL, 'Delda', 'christian.delda@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3234, '2022-0108', 'Lloyd', 'A.', 'Evangelista', 'lloyd.evangelista@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3235, '2022-0073', 'Samson', 'L.', 'Fulgencio', 'samson.fulgencio@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3236, '2022-0145', 'John', 'Dragan B.', 'Gardoce', 'john.gardoce@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3237, '2022-0127', 'John', 'Elmer', 'Gonzales', 'john.e.gonzales@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3238, '2022-0144', 'Mark', 'Vender N.', 'Muhi', 'mark.muhi@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3239, '2022-0112', 'Marc', 'Paulo B.', 'Relano', 'marc.relano@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3240, '2022-0113', 'Cee', 'Jey G.', 'Rellora', 'cee.rellora@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3241, '2022-0134', 'Franklin', 'R.', 'Salcedo', 'franklin.salcedo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3242, '2022-0120', 'Russel', 'I.', 'Sason', 'russel.sason@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(3243, '2022-0132', 'John', 'Paul D.', 'Teves', 'john.teves@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(3244, '2022-0131', 'John', 'Xavier A.', 'Villanueva', 'john.villanueva@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(3245, '2022-0114', 'Reinier', 'Aron L.', 'Visayana', 'reinier.visayana@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'male', NULL, 30, 4, 'regular', 'active', NULL, NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(3246, '2026-0001', 'Rosa', NULL, 'Castillo', 'rosa.castillo@colegiodenaujan.edu.ph', NULL, NULL, NULL, NULL, 'female', NULL, NULL, 1, 'regular', 'active', NULL, NULL, '2026-04-24 03:43:24', '2026-04-24 03:43:24');

-- --------------------------------------------------------

--
-- Table structure for table `student_document_requests`
--

DROP TABLE IF EXISTS `student_document_requests`;
CREATE TABLE IF NOT EXISTS `student_document_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique request ID (e.g., REQ-2024-001)',
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Existing student ID',
  `document_type` enum('coe','tor','certificate','good_moral','transferee','others') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Specific document name',
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Purpose of document request',
  `urgency_level` enum('normal','urgent','rush') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `status` enum('pending','processing','ready','claimed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `target_date` date DEFAULT NULL COMMENT 'Expected completion date',
  `completion_date` timestamp NULL DEFAULT NULL COMMENT 'Actual completion date',
  `claimed_date` timestamp NULL DEFAULT NULL COMMENT 'When document was claimed',
  `processing_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'Processing fee if applicable',
  `payment_status` enum('unpaid','paid','waived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes or special instructions',
  `processed_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Staff who processed the request',
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path to generated document',
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
-- Table structure for table `student_history`
--

DROP TABLE IF EXISTS `student_history`;
CREATE TABLE IF NOT EXISTS `student_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `field_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `changed_by` int DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `units` int NOT NULL DEFAULT '3',
  `department_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_title`, `units`, `department_code`, `created_at`) VALUES
(1, 'IT101', 'Introduction to Computing', 3, 'BSIS', '2026-03-18 13:54:08'),
(2, 'IT102', 'Computer Programming 1', 3, 'BSIS', '2026-03-18 13:54:08'),
(3, 'GE101', 'Understanding the Self', 3, 'BSIS', '2026-03-18 13:54:08'),
(4, 'MATH101', 'Mathematics in the Modern World', 3, 'BSIS', '2026-03-18 13:54:08'),
(7, 'CS101', 'Introduction to Computing', 3, 'BSIS', '2026-03-19 01:38:38'),
(8, 'CS102', 'Computer Programming 1', 3, 'BSIS', '2026-03-19 01:38:38'),
(9, 'PE101', 'Physical Education 1', 2, NULL, '2026-03-19 01:38:38'),
(10, 'CC105', 'Information Management', 3, 'BSIS', '2026-03-19 01:53:24'),
(11, 'CC106', 'Applications Development', 3, 'BSIS', '2026-03-19 01:53:24'),
(12, 'ISP111', 'Evaluation of Business Performance', 3, 'BSIS', '2026-03-19 01:53:24'),
(13, 'ISP112', 'Data Mining', 3, 'BSIS', '2026-03-19 01:53:24'),
(14, 'ISP113', 'IS Project Management 1', 3, 'BSIS', '2026-03-19 01:53:24'),
(15, 'ISP114', 'Methods of Research', 3, 'BSIS', '2026-03-19 01:53:24'),
(16, 'ISE101', 'Enterprise Systems', 3, 'BSIS', '2026-03-19 01:53:24'),
(17, 'ISCAP101', 'Capstone Project 1', 3, 'BSIS', '2026-03-19 01:53:24'),
(18, 'ISP115', 'IS Project Management 2', 3, 'BSIS', '2026-03-19 01:53:24'),
(19, 'ISP116', 'Enterprise Resource Planning', 3, 'BSIS', '2026-03-19 01:53:24'),
(20, 'ISP117', 'Business Intelligence', 3, 'BSIS', '2026-03-19 01:53:24'),
(21, 'ISE102', 'IS Innovations and New Technologies', 3, 'BSIS', '2026-03-19 01:53:24'),
(22, 'ISE103', 'IT Service Management', 3, 'BSIS', '2026-03-19 01:53:24'),
(23, 'CS103', 'Computer Programming 2', 3, 'BSIS', '2026-03-19 03:04:02'),
(24, 'GE102', 'Readings in Philippine History', 3, 'BSIS', '2026-03-19 03:04:02'),
(25, 'TLE 3 AFA', 'Agrarian', 3, NULL, '2026-03-30 08:09:47');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','email','phone','textarea','file','video','number','select') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `setting_label` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`),
  KEY `idx_setting_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=5153 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `setting_label`, `setting_group`, `description`, `is_required`, `created_at`, `updated_at`) VALUES
(1, 'institution_name', 'Colegio De Naujan', 'text', 'Institution Name', 'general', 'The official name of the institution', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:25'),
(2, 'contact_email', 'colegiodenaujanregistrar@gmail.com', 'email', 'Contact Email', 'general', 'Main contact email address', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:25'),
(3, 'contact_phone', '09563725576', 'phone', 'Contact Phone', 'general', 'Main contact phone number', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:25'),
(4, 'address', 'Barangay Santiago Naujan, Oriental Mindoro', 'textarea', 'Address', 'general', 'Physical address of the institution', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:25'),
(5, 'academic_year', '2025-2026', 'select', 'Academic Year', 'general', 'Current academic year', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:25'),
(6, 'home_video', 'assets/videos/landingvid.mp4', 'video', 'Home Page Video', 'media', 'Background video for the home page hero section', 0, '2026-01-29 15:03:21', '2026-01-29 15:03:21'),
(7, 'admin_username', 'admin', 'text', 'Admin Username', 'account', 'Administrator username', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:19'),
(8, 'admin_email', 'colegiodenaujanregistrar@gmail.com', 'email', 'Admin Email', 'account', 'Administrator email address', 0, '2026-01-29 15:03:21', '2026-02-23 10:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Hashed password',
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff','faculty','program_head','student') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `must_change_password` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
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
) ENGINE=InnoDB AUTO_INCREMENT=6480 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `must_change_password`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'colegiodenaujanregistrar@gmail.com', 'colegiodenaujanregistrar@gmail.com', '$2y$10$5T0.msvXd.5w1LuK865vD.DZ6VRI3eNk35wk8PnWzUJyqjrYdlDZi', 'Registrar Admin', 'admin', 0, 'active', NULL, '2026-01-29 14:47:27', '2026-02-23 10:26:31'),
(5939, 'jerlyn.aday@colegiodenaujan.edu.ph', 'jerlyn.aday@colegiodenaujan.edu.ph', '$2y$10$XYOZ0l12zbIqcM0K7tizvOHPOmhrPhubMS.al7UHmHscOF5REJpxO', 'Jerlyn Aday', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5940, 'althea.dudas@colegiodenaujan.edu.ph', 'althea.dudas@colegiodenaujan.edu.ph', '$2y$10$2OOdT2KB3QNVG3J5T8bjY.N2Bqk/oH7u8EjmLIPNke7hcEed0k6rG', 'Althea Dudas', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5941, 'jasmine.gelena@colegiodenaujan.edu.ph', 'jasmine.gelena@colegiodenaujan.edu.ph', '$2y$10$Ehr31GYNh0wrS1M0jn4KCe4.TZvsAMovkfSPho7hIKq.T2Q7NIviG', 'Jasmine Gelena', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5942, 'kyla.jacob@colegiodenaujan.edu.ph', 'kyla.jacob@colegiodenaujan.edu.ph', '$2y$10$dPwzdXUTPtqFzwXbmRqrS.uLwgdQEiVph1x0A4vviKdwZes4K0eqi', 'Kyla Jacob', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5943, 'kylyn.jacob@colegiodenaujan.edu.ph', 'kylyn.jacob@colegiodenaujan.edu.ph', '$2y$10$uN4F7LxJCYnORshg61rL5OuSPZQvq77HVNuGjL2ez6sY15VaZgA0W', 'Kylyn Jacob', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5944, 'amaya.ma.nibo@colegiodenaujan.edu.ph', 'amaya.ma.nibo@colegiodenaujan.edu.ph', '$2y$10$BJL2C5Hh0rATSuKAVUoH6u8cYAM5u1PfH1MJOJ1OtcUBXV6wwBmGu', 'Amaya Mañibo', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5945, 'keana.marquinez@colegiodenaujan.edu.ph', 'keana.marquinez@colegiodenaujan.edu.ph', '$2y$10$eO8QLtY2u2g0d.RDUwzIzOH.z1dtvzMkVrmUbE9b3xxGlo/R0B.Gu', 'Keana Marquinez', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5946, 'ashley.mendoza@colegiodenaujan.edu.ph', 'ashley.mendoza@colegiodenaujan.edu.ph', '$2y$10$jrlMoginaGIdWlAq.MIPS.dnpXMworm.e7XO2lfthg4HKw8Zbdjyy', 'Ashley Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5947, 'ana.quimora@colegiodenaujan.edu.ph', 'ana.quimora@colegiodenaujan.edu.ph', '$2y$10$vHlVUycBfjFwSoP/SvLw1.aKU8uyc4YXWlK8tx4IaCOt7RM8zoDgW', 'Ana Quimora', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5948, 'camille.tordecilla@colegiodenaujan.edu.ph', 'camille.tordecilla@colegiodenaujan.edu.ph', '$2y$10$KLePEZa1GneeW2fMYKBamey3TlGEwWUYDtUU8H6fHOfvTsOll5EzC', 'Camille Tordecilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5949, 'jonalyn.untalan@colegiodenaujan.edu.ph', 'jonalyn.untalan@colegiodenaujan.edu.ph', '$2y$10$Z0Ou2JUDKQGorEzqR0gvI.uTqPAuTHRBfawvTyc9o7hS.4zUp.YNm', 'Jonalyn Untalan', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5950, 'lyra.villanueva@colegiodenaujan.edu.ph', 'lyra.villanueva@colegiodenaujan.edu.ph', '$2y$10$KyDK0nwUekftxzhKgjhGYe5uhzO.vXwx4FY7Hdj4eskrXpstqz7Vu', 'Lyra Villanueva', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5951, 'rhaizza.villanueva@colegiodenaujan.edu.ph', 'rhaizza.villanueva@colegiodenaujan.edu.ph', '$2y$10$ILm9/sRwQwqkZHAkuPYHpuBD8lLM2GTUQUSVBoOkZAsNhofKAgjeS', 'Rhaizza Villanueva', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5952, 'john.batarlo@colegiodenaujan.edu.ph', 'john.batarlo@colegiodenaujan.edu.ph', '$2y$10$4ogrPyVVTfOIad4tWv8pX.URb0cpzjU5CkfmaK1Akeg99YF1WMPla', 'John Batarlo', 'student', 1, 'active', NULL, '2026-03-23 14:41:15', '2026-03-23 14:41:15'),
(5953, 'ace.castillo@colegiodenaujan.edu.ph', 'ace.castillo@colegiodenaujan.edu.ph', '$2y$10$betaxDjeV4MJTEvMtrDOOeqrCqoEoCatmZVxmP3Kt4fjr3viHhzYK', 'Ace Castillo', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5954, 'john.castillo@colegiodenaujan.edu.ph', 'john.castillo@colegiodenaujan.edu.ph', '$2y$10$kPGEPGb42SUeNT1Nswy8hOxZXQ3aCtewmY5I6phKk3ZYmya7FLTgm', 'John Castillo', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5955, 'jericho.del.mundo@colegiodenaujan.edu.ph', 'jericho.del.mundo@colegiodenaujan.edu.ph', '$2y$10$yn99VeDboLEaKFtG0ITCvu3m0MYV3PIi1X9SkDt/Vm42e2u6ereHm', 'Jericho Del Mundo', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5956, 'khyn.delos.reyes@colegiodenaujan.edu.ph', 'khyn.delos.reyes@colegiodenaujan.edu.ph', '$2y$10$Zi/cLdflbguMbw6uTE8vaedfFlfgp9e/gSRitqlwc5WrkawEBLxwy', 'Khyn Delos Reyes', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5957, 'gian.dudas@colegiodenaujan.edu.ph', 'gian.dudas@colegiodenaujan.edu.ph', '$2y$10$brm4Eybl5KvZjSf78f8F8.CJmfc8/N4GPF8QCZW5MWc8xCwIhPRt2', 'Gian Dudas', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5958, 'mark.fajil@colegiodenaujan.edu.ph', 'mark.fajil@colegiodenaujan.edu.ph', '$2y$10$rays8zFH/vwBldVQXUtheuLYTBHUW/8DqfdrCUqHB0ksd1pdnmtfm', 'Mark Fajil', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5959, 'mark.francisco@colegiodenaujan.edu.ph', 'mark.francisco@colegiodenaujan.edu.ph', '$2y$10$rlJfdI87bTSHmNipg./FW.6GHIqojWsnBtTC6wl//GX3pWOoCnrV.', 'Mark Francisco', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5960, 'jhake.garan@colegiodenaujan.edu.ph', 'jhake.garan@colegiodenaujan.edu.ph', '$2y$10$Dhnv5BgNSiiAhA/8mo5QBeU6cyuSOh5c5PpiRRc0mpB2ADHnyvKL.', 'Jhake Garan', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5961, 'jared.gasic@colegiodenaujan.edu.ph', 'jared.gasic@colegiodenaujan.edu.ph', '$2y$10$okWl6MOFFTNYgl2sMTtwSuOHF7dAc5s6LN1jCVwrQ8jbqF8DeQKUm', 'Jared Gasic', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5962, 'bobby.godoy@colegiodenaujan.edu.ph', 'bobby.godoy@colegiodenaujan.edu.ph', '$2y$10$/KmlUoDsCtdALW/bqZvC8.2l4DtioVxVogilRWkKGYIDkWj6Yh4qu', 'Bobby Godoy', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5963, 'edward.holgado@colegiodenaujan.edu.ph', 'edward.holgado@colegiodenaujan.edu.ph', '$2y$10$NXrpGDO2MVCoCbKxnV9HDutuxlB16klVeVQL8y8JAN.Yy15os4YKi', 'Edward Holgado', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5964, 'jaypee.jacob@colegiodenaujan.edu.ph', 'jaypee.jacob@colegiodenaujan.edu.ph', '$2y$10$jPDNNnhvB4nho5L9p6eTHuaDYQY.gYkdtDo8rdSpFLFqaY0o67Cz6', 'Jaypee Jacob', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5965, 'jhon.macapuno@colegiodenaujan.edu.ph', 'jhon.macapuno@colegiodenaujan.edu.ph', '$2y$10$us6oZwB4qjgvSvdLxkKf.uD.wGUhd2/Zr6OdwFvIbOIlFCwgU07UK', 'Jhon Macapuno', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5966, 'paul.madla@colegiodenaujan.edu.ph', 'paul.madla@colegiodenaujan.edu.ph', '$2y$10$UdwVTeImYrQuNBj.Jx9ZwuZZZAMN0sjkudZ4OpQnBjCdrn9l8p/J2', 'Paul Madla', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5967, 'marlex.mendoza@colegiodenaujan.edu.ph', 'marlex.mendoza@colegiodenaujan.edu.ph', '$2y$10$zQuoQtznW1IjK.37h6OlDO30LFctY.Wsq1UUuWpLJ9LbbUM7Gnkim', 'Marlex Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5968, 'ron.ron.montero@colegiodenaujan.edu.ph', 'ron.ron.montero@colegiodenaujan.edu.ph', '$2y$10$svSYa/cfI.E/tvl/Xw4gQOjzkxnRMK6ObY3iKZ1zZqqjV73EAKxUu', 'Ron-Ron Montero', 'student', 1, 'active', NULL, '2026-03-23 14:41:16', '2026-03-23 14:41:16'),
(5969, 'john.moreno@colegiodenaujan.edu.ph', 'john.moreno@colegiodenaujan.edu.ph', '$2y$10$46LW3S7aAqPstvc56TUr5uudJhUxFcPEvfxllx6Hwj9NrMgHbZpkO', 'John Moreno', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5970, 'johnwin.pastor@colegiodenaujan.edu.ph', 'johnwin.pastor@colegiodenaujan.edu.ph', '$2y$10$MEWUsBIZuDy6hEBQdiWK.ej4ESvWp1tOLL.ogz6MgbVzgWyEq0xDe', 'Johnwin Pastor', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5971, 'jhon.perez@colegiodenaujan.edu.ph', 'jhon.perez@colegiodenaujan.edu.ph', '$2y$10$fcdo6cU4hgSOf2QA8uLY3.jYoRPf6Da1YC5cvnSZ5U.u0/uWYqiVO', 'Jhon Perez', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5972, 'john.perez@colegiodenaujan.edu.ph', 'john.perez@colegiodenaujan.edu.ph', '$2y$10$hV3NxPbgohSRKPGnqoQMveUPvwI5sx4hZkfSPTYR.N7Q4Zq2t8EDm', 'John Perez', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5973, 'khim.tejada@colegiodenaujan.edu.ph', 'khim.tejada@colegiodenaujan.edu.ph', '$2y$10$pUvp3Q3/jcTSHVFja7WAweE81oax7SO9TKEcXqJ2iwHq6l4PZc.Z6', 'Khim Tejada', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5974, 'mary.asi@colegiodenaujan.edu.ph', 'mary.asi@colegiodenaujan.edu.ph', '$2y$10$F3M8idxbekIOZmq4EkeB4etQM7KEiAu/vH06yq4PxQFktLHwI6U2O', 'Mary Asi', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5975, 'marydith.atienza@colegiodenaujan.edu.ph', 'marydith.atienza@colegiodenaujan.edu.ph', '$2y$10$HqgeuAEscW2W8qAU8dmBcOm9tp3dMm8CxEBI3.EG.227Y/QYr5.uW', 'Marydith Atienza', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5976, 'charisma.banila@colegiodenaujan.edu.ph', 'charisma.banila@colegiodenaujan.edu.ph', '$2y$10$z4L1MIvNQ/I95Hz3lU9MMul0UmMfyaMCW3z5FAlueqPE6jR6FGpr6', 'Charisma Banila', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5977, 'myka.braza@colegiodenaujan.edu.ph', 'myka.braza@colegiodenaujan.edu.ph', '$2y$10$42wczkvTBh0Ww6daYktCXeTFjiwtHnkbWT46WkCpusbWcnWQrsaJy', 'Myka Braza', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5978, 'rhealyne.cardona@colegiodenaujan.edu.ph', 'rhealyne.cardona@colegiodenaujan.edu.ph', '$2y$10$wlQm24Fvn9WAgV0YYm5nYuXIqhpqi6z81aw03phSvCoGEWcKK7Moq', 'Rhealyne Cardona', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5979, 'danica.castillo@colegiodenaujan.edu.ph', 'danica.castillo@colegiodenaujan.edu.ph', '$2y$10$8kFrwEaLepC0AYJe9x.LNuAOt2t3oXWTYL.reDa0w5WABJprTikWe', 'Danica Castillo', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5980, 'marra.cleofe@colegiodenaujan.edu.ph', 'marra.cleofe@colegiodenaujan.edu.ph', '$2y$10$LTxzCtlUW4LI9gyICQUdauv79PXUpuO/hNof2AiwT10wtaVMER/vW', 'Marra Cleofe', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5981, 'jocelyn.de.guzman@colegiodenaujan.edu.ph', 'jocelyn.de.guzman@colegiodenaujan.edu.ph', '$2y$10$M6/cxGnNd.ywQK.YbEnIy.IJqFFykbiSiYcYEoW8gONkX52yqCC9S', 'Jocelyn De Guzman', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5982, 'anna.de.leon@colegiodenaujan.edu.ph', 'anna.de.leon@colegiodenaujan.edu.ph', '$2y$10$116NjTftTBHlcbPRLS/ZkOihNHEVVSUcHQqJa0UCz57DpCON1dvRC', 'Anna De Leon', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5983, 'shane.dudas@colegiodenaujan.edu.ph', 'shane.dudas@colegiodenaujan.edu.ph', '$2y$10$m83XSFMnOxpW0u4.cvjl.OVAxxtgq.XLB95wCiMcFxjJ8CZzA2S96', 'Shane Dudas', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5984, 'analyn.fajardo@colegiodenaujan.edu.ph', 'analyn.fajardo@colegiodenaujan.edu.ph', '$2y$10$Wr.T6SN91N0Lk2d4NhXON.VRhAck2WbchJfh7gPGosngonLrsFnDK', 'Analyn Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:17', '2026-03-23 14:41:17'),
(5985, 'zean.falcutila@colegiodenaujan.edu.ph', 'zean.falcutila@colegiodenaujan.edu.ph', '$2y$10$2cTXCG5OcJJhTBs6AtYyiOB5nA8ASCk3yzvw6lv.XJQQknI22ZnI6', 'Zean Falcutila', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5986, 'sharmaine.fonte@colegiodenaujan.edu.ph', 'sharmaine.fonte@colegiodenaujan.edu.ph', '$2y$10$cS57EAvnTku1MFrOKGivIea8fsnvRKwcYGHzzsuxbD88Q1CJSZ5Tm', 'Sharmaine Fonte', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5987, 'crystal.gagote@colegiodenaujan.edu.ph', 'crystal.gagote@colegiodenaujan.edu.ph', '$2y$10$SQENYXOc3mrqXNkmuqTfLe3Vsh0bKsVMjg.lGc/jHM2B2zFTvvDMe', 'Crystal Gagote', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5988, 'janel.garcia@colegiodenaujan.edu.ph', 'janel.garcia@colegiodenaujan.edu.ph', '$2y$10$JlhswsbQDoueVBynt5r07eEelFJRXVn0qwbBilr1pwCYYzcCNlQWm', 'Janel Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5989, 'aleah.gida@colegiodenaujan.edu.ph', 'aleah.gida@colegiodenaujan.edu.ph', '$2y$10$U.DTkIDKIkY3MgCtigTc4O5oPg7Q7X6gQp.wlAt6VrxhDpBtdBYP6', 'Aleah Gida', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5990, 'bhea.gillado@colegiodenaujan.edu.ph', 'bhea.gillado@colegiodenaujan.edu.ph', '$2y$10$uW4cCXh0qVh8KhzzeZXJ1.iFXVeT.zr4Kesm5fcsQIofLdNSIhHPu', 'Bhea Gillado', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5991, 'mae.hernandez@colegiodenaujan.edu.ph', 'mae.hernandez@colegiodenaujan.edu.ph', '$2y$10$npl6/wgrdN/gX5EcV8DHfufppV9C08AG1FZD.RKaySV65xmi/gaDq', 'Mae Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5992, 'arian.maculit@colegiodenaujan.edu.ph', 'arian.maculit@colegiodenaujan.edu.ph', '$2y$10$Pe4XlqwkPSiBSBs2IibYW.PKKYpGwrSCAOorF28hNmK0ILGK/cYOG', 'Arian Maculit', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5993, 'mikee.manay@colegiodenaujan.edu.ph', 'mikee.manay@colegiodenaujan.edu.ph', '$2y$10$Iyaed19BCHDFMW1Vk8rNeOFhL3xo5QFpZPj19TjuRwd7j7fYs/EVi', 'Mikee Manay', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5994, 'lorain.medina@colegiodenaujan.edu.ph', 'lorain.medina@colegiodenaujan.edu.ph', '$2y$10$Amd/5LfzG4qAe7kQ8.HDb.OAMI.w2yc5A0pJ2BKTDsjckQTJudsZW', 'Lorain Medina', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5995, 'lovely.mercado@colegiodenaujan.edu.ph', 'lovely.mercado@colegiodenaujan.edu.ph', '$2y$10$T4OeMOZ4GmlwIr9IkpVrKuAT0fcDlUaPmRD69csNk3wBU82hchjK2', 'Lovely Mercado', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5996, 'romelyn.mongcog@colegiodenaujan.edu.ph', 'romelyn.mongcog@colegiodenaujan.edu.ph', '$2y$10$aTzuduitxYrys.5MdjwTVufgv9MwSHyT2M3yuFHvbWxKSdiuBcsSy', 'Romelyn Mongcog', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5997, 'lleyn.olympia@colegiodenaujan.edu.ph', 'lleyn.olympia@colegiodenaujan.edu.ph', '$2y$10$t4Esu4iokaPHDwz9OSVCWOxMr2h42qUVzrLn/4mcTH4ukABK7Vn0G', 'Lleyn Olympia', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5998, 'althea.paala@colegiodenaujan.edu.ph', 'althea.paala@colegiodenaujan.edu.ph', '$2y$10$M/iz6hmBGcLYjv72lNPE5uaTryxqZGd.eewVXQcT0HlQNfYoC6QAG', 'Althea Paala', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(5999, 'ivy.petilo@colegiodenaujan.edu.ph', 'ivy.petilo@colegiodenaujan.edu.ph', '$2y$10$Cc9T.BhH/jLiYks7a7U7F.sVshy8EmvKWPgSm0FJq8vrKxAsWQTRa', 'Ivy Petilo', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(6000, 'irish.ramos@colegiodenaujan.edu.ph', 'irish.ramos@colegiodenaujan.edu.ph', '$2y$10$bY2Jp21geMZokC4ajUOc6.y4jfE8AO8fueNFB1UkCUCBE6eyzkXR6', 'Irish Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(6001, 'rubilyn.roxas@colegiodenaujan.edu.ph', 'rubilyn.roxas@colegiodenaujan.edu.ph', '$2y$10$V4fpvdWD5gLjY36E49d3D.pMHsU2QdmZmAPwXShbcO2WzgCwrQ7SO', 'Rubilyn Roxas', 'student', 1, 'active', NULL, '2026-03-23 14:41:18', '2026-03-23 14:41:18'),
(6002, 'marie.tolentino@colegiodenaujan.edu.ph', 'marie.tolentino@colegiodenaujan.edu.ph', '$2y$10$U0NHPq1FrLxTu1K1d2tRRuwh6Y6k2V60nVoKEMZGj/yvAaqu5EA6C', 'Marie Tolentino', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6003, 'wyncel.tolentino@colegiodenaujan.edu.ph', 'wyncel.tolentino@colegiodenaujan.edu.ph', '$2y$10$OiOa/UCA.IV2zJxAl1EweOaJa4XGbxIdRyAaHJ30IqOn26m4P/SWa', 'Wyncel Tolentino', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6004, 'felicity.villegas@colegiodenaujan.edu.ph', 'felicity.villegas@colegiodenaujan.edu.ph', '$2y$10$Swa62JOfYWGHOsOtH9kIEuAFFJ7YYn5N.dNb.N4LeMxLGZOI6lRmy', 'Felicity Villegas', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6005, 'danilo.cabiles@colegiodenaujan.edu.ph', 'danilo.cabiles@colegiodenaujan.edu.ph', '$2y$10$/pWfP/voRIZ7cpnTqpmryO4c.1uwIfHeT8T.ayBauI7Ya4t0g6JW.', 'Danilo Cabiles', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6006, 'aldrin.carable@colegiodenaujan.edu.ph', 'aldrin.carable@colegiodenaujan.edu.ph', '$2y$10$9DvLWavJqxdMOmd9DNSsoOZrzkT2vxtS2KnxTaN1NbyfzNJ2Uwi4a', 'Aldrin Carable', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6007, 'daniel.franco@colegiodenaujan.edu.ph', 'daniel.franco@colegiodenaujan.edu.ph', '$2y$10$SjPFsCivUDRXkC9lLQ9YoOaNOCi.EfTLeqvr0Yqd6YxAIdCQhlL46', 'Daniel Franco', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6008, 'jarred.gomez@colegiodenaujan.edu.ph', 'jarred.gomez@colegiodenaujan.edu.ph', '$2y$10$/VwdHDlxhbko1D1FpAVQ0ex6w1kEuQZ/QjWtb1Aa4kPXsg79DJVf6', 'Jarred Gomez', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6009, 'jairus.macuha@colegiodenaujan.edu.ph', 'jairus.macuha@colegiodenaujan.edu.ph', '$2y$10$q7/eX9cwWeiGRSw3SEQcyeUVWOUqTrdNSEXwHZXgSY0ScuM5NWh6y', 'Jairus Macuha', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6010, 'mel.magat@colegiodenaujan.edu.ph', 'mel.magat@colegiodenaujan.edu.ph', '$2y$10$d47AyO0.Hosg0ee3.lvZWuF6Hm7lsAkRPfXH.VES9Z2ZfcCeDMx3q', 'Mel Magat', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6011, 'erwin.tejedor@colegiodenaujan.edu.ph', 'erwin.tejedor@colegiodenaujan.edu.ph', '$2y$10$QSQQYouMgQmeLEn9CJJHF.P1Y/mEVf7jw/Y7zn8.1rMbr58YLmwNK', 'Erwin Tejedor', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6012, 'brix.velasco@colegiodenaujan.edu.ph', 'brix.velasco@colegiodenaujan.edu.ph', '$2y$10$pnXCLSsicjz5ZhB4FwOfbuY43qYkye1dQ.b3/umo/Znb1xZCI6kDy', 'Brix Velasco', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6013, 'k.ann.abela@colegiodenaujan.edu.ph', 'k.ann.abela@colegiodenaujan.edu.ph', '$2y$10$bLlXLaLdpjEFVKo9ZYCTSOGpJgMTFcZT/ifrbglWjAJ6QDbhUyg0a', 'K-Ann Abela', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6014, 'anne.acuzar@colegiodenaujan.edu.ph', 'anne.acuzar@colegiodenaujan.edu.ph', '$2y$10$8K3NB8lyZLB009o7X8EAieGJY1I0iQs5jvIcalAhSFNQ1GNa4Vvmy', 'Anne Acuzar', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6015, 'shane.abendan@colegiodenaujan.edu.ph', 'shane.abendan@colegiodenaujan.edu.ph', '$2y$10$7f5Sa5hI/IpW0OxgZoPu7encJBKwaSaCLC72LCLqYZMK4jY0.Bd86', 'Shane Abendan', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6016, 'hanna.aborde@colegiodenaujan.edu.ph', 'hanna.aborde@colegiodenaujan.edu.ph', '$2y$10$BxjIfl6dhoSZiDcsmrVpSugqb8lFW65smJw.sWjt/yEY8DumUCli2', 'Hanna Aborde', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6017, 'rysa.alfante@colegiodenaujan.edu.ph', 'rysa.alfante@colegiodenaujan.edu.ph', '$2y$10$GcbhwwqFVMbAWC9ABggKGetBwK45bh1pIUI/o0BtacOUI69rjBS3m', 'Rysa Alfante', 'student', 1, 'active', NULL, '2026-03-23 14:41:19', '2026-03-23 14:41:19'),
(6018, 'jeny.amado@colegiodenaujan.edu.ph', 'jeny.amado@colegiodenaujan.edu.ph', '$2y$10$.q.kO3.w69mTtWVQv6vsWejaMa8NCCwDnKu1USlsjldQcSz9gvKgC', 'Jeny Amado', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6019, 'jonah.asi@colegiodenaujan.edu.ph', 'jonah.asi@colegiodenaujan.edu.ph', '$2y$10$IYgPEcDqJn49zulNszttq.gqk5JmpXoZgenin1aQ5pWpX2ok0.xt6', 'Jonah Asi', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6020, 'jhovelyn.bacay@colegiodenaujan.edu.ph', 'jhovelyn.bacay@colegiodenaujan.edu.ph', '$2y$10$HTCZAKjfHS4q4Rff9MhRjOvnYati/i0lVX6nv4cu1R3K00z5A3Ahq', 'Jhovelyn Bacay', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6021, 'alexa.bon@colegiodenaujan.edu.ph', 'alexa.bon@colegiodenaujan.edu.ph', '$2y$10$xvR5/jrbeBhO1Qvuzd54Ru.D8VoLYPtbWJW.4RJJKOai5omjrwawm', 'Alexa Bon', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6022, 'lorraine.bonado@colegiodenaujan.edu.ph', 'lorraine.bonado@colegiodenaujan.edu.ph', '$2y$10$UJvN1hrh8iHRYDJS76IyoOun3X5ZkcO6/vZxYK4A1mC3KT5rYxT.G', 'Lorraine Bonado', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6023, 'shiella.bonifacio@colegiodenaujan.edu.ph', 'shiella.bonifacio@colegiodenaujan.edu.ph', '$2y$10$tYW.SA2.qimWlEy.GuJUu.SzHAsDv4AsHufN.gRvLNZ5q4XTwZ8xm', 'Shiella Bonifacio', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6024, 'claren.carable@colegiodenaujan.edu.ph', 'claren.carable@colegiodenaujan.edu.ph', '$2y$10$IVikXeBWkacIH9E9tHsPRunTfkuxRh9VV8zRmDYCfickAdW7iG3gO', 'Claren Carable', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6025, 'prences.consigo@colegiodenaujan.edu.ph', 'prences.consigo@colegiodenaujan.edu.ph', '$2y$10$Qd/HS2JAgpPo9IMtG84iTOgsZE7udZufgXl2kbkRW3OpZPE7wf8.6', 'Prences Consigo', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6026, 'jamhyca.de.chavez@colegiodenaujan.edu.ph', 'jamhyca.de.chavez@colegiodenaujan.edu.ph', '$2y$10$wbyY3MEn86L8aMXaSOpjq.HEi.igLaygrRjgoPZtgf3y/m94.9WOO', 'Jamhyca De Chavez', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6027, 'nicole.defeo@colegiodenaujan.edu.ph', 'nicole.defeo@colegiodenaujan.edu.ph', '$2y$10$vFl4VT2VgRxaRuPEByYleuYAd90ihrjpAKlVgSKOSQK/B8W2Hr3vq', 'Nicole Defeo', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6028, 'sophia.delos.reyes@colegiodenaujan.edu.ph', 'sophia.delos.reyes@colegiodenaujan.edu.ph', '$2y$10$89q2qqht/oB8DrQVR4dOKeb45ESSFQN0nkFglus0v0JBJ/AxuiXFm', 'Sophia Delos Reyes', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6029, 'romelyn.elida@colegiodenaujan.edu.ph', 'romelyn.elida@colegiodenaujan.edu.ph', '$2y$10$bz7vBKjOG.MntasBm.h4zO0ztctVDrt3G3rAz79RJCaKHILv5DISW', 'Romelyn Elida', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6030, 'christina.enriquez@colegiodenaujan.edu.ph', 'christina.enriquez@colegiodenaujan.edu.ph', '$2y$10$Zv3wT4RRWuqpur18fshGIeSrYYlPHyb.HPdRW2sPGsxn0uGTEXD.e', 'Christina Enriquez', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6031, 'elayca.fajardo@colegiodenaujan.edu.ph', 'elayca.fajardo@colegiodenaujan.edu.ph', '$2y$10$0voS/MzPnBl2TWi9kDVCZughqlut1Bn4nYusg18tfSh6uEnIPnR3e', 'Elayca Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6032, 'ailla.fajura@colegiodenaujan.edu.ph', 'ailla.fajura@colegiodenaujan.edu.ph', '$2y$10$jFNdsNBXJVSMUiOI.E7i4uOOt7n0EIaycDft1DERY.9AAc9CUUNbS', 'Ailla Fajura', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6033, 'judith.fallarna@colegiodenaujan.edu.ph', 'judith.fallarna@colegiodenaujan.edu.ph', '$2y$10$kMCgqn/EJ0AUTb.wVcW54OvCG6em1QNkDzCH2OmYWhwh4jrtsSOgG', 'Judith Fallarna', 'student', 1, 'active', NULL, '2026-03-23 14:41:20', '2026-03-23 14:41:20'),
(6034, 'jenelyn.fonte@colegiodenaujan.edu.ph', 'jenelyn.fonte@colegiodenaujan.edu.ph', '$2y$10$dKCpfsHVykkSoMjcpm8w4.DkWwPPIkpdiv1w8C7sAQys8ejygjagK', 'Jenelyn Fonte', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6035, 'katrice.garcia@colegiodenaujan.edu.ph', 'katrice.garcia@colegiodenaujan.edu.ph', '$2y$10$p507FGcSqH7x.Ven8LH.3OGB8rGD.GI9iLDsdkUISRI6buwHIA5L2', 'Katrice Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6036, 'shalemar.geroleo@colegiodenaujan.edu.ph', 'shalemar.geroleo@colegiodenaujan.edu.ph', '$2y$10$SwRN6SPtBIp3HHfY/EfZLuqJlGoncxdsbzc4uhbxSHKdfDuphuw6m', 'Shalemar Geroleo', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6037, 'edlyn.hernandez@colegiodenaujan.edu.ph', 'edlyn.hernandez@colegiodenaujan.edu.ph', '$2y$10$xf9QGixMaS57BUBo/zTvoOUP/BVUbdBCjDOKM80pjoWtWyC.UN4Hi', 'Edlyn Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6038, 'angela.lotho@colegiodenaujan.edu.ph', 'angela.lotho@colegiodenaujan.edu.ph', '$2y$10$06xcxKa6j1BVJ6Hv9r9Jh.sXSg66.EuMDfKsAKMn09QOSebbSURN6', 'Angela Lotho', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6039, 'remz.macapuno@colegiodenaujan.edu.ph', 'remz.macapuno@colegiodenaujan.edu.ph', '$2y$10$ufeWi9Bx49y5fjABIC72HeyXzq1.D3uGinNoSxc60GWco9bJrRUIK', 'Remz Macapuno', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6040, 'leslie.melgar@colegiodenaujan.edu.ph', 'leslie.melgar@colegiodenaujan.edu.ph', '$2y$10$ralmietzMw0Mvw8gjEdDouduKgt/skeZkrB1mQV2mZOVfWsCRY2Si', 'Leslie Melgar', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6041, 'camille.milambiling@colegiodenaujan.edu.ph', 'camille.milambiling@colegiodenaujan.edu.ph', '$2y$10$QCGIR7RpNJNJw5bnbo12euajjvtjG1nNmG8WwYBmpxdhzNshtQJ0e', 'Camille Milambiling', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6042, 'erica.motol@colegiodenaujan.edu.ph', 'erica.motol@colegiodenaujan.edu.ph', '$2y$10$mxChpzgX6vJsAOKfSgWy3.pm54AbAzPBzIKao4JBJy9AK.CaEd5AC', 'Erica Motol', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6043, 'ma.obando@colegiodenaujan.edu.ph', 'ma.obando@colegiodenaujan.edu.ph', '$2y$10$QZfoPb/nbhQTkbMHYx4P3OiRo53K9DRFUs6QpyS7pUk3iy2a6CcS.', 'Ma. Obando', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6044, 'argel.ocampo@colegiodenaujan.edu.ph', 'argel.ocampo@colegiodenaujan.edu.ph', '$2y$10$7zaTlVPiA0djc0nYDPgBneP/DrNQ7xVUm5guZxjlcz1VocGZ9cBHS', 'Argel Ocampo', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6045, 'jea.rivera@colegiodenaujan.edu.ph', 'jea.rivera@colegiodenaujan.edu.ph', '$2y$10$TTDGcJPxlUkbmPFgmUr2dOuTRZH33HENJVY.43kU/GCSPqTMzWJzK', 'Jea Rivera', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6046, 'ashly.rana@colegiodenaujan.edu.ph', 'ashly.rana@colegiodenaujan.edu.ph', '$2y$10$JZs80fsHL7FSgNlaAexZm.MIJfwWlOYNwxP6tFGagPlORGRPKkfYe', 'Ashly Rana', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6047, 'aimie.reyes@colegiodenaujan.edu.ph', 'aimie.reyes@colegiodenaujan.edu.ph', '$2y$10$tCV4ZyuZ6/SMlAjWMsJh0O/yTszvD5pN2G4EqXPYqPjDde5laR7d.', 'Aimie Reyes', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6048, 'rhenelyn.sandoval@colegiodenaujan.edu.ph', 'rhenelyn.sandoval@colegiodenaujan.edu.ph', '$2y$10$tY9A03rVlfE2QWOf.5n7WegF/Qh8OQgQtkQNx.qY0y2bWDzTt77o2', 'Rhenelyn Sandoval', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6049, 'nicole.silva@colegiodenaujan.edu.ph', 'nicole.silva@colegiodenaujan.edu.ph', '$2y$10$Cjj1RHOEijPhaGq9HJIRiOORQxNTjVRrsaEZDpwxOBjGyudQuM5DG', 'Nicole Silva', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6050, 'jeane.sulit@colegiodenaujan.edu.ph', 'jeane.sulit@colegiodenaujan.edu.ph', '$2y$10$9trDP.6d936ev1OSQjeln.nCRIBLf8jLgo/6Y1ySM4eSTJUhpwjfm', 'Jeane Sulit', 'student', 1, 'active', NULL, '2026-03-23 14:41:21', '2026-03-23 14:41:21'),
(6051, 'pauleen.villaruel@colegiodenaujan.edu.ph', 'pauleen.villaruel@colegiodenaujan.edu.ph', '$2y$10$MMQmuHqnVfBAAsK42VOw/.mpXvZ0BWYqOpc5N.ei/g5FBSoFP0ZA.', 'Pauleen Villaruel', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6052, 'megan.visaya@colegiodenaujan.edu.ph', 'megan.visaya@colegiodenaujan.edu.ph', '$2y$10$wILOOMvYRhy9k/q2IHsW5OGV1yY9TE1H3C7P3z3NSiyR5B/lo01JC', 'Megan Visaya', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6053, 'rodel.arenas@colegiodenaujan.edu.ph', 'rodel.arenas@colegiodenaujan.edu.ph', '$2y$10$kHkfsR5sySnNoGtAHx7yL.078EvjifUqUes.Ukpzk52d5TRx335Xm', 'Rodel Arenas', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6054, 'rexner.eguillon@colegiodenaujan.edu.ph', 'rexner.eguillon@colegiodenaujan.edu.ph', '$2y$10$Q6bSoH1wyisBL09kYP7QB.FYKNTb8e2geeExQouOIsnQyXXO9BuYS', 'Rexner Eguillon', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6055, 'reymart.elmido@colegiodenaujan.edu.ph', 'reymart.elmido@colegiodenaujan.edu.ph', '$2y$10$M43KoTPwGeJ0mStVriwY0O6fwwxaFhxbkwndfOKVTAQbc6mPxWfge', 'Reymart Elmido', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6056, 'kervin.garachico@colegiodenaujan.edu.ph', 'kervin.garachico@colegiodenaujan.edu.ph', '$2y$10$hRflV8hWkajM3iQCUl/7SeLtmm4VEhoNvrOwNEpp9.Y3jdrEq7Xbu', 'Kervin Garachico', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6057, 'zyris.guavez@colegiodenaujan.edu.ph', 'zyris.guavez@colegiodenaujan.edu.ph', '$2y$10$s70FtmI.u5CmfCJgdkAn9.n4CsJ95W2J.UaC4/9VSKL5U13MtwGR6', 'Zyris Guavez', 'student', 0, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:44:30'),
(6058, 'marjun.linayao@colegiodenaujan.edu.ph', 'marjun.linayao@colegiodenaujan.edu.ph', '$2y$10$9oLLA774zAB674JkBtW/muHfbTxBQ7DomVrtWxvfQltoH9jwsrge6', 'Marjun Linayao', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6059, 'john.macapuno@colegiodenaujan.edu.ph', 'john.macapuno@colegiodenaujan.edu.ph', '$2y$10$kBc3YOXbEhF1BqRbw17VIeZoNrUXPVMqSw.DAl9pXZ6eDfZgzFnVm', 'John Macapuno', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6060, 'helbert.maulion@colegiodenaujan.edu.ph', 'helbert.maulion@colegiodenaujan.edu.ph', '$2y$10$ANXYV4lZYhEpPWCngFDvCee3UT6BckSlr338HSRL.Ud.soyyyYtvK', 'Helbert Maulion', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6061, 'dindo.tolentino@colegiodenaujan.edu.ph', 'dindo.tolentino@colegiodenaujan.edu.ph', '$2y$10$.Es6NgiKZ446CLYqkuFjOOi5XFgDPTFYE1fExTg.XXo9IXSpAAWi6', 'Dindo Tolentino', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6062, 'novelyn.albufera@colegiodenaujan.edu.ph', 'novelyn.albufera@colegiodenaujan.edu.ph', '$2y$10$spDpsEcxiJzDo2Ri1.7UhOSRvXZ5Q/99jSAbRW6Zz9r7Pi8atmCBu', 'Novelyn Albufera', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6063, 'angela.aldea@colegiodenaujan.edu.ph', 'angela.aldea@colegiodenaujan.edu.ph', '$2y$10$44zI71bJi.uH00pMokH8tez8VipwatHK7kyq1f0Tg77.Roh0MiaZ6', 'Angela Aldea', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6064, 'maria.aldovino@colegiodenaujan.edu.ph', 'maria.aldovino@colegiodenaujan.edu.ph', '$2y$10$7HgbwcJmWdd0tC30PQ0pu.eqe94004LRSr7wz0nOGIVPBlyh4YDQm', 'Maria Aldovino', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6065, 'aizel.alvarez@colegiodenaujan.edu.ph', 'aizel.alvarez@colegiodenaujan.edu.ph', '$2y$10$w6n/bVlPBEl44HYgPOHfXupsYNR.IAv/hoUs2.RZ/fsdsXmT.L14y', 'Aizel Alvarez', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6066, 'sherilyn.anyayahan@colegiodenaujan.edu.ph', 'sherilyn.anyayahan@colegiodenaujan.edu.ph', '$2y$10$vlKJQwXveFyLCoksL234k.Rxanu1PQogQS1s.eiLzbABDNkpAYX3O', 'Sherilyn Anyayahan', 'student', 1, 'active', NULL, '2026-03-23 14:41:22', '2026-03-23 14:41:22'),
(6067, 'mika.buadilla@colegiodenaujan.edu.ph', 'mika.buadilla@colegiodenaujan.edu.ph', '$2y$10$x4bajqxqoXaNIB0SZnngVuVrDCP5Sti2/ckZQgMNdmYQ6Pqa4ZxpG', 'Mika Buadilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6068, 'daniela.cabiles@colegiodenaujan.edu.ph', 'daniela.cabiles@colegiodenaujan.edu.ph', '$2y$10$yeduHRx89l1683ZNEcw1ZuWnB6a4M79XEUWkKi8unScVK9wYPFNFa', 'Daniela Cabiles', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6069, 'prinses.calaolao@colegiodenaujan.edu.ph', 'prinses.calaolao@colegiodenaujan.edu.ph', '$2y$10$s/XYLLtDYIy8bdl0Akr4buWv5595EXxAOMoxlIlzfd./kSSqX/nVS', 'Prinses Calaolao', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6070, 'deah.carpo@colegiodenaujan.edu.ph', 'deah.carpo@colegiodenaujan.edu.ph', '$2y$10$CpnXbkwQubha80jrXO274O6xVcYKrzt0xLeXAcQL6yuc9bU8WyCZy', 'Deah Carpo', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6071, 'jedidiah.gelena@colegiodenaujan.edu.ph', 'jedidiah.gelena@colegiodenaujan.edu.ph', '$2y$10$OziRJvskJZ2Zxw2pXHn0bOfiNsMmF5MOn5RlmYzB2E840L1fWb7Ca', 'Jedidiah Gelena', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6072, 'aleyah.jara@colegiodenaujan.edu.ph', 'aleyah.jara@colegiodenaujan.edu.ph', '$2y$10$FHcbyDW6ZUTSNS8fqEZ.yurcub1k7GjX7/fSBJELhY0LT6UxZjnuG', 'Aleyah Jara', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6073, 'charese.jolo@colegiodenaujan.edu.ph', 'charese.jolo@colegiodenaujan.edu.ph', '$2y$10$51SoeO7bocD1XNQbemmFqORHztScePBus0a4D1FqHFk7RovKtBGRu', 'Charese Jolo', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6074, 'janice.lugatic@colegiodenaujan.edu.ph', 'janice.lugatic@colegiodenaujan.edu.ph', '$2y$10$SeS9PagXtxf28kSZVEY1BOBsUWMqW5rK1iSeFBLnkOLCTbRodbkVe', 'Janice Lugatic', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6075, 'abegail.malogue.no@colegiodenaujan.edu.ph', 'abegail.malogue.no@colegiodenaujan.edu.ph', '$2y$10$E.9RSbLxIKYJ8yEUE9ibguAQe5dLB3/haQUPz5rCXikMYSubRTtDe', 'Abegail Malogueño', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6076, 'ericca.marquez@colegiodenaujan.edu.ph', 'ericca.marquez@colegiodenaujan.edu.ph', '$2y$10$Y6TcwxVSW36tnEsENZZwL.Aj3BcXOKb1mkm08EZjDkwgbPcY1YtqK', 'Ericca Marquez', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6077, 'arien.montesa@colegiodenaujan.edu.ph', 'arien.montesa@colegiodenaujan.edu.ph', '$2y$10$ZFBcsYIXzk5s8SKOWI8Qk.et7nDO/igDTanFtYBnbOKRBRacHg6u2', 'Arien Montesa', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6078, 'jasmine.nuestro@colegiodenaujan.edu.ph', 'jasmine.nuestro@colegiodenaujan.edu.ph', '$2y$10$OmCHpJybvEO4CeyWHHI4Duhf2cUTSxMF.T.ou2WWUBiq39EVw/noC', 'Jasmine Nuestro', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6079, 'nicole.ola@colegiodenaujan.edu.ph', 'nicole.ola@colegiodenaujan.edu.ph', '$2y$10$Ql/ArcYeAFyxVABm8Jipo.bVc91A9uy9CvpDPRWybaCf6vKd9/336', 'Nicole Ola', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6080, 'alyssa.quintia@colegiodenaujan.edu.ph', 'alyssa.quintia@colegiodenaujan.edu.ph', '$2y$10$Oe50M709nlIYskNojF9sdeQOaFJlbCStBvjSSGrjSt4OxD9S7QIK.', 'Alyssa Quintia', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6081, 'jona.romero@colegiodenaujan.edu.ph', 'jona.romero@colegiodenaujan.edu.ph', '$2y$10$gMIWsNwpZJrgAQrMVIsp0.hwSQmako6BnPO66AuagK944tR954Aoq', 'Jona Romero', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6082, 'marbhel.rucio@colegiodenaujan.edu.ph', 'marbhel.rucio@colegiodenaujan.edu.ph', '$2y$10$FC7wFWDRXff/DwAAhP3WmO2EiTN1pO6VEdLimkAYZuMmIwv.OI68e', 'Marbhel Rucio', 'student', 1, 'active', NULL, '2026-03-23 14:41:23', '2026-03-23 14:41:23'),
(6083, 'lovely.torres@colegiodenaujan.edu.ph', 'lovely.torres@colegiodenaujan.edu.ph', '$2y$10$ypqWJeZ7Y5OkV9VG2xFwyufBiwXn2.PVsgybEpQuUVhxXgVPhJ7rm', 'Lovely Torres', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6084, 'rexon.abanilla@colegiodenaujan.edu.ph', 'rexon.abanilla@colegiodenaujan.edu.ph', '$2y$10$i5lwJ9I9Omd3OuZ5h.Xxh.cj34JR/ciZbo3HLOiZgY8GEK3mfptS.', 'Rexon Abanilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6085, 'ramfel.azucena@colegiodenaujan.edu.ph', 'ramfel.azucena@colegiodenaujan.edu.ph', '$2y$10$knMDa13SuzQPUA2pv0By6eVMfd.IvwQnrOo/oTrTTy.3DwpwvnAWe', 'Ramfel Azucena', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6086, 'jeverson.bersoto@colegiodenaujan.edu.ph', 'jeverson.bersoto@colegiodenaujan.edu.ph', '$2y$10$DDVhKvetG7/nvhDgWvkcb.mCR2qZJMQ2j07S39XYA7zvSnIGODgem', 'Jeverson Bersoto', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6087, 'shervin.castro@colegiodenaujan.edu.ph', 'shervin.castro@colegiodenaujan.edu.ph', '$2y$10$rO9o29Eqk3A6S6qng/fBLe99Kr9goTEMO3cdaP5FCi3blPsJzZf5y', 'Shervin Castro', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6088, 'daniel.de.ade@colegiodenaujan.edu.ph', 'daniel.de.ade@colegiodenaujan.edu.ph', '$2y$10$Lb7xAz5MnHP2e5YJOeWr9unJYmoIZbVrKvAKhABMUYUpDh8Os/NU2', 'Daniel De Ade', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6089, 'dave.despa@colegiodenaujan.edu.ph', 'dave.despa@colegiodenaujan.edu.ph', '$2y$10$lWO3OSyAtawP8sBmCtOpZe1LlddlNEDNco6IdVlJkappuAR5DxEqC', 'Dave Despa', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6090, 'alexander.ducado@colegiodenaujan.edu.ph', 'alexander.ducado@colegiodenaujan.edu.ph', '$2y$10$iV2/usjIkslaT87O1kKoY.br4A.DJF.2b1PqDlszyaR71ukyTcGpa', 'Alexander Ducado', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6091, 'uranus.evangelista@colegiodenaujan.edu.ph', 'uranus.evangelista@colegiodenaujan.edu.ph', '$2y$10$FQKCZmNUu4.1Wocw3MybsuivfJUxFQWKt9ucNW1SN6WXs/1V3MHyq', 'Uranus Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6092, 'joshua.gabon@colegiodenaujan.edu.ph', 'joshua.gabon@colegiodenaujan.edu.ph', '$2y$10$.WUh8keF9jA6ZEZoFBCkP.ML7G7thzpFFdlXv2IBsNNHCqWvqwVO6', 'Joshua Gabon', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6093, 'john.gavilan@colegiodenaujan.edu.ph', 'john.gavilan@colegiodenaujan.edu.ph', '$2y$10$kwLDoKgrjjQMTbmTggbmcuLEcenoW2iJaYwPDMa3MXtKVhXXV4S8W', 'John Gavilan', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6094, 'mc.gibo@colegiodenaujan.edu.ph', 'mc.gibo@colegiodenaujan.edu.ph', '$2y$10$V0xBcyWX94fqEudmHB5slO8O9j2FeyorW6a8K8ivarwuW4aL/H3SG', 'Mc Gibo', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6095, 'dan.hatulan@colegiodenaujan.edu.ph', 'dan.hatulan@colegiodenaujan.edu.ph', '$2y$10$Td9Jk1/AzSmupJpcMLvAMeCDHCFQsxe66fceP7eOAhB.nlyrsudRm', 'Dan Hatulan', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6096, 'benjamin.hernandez@colegiodenaujan.edu.ph', 'benjamin.hernandez@colegiodenaujan.edu.ph', '$2y$10$jv4JPkIo5pe3OL5FaLsFkeSlt4W4lUl6aT5HJwwXG2.8gLLYcYISm', 'Benjamin Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6097, 'renz.hernandez@colegiodenaujan.edu.ph', 'renz.hernandez@colegiodenaujan.edu.ph', '$2y$10$5kJPmgY1q7fjNw8o0Xyx4ekprrQnmas65JyxzF7erOEyAzGEvfsei', 'Renz Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6098, 'ralph.javier@colegiodenaujan.edu.ph', 'ralph.javier@colegiodenaujan.edu.ph', '$2y$10$tVLF.JfY871hH8hbKFvpreXFcivXbjoYAccTww8/MBPA8AToxHw5q', 'Ralph Javier', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6099, 'andrew.laredo@colegiodenaujan.edu.ph', 'andrew.laredo@colegiodenaujan.edu.ph', '$2y$10$9zXyEhU0aDEN55defpH1DePBmVZWg3z9rGM2nEBKO5gJ3IuXm/Zyq', 'Andrew Laredo', 'student', 1, 'active', NULL, '2026-03-23 14:41:24', '2026-03-23 14:41:24'),
(6100, 'janryx.las.pinas@colegiodenaujan.edu.ph', 'janryx.las.pinas@colegiodenaujan.edu.ph', '$2y$10$IV6J1DP1tddlApv1hgchOOV/56Vi67t5BV064AvObtLXTBv2G1QJy', 'Janryx Las Pinas', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6101, 'bricks.lindero@colegiodenaujan.edu.ph', 'bricks.lindero@colegiodenaujan.edu.ph', '$2y$10$B8nwQYCx8ZiO.X3wkkRFGO11lXfW4iYfemLnROOipngsDqsodBfA6', 'Bricks Lindero', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6102, 'luigi.lomio@colegiodenaujan.edu.ph', 'luigi.lomio@colegiodenaujan.edu.ph', '$2y$10$zfe9OCwsw4uiB2BRLMBmyOdwhBkNMW8h4yDvMlmJ4w7TkWV.LFuaW', 'Luigi Lomio', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6103, 'john.macalindol@colegiodenaujan.edu.ph', 'john.macalindol@colegiodenaujan.edu.ph', '$2y$10$u1CI4OlTct2e4itz75tgG.D96p1Gei41RDVD0oZSeJQU4JhRCisxa', 'John Macalindol', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6104, 'jandy.macapuno@colegiodenaujan.edu.ph', 'jandy.macapuno@colegiodenaujan.edu.ph', '$2y$10$cpVL.OuVMxzddSnfW6qBleUgNjOqKuA2jHMu1bByDQl2SQX.q0nUe', 'Jandy Macapuno', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6105, 'cedrick.mandia@colegiodenaujan.edu.ph', 'cedrick.mandia@colegiodenaujan.edu.ph', '$2y$10$0S/IQgoehTf62HKkpq0nAu155XE9MRNM/Gjlrrjk3WcXG.r.BPmFC', 'Cedrick Mandia', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6106, 'eric.marinduque@colegiodenaujan.edu.ph', 'eric.marinduque@colegiodenaujan.edu.ph', '$2y$10$ar1MY0YD3Bz7AQteZST0eOAcY6hhI/BYDyQDeF7fIaDQOj5btwQVS', 'Eric Marinduque', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6107, 'jimrex.mayano@colegiodenaujan.edu.ph', 'jimrex.mayano@colegiodenaujan.edu.ph', '$2y$10$m.BzppYS3VNSE.EwcEdo/.R0F/aucX5l8/RB0.4VFu5fOgsq8HOj6', 'Jimrex Mayano', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6108, 'hedyen.mendoza@colegiodenaujan.edu.ph', 'hedyen.mendoza@colegiodenaujan.edu.ph', '$2y$10$Rkfk12BNxekRd..xLiTgYuOpQeAMEcrHpQVHzLulCcHOk9o/yW1YC', 'Hedyen Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6109, 'mark.montevirgen@colegiodenaujan.edu.ph', 'mark.montevirgen@colegiodenaujan.edu.ph', '$2y$10$oyR3.TnRtVKt0N4QeT3JLOeQP9cZq6iQHSjrCog5YN7IbpY.W91eq', 'Mark Montevirgen', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6110, 'jm.nas@colegiodenaujan.edu.ph', 'jm.nas@colegiodenaujan.edu.ph', '$2y$10$xpSE/YCvO3OoZ36H.aCUtuKqH37KsIBm.b6b1ocpMjBvblcUlYHcO', 'JM Nas', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6111, 'vhon.ornos@colegiodenaujan.edu.ph', 'vhon.ornos@colegiodenaujan.edu.ph', '$2y$10$d7r5p58lhfHZhYF9nlVo8eIWZgxEL.tzPy.YtDg6zGvV8fI3TqQhC', 'Vhon Ornos', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6112, 'carl.padua@colegiodenaujan.edu.ph', 'carl.padua@colegiodenaujan.edu.ph', '$2y$10$qbBPBOJDsktA4/d/ZojhQOVPX3qSWXThpNLLrNwc/v2RRBOWIZdQK', 'Carl Padua', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6113, 'patrick.paz@colegiodenaujan.edu.ph', 'patrick.paz@colegiodenaujan.edu.ph', '$2y$10$jnA67Dajyccg.ZRFaVBpLOte.iNlN3vNc/iPUre.egvLAbb6cw1eq', 'Patrick Paz', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6114, 'mark.pecolados@colegiodenaujan.edu.ph', 'mark.pecolados@colegiodenaujan.edu.ph', '$2y$10$mHBocXchUEIVkDHgnhNuSe7Pb7awZ.A.9LTbh2.lSrDqDQGw4fby2', 'Mark Pecolados', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6115, 'tristan.plata@colegiodenaujan.edu.ph', 'tristan.plata@colegiodenaujan.edu.ph', '$2y$10$nLJLnUqmFuZoOTG6BDhkl.8fe4WYUT7cxIbkmzFdChFoI37rjov5G', 'Tristan Plata', 'student', 1, 'active', NULL, '2026-03-23 14:41:25', '2026-03-23 14:41:25'),
(6116, 'jude.somera@colegiodenaujan.edu.ph', 'jude.somera@colegiodenaujan.edu.ph', '$2y$10$vMuHBU2XGlM0zFoEw.wOvubWSFEqPRhWLrK4KQOS6bTLz3RO8CmJi', 'Jude Somera', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6117, 'philip.tabor@colegiodenaujan.edu.ph', 'philip.tabor@colegiodenaujan.edu.ph', '$2y$10$2k0w.8P0qOI1Wa7vEjW8BOT/Zxns5m4mSawIIrpH9Zz9TfXG0kxlq', 'Philip Tabor', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6118, 'ivan.ylagan@colegiodenaujan.edu.ph', 'ivan.ylagan@colegiodenaujan.edu.ph', '$2y$10$S3Bw1e1uur8Bjf4lWRVOXuUgcl8a8uQiqU5PDXJXpFtNLKAk/lrl2', 'Ivan Ylagan', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6119, 'kiana.a.nonuevo@colegiodenaujan.edu.ph', 'kiana.a.nonuevo@colegiodenaujan.edu.ph', '$2y$10$GZuGdlrrpE/0tj0GFFWEhOZwbs.ODh0EG7o3ZFhO9IAUkgTBPWCCu', 'Kiana Añonuevo', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6120, 'kyla.anonuevo@colegiodenaujan.edu.ph', 'kyla.anonuevo@colegiodenaujan.edu.ph', '$2y$10$0Q7kJp0CmQEj6FqdUfLqtOBym3UHNL11Mmp3lYaZj6Vp/epscrxuS', 'Kyla Anonuevo', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6121, 'katrice.antipasado@colegiodenaujan.edu.ph', 'katrice.antipasado@colegiodenaujan.edu.ph', '$2y$10$IN8p3Ev55XH/6nObUetNUeWqy1tbL94qmUZPsatBaH8kygPWvMBou', 'Katrice Antipasado', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6122, 'regine.antipasado@colegiodenaujan.edu.ph', 'regine.antipasado@colegiodenaujan.edu.ph', '$2y$10$NmgQ4qFtVaVhGJgXilwov.AGeTpy0zA7fWK8iqUiyx3W1xUDf6/CG', 'Regine Antipasado', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6123, 'juneth.baliday@colegiodenaujan.edu.ph', 'juneth.baliday@colegiodenaujan.edu.ph', '$2y$10$y7gkhkYXtcKPrFlNkd/V.ey2dA03dhPx2gTxx7TaFOqC/bKl9cqGq', 'Juneth Baliday', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6124, 'gielysa.concha@colegiodenaujan.edu.ph', 'gielysa.concha@colegiodenaujan.edu.ph', '$2y$10$G7gfaeBZ4TjFbGbEzoJ2..N7FaPsp/Nz1R5wazeuUxUFudoGjuESu', 'Gielysa Concha', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6125, 'maecelle.fiedalan@colegiodenaujan.edu.ph', 'maecelle.fiedalan@colegiodenaujan.edu.ph', '$2y$10$/FdQ7UWVXtYzWXui1h.rK.qCA8rfd68DkWiQJsvWgibueBldcwhf2', 'Maecelle Fiedalan', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6126, 'lara.garcia@colegiodenaujan.edu.ph', 'lara.garcia@colegiodenaujan.edu.ph', '$2y$10$m/rYkhoybApGafLStM1sCO6gnVd8qdFQ21Oq5Eit0elBNZXoUDIhC', 'Lara Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6127, 'jade.garing@colegiodenaujan.edu.ph', 'jade.garing@colegiodenaujan.edu.ph', '$2y$10$plC4hw2gncc/l6roDCVtLeSiEUnN0JkB9hGAL1jaxkelwuwCobYXW', 'Jade Garing', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6128, 'rica.glodo@colegiodenaujan.edu.ph', 'rica.glodo@colegiodenaujan.edu.ph', '$2y$10$VCaJLTDaCzXNvJVCvwFR5.ZYONgbj./RSR032jN6HPrz03Qlkp2E2', 'Rica Glodo', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6129, 'danica.hornilla@colegiodenaujan.edu.ph', 'danica.hornilla@colegiodenaujan.edu.ph', '$2y$10$VMs2L9ZkokV3Pb.KtZZEnOK.zEfnENsX9RpcY1uHSm.eaapplM4ne', 'Danica Hornilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6130, 'jenny.idea@colegiodenaujan.edu.ph', 'jenny.idea@colegiodenaujan.edu.ph', '$2y$10$Pp0WEkmELeq3CKF9ub/GIuDDfxG/PUwGLmgZ54l32B6hQtpLvf2Tq', 'Jenny Idea', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6131, 'kimberly.illut@colegiodenaujan.edu.ph', 'kimberly.illut@colegiodenaujan.edu.ph', '$2y$10$k44nz4c0.8j9w9DJ16hTH.oRh.25Tj3vGkSE65HqE2ev174Blwm/m', 'Kimberly Illut', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6132, 'roma.mendoza@colegiodenaujan.edu.ph', 'roma.mendoza@colegiodenaujan.edu.ph', '$2y$10$Savl7RaWU25zYkk8JD42jesFmcO.uG1pwPGl5rBgSAv0chk6Ag18W', 'Roma Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:26', '2026-03-23 14:41:26'),
(6133, 'evangeline.mojica@colegiodenaujan.edu.ph', 'evangeline.mojica@colegiodenaujan.edu.ph', '$2y$10$iJ734ASe6Ja0A0xy1eo5iu/j8mS9MvNy1OU0f5Ml9jjVopT54WxYy', 'Evangeline Mojica', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6134, 'carla.nineria@colegiodenaujan.edu.ph', 'carla.nineria@colegiodenaujan.edu.ph', '$2y$10$/jIK1LEldVEP2hSfpyaPh./aJXN1DS6.fABkSTwV00h9FynhKh0QO', 'Carla Nineria', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6135, 'kyla.oliveria@colegiodenaujan.edu.ph', 'kyla.oliveria@colegiodenaujan.edu.ph', '$2y$10$fH08KBtsneesU.GwuuSQieeX5DD3paU0ejJoR0KZ/hoGh7ykUn8Ye', 'Kyla Oliveria', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6136, 'mikayla.paala@colegiodenaujan.edu.ph', 'mikayla.paala@colegiodenaujan.edu.ph', '$2y$10$7TcNbfDMVCfPAfkhEjUMh.9lemwYx.oYqBorje5xlmPdrmy2i3tKm', 'Mikayla Paala', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6137, 'necilyn.ramos@colegiodenaujan.edu.ph', 'necilyn.ramos@colegiodenaujan.edu.ph', '$2y$10$7KeM9.Lp6fc0OH2NUJu2MuD6mdLKiX7SL4GqdTzMO0z/MbUZNTRde', 'Necilyn Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6138, 'mischell.velasquez@colegiodenaujan.edu.ph', 'mischell.velasquez@colegiodenaujan.edu.ph', '$2y$10$H4tjkjUnn/SH7l8EzybVVOLr9f5IpQOWZoSXFF4AdPDJEcyfhO1tC', 'Mischell Velasquez', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6139, 'emerson.adarlo@colegiodenaujan.edu.ph', 'emerson.adarlo@colegiodenaujan.edu.ph', '$2y$10$6RDKNtp1UK9n4BwceSmniOmQ1wG7pCU42ElLkFftXCYAmLN/m9G9i', 'Emerson Adarlo', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6140, 'shim.adarlo@colegiodenaujan.edu.ph', 'shim.adarlo@colegiodenaujan.edu.ph', '$2y$10$MB6.9UwqE4cX.FyHqGTBCOgDXUJoCTVO.N3gSiVZGroeOfaQi0suy', 'Shim Adarlo', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6141, 'cedrick.cardova@colegiodenaujan.edu.ph', 'cedrick.cardova@colegiodenaujan.edu.ph', '$2y$10$t9rzE1bbcnkGBirhCT1Ip.ODKQEGWa.8WtuQYCjGDAqU1q3x4w4Ve', 'Cedrick Cardova', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27');
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `must_change_password`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(6142, 'john.de.lemos@colegiodenaujan.edu.ph', 'john.de.lemos@colegiodenaujan.edu.ph', '$2y$10$/nSfRrutoAT590J1fns8xO8JThwmDtUy1D9WL68CWtvkVTx7HZ4KW', 'John De Lemos', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6143, 'reymar.faeldonia@colegiodenaujan.edu.ph', 'reymar.faeldonia@colegiodenaujan.edu.ph', '$2y$10$j2qaUTSmPkUEwoOhCJspHeCbayKJAgGHeOZVl0FLE5IqpAj/bFBgK', 'Reymar Faeldonia', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6144, 'john.fegidero@colegiodenaujan.edu.ph', 'john.fegidero@colegiodenaujan.edu.ph', '$2y$10$jeroy8EIwoWnZH.IyZE2MeBODD4RpOf3Zy7u3SxWAZTd.RCC01weW', 'John Fegidero', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6145, 'john.gaba@colegiodenaujan.edu.ph', 'john.gaba@colegiodenaujan.edu.ph', '$2y$10$/yC2FoV0sWbj0ys39IpD6eke4NUS6PBMx9L2P9ng1O.Ox5AzgW/ta', 'John Gaba', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6146, 'antonio.francisco@colegiodenaujan.edu.ph', 'antonio.francisco@colegiodenaujan.edu.ph', '$2y$10$j4uQkU65Bfmo/ghFz6K01OWr/UcdU4wuxNChKkjYOjuFiSFKxO35.', 'Antonio Francisco', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6147, 'karl.hardin@colegiodenaujan.edu.ph', 'karl.hardin@colegiodenaujan.edu.ph', '$2y$10$2lwshiQz80gyAVPkyl0BxOfywLN3n/rYNODIwMFwS.H5Y1Y9EpXIK', 'karl Hardin', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6148, 'prince.geneta@colegiodenaujan.edu.ph', 'prince.geneta@colegiodenaujan.edu.ph', '$2y$10$VcOR.3ujqro4c1j60brkW.tqLpNvWDfkSn4yPLsGU4EmJfdrtBXAy', 'Prince Geneta', 'student', 1, 'active', NULL, '2026-03-23 14:41:27', '2026-03-23 14:41:27'),
(6149, 'john.laredo@colegiodenaujan.edu.ph', 'john.laredo@colegiodenaujan.edu.ph', '$2y$10$Ie/JpxDPdLib/WvAJB6eiucZ5dsm.oS.LQXsSkO/d80sv9Tvce2FG', 'John Laredo', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6150, 'mc.masangkay@colegiodenaujan.edu.ph', 'mc.masangkay@colegiodenaujan.edu.ph', '$2y$10$zGyLuBdcVwW0uZTqRF9M0eqxqlDB.Yc.QftJ6gCD9.mqKTGrK6wVW', 'Mc Masangkay', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6151, 'aaron.manalo@colegiodenaujan.edu.ph', 'aaron.manalo@colegiodenaujan.edu.ph', '$2y$10$cu6DPZNu23iM/wBgUtKKvewLpFeYTglw//9IS37p9lJXe06VfGKWO', 'Aaron Manalo', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6152, 'great.mendoza@colegiodenaujan.edu.ph', 'great.mendoza@colegiodenaujan.edu.ph', '$2y$10$BiIhZut8arwp9HAmhJep6.1LnuCOJVAxKTYXYRpyzn3ExJybDqRHK', 'Great Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6153, 'jhon.oliveria@colegiodenaujan.edu.ph', 'jhon.oliveria@colegiodenaujan.edu.ph', '$2y$10$StG6vuhFDM.yYUzyiYG4qO2sPUCQTUpOnFx7RQioUtCGvc6zLn5am', 'Jhon Oliveria', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6154, 'kevin.rucio@colegiodenaujan.edu.ph', 'kevin.rucio@colegiodenaujan.edu.ph', '$2y$10$m2PskeryW3KY2DwY9WqcWuWBKt3QsMQwPGAhlNezhAjRxg3gHJoZa', 'Kevin Rucio', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6155, 'jhon.reyes@colegiodenaujan.edu.ph', 'jhon.reyes@colegiodenaujan.edu.ph', '$2y$10$E00W4QSw0cSV3AZIxepQX.muWaSqBKoOx172LVdRQemDvBKil3QTO', 'Jhon Reyes', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6156, 'arhizza.abanilla@colegiodenaujan.edu.ph', 'arhizza.abanilla@colegiodenaujan.edu.ph', '$2y$10$.5M5ZRvjyoyT87HMvPLd9ONwZXA8o3QTz3Lgz.YhYf/oIZzIdw28S', 'Arhizza Abanilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6157, 'carla.azucena@colegiodenaujan.edu.ph', 'carla.azucena@colegiodenaujan.edu.ph', '$2y$10$G/ANznNd2ZfhHE9zOd/DgODaXaX6O7EMKijWb5pFCnIt87n39qTTS', 'Carla Azucena', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6158, 'angel.cason@colegiodenaujan.edu.ph', 'angel.cason@colegiodenaujan.edu.ph', '$2y$10$ZWibCb2ieHNrPMhfSl1BeeYxXZLev.gcnbAVs0U6yDdQomGfUSw42', 'Angel Cason', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6159, 'kc.de.guzman@colegiodenaujan.edu.ph', 'kc.de.guzman@colegiodenaujan.edu.ph', '$2y$10$kM3cmi0SiM2iJqK/PTP0fO7h4DuKqh5cCiZa.3lXzB1o0gZ34pWQS', 'KC De Guzman', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6160, 'francene.delos.santos@colegiodenaujan.edu.ph', 'francene.delos.santos@colegiodenaujan.edu.ph', '$2y$10$sYKUoiRKT44efh95PHk1hOEH3Dlbq1oaEH0ham9mpCYxEcaZZIwS2', 'Francene Delos Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6161, 'shane.elio@colegiodenaujan.edu.ph', 'shane.elio@colegiodenaujan.edu.ph', '$2y$10$vXVK/nAG/o0Jy7LkO.SjyOFqdP54rIbUbgGQL2MvfzUsol9dkwN7G', 'Shane Elio', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6162, 'maria.garcia@colegiodenaujan.edu.ph', 'maria.garcia@colegiodenaujan.edu.ph', '$2y$10$bYU1.KEgNCphpSbC1lspXuXNJP0aAUy6ta5AkYZG1EkHQJihchfaO', 'Maria Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6163, 'shane.gardoce@colegiodenaujan.edu.ph', 'shane.gardoce@colegiodenaujan.edu.ph', '$2y$10$Ooz6HxJwIWPjcHrHCsCg6uBfrDVMDsa8Tw9k4CMa0NTPqrT/cUOF.', 'Shane Gardoce', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6164, 'janah.glor@colegiodenaujan.edu.ph', 'janah.glor@colegiodenaujan.edu.ph', '$2y$10$BvAovzFPgTrHU8vrxRwoHOu2KA22.2z.lkkwUQ.spojOkSttD7O76', 'Janah Glor', 'student', 1, 'active', NULL, '2026-03-23 14:41:28', '2026-03-23 14:41:28'),
(6165, 'catherine.gomez@colegiodenaujan.edu.ph', 'catherine.gomez@colegiodenaujan.edu.ph', '$2y$10$ZEOIW6Dtldsx8ymk0H/UxuTM7.qkjApaGemZQ9t9VZduDU5iXZagm', 'Catherine Gomez', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6166, 'april.llamoso@colegiodenaujan.edu.ph', 'april.llamoso@colegiodenaujan.edu.ph', '$2y$10$cp33.v5/tz3BafbyBgUZQ.UhJ5Cf3fc1cYw3F.AuveAAUlJ2pv6WC', 'April Llamoso', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6167, 'irene.loto@colegiodenaujan.edu.ph', 'irene.loto@colegiodenaujan.edu.ph', '$2y$10$AJHOsaoMqOkDP98TCHC/juY7.4genhz2H6mlgDQPWo556zGjAYp7a', 'Irene Loto', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6168, 'angela.lumanglas@colegiodenaujan.edu.ph', 'angela.lumanglas@colegiodenaujan.edu.ph', '$2y$10$L2HeD7TrYDnZt.vY9WrDaeMWKERfcUDKnODTU4x/08jpZrpeuPcXe', 'Angela Lumanglas', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6169, 'michelle.lumanglas@colegiodenaujan.edu.ph', 'michelle.lumanglas@colegiodenaujan.edu.ph', '$2y$10$8UVBSm4D8x.xAAA2gXhKPeiWIZGdbQ39SeaKyg4OabVY/TXclXJbG', 'Michelle Lumanglas', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6170, 'febelyn.magboo@colegiodenaujan.edu.ph', 'febelyn.magboo@colegiodenaujan.edu.ph', '$2y$10$cgq5i0k0NjmGIqqSBgMeF.ItkNym6Dw4v5EEE8jG0056tSV0/Lqti', 'Febelyn Magboo', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6171, 'chelo.marasigan@colegiodenaujan.edu.ph', 'chelo.marasigan@colegiodenaujan.edu.ph', '$2y$10$yOtsnEm.lPUcVA044jCBeuzW9LgTtHJIZLlG1C74QWXn97awRgkjC', 'Chelo Marasigan', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6172, 'joana.paala@colegiodenaujan.edu.ph', 'joana.paala@colegiodenaujan.edu.ph', '$2y$10$vO/TnezGD7Gmeis2eTCdgO9sVM/NNOXZZ9kUP5GDZSo8GWU8FR24G', 'Joana Paala', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6173, 'maria.pasado@colegiodenaujan.edu.ph', 'maria.pasado@colegiodenaujan.edu.ph', '$2y$10$tIkWTZaQ8Gmb6Xvw92cwte2t0oNUOTTCG8elpxqpvr1RopL55WMAS', 'Maria Pasado', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6174, 'danica.pederio@colegiodenaujan.edu.ph', 'danica.pederio@colegiodenaujan.edu.ph', '$2y$10$gowgIwGcMQ1jcKKpevZNpOvlSe0XhsveJTA42JdxDJ3dko7XGy1oC', 'Danica Pederio', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6175, 'angela.teves@colegiodenaujan.edu.ph', 'angela.teves@colegiodenaujan.edu.ph', '$2y$10$j87fWOquHuZLvFrwqSVpyuIcdWiCNvcBU.diRA2Zl.XjscS9I0AvW', 'Angela Teves', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6176, 'zairene.undaloc@colegiodenaujan.edu.ph', 'zairene.undaloc@colegiodenaujan.edu.ph', '$2y$10$HhLctIlgSTBhzmZ1E3TjKuXD9DFSX5eINkK7oLpyBiabNBKr/yWbG', 'Zairene Undaloc', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6177, 'john.cuasay@colegiodenaujan.edu.ph', 'john.cuasay@colegiodenaujan.edu.ph', '$2y$10$tZtyOdSpX4kDALYn7AweSOxz5kGAIU/TgPK1JK1ncH52hHcmGOD5a', 'John Cuasay', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6178, 'bert.ferrera@colegiodenaujan.edu.ph', 'bert.ferrera@colegiodenaujan.edu.ph', '$2y$10$kcLYpSjILie0IHDFJDzqM.X6CG78Et6FJxnPta668Pq9gfLRtQFlG', 'Bert Ferrera', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6179, 'rickson.ferry@colegiodenaujan.edu.ph', 'rickson.ferry@colegiodenaujan.edu.ph', '$2y$10$ovI6ibNqMxnXqY.r1imBUu3iWPGtl7JcjltSLlUkEDkASLNND75e6', 'Rickson Ferry', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6180, 'john.fransisco@colegiodenaujan.edu.ph', 'john.fransisco@colegiodenaujan.edu.ph', '$2y$10$pkTKCz2kODCXPaK0EhiFM.DCTML21YxSkffX3/otuF5OcN9RSDmmu', 'John Fransisco', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6181, 'allan.loto@colegiodenaujan.edu.ph', 'allan.loto@colegiodenaujan.edu.ph', '$2y$10$sbX9uOIvDvSqW.VEFz/y.eF63PjuvmYamABKcblybpQ7Nh40SGD4u', 'Allan Loto', 'student', 1, 'active', NULL, '2026-03-23 14:41:29', '2026-03-23 14:41:29'),
(6182, 'jhon.obando@colegiodenaujan.edu.ph', 'jhon.obando@colegiodenaujan.edu.ph', '$2y$10$acBWeI5T.MWO8NpSFGuplOwijLX7RabU/TauBwHFdX03Ew2mOf5ri', 'Jhon Obando', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6183, 'rodel.roldan@colegiodenaujan.edu.ph', 'rodel.roldan@colegiodenaujan.edu.ph', '$2y$10$0cleJkDIY.2dGcql0b4XreolB6V3IZgLuMiByTgHDDFRfD9ANSl3K', 'Rodel Roldan', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6184, 'ashlyn.abanilla@colegiodenaujan.edu.ph', 'ashlyn.abanilla@colegiodenaujan.edu.ph', '$2y$10$JRiuYgcKtp/47WfV/iIyBe39nK5kQn3a9uHIHIfErbR9w5eHTG47W', 'Ashlyn Abanilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6185, 'patricia.agoncillo@colegiodenaujan.edu.ph', 'patricia.agoncillo@colegiodenaujan.edu.ph', '$2y$10$/NAxazEd1FRqYAzgdkelvObwCqEvQxBZX1f.h5YAsB/Jt3qxaLFqq', 'Patricia Agoncillo', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6186, 'benelyn.aguho@colegiodenaujan.edu.ph', 'benelyn.aguho@colegiodenaujan.edu.ph', '$2y$10$zYZxQJAvT4r4ekwhg2jzUu90l0t1npRSz.fEKa1hIx7s9gVxzuJ8C', 'Benelyn Aguho', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6187, 'lynse.albufera@colegiodenaujan.edu.ph', 'lynse.albufera@colegiodenaujan.edu.ph', '$2y$10$xr3dvjA9Rte5t6myT8i4KOuJhQPQG5vOF6pE9ZVdHjwy2ESSq1Y/i', 'Lynse Albufera', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6188, 'lara.altamia@colegiodenaujan.edu.ph', 'lara.altamia@colegiodenaujan.edu.ph', '$2y$10$j1k5jNFGBRV1Jo0RC/rxDe22LIsTBKHnKrhyuOYZa0mCH/5Ri4Goe', 'Lara Altamia', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6189, 'crislyn.anyayahan@colegiodenaujan.edu.ph', 'crislyn.anyayahan@colegiodenaujan.edu.ph', '$2y$10$Cy0MJ636j5/0aNiEhvtx7OcWg8hvsouO7SuMGsvQgUpfeusKNpoUG', 'Crislyn Anyayahan', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6190, 'rocel.ara.nez@colegiodenaujan.edu.ph', 'rocel.ara.nez@colegiodenaujan.edu.ph', '$2y$10$Yjl3.5/6iEhpdbpNGhjvO.ALZyFtj.EQQBR2/pbjV4I7u4gQqY26C', 'Rocel Arañez', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6191, 'katrice.atienza@colegiodenaujan.edu.ph', 'katrice.atienza@colegiodenaujan.edu.ph', '$2y$10$q26PyFDGVVb6reYPkH/WJuFMZ2usa5bSu/mBYvCoOxapLgnUf82YG', 'Katrice Atienza', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6192, 'maica.bacal@colegiodenaujan.edu.ph', 'maica.bacal@colegiodenaujan.edu.ph', '$2y$10$RPcIZWIyUelbvzFE9lD.Qu0oC3yzfjaQoO8cVisjrDGktI4ausY2a', 'Maica Bacal', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6193, 'cherylyn.bacsa@colegiodenaujan.edu.ph', 'cherylyn.bacsa@colegiodenaujan.edu.ph', '$2y$10$OuQtQu3UoSNuzoJHqvxQLeFj1/n4PNnV0m23Yvsb0ClEdflYNQdcK', 'Cherylyn Bacsa', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6194, 'realyn.bercasi@colegiodenaujan.edu.ph', 'realyn.bercasi@colegiodenaujan.edu.ph', '$2y$10$euj2EE9aRwF6BYeFdF7Tz.5WhfvuaFCclC4RMszo6GnLbfoV7RFpi', 'Realyn Bercasi', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6195, 'elyza.buquis@colegiodenaujan.edu.ph', 'elyza.buquis@colegiodenaujan.edu.ph', '$2y$10$hY2th60k5Mf0HeZM3xEEL.x4EvLUHNEZvbl4QFUhynhs/qs67Rsku', 'Elyza Buquis', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6196, 'kim.caringal@colegiodenaujan.edu.ph', 'kim.caringal@colegiodenaujan.edu.ph', '$2y$10$S5g20u2P90Fl4BMYDg4Jq.jpe5BpuO7hRaimJ5EMEyxd..0FlS3OW', 'Kim Caringal', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6197, 'shane.dalisay@colegiodenaujan.edu.ph', 'shane.dalisay@colegiodenaujan.edu.ph', '$2y$10$fm7W1NVWVpqaHR9VJc7qRO.GycxZRp0iz.4BvpdVrmya8JOkTdXui', 'Shane Dalisay', 'student', 1, 'active', NULL, '2026-03-23 14:41:30', '2026-03-23 14:41:30'),
(6198, 'mariel.delos.santos@colegiodenaujan.edu.ph', 'mariel.delos.santos@colegiodenaujan.edu.ph', '$2y$10$MOaZFz38aqyWgs6IcsOtcuHheu8zRR8mJQoAFJuIK.5PWQMuragFq', 'Mariel Delos Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6199, 'angel.dimoampo@colegiodenaujan.edu.ph', 'angel.dimoampo@colegiodenaujan.edu.ph', '$2y$10$z42I.0n92bkZsWg4YJOzr.PQHZLoxHeXoOQaQXmab8ozXXti.fWZK', 'Angel Dimoampo', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6200, 'kristine.dris@colegiodenaujan.edu.ph', 'kristine.dris@colegiodenaujan.edu.ph', '$2y$10$vM.fh79/0lguxKNF9twxEuoOUVw/7KX5QnXla9hENXSD0/MTZx91W', 'Kristine Dris', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6201, 'rexlyn.eguillon@colegiodenaujan.edu.ph', 'rexlyn.eguillon@colegiodenaujan.edu.ph', '$2y$10$V71ESe7EMVNj1FQ6yjLWLeBQ1Laixb5o9iztjmcSxAbxetb3BaaX2', 'Rexlyn Eguillon', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6202, 'maricar.evangelista@colegiodenaujan.edu.ph', 'maricar.evangelista@colegiodenaujan.edu.ph', '$2y$10$054406ga83DHZFJ.z.8rtOQX.diHwZN9IP7IYSnrb9MYftRH2xdam', 'Maricar Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6203, 'chariz.fajardo@colegiodenaujan.edu.ph', 'chariz.fajardo@colegiodenaujan.edu.ph', '$2y$10$.Amemw4KMj9izU7XI5ANN.PXr3CWJhajzx9jarrtZIjXWh5I5MG1S', 'Chariz Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6204, 'hazel.feudo@colegiodenaujan.edu.ph', 'hazel.feudo@colegiodenaujan.edu.ph', '$2y$10$FZ4HkE.uYVDBUb1.PwfmfuoYmgj4SauXqARr.A/x6AG01kDLmZZKG', 'Hazel Feudo', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6205, 'marie.gado@colegiodenaujan.edu.ph', 'marie.gado@colegiodenaujan.edu.ph', '$2y$10$GymPjcHk9uvN92VjsOz.ceZkxyjUZpErxDjHBvY9OFa9jWajU5Kuy', 'Marie Gado', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6206, 'leah.galit@colegiodenaujan.edu.ph', 'leah.galit@colegiodenaujan.edu.ph', '$2y$10$7UgsQPkMiQu4VM3Bh46F2e2I3gJsWK28DPuUNewdhU93I4qJxiW2q', 'Leah Galit', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6207, 'aiexa.guira@colegiodenaujan.edu.ph', 'aiexa.guira@colegiodenaujan.edu.ph', '$2y$10$JTlhy/bJWefvVwH6gaH7cuse/TsvYBETO6Kywi3xYeeK/0Tmz8stC', 'Aiexa Guira', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6208, 'andrea.hernandez@colegiodenaujan.edu.ph', 'andrea.hernandez@colegiodenaujan.edu.ph', '$2y$10$SAKonkkZESb/opExh9OnPONflWDyTSEU92DO4HU0vU32igdQ4Vk7K', 'Andrea Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6209, 'eslley.hernandez@colegiodenaujan.edu.ph', 'eslley.hernandez@colegiodenaujan.edu.ph', '$2y$10$w/ZBhszoYIACjjzjnVquSu.CZ8E0KHuCFS3yGySkjvdMFRoIpq/Mq', 'Eslley Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6210, 'jazleen.llamoso@colegiodenaujan.edu.ph', 'jazleen.llamoso@colegiodenaujan.edu.ph', '$2y$10$bh6vjXip4n3SSUwz2PhwOuA6c1wMq7UAeh67h76PktkWiLAldjK82', 'Jazleen Llamoso', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6211, 'joan.lomio@colegiodenaujan.edu.ph', 'joan.lomio@colegiodenaujan.edu.ph', '$2y$10$gwRtwRL2LXIoMwoPGufQbupv0fNPl4fIOEmKoVHHMxyck9MbGZCPO', 'Joan Lomio', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6212, 'kriselle.mabuti@colegiodenaujan.edu.ph', 'kriselle.mabuti@colegiodenaujan.edu.ph', '$2y$10$/dYEwjT/6M98mFme5rXvk.OAATDm5CmQZkLDgdRIIFCBpwRT2d9Ge', 'Kriselle Mabuti', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6213, 'angel.mascarinas@colegiodenaujan.edu.ph', 'angel.mascarinas@colegiodenaujan.edu.ph', '$2y$10$a5rlnbaunbJisuwC0x86kuE8K0aBsJgFHzKNMTyd/j/BwE/QdyS.y', 'Angel Mascarinas', 'student', 1, 'active', NULL, '2026-03-23 14:41:31', '2026-03-23 14:41:31'),
(6214, 'hannah.melgar@colegiodenaujan.edu.ph', 'hannah.melgar@colegiodenaujan.edu.ph', '$2y$10$wss6q4ydhwceYL8PS0Dhyeiu3vPLNxZgDplTi0Jz/gaOBFfxT1U1.', 'Hannah Melgar', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6215, 'rexy.mingo@colegiodenaujan.edu.ph', 'rexy.mingo@colegiodenaujan.edu.ph', '$2y$10$tHH0R7XJIJD9ZLPQWTqt/.qhMjSP9FLMUradaNefylOxXxXTRyz4m', 'Rexy Mingo', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6216, 'precious.moya@colegiodenaujan.edu.ph', 'precious.moya@colegiodenaujan.edu.ph', '$2y$10$HpVErdoowqH7.N.vBnEatuV2jdaFrOwuwHClHoYlAvUU0dW7iTQO2', 'Precious Moya', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6217, 'cherese.nao@colegiodenaujan.edu.ph', 'cherese.nao@colegiodenaujan.edu.ph', '$2y$10$y/3yk4BKamlZvq/VZNb5eeSlor6i/rqcQZoNC.3pJJvD66NwltvDy', 'Cherese Nao', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6218, 'margie.nu.nez@colegiodenaujan.edu.ph', 'margie.nu.nez@colegiodenaujan.edu.ph', '$2y$10$4i23u9vG6/qRN7yac.5RMOJZAwFA8XMOT2027fiCFB7YdXeRyiz5e', 'Margie Nuñez', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6219, 'hazel.panganiban@colegiodenaujan.edu.ph', 'hazel.panganiban@colegiodenaujan.edu.ph', '$2y$10$tNEini21swR9b.s1lyM0QOAzMahr8ntAS5OQTFjMbJJWG5/9uRZ12', 'Hazel Panganiban', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6220, 'angela.papasin@colegiodenaujan.edu.ph', 'angela.papasin@colegiodenaujan.edu.ph', '$2y$10$PozpFB5JLQHg9zCC.X0hBej/HSQRuLG9T47mm3UD1OJW9eiJyOYny', 'Angela Papasin', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6221, 'jasmine.prangue@colegiodenaujan.edu.ph', 'jasmine.prangue@colegiodenaujan.edu.ph', '$2y$10$xeg3OErwGx68iOrScLWGZe5CkGksGB25rLZtB2H7RBcWyqxUcQsbW', 'Jasmine Prangue', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6222, 'jeyzelle.rellora@colegiodenaujan.edu.ph', 'jeyzelle.rellora@colegiodenaujan.edu.ph', '$2y$10$o/oNpwmW7ocTn9GOguybneD6hhEcHFe98FgVgiAJkPF/kyQU44DT2', 'Jeyzelle Rellora', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6223, 'katrina.rufino@colegiodenaujan.edu.ph', 'katrina.rufino@colegiodenaujan.edu.ph', '$2y$10$C953P91wiJb0g6g1tCeaGO.CRbU6jiBh67C7DQvyo26UzMbAAEQ8i', 'Katrina Rufino', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6224, 'ni.na.sanchez@colegiodenaujan.edu.ph', 'ni.na.sanchez@colegiodenaujan.edu.ph', '$2y$10$LSUZFexAvrTFugBgrExQ/e/HmqSGK5gus55q8rfranAuta4VmdPcu', 'Niña Sanchez', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6225, 'edcel.santillan@colegiodenaujan.edu.ph', 'edcel.santillan@colegiodenaujan.edu.ph', '$2y$10$1JWvzrJF7gULcq7Go70nnOpdziWymmEuOwxgktmJdPqg.vkwUfegW', 'Edcel Santillan', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6226, 'mary.sara@colegiodenaujan.edu.ph', 'mary.sara@colegiodenaujan.edu.ph', '$2y$10$5F0NSkiHMVVkFKfX76MzQeMDGM7Bbi17HIzndi2TVFtRjGlg8b1Eu', 'Mary Sara', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6227, 'cynthia.torres@colegiodenaujan.edu.ph', 'cynthia.torres@colegiodenaujan.edu.ph', '$2y$10$hWszkSUhPhFiU8MjbadmNOKrWVVnuY0qTZ2IyZWOqD8Afg1k8ljye', 'Cynthia Torres', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6228, 'jolie.tugmin@colegiodenaujan.edu.ph', 'jolie.tugmin@colegiodenaujan.edu.ph', '$2y$10$vVri8XWkfRo76Pyv14sdHef/xaVMtTa7JAuf8wA3Qj.qqDTkSrz96', 'Jolie Tugmin', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6229, 'lesley.villanueva@colegiodenaujan.edu.ph', 'lesley.villanueva@colegiodenaujan.edu.ph', '$2y$10$ERwgNjjYraRBu3PJB2XEYe1GVBykc/Obl8XBnJk1wsOZ4uCfiWeam', 'Lesley Villanueva', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6230, 'lany.ylagan@colegiodenaujan.edu.ph', 'lany.ylagan@colegiodenaujan.edu.ph', '$2y$10$ZHy8FVn64PFouRYbe1p75uAt9Nh2UUD9i3CGnw5gHM5T8HwJTlhEu', 'Lany Ylagan', 'student', 1, 'active', NULL, '2026-03-23 14:41:32', '2026-03-23 14:41:32'),
(6231, 'marvin.caraig@colegiodenaujan.edu.ph', 'marvin.caraig@colegiodenaujan.edu.ph', '$2y$10$Hq5EvXR14zl7WOWipwATuext74hIIAr81JfkLSWdv2KYex87IBVW2', 'Marvin Caraig', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6232, 'denniel.delos.santos@colegiodenaujan.edu.ph', 'denniel.delos.santos@colegiodenaujan.edu.ph', '$2y$10$jbMDOoddI0jWFLkpVHLute0brLZ9/vlvqNXwxaOPvW7iOsUteFbRK', 'Denniel Delos Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6233, 'alex.magsisi@colegiodenaujan.edu.ph', 'alex.magsisi@colegiodenaujan.edu.ph', '$2y$10$dnkIMvAS2HcNcNJc6AcMsOq9IA8spkTe2y8NrT0woCUJDPd3FN0RW', 'Alex Magsisi', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6234, 'jan.manalo@colegiodenaujan.edu.ph', 'jan.manalo@colegiodenaujan.edu.ph', '$2y$10$zZGw7vLxQqX1jtCd4DtF4ut/ZklWSEHx9nI7jjp4brBRDvbORBLeC', 'Jan Manalo', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6235, 'aj.masangkay@colegiodenaujan.edu.ph', 'aj.masangkay@colegiodenaujan.edu.ph', '$2y$10$IJJOvzbwe6zLE9sOXPUMX.Agide/8NthBAX41004kRBVwhUZG5P6q', 'AJ Masangkay', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6236, 'john.roldan@colegiodenaujan.edu.ph', 'john.roldan@colegiodenaujan.edu.ph', '$2y$10$Gipe.GlcdsLcL4sLoLpXPOwd7MY2tz9SOWAzUPOtQxNXqLX4IsMjS', 'John Roldan', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6237, 'ronald.ta.nada@colegiodenaujan.edu.ph', 'ronald.ta.nada@colegiodenaujan.edu.ph', '$2y$10$V30OG8Rsn7RMr2kbc8iio.GBs10AvCS4fsrj9aB9FneH6xL.M8K2K', 'Ronald Tañada', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6238, 'd.jay.teriompo@colegiodenaujan.edu.ph', 'd.jay.teriompo@colegiodenaujan.edu.ph', '$2y$10$4OjVSYi7MiHr9QTF6wUN0Oeoa/7KHX9o56vuO8/YNd7OsY8UzP69a', 'D-Jay Teriompo', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6239, 'marsha.azucena@colegiodenaujan.edu.ph', 'marsha.azucena@colegiodenaujan.edu.ph', '$2y$10$IhgNAWniTuzu4UPNx/MbR.6iwd9pcSZ0a4GzDRZAb./.fRlakJWWO', 'Marsha Azucena', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6240, 'melsan.aday@colegiodenaujan.edu.ph', 'melsan.aday@colegiodenaujan.edu.ph', '$2y$10$229IFVA40SN7p1A84RFlC.Wp1rw9UdpdIGpbGRrkK0/3QhXc8RC36', 'Melsan Aday', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6241, 'jonice.alturas@colegiodenaujan.edu.ph', 'jonice.alturas@colegiodenaujan.edu.ph', '$2y$10$kkKEIRl6MPoVzWGwqQwcNuAbUsJIB4f.NkVfrcZAlYvNNEpNmIZZu', 'Jonice Alturas', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6242, 'precious.apil@colegiodenaujan.edu.ph', 'precious.apil@colegiodenaujan.edu.ph', '$2y$10$HCDWOs1NkocoVkoAgYS/DuWWBv2UlsOiHPUj4HV48u8n5U6BUADJW', 'Precious Apil', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6243, 'ludelyn.belbes@colegiodenaujan.edu.ph', 'ludelyn.belbes@colegiodenaujan.edu.ph', '$2y$10$bO/K5BGkCKX8/2yEmv/xe.HrsDXJZU2BlSjfdUiU6N7U5bE9iRlf6', 'Ludelyn Belbes', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6244, 'princess.cabasi@colegiodenaujan.edu.ph', 'princess.cabasi@colegiodenaujan.edu.ph', '$2y$10$qJZ/qrvAJiVb4PdDagncqejNBgnUc3ex5ilcgM777xQEPOop28QIO', 'Princess Cabasi', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6245, 'charlaine.de.belen@colegiodenaujan.edu.ph', 'charlaine.de.belen@colegiodenaujan.edu.ph', '$2y$10$fzNGKAdzN4fmpB4qT/n/2OkauK82pnfkSQjtzTjd7kIhKxibGKUbO', 'Charlaine De Belen', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6246, 'arjean.de.castro@colegiodenaujan.edu.ph', 'arjean.de.castro@colegiodenaujan.edu.ph', '$2y$10$LviOHZfm5LQXj6HGi9qezug52MQ45HgktJX67./JT1a44sSFpLnV.', 'Arjean De Castro', 'student', 1, 'active', NULL, '2026-03-23 14:41:33', '2026-03-23 14:41:33'),
(6247, 'precious.de.guzman@colegiodenaujan.edu.ph', 'precious.de.guzman@colegiodenaujan.edu.ph', '$2y$10$scaR0tz0eIeuDOyGO7BnrONcOetYistQ2dISrnk/V32HWCBpUqkKy', 'Precious De Guzman', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6248, 'marina.de.luzon@colegiodenaujan.edu.ph', 'marina.de.luzon@colegiodenaujan.edu.ph', '$2y$10$dclSl.z1Ol3gqxV9ru.87uYFBRVg.5Ji6KkOJPyO7vjmuFVXpTY2K', 'Marina De Luzon', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6249, 'nesvita.dorias@colegiodenaujan.edu.ph', 'nesvita.dorias@colegiodenaujan.edu.ph', '$2y$10$sfsCUBEK9cyqF/.lOZlgsuqdd8vdd.h7ZwH7Ub3lQRGUO4WquDE9C', 'Nesvita Dorias', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6250, 'stella.flores@colegiodenaujan.edu.ph', 'stella.flores@colegiodenaujan.edu.ph', '$2y$10$JulAjYEnf6tbBwaGEPVqd.5CBbyWssynttjzfKXKrNIH2Cq135J.G', 'Stella Flores', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6251, 'arlene.gaba@colegiodenaujan.edu.ph', 'arlene.gaba@colegiodenaujan.edu.ph', '$2y$10$JrfjKGKeWRcG3/ZrumQ6v.xE.PoTpbFoK.SDU2k9YUfwQyo.J4B0K', 'Arlene Gaba', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6252, 'jay.ann.jamilla@colegiodenaujan.edu.ph', 'jay.ann.jamilla@colegiodenaujan.edu.ph', '$2y$10$R27yFyxsXyn6jS8wnTmw9e26ZN1bumwNKuVAx4QwgJ7t1Nsga7D8S', 'Jay-Ann Jamilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6253, 'mikaela.layson@colegiodenaujan.edu.ph', 'mikaela.layson@colegiodenaujan.edu.ph', '$2y$10$Aj..ZUArk7AMBSibT3A8sOA6A29z28L0UIspC8Vfpsqc8CwJI.lFa', 'Mikaela Layson', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6254, 'christine.lomio@colegiodenaujan.edu.ph', 'christine.lomio@colegiodenaujan.edu.ph', '$2y$10$r1e9rCMp8znUq7jOvI4q1.NCQnm5azjoo/KLI9S5AsW12DQhHONlO', 'Christine Lomio', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6255, 'ariane.magboo@colegiodenaujan.edu.ph', 'ariane.magboo@colegiodenaujan.edu.ph', '$2y$10$/D/RLyXuuglhCa0Vn5ExLujy11kvwQBUclZmNWJiDb50hv1eqlJ..', 'Ariane Magboo', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6256, 'nerissa.magsisi@colegiodenaujan.edu.ph', 'nerissa.magsisi@colegiodenaujan.edu.ph', '$2y$10$.o.SAYqkvJl3RltqfUrLweh7bPWJ1y4gsZXkfNdV5NV8YBk4QHQ8m', 'Nerissa Magsisi', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6257, 'keycel.manalo@colegiodenaujan.edu.ph', 'keycel.manalo@colegiodenaujan.edu.ph', '$2y$10$mSox5gtxTFpLIcOHDzlDueZpcZOR.J6Gag4NRRxBBqC6upDpU9h5O', 'Keycel Manalo', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6258, 'grace.manibo@colegiodenaujan.edu.ph', 'grace.manibo@colegiodenaujan.edu.ph', '$2y$10$OMtVhVZq/IEiG6FeBUOJL.01jtfXPhZ1hw1Rx.J7rpLkAkM9qcc9.', 'Grace Manibo', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6259, 'lovelyn.marcos@colegiodenaujan.edu.ph', 'lovelyn.marcos@colegiodenaujan.edu.ph', '$2y$10$UQ7QEplrW.9LmGNGFN3UIuVoIGYXyC9vXMNoTGOyqOF1dXOVJcbOm', 'Lovelyn Marcos', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6260, 'shenna.obando@colegiodenaujan.edu.ph', 'shenna.obando@colegiodenaujan.edu.ph', '$2y$10$tSxHABQmaXwl3ZiLsMSaVu4LPjIob69KBDsv8XG5a1no89HaDrlou', 'Shenna Obando', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6261, 'myzell.ramos@colegiodenaujan.edu.ph', 'myzell.ramos@colegiodenaujan.edu.ph', '$2y$10$W7fW2uKOhrcpEBRNQ2hB8uST1vcJjgdTjiXgbIs/eBwzCKLW/xIXe', 'Myzell Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6262, 'shella.ramos@colegiodenaujan.edu.ph', 'shella.ramos@colegiodenaujan.edu.ph', '$2y$10$UAP93VKoZ3KXOGDl8NiO6.6FmGADMRWVOPNX0dsqMdZVb3zcIWs2O', 'Shella Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6263, 'desiree.raymundo@colegiodenaujan.edu.ph', 'desiree.raymundo@colegiodenaujan.edu.ph', '$2y$10$eDuI7zlr8RoG8pl9/NKQkekb01O.VEQYNpUZprho9frzrAgLzDjgu', 'Desiree Raymundo', 'student', 1, 'active', NULL, '2026-03-23 14:41:34', '2026-03-23 14:41:34'),
(6264, 'romelyn.rocha@colegiodenaujan.edu.ph', 'romelyn.rocha@colegiodenaujan.edu.ph', '$2y$10$egjkgSMXhMhZX8FkVaSKnu5XWTooSw7vqs8D85OLilhq/4HFyuQ2O', 'Romelyn Rocha', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6265, 'john.bacsa@colegiodenaujan.edu.ph', 'john.bacsa@colegiodenaujan.edu.ph', '$2y$10$CcFqkIHair2PIPlt3StYFejv903wkGJl7ww8GBpfesbYBNLqwv8S.', 'John Bacsa', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6266, 'john.balansag@colegiodenaujan.edu.ph', 'john.balansag@colegiodenaujan.edu.ph', '$2y$10$L0s5TswpLR.sDtCqdxGlj.l3odkoB6TB6FgGa3TQAaUASJ9SUD/Ay', 'John Balansag', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6267, 'raphael.bugayong@colegiodenaujan.edu.ph', 'raphael.bugayong@colegiodenaujan.edu.ph', '$2y$10$1I92UaCM0169wb9L1I/FiuwK8q.ver.hTr63Cy2H..BkQMMfupaY2', 'Raphael Bugayong', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6268, 'mark.bunag@colegiodenaujan.edu.ph', 'mark.bunag@colegiodenaujan.edu.ph', '$2y$10$U2g/r8WZR8ArSGidMNSAruSCaEIVsj6bshUda8Ug5UnzAnZENiksi', 'Mark Bunag', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6269, 'alvin.corona@colegiodenaujan.edu.ph', 'alvin.corona@colegiodenaujan.edu.ph', '$2y$10$ae6ltmm8Dd4Ns94aEaDI2.gI/vsJaIH8ZiBuvEgp5mhoAkM9se4Ti', 'Alvin Corona', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6270, 'mark.cueto@colegiodenaujan.edu.ph', 'mark.cueto@colegiodenaujan.edu.ph', '$2y$10$f6zwH16vyHBjHDeztRjuMOIqwdl21zoL/lp7DKNQ.73alt.61fP8K', 'Mark Cueto', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6271, 'charles.dimailig@colegiodenaujan.edu.ph', 'charles.dimailig@colegiodenaujan.edu.ph', '$2y$10$UD0Adx2J61BGjunJJpLhk.Yd54dNK71AWyT3AtxRB/mDV2H.EeWB2', 'Charles Dimailig', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6272, 'airon.evangelista@colegiodenaujan.edu.ph', 'airon.evangelista@colegiodenaujan.edu.ph', '$2y$10$Jm/mKWsx8dhxENFvJZdFXuRvu.Q.5jPsfC/9p4iacqmMCNApWKPGm', 'Airon Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6273, 'gino.genabe@colegiodenaujan.edu.ph', 'gino.genabe@colegiodenaujan.edu.ph', '$2y$10$7BsIoqAbDpeJY6sMGV9N2.k20c.P98wz.KugoPYwXG1.tHQpkbKDu', 'Gino Genabe', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6274, 'miklo.lumanglas@colegiodenaujan.edu.ph', 'miklo.lumanglas@colegiodenaujan.edu.ph', '$2y$10$68.X1MYfhRUDI43oCFbGIO.RBNJU/DmDDYnF.MXr/s6O6ypKRVppG', 'Miklo Lumanglas', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6275, 'ramcil.macapuno@colegiodenaujan.edu.ph', 'ramcil.macapuno@colegiodenaujan.edu.ph', '$2y$10$LlDdpo9I9MvjZMaxZ7K6bOG2hqeZjW/WP7WRMKoIsw6r3qDJiX/6u', 'Ramcil Macapuno', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6276, 'florence.macalelong@colegiodenaujan.edu.ph', 'florence.macalelong@colegiodenaujan.edu.ph', '$2y$10$riAhHGO5yb3.ya/YOhnI1ecN4NOM7zHwNTv/gfioXfiCGAXtur1l6', 'Florence Macalelong', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6277, 'patrick.matanguihan@colegiodenaujan.edu.ph', 'patrick.matanguihan@colegiodenaujan.edu.ph', '$2y$10$GdEPUtlGpRwjILCw5cxfcOop1EoX8/vWxLQHaqqo5R3HLcRUDCpmu', 'Patrick Matanguihan', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6278, 'dranzel.miranda@colegiodenaujan.edu.ph', 'dranzel.miranda@colegiodenaujan.edu.ph', '$2y$10$OBu.76erHMNdqdy9urdaZOfvNECEia3t0Iti.JY9d.WF3nwcV8.eO', 'Dranzel Miranda', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6279, 'carlo.mondragon@colegiodenaujan.edu.ph', 'carlo.mondragon@colegiodenaujan.edu.ph', '$2y$10$T78pZAJCCoL2n/dL0tI2CuheWR/aLB.OtZsNDWZLdj3RWaMmNalK2', 'Carlo Mondragon', 'student', 1, 'active', NULL, '2026-03-23 14:41:35', '2026-03-23 14:41:35'),
(6280, 'john.montianto@colegiodenaujan.edu.ph', 'john.montianto@colegiodenaujan.edu.ph', '$2y$10$2MV/wfG.ddQcIq9UxM.M0uInj4G65BD.n7jJxeb2K41QvCcSzUDiC', 'John Montianto', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6281, 'christian.moreno@colegiodenaujan.edu.ph', 'christian.moreno@colegiodenaujan.edu.ph', '$2y$10$CPpb5WwRQhTHa32ApKowz.VK.k7OxPVvPAgQ.G5faWwfBTEjbsNyy', 'Christian Moreno', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6282, 'amiel.pantua@colegiodenaujan.edu.ph', 'amiel.pantua@colegiodenaujan.edu.ph', '$2y$10$f2KtVzqoY.voZ.q6evlbkeQkmiwBlNq8k4fOyw2xXQ7VaylhH8BBC', 'Amiel Pantua', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6283, 'james.paradijas@colegiodenaujan.edu.ph', 'james.paradijas@colegiodenaujan.edu.ph', '$2y$10$GVYg4Gd0GANgrw1p7S1QL.rcpO3evuYJihvWXvhkd.tv4dmeMEuAS', 'James Paradijas', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6284, 'jhezreel.pastorfide@colegiodenaujan.edu.ph', 'jhezreel.pastorfide@colegiodenaujan.edu.ph', '$2y$10$nYLNuxOOg8u04PrfFSFbmO9yuILy4MAPwBkRrIEeRTPyDHF7PHHZ2', 'Jhezreel Pastorfide', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6285, 'matt.reyes@colegiodenaujan.edu.ph', 'matt.reyes@colegiodenaujan.edu.ph', '$2y$10$t./ImlB2dLXSbhr.0amh/O0CDg.UXZQ0y60YiWAjWBZSWzuZcR9vm', 'Matt Reyes', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6286, 'merwin.santos@colegiodenaujan.edu.ph', 'merwin.santos@colegiodenaujan.edu.ph', '$2y$10$l5OReJ.zS9JqWBfyNrssGuTOckOV90wCknG68AenOIBX2qKXK1Uvu', 'Merwin Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6287, 'benjamin.sarvida@colegiodenaujan.edu.ph', 'benjamin.sarvida@colegiodenaujan.edu.ph', '$2y$10$1O6N8kLhpmIodPDzi02L0OA/6AbJYXwCxM2myBVqDTXh.a38lnMXG', 'Benjamin Sarvida', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6288, 'jerus.savariz@colegiodenaujan.edu.ph', 'jerus.savariz@colegiodenaujan.edu.ph', '$2y$10$p4VaDi6/BRPsp8Xp1TcBRe5sVSUAGCB7svJsHu6MJ2eP0jbAMnJN2', 'Jerus Savariz', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6289, 'gerson.urdanza@colegiodenaujan.edu.ph', 'gerson.urdanza@colegiodenaujan.edu.ph', '$2y$10$ANVqnrIyLE9NaP1OprQnbuUMTQNQra3n948EIZAvjyWApEJ8rr3Ku', 'Gerson Urdanza', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6290, 'jyrus.ylagan@colegiodenaujan.edu.ph', 'jyrus.ylagan@colegiodenaujan.edu.ph', '$2y$10$yXwjiVhR9nASTwjebmtNMuGswYATERJ87Ef8i/GgfBbUQxceOmSb2', 'Jyrus Ylagan', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6291, 'jonah.anyayahan@colegiodenaujan.edu.ph', 'jonah.anyayahan@colegiodenaujan.edu.ph', '$2y$10$Y0XzCe4rOcl26wy1E9DbE.HKVkRLwO9SPMD1nfYAcWGxgc3RswnOC', 'Jonah Anyayahan', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6292, 'leica.banila@colegiodenaujan.edu.ph', 'leica.banila@colegiodenaujan.edu.ph', '$2y$10$XZkq0pxvsMoqXaZI.79v6OFDmOZjW54wvl7JZXr9zMZzxAkS2bahq', 'Leica Banila', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6293, 'juvylyn.basa@colegiodenaujan.edu.ph', 'juvylyn.basa@colegiodenaujan.edu.ph', '$2y$10$rnpvosW7lLqfxkQmkW/Rg.tTAWHBm2HtM3FDQEqvPMh2wovYJ2FC2', 'Juvylyn Basa', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6294, 'rashele.delgaco@colegiodenaujan.edu.ph', 'rashele.delgaco@colegiodenaujan.edu.ph', '$2y$10$6.DonC//2y1gBqHfMKSlwuVDcKaXnuAZsfxbaqOLR0JIrGdoT8NWG', 'Rashele Delgaco', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6295, 'cristal.de.chusa@colegiodenaujan.edu.ph', 'cristal.de.chusa@colegiodenaujan.edu.ph', '$2y$10$9GPnnOI6BSBYKpRSFh9/zuRMN8JUxhGiZKL3qaGJVHKPHeGiSY35y', 'Cristal De Chusa', 'student', 1, 'active', NULL, '2026-03-23 14:41:36', '2026-03-23 14:41:36'),
(6296, 'jaime.evora@colegiodenaujan.edu.ph', 'jaime.evora@colegiodenaujan.edu.ph', '$2y$10$pGrWhSWvhvA2MNVYxHqxZ.Nz0lRziR3FOrTzlWCVg2cpPlU6JonpG', 'Jaime Evora', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6297, 'jeanlyn.garcia@colegiodenaujan.edu.ph', 'jeanlyn.garcia@colegiodenaujan.edu.ph', '$2y$10$VLBrRiJwNdvMOkyi.N4jdOPE9t5oyKJIGUbpuP13yQJJXvZPDCWUu', 'Jeanlyn Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6298, 'baby.godoy@colegiodenaujan.edu.ph', 'baby.godoy@colegiodenaujan.edu.ph', '$2y$10$yZwyA90SKyqa1Bxff72yGuHvRlF53s7lNKsAknhzz7ef8vNtwWwiy', 'Baby Godoy', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6299, 'herjane.gozar@colegiodenaujan.edu.ph', 'herjane.gozar@colegiodenaujan.edu.ph', '$2y$10$00CbP/hsN.Oad5HQkzJlyuSbqlljgluCyyRzoDFRuw3/dhG3bHDGa', 'Herjane Gozar', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6300, 'zyra.gutierrez@colegiodenaujan.edu.ph', 'zyra.gutierrez@colegiodenaujan.edu.ph', '$2y$10$j1.in4NiUWGv5Mj1qLCgfuY3orO.C50JJwM1VYGCUplaJJ2IXCL.S', 'Zyra Gutierrez', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6301, 'angielene.landicho@colegiodenaujan.edu.ph', 'angielene.landicho@colegiodenaujan.edu.ph', '$2y$10$RmKGjwfkHW/vW5zhckQo3OuxKR4YgBw.PXBJRLg7u/mGZFWKba2iO', 'Angielene Landicho', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6302, 'laila.limun@colegiodenaujan.edu.ph', 'laila.limun@colegiodenaujan.edu.ph', '$2y$10$xgfVazWho6GJ4danucZfqePHiFOBaPw2gPt0OG4HNARlBV8t.dWDe', 'Laila Limun', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6303, 'jennie.lopez@colegiodenaujan.edu.ph', 'jennie.lopez@colegiodenaujan.edu.ph', '$2y$10$wT7HemoGMg250opZhbHcQ.fSej2IuuRcx5iPb1Jz2j0HcqHeT4jJq', 'Jennie Lopez', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6304, 'judy.madrigal@colegiodenaujan.edu.ph', 'judy.madrigal@colegiodenaujan.edu.ph', '$2y$10$TKjNGVzzHrUb5xK7KjUF9OJDp5CJsn09yPtbHt5JXY9ji1W.OV99S', 'Judy Madrigal', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6305, 'maan.masangkay@colegiodenaujan.edu.ph', 'maan.masangkay@colegiodenaujan.edu.ph', '$2y$10$8vM5MkVYVVpf2WVvwzqDpOS0aa5vTdscQvUfxHKuB1Pek61nbTmR6', 'Maan Masangkay', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6306, 'genesis.mendoza@colegiodenaujan.edu.ph', 'genesis.mendoza@colegiodenaujan.edu.ph', '$2y$10$6X0.riDZ4K5rTZjYwlUwzOqtlUGf5gKZFTeButSdjsCG7BrqFvc0W', 'Genesis Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6307, 'marian.mendoza@colegiodenaujan.edu.ph', 'marian.mendoza@colegiodenaujan.edu.ph', '$2y$10$nLxkYHX00R4Credn7agyjOb69PONILIEtDVgPFf6xphuVBd9sQ1Xm', 'Marian Mendoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6308, 'lailin.obando@colegiodenaujan.edu.ph', 'lailin.obando@colegiodenaujan.edu.ph', '$2y$10$D7rJ1CXjmw4fUbN1/v2OdeMHMmAKkG9/SY7SZd/aRbeRKmyzzgGjC', 'Lailin Obando', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6309, 'kyla.rucio@colegiodenaujan.edu.ph', 'kyla.rucio@colegiodenaujan.edu.ph', '$2y$10$utYjHD.jOfd0L6C6rQeYk.IiucegayLSf2bfWnn4ZeZlKqQsxltYa', 'Kyla Rucio', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6310, 'lyn.velasquez@colegiodenaujan.edu.ph', 'lyn.velasquez@colegiodenaujan.edu.ph', '$2y$10$.QRfG77JhQM8dvV15gaJzuwIu2ss..8E8NPkBpPji0AGxlv/Xdbl.', 'Lyn Velasquez', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6311, 'jhon.acojedo@colegiodenaujan.edu.ph', 'jhon.acojedo@colegiodenaujan.edu.ph', '$2y$10$/jmOT/RZac8Sdmm1/1A8nOt8Qh5T.y1sQYErZr7jS8T5.wGT9y70C', 'Jhon Acojedo', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6312, 'sherwin.calibot@colegiodenaujan.edu.ph', 'sherwin.calibot@colegiodenaujan.edu.ph', '$2y$10$AEtlxaBbJLQkyLJCP1EXweqwhjkricExFMTEcNPH4WUGfuLQ5s9Eu', 'Sherwin Calibot', 'student', 1, 'active', NULL, '2026-03-23 14:41:37', '2026-03-23 14:41:37'),
(6313, 'joriz.collado@colegiodenaujan.edu.ph', 'joriz.collado@colegiodenaujan.edu.ph', '$2y$10$WQoed856jH2vopmE2t37GezebIc8lhPcQWAEFLSg50Nj1Nt642h/e', 'Joriz Collado', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6314, 'mark.dalay@colegiodenaujan.edu.ph', 'mark.dalay@colegiodenaujan.edu.ph', '$2y$10$H6NFxoveXBXzTgXu1dJsWeZo26WjOjkdWj/Mj7NRQPJomQv/U7qnW', 'Mark Dalay', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6315, 'adrian.dilao@colegiodenaujan.edu.ph', 'adrian.dilao@colegiodenaujan.edu.ph', '$2y$10$KNBBuQWu6GnnoNyT06nJrOeGXyFej0waJcmoDXJDg.BZZAJlubCEm', 'Adrian Dilao', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6316, 'mc.fabellon@colegiodenaujan.edu.ph', 'mc.fabellon@colegiodenaujan.edu.ph', '$2y$10$rzq5MshgWCKqSzpvTemUauZzAvQmqMNO3N/bRyPcYdEaKEMGSNe9W', 'Mc Fabellon', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6317, 'john.fernandez@colegiodenaujan.edu.ph', 'john.fernandez@colegiodenaujan.edu.ph', '$2y$10$qEPZJe0iyqzMLi4bKjUo3eFDw6VRNDF3BmsFAGP.HrUldLBJOPCE6', 'John Fernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6318, 'mark.fransisco@colegiodenaujan.edu.ph', 'mark.fransisco@colegiodenaujan.edu.ph', '$2y$10$xvTmhJ8qZYszdxIxEkDdI.uqqaKo.l5jL9TicSf3UABdG8aMwAUd2', 'Mark Fransisco', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6319, 'kian.gale@colegiodenaujan.edu.ph', 'kian.gale@colegiodenaujan.edu.ph', '$2y$10$fdA2MY8no2471RwR6bBbyumJv5FZP5sezavu8f8feh4f6WNovcOyS', 'Kian Gale', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6320, 'michael.magat@colegiodenaujan.edu.ph', 'michael.magat@colegiodenaujan.edu.ph', '$2y$10$jM86d7fAziAWl1igo4lHau1qYt2ayx5xuVnFbMR8uQDF0uanKhy8W', 'Michael Magat', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6321, 'john.k.moreno@colegiodenaujan.edu.ph', 'john.k.moreno@colegiodenaujan.edu.ph', '$2y$10$ONjTvwiJR7p32WXApS550OuWTbl9CDnRJSj.VMT.YxgWcgHXMMPY2', 'John Moreno', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6322, 'jayson.ramos@colegiodenaujan.edu.ph', 'jayson.ramos@colegiodenaujan.edu.ph', '$2y$10$Gb3UAZ0nfFt65bWB7eDEPeJA0bh3Nzg4OVOmayWDrBN7STM4F0MBq', 'Jayson Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6323, 'joel.villena@colegiodenaujan.edu.ph', 'joel.villena@colegiodenaujan.edu.ph', '$2y$10$uUKaE0CrNMb6sRlYWO7j1O2M4Q7LiKxzEgBLs/iBatKUNQGwWTlKm', 'Joel Villena', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6324, 'jazzle.cudiamat@colegiodenaujan.edu.ph', 'jazzle.cudiamat@colegiodenaujan.edu.ph', '$2y$10$4F3/BNde6khReyd7qUhMTe81ibSa5oFlmJu90U6mkkPcRjQCXSZz6', 'Jazzle Cudiamat', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6325, 'jenny.fajardo@colegiodenaujan.edu.ph', 'jenny.fajardo@colegiodenaujan.edu.ph', '$2y$10$3lPcVHw/CXv9h37pB4Xbdu/PcK/jNqRAAp/FEJ0hRN.f859alyVve', 'Jenny Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6326, 'mary.sim@colegiodenaujan.edu.ph', 'mary.sim@colegiodenaujan.edu.ph', '$2y$10$Ysq.Eu8FCwpaK3P7YcAtDOYhn3/FZasEzpUNF7yjNyylduB1fHA2K', 'Mary Sim', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6327, 'jordan.abeleda@colegiodenaujan.edu.ph', 'jordan.abeleda@colegiodenaujan.edu.ph', '$2y$10$frYHZOPs9nfYRfwKFeWfuuzlK1v3c9d3lZbx4DkrueSEgyANasb1i', 'Jordan Abeleda', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6328, 'ralf.atienza@colegiodenaujan.edu.ph', 'ralf.atienza@colegiodenaujan.edu.ph', '$2y$10$TAU9/EYIN7r7WBWqEUXiCeHDcNIyXX6W4a0ymHaTlM1MjQf5ZFd0W', 'Ralf Atienza', 'student', 1, 'active', NULL, '2026-03-23 14:41:38', '2026-03-23 14:41:38'),
(6329, 'mon.bae@colegiodenaujan.edu.ph', 'mon.bae@colegiodenaujan.edu.ph', '$2y$10$4nvxDdN75gZbPfgKdF03auzcVQv3WrhZFjzMhfER2urSCM66/b6Oa', 'Mon Bae', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6330, 'john.balmes@colegiodenaujan.edu.ph', 'john.balmes@colegiodenaujan.edu.ph', '$2y$10$BiyaZKuZGnxYwlOmebgxzO7fzQ0Y.tb5IkUSrBz7YGhR9MtD/4e0y', 'John Balmes', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6331, 'john.bola.nos@colegiodenaujan.edu.ph', 'john.bola.nos@colegiodenaujan.edu.ph', '$2y$10$kICyMQ17Ul9Bl3zuWR3iTOTL8C5L07kzV6ralFc7EbbPeNXGShqay', 'John Bolaños', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6332, 'justine.dela.cruz@colegiodenaujan.edu.ph', 'justine.dela.cruz@colegiodenaujan.edu.ph', '$2y$10$QAGMFK6inb58AxA1vC/nmup8PqG5YFtDFY1QIbxC0gG8c.KuuURD6', 'Justine Dela Cruz', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6333, 'carl.evangelista@colegiodenaujan.edu.ph', 'carl.evangelista@colegiodenaujan.edu.ph', '$2y$10$LdFgWHzv/litMyY9rDb3RuBQ7QNAtPfAS00NmvnV73AuhSLTtjl/2', 'Carl Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6334, 'mon.faner@colegiodenaujan.edu.ph', 'mon.faner@colegiodenaujan.edu.ph', '$2y$10$uCDNjo7zLrsQZ1YA8Hjrjur1vlRqk.1IX/NhYbiHGdkGBjxEy67dm', 'Mon Faner', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6335, 'john.freyra@colegiodenaujan.edu.ph', 'john.freyra@colegiodenaujan.edu.ph', '$2y$10$qevuulCJH8FQZWS9xsZiruabFhQel9LmWjajIdqMahD3l.pXA6NCu', 'John Freyra', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6336, 'ryan.garcia@colegiodenaujan.edu.ph', 'ryan.garcia@colegiodenaujan.edu.ph', '$2y$10$IFT3q.cVd0.oTWlQfq3VR.lvEW5pF8a5rkNBDA6O0JQCzzV1iJQ7O', 'Ryan Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6337, 'jeshler.gervacio@colegiodenaujan.edu.ph', 'jeshler.gervacio@colegiodenaujan.edu.ph', '$2y$10$9G54kz5NNprNtWh6AY9jjO/bRcWBE2ncDRd6dpOJNY6whHNLzcfya', 'Jeshler Gervacio', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6338, 'melvic.magsino@colegiodenaujan.edu.ph', 'melvic.magsino@colegiodenaujan.edu.ph', '$2y$10$yHZqJ7kAWSftpZelfEYotOXtEKoyTrCgV/kw0jtP5Dd5v/VOgeFG.', 'Melvic Magsino', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6339, 'jerome.mauro@colegiodenaujan.edu.ph', 'jerome.mauro@colegiodenaujan.edu.ph', '$2y$10$7GlSsZF.lYXYYAk1JoQ7uOgk5Di3Yy194wB5FysmWFbwd3S86d3HC', 'Jerome Mauro', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6340, 'jundell.morales@colegiodenaujan.edu.ph', 'jundell.morales@colegiodenaujan.edu.ph', '$2y$10$NW7fViG23sYr0MHR8BT17u/wIjVaHqjHfIUPcqg9iiy9t3kqqHyNC', 'Jundell Morales', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6341, 'adrian.pampilo@colegiodenaujan.edu.ph', 'adrian.pampilo@colegiodenaujan.edu.ph', '$2y$10$lIfTNm4QxoDKUivouO1AquaOORPaCgy.eHtHlNeuBrX7SoBbXRwhS', 'Adrian Pampilo', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6342, 'john.pedragoza@colegiodenaujan.edu.ph', 'john.pedragoza@colegiodenaujan.edu.ph', '$2y$10$NXsByP699i408Zw0tI0CduGEwAMuUBkQS7C05nKd.RsBwg2o1IcJ.', 'John Pedragoza', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6343, 'king.saranillo@colegiodenaujan.edu.ph', 'king.saranillo@colegiodenaujan.edu.ph', '$2y$10$EKbK0h557Xy3rjvHihKXz.28ctRJ9AL2o5TypSHdv8nX1xmz84062', 'King Saranillo', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6344, 'jhon.victoriano@colegiodenaujan.edu.ph', 'jhon.victoriano@colegiodenaujan.edu.ph', '$2y$10$.DOyKkDZcyB5SdWZWi61uezMJbklM9HmOECOyfE5c3yuYFVb4PESO', 'Jhon Victoriano', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39'),
(6345, 'janelle.absin@colegiodenaujan.edu.ph', 'janelle.absin@colegiodenaujan.edu.ph', '$2y$10$lIdnHlLtI7z6Jep2A/EV/.RB5tuI7pYdQW1H/U5odSNBVutoD.QgK', 'Janelle Absin', 'student', 1, 'active', NULL, '2026-03-23 14:41:39', '2026-03-23 14:41:39');
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `must_change_password`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(6346, 'jan.bonado@colegiodenaujan.edu.ph', 'jan.bonado@colegiodenaujan.edu.ph', '$2y$10$biwuftoqrzfCjx1psFgpTOnawRFqSJdfnOwgATsN4HXFX5cVrHeDO', 'Jan Bonado', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6347, 'robelyn.bonado@colegiodenaujan.edu.ph', 'robelyn.bonado@colegiodenaujan.edu.ph', '$2y$10$n0mtbKYZ27mt2IX7LHHHXeW0Gr1S1oV3ndsEIn5eHNc1mM15okg/i', 'Robelyn Bonado', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6348, 'princes.capote@colegiodenaujan.edu.ph', 'princes.capote@colegiodenaujan.edu.ph', '$2y$10$b5zC/kTOz8KtViK5sTEMZ..MEkXd.Po/14d9X1O0Dcmoz5l3qmXxe', 'Princes Capote', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6349, 'joann.carandan@colegiodenaujan.edu.ph', 'joann.carandan@colegiodenaujan.edu.ph', '$2y$10$XUGDwStq7OHaMeGTW.Icde3N3cBayhxR2PphxJy/lwtQl6OALsbkC', 'Joann Carandan', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6350, 'christine.catapang@colegiodenaujan.edu.ph', 'christine.catapang@colegiodenaujan.edu.ph', '$2y$10$pQZ32HdZKaXlj.FuKqJANuUj0Cdo3jOGpnj5l1h4/eF5ILQ23ABly', 'Christine Catapang', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6351, 'arlyn.corona@colegiodenaujan.edu.ph', 'arlyn.corona@colegiodenaujan.edu.ph', '$2y$10$9p5f7L8YScK2FIUxjt.1EelafXTP4nN9yodLqvrjxmdnNBh7bpgcK', 'Arlyn Corona', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6352, 'stacy.cortez@colegiodenaujan.edu.ph', 'stacy.cortez@colegiodenaujan.edu.ph', '$2y$10$FIjp0KzPUY/eLDhD6O02WeBt/tJ22svMSfis4GNe9JocwkrQAE7iO', 'Stacy Cortez', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6353, 'de.c@colegiodenaujan.edu.ph', 'de.c@colegiodenaujan.edu.ph', '$2y$10$UgD31JDmSuRU.OIJp47WGexndRYZXuodoj1iEx47GiHKYBAe2AF32', 'De C.', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6354, 'angel.de.lara@colegiodenaujan.edu.ph', 'angel.de.lara@colegiodenaujan.edu.ph', '$2y$10$0bxyhSTag18QswA3JBVsz.xXmtIL/Q2OYk1P5rjKsdRS/ZIu8wgEy', 'Angel De Lara', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6355, 'lorebel.de.leon@colegiodenaujan.edu.ph', 'lorebel.de.leon@colegiodenaujan.edu.ph', '$2y$10$YmCozaMQhh4cS/EftymyR.PKFQNw8OqbG10rpEBlVCZT/Kddyl0Uu', 'Lorebel De Leon', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6356, 'rocelyn.dela.rosa@colegiodenaujan.edu.ph', 'rocelyn.dela.rosa@colegiodenaujan.edu.ph', '$2y$10$7sWpeKJ9cwHB8tdv6TMCuugICBiCNoRU2ITekJgpISskXlx7/zmji', 'Rocelyn Dela Rosa', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6357, 'ronalyn.dela.rosa@colegiodenaujan.edu.ph', 'ronalyn.dela.rosa@colegiodenaujan.edu.ph', '$2y$10$zoFvKFnjaI4Cy2c.b21LVu3zVxp5WvqPfsFO6dobzOvgr1.L5kVXO', 'Ronalyn Dela Rosa', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6358, 'krisnah.dorias@colegiodenaujan.edu.ph', 'krisnah.dorias@colegiodenaujan.edu.ph', '$2y$10$x5Fv0nulEalKPb2rOtqO4u1Xed9mhI0wKqF3eH/CTHd2fkps.FH7G', 'Krisnah Dorias', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6359, 'ayessa.gaba@colegiodenaujan.edu.ph', 'ayessa.gaba@colegiodenaujan.edu.ph', '$2y$10$a/lQCegxBuxqdZhneA3NWOklhFscTQfyCNi6ZY/DrCm0nrrjsPIUa', 'Ayessa Gaba', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6360, 'margie.gatilo@colegiodenaujan.edu.ph', 'margie.gatilo@colegiodenaujan.edu.ph', '$2y$10$oCucIwOfsLxQN0BFZqXXMeTrsOc0ypckjV6RfpMiaZsOXRS3li8sC', 'Margie Gatilo', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6361, 'jasmine.gayao@colegiodenaujan.edu.ph', 'jasmine.gayao@colegiodenaujan.edu.ph', '$2y$10$IgqJM8dL9QGkULE42939seT/j7xw2BR0eZJ2qGVYS61L2K4vNaBpC', 'Jasmine Gayao', 'student', 1, 'active', NULL, '2026-03-23 14:41:40', '2026-03-23 14:41:40'),
(6362, 'mikaela.hernandez@colegiodenaujan.edu.ph', 'mikaela.hernandez@colegiodenaujan.edu.ph', '$2y$10$ri3cn0y/OYLD3muInw2pGO2ZKL7wVatIl.SvTGs8F0dABm6dwyQUK', 'Mikaela Hernandez', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6363, 'vanessa.latoga@colegiodenaujan.edu.ph', 'vanessa.latoga@colegiodenaujan.edu.ph', '$2y$10$/tKy6gdvXK0Bn2h2s/ZQTuPZHah/LZzvXxqJDc3bbDxICmSSEOE4u', 'Vanessa Latoga', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6364, 'alwena.madrigal@colegiodenaujan.edu.ph', 'alwena.madrigal@colegiodenaujan.edu.ph', '$2y$10$czYw5qsbAqN00kQF3aAcou/yhI5aGYs5zSYSYMixBaq6JvrCn.5I2', 'Alwena Madrigal', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6365, 'maria.magsisi@colegiodenaujan.edu.ph', 'maria.magsisi@colegiodenaujan.edu.ph', '$2y$10$wYefXl2RGEdjnorRgAzGguOLLomufKqRq4XvMRDrr3I4/jxdY9DEK', 'Maria Magsisi', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6366, 'carla.matira@colegiodenaujan.edu.ph', 'carla.matira@colegiodenaujan.edu.ph', '$2y$10$JVqbPmKzpCekTs2tjNg2YOsedxhB2/HdwHFoYcuOsGYIDAgjxkOlq', 'Carla Matira', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6367, 'allysa.mirasol@colegiodenaujan.edu.ph', 'allysa.mirasol@colegiodenaujan.edu.ph', '$2y$10$jUl/FWv0ALAFBLytugd.sejOg0BwJznCPKzNQu91VT3vi0B8s7MSu', 'Allysa Mirasol', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6368, 'manilyn.narca@colegiodenaujan.edu.ph', 'manilyn.narca@colegiodenaujan.edu.ph', '$2y$10$cmtKOcV.i5Yqd0w6g5BXQugujxY59t11c1nZM3uSftUipHtWLCnsi', 'Manilyn Narca', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6369, 'sharah.ojales@colegiodenaujan.edu.ph', 'sharah.ojales@colegiodenaujan.edu.ph', '$2y$10$xxY26vBfJpfvEncA.jjBOe8R8ZwfSZGSLFoLfXaAAMfIqncS4Xw62', 'Sharah Ojales', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6370, 'geselle.rivas@colegiodenaujan.edu.ph', 'geselle.rivas@colegiodenaujan.edu.ph', '$2y$10$KjCv.14lYyhe288FCM5BUeALZJFoIt0umVTobijUqgWynb2a4YHEe', 'Geselle Rivas', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6371, 'angel.sanchez@colegiodenaujan.edu.ph', 'angel.sanchez@colegiodenaujan.edu.ph', '$2y$10$FsTnu7CrJ5PrnxydgRXgw.lgi8vyw6oRFB4xDqHFWfZQPuGkJoAlK', 'Angel Sanchez', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6372, 'jamaica.sarabia@colegiodenaujan.edu.ph', 'jamaica.sarabia@colegiodenaujan.edu.ph', '$2y$10$19bcKbyScFg63WiOUOQqAOQZfTkKSbUjsEh6m4a6wuLsvkCFTWSJS', 'Jamaica Sarabia', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6373, 'nicole.villafranca@colegiodenaujan.edu.ph', 'nicole.villafranca@colegiodenaujan.edu.ph', '$2y$10$wOTr3qrv.uQmdnwEykGOZOaupsTz5084zSu0WiBSZBPyRRtLLtatS', 'Nicole Villafranca', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6374, 'jennylyn.villanueva@colegiodenaujan.edu.ph', 'jennylyn.villanueva@colegiodenaujan.edu.ph', '$2y$10$HhP9M1CuJdZ7Hxw/oQMt4.AcjEVWHg/xMT5pYyQoyiW0wJLynf1f2', 'Jennylyn Villanueva', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6375, 'john.amido@colegiodenaujan.edu.ph', 'john.amido@colegiodenaujan.edu.ph', '$2y$10$CcxFcbufZhXQA5aINWkyfOfx191aSnCM/n3n1DO1vfq8vnn20U8di', 'John Amido', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6376, 'reniel.borja@colegiodenaujan.edu.ph', 'reniel.borja@colegiodenaujan.edu.ph', '$2y$10$elpDxtjpq/bxv8E/PRH3OeMW0CUMgGwIYa7Clxg3J7iukFtAadzDK', 'Reniel Borja', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6377, 'john.chiquito@colegiodenaujan.edu.ph', 'john.chiquito@colegiodenaujan.edu.ph', '$2y$10$e4qQYCdz3IAn2.TJJ8YY8.GrUL73jJ9oOFeXp1XyA402MSA46d15C', 'John Chiquito', 'student', 1, 'active', NULL, '2026-03-23 14:41:41', '2026-03-23 14:41:41'),
(6378, 'justin.como@colegiodenaujan.edu.ph', 'justin.como@colegiodenaujan.edu.ph', '$2y$10$YCVyKI1kvXnBl8kgXKb/zOzMwNYax7dyqXKpP.Nhzxisv7eJT2kAi', 'Justin Como', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6379, 'moises.delos.santos@colegiodenaujan.edu.ph', 'moises.delos.santos@colegiodenaujan.edu.ph', '$2y$10$Z3AeIMncLAf3h9KSFgk8guX/jcUN4mLwI4n0oeZf1Vu3FDn/LH2za', 'Moises Delos Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6380, 'philip.garcia@colegiodenaujan.edu.ph', 'philip.garcia@colegiodenaujan.edu.ph', '$2y$10$TdMqB4/ZwXP2OUHHSEe28eRiXo1CjxBKbGi2HjH.ZUl9koUukMdMe', 'Philip Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6381, 'bryan.pe.naescosa@colegiodenaujan.edu.ph', 'bryan.pe.naescosa@colegiodenaujan.edu.ph', '$2y$10$nDIinhjDqNZWyLVh.ryqX.xUKD4D7bgBLLYs/DV5/mTJwF69KQOUK', 'Bryan Peñaescosa', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6382, 'john.ramos@colegiodenaujan.edu.ph', 'john.ramos@colegiodenaujan.edu.ph', '$2y$10$QCG0QwE.8.lshicud/kHA.3gBumSQtFk2Jd5.EF.p4JazSEKY7.wG', 'John Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6383, 'rezlyn.aguba@colegiodenaujan.edu.ph', 'rezlyn.aguba@colegiodenaujan.edu.ph', '$2y$10$H/wK2DrTqUBszfew3oGhguF1YYFFpFSrjgvunNGJFeN3r04peUpXu', 'Rezlyn Aguba', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6384, 'lyzel.bool@colegiodenaujan.edu.ph', 'lyzel.bool@colegiodenaujan.edu.ph', '$2y$10$F.tg5OlStN8Lv/Oo4vMslO0Kzictqpj4y6xSv9TcGtjhLEtEOdWPC', 'Lyzel Bool', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6385, 'jesca.chavez@colegiodenaujan.edu.ph', 'jesca.chavez@colegiodenaujan.edu.ph', '$2y$10$AnT8vBOqOWY2LhcAMhL3JeTYEg/YDkjqfK1RRdDKsBpCQAI87hQRW', 'Jesca Chavez', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6386, 'hiedie.claus@colegiodenaujan.edu.ph', 'hiedie.claus@colegiodenaujan.edu.ph', '$2y$10$PQwwEZKJqSbMOUR2HAiLZ.yjI6bbFpfSL2rk4BUFg/dKj4EZdw8tO', 'Hiedie Claus', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6387, 'kc.dela.roca@colegiodenaujan.edu.ph', 'kc.dela.roca@colegiodenaujan.edu.ph', '$2y$10$kjuKT3PmC68fZZ0uI43FgOMEkAB0/dKxL19JOf57of7XZTRXcGVh2', 'KC Dela Roca', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6388, 'bea.fajardo@colegiodenaujan.edu.ph', 'bea.fajardo@colegiodenaujan.edu.ph', '$2y$10$9YE0yMCje/L7UtDel5ZtB.3ewuY/wPna4m2x7TmnN3BJoAg/Cxl8O', 'Bea Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6389, 'sherlyn.festin@colegiodenaujan.edu.ph', 'sherlyn.festin@colegiodenaujan.edu.ph', '$2y$10$X.YautgQInErBbnvvXcugOEuagiMX3bOkaekCBsAhs.9fWy0sVTjS', 'Sherlyn Festin', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6390, 'clarissa.feudo@colegiodenaujan.edu.ph', 'clarissa.feudo@colegiodenaujan.edu.ph', '$2y$10$eOfQe4ZAmRMsr0ir38i5x.lfsJDmF.xVW47hIoB4nbCVpI0bwLLZi', 'Clarissa Feudo', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6391, 'irish.magcamit@colegiodenaujan.edu.ph', 'irish.magcamit@colegiodenaujan.edu.ph', '$2y$10$.eWNruQzLvOoYLlqy6B2xuaviwvtT8LXp.whPRZtCvJvFbx5ZrS8q', 'Irish Magcamit', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6392, 'cristine.manalo@colegiodenaujan.edu.ph', 'cristine.manalo@colegiodenaujan.edu.ph', '$2y$10$fn362/boPaTvGBEy5w1ecuUOzdE5mdiCQaS0lTb9m33F5tDsQtq.G', 'Cristine Manalo', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6393, 'geraldine.manalo@colegiodenaujan.edu.ph', 'geraldine.manalo@colegiodenaujan.edu.ph', '$2y$10$O.qyKEqLGJtU6H1UfM4hSOG.C3Qu72To7q5Yy/xLh8WXecHqbxA2K', 'Geraldine Manalo', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6394, 'shiloh.manhic@colegiodenaujan.edu.ph', 'shiloh.manhic@colegiodenaujan.edu.ph', '$2y$10$FcrvtcxnPOSfhZ.07DyWhui02SUKumpMFw3Bw9N0jqHEq.F8BVqvu', 'Shiloh Manhic', 'student', 1, 'active', NULL, '2026-03-23 14:41:42', '2026-03-23 14:41:42'),
(6395, 'shylyn.mansalapus@colegiodenaujan.edu.ph', 'shylyn.mansalapus@colegiodenaujan.edu.ph', '$2y$10$4YfzQ2r37MPRIA5OIG8q0uiYid8pgLmg3OZdaRC9M323Iv/1RyMeS', 'Shylyn Mansalapus', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6396, 'irish.nao@colegiodenaujan.edu.ph', 'irish.nao@colegiodenaujan.edu.ph', '$2y$10$VwFnhioe4G1JboMM530wwOP7XJ7eJDWhG0VajCseHm3V07dGkWv1K', 'Irish Nao', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6397, 'paulyn.perez@colegiodenaujan.edu.ph', 'paulyn.perez@colegiodenaujan.edu.ph', '$2y$10$E3fZWFOBiTy8tL/Y84DF3etI8ydaApuBh7otqmdmQWJOp2PMh0Ow6', 'Paulyn Perez', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6398, 'shane.ramos@colegiodenaujan.edu.ph', 'shane.ramos@colegiodenaujan.edu.ph', '$2y$10$FeoB888Oeil88GknIMRPr.7wvPLvCHnjZpyMwwETluKMTgIOJMZOS', 'Shane Ramos', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6399, 'andrea.rivera@colegiodenaujan.edu.ph', 'andrea.rivera@colegiodenaujan.edu.ph', '$2y$10$HnQ4UixIaF9y.uLwDQMGFu1OoA.BN41EbI17IUclOn7prLMVM/Lfa', 'Andrea Rivera', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6400, 'angel.vargas@colegiodenaujan.edu.ph', 'angel.vargas@colegiodenaujan.edu.ph', '$2y$10$gLdQ7QQG/myTOgViMvfeouplDtcdgZ4TW40PHjdVbqb9zglv4fSd6', 'Angel Vargas', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6401, 'jamaica.villena@colegiodenaujan.edu.ph', 'jamaica.villena@colegiodenaujan.edu.ph', '$2y$10$5kgXmvWPCARKCLRQdZ2ru.lNfixgDuU1gwnhYS8q5WJcLKILf.0me', 'Jamaica Villena', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6402, 'monaliza.waing@colegiodenaujan.edu.ph', 'monaliza.waing@colegiodenaujan.edu.ph', '$2y$10$b8hodmSs3.n6XkH6Dd7zv.ZSyENKNq.b9Qz8Xxv3e9M3CXLL00V4O', 'Monaliza Waing', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6403, 'jay.aguilar@colegiodenaujan.edu.ph', 'jay.aguilar@colegiodenaujan.edu.ph', '$2y$10$oLjhDKDNA7iTLFxelc6dF.aomUxJlzWPbtoN5oJtDRAQcLAPDJptS', 'Jay Aguilar', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6404, 'ken.algaba@colegiodenaujan.edu.ph', 'ken.algaba@colegiodenaujan.edu.ph', '$2y$10$SFZqQlYki65JVQi1ljHJAuCGXr3Gvy9OXCnuo2a6A4cqG71io7FVa', 'Ken Algaba', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6405, 'mark.baes@colegiodenaujan.edu.ph', 'mark.baes@colegiodenaujan.edu.ph', '$2y$10$KuAWLxJQmKvXziEdAZlMl.kUHlKCe9ar3Dda4lxEqmk8GujOIzyQu', 'Mark Baes', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6406, 'john.bastida@colegiodenaujan.edu.ph', 'john.bastida@colegiodenaujan.edu.ph', '$2y$10$GRv6lH1Z84jozcG7ZgQcYO5.RnGLg4p6w8YDQ/7DZO5tdzfn3Ow1y', 'John Bastida', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6407, 'bryan.caguete@colegiodenaujan.edu.ph', 'bryan.caguete@colegiodenaujan.edu.ph', '$2y$10$UMBfeOSUX6gSxvIlq/kQpu3HUe2G3XjWkeTkqxWvh4fc8H18htH3W', 'Bryan Caguete', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6408, 'vitoel.curatcha@colegiodenaujan.edu.ph', 'vitoel.curatcha@colegiodenaujan.edu.ph', '$2y$10$tb0TkB2FeESO1exatRN9quFRA.pIsj.j9wL7gVC4kL2pvSinnkJbi', 'Vitoel Curatcha', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6409, 'karl.de.leon@colegiodenaujan.edu.ph', 'karl.de.leon@colegiodenaujan.edu.ph', '$2y$10$m1ZvXF7kWJuOVEeKHKSEqe41Wg1Q9EXTF8kYahY6iasvmTdAdwVZ.', 'Karl De Leon', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6410, 'renzie.escaro@colegiodenaujan.edu.ph', 'renzie.escaro@colegiodenaujan.edu.ph', '$2y$10$ui7YhYuoPGcCxe1MDLafA.wagSZNSvyueSXHOnpHIOjiW1yx812Kq', 'Renzie Escaro', 'student', 1, 'active', NULL, '2026-03-23 14:41:43', '2026-03-23 14:41:43'),
(6411, 'nathaniel.falcunaya@colegiodenaujan.edu.ph', 'nathaniel.falcunaya@colegiodenaujan.edu.ph', '$2y$10$dqMnj4sDBbUneO6.ZVsy1uLBq19MmS8y1ZAYw8Yzsq56qxWxkXqmW', 'Nathaniel Falcunaya', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6412, 'kyzer.gonda@colegiodenaujan.edu.ph', 'kyzer.gonda@colegiodenaujan.edu.ph', '$2y$10$w4G9Crrm94NdFCdBr5yEqO4XaimIGhb3RCi8ElrpJb2YPgIR1JWji', 'Kyzer Gonda', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6413, 'john.gonzales@colegiodenaujan.edu.ph', 'john.gonzales@colegiodenaujan.edu.ph', '$2y$10$BIakeWSz0v0pwfpkNzm9uuOIdQ2bpU0kzvepYJWxprVhHpPRR/5Qa', 'John Gonzales', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6414, 'reniel.jara@colegiodenaujan.edu.ph', 'reniel.jara@colegiodenaujan.edu.ph', '$2y$10$hbb9EOuCU6QiXbsY2Wq7Ju0aaWSuimYaYrOhm5xb3B/l5bRJsJQEe', 'Reniel Jara', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6415, 'steven.legayada@colegiodenaujan.edu.ph', 'steven.legayada@colegiodenaujan.edu.ph', '$2y$10$iPLnJSCI7jB6W1GGt13t/.QSk60gQU32bJZBCPxNel/aF8jKgVFhO', 'Steven Legayada', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6416, 'angelo.lumanglas@colegiodenaujan.edu.ph', 'angelo.lumanglas@colegiodenaujan.edu.ph', '$2y$10$.RrJsG8j9PK6Lw45lzUyOePn8aTBnIoEMuBrfwJvMngbFm4LD7sJC', 'Angelo Lumanglas', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6417, 'jhon.madrigal@colegiodenaujan.edu.ph', 'jhon.madrigal@colegiodenaujan.edu.ph', '$2y$10$FT37j2NyvNYOBx3jIwid9OGcnCovh58yF61c02Z2Re7QtBTCYKoP.', 'Jhon Madrigal', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6418, 'rhaven.magmanlac@colegiodenaujan.edu.ph', 'rhaven.magmanlac@colegiodenaujan.edu.ph', '$2y$10$t8UEAS0dv9zI05gvCK1H1uk.cjAks/usZXnc8LpCR/Qh4PCVVhtMG', 'Rhaven Magmanlac', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6419, 'jumyr.moreno@colegiodenaujan.edu.ph', 'jumyr.moreno@colegiodenaujan.edu.ph', '$2y$10$x/wjVQTJxUuVRsDEr6eVzup6YhRZZMPThv0WqU.ihoP6tmJN4NuHC', 'Jumyr Moreno', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6420, 'dan.paala@colegiodenaujan.edu.ph', 'dan.paala@colegiodenaujan.edu.ph', '$2y$10$Pj.RTUgN/IbJpAyCRoos5u1TtKBmC9i3jUMgEeOJ.G8m6NELKQzCK', 'Dan Paala', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6421, 'patrick.romasanta@colegiodenaujan.edu.ph', 'patrick.romasanta@colegiodenaujan.edu.ph', '$2y$10$7UI.QaCams636eueHz84tu6LekeTmxkOBQ0d5jUhD4U96J65ZQLjW', 'Patrick Romasanta', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6422, 'jereck.roxas@colegiodenaujan.edu.ph', 'jereck.roxas@colegiodenaujan.edu.ph', '$2y$10$McjrbNSvGL5Plvza.uSkQ.dZ/dcR9lt.d2faQq08TkKXyTHBBHeky', 'Jereck Roxas', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6423, 'jan.santos@colegiodenaujan.edu.ph', 'jan.santos@colegiodenaujan.edu.ph', '$2y$10$Ak9mnxcP9xY30uSBcszzDeWuIuBCenDaMHHsv3T4DAdOYY/bV55pu', 'Jan Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6424, 'john.torralba@colegiodenaujan.edu.ph', 'john.torralba@colegiodenaujan.edu.ph', '$2y$10$2nsrjbwoI1YQ7UGTy9BAMOnZZR0DeKFnck6MLtPyeufe4efU8P9kW', 'John Torralba', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6425, 'dianne.alulod@colegiodenaujan.edu.ph', 'dianne.alulod@colegiodenaujan.edu.ph', '$2y$10$rNRg8xha92aoULEpad2lGOCa8duC.WVo4cZe2Vi2JHoCMxYCN4bTa', 'Dianne Alulod', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6426, 'rechel.arenas@colegiodenaujan.edu.ph', 'rechel.arenas@colegiodenaujan.edu.ph', '$2y$10$Vq6LMWf5rT9Mklbe4GrW8OxMhu98R.OEw14v8qigVnDgU10JzA1CK', 'Rechel Arenas', 'student', 1, 'active', NULL, '2026-03-23 14:41:44', '2026-03-23 14:41:44'),
(6427, 'allyna.atienza@colegiodenaujan.edu.ph', 'allyna.atienza@colegiodenaujan.edu.ph', '$2y$10$CQVcsx7KyRh.DR0WEKeA1u7vWDilsRQWJVgKJzzdbcFyOvwbOzqvq', 'Allyna Atienza', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6428, 'angela.bonilla@colegiodenaujan.edu.ph', 'angela.bonilla@colegiodenaujan.edu.ph', '$2y$10$GdVIT8MmP0hGrc26Ib2jmejavcS8T0dMF5wubGXK.Q6kDW5MbrJGC', 'Angela Bonilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6429, 'aira.cabulao@colegiodenaujan.edu.ph', 'aira.cabulao@colegiodenaujan.edu.ph', '$2y$10$k0nU7BS7LOWZnznd4Ne1fOh2e8cS3FaLZke.WNgqON8sWJyLA8N2a', 'Aira Cabulao', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6430, 'janice.cadacio@colegiodenaujan.edu.ph', 'janice.cadacio@colegiodenaujan.edu.ph', '$2y$10$0ctvVINPwL3VDV8vQFr55.f3TCqbjxQraBTVqSvYZcSnWlooRn7zW', 'Janice Cadacio', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6431, 'maries.cantos@colegiodenaujan.edu.ph', 'maries.cantos@colegiodenaujan.edu.ph', '$2y$10$HLp463mptRNRrSVcahMjQOMANkR4L3cSc7cFYM4..UvfzAY.o9eO6', 'Maries Cantos', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6432, 'veronica.cantos@colegiodenaujan.edu.ph', 'veronica.cantos@colegiodenaujan.edu.ph', '$2y$10$FKZAaXHrXWpDRkuOJXgt8.vkMJZxaSN22j1OpWZjmBygN/whRg9Ey', 'Veronica Cantos', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6433, 'diana.caringal@colegiodenaujan.edu.ph', 'diana.caringal@colegiodenaujan.edu.ph', '$2y$10$uRl8VpO2bQh6LZwyJhpAru3hYJP5VtC7.KcXoF7ZT/zAUbPRU.dye', 'Diana Caringal', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6434, 'lorebeth.casapao@colegiodenaujan.edu.ph', 'lorebeth.casapao@colegiodenaujan.edu.ph', '$2y$10$/NTl7KqYYPIgy0ySK1ho2eLRFzEpTQZDT3w5l4M/bRk4VP3kghtPe', 'Lorebeth Casapao', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6435, 'carla.chiquito@colegiodenaujan.edu.ph', 'carla.chiquito@colegiodenaujan.edu.ph', '$2y$10$kQWvC86JPDy17Fdm4MKxseHmXTm.MgyzXVtQTVIPDmbqJqwgn8Ixy', 'Carla Chiquito', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6436, 'melody.enriquez@colegiodenaujan.edu.ph', 'melody.enriquez@colegiodenaujan.edu.ph', '$2y$10$g5CNo77uPy3hOgRMgnQX8OXi1zBzzdgant8.InW6d.2LHUUe6wN5K', 'Melody Enriquez', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6437, 'maricon.evangelista@colegiodenaujan.edu.ph', 'maricon.evangelista@colegiodenaujan.edu.ph', '$2y$10$2ARUBGwPSAXyWLTGAvmAqurCTOjAk6G6aU19KM9yP9ObHzv6OoWKi', 'Maricon Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6438, 'mary.fajardo@colegiodenaujan.edu.ph', 'mary.fajardo@colegiodenaujan.edu.ph', '$2y$10$coEFG4TDG3mfJX1Xduq13eDpmCVYGyvEcmbctDuJeDfG3zOMX0QMi', 'Mary Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6439, 'kaecy.ferry@colegiodenaujan.edu.ph', 'kaecy.ferry@colegiodenaujan.edu.ph', '$2y$10$Fh4RoKzWfFpQI7hQAt622OGqvYZHsUD9qkHoLLwAKf3I4256UQBOm', 'Kaecy Ferry', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6440, 'zybel.garan@colegiodenaujan.edu.ph', 'zybel.garan@colegiodenaujan.edu.ph', '$2y$10$VrJy2IdgvvuycBCdRvMuEeo5U2P0w9U0axbLm1740s1G45G6Wkztu', 'Zybel Garan', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6441, 'ic.gutierrez@colegiodenaujan.edu.ph', 'ic.gutierrez@colegiodenaujan.edu.ph', '$2y$10$JCsQd58EiBe1zjwTVTDEPe3n6/TYfiJl42IBm4SWf7Z/tK/Yt6e3.', 'IC Gutierrez', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6442, 'jane.mansalapus@colegiodenaujan.edu.ph', 'jane.mansalapus@colegiodenaujan.edu.ph', '$2y$10$qS3b89H8.EYXaNjQ6uqMQu2Z3.eDP7Lftt5RBe/LeIlqf1suPSUEa', 'Jane Mansalapus', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6443, 'hanna.mercado@colegiodenaujan.edu.ph', 'hanna.mercado@colegiodenaujan.edu.ph', '$2y$10$ZRKiY2DuwLAv5hNbgONB/.jwbkUGD7ajsBRSls/RRL7VpjvQY/Vze', 'Hanna Mercado', 'student', 1, 'active', NULL, '2026-03-23 14:41:45', '2026-03-23 14:41:45'),
(6444, 'abegail.moong@colegiodenaujan.edu.ph', 'abegail.moong@colegiodenaujan.edu.ph', '$2y$10$nPHhxBtzdh73h2Xhxn4KueXUFPaDYBMX0V6rynkj/NRY52ZobB2kG', 'Abegail Moong', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6445, 'laiza.pole@colegiodenaujan.edu.ph', 'laiza.pole@colegiodenaujan.edu.ph', '$2y$10$lDpne8p9NSPMlthkxmgljOlfvZIcqyC37FnXLgX.0X.rxVtDmTtHS', 'Laiza Pole', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6446, 'jarryfel.tembrevilla@colegiodenaujan.edu.ph', 'jarryfel.tembrevilla@colegiodenaujan.edu.ph', '$2y$10$mzCE7R9pTrLzOwgMwzv8WOLFo/2GYRnn7D20dNQg2zQwDGAFcVswm', 'Jarryfel Tembrevilla', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6447, 'jay.avelino@colegiodenaujan.edu.ph', 'jay.avelino@colegiodenaujan.edu.ph', '$2y$10$8roRESrXMkD5SYie5htOTe/M1vn4eQnm6D.LCfOBY575.Cd/cDXwS', 'Jay Avelino', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6448, 'jairus.cabales@colegiodenaujan.edu.ph', 'jairus.cabales@colegiodenaujan.edu.ph', '$2y$10$Y7CTZioI0b.d8YYFybOY7OIBVuvHNmveY6MeLuECV0Ts9FMyIvAN.', 'Jairus Cabales', 'student', 0, 'active', NULL, '2026-03-23 14:41:46', '2026-03-26 12:09:46'),
(6449, 'jleo.mazo@colegiodenaujan.edu.ph', 'jleo.mazo@colegiodenaujan.edu.ph', '$2y$10$HWWjdLeja42L.pDLY.mRxeu3iY42fpxODYaamVdF99AAycZHZN/Jm', 'Jleo Mazo', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6450, 'mark.panganiban@colegiodenaujan.edu.ph', 'mark.panganiban@colegiodenaujan.edu.ph', '$2y$10$LWM5iMgXPbMg4CCMupGfde1uBZeE4z05tyDVGi9Odv.MQqz259M8y', 'Mark Panganiban', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6451, 'bernabe.solas@colegiodenaujan.edu.ph', 'bernabe.solas@colegiodenaujan.edu.ph', '$2y$10$K9iZyADILgRHF0kNIKEzFeLFcYHR4blIqkajROpZm9Ue61IshLZ22', 'Bernabe Solas', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6452, 'mark.villena@colegiodenaujan.edu.ph', 'mark.villena@colegiodenaujan.edu.ph', '$2y$10$NTnMzmn9avUUbMkWV2c1AeRf5CUczgqHsnhFPDiLtvlQIbG89wuu2', 'Mark Villena', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6453, 'nhicel.bueno@colegiodenaujan.edu.ph', 'nhicel.bueno@colegiodenaujan.edu.ph', '$2y$10$bKlnB4AtPJhzyvbFhJ/OQ.jcEnx5r.nS5ABJGFPyFv3PO2DODLADu', 'Nhicel Bueno', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6454, 'dianne.cezar@colegiodenaujan.edu.ph', 'dianne.cezar@colegiodenaujan.edu.ph', '$2y$10$jdRcE.MZc.JH5ERU1Ka2pOHpLlkI091ilyWidReFljHMVYa3wXQPS', 'Dianne Cezar', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6455, 'princess.de.castro@colegiodenaujan.edu.ph', 'princess.de.castro@colegiodenaujan.edu.ph', '$2y$10$zKpTkrD69gNp3.pEL7gR8ewAjmK0UjWdiLYBZbIFWUIgoIx48B5lW', 'Princess De Castro', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6456, 'shiela.fajardo@colegiodenaujan.edu.ph', 'shiela.fajardo@colegiodenaujan.edu.ph', '$2y$10$6dBHammrCsnL547V.Nx.HeiwlLQjnQxuO/aZCYvWOPXRyBFmHEW4S', 'Shiela Fajardo', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6457, 'shiela.garcia@colegiodenaujan.edu.ph', 'shiela.garcia@colegiodenaujan.edu.ph', '$2y$10$t3LoyOsvpl99v4vf72n3j.LTN.iciDxCpmrPqHb9TSnr2ZLc/n4Jm', 'Shiela Garcia', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6458, 'jessa.geneta@colegiodenaujan.edu.ph', 'jessa.geneta@colegiodenaujan.edu.ph', '$2y$10$OMbS8cRcT4CdLkfSvrZtQeL3EF5VFESFctuClaRP.0dYjiSYQo8xu', 'Jessa Geneta', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6459, 'jee.llamoso@colegiodenaujan.edu.ph', 'jee.llamoso@colegiodenaujan.edu.ph', '$2y$10$NnGDCtk/7BZ0SHYENxhO1e75QtDBDcjzTJ7T6XJOZEJJU/n0ocukm', 'Jee Llamoso', 'student', 1, 'active', NULL, '2026-03-23 14:41:46', '2026-03-23 14:41:46'),
(6460, 'princess.santos@colegiodenaujan.edu.ph', 'princess.santos@colegiodenaujan.edu.ph', '$2y$10$j.Luiwmo8Qt6Qg0uKgJwF.8a.0eiFoI6WvvXwAuyMSkvHDCyTITfi', 'Princess Santos', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6461, 'von.algaba@colegiodenaujan.edu.ph', 'von.algaba@colegiodenaujan.edu.ph', '$2y$10$3MIoNYJhmmNJYtP.Kfk4uu8U0iU/m.QIZsEz.MBBkGPsYwjQa7gqG', 'Von Algaba', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6462, 'john.aniel@colegiodenaujan.edu.ph', 'john.aniel@colegiodenaujan.edu.ph', '$2y$10$UU0mATIJRt4y4F8Lxg2rGuxYcb0WHTVTFR5ydAUizcfZxfVM7GigS', 'John Aniel', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6463, 'keil.antenor@colegiodenaujan.edu.ph', 'keil.antenor@colegiodenaujan.edu.ph', '$2y$10$Tm5o.FeAfbvF8qJbt3bLF.U92LoxK5BZdk9BZ8Cx7ItUZbOimBXBO', 'Keil Antenor', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6464, 'mark.bacay@colegiodenaujan.edu.ph', 'mark.bacay@colegiodenaujan.edu.ph', '$2y$10$RgUT5UmRZdiRs9m1UJhoeuURG7CCplOxYZ9ti1KQ70sFhuV.b2o1i', 'Mark Bacay', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6465, 'michael.de.guzman@colegiodenaujan.edu.ph', 'michael.de.guzman@colegiodenaujan.edu.ph', '$2y$10$sQebG48tWDdzowvSmFDxfOI3iE.uyKyEYajYpPcBMVupsh43cUE3.', 'Michael De Guzman', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6466, 'christian.delda@colegiodenaujan.edu.ph', 'christian.delda@colegiodenaujan.edu.ph', '$2y$10$nmjEqY5FzlAfJ9gsEi62KewPnMp3Z8Ofijw045Ss1NL3lc89tyUU.', 'Christian Delda', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6467, 'lloyd.evangelista@colegiodenaujan.edu.ph', 'lloyd.evangelista@colegiodenaujan.edu.ph', '$2y$10$QNl6uRbc2VzKqGWbRYDrkOrvvIT2mAipAtay27loo6uiegpny7f7.', 'Lloyd Evangelista', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6468, 'samson.fulgencio@colegiodenaujan.edu.ph', 'samson.fulgencio@colegiodenaujan.edu.ph', '$2y$10$6GanJgEnMEezm8OARRsgv.fKQIIOZ5W5/vRKlkgJ/Aoqpotcdlk9S', 'Samson Fulgencio', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6469, 'john.gardoce@colegiodenaujan.edu.ph', 'john.gardoce@colegiodenaujan.edu.ph', '$2y$10$Ms92cWGGz5mM.K5Bmhla4OF28EadnoiILtZWkm95PvHRSsawynr2C', 'John Gardoce', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6470, 'john.e.gonzales@colegiodenaujan.edu.ph', 'john.e.gonzales@colegiodenaujan.edu.ph', '$2y$10$aIa15RUhok74IlOQ1mjVc.ZrTZwkQxP1GV2dpwtqSz82KZwFxmtnS', 'John Gonzales', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6471, 'mark.muhi@colegiodenaujan.edu.ph', 'mark.muhi@colegiodenaujan.edu.ph', '$2y$10$O1NTFg0hSM3rAxihZQHm6uJ4BMpaMxgn9nzftPSFNgjRbv3Qy/8/e', 'Mark Muhi', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6472, 'marc.relano@colegiodenaujan.edu.ph', 'marc.relano@colegiodenaujan.edu.ph', '$2y$10$wZ6Vh5mdtn9NhnULip8te.Wcm36p1K6nUksLaGrGSM9BmkMwrhVAu', 'Marc Relano', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6473, 'cee.rellora@colegiodenaujan.edu.ph', 'cee.rellora@colegiodenaujan.edu.ph', '$2y$10$GUb4ZeW0E.TGRhzu./xNDubi/GA65ry0DPZK/Z9VeUWkIaWqOf68K', 'Cee Rellora', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6474, 'franklin.salcedo@colegiodenaujan.edu.ph', 'franklin.salcedo@colegiodenaujan.edu.ph', '$2y$10$JNff8.1KIFScP1JC5onjjuZW8fOw82C9h9mMycDUFjuO.QaTUtDg.', 'Franklin Salcedo', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6475, 'russel.sason@colegiodenaujan.edu.ph', 'russel.sason@colegiodenaujan.edu.ph', '$2y$10$C7QPPt.RUwLagxFJhMa36OTsh5QDGfx5Qag74d78Y9L9sTOkFSFV.', 'Russel Sason', 'student', 1, 'active', NULL, '2026-03-23 14:41:47', '2026-03-23 14:41:47'),
(6476, 'john.teves@colegiodenaujan.edu.ph', 'john.teves@colegiodenaujan.edu.ph', '$2y$10$eidMvIvj4Fvx12w2MgnWV.eqldmrLM2MA4frz.xTU2Lqn2vP.A4KS', 'John Teves', 'student', 1, 'active', NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(6477, 'john.villanueva@colegiodenaujan.edu.ph', 'john.villanueva@colegiodenaujan.edu.ph', '$2y$10$4o.NLFrmF3.HPhQ75jF5fuADW4Ds8.Q4XlQ7uyic.DcEQuX8dA7fq', 'John Villanueva', 'student', 1, 'active', NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(6478, 'reinier.visayana@colegiodenaujan.edu.ph', 'reinier.visayana@colegiodenaujan.edu.ph', '$2y$10$vN4kZ/6a8tx6eOUKDbSpDO8zyWHHNUUyJIu4EgaiXkNQ.PQLIkXf6', 'Reinier Visayana', 'student', 1, 'active', NULL, '2026-03-23 14:41:48', '2026-03-23 14:41:48'),
(6479, 'rosa.castillo@colegiodenaujan.edu.ph', 'rosa.castillo@colegiodenaujan.edu.ph', '$2y$10$O3NecowWXP/fx2hu5IKqM.0culv9wes1j0bKoYJRrx2q9YnUHIygG', 'Rosa Castillo', 'student', 1, 'active', NULL, '2026-04-24 03:43:24', '2026-04-24 03:43:24');

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
CREATE TABLE IF NOT EXISTS `violations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `violation_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('minor','major','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'minor',
  `date_occurred` date NOT NULL,
  `reported_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','resolved','dismissed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `resolution_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `v_active_programs`
--

DROP TABLE IF EXISTS `v_active_programs`;
CREATE TABLE IF NOT EXISTS `v_active_programs` (
  `category` enum('4-years','technical') DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `enrolled_students` int DEFAULT NULL,
  `has_prospectus` varchar(3) DEFAULT NULL,
  `id` int DEFAULT NULL,
  `short_title` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `fk_exam_schedule` FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedules` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `class_schedules_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_schedules_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiry_messages`
--
ALTER TABLE `inquiry_messages`
  ADD CONSTRAINT `inquiry_messages_ibfk_1` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
