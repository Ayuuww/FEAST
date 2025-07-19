-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 03:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `feast`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `role` enum('student','faculty','admin','superadmin') NOT NULL,
  `activity` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `role`, `activity`, `timestamp`) VALUES
(4, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 22:48:11'),
(5, '000-0000-0', 'student', 'Logged in', '2025-07-17 22:50:38'),
(6, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 22:51:25'),
(7, '221-0388-1', 'student', 'Logged in', '2025-07-17 23:00:25'),
(8, '221-0388-1', 'student', 'Rated 97.333333333333% for ISPC-101 handled by 003-0000-3', '2025-07-17 23:00:39'),
(9, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 23:01:00'),
(10, '221-0388-1', 'student', 'Logged in', '2025-07-17 23:02:50'),
(11, '221-0388-1', 'student', 'Rated 100% for ISPE-102 handled by ', '2025-07-17 23:03:10'),
(12, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 23:03:31'),
(13, '001-1111-1', 'student', 'Logged in', '2025-07-17 23:05:55'),
(14, '001-1111-1', 'student', 'Rated 97.333333333333% for CVMM-001 handled by Admin Veterinary  Medicine', '2025-07-17 23:06:11'),
(15, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 23:06:37'),
(16, '000-0000-3', 'admin', 'Logged in', '2025-07-17 23:48:10'),
(17, '000-0000-3', 'admin', 'Evaluated Faculty: Rufo S. Faculty with a rating of 97.33%', '2025-07-17 23:48:27'),
(18, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 23:49:10'),
(19, '221-0387-1', 'superadmin', 'Logged in', '2025-07-17 23:58:54'),
(20, '000-0000-3', 'admin', 'Logged in', '2025-07-18 00:03:02'),
(21, '221-0387-1', 'superadmin', 'Logged in', '2025-07-18 00:03:12'),
(22, '221-0387-1', 'superadmin', 'Logged in', '2025-07-18 16:03:12'),
(23, '000-0000-3', 'admin', 'Logged in', '2025-07-18 17:16:09'),
(24, '000-0000-3', 'admin', 'Evaluated Faculty: Excel M. Faculty with a rating of 94.67%', '2025-07-18 17:24:09'),
(25, '000-0000-0', 'student', 'Logged in', '2025-07-18 17:29:21'),
(26, '000-0000-1', 'faculty', 'Logged in', '2025-07-18 18:18:23'),
(27, '221-0387-1', 'superadmin', 'Logged in', '2025-07-18 18:19:09'),
(28, '221-0387-1', 'superadmin', 'Logged in', '2025-07-18 19:02:12'),
(29, '000-0000-3', 'admin', 'Logged in', '2025-07-18 19:07:07'),
(30, '221-0387-1', 'superadmin', 'Logged in', '2025-07-18 19:56:05'),
(31, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 01:19:17'),
(32, '000-0000-1', 'faculty', 'Logged in', '2025-07-19 01:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) DEFAULT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `department_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adds`
--

INSERT INTO `adds` (`id`, `rank_name`, `position_name`, `section_name`, `department_name`) VALUES
(33, 'Instructor I', NULL, NULL, NULL),
(34, NULL, 'Dean', NULL, NULL),
(35, NULL, NULL, '4-B', NULL),
(36, NULL, NULL, NULL, 'CIS'),
(37, NULL, 'Program Director', NULL, NULL),
(38, 'Professor V', NULL, NULL, NULL),
(39, NULL, NULL, NULL, 'CAS'),
(40, NULL, NULL, '1-A', NULL),
(41, NULL, NULL, NULL, 'CVM'),
(42, 'Instructor II', NULL, NULL, NULL),
(43, NULL, NULL, NULL, 'BPED-Math');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `position` varchar(50) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `status` varchar(11) NOT NULL DEFAULT 'active',
  `faculty_rank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `position`, `role`, `status`, `faculty_rank`) VALUES
('000-0000-3', 'Edith', 'Maam', 'Admin', 'ILOVEDMMMSU', 'CIS', 'Dean', 'admin', 'active', 'Instructor I'),
('000-0000-5', 'Program ', 'Chair', 'Admin', 'ILOVEDMMMSU', 'BPED-Math', 'Program Director', 'admin', 'active', 'Professor V'),
('000-0000-9', 'Admin', 'Veterinary ', 'Medicine', 'ILOVEDMMMSU', 'CVM', 'Dean', 'admin', 'active', 'Instructor II');

-- --------------------------------------------------------

--
-- Table structure for table `admin_evaluation`
--

CREATE TABLE `admin_evaluation` (
  `id` int(11) NOT NULL,
  `evaluator_id` varchar(50) NOT NULL,
  `evaluatee_id` varchar(50) NOT NULL,
  `evaluator_position` varchar(11) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `total_score` int(11) NOT NULL,
  `computed_rating` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `evaluation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation`
--

INSERT INTO `admin_evaluation` (`id`, `evaluator_id`, `evaluatee_id`, `evaluator_position`, `academic_year`, `semester`, `total_score`, `computed_rating`, `comments`, `department`, `evaluation_date`) VALUES
(22, '000-0000-3', '003-0000-3', 'Dean', '2025-2026', '1st Semester', 73, 97.33, 'Excellent instructor I have', 'CIS', '2025-07-16 21:14:20'),
(23, '000-0000-3', '000-0000-1', 'Dean', '2024-2025', '1st Semester', 73, 97.33, '', 'CIS', '2025-07-17 23:48:27'),
(24, '000-0000-3', '005-0000-5', 'Dean', '2024-2025', '1st Semester', 71, 94.67, '', 'CIS', '2025-07-18 17:24:09');

-- --------------------------------------------------------

--
-- Table structure for table `admin_evaluation_submissions`
--

CREATE TABLE `admin_evaluation_submissions` (
  `id` int(11) NOT NULL,
  `evaluator_id` varchar(50) DEFAULT NULL,
  `evaluatee_id` varchar(50) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `total_score` int(11) DEFAULT NULL,
  `rating_percent` decimal(5,2) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `submission_date` datetime DEFAULT current_timestamp(),
  `form_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation_submissions`
--

INSERT INTO `admin_evaluation_submissions` (`id`, `evaluator_id`, `evaluatee_id`, `semester`, `academic_year`, `total_score`, `rating_percent`, `comment`, `submission_date`, `form_data`) VALUES
(3, '000-0000-3', '003-0000-3', '1st Semester', '2025-2026', 73, 97.33, 'Excellent instructor I have', '2025-07-16 21:14:20', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}'),
(4, '000-0000-3', '000-0000-1', '1st Semester', '2024-2025', 73, 97.33, '', '2025-07-17 23:48:27', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":4,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}'),
(5, '000-0000-3', '005-0000-5', '1st Semester', '2024-2025', 71, 94.67, '', '2025-07-18 17:24:09', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `department` varchar(11) NOT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  `subject_title` varchar(50) NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  `faculty_id` varchar(50) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `computed_rating` decimal(5,2) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `semester` varchar(255) DEFAULT NULL,
  `student_section` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `student_id`, `department`, `subject_code`, `subject_title`, `academic_year`, `faculty_id`, `total_score`, `computed_rating`, `comment`, `created_at`, `semester`, `student_section`) VALUES
(77, '000-0000-0', 'CIS', 'ISPC-101', 'Computer Programming', '2025-2026', '003-0000-3', 44.00, 58.67, 'Solid Teaching', '2025-07-16 12:27:38', '1st Semester', '4-B'),
(78, '221-0388-1', 'CIS', 'ISPC-101', 'Computer Programming', '2025-2026', '003-0000-3', 51.00, 68.00, 'Excellent Teaching', '2025-07-16 12:28:39', '1st Semester', '1-A'),
(79, '221-0388-1', 'CIS', 'ISPE-102', 'Information Management 2', '2025-2026', '003-0000-3', 73.00, 97.33, 'Yes Maam', '2025-07-16 12:35:58', '1st Semester', '1-A'),
(80, '000-0000-0', 'CIS', 'ISPE-102', 'Information Management 2', '2025-2026', '003-0000-3', 68.00, 90.67, 'Great Teaching', '2025-07-16 12:38:05', '1st Semester', '4-B'),
(81, '001-1111-1', 'CIS', 'ISPC-101', 'Computer Programming', '2025-2026', '003-0000-3', 71.00, 94.67, 'Excellent Teaching', '2025-07-16 15:54:59', '1st Semester', '4-B'),
(82, '001-1111-1', 'CVM', 'CVMM-001', 'Medicine ', '2025-2026', '000-0000-9', 63.00, 84.00, '', '2025-07-16 16:42:37', '1st Semester', '4-B'),
(83, '101-1010-1', 'BPED-Math', 'EDUC-101', 'Edukasyon Sa Pagpapakatao', '2025-2026', '000-0000-5', 75.00, 100.00, '', '2025-07-16 16:49:09', '1st Semester', '1-A'),
(84, '000-0000-0', 'CIS', 'ISPC-101', 'Computer Programming', '2025-2026', '003-0000-3', 73.00, 97.33, '', '2025-07-17 11:52:47', '2nd Semester', '4-B'),
(85, '000-0000-0', 'CIS', 'ISPE-102', 'Information Management 2', '2024-2025', '003-0000-3', 73.00, 97.33, '', '2025-07-17 12:20:24', '1st Semester', '4-B'),
(86, '000-0000-0', 'CIS', 'ISPC-101', 'Computer Programming', '2024-2025', '003-0000-3', 73.00, 97.33, '', '2025-07-17 14:51:10', '1st Semester', '4-B'),
(87, '221-0388-1', 'CIS', 'ISPC-101', 'Computer Programming', '2024-2025', '003-0000-3', 73.00, 97.33, '', '2025-07-17 15:00:39', '1st Semester', '1-A'),
(88, '221-0388-1', 'CIS', 'ISPE-102', 'Information Management 2', '2024-2025', '003-0000-3', 75.00, 100.00, '', '2025-07-17 15:03:10', '1st Semester', '1-A'),
(89, '001-1111-1', 'CVM', 'CVMM-001', 'Medicine ', '2024-2025', '000-0000-9', 73.00, 97.33, '', '2025-07-17 15:06:11', '1st Semester', '4-B');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_settings`
--

CREATE TABLE `evaluation_settings` (
  `id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_settings`
--

INSERT INTO `evaluation_settings` (`id`, `semester`, `academic_year`, `updated_at`) VALUES
(1, '1st Semester', '2024-2025', '2025-07-17 12:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_switch`
--

CREATE TABLE `evaluation_switch` (
  `id` int(11) NOT NULL,
  `status` enum('on','off') NOT NULL DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_switch`
--

INSERT INTO `evaluation_switch` (`id`, `status`) VALUES
(1, 'on');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `faculty_rank`, `role`, `status`) VALUES
('000-0000-1', 'Rufo', 'Sir', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor V', 'faculty', 'active'),
('000-0000-3', 'Edith', 'Maam', 'Admin', NULL, 'CIS', 'Professor V', 'faculty', 'active'),
('000-0000-5', 'Program ', 'Chair', 'Admin', NULL, 'BPED-Math', 'Professor V', 'faculty', 'active'),
('000-0000-9', 'Admin', 'Veterinary ', 'Medicine', NULL, 'CVM', 'Instructor II', 'faculty', 'active'),
('002-0000-2', 'Shirley', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CAS', 'Instructor I', 'faculty', 'active'),
('003-0000-3', 'Maricel', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor V', 'faculty', 'active'),
('004-0000-4', 'Kenneth', 'Sir', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Instructor I', 'faculty', 'active'),
('005-0000-5', 'Excel', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor V', 'faculty', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_peer_evaluation`
--

CREATE TABLE `faculty_peer_evaluation` (
  `id` int(11) NOT NULL,
  `evaluator_id` varchar(50) DEFAULT NULL,
  `evaluated_faculty_id` varchar(50) DEFAULT NULL,
  `school_year` varchar(9) DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `department` varchar(11) NOT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `section`, `role`) VALUES
('000-0000-0', 'Mark', 'Lagman', 'Student', 'ILOVEDMMMSU', 'CIS', '4-B', 'student'),
('000-0001-0', 'Student', 'From', 'CAS', 'ILOVEDMMMSU', 'CAS', '1-A', 'student'),
('001-1111-1', 'Sample', 'Name', 'Student', 'ILOVEDMMMSU', 'CVM', '4-B', 'student'),
('101-1010-1', 'Education', 'Math', 'Student', 'ILOVEDMMMSU', 'BPED-Math', '1-A', 'student'),
('221-0388-1', 'Charles', 'Adonis', 'Student', 'ILOVEDMMMSU', 'CIS', '1-A', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `student_evaluation_submissions`
--

CREATE TABLE `student_evaluation_submissions` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  `faculty_id` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `answers` text NOT NULL,
  `total_score` int(11) DEFAULT 0,
  `computed_rating` decimal(5,2) DEFAULT 0.00,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_evaluation_submissions`
--

INSERT INTO `student_evaluation_submissions` (`id`, `student_id`, `subject_code`, `faculty_id`, `department`, `academic_year`, `semester`, `created_at`, `answers`, `total_score`, `computed_rating`, `comment`) VALUES
(7, '000-0000-0', 'ISPC-101', '003-0000-3', 'CIS', '2025-2026', '1st Semester', '2025-07-16 20:27:38', '{\"q0\":1,\"q1\":2,\"q2\":3,\"q3\":4,\"q4\":5,\"q5\":4,\"q6\":3,\"q7\":2,\"q8\":1,\"q9\":2,\"q10\":3,\"q11\":4,\"q12\":5,\"q13\":3,\"q14\":2}', 44, 58.67, 'Solid Teaching'),
(8, '221-0388-1', 'ISPC-101', '003-0000-3', 'CIS', '2025-2026', '1st Semester', '2025-07-16 20:28:39', '{\"q0\":5,\"q1\":4,\"q2\":3,\"q3\":2,\"q4\":1,\"q5\":2,\"q6\":3,\"q7\":4,\"q8\":5,\"q9\":3,\"q10\":3,\"q11\":3,\"q12\":3,\"q13\":5,\"q14\":5}', 51, 68.00, 'Excellent Teaching'),
(9, '221-0388-1', 'ISPE-102', '003-0000-3', 'CIS', '2025-2026', '1st Semester', '2025-07-16 20:35:58', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":4}', 73, 97.33, 'Yes Maam'),
(10, '000-0000-0', 'ISPE-102', '003-0000-3', 'CIS', '2025-2026', '1st Semester', '2025-07-16 20:38:05', '{\"q0\":5,\"q1\":4,\"q2\":3,\"q3\":4,\"q4\":4,\"q5\":4,\"q6\":5,\"q7\":5,\"q8\":4,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 68, 90.67, 'Great Teaching'),
(11, '001-1111-1', 'ISPC-101', '003-0000-3', 'CIS', '2025-2026', '1st Semester', '2025-07-16 23:54:59', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":4,\"q6\":5,\"q7\":5,\"q8\":4,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 71, 94.67, 'Excellent Teaching'),
(12, '001-1111-1', 'CVMM-001', '000-0000-9', 'CVM', '2025-2026', '1st Semester', '2025-07-17 00:42:37', '{\"q0\":5,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":4,\"q12\":3,\"q13\":2,\"q14\":2}', 63, 84.00, ''),
(13, '101-1010-1', 'EDUC-101', '000-0000-5', 'BPED-Math', '2025-2026', '1st Semester', '2025-07-17 00:49:09', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, ''),
(14, '000-0000-0', 'ISPC-101', '003-0000-3', 'CIS', '2025-2026', '2nd Semester', '2025-07-17 19:52:47', '{\"q0\":5,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, ''),
(15, '000-0000-0', 'ISPE-102', '003-0000-3', 'CIS', '2024-2025', '1st Semester', '2025-07-17 20:20:24', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":4,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":4,\"q14\":5}', 73, 97.33, ''),
(16, '000-0000-0', 'ISPC-101', '003-0000-3', 'CIS', '2024-2025', '1st Semester', '2025-07-17 22:51:10', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 73, 97.33, ''),
(17, '221-0388-1', 'ISPC-101', '003-0000-3', 'CIS', '2024-2025', '1st Semester', '2025-07-17 23:00:39', '{\"q0\":5,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, ''),
(18, '221-0388-1', 'ISPE-102', '003-0000-3', 'CIS', '2024-2025', '1st Semester', '2025-07-17 23:03:10', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, ''),
(19, '001-1111-1', 'CVMM-001', '000-0000-9', 'CVM', '2024-2025', '1st Semester', '2025-07-17 23:06:11', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

CREATE TABLE `student_subject` (
  `idnumber` int(11) NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `subject_code` varchar(11) NOT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `admin_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`idnumber`, `student_id`, `subject_code`, `faculty_id`, `admin_id`) VALUES
(31, '221-0388-1', 'ISPC-101', '003-0000-3', NULL),
(32, '000-0000-0', 'ISPC-101', '003-0000-3', NULL),
(33, '001-1111-1', 'ISPC-101', '003-0000-3', NULL),
(34, '001-1111-1', 'CVMM-001', '000-0000-9', NULL),
(35, '221-0388-1', 'ISPE-102', '003-0000-3', NULL),
(36, '000-0000-0', 'ISPE-102', '003-0000-3', NULL),
(37, '101-1010-1', 'EDUC-101', '000-0000-5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `idnumber` int(11) NOT NULL,
  `code` varchar(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `admin_id` varchar(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`, `admin_id`, `department`) VALUES
(26, 'ISPC-101', 'Computer Programming', '003-0000-3', NULL, 'CIS'),
(27, 'CVMM-001', 'Medicine ', '000-0000-9', NULL, 'CVM'),
(28, 'ISPE-102', 'Information Management 2', '003-0000-3', NULL, 'CIS'),
(29, 'EDUC-101', 'Edukasyon Sa Pagpapakatao', '000-0000-5', NULL, 'BPED-Math');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'superadmin',
  `status` varchar(11) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `role`, `status`) VALUES
('221-0387-1', 'clark joshua', 'velasco', 'rojas', '12345678', 'superadmin', 'active'),
('221-0387-2', 'Clak', 'Juswa', 'Rujas', '12345678', 'superadmin', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adds`
--
ALTER TABLE `adds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rank_name` (`rank_name`),
  ADD KEY `position_name` (`position_name`),
  ADD KEY `section_name` (`section_name`),
  ADD KEY `department_name` (`department_name`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `position` (`position`),
  ADD KEY `fk_admin_department` (`department`);

--
-- Indexes for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_evaluator` (`evaluator_id`),
  ADD KEY `fk_faculty_evaluatee` (`evaluatee_id`),
  ADD KEY `fk_evaluator_position` (`evaluator_position`);

--
-- Indexes for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_eval` (`student_id`,`subject_code`,`academic_year`,`semester`),
  ADD KEY `subject_code_key` (`subject_code`),
  ADD KEY `faculty_id_key` (`faculty_id`),
  ADD KEY `subject_title` (`subject_title`),
  ADD KEY `fk_evaluation_department` (`department`),
  ADD KEY `fk_evaluation_student_section` (`student_section`);

--
-- Indexes for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `department` (`department`),
  ADD KEY `fk_faculty_rank` (`faculty_rank`);

--
-- Indexes for table `faculty_peer_evaluation`
--
ALTER TABLE `faculty_peer_evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `department` (`department`),
  ADD KEY `section` (`section`),
  ADD KEY `section_2` (`section`);

--
-- Indexes for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `student_key` (`student_id`),
  ADD KEY `subject_key` (`subject_code`),
  ADD KEY `faculty_student_subject` (`faculty_id`),
  ADD KEY `student_subject_admin_key` (`admin_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `faculty_key` (`faculty_id`),
  ADD KEY `code` (`code`),
  ADD KEY `title` (`title`),
  ADD KEY `subject_admin_fk` (`admin_id`);

--
-- Indexes for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`idnumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculty_peer_evaluation`
--
ALTER TABLE `faculty_peer_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_position` FOREIGN KEY (`position`) REFERENCES `adds` (`position_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD CONSTRAINT `fk_admin_evaluator` FOREIGN KEY (`evaluator_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_admin` FOREIGN KEY (`evaluator_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_faculty` FOREIGN KEY (`evaluatee_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluator_position` FOREIGN KEY (`evaluator_position`) REFERENCES `admin` (`position`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_evaluatee` FOREIGN KEY (`evaluatee_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `fk_evaluation_department` FOREIGN KEY (`department`) REFERENCES `faculty` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluation_student_section` FOREIGN KEY (`student_section`) REFERENCES `student` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_id_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_code_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_title_key` FOREIGN KEY (`subject_title`) REFERENCES `subject` (`title`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `fk_faculty_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_section` FOREIGN KEY (`section`) REFERENCES `adds` (`section_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD CONSTRAINT `faculty_student_subject` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_subject_admin_key` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_admin_fk` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`idnumber`),
  ADD CONSTRAINT `subject_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_faculty_fk` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`),
  ADD CONSTRAINT `subject_faculty_id` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
