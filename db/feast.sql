-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 04:55 PM
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
(60, '100-0000-0', 'admin', 'Logged in', '2025-07-19 19:26:19'),
(61, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty with a rating of 97.33%', '2025-07-19 19:38:49'),
(62, '221-0388-1', 'student', 'Logged in', '2025-07-19 19:55:08'),
(63, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:04:48'),
(64, '100-0000-0', 'admin', 'Logged in', '2025-07-19 20:08:08'),
(65, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:19:17'),
(66, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:28:43'),
(67, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:31:15'),
(68, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:40:46'),
(69, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 20:44:31'),
(70, '001-0000-0', 'admin', 'Logged in', '2025-07-19 20:53:18'),
(71, '001-0000-0', 'faculty', 'Logged in', '2025-07-19 20:54:47'),
(72, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 21:17:50'),
(73, '000-0000-0', 'student', 'Logged in', '2025-07-19 21:25:27'),
(74, '100-0000-0', 'admin', 'Logged in', '2025-07-19 21:28:22'),
(75, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 21:35:09'),
(76, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 21:35:48'),
(77, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 22:03:27'),
(78, '100-0000-0', 'admin', 'Logged in', '2025-07-19 22:04:15'),
(79, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty with a rating of 98.67%', '2025-07-19 22:05:08'),
(80, '100-0000-0', 'admin', 'Logged in', '2025-07-19 22:11:27'),
(81, '221-0387-1', 'superadmin', 'Logged in', '2025-07-19 22:21:51'),
(82, '221-0387-1', 'superadmin', 'Logged in', '2025-07-21 00:55:15'),
(83, '100-0000-0', 'admin', 'Logged in', '2025-07-21 00:58:22'),
(84, '001-0000-0', 'faculty', 'Logged in', '2025-07-21 01:08:26'),
(85, '001-0000-0', 'faculty', 'Logged in', '2025-07-21 19:20:56'),
(86, '000-0000-0', 'student', 'Logged in', '2025-07-21 20:58:29'),
(87, '000-0000-0', 'student', 'Rated 100% for ISPC-101 handled by Maricel Maam Faculty', '2025-07-21 21:00:02'),
(88, '221-0388-1', 'student', 'Logged in', '2025-07-21 21:01:08'),
(89, '100-0000-0', 'admin', 'Logged in', '2025-07-21 22:05:14'),
(90, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-21 22:05:49'),
(91, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-21 22:19:15'),
(92, '221-0387-1', 'superadmin', 'Logged in', '2025-07-21 22:20:25'),
(93, '100-0000-0', 'admin', 'Logged in', '2025-07-21 22:20:46'),
(94, '221-0387-1', 'superadmin', 'Logged in', '2025-07-21 22:23:55'),
(95, '100-0000-0', 'admin', 'Logged in', '2025-07-21 22:24:18'),
(96, '221-0388-1', 'student', 'Logged in', '2025-07-21 22:35:19'),
(97, '100-0000-0', 'admin', 'Logged in', '2025-07-21 22:52:02'),
(98, '100-0000-0', 'admin', 'Logged in', '2025-07-21 23:01:26'),
(99, '000-0000-0', 'student', 'Logged in', '2025-07-21 23:02:12'),
(100, '000-0000-0', 'student', 'Rated 98.67% for ISPC-101 handled by Maricel Maam Faculty', '2025-07-21 23:05:02'),
(101, '000-0000-0', 'student', 'Rated 97.33% for ISBA-101 handled by Maam Edith Admin', '2025-07-21 23:14:15'),
(102, '221-0388-1', 'student', 'Logged in', '2025-07-21 23:17:41'),
(103, '221-0388-1', 'student', 'Rated 97.33% for ISPC-101 handled by Maricel Maam Faculty', '2025-07-21 23:18:03'),
(104, '221-0388-1', 'student', 'Rated 98.67% for ISBA-101 handled by Maam Edith Admin', '2025-07-21 23:19:31'),
(105, '221-0387-1', 'superadmin', 'Logged in', '2025-07-21 23:20:02'),
(106, '000-0000-0', 'student', 'Logged in', '2025-07-21 23:20:40'),
(107, '000-0000-0', 'student', 'Logged in', '2025-07-23 00:24:56'),
(108, '221-0387-1', 'superadmin', 'Logged in', '2025-07-23 00:25:20'),
(109, '100-0000-0', 'admin', 'Logged in', '2025-07-23 00:25:51'),
(110, '000-0000-0', 'student', 'Rated 97.33% for ISAE-101 handled by Maricel Maam Faculty', '2025-07-23 00:30:48'),
(111, '221-0388-1', 'student', 'Logged in', '2025-07-23 00:43:53'),
(112, '221-0388-1', 'student', 'Rated 98.67% for ISAE-101 handled by Maricel Maam Faculty', '2025-07-23 00:44:27'),
(113, '100-0000-0', 'admin', 'Logged in', '2025-07-23 01:27:22'),
(114, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-23 01:35:11'),
(115, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-23 01:43:14'),
(116, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-23 01:59:21'),
(117, '100-0000-0', 'admin', 'Evaluated Faculty: Maam E. Admin for 2025-2026 1st Semester', '2025-07-23 02:02:08'),
(118, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 1st Semester', '2025-07-23 02:08:56'),
(119, '100-0000-0', 'admin', 'Evaluated Faculty: Maam E. Admin for 2025-2026 1st Semester', '2025-07-23 02:11:51'),
(120, '100-0000-0', 'admin', 'Logged in', '2025-07-23 14:08:30'),
(121, '221-0387-1', 'superadmin', 'Logged in', '2025-07-23 14:18:23'),
(122, '221-0387-1', 'superadmin', 'Logged in', '2025-07-23 14:20:02'),
(123, '100-0000-0', 'admin', 'Logged in', '2025-07-23 18:01:27'),
(124, '100-0000-0', 'admin', 'Logged in', '2025-07-23 18:14:19'),
(125, '001-0000-0', 'faculty', 'Logged in', '2025-07-23 18:17:03'),
(126, '100-0000-0', 'admin', 'Logged in', '2025-07-23 18:22:56'),
(127, '221-0387-1', 'superadmin', 'Logged in', '2025-07-23 18:54:50'),
(128, '100-0000-0', 'admin', 'Logged in', '2025-07-23 18:55:57'),
(129, '100-0000-0', 'admin', 'Logged in', '2025-07-23 20:04:50'),
(130, '221-0387-1', 'superadmin', 'Logged in', '2025-07-23 20:12:48'),
(131, '100-0000-0', 'admin', 'Logged in', '2025-07-23 20:55:20'),
(132, '001-0000-0', 'faculty', 'Logged in', '2025-07-23 20:55:33'),
(133, '500-0000-0', 'admin', 'Logged in', '2025-07-23 21:33:02'),
(134, '001-0000-0', 'faculty', 'Logged in', '2025-07-23 21:39:11'),
(135, '100-0000-0', 'admin', 'Logged in', '2025-07-24 10:37:53'),
(136, '100-0000-0', 'admin', 'Logged in', '2025-07-24 13:35:01'),
(137, '221-0387-1', 'superadmin', 'Logged in', '2025-07-24 13:38:33'),
(138, '100-0000-0', 'admin', 'Logged in', '2025-07-24 13:39:05'),
(139, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 2nd Semester', '2025-07-24 13:39:27'),
(140, '001-0000-0', 'faculty', 'Logged in', '2025-07-24 13:40:23'),
(141, '000-0000-1', 'student', 'Logged in', '2025-07-24 13:51:11'),
(142, '100-0000-0', 'admin', 'Logged in', '2025-07-24 13:51:33'),
(143, '000-0000-0', 'student', 'Logged in', '2025-07-24 16:43:55'),
(144, '000-0000-0', 'student', 'Rated 98.67% for ISPC-101 handled by Maricel Maam Faculty', '2025-07-24 16:44:16'),
(145, '100-0000-0', 'admin', 'Logged in', '2025-07-24 16:44:45'),
(146, '001-0000-0', 'faculty', 'Logged in', '2025-07-24 17:04:55'),
(147, '100-0000-0', 'admin', 'Logged in', '2025-07-24 17:39:56'),
(148, '000-0000-1', 'student', 'Logged in', '2025-07-24 17:41:39'),
(149, '000-0000-1', 'student', 'Rated 74.67% for ISBA-101 handled by Maam Edith Admin', '2025-07-24 17:42:03'),
(150, '100-0000-0', 'admin', 'Logged in', '2025-07-24 17:42:48'),
(151, '221-0387-1', 'superadmin', 'Logged in', '2025-07-24 17:56:14'),
(152, '100-0000-0', 'admin', 'Logged in', '2025-07-24 21:34:58'),
(153, '200-0000-0', 'admin', 'Logged in', '2025-07-24 21:37:29'),
(154, '100-0000-0', 'admin', 'Logged in', '2025-07-24 21:59:42'),
(155, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo B. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:00:05'),
(156, '100-0000-0', 'admin', 'Evaluated Faculty: Excel M. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:02:38'),
(157, '400-0000-0', 'admin', 'Logged in', '2025-07-24 22:07:30'),
(158, '221-0387-1', 'superadmin', 'Logged in', '2025-07-24 22:10:10'),
(159, '200-0000-0', 'admin', 'Logged in', '2025-07-24 22:10:51'),
(160, '200-0000-0', 'admin', 'Evaluated Faculty: Shirley M. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:15:24'),
(161, '221-0387-1', 'superadmin', 'Logged in', '2025-07-24 22:15:50'),
(162, '100-0000-0', 'admin', 'Logged in', '2025-07-24 22:28:34'),
(163, '221-0387-1', 'superadmin', 'Logged in', '2025-07-24 22:29:21'),
(164, '100-0000-0', 'admin', 'Logged in', '2025-07-24 22:31:38'),
(165, '100-0000-0', 'admin', 'Evaluated Faculty: Excel M. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:31:58'),
(166, '100-0000-0', 'admin', 'Evaluated Faculty: Maricel M. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:32:28'),
(167, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo B. Faculty for 2025-2026 2nd Semester', '2025-07-24 22:32:48');

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
(43, NULL, NULL, NULL, 'BPED-Math'),
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
(64, NULL, NULL, NULL, 'CAFF');

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
('100-0000-0', 'Maam', 'Edith', 'Admin', 'ILOVEDMMMSU', 'CIS', 'Dean', 'admin', 'active', 'Associate Professor I'),
('200-0000-0', 'Program', 'Chair', 'Admin', 'ILOVEDMMMSU', 'CAS', 'Program Chair', 'admin', 'active', 'Professor V'),
('300-0000-0', 'Vergil', 'Cry', 'Admin', 'ILOVEDMMMSU', 'CVM', 'Dean', 'admin', 'active', 'Professor IV'),
('400-0000-0', 'Yelan', 'Hydro', 'Admin', 'ILOVEDMMMSU', 'BPED-Math', 'Program Chair', 'admin', 'active', 'Professor III'),
('500-0000-0', 'Klee', 'Pyro', 'Admin', 'ILOVEDMMMSU', 'CAFF', 'Dean', 'admin', 'active', 'Professor IV');

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

--
-- Dumping data for table `admin_evaluation`
--

INSERT INTO `admin_evaluation` (`id`, `evaluator_id`, `evaluatee_id`, `evaluator_position`, `academic_year`, `semester`, `total_score`, `computed_rating`, `comments`, `department`, `evaluation_date`) VALUES
(46, '200-0000-0', '002-0000-0', 'Program Chair', '2025-2026', '2nd Semester', 75, 100.00, '', 'CAS', '2025-07-24 22:15:24'),
(47, '100-0000-0', '003-0000-0', 'Dean', '2025-2026', '2nd Semester', 44, 58.67, '', 'CIS', '2025-07-24 22:31:58'),
(48, '100-0000-0', '001-0000-0', 'Dean', '2025-2026', '2nd Semester', 68, 90.67, 'Great Instructor', 'CIS', '2025-07-24 22:32:28'),
(49, '100-0000-0', '008-0000-0', 'Dean', '2025-2026', '2nd Semester', 44, 58.67, '', 'CIS', '2025-07-24 22:32:48');

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
(22, '200-0000-0', '002-0000-0', '2nd Semester', '2025-2026', 75, 100.00, '', '2025-07-24 22:15:24', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}'),
(23, '100-0000-0', '003-0000-0', '2nd Semester', '2025-2026', 44, 58.67, '', '2025-07-24 22:31:58', '{\"q0\":5,\"q1\":4,\"q2\":3,\"q3\":2,\"q4\":1,\"q5\":2,\"q6\":3,\"q7\":4,\"q8\":5,\"q9\":4,\"q10\":3,\"q11\":2,\"q12\":1,\"q13\":2,\"q14\":3}'),
(24, '100-0000-0', '001-0000-0', '2nd Semester', '2025-2026', 68, 90.67, 'Great Instructor', '2025-07-24 22:32:28', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":3,\"q8\":3,\"q9\":4,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":4,\"q14\":5}'),
(25, '100-0000-0', '008-0000-0', '2nd Semester', '2025-2026', 44, 58.67, '', '2025-07-24 22:32:48', '{\"q0\":5,\"q1\":4,\"q2\":3,\"q3\":2,\"q4\":1,\"q5\":2,\"q6\":3,\"q7\":4,\"q8\":5,\"q9\":4,\"q10\":3,\"q11\":2,\"q12\":1,\"q13\":2,\"q14\":3}');

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
(102, '000-0000-0', 'CIS', 'ISPC-101', 'Computer Programming I', '2025-2026', '001-0000-0', 74.00, 98.67, 'Excellent Teaching', '2025-07-21 15:05:02', '1st Semester', '1-A'),
(103, '000-0000-0', 'CIS', 'ISBA-101', 'Accounting', '2025-2026', '100-0000-0', 73.00, 97.33, '', '2025-07-21 15:14:15', '1st Semester', '1-A'),
(104, '221-0388-1', 'CIS', 'ISPC-101', 'Computer Programming I', '2025-2026', '001-0000-0', 73.00, 97.33, 'Great', '2025-07-21 15:18:03', '1st Semester', '4-B'),
(105, '221-0388-1', 'CIS', 'ISBA-101', 'Accounting', '2025-2026', '100-0000-0', 74.00, 98.67, 'Naisu', '2025-07-21 15:19:31', '1st Semester', '4-B'),
(106, '000-0000-0', 'CIS', 'ISAE-101', 'Fundamentals', '2025-2026', '001-0000-0', 73.00, 97.33, '', '2025-07-22 16:30:48', '1st Semester', '1-A'),
(107, '221-0388-1', 'CIS', 'ISAE-101', 'Fundamentals', '2025-2026', '001-0000-0', 74.00, 98.67, 'Nice', '2025-07-22 16:44:27', '1st Semester', '4-B'),
(108, '000-0000-0', 'CIS', 'ISPC-101', 'Computer Programming I', '2025-2026', '001-0000-0', 74.00, 98.67, '', '2025-07-24 08:44:16', '2nd Semester', '1-A'),
(109, '000-0000-1', 'CIS', 'ISBA-101', 'Accounting', '2025-2026', '100-0000-0', 56.00, 74.67, 'Nice Teaching', '2025-07-24 09:42:03', '2nd Semester', '4-B');

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
(1, '2nd Semester', '2025-2026', '2025-07-24 05:38:44');

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
('001-0000-0', 'Maricel', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor V', 'faculty', 'active'),
('002-0000-0', 'Shirley', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CAS', 'Professor V', 'faculty', 'active'),
('003-0000-0', 'Excel', 'Maam', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor II', 'faculty', 'active'),
('004-0000-0', 'Mark', 'Kristian', 'Faculty', 'ILOVEDMMMSU', 'BPED-Math', 'Professor III', 'faculty', 'active'),
('005-0000-0', 'Jose', 'Christoper', 'Faculty', 'ILOVEDMMMSU', 'CVM', 'Professor I', 'faculty', 'active'),
('006-0000-0', 'Van', 'Apollo', 'Faculty', 'ILOVEDMMMSU', 'CVM', 'Professor II', 'faculty', 'active'),
('007-0000-0', 'Delwin', 'Caligma', 'Faculty', 'ILOVEDMMMSU', 'BPED-Math', 'Professor IV', 'faculty', 'active'),
('008-0000-0', 'Rufo', 'Baro', 'Faculty', 'ILOVEDMMMSU', 'CIS', 'Professor IV', 'faculty', 'active'),
('100-0000-0', 'Maam', 'Edith', 'Admin', NULL, 'CIS', 'Associate Professor I', 'faculty', 'active'),
('200-000-0', 'Program', 'Chair', 'Admin', NULL, 'CAS', 'Professor V', 'faculty', 'active'),
('300-0000-0', 'Vergil', 'Cry', 'Admin', NULL, 'CVM', 'Professor IV', 'faculty', 'active'),
('400-0000-0', 'Yelan', 'Hydro', 'Admin', NULL, 'BPED-Math', 'Professor III', 'faculty', 'active'),
('500-0000-0', 'Klee', 'Pyro', 'Admin', NULL, 'CAFF', 'Professor IV', 'faculty', 'active');

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
('000-0000-0', 'Mark', 'Kristian', 'Student', 'ILOVEDMMMSU', 'CAS', '1-A', 'student'),
('000-0000-1', 'Clark', 'Joshua', 'Student', 'ILOVEDMMMSU', 'CVM', '4-B', 'student'),
('221-0388-1', 'Charles', 'Adonis', 'Student', 'ILOVEDMMMSU', 'CIS', '4-B', 'student');

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
(27, '000-0000-0', 'ISPC-101', '001-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-21 21:00:02', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Great at teaching'),
(28, '000-0000-0', 'ISPC-101', '001-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-21 23:05:02', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, 'Excellent Teaching'),
(29, '000-0000-0', 'ISBA-101', '100-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-21 23:14:15', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 73, 97.33, ''),
(30, '221-0388-1', 'ISPC-101', '001-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-21 23:18:03', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":4,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, 'Great'),
(31, '221-0388-1', 'ISBA-101', '100-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-21 23:19:31', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":4,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, 'Naisu'),
(32, '000-0000-0', 'ISAE-101', '001-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-23 00:30:48', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":4,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, ''),
(33, '221-0388-1', 'ISAE-101', '001-0000-0', 'CIS', '2025-2026', '1st Semester', '2025-07-23 00:44:27', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, 'Nice'),
(34, '000-0000-0', 'ISPC-101', '001-0000-0', 'CIS', '2025-2026', '2nd Semester', '2025-07-24 16:44:16', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, ''),
(35, '000-0000-1', 'ISBA-101', '100-0000-0', 'CIS', '2025-2026', '2nd Semester', '2025-07-24 17:42:03', '{\"q0\":5,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":3,\"q9\":3,\"q10\":2,\"q11\":2,\"q12\":2,\"q13\":2,\"q14\":3}', 56, 74.67, 'Nice Teaching');

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

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`idnumber`, `student_id`, `subject_code`, `academic_year`, `semester`, `faculty_id`, `admin_id`) VALUES
(63, '000-0000-0', 'ISPC-101', '2025-2026', '1st Semester', '001-0000-0', NULL),
(64, '221-0388-1', 'ISPC-101', '2025-2026', '1st Semester', '001-0000-0', NULL),
(66, '000-0000-0', 'ISBA-101', '2025-2026', '1st Semester', '100-0000-0', NULL),
(67, '221-0388-1', 'ISBA-101', '2025-2026', '1st Semester', '100-0000-0', NULL),
(69, '000-0000-0', 'ISAE-101', '2025-2026', '1st Semester', '001-0000-0', NULL),
(70, '221-0388-1', 'ISAE-101', '2025-2026', '1st Semester', '001-0000-0', NULL),
(71, '000-0000-0', 'ISPE-110', '2025-2026', '2nd Semester', '003-0000-0', NULL),
(72, '000-0000-0', 'ISBA-101', '2025-2026', '2nd Semester', '100-0000-0', NULL),
(73, '000-0000-0', 'ISPC-101', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(74, '000-0000-0', 'ISAE-101', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(75, '221-0388-1', 'ISPE-110', '2025-2026', '2nd Semester', '003-0000-0', NULL),
(76, '221-0388-1', 'ISBA-101', '2025-2026', '2nd Semester', '100-0000-0', NULL),
(77, '221-0388-1', 'ISPC-101', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(78, '221-0388-1', 'ISAE-101', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(79, '000-0000-1', 'ISPE-110', '2025-2026', '2nd Semester', '003-0000-0', NULL),
(80, '000-0000-1', 'ISBA-101', '2025-2026', '2nd Semester', '100-0000-0', NULL),
(81, '000-0000-1', 'ISPC-101', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(82, '000-0000-1', 'ISAE-101', '2025-2026', '2nd Semester', '001-0000-0', NULL);

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
(37, 'ISPC-101', 'Computer Programming I', '001-0000-0', NULL, 'CIS'),
(38, 'ISBA-101', 'Accounting', '100-0000-0', NULL, 'CIS'),
(39, 'ISAE-101', 'Fundamentals', '001-0000-0', NULL, 'CIS'),
(42, 'ISPE-110', 'IT Audit', '003-0000-0', NULL, 'CIS'),
(43, 'PATHFIT-1', 'Physical Education', '200-000-0', NULL, 'CAS');

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
('221-0387-1', 'Clark Joshua', 'Joshua', 'Rojas', '12345678', 'superadmin', 'active'),
('221-1230-1', 'Super', 'Admin', 'Superadmin', '12345678', 'superadmin', 'active');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
