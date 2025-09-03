-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 06:16 PM
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
(11, '221-0387-1', 'superadmin', 'Logged in', '2025-08-21 15:03:24'),
(12, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-08-21 15:03:27'),
(13, '221-0387-1', 'superadmin', 'Evaluation turned off', '2025-08-21 15:05:45'),
(14, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-08-21 15:17:59'),
(15, '100-0000-0', 'admin', 'Logged in', '2025-08-21 15:23:06'),
(16, '221-0387-1', 'superadmin', 'Logged in', '2025-08-21 15:27:01'),
(17, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-08-21 15:29:55'),
(18, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-08-21 15:31:16'),
(19, '221-0387-1', 'superadmin', 'Logged in', '2025-08-21 15:34:55'),
(20, '221-0387-1', 'superadmin', 'Logged in', '2025-08-21 15:35:08'),
(21, '221-0387-1', 'superadmin', 'Logged in', '2025-08-21 15:37:20'),
(22, '221-0387-1', 'superadmin', 'Logged in', '2025-08-25 16:39:50'),
(23, '221-0387-1', 'superadmin', 'Logged in', '2025-09-01 03:05:14'),
(24, '221-0387-1', 'superadmin', 'Evaluation turned off', '2025-09-01 03:05:38'),
(25, '100-0000-0', 'admin', 'Logged in', '2025-09-01 03:16:06'),
(26, '221-0387-1', 'superadmin', 'Logged in', '2025-09-01 03:32:39'),
(27, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-09-01 03:32:43'),
(28, '400-0000-0', 'admin', 'Logged in', '2025-09-01 18:55:06'),
(29, '100-0000-0', 'admin', 'Logged in', '2025-09-01 18:59:34'),
(30, '400-0000-0', 'admin', 'Logged in', '2025-09-01 19:00:33'),
(31, '100-0000-0', 'admin', 'Logged in', '2025-09-01 19:01:02'),
(32, '400-0000-0', 'admin', 'Logged in', '2025-09-01 19:01:16'),
(33, '100-0000-0', 'admin', 'Logged in', '2025-09-01 19:03:18'),
(34, '400-0000-0', 'admin', 'Logged in', '2025-09-01 19:05:04'),
(35, '400-0000-0', 'admin', 'Logged in', '2025-09-01 19:09:12'),
(36, '300-0000-0', 'admin', 'Logged in', '2025-09-01 19:20:45'),
(37, '200-0000-0', 'admin', 'Logged in', '2025-09-01 19:47:11'),
(38, '100-0000-0', 'admin', 'Logged in', '2025-09-01 19:54:40'),
(39, '100-0000-0', 'admin', 'Logged in', '2025-09-01 20:19:28'),
(40, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-09-01 20:34:12'),
(41, '100-0000-0', 'admin', 'Evaluated Faculty: Excel M. Faculty for 2025-2026 1st Semester', '2025-09-01 20:37:12'),
(42, '100-0000-0', 'admin', 'Evaluated Faculty: Maam E. Admin for 2025-2026 1st Semester', '2025-09-01 20:39:31'),
(43, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo B. Faculty for 2025-2026 1st Semester', '2025-09-01 20:41:46'),
(44, '100-0000-0', 'admin', 'Evaluated Faculty: Maam E. Admin for 2025-2026 1st Semester', '2025-09-01 20:45:13'),
(45, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-09-01 20:47:33'),
(46, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo B. Faculty for 2025-2026 1st Semester', '2025-09-01 20:49:43'),
(47, '000-0000-0', 'student', 'Logged in', '2025-09-01 20:51:25'),
(48, '000-0000-1', 'student', 'Logged in', '2025-09-01 20:56:38'),
(49, '300-0000-0', 'admin', 'Logged in', '2025-09-01 20:57:11'),
(50, '300-0000-0', 'admin', 'Logged in', '2025-09-01 20:58:21'),
(51, '000-0000-1', 'student', 'Rated 92% for MD101 handled by Jose Christoper Faculty', '2025-09-01 20:58:39'),
(52, '000-0000-1', 'student', 'Rated 94.67% for MD101 handled by Jose Christoper Faculty', '2025-09-01 21:03:02'),
(53, '300-0000-0', 'admin', 'Evaluated Faculty: Jose C. Faculty for 2025-2026 1st Semester', '2025-09-01 21:03:48'),
(54, '000-0000-1', 'student', 'Rated 84% for MD101 handled by Jose Christoper Faculty', '2025-09-01 21:08:24'),
(55, '100-0000-0', 'admin', 'Logged in', '2025-09-02 07:54:07'),
(56, '221-0387-1', 'superadmin', 'Logged in', '2025-09-02 07:54:42'),
(57, '008-0000-0', 'faculty', 'Logged in', '2025-09-02 08:16:20'),
(58, '000-0000-0', 'student', 'Logged in', '2025-09-02 09:07:20'),
(59, '008-0000-0', 'faculty', 'Logged in', '2025-09-02 09:08:18'),
(60, '100-0000-0', 'admin', 'Logged in', '2025-09-02 09:42:50'),
(61, '100-0000-0', 'admin', 'Logged in', '2025-09-02 09:45:27'),
(62, '221-0387-1', 'superadmin', 'Logged in', '2025-09-02 11:17:46'),
(63, '100-0000-0', 'admin', 'Logged in', '2025-09-02 11:27:23'),
(64, '100-0000-0', 'admin', 'Logged in', '2025-09-02 11:53:57'),
(65, '200-0000-0', 'admin', 'Logged in', '2025-09-02 12:14:42'),
(66, '200-0000-0', 'admin', 'Logged in', '2025-09-03 01:00:40'),
(67, '200-0000-0', 'admin', 'Evaluated Faculty: Program C. Admin for 2025-2026 1st Semester', '2025-09-03 16:40:04'),
(68, '200-0000-0', 'admin', 'Evaluated Faculty: Shirley M. Faculty for 2025-2026 1st Semester', '2025-09-03 16:41:24'),
(69, '000-0000-0', 'student', 'Logged in', '2025-09-03 16:42:31'),
(70, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 16:42:47'),
(71, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'1st Semester - 2024-2025\'', '2025-09-03 16:42:58'),
(72, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2024-2025\' to \'1st Semester - 2023-2024\'', '2025-09-03 16:44:33'),
(73, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2023-2024\' to \'2nd Semester - 2023-2024\'', '2025-09-03 16:45:07'),
(74, '004-0000-0', 'faculty', 'Logged in', '2025-09-03 16:46:24'),
(75, '002-0000-0', 'faculty', 'Logged in', '2025-09-03 16:46:37'),
(76, '000-0000-1', 'student', 'Logged in', '2025-09-03 16:47:19'),
(77, '100-0000-0', 'admin', 'Logged in', '2025-09-03 16:48:30'),
(78, '000-0000-0', 'student', 'Logged in', '2025-09-03 17:14:32'),
(79, '400-0000-0', 'admin', 'Logged in', '2025-09-03 17:17:00'),
(80, '100-0000-0', 'admin', 'Logged in', '2025-09-03 17:33:56'),
(81, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 19:15:49'),
(82, '200-0000-0', 'admin', 'Logged in', '2025-09-03 20:39:53'),
(83, '200-0000-0', 'admin', 'Evaluated Faculty: Frediz W. Superadmin for 2023-2024 2nd Semester', '2025-09-03 20:40:20'),
(84, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 20:41:50'),
(85, '000-0000-0', 'student', 'Logged in', '2025-09-03 20:47:50'),
(86, '000-0000-0', 'student', 'Rated 94.67% for GECC105 handled by Frediz Wanda Superadmin', '2025-09-03 20:49:06'),
(87, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 20:51:02'),
(88, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 20:57:47'),
(89, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 21:00:02'),
(90, '100-0000-0', 'admin', 'Logged in', '2025-09-03 21:05:23'),
(91, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 21:06:41'),
(92, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 21:06:54'),
(93, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 21:17:26'),
(94, '221-0387-2', 'superadmin', 'Logged in', '2025-09-03 21:17:35'),
(95, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 21:28:09'),
(96, '100-0000-0', 'admin', 'Logged in', '2025-09-03 21:46:07'),
(97, '221-0387-1', 'superadmin', 'Logged in', '2025-09-03 21:59:04'),
(98, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2023-2024\' to \'1st Semester - 2023-2024\'', '2025-09-03 21:59:10'),
(99, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2023-2024\' to \'Summer - 2023-2024\'', '2025-09-03 21:59:20'),
(100, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'Summer - 2023-2024\' to \'1st Semester - 2023-2024\'', '2025-09-03 21:59:23'),
(101, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2023-2024\' to \'1st Semester - 2023-2024\'', '2025-09-03 21:59:30'),
(102, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-09-03 22:44:15'),
(103, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2023-2024\' to \'1st Semester - 2025-2026\'', '2025-09-03 22:50:14'),
(104, '221-0387-1', 'superadmin', 'Logged in to the system', '2025-09-04 00:03:48'),
(105, '000-0000-0', 'student', 'Logged in', '2025-09-04 00:04:13'),
(106, '221-0387-1', 'superadmin', 'Logged in', '2025-09-04 00:04:19'),
(107, '221-0387-1', 'superadmin', 'Logged in', '2025-09-04 00:06:52'),
(108, '221-0387-1', 'superadmin', 'Logged in', '2025-09-04 00:07:11'),
(109, '100-0000-0', 'admin', 'Logged in', '2025-09-04 00:10:09');

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
(34, NULL, 'Dean', NULL, NULL),
(35, NULL, NULL, '4-B', NULL),
(36, NULL, NULL, NULL, 'CIS'),
(38, 'Professor V', NULL, NULL, NULL),
(39, NULL, NULL, NULL, 'CAS'),
(41, NULL, NULL, NULL, 'CVM'),
(42, 'Instructor II', NULL, NULL, NULL),
(43, NULL, NULL, NULL, 'BPED'),
(44, 'Instructor III', NULL, NULL, NULL),
(46, NULL, NULL, '1-C', NULL),
(47, NULL, NULL, '1-D', NULL),
(49, NULL, 'Program Chair', NULL, NULL),
(50, NULL, NULL, '1-A', NULL),
(51, NULL, NULL, '1-B', NULL),
(52, NULL, NULL, '2-B', NULL),
(53, NULL, NULL, '2-A', NULL),
(54, NULL, NULL, '2-C', NULL),
(56, 'Instructor I', NULL, NULL, NULL),
(57, 'Professor I', NULL, NULL, NULL),
(58, 'Professor II', NULL, NULL, NULL),
(59, 'Professor III', NULL, NULL, NULL),
(60, 'Professor IV', NULL, NULL, NULL),
(61, 'Associate Professor I', NULL, NULL, NULL),
(62, NULL, NULL, '2-D', NULL),
(63, NULL, NULL, '4-A', NULL),
(64, NULL, NULL, NULL, 'CAFF'),
(65, NULL, 'Research Facilitator', NULL, NULL),
(66, NULL, 'Head of Department', NULL, NULL),
(67, NULL, 'HR', NULL, NULL),
(68, NULL, 'HI', NULL, NULL),
(69, NULL, 'Registrar', NULL, NULL);

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
('100-0000-0', 'Edith', 'Ubuennga', 'Admin', '$2y$10$E/y.oUdVe.ZN1Icljq6SKeXtJoc89IAaVtvi8NoEvA9TXDGuC83eK', 'CIS', 'Dean', 'admin', 'active', 'Professor V'),
('200-0000-0', 'Van', 'Apollo', 'Mon', '$2y$10$Uom.ghb5C.VcYEHt80lg9Oxy1gMNSyMTc96b71k33EI5n4hqwNNOa', 'BPED', 'Dean', 'admin', 'active', 'Professor IV');

-- --------------------------------------------------------

--
-- Table structure for table `admin_evaluation`
--

CREATE TABLE `admin_evaluation` (
  `id` int(11) NOT NULL,
  `evaluator_id` varchar(50) NOT NULL,
  `evaluatee_id` varchar(50) NOT NULL,
  `evaluator_position` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `total_score` int(11) NOT NULL,
  `computed_rating` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `evaluation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, '1st Semester', '2025-2026', '2025-09-03 14:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_switch`
--

CREATE TABLE `evaluation_switch` (
  `id` int(11) NOT NULL,
  `status` enum('on','off') NOT NULL DEFAULT 'off',
  `user_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_switch`
--

INSERT INTO `evaluation_switch` (`id`, `status`, `user_id`) VALUES
(2, 'on', '221-0387-1');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `faculty_rank`, `role`, `status`) VALUES
('001-0000-0', 'Maricel', 'O', 'Pre', '$2y$10$6byd2/SIBl8NVyxzCSduxu7itynY1jj9WWYHxQ.56iq/DdOij4rue', 'CIS', 'Professor III', 'faculty', 'active'),
('002-0000-0', 'Jose', 'Christopher', 'Apocero', '$2y$10$4WnIQbSHB2HS0IR5WphnyOonEM3B4W3f4ES5tmaQYAc', 'CVM', 'Professor III', 'faculty', 'active'),
('100-0000-0', 'Edith', 'Ubuennga', 'Admin', '$2y$10$E/y.oUdVe.ZN1Icljq6SKeXtJoc89IAaVtvi8NoEvA9', 'CIS', 'Professor V', 'faculty', 'active'),
('200-0000-0', 'Van', 'Apollo', 'Mon', '$2y$10$Uom.ghb5C.VcYEHt80lg9Oxy1gMNSyMTc96b71k33EI', 'BPED', 'Professor IV', 'faculty', 'active'),
('221-0387-2', 'Frediz', 'Wanda', 'Superadmin', NULL, 'CAS', 'Professor III', 'faculty', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(11) NOT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `section`, `role`) VALUES
('000-0000-0', 'Clark', 'Joshua', 'Rojas', '$2y$10$P.AOcZeDPoC5Z76gbMsLWuXz0yWsb6pwzdu48Kc.an9w.H4f9utdC', 'CVM', '2-C', 'student');

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

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

CREATE TABLE `student_subject` (
  `idnumber` int(11) NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `subject_code` varchar(11) NOT NULL,
  `academic_year` varchar(9) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `admin_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `idnumber` varchar(111) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'superadmin',
  `department` varchar(255) DEFAULT NULL,
  `faculty_rank` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `status` varchar(11) DEFAULT 'active',
  `faculty` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `role`, `department`, `faculty_rank`, `position`, `status`, `faculty`) VALUES
('221-0387-1', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$AmZ.XQytv5324mnNsKfjGOydeNZ.UTkyuMio0mwI95BgutAx3Dbja', 'superadmin', '', NULL, 'HR', 'active', 'No'),
('221-0387-2', 'Frediz', 'Wanda', 'Superadmin', '$2y$10$BETvvlaqlT1rC6yz4.30H.zpaoe2/WjuK/N170JrhWzlfK2tm4nOi', 'superadmin', 'CAS', 'Professor III', 'HI', 'active', 'Yes');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `superadmin_id_key` (`user_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `department` (`department`),
  ADD KEY `fk_faculty_rank` (`faculty_rank`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

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
-- Constraints for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  ADD CONSTRAINT `superadmin_id_key` FOREIGN KEY (`user_id`) REFERENCES `superadmin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;

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
