-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 05:21 PM
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
(285, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 12:58:54'),
(286, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-19 14:03:20'),
(287, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:06:34'),
(288, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:08:22'),
(289, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:09:02'),
(290, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 14:11:03'),
(291, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:11:24'),
(292, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:14:33'),
(293, '000-0000-1', 'student', 'Logged in', '2025-10-19 14:18:07'),
(294, '000-0000-1', 'student', 'Rated 90.67% for ISBA105 handled by Rufo A Baro', '2025-10-19 14:18:43'),
(295, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:20:44'),
(296, '221-0387-10', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-10-19 14:20:52'),
(297, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:21:01'),
(298, '000-0000-1', 'student', 'Logged in', '2025-10-19 14:22:31'),
(299, '000-0000-1', 'student', 'Rated 92% for ISBA105 handled by Rufo A Baro', '2025-10-19 14:23:09'),
(300, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:23:43'),
(301, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 14:24:12'),
(302, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:24:50'),
(303, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:25:27'),
(304, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:26:10'),
(305, '221-0387-10', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-19 14:26:14'),
(306, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:26:24'),
(307, '221-0388-1', 'student', 'Logged in', '2025-10-19 14:26:51'),
(308, '221-0388-1', 'student', 'Rated 85.33% for ISBA105 handled by Rufo A Baro', '2025-10-19 14:27:06'),
(309, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 14:27:21'),
(310, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:27:47'),
(311, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:29:21'),
(312, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 14:31:40'),
(313, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:32:01'),
(314, '221-0387-10', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-10-19 14:32:20'),
(315, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 14:32:29'),
(316, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:32:55'),
(317, '100-0000-0', 'admin', 'Logged in', '2025-10-19 14:33:48'),
(318, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo A. Baro for 2025-2026 2nd Semester', '2025-10-19 14:34:13'),
(319, '221-0387-10', 'superadmin', 'Logged in', '2025-10-19 14:34:59'),
(320, '100-0000-0', 'admin', 'Logged in', '2025-10-19 15:13:09'),
(321, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 15:26:48'),
(322, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-19 15:27:48'),
(323, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 15:28:02'),
(324, '000-0000-1', 'student', 'Logged in', '2025-10-19 15:28:42'),
(325, '000-0000-1', 'student', 'Rated 86.67% for ISPC104  handled by Clark Joshua V Rojas', '2025-10-19 15:28:58'),
(326, '100-0000-0', 'admin', 'Logged in', '2025-10-19 15:42:31'),
(327, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-10-19 15:47:56'),
(328, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 15:48:04'),
(329, '221-0388-1', 'student', 'Logged in', '2025-10-19 15:49:02'),
(330, '221-0388-1', 'student', 'Rated 82.67% for ISPC104  handled by Clark Joshua V Rojas', '2025-10-19 15:49:43'),
(331, '100-0000-0', 'admin', 'Logged in', '2025-10-19 15:50:26'),
(332, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-19 15:52:28'),
(333, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-10-19 15:52:44'),
(334, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-19 16:06:12'),
(335, '221-0388-1', 'student', 'Logged in', '2025-10-19 16:06:37'),
(336, '221-0388-1', 'student', 'Rated 93.33% for ISPC106 handled by Edelita C Ebuenga', '2025-10-19 16:06:54'),
(337, '100-0000-0', 'admin', 'Logged in', '2025-10-19 16:07:20'),
(338, '001-0000-0', 'faculty', 'Logged in', '2025-10-19 16:10:50'),
(339, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 16:16:15'),
(340, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-10-19 16:16:19'),
(341, '241-0132-1', 'student', 'Logged in', '2025-10-19 17:23:41'),
(342, '221-0387-1', 'superadmin', 'Logged in', '2025-10-19 20:48:13'),
(343, '00001', 'faculty', 'Logged in', '2025-10-19 20:53:12'),
(344, '100-0000-0', 'admin', 'Logged in', '2025-10-19 21:38:50'),
(345, '100-0000-0', 'admin', 'Logged in', '2025-10-20 15:32:25'),
(346, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 15:46:14'),
(347, '00001', 'admin', 'Logged in', '2025-10-20 16:07:28'),
(348, '00001', 'admin', 'Evaluated Faculty: Ferdinand E. Marcos for 2025-2026 1st Semester', '2025-10-20 16:23:18'),
(349, '00001', 'admin', 'Evaluated Faculty: Manuel L. Quezon for 2025-2026 1st Semester', '2025-10-20 16:27:59'),
(350, '00001', 'admin', 'Evaluated Faculty: Imelda R. Marcos for 2025-2026 1st Semester', '2025-10-20 16:29:00'),
(351, '00001', 'admin', 'Evaluated Faculty: Ramon M. Magsaysay for 2025-2026 1st Semester', '2025-10-20 16:35:52'),
(352, '100-0000-0', 'admin', 'Logged in', '2025-10-20 16:46:02'),
(353, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 16:48:30'),
(354, '00001', 'admin', 'Logged in', '2025-10-20 16:57:33'),
(355, '100-0000-0', 'admin', 'Logged in', '2025-10-20 17:35:25'),
(356, '00001', 'admin', 'Logged in', '2025-10-20 17:35:51'),
(357, '100-0000-0', 'admin', 'Logged in', '2025-10-20 17:50:47'),
(358, '00001', 'admin', 'Logged in', '2025-10-20 17:53:58'),
(359, '100-0000-0', 'admin', 'Logged in', '2025-10-20 17:58:00'),
(360, '241-0047-1', 'student', 'Logged in', '2025-10-20 18:15:53'),
(361, '241-0047-1', 'student', 'Rated 85.33% for GECC105 handled by Claro M Recto', '2025-10-20 18:19:46'),
(362, '100-0000-0', 'admin', 'Logged in', '2025-10-20 18:24:28'),
(363, '241-0047-1', 'student', 'Rated 85.33% for GEMC101a handled by Leonor R Rivera', '2025-10-20 18:27:08'),
(364, '00001', 'admin', 'Logged in', '2025-10-20 18:30:09'),
(365, '00001', 'admin', 'Evaluated Faculty: Benigno S. Aquino for 2025-2026 1st Semester', '2025-10-20 18:34:00'),
(366, '00001', 'admin', 'Evaluated Faculty: Aurora A. Quezon for 2025-2026 1st Semester', '2025-10-20 18:36:14'),
(367, '00001', 'admin', 'Evaluated Faculty: Amelita M. Ramos for 2025-2026 1st Semester', '2025-10-20 18:46:57'),
(368, '00001', 'admin', 'Evaluated Faculty: Corazon C. Aquino for 2025-2026 1st Semester', '2025-10-20 18:49:44'),
(369, '00001', 'admin', 'Evaluated Faculty: Ramon M. Magsaysay for 2025-2026 1st Semester', '2025-10-20 18:53:23'),
(370, '100-0000-0', 'admin', 'Logged in', '2025-10-20 19:04:36'),
(371, '00001', 'admin', 'Logged in', '2025-10-20 19:06:14'),
(372, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 19:44:47'),
(373, '00012', 'faculty', 'Logged in', '2025-10-20 19:49:10'),
(374, '00001', 'admin', 'Logged in', '2025-10-20 19:50:13'),
(375, '100-0000-0', 'admin', 'Logged in', '2025-10-20 20:01:19'),
(376, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo A. Baro for 2025-2026 1st Semester', '2025-10-20 20:05:32'),
(377, '00001', 'admin', 'Logged in', '2025-10-20 20:09:01'),
(378, '00000', 'admin', 'Logged in', '2025-10-20 21:03:48'),
(379, '00001', 'admin', 'Logged in', '2025-10-20 21:05:08'),
(380, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 21:42:05'),
(381, '00001', 'admin', 'Logged in', '2025-10-20 21:51:16'),
(382, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 21:52:26'),
(383, '00001', 'admin', 'Logged in', '2025-10-20 22:13:43'),
(384, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 22:13:59'),
(385, '00001', 'admin', 'Logged in', '2025-10-20 22:51:02'),
(386, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 22:51:34'),
(387, '00001', 'admin', 'Logged in', '2025-10-20 23:05:23'),
(388, '100-0000-0', 'admin', 'Logged in', '2025-10-20 23:10:47'),
(389, '221-0387-1', 'superadmin', 'Logged in', '2025-10-20 23:13:08'),
(390, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 16:32:32'),
(391, '00001', 'admin', 'Logged in', '2025-10-21 17:47:31'),
(392, '100-0000-0', 'admin', 'Logged in', '2025-10-21 17:47:45'),
(393, '00031', 'faculty', 'Logged in', '2025-10-21 17:48:09'),
(394, '00011', 'faculty', 'Logged in', '2025-10-21 17:48:33'),
(395, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 17:49:02'),
(396, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 20:59:23'),
(397, '241-0208-1', 'student', 'Logged in', '2025-10-21 21:57:50'),
(398, '241-0208-1', 'student', 'Rated 80% for ISPC104  handled by Clark Joshua V Rojas', '2025-10-21 21:58:04'),
(399, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 21:58:28'),
(400, '000-0000-1', 'student', 'Logged in', '2025-10-21 22:28:34'),
(401, '00012', 'faculty', 'Logged in', '2025-10-21 22:32:25'),
(402, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 22:32:52'),
(403, '00012', 'faculty', 'Logged in', '2025-10-21 22:33:09'),
(404, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 22:37:56'),
(405, '00001', 'admin', 'Logged in', '2025-10-21 22:38:23'),
(406, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 22:46:18'),
(407, '00001', 'admin', 'Logged in', '2025-10-21 22:47:07'),
(408, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 22:51:59');

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) DEFAULT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adds`
--

INSERT INTO `adds` (`id`, `rank_name`, `position_name`, `section_name`, `department_name`) VALUES
(34, NULL, 'Dean', NULL, NULL),
(35, NULL, NULL, '4-B', NULL),
(36, NULL, NULL, NULL, 'CIS'),
(38, 'Professor V', NULL, NULL, NULL),
(39, NULL, NULL, NULL, 'CAS-Science in Biology'),
(41, NULL, NULL, NULL, 'CVM'),
(42, 'Instructor II', NULL, NULL, NULL),
(43, NULL, NULL, NULL, 'BPEd'),
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
(64, NULL, NULL, NULL, 'CAFF-Forestry'),
(65, NULL, 'Research Facilitator', NULL, NULL),
(66, NULL, 'Chair Person', NULL, NULL),
(67, NULL, 'Human Resource', NULL, NULL),
(68, NULL, 'Head Instruction', NULL, NULL),
(69, NULL, 'Registrar', NULL, NULL),
(70, NULL, NULL, NULL, 'IABE'),
(71, NULL, NULL, NULL, 'CAS-Arts in English Language'),
(75, NULL, NULL, NULL, 'CA-Animal Science'),
(76, NULL, NULL, NULL, 'CAFF-Agroforestry'),
(79, NULL, NULL, NULL, 'ABM-Entreprenuership'),
(80, NULL, NULL, NULL, 'BEEd'),
(81, NULL, NULL, NULL, 'BECEd'),
(84, NULL, NULL, NULL, 'BTLEd'),
(85, NULL, NULL, NULL, 'CA-Crop Science (Agronomy)'),
(86, NULL, NULL, NULL, 'CA-Crop Science (Horticulture)'),
(87, NULL, NULL, NULL, 'CA-Crop Protection'),
(88, NULL, NULL, NULL, 'CA-Soil Science'),
(89, NULL, NULL, NULL, 'CA-Apiculture'),
(90, NULL, NULL, NULL, 'BSEd-English'),
(91, NULL, NULL, NULL, 'BSEd-Filipino'),
(92, NULL, NULL, NULL, 'BSEd-Science'),
(93, NULL, NULL, NULL, 'BSEd-Mathematics'),
(94, NULL, NULL, NULL, 'ABM-Cooperative'),
(95, NULL, NULL, NULL, 'IES'),
(96, 'Associate Professor II', NULL, NULL, NULL),
(97, 'Associate Professor III', NULL, NULL, NULL),
(98, 'Associate Professor IV', NULL, NULL, NULL),
(99, 'Associate Professor V', NULL, NULL, NULL),
(100, 'Assistant Professor I', NULL, NULL, NULL),
(101, 'Assistant Professor II', NULL, NULL, NULL),
(102, 'Assistant Professor III', NULL, NULL, NULL),
(103, 'Assistant Professor IV', NULL, NULL, NULL),
(104, 'Professor VI', NULL, NULL, NULL),
(105, NULL, NULL, '3-A', NULL),
(106, NULL, NULL, '3-B', NULL),
(107, NULL, NULL, '3-C', NULL),
(108, NULL, NULL, '3-D', NULL),
(109, NULL, NULL, '4-C', NULL),
(110, NULL, NULL, '4-D', NULL),
(111, NULL, 'Director', NULL, NULL),
(112, NULL, NULL, NULL, 'CAS-General Education');

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
  `position` varchar(50) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `status` varchar(11) NOT NULL DEFAULT 'active',
  `faculty_rank` varchar(255) DEFAULT NULL,
  `is_faculty` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `position`, `role`, `status`, `faculty_rank`, `is_faculty`) VALUES
('00001', 'Desiry Mitch', 'R', 'Ocampo', '$2y$10$Xl9prTbmhrh0b./63kuREeG4N8CydFLL5bbP72TZ0gXxIDaU4KuSe', 'Program Chair', 'admin', 'active', 'Professor III', 'yes'),
('100-0000-0', 'Edelita', 'C', 'Ebuenga', '$2y$10$OftwVTuPCCkL0W5C1sI5ee12pvEkAmn859sa6TkSGH0ySDPMGso2u', 'Dean', 'admin', 'active', 'Professor II', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `admin_departments`
--

CREATE TABLE `admin_departments` (
  `admin_idnumber` varchar(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_departments`
--

INSERT INTO `admin_departments` (`admin_idnumber`, `department_name`) VALUES
('00001', 'BECEd'),
('00001', 'BEEd'),
('00001', 'BPEd'),
('100-0000-0', 'CIS');

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
  `department` varchar(255) DEFAULT NULL,
  `evaluation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation`
--

INSERT INTO `admin_evaluation` (`id`, `evaluator_id`, `evaluatee_id`, `evaluator_position`, `academic_year`, `semester`, `total_score`, `computed_rating`, `comments`, `department`, `evaluation_date`) VALUES
(64, '100-0000-0', '001-0000-0', 'Dean', '2025-2026', '2nd Semester', 64, 85.33, '', 'CIS', '2025-10-19 14:34:13'),
(74, '100-0000-0', '001-0000-0', 'Dean', '2025-2026', '1st Semester', 46, 61.33, '', 'CIS', '2025-10-20 20:05:32');

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
(40, '100-0000-0', '001-0000-0', '2nd Semester', '2025-2026', 64, 85.33, '', '2025-10-19 14:34:13', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":4,\"q6\":3,\"q7\":4,\"q8\":4,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":4,\"q13\":5,\"q14\":4}'),
(50, '100-0000-0', '001-0000-0', '1st Semester', '2025-2026', 46, 61.33, '', '2025-10-20 20:05:32', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":3,\"q5\":2,\"q6\":2,\"q7\":3,\"q8\":2,\"q9\":2,\"q10\":3,\"q11\":2,\"q12\":3,\"q13\":3,\"q14\":3}');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `department` varchar(255) NOT NULL,
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
(132, '241-0047-1', 'BECEd', 'GECC105', 'Theory of Probability', '2025-2026', '00011', 64.00, 85.33, '', '2025-10-20 10:19:46', '1st Semester', '2-D'),
(133, '241-0047-1', 'BECEd', 'GEMC101a', 'Life and Works of Rizal', '2025-2026', '00012', 64.00, 85.33, '', '2025-10-20 10:27:08', '1st Semester', '2-D'),
(134, '241-0208-1', 'CIS', 'ISPC104 ', ' IT Audit and Control', '2025-2026', '221-0387-1', 60.00, 80.00, '', '2025-10-21 13:58:04', '1st Semester', '3-D');

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
(1, '1st Semester', '2025-2026', '2025-10-19 08:06:12');

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
(4, 'on', '221-0387-1');

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
  `department` varchar(255) NOT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `faculty_rank`, `role`, `status`) VALUES
('00000', 'Jose', 'P', 'Rizal', '$2y$10$qI2V9jn5MTnLvlUhmhsgR.6AKyUqA7vJzVY5sC8TPRCmsGHjo/4V.', 'ABM-Cooperative', 'Associate Professor III', 'faculty', 'active'),
('00001', 'Desiry Mitch', 'R', 'Ocampo', NULL, 'BPEd', 'Professor III', 'faculty', 'active'),
('00002', 'Maria', 'C', 'Aquino', '$2y$10$I5RDv36rqpStqTFK.Z/MyejcTX.DzCElESyDiIkcinVAh9//kLove', 'ABM-Cooperative', 'Associate Professor I', 'faculty', 'active'),
('00003', 'Andres', 'B', 'Bonifacio', '$2y$10$wUfU3GX5U5IZyIGnpFBWTupCi/atc92YEvGAUOf/vjF.5JU70LyRS', 'ABM-Cooperative', 'Assistant Professor II', 'faculty', 'active'),
('00004', 'Gabriela', 'S', 'Silang', '$2y$10$ylepv15qmGmxRf0Outo3pew892moWf69ihBGd79Z6XhA0tRSd.Xu2', 'ABM-Cooperative', 'Professor III', 'faculty', 'active'),
('00005', 'Emilio', 'A', 'Aguinaldo', '$2y$10$OQssSLb2L4FCWnj88T2uBuWKI2mRhbzJ4lN9udBmW5qWQb.RDVtyW', 'ABM-Cooperative', 'Instructor II', 'faculty', 'active'),
('00006', 'Teresa', 'M', 'Magbanua', '$2y$10$6uSgeYrmLLkaL5EI5Dh1W.Hwvyp7dQAQa2m77P/yc8esBVht3ADie', 'ABM-Entreprenuership', 'Professor I', 'faculty', 'active'),
('00007', 'Juan', 'L', 'Luna', '$2y$10$XEalBla4V1EIBxolEv8IO.HdBPzBq1MzZvlI06x3rwOHFMBJ5PuLy', 'ABM-Entreprenuership', 'Professor II', 'faculty', 'active'),
('00008', 'Melchora', 'A', 'Aquino', '$2y$10$7K6F2/S2l9SdPNVRaa85LOFxoMUbgil8RO39ewEb1n2eMo9yCJn1G', 'ABM-Entreprenuership', 'Instructor I', 'faculty', 'active'),
('00009', 'Apolinario', 'M', 'Mabini', '$2y$10$OXhJBHvQQ/HiqsmGGLOdguEmd57e06EWujoJ43ErWH9QtbB6sMJcu', 'ABM-Entreprenuership', 'Associate Professor V', 'faculty', 'active'),
('00010', 'Gregorio', 'D', 'Del Pilar', '$2y$10$j7PTMyw6seVG1uYiUrlGr.laIoOOwyzKxMfDDA8krunRKJAH5mfs6', 'ABM-Entreprenuership', 'Professor I', 'faculty', 'active'),
('00011', 'Claro', 'M', 'Recto', '$2y$10$Ha1ZNX1OJ64RnaVJH6Xx4uSlVxqlZ4pjA0DsVbI2hE33wR/Rm366O', 'BECEd', 'Assistant Professor I', 'faculty', 'active'),
('00012', 'Leonor', 'R', 'Rivera', '$2y$10$AMLYvDq9XR.DmPrF/6KzP.vPZxv5TqQGLPtskEcBZdbGP3hXmhdNa', 'BECEd', 'Assistant Professor IV', 'faculty', 'active'),
('00013', 'Manuel', 'L', 'Quezon', '$2y$10$tgbasNS5kJj0EfsxrwDhae.CN72tym6U0h00MJxsLjJQvDz8VFfri', 'BECEd', 'Professor V', 'faculty', 'active'),
('00014', 'Trinidad', 'T', 'Tecson', '$2y$10$Vm17A3DFNLrMxFkHU1UiV.tbemrF8aKouXmCX2o8aQGqsyKazTt6y', 'BECEd', 'Assistant Professor I', 'faculty', 'active'),
('00015', 'Sergio', 'O', 'Osmena', '$2y$10$XRv.P0lD2FCFAKx63jBZgurBwQIbPSHW1mKBHqIA51N2qkS.5tQOG', 'BECEd', 'Associate Professor III', 'faculty', 'active'),
('00016', 'Benigno', 'S', 'Aquino', '$2y$10$glR2LdKtor.NY2pnEVkg6.lLeTsWqwGVJFzxURMbuLWPqTSJhjlge', 'BEEd', 'Professor II', 'faculty', 'active'),
('00017', 'Corazon', 'C', 'Aquino', '$2y$10$oyWQ4RXVueiWY69q6bACk.0FSlEoona2Jqu.D4C4WEY1zr3AR2S5K', 'BEEd', 'Professor I', 'faculty', 'active'),
('00018', 'Ramon', 'M', 'Magsaysay', '$2y$10$14eIBRFvIiauvmbBUNOIIe4lQnBLIPVsEJgAnGZPBfPV0mKLTgc2O', 'BEEd', 'Assistant Professor II', 'faculty', 'active'),
('00019', 'Aurora', 'A', 'Quezon', '$2y$10$KuZvAkOBt48uMYxbaMfwD.W3UjCZhR2v6hmaBt2DVQAOGhkIrKIKe', 'BEEd', 'Associate Professor III', 'faculty', 'active'),
('00020', 'Diosdado', 'P', 'Macapagal', '$2y$10$cbY/TfN544nZC6n..rqzgetrnmuNZxiz4XSvqSk3ldB3KZcFS6Pyu', 'BEEd', 'Instructor I', 'faculty', 'active'),
('00021', 'Eva', 'M', 'Macapagal', '$2y$10$nFZS5y7wmfUlNUyT2BWto.I/yc5niRImr.HdS.MIc51lEWSXrmgG6', 'BPEd', 'Instructor I', 'faculty', 'active'),
('00022', 'Ferdinand', 'E', 'Marcos', '$2y$10$ACsIxGBjl7Qx7Wtb9ii3TuAG6nIXhiJuyvNqlyh81JdQZG3cnwtw6', 'BPEd', 'Assistant Professor IV', 'faculty', 'active'),
('00023', 'Imelda', 'R', 'Marcos', '$2y$10$FAUfg/F6D87Cqa5AzOfh0OSs.TT4Oll0KTgydvi6mlaY8uTyBHg/q', 'BPEd', 'Associate Professor IV', 'faculty', 'active'),
('00024', 'Fidel', 'V', 'Ramos', '$2y$10$A3YBtpTDJ2Nz11TePeXctO8JyEs7nKmi9KTq6aCvnyipORBPLQZRm', 'BPEd', 'Associate Professor III', 'faculty', 'active'),
('00025', 'Amelita', 'M', 'Ramos', '$2y$10$Ew0DGy2PKbwg05guYrDPK.MIvE0bn8BBYKgbL68hyncO4xeLfjstS', 'BPEd', 'Instructor I', 'faculty', 'active'),
('00026', 'Joseph', 'E', 'Estrada', '$2y$10$FonTqv9qrk6AnLhICCt3Ze3AQ1J6u2O3Z7Ob6O9grxdRSYZqEiirS', 'BSEd-English', 'Professor II', 'faculty', 'active'),
('00027', 'Luisa', 'P', 'Estrada', '$2y$10$HNR0MLQJzLUHT1PQbdcsB.3dQlABsMZy6lYKFhJ2YULzx4i6o4LPW', 'BSEd-English', 'Professor VI', 'faculty', 'active'),
('00028', 'Gloria', 'M', 'Arroyo', '$2y$10$9CkB1.N2iALajawJeyGXdeOpCh5NQvtBpEG38xSRTPazSlJkJTkje', 'BSEd-English', 'Instructor II', 'faculty', 'active'),
('00029', 'Jose Miguel', 'T', 'Arroyo', '$2y$10$PZboae7BkNSFJRHfgvtT/.D.sglpKcqcTwuKGcfdhtM8fOdxmLBTm', 'BSEd-English', 'Assistant Professor III', 'faculty', 'active'),
('00030', 'Rodrigo', 'R', 'Duterte', '$2y$10$6o..pmVPfqMT29wMtCYnAeG1Bx.9afw2dnlwwABc695n3BnhEMzUK', 'BSEd-English', 'Professor IV', 'faculty', 'active'),
('00031', 'Elizabeth', 'Z', 'Duterte', '$2y$10$zzh29s1VIcigQB64v8dToemqc9sJIyVo9GJIB778C6YNU8qSgBVwG', 'BSEd-Filipino', 'Professor V', 'faculty', 'active'),
('00032', 'Leni', 'G', 'Robredo', '$2y$10$bGIjlLK5cqkNrQS4eMNOMehy4E0wMAuJ5fxQrvzJ7MvqEgmLCYqY6', 'BSEd-Filipino', 'Professor V', 'faculty', 'active'),
('00033', 'Jesse', 'M', 'Robredo', '$2y$10$gYzkFsYHatZQWgZ29uEqhedvdGH9/FYLLPaOPaHb4SVoFNw7Oq36a', 'BSEd-Filipino', 'Professor II', 'faculty', 'active'),
('00034', 'Francis', 'P', 'Pangilinan', '$2y$10$9vIou7SUgZ/sEWRLCr5pyuUVZP2ZfBtxwfBaK.MhNqlxwzH1jhW1O', 'BSEd-Filipino', 'Assistant Professor IV', 'faculty', 'active'),
('00035', 'Sharon', 'C', 'Pangilinan', '$2y$10$5QkoG1alPClxKsb4jFoHPurpINdp4Uco6lHoi53ohrbPP6OCdid.u', 'BSEd-Filipino', 'Professor I', 'faculty', 'active'),
('00036', 'Grace', 'P', 'Poe', '$2y$10$m3bTqo8vm3q4B/Z7JY2xweR7XvTCqI7fGkwmlOGfSZOdOkWW1KJoe', 'BSEd-Mathematics', 'Professor V', 'faculty', 'active'),
('00037', 'Fernando', 'P', 'Poe', '$2y$10$VwdaBuWrJ..2KrDgT/NzGe4fGuXGQSLtJLK7O/laFmYkfCom4kbba', 'BSEd-Mathematics', 'Associate Professor III', 'faculty', 'active'),
('00038', 'Susan', 'R', 'Poe', '$2y$10$hA4wMznPHk56nTkwi3Fgpu1eJcO8AdzWXYT8G/p7pNj0zg6H4JGhG', 'BSEd-Mathematics', 'Assistant Professor IV', 'faculty', 'active'),
('00039', 'Panfilo', 'M', 'Lacson', '$2y$10$0PvujyDrn6gVpbOECxLBvOyXK3CNKTbYxPcYBkhVZmCyxalvEpFpG', 'BSEd-Mathematics', 'Professor II', 'faculty', 'active'),
('00040', 'Vicente', 'C', 'Sotto', '$2y$10$D3K0XrIbG.OB2O8bpGhVwuzwsZuoOa2SMDAyms1jMrswAt38oVTxy', 'BSEd-Mathematics', 'Assistant Professor III', 'faculty', 'active'),
('00041', 'Helen', 'G', 'Sotto', '$2y$10$7R31WFoo/WmTqrrbFenWduC6awEsjQk0Mg51XOvHwtBbpnpFUYmve', 'BSEd-Science', 'Associate Professor IV', 'faculty', 'active'),
('00042', 'Manny', 'P', 'Pacquiao', '$2y$10$maoqwHXl2VHGcwoL6B20Au2yfgR.cv3VhrQFgVpP41cueTZdYrXoS', 'BSEd-Science', 'Professor I', 'faculty', 'active'),
('00043', 'Jinkee', 'J', 'Pacquiao', '$2y$10$oRwJrih6rCanFvxw0nPIW.LXvzZNSHg5Whn1llf/m9wPBkYadSZPW', 'BSEd-Science', 'Associate Professor I', 'faculty', 'active'),
('00044', 'Isko', 'M', 'Domagoso', '$2y$10$iYhjhnnJJtEgwFcuHZUoWe2jO0qaZz8Ov9I2iXgY73i22K.DVUOvy', 'BSEd-Science', 'Assistant Professor IV', 'faculty', 'active'),
('00045', 'Sara', 'Z', 'Duterte', '$2y$10$9YFE0IeIr8mBNGotGP1Xde2eZhlx.TkIlFDmGX26oYsGMql6ZkzsC', 'BSEd-Science', 'Associate Professor I', 'faculty', 'active'),
('00046', 'Bongbong', 'R', 'Marcos', '$2y$10$7mX5ATWSCmS8gyYnb/vxD.GFH4fANK2nuRTmbVSA/JobcQfKQV4li', 'BTLEd', 'Professor I', 'faculty', 'active'),
('00047', 'Liza', 'A', 'Marcos', '$2y$10$XBqJcR7tmauYkKvOLDtIjOapvFrmHgGlW42Z2Py5gqwxpG5m6Dda6', 'BTLEd', 'Professor III', 'faculty', 'active'),
('00048', 'Alan Peter', 'S', 'Cayetano', '$2y$10$atHmwdVdlr0voWiVQ1BWnuTKdrePKxGQQbEHyLtC4IwMmsRzdAdfC', 'BTLEd', 'Professor II', 'faculty', 'active'),
('00049', 'Pia', 'S', 'Cayetano', '$2y$10$mwu49ioGRyTcYtH./kT6Tu7l6/NNXKQ8NRJ2VMrobXU3ZPu7pJRpm', 'BTLEd', 'Associate Professor IV', 'faculty', 'active'),
('00050', 'Sonny', 'A', 'Angara', '$2y$10$4mqlpyW5N7dnlxzhVG8FOOb1D7ZWXwas3qhA8XSLrnxXIBHt1X9g.', 'BTLEd', 'Professor I', 'faculty', 'active'),
('00051', 'Risa', 'H', 'Hontiveros', '$2y$10$WpKTOx5b1ZJzuyh0diTCbOu78ZHFaLPuU3aK6VfW98hfxtIMFCeNC', 'CA-Animal Science', 'Associate Professor IV', 'faculty', 'active'),
('00052', 'Leila', 'M', 'De Lima', '$2y$10$0VzyMMvBxNLYKVC5h.4eF.P2Jq6Fuq32iIdfCwPI9TcQUffl7fDZO', 'CA-Animal Science', 'Professor IV', 'faculty', 'active'),
('00053', 'Antonio', 'F', 'Trillanes', '$2y$10$8aiWP1AqjlyNZ1sQ6uyiiO/W5lqgN/W740tAyeUpB92pMLP7e5Dnm', 'CA-Animal Science', 'Assistant Professor II', 'faculty', 'active'),
('00054', 'Franklin', 'M', 'Drilon', '$2y$10$7z6CV4LnUzRBI6HeJkJdhOggk/0YrMR/311muJmSwlSsnp6k38JBy', 'CA-Animal Science', 'Professor II', 'faculty', 'active'),
('00055', 'Richard', 'J', 'Gordon', '$2y$10$Bvh9/EpozjeBzAJeQwNK2uSAtqyjKtItlp/KCozodlaL0PBJHHZ0O', 'CA-Animal Science', 'Instructor II', 'faculty', 'active'),
('00056', 'Ralph', 'G', 'Recto', '$2y$10$EoAYU6pdbnxC0.2N5UkYNO2/WmKbbZ7Upd6K5vCI/uVi05eiZYBG6', 'CA-Apiculture', 'Professor I', 'faculty', 'active'),
('00057', 'Vilma', 'S', 'Recto', '$2y$10$YdLFFSdaPr5hNWXTRsUtY.PQ0hT3GHPSxOWCGwaxCPWIOv/5QHBJK', 'CA-Apiculture', 'Instructor II', 'faculty', 'active'),
('00058', 'Loren', 'B', 'Legarda', '$2y$10$VQTdlmDfh.Gr89x3IH3j4.bXJkvFgh/iruaLf82jj70SB18g4HUzO', 'CA-Apiculture', 'Professor IV', 'faculty', 'active'),
('00059', 'Cynthia', 'A', 'Villar', '$2y$10$u5MCnDxqLs4HdnNvQU23reHDnhHDy3ryB/LJtmWmu34HG1Rex0bHy', 'CA-Apiculture', 'Professor I', 'faculty', 'active'),
('00060', 'Manny', 'B', 'Villar', '$2y$10$EoHyxNUMTeD11Ao19eTft./Biy9yyx01hA6AnLGlwzivE5GFq0xmu', 'CA-Apiculture', 'Professor II', 'faculty', 'active'),
('00061', 'Koko', 'A', 'Pimentel', '$2y$10$FFPTqEfbdWaJfK4fc5V2Ue60lF6wEe.UC6wOJwW8GAGG.SKD90wQK', 'CA-Crop Protection', 'Instructor III', 'faculty', 'active'),
('00062', 'Nancy', 'S', 'Binay', '$2y$10$Np/lavPkjFmNCbxv80yLseZdfutWSWSdV2AIhHTl618xPyJlXGtkm', 'CA-Crop Protection', 'Assistant Professor I', 'faculty', 'active'),
('00063', 'Jejomar', 'C', 'Binay', '$2y$10$r5GUvJc7vLXuaJuwSAPwBOug70YbD/yvm/OD2GgtB3wBaMTRssq9S', 'CA-Crop Protection', 'Associate Professor II', 'faculty', 'active'),
('00064', 'Abigail', 'S', 'Binay', '$2y$10$HPulfw9/M4fSkdvgIaytWOltKaxhI0ijb6GELn7mv9X60/3rGm1Za', 'CA-Crop Protection', 'Associate Professor I', 'faculty', 'active'),
('00065', 'Win', 'G', 'Gatchalian', '$2y$10$Bx8G5u3DWJmOJln1bcq34uwOYGwU/Rl.2/L7FndoqZkogv/MGye3S', 'CA-Crop Protection', 'Associate Professor V', 'faculty', 'active'),
('00066', 'Sherwin', 'T', 'Gatchalian', '$2y$10$sRySR23UipRG5DSQO1UKPONg4tAIJYgGHpcL3m55G2VWPFrk7d2lG', 'CA-Crop Science (Agronomy)', 'Instructor II', 'faculty', 'active'),
('00067', 'Joel', 'E', 'Villanueva', '$2y$10$UumuXhaLm4wG8vy/BL9rKOJuJD8LZ10l3.nBMaZsQS5B6zvrgsDVS', 'CA-Crop Science (Agronomy)', 'Associate Professor III', 'faculty', 'active'),
('00068', 'Migz', 'F', 'Zubiri', '$2y$10$9X4qzqOzqvjSMXkilTYqTeXU.ZIpgpiZqGlMQmWUqljY3wemveHoK', 'CA-Crop Science (Agronomy)', 'Associate Professor I', 'faculty', 'active'),
('00069', 'Juan Miguel', 'F', 'Zubiri', '$2y$10$k.GN/bvE2rS5y15Hy.OSCuqj.kQ0IgJtBOv2uD8vQRY19fBFJGoV6', 'CA-Crop Science (Agronomy)', 'Assistant Professor II', 'faculty', 'active'),
('00070', 'Francis', 'N', 'Tolentino', '$2y$10$KIhQbz5tpckxDYy6YAgxwecIsjDJTls0XytNjHArNrCIrjobPFxrm', 'CA-Crop Science (Agronomy)', 'Instructor III', 'faculty', 'active'),
('00071', 'Bong', 'R', 'Go', '$2y$10$.1I5N/ZHhiNWpZU4AbcOieQ83OLfw8MqDLhivrUhIHlgoEBEzQTDu', 'CA-Crop Science (Horticulture)', 'Professor V', 'faculty', 'active'),
('00072', 'Ronald', 'D', 'Dela Rosa', '$2y$10$nl3kSwlEtVfWppWl8IwtY.0YLXQtHa/oY9Xc/3ElV/7IN69DoUy2O', 'CA-Crop Science (Horticulture)', 'Professor III', 'faculty', 'active'),
('00073', 'Imee', 'R', 'Marcos', '$2y$10$PtJ6yMwzPngPtOQ/TA52qumLTIt.fDaEmM3vsSRvA8aigNqLDVGRW', 'CA-Crop Science (Horticulture)', 'Associate Professor I', 'faculty', 'active'),
('00074', 'Robin', 'C', 'Padilla', '$2y$10$d78xZyv/Mgu9DA.hqlo3xeQ3YdKqPom0VflzyYbfHcHY5yHmMeJpK', 'CA-Crop Science (Horticulture)', 'Professor III', 'faculty', 'active'),
('00075', 'Raffy', 'T', 'Tulfo', '$2y$10$km63kcLiA2VSAtgK6C7k4.v3PkuhbvrTkTwuMGrLceIPlzRHRPExK', 'CA-Crop Science (Horticulture)', 'Associate Professor I', 'faculty', 'active'),
('00076', 'Mark', 'A', 'Villar', '$2y$10$4o1XDNYbuqbYfuye1Nc4PeRio1g1m4YbQEQm7GSTnBATeK6V.0fnm', 'CA-Soil Science', 'Professor IV', 'faculty', 'active'),
('00077', 'JV', 'G', 'Ejercito', '$2y$10$K8qntJMMhHtEoNjmSEFEouO3HhSVb9iNxoWsSHxwJkMRopIDLBxle', 'CA-Soil Science', 'Instructor II', 'faculty', 'active'),
('00078', 'Jinggoy', 'E', 'Estrada', '$2y$10$esDC6jRPMkmDRXWJgsLRXOXySjEznwTz7Q2h2DLcyzmVJ6mIkIqkS', 'CA-Soil Science', 'Associate Professor III', 'faculty', 'active'),
('00079', 'Chiz', 'G', 'Escudero', '$2y$10$x1IDPOuJ/BDG/MP4PC5NquS.rYJgVZudVbS69ffVCLTeEKIIXCrg2', 'CA-Soil Science', 'Professor VI', 'faculty', 'active'),
('00080', 'Heart', 'E', 'Escudero', '$2y$10$rGfflDpxB5PSKGBCnc3XaOhpex6mJ4Bb18mqUVH.238w.rWlMdGSe', 'CA-Soil Science', 'Professor I', 'faculty', 'active'),
('00081', 'Lito', 'M', 'Lapid', '$2y$10$H24bFU0ihdLNrGIvY.lbDuBSQAw8Dbpef9DE9j.LZY8fzzktm2RqG', 'CAFF-Agroforestry', 'Instructor III', 'faculty', 'active'),
('00082', 'Ed', 'J', 'Angara', '$2y$10$DH4rbusauAENhSY8tVMU5ez2px/xZhWONWCgKA6xCErpdwDbWZr7a', 'CAFF-Agroforestry', 'Assistant Professor II', 'faculty', 'active'),
('00083', 'Juan', 'C', 'Angara', '$2y$10$uL4g.6LOPxsupgGOgqrwb.u0HlEuomR3H4tGDR.UDyYnIl/inszpW', 'CAFF-Agroforestry', 'Associate Professor IV', 'faculty', 'active'),
('00084', 'Miriam', 'D', 'Santiago', '$2y$10$CScE4HgMY1EYcbdh9Tu2BO09kJnqUIkQHC/zPKHyNUTBdgYGTX9wq', 'CAFF-Agroforestry', 'Professor IV', 'faculty', 'active'),
('00085', 'Rene', 'A', 'Saguisag', '$2y$10$priJJxpgHSXdHJT0cn1O4.9VJUiPX6CIguy1wwoZtvKyFvsqEpGo2', 'CAFF-Agroforestry', 'Assistant Professor IV', 'faculty', 'active'),
('00086', 'Joker', 'P', 'Arroyo', '$2y$10$Uw/.RtrgnpFS/zOCAeNNQ.bmUg5bw86J4xz/Ga8nFb2Z1L8cJZbZ6', 'CAFF-Forestry', 'Associate Professor I', 'faculty', 'active'),
('00087', 'Noli', 'L', 'De Castro', '$2y$10$cDlMpZgeDiKG0uUdJUBt9OPG0Tx6zigQYz/HHLH.IV7K0vKQisl3K', 'CAFF-Forestry', 'Professor III', 'faculty', 'active'),
('00088', 'Mar', 'A', 'Roxas', '$2y$10$0FTsasBUi05kG6/Jt1o19.cpTYW7QbY2O0ymYlL3EHp/hIfNdtP3y', 'CAFF-Forestry', 'Assistant Professor III', 'faculty', 'active'),
('00089', 'Korina', 'S', 'Roxas', '$2y$10$6xooAl4C9TOJ8ilFXbWMqODcwWApoHzbUH9H0JBDVu9vCeT/OgG2i', 'CAFF-Forestry', 'Associate Professor I', 'faculty', 'active'),
('00090', 'Teofisto', 'G', 'Guingona', '$2y$10$.BDYy57yVBzcUkHJAW9SkeibQl//C/2bgXvzjy667ygNhP4r0wtxW', 'CAFF-Forestry', 'Professor V', 'faculty', 'active'),
('00091', 'Ernesto', 'M', 'Maceda', '$2y$10$v4w6FVESSrYckl9bEr2UCu9W2RpJbXJDObNsd0FCHFke5zR9TbRk6', 'CAS-Arts in English Language', 'Professor IV', 'faculty', 'active'),
('00092', 'Blas', 'F', 'Ople', '$2y$10$1PPSDXed794vIwpGytE0B.VfrmV0Kzy.NZY8g.oFcs/Q9KE8D4p/y', 'CAS-Arts in English Language', 'Associate Professor V', 'faculty', 'active'),
('00093', 'Raul', 'S', 'Roco', '$2y$10$VlkjVv59ptIfOLlzxIKAXuz3n1Otdp0OyDFuCKBq98WdkHx1VbA92', 'CAS-Arts in English Language', 'Professor II', 'faculty', 'active'),
('00094', 'Heherson', 'T', 'Alvarez', '$2y$10$EBzxBEOar2AJ7BbMFd7cHe.velfouNQU9rwRn0uBs/BeKHcwhNRHq', 'CAS-Arts in English Language', 'Associate Professor II', 'faculty', 'active'),
('00095', 'Juan', 'P', 'Enrile', '$2y$10$0ZlIZuxmYsksnNLQX6BkLO06.JMr5usjQ4vzz0G2MD3iWKzhvgHI.', 'CAS-Arts in English Language', 'Associate Professor III', 'faculty', 'active'),
('00096', 'Gringo', 'B', 'Honasan', '$2y$10$CEsucAik1HDNRA8TQIMgHuG8v8AibjTa3hiVCCLxb1PUiMyMbxNf.', 'CAS-Science in Biology', 'Professor IV', 'faculty', 'active'),
('00097', 'Orly', 'S', 'Mercado', '$2y$10$DxxY9CEDg3q1ukRbvk7NK.XFkFcEZfLPL4GWPv3sANOi6TvtQNLNK', 'CAS-Science in Biology', 'Assistant Professor III', 'faculty', 'active'),
('00098', 'Rodolfo', 'G', 'Biazon', '$2y$10$6GAzr0ybF9Eq57q2lRm4GOhewiBBUlNOBZzlufyJWM4u4JTxOdJvi', 'CAS-Science in Biology', 'Associate Professor II', 'faculty', 'active'),
('00099', 'Alfredo', 'S', 'Lim', '$2y$10$Aqu4KSHNegJ/GGrSlfsRSOLCkFv58PzNVJWOgBs1xOwNQjQdwKToe', 'CAS-Science in Biology', 'Professor V', 'faculty', 'active'),
('001-0000-0', 'Rufo', 'A', 'Baro', '$2y$10$hcj7EUueIuEzItBePg6wcOslDdEsUo8zU.L13vswVBBbY56eTX5ti', 'CIS', 'Professor I', 'faculty', 'active'),
('00100', 'Robert', 'S', 'Jaworski', '$2y$10$PqWyuhmQDW0YGVuleXD9XONeOqPElg5OY3v5UEqUI0x9No7BNsXEG', 'CAS-Science in Biology', 'Professor I', 'faculty', 'active'),
('00101', 'Freddie', 'N', 'Webb', '$2y$10$PnC97D/PsKWuRB1igERhcuGz.w79ZTYq3v53Q5J8KOejyh/7Y9MiC', 'CIS', 'Professor I', 'faculty', 'active'),
('00102', 'Tito', 'V', 'Sotto', '$2y$10$3S816aqesBApv7E0Jfka5u3NMF7kyyAUJ3PffHDtoNIwolbfGoUue', 'CIS', 'Associate Professor IV', 'faculty', 'active'),
('00103', 'Vic', 'M', 'Sotto', '$2y$10$hD2vxQsozLvBbm0fTkxozOaCkxOjZ2OabBwb90aGkqUyoACFFaaO6', 'CIS', 'Assistant Professor I', 'faculty', 'active'),
('00104', 'Joey', 'M', 'De Leon', '$2y$10$xcWXs2tqtmd9qVrxL580ge6GXqv3giCRuKue1iDMYSgRIHY.HXOJy', 'CIS', 'Professor V', 'faculty', 'active'),
('00105', 'Francis', 'M', 'Magalona', '$2y$10$jvRG0plmn/HtpdqJl.BEGu3Pajc2XNjUpAADzUeHVp2UZcAcmDUzO', 'CIS', 'Instructor I', 'faculty', 'active'),
('00106', 'Ely', 'E', 'Buendia', '$2y$10$l4Ya.xBvYxfJ9HxoqVAuEeewi.Msa0YlxVh.wrbYAxvSwa2xHWePG', 'CVM', 'Professor V', 'faculty', 'active'),
('00107', 'Bamboo', 'M', 'Manalac', '$2y$10$KbrV3cFWSUYyMkL4maQ3TuG4FjFzeeep71uf4aRVy.NaXiBHQFOt6', 'CVM', 'Assistant Professor I', 'faculty', 'active'),
('00108', 'Sarah', 'G', 'Geronimo', '$2y$10$L8D5dKT2KM2OcRHzxvgEEO16ApgkcjmxrM1/LUGP7TwD7NxUMhp9.', 'CVM', 'Associate Professor II', 'faculty', 'active'),
('00109', 'Lea', 'S', 'Salonga', '$2y$10$PSmWAo.WxLUNiZ28YrJMLOdZEdEYv.eaBFOte2L0P1umKT.6z3Fd2', 'CVM', 'Instructor I', 'faculty', 'active'),
('00110', 'Gary', 'V', 'Valenciano', '$2y$10$0kB342Pi73m98PU6Yvtmzu63J51wPzg6hILwc0134RV8csPRtec6C', 'CVM', 'Instructor II', 'faculty', 'active'),
('00111', 'Martin', 'M', 'Nievera', '$2y$10$UI8SUvcTHOVjGasnn5WIieK3L2FZ9Hm2cwobRkZWq2jH.uP4JyIPC', 'IABE', 'Associate Professor III', 'faculty', 'active'),
('00112', 'Regine', 'V', 'Alcasid', '$2y$10$hrPbDaGH2nSksjxIRWZE2OM2HeaKpaCAbhauBYmpwJpHFg5.im1lK', 'IABE', 'Assistant Professor IV', 'faculty', 'active'),
('00113', 'Ogie', 'A', 'Alcasid', '$2y$10$F7ttTpBbZ1q0RVJkkb8BKeZ/301NBkPfD5EDQA36R5bVA/B60m5M6', 'IABE', 'Professor III', 'faculty', 'active'),
('00114', 'Sharon', 'C', 'Cuneta', '$2y$10$mEaaxB1JwCNSnPb7WSuX9uzoVdSEracBsA07w0AOMez4gkjg5qXR.', 'IABE', 'Associate Professor V', 'faculty', 'active'),
('00115', 'Zsa Zsa', 'P', 'Padilla', '$2y$10$I3gpWyqXS8YZxFZ6ba91IutvPvNdRcPKdvxIsjmnXAOCfVcwkTlQa', 'IABE', 'Professor III', 'faculty', 'active'),
('00116', 'Karylle', 'T', 'Yuzon', '$2y$10$UpOkZgCHKHO8XF.4o3N2sOKpzOlpjQkP.SdnyzwV74WdaZPa5ANKi', 'IES', 'Professor I', 'faculty', 'active'),
('00117', 'Vhong', 'F', 'Navarro', '$2y$10$d1A8OBcsoT4bVheNqUg9MuYAGyjOc6YjNP65c4xAwJdh8Yl38DBUK', 'IES', 'Assistant Professor III', 'faculty', 'active'),
('00118', 'Anne', 'C', 'Smith', '$2y$10$A.iPuxmxdzSLYFXrQwFlGuBdSzwXauFLpj8wshjMvG2pbuZ9j49WS', 'IES', 'Associate Professor V', 'faculty', 'active'),
('00119', 'Vice', 'G', 'Ganda', '$2y$10$5BdOKeddJWB03y2N23LL6erXhRko8YtclBKxkn89HDxr8IorWWbN.', 'IES', 'Professor VI', 'faculty', 'active'),
('00120', 'Billy', 'J', 'Crawford', '$2y$10$M2FP0s1E91fJD9Wv3uJMo.FspjgQ4DVaXBdnpgEdtBh2AK7Ey9lMC', 'IES', 'Associate Professor III', 'faculty', 'active'),
('100-0000-0', 'Edelita', 'C', 'Ebuenga', '$2y$10$8PARyuQRwico6m27obo2CudnzYV20ZcxfNjNcnocITSWU8eGZkiXy', 'CIS', 'Professor II', 'faculty', 'active'),
('221-0387-1', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$0MVzJneKo66YW/H.pvOIROKSb.42/xD4OR2lytbaCGkNdKBNUkYxK', 'CIS', 'Professor IV', 'faculty', 'active');

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
  `department` varchar(255) NOT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `section`, `role`) VALUES
('000-0000-1', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$bXnX3ipyivDYwhcIV9tlieAfxDWFTRBt2a3/gA6yxVHpIbvaDpvza', 'CIS', '4-B', 'student'),
('221-0388-1', 'Charls Adonis', 'V', 'Rojas', '$2y$10$IFvW9FUT7w/YOEmTMQoE4ud3zMMi/Vqf9uMMR0GQS56yJvGE1BhBy', 'CIS', '4-B', 'student'),
('241-0001-1', 'Aaron', 'M', 'Santos', '$2y$10$rAYtzBiKBAreScrd4geBi.x5SRcf378WS2KHUYGb.PZfCICYZEdqO', 'ABM-Cooperative', '3-D', 'student'),
('241-0002-1', 'Abigail', 'R', 'Reyes', '$2y$10$ojvVrZua19aIS.61WcPI7u59yHEpe355Zaf73HE3l6ZH023HB7DcC', 'ABM-Cooperative', '3-C', 'student'),
('241-0003-1', 'Adrian', 'C', 'Cruz', '$2y$10$iuGggCHtBT.dvTDkXpaGneJNmz9oCj7r2RSPjfb9NT.QnSs5l5a.q', 'ABM-Cooperative', '2-B', 'student'),
('241-0004-1', 'Alexa', 'G', 'Garcia', '$2y$10$pCr/BvnE/KJeJX5nZQOQV.0vxlxrWdyvm2rvuZPfQGo8ATt2T8jfu', 'ABM-Cooperative', '3-C', 'student'),
('241-0005-1', 'Alfred', 'T', 'Tan', '$2y$10$zIx5cIBVXKvzmdSchWcO5enXtMp2.rODxnhQjpv2OlpQzsZFC5oX2', 'ABM-Cooperative', '1-C', 'student'),
('241-0006-1', 'Alice', 'P', 'Perez', '$2y$10$LHnkRrD52rwhHKg96ZvyeuQYKnqyfk.Xkbk4H.gc4Kr0Sj/jd0zDK', 'ABM-Cooperative', '4-C', 'student'),
('241-0007-1', 'Alvin', 'L', 'Lim', '$2y$10$WHpfzxr6RISycam5curp3OwL8pn4A8DFJe0ALr6CPXTCTBOjvZS/2', 'ABM-Cooperative', '1-C', 'student'),
('241-0008-1', 'Amanda', 'D', 'David', '$2y$10$OOl7LlQp2c0lItl1vVHJXOTGjwAm1PZP9Bs.dIpyN6EvVN92kNQxu', 'ABM-Cooperative', '4-B', 'student'),
('241-0009-1', 'Andrew', 'O', 'Ocampo', '$2y$10$yF9UOj/T4paPa7hGN4fO1O70q14TuioRt.M3J1Xi8k/zngoUPMTQO', 'ABM-Cooperative', '4-C', 'student'),
('241-0010-1', 'Angel', 'M', 'Mendoza', '$2y$10$HlGNaKi7ufOIVvWI3/2x7.EUY/5yhiMVs2BJu0XOlwfCpZPSuwoMO', 'ABM-Cooperative', '4-D', 'student'),
('241-0011-1', 'Arthur', 'F', 'Flores', '$2y$10$M6zJBzuPakMfnnNRCUsFO.CFHAT30xTVsN0Mn295ZpG8F8HSemvYq', 'ABM-Entreprenuership', '1-B', 'student'),
('241-0012-1', 'Audrey', 'G', 'Gonzales', '$2y$10$N79FSUBUuibv1En1aibsWeybkFCahFrGWMyjpJctlEx8ddXM5EHee', 'ABM-Entreprenuership', '2-D', 'student'),
('241-0013-1', 'Austin', 'L', 'Lopez', '$2y$10$KR0tW0i/N4wz/zQRiiFCtOZ4IJV6eBgkavq5DOUcPrufzy0UxYMsq', 'ABM-Entreprenuership', '1-A', 'student'),
('241-0014-1', 'Ava', 'R', 'Rodriguez', '$2y$10$3Wkmih3gzgwc5yRKowmwyOQuvQjXy1brzTQzP4vpk25Rn/fQlRdPm', 'ABM-Entreprenuership', '4-B', 'student'),
('241-0015-1', 'Barry', 'A', 'Aquino', '$2y$10$j2gFinlkUMQ82f3fo.tUq.i2spdUrtfgIIBfZxXe3EPJBx95h3iG.', 'ABM-Entreprenuership', '1-D', 'student'),
('241-0016-1', 'Bella', 'T', 'Torres', '$2y$10$2a.VduqTJg53VNH6pL1vQej5uwlWgnL.vV895.5NhRMPTZuFwkuSG', 'ABM-Entreprenuership', '3-A', 'student'),
('241-0017-1', 'Benjamin', 'S', 'Santos', '$2y$10$E9ao0m0IsV65MZF7nyvBK.sP7yvfDzuQa2mnYergfHBCdLsexOZ8K', 'ABM-Entreprenuership', '4-B', 'student'),
('241-0018-1', 'Brianna', 'R', 'Reyes', '$2y$10$OHTeT3Sy4zW5agOrCOS.ZuhOtTdXfS6//bl4kCy6JqvpcLZcNexXe', 'ABM-Entreprenuership', '4-A', 'student'),
('241-0019-1', 'Bruce', 'C', 'Cruz', '$2y$10$KT2vLItckrqI94IjbwvXleOVWwaElOr1HsQU0FDAq9DHidmYK2fyq', 'ABM-Entreprenuership', '1-D', 'student'),
('241-0020-1', 'Brooklyn', 'G', 'Garcia', '$2y$10$FgLqVOS.5EZoq4aKX.Udb.1vxuar4Q1MslTjNNfyXAKOneTEAAriq', 'ABM-Entreprenuership', '4-B', 'student'),
('241-0021-1', 'Caleb', 'T', 'Tan', '$2y$10$5fGiR6aGWHBoZpSuB1dnAuo4qxHVAcPBHYAS5R/j7mCE53wTl1M6m', 'BECEd', '2-D', 'student'),
('241-0022-1', 'Camila', 'P', 'Perez', '$2y$10$GyU3TUvCOk2tMWvmNEAFvugIGuC.DEqneI2gxOf6rHHJdqjCKjeSe', 'BECEd', '4-A', 'student'),
('241-0023-1', 'Carl', 'L', 'Lim', '$2y$10$m4YkKdIlKMxYfdQyaxKO/OFX.ydqI.sU2SLpEZOsejNPoZMiR9dx2', 'BECEd', '3-A', 'student'),
('241-0024-1', 'Caroline', 'D', 'David', '$2y$10$D138AAGRjj1I89ePO.bzRuw84m4q4D0e9q/P8U4sW6eDf93dRfIdG', 'BECEd', '1-D', 'student'),
('241-0025-1', 'Charles', 'O', 'Ocampo', '$2y$10$d26/ztc87yvumj2/V7LHmusYHVeJx.FOXkSpl/bUSb0IjVk2RyFem', 'BECEd', '3-C', 'student'),
('241-0026-1', 'Chloe', 'M', 'Mendoza', '$2y$10$8a0vaCuKW9XDCMAaeiLjKezJCPTkz1n.wx0DgyzuZQZHXG0xVx7aO', 'BECEd', '3-B', 'student'),
('241-0027-1', 'Christian', 'F', 'Flores', '$2y$10$XO4FH9uZxVBp938ItJk2u.MWepz/o2a5V0aCg60z0T8T/jsLmbA8.', 'BECEd', '4-D', 'student'),
('241-0028-1', 'Claire', 'G', 'Gonzales', '$2y$10$xBReV/JdSpwe7uKVhgfk4OBgjTfR2Q9hEXs8DL52cwQjbrw0Zncum', 'BECEd', '1-A', 'student'),
('241-0029-1', 'Christopher', 'L', 'Lopez', '$2y$10$XIv5Pkpku79akZEqNQA5fOBoOtPd4lQu3QofCwZaXCiU9tdcQi0uS', 'BECEd', '2-C', 'student'),
('241-0030-1', 'Clara', 'R', 'Rodriguez', '$2y$10$EBND7.au7iCyS58aX2.mX.e1jgXsKDsQFMM/88vp33sJQrTbynfva', 'BECEd', '4-B', 'student'),
('241-0031-1', 'Cole', 'A', 'Aquino', '$2y$10$QkCfxJv2n8Ovjo1A1u.Za.EmjlFLaM167TDpLFlamlGPuDpd/euxO', 'BEEd', '1-B', 'student'),
('241-0032-1', 'Cora', 'T', 'Torres', '$2y$10$Ikn32zTwq.5Vsj.EzydQYusfi/xbFFjNeJId6o2XNJBimQeAhzhtO', 'BEEd', '4-C', 'student'),
('241-0033-1', 'Connor', 'S', 'Santos', '$2y$10$cwb85kcFcYSHHqL3Kayx0.1Q8uPoOtyN5kESaXflVFF1vRExtznBi', 'BEEd', '1-C', 'student'),
('241-0034-1', 'Daisy', 'R', 'Reyes', '$2y$10$62AJLJlqd.6QAsl.tgXL2O/.UU8iNp6tc169URN43vORaJBen0jk2', 'BEEd', '1-B', 'student'),
('241-0035-1', 'Damian', 'C', 'Cruz', '$2y$10$cPkHJdkFt6cg0Uh/uwh92uyF61KSTFfizkj8pCI622VW2Z/Co1KRe', 'BEEd', '1-A', 'student'),
('241-0036-1', 'Daniela', 'G', 'Garcia', '$2y$10$SS49p2AVdlI2r6bljltzI.vHTuMhvupRbZpvMU0sjq965FNHluwS2', 'BEEd', '4-C', 'student'),
('241-0037-1', 'David', 'T', 'Tan', '$2y$10$UmcQLI3eeAl24ntVPtKL.O5VZdDht/vx7qz5gVVCblPFuR9d50RbK', 'BEEd', '3-A', 'student'),
('241-0038-1', 'Delilah', 'P', 'Perez', '$2y$10$NMG1nZFO2bdzkTlABjJq2OjDEYtD9.jg0ckkdSxMo8my5iA3W7HNG', 'BEEd', '4-A', 'student'),
('241-0039-1', 'Declan', 'L', 'Lim', '$2y$10$LdJfgDRZ.BFYQI0oM1t6duvQC6zAdAnl.xFLTWZAJZbwxq2KKS7b.', 'BEEd', '2-C', 'student'),
('241-0040-1', 'Diana', 'D', 'David', '$2y$10$ismN8bEseOrqprgfJOh0I.64kfZkcD8h9gGlLAjfyHpmRYVodskIO', 'BEEd', '3-C', 'student'),
('241-0041-1', 'Diego', 'O', 'Ocampo', '$2y$10$3iURxGOy36PILgJJN8oDtO3Z1cDJHP2b5AWvrHm8uXoZAdofIe0Za', 'BPEd', '1-B', 'student'),
('241-0042-1', 'Elena', 'M', 'Mendoza', '$2y$10$F9dzGXDWcOs7AMTS6dINZePVsbIaOTcizerZ1ju0sHXiIWUtj8UQm', 'BPEd', '3-A', 'student'),
('241-0043-1', 'Dominic', 'F', 'Flores', '$2y$10$7p3kSasc8SIgotTDF3Ymjun1AYYW9Bu7BiQPyZOCIqqT/fB5aaale', 'BPEd', '2-C', 'student'),
('241-0044-1', 'Eliza', 'G', 'Gonzales', '$2y$10$OvR8c67cvn93YCYlV.ZVn.Aq5hxYZ.P/c62SwgL5JAMLnE49f5bkK', 'BPEd', '2-B', 'student'),
('241-0045-1', 'Dylan', 'L', 'Lopez', '$2y$10$IFnquGQiUohDQoXdjXZdzuzUmfa1hJNkg05EpNGRqvye1flPjKYUC', 'BPEd', '3-B', 'student'),
('241-0046-1', 'Ellie', 'R', 'Rodriguez', '$2y$10$QJ4nrs6jQ2N576rc0Fn1uODzccvPcVpagpMS8fH2hosfEaLRB.uUC', 'BPEd', '4-B', 'student'),
('241-0047-1', 'Edward', 'A', 'Aquino', '$2y$10$Zb4hu.jMmmDQD3.TLGJrcut0k72cj5Era8fG2wXYqXMwKlUN4t0w6', 'BPEd', '2-D', 'student'),
('241-0048-1', 'Emilia', 'T', 'Torres', '$2y$10$DAt.jc9UksU5HOkWE7fOp.VM9qaqLbEcY82PMWxXGn9X3GckFTapC', 'BPEd', '4-B', 'student'),
('241-0049-1', 'Elias', 'S', 'Santos', '$2y$10$uKupbrgRstVtEIE/ljvWWOFwauowlEKFS.X1zWgA5kp7flDvhXocS', 'BPEd', '4-C', 'student'),
('241-0050-1', 'Emily', 'R', 'Reyes', '$2y$10$g4.7bk5OWMq7USVwO.1IlObqJjM3eo/5dnRF9VmsjP.DGWeB8ICYy', 'BPEd', '4-C', 'student'),
('241-0051-1', 'Emmanuel', 'C', 'Cruz', '$2y$10$AICoHnHNzzvpnnaM8k8gA.2KpI9wozOvk7IskIhnPvJ8iQOfzLAUG', 'BSEd-English', '4-B', 'student'),
('241-0052-1', 'Eva', 'G', 'Garcia', '$2y$10$wpIVGmtsEJH4f2PxkVESc.J7D8ZmFKr4Yon554UT05iJpVQxj0dEm', 'BSEd-English', '3-A', 'student'),
('241-0053-1', 'Enzo', 'T', 'Tan', '$2y$10$juCOOBPV7fyb4OB1eRqIs.wAp0O4/IjXZEXBbnGvHcTQxqG8nTBhW', 'BSEd-English', '1-B', 'student'),
('241-0054-1', 'Evelyn', 'P', 'Perez', '$2y$10$8by59vH2FiJ9lq1pS3lUDeZk0G5Ymb/ki3w28GIhTrSf8rlu1LAfS', 'BSEd-English', '4-A', 'student'),
('241-0055-1', 'Eric', 'L', 'Lim', '$2y$10$yjEeQP92HnhQCRdhKECZp.xfy69/ifOM.cXp5xaGqGZHf1Rj/EmKu', 'BSEd-English', '3-D', 'student'),
('241-0056-1', 'Ezra', 'D', 'David', '$2y$10$CPsNYKwOWaQCnEjBqzn.xec8plEnIjHwWopZCtldEPlBz1UteY9SS', 'BSEd-English', '1-C', 'student'),
('241-0057-1', 'Ezekiel', 'O', 'Ocampo', '$2y$10$icKkkckc64BcW86M.nNyi.M5mQx9qw3ZXTIL5UFqck8VJh01mfp8q', 'BSEd-English', '3-B', 'student'),
('241-0058-1', 'Faith', 'M', 'Mendoza', '$2y$10$tU0IJsmAhZfdX9p9/i4r0eQenpEPNjZZfbtZdgy9mf.i.0iB/PIuC', 'BSEd-English', '2-D', 'student'),
('241-0059-1', 'Felix', 'F', 'Flores', '$2y$10$FKEK1S2yNXLy.EuWyUCl6OUT4c4bPBTfMN4Kuhk7WGMQUIxmwHryy', 'BSEd-English', '3-C', 'student'),
('241-0060-1', 'Fatima', 'G', 'Gonzales', '$2y$10$RWI7z4lDadXCxFPq0rMA5.p/M9wmXluCQvnEgK2kmMYJq/4wKIDtq', 'BSEd-English', '4-B', 'student'),
('241-0061-1', 'Finn', 'L', 'Lopez', '$2y$10$ASEIK/AwVB5h8oVvbfslXeFoHZH/1e.Hv6i97IrlJHeQVvB804qQu', 'BSEd-Filipino', '2-A', 'student'),
('241-0062-1', 'Faye', 'R', 'Rodriguez', '$2y$10$gEvzaN7YyoJswgqcmPGQZufMfdTWvq/bwMdgF5ABOIt/RPUUYjeU2', 'BSEd-Filipino', '4-B', 'student'),
('241-0063-1', 'Frank', 'A', 'Aquino', '$2y$10$l42zozVhkN9IExMwMgtG3e4Pyg4zw2GjdaW9j85YI8XTV76XKN4Om', 'BSEd-Filipino', '2-C', 'student'),
('241-0064-1', 'Freya', 'T', 'Torres', '$2y$10$dbfVYTTJQGtbR5URKXgMaOb0gKoBI.YUIQOLk8dSTNPqvHCcyRs5O', 'BSEd-Filipino', '2-D', 'student'),
('241-0065-1', 'Gabriel', 'S', 'Santos', '$2y$10$wWQDdNvVyz40tvoYnqGE2Oi2z2IA6FPfUz0HJYWuWQ2y6YAkjB6ym', 'BSEd-Filipino', '1-B', 'student'),
('241-0066-1', 'Gabriela', 'R', 'Reyes', '$2y$10$rPGr8IYtm5GN7xrm.23MU.SBK5T4nHNn7GaYQCGt7.nPX.RY/F5Ty', 'BSEd-Filipino', '4-D', 'student'),
('241-0067-1', 'Gael', 'C', 'Cruz', '$2y$10$.MwDDsMyhmH14HcMMbmzP.8D6Vzjp7fWSVkcFvIYeCMdb2DNk6cRO', 'BSEd-Filipino', '3-D', 'student'),
('241-0068-1', 'Gemma', 'G', 'Garcia', '$2y$10$7cW9Lsl1C3BIbT62QegKd.AOlbFnFyk4t4crwXaBFPq/oZbgs7rkK', 'BSEd-Filipino', '3-D', 'student'),
('241-0069-1', 'Gavin', 'T', 'Tan', '$2y$10$0QDv7rrh7aqGe6FA43Exb.63DyLnG10NSHmpwUKM1EAyC3JdZzzPW', 'BSEd-Filipino', '2-D', 'student'),
('241-0070-1', 'Genesis', 'P', 'Perez', '$2y$10$mfPj95sEXqqF7ajidZ3mjuvInpzKgWqA11NI8RO/dBhNvfOAgrYK.', 'BSEd-Filipino', '1-B', 'student'),
('241-0071-1', 'George', 'L', 'Lim', '$2y$10$M18qmCm2iuleXooeQz6FbO.CnasX0U3JwK.kAJeTt4UoSJq7F5/5G', 'BSEd-Mathematics', '1-B', 'student'),
('241-0072-1', 'Georgia', 'D', 'David', '$2y$10$iGBjGiHuLByPWlC3vwXY/Oqu7ksNq0ftu3rvcKOV39RpC5faa1P7m', 'BSEd-Mathematics', '2-A', 'student'),
('241-0073-1', 'Gerald', 'O', 'Ocampo', '$2y$10$3vrf8bm.t.Fl7TWK.kjhG.eEB6cuxcCMaDMq2iR1EFQWC6tE8/I/G', 'BSEd-Mathematics', '4-D', 'student'),
('241-0074-1', 'Gianna', 'M', 'Mendoza', '$2y$10$EDaTpzO751oBmdk18evezuZFWJhghy25zFzqTDG59Qlzvs3nOilUK', 'BSEd-Mathematics', '1-C', 'student'),
('241-0075-1', 'Gideon', 'F', 'Flores', '$2y$10$M9cqsfKQwOcGlT/v8cq5DeO.8NgvCn0ya0rg6CzzTJJFxeVj5wqIu', 'BSEd-Mathematics', '3-D', 'student'),
('241-0076-1', 'Giselle', 'G', 'Gonzales', '$2y$10$QtjxqBzudiN5AZ0HsnrCKeEx4MBjXuebkDV9viAqGmwjW.GR6Si3K', 'BSEd-Mathematics', '1-B', 'student'),
('241-0077-1', 'Graham', 'L', 'Lopez', '$2y$10$JXQbh9PD42eF4BfHKmZXYeMNfymcC64sxdE95qTBkJFdU0penFjHO', 'BSEd-Mathematics', '2-D', 'student'),
('241-0078-1', 'Gloria', 'R', 'Rodriguez', '$2y$10$2yyqD8qpz0Wy4kqVu0tmDuIJCYz700JV09yboxhrRW6wTQYB1Y0wq', 'BSEd-Mathematics', '1-B', 'student'),
('241-0079-1', 'Grant', 'A', 'Aquino', '$2y$10$9EA3liK/mc.AWde1JVoJhed0kmDuHmjotZucfQQFIa6xr9ySDiF6e', 'BSEd-Mathematics', '4-C', 'student'),
('241-0080-1', 'Grace', 'T', 'Torres', '$2y$10$V7T0iR5nHe/yHIn/mSZ49.c4KBTT/Tdv49nWzwCfpORApCkegeUiK', 'BSEd-Mathematics', '2-A', 'student'),
('241-0081-1', 'Grayson', 'S', 'Santos', '$2y$10$TYiYNEEVyhHqgNQ27OSI4uVZ7HFXoOpIP5nzBn4UdnfyaMCBux6KC', 'BSEd-Science', '4-A', 'student'),
('241-0082-1', 'Gwen', 'R', 'Reyes', '$2y$10$22vjlS1AaI4/JDU3.JfaOuhtSlM84Vv35aS1YyuFoAtNiyQ82Fa.6', 'BSEd-Science', '1-C', 'student'),
('241-0083-1', 'Gregory', 'C', 'Cruz', '$2y$10$bvLBSoSwuxWkm94U4ATN3e6kXA3c2N5y2xtobuQa4YcdWGLS4Dyp.', 'BSEd-Science', '1-D', 'student'),
('241-0084-1', 'Hailey', 'G', 'Garcia', '$2y$10$ua6ShBQ6De92U8WqbM60cuT7O4yzLGBG0OTr4/HWJjrkD7Ma7X7Ni', 'BSEd-Science', '3-C', 'student'),
('241-0085-1', 'Hank', 'T', 'Tan', '$2y$10$Au.v293CKB0o0ugtGe05UeXvw8e56TMFY/tBSywEikJ0Jfh.E0pnK', 'BSEd-Science', '3-D', 'student'),
('241-0086-1', 'Hannah', 'P', 'Perez', '$2y$10$vnRdBcP4XoUSYcLmY34QSORQla60qyhRW4wec86DHIJGwv/y0F/Mi', 'BSEd-Science', '3-B', 'student'),
('241-0087-1', 'Harold', 'L', 'Lim', '$2y$10$KrhsO0owmInorPt76t/xe.KqIDey70kzF.f8lnxdWl1IMISc1qZc6', 'BSEd-Science', '4-B', 'student'),
('241-0088-1', 'Harmony', 'D', 'David', '$2y$10$XoM3l2ZqZ.0ezA3TLFCZu.iSVpWDA61kGH3JZ0i2RfbYhDKFqXyXO', 'BSEd-Science', '2-C', 'student'),
('241-0089-1', 'Harrison', 'O', 'Ocampo', '$2y$10$P6NMTolEIWKoQAxuEHPXYOrde1ODISD5qJeWemi3HZCv8HKrupAPC', 'BSEd-Science', '3-A', 'student'),
('241-0090-1', 'Harper', 'M', 'Mendoza', '$2y$10$GWAIrkT3PK/dH.2t7epVpOdx8Isd6mDFzk3qqYlFMncghldKxA5PC', 'BSEd-Science', '2-D', 'student'),
('241-0091-1', 'Harry', 'F', 'Flores', '$2y$10$UkjFOurI7C3QwwSvsHS9s.daABC/k4uQzD4bpk9AmUD1FEC7wBzKi', 'BTLEd', '4-A', 'student'),
('241-0092-1', 'Hazel', 'G', 'Gonzales', '$2y$10$U4yxpq7CQwTMOouo501JKuYQKCm9oNJHTIC3UisPiV5.r7wRNOFBe', 'BTLEd', '3-B', 'student'),
('241-0093-1', 'Harvey', 'L', 'Lopez', '$2y$10$vWb5TzERIggcH5O8AUcIAOK75molBBkswerCfOu0i0NQ9Nyeg/eSq', 'BTLEd', '2-D', 'student'),
('241-0094-1', 'Heather', 'R', 'Rodriguez', '$2y$10$lymUwKJ6xcbmDocJhhbMQOvXhsbM/KnSiWMl8tkDoPFAiFeAd4BuK', 'BTLEd', '2-D', 'student'),
('241-0095-1', 'Hayden', 'A', 'Aquino', '$2y$10$t4M6FWRA8QWr7HlNT2l/qODffg6XFQhA9OYeJYftUO2FNeSuW6L7q', 'BTLEd', '1-C', 'student'),
('241-0096-1', 'Heidi', 'T', 'Torres', '$2y$10$dT8ixe6wEhEu.I7.dDYcVeMV7cXOrfbQQGRzo4xGoBNIz53xhS7q6', 'BTLEd', '1-C', 'student'),
('241-0097-1', 'Hector', 'S', 'Santos', '$2y$10$jMF8tGmkW2WFb22oz6scmeNRwppnzczpgzNt3O4bRbfqXxnpub2kq', 'BTLEd', '2-C', 'student'),
('241-0098-1', 'Helen', 'R', 'Reyes', '$2y$10$6q8cZlf7Qi9Bcs5PCjQbrufTaT5QgNppaDwZxCHcDbB508BmeC5d6', 'BTLEd', '3-A', 'student'),
('241-0099-1', 'Holden', 'C', 'Cruz', '$2y$10$U693NG1QOIvs4AvA5BPD..9R6SrrhTtUmoqyP44Lv6NUXCw5A.lay', 'BTLEd', '3-A', 'student'),
('241-0100-1', 'Holly', 'G', 'Garcia', '$2y$10$w7KJrCwjiIoSacIR288oIeHJYjWAcNxh6hWPeSCtqAAKZYSnIFGBK', 'BTLEd', '1-A', 'student'),
('241-0101-1', 'Hugo', 'T', 'Tan', '$2y$10$LPnO1shFWwZF8g8h4wrhruxZpmDqKreQMFbOYPB51QlKBvO4uRrwu', 'CA-Animal Science', '3-B', 'student'),
('241-0102-1', 'Hope', 'P', 'Perez', '$2y$10$JAsq9zk6SRgs2ZO0qY/a.up0z.cFkRb8JkEnBSogXU7sQq.wfIL6W', 'CA-Animal Science', '3-D', 'student'),
('241-0103-1', 'Hunter', 'L', 'Lim', '$2y$10$XsSZiKxrLJtYP7U9QTWtlOj85GlOC.3PuwCPWE/Nm2eBPiwYubqhi', 'CA-Animal Science', '1-A', 'student'),
('241-0104-1', 'Ian', 'D', 'David', '$2y$10$SRXmvVg4iDRp9TXY9yclu.S32lQMCnoLsmppPdW6P2gYFvz7LhROG', 'CA-Animal Science', '4-B', 'student'),
('241-0105-1', 'Irene', 'O', 'Ocampo', '$2y$10$i94t64aeEM4/VShsQuchz.ypg1v9Fx3PXDKlRVY08k4IgH.9s/BcW', 'CA-Animal Science', '1-C', 'student'),
('241-0106-1', 'Isaac', 'M', 'Mendoza', '$2y$10$wlr70AjpWeweNzUtFwanaOw1AoQbY8gv/wY2dgid7xjpz8d2VThca', 'CA-Animal Science', '1-D', 'student'),
('241-0107-1', 'Isla', 'F', 'Flores', '$2y$10$TkXR2aMd3u8DP220HT.gN.qgS68ngw4ruJQ2/AflLG.OlibWF48cK', 'CA-Animal Science', '3-B', 'student'),
('241-0108-1', 'Ivan', 'G', 'Gonzales', '$2y$10$ipu5x.zcdwCT27q39EcDoejy61bAu9ox3g/Jkz0nsJiR3JrScIbwi', 'CA-Animal Science', '2-C', 'student'),
('241-0109-1', 'Ivy', 'L', 'Lopez', '$2y$10$ofG0aWdRUvGfsEi0T/DjTOtOtZVUsU8gWQ7sW8YLrMry/bwU6yi0K', 'CA-Animal Science', '1-D', 'student'),
('241-0110-1', 'Jack', 'R', 'Rodriguez', '$2y$10$xWbrvcaHOZk4ur1CbmoxR.CSc6gJt6hlp6gEyYPgGynZUofEB/0Nu', 'CA-Animal Science', '4-B', 'student'),
('241-0111-1', 'Jade', 'A', 'Aquino', '$2y$10$r8EYdJPnqsh/h3nZUNtMBOFmDd6lBkzn6h6lyH5Gs7nc34y4OB45q', 'CA-Apiculture', '3-C', 'student'),
('241-0112-1', 'Jacob', 'T', 'Torres', '$2y$10$/SbY1AU0HCWc/KiNIxDkxe4dMZoYHa7p6ks2qaI13zWFGcChAj07O', 'CA-Apiculture', '4-A', 'student'),
('241-0113-1', 'Jada', 'S', 'Santos', '$2y$10$6LkPI3th1LdzEmnoiaR27eowGopkfzJ71uAypTzb5MaJwLFjnV0/W', 'CA-Apiculture', '4-A', 'student'),
('241-0114-1', 'James', 'R', 'Reyes', '$2y$10$olWDZDrwp0UXVsZact7Su.5oKGBRw1fBw.rCR1XZvmuJ8Nl6wOSIq', 'CA-Apiculture', '3-A', 'student'),
('241-0115-1', 'Jane', 'C', 'Cruz', '$2y$10$cfWvPj7dyrWy8rm1lCrEeOb0YNz6IPT5Eksa.NeOAHDNpn/rHtypO', 'CA-Apiculture', '2-A', 'student'),
('241-0116-1', 'Jasper', 'G', 'Garcia', '$2y$10$eVWWUS85lZ8P5032h2XuuO0E0UxZYIc49zjr/NcIhcJiq5HF45ijG', 'CA-Apiculture', '4-D', 'student'),
('241-0117-1', 'Jasmine', 'T', 'Tan', '$2y$10$4Kh4KwA6BAPzp86vypjyouctcYYwnhUGW3W3WTf2cySYASnhp5Z6m', 'CA-Apiculture', '4-B', 'student'),
('241-0118-1', 'Jason', 'P', 'Perez', '$2y$10$56P/uj7dA9KkLuixPbENUuuSTmy0HRE75LFPl0JrwCWSXK6cdyn76', 'CA-Apiculture', '2-D', 'student'),
('241-0119-1', 'Jenna', 'L', 'Lim', '$2y$10$kPbgAPqbH9U2hmHPvNCco.XvrUIFouXA5zWGubFFtdoLCnUL/Npce', 'CA-Apiculture', '4-B', 'student'),
('241-0120-1', 'Javier', 'D', 'David', '$2y$10$MliZtSAtMTvQwqmENDHtPOrQNWGCQA2noyMp335ZVRnG3FnyWZwlu', 'CA-Apiculture', '3-D', 'student'),
('241-0121-1', 'Jennifer', 'O', 'Ocampo', '$2y$10$56HRCgHcMmPE0abSoGm8lOvnrY6eNUd4VnZI4p6aMGlfTzaGKnyzO', 'CA-Crop Protection', '1-B', 'student'),
('241-0122-1', 'Jeremy', 'M', 'Mendoza', '$2y$10$SG6tvHKlasKBSIP71n7.muTCkfCVS1gu1FlOa.XKJUNnIYPrjQ/5W', 'CA-Crop Protection', '2-C', 'student'),
('241-0123-1', 'Jessica', 'F', 'Flores', '$2y$10$zyi3E0jEWGZb/ybaJRX6COElQTP4irHZAWEO1JmpungqvUhM00v2W', 'CA-Crop Protection', '3-B', 'student'),
('241-0124-1', 'Jesse', 'G', 'Gonzales', '$2y$10$T8B7OAWigsM8IxOQ0YLry.zS8iFOEbSiGoiUlF3sgnijDxqoX5s8C', 'CA-Crop Protection', '3-D', 'student'),
('241-0125-1', 'Jewel', 'L', 'Lopez', '$2y$10$X3raAVL0yydTSkI3fotdcehmfm8.GSE5jitl4HhxVdwO5SXypenLu', 'CA-Crop Protection', '4-C', 'student'),
('241-0126-1', 'Joel', 'R', 'Rodriguez', '$2y$10$pCItX2wBtNExG6z4Rq06Pu4rKLriH5ooNcaumqwpblCEWfYSU4j16', 'CA-Crop Protection', '2-A', 'student'),
('241-0127-1', 'Jocelyn', 'A', 'Aquino', '$2y$10$/XNRe8JiT6ildjeOpscWNuDvsUPb1V51QLxu.DGDNyQs3ZLOhJv6S', 'CA-Crop Protection', '3-C', 'student'),
('241-0128-1', 'John', 'T', 'Torres', '$2y$10$Hox9iigQ3yAS5R0IPgg6MOdSR9fejUFBiTcVLkssZH4Lc.G09QLiy', 'CA-Crop Protection', '2-D', 'student'),
('241-0129-1', 'Jolie', 'S', 'Santos', '$2y$10$H.OhV9JoDhlNqXIxyjcDO.MTcllisAvQeGucF2FbotddwuOfUhso2', 'CA-Crop Protection', '2-D', 'student'),
('241-0130-1', 'Jonathan', 'R', 'Reyes', '$2y$10$6sqCmWfFYh7NRH/d/BPk7OyEqospkF7bcsZH8Oh5mP5sP9k02.E.a', 'CA-Crop Protection', '4-D', 'student'),
('241-0131-1', 'Jordan', 'C', 'Cruz', '$2y$10$LfNqB8VMh2/jMbk338vAZOTWIbInztuI7kAPO9j2ZcQU.tUfcKR8C', 'CA-Crop Science (Agronomy)', '2-A', 'student'),
('241-0132-1', 'Josephine', 'G', 'Garcia', '$2y$10$8UnIcZKXwrt7IW3pe5tBvOugJ5eadW91sOYAtIP7cEzdr1puCdDO.', 'CA-Crop Science (Agronomy)', '3-D', 'student'),
('241-0133-1', 'Jose', 'T', 'Tan', '$2y$10$89YnXEnr71dE7OUsyI8MSOUeFn/xWolUp.SUK3pjz2FccuVZ8hXNu', 'CA-Crop Science (Agronomy)', '3-A', 'student'),
('241-0134-1', 'Joy', 'P', 'Perez', '$2y$10$LmkuP1Y2j09bkaSM9zpMq.PboeuJI6rBAJkg28ufrI2wlxSFbTdaS', 'CA-Crop Science (Agronomy)', '3-C', 'student'),
('241-0135-1', 'Joshua', 'L', 'Lim', '$2y$10$3aSmo4FdWnG6hLp72BWtEeKxQmDQzLpRAXlBcv8JfzbJLqMjPqU7m', 'CA-Crop Science (Agronomy)', '3-D', 'student'),
('241-0136-1', 'Juan', 'D', 'David', '$2y$10$nUoE5GtVMU1dk/OvcNUvaeBPJxj99puWmVc1fDqzzutNQJZqGK9wS', 'CA-Crop Science (Agronomy)', '2-D', 'student'),
('241-0137-1', 'Judith', 'O', 'Ocampo', '$2y$10$qjcDjDYJKoASFg.hvSmhGOMwRQTZzIDUhAY4nyjCXdsBUzhKcb.2G', 'CA-Crop Science (Agronomy)', '1-C', 'student'),
('241-0138-1', 'Jude', 'M', 'Mendoza', '$2y$10$ZBO/zySS7gfLa0nJZn/jHeP/0onHqms.AVNFMeS3TVUaIihXjUQJ2', 'CA-Crop Science (Agronomy)', '3-A', 'student'),
('241-0139-1', 'Julia', 'F', 'Flores', '$2y$10$yI6a85MZppfLK5P9yVrd.OxZ2mIOXT3be.ZhkI7p.MtEY/NyFWeZm', 'CA-Crop Science (Agronomy)', '1-C', 'student'),
('241-0140-1', 'Julian', 'G', 'Gonzales', '$2y$10$4j2mD5OisHP3s3su4YQkPOzuMreD5Hk8nVp20Ysigs5fWe0VsL9W2', 'CA-Crop Science (Agronomy)', '1-A', 'student'),
('241-0141-1', 'Juliana', 'L', 'Lopez', '$2y$10$FZyRUh1I5GvXTjX5naGfJejLkH2lchq45cApq4sNts0nERcIeNoEu', 'CA-Crop Science (Horticulture)', '4-B', 'student'),
('241-0142-1', 'Justin', 'R', 'Rodriguez', '$2y$10$SIdRSqKLoq1j0c9dy5uzmuCdpSEjEznXvlHu1Nqh.Bpvijza5MrZO', 'CA-Crop Science (Horticulture)', '4-D', 'student'),
('241-0143-1', 'Kai', 'A', 'Aquino', '$2y$10$CmVQNJAv.uCNz48CJWgnSuKpyidoCPLwCnzKBU6qTUPZm.N3cB/va', 'CA-Crop Science (Horticulture)', '2-C', 'student'),
('241-0144-1', 'Kaitlyn', 'T', 'Torres', '$2y$10$znfhAGSL2Yy1c0Bwk6jNKul47MBAXL0q0ee90UNtwj4dDcHjB7i8y', 'CA-Crop Science (Horticulture)', '1-D', 'student'),
('241-0145-1', 'Kane', 'S', 'Santos', '$2y$10$wx62IAC.dXKW6j7MVwHxzOTlIj3FJnqcXZdhgKGLja7N/aGttE0bW', 'CA-Crop Science (Horticulture)', '4-A', 'student'),
('241-0146-1', 'Kara', 'R', 'Reyes', '$2y$10$qwrQGb14X0lFPAAGjkKHNOVMQlSsNoSPXPDl/5GJAydvVLdYCJJoW', 'CA-Crop Science (Horticulture)', '2-A', 'student'),
('241-0147-1', 'Karl', 'C', 'Cruz', '$2y$10$6zWTgAp9zY8xJKFrJmTycO9nxd6CigFGXOGsPFA4/m7Yb1UFFkvZK', 'CA-Crop Science (Horticulture)', '1-B', 'student'),
('241-0148-1', 'Karla', 'G', 'Garcia', '$2y$10$JFZGHkzEncvRHJVJgzkt0eS3/f7DU8vxKqS3HtqQmQUr.l7qFXma.', 'CA-Crop Science (Horticulture)', '3-A', 'student'),
('241-0149-1', 'Keith', 'T', 'Tan', '$2y$10$IkbDA7mYdh66M1zspKlBp..1HGKoTWZuZJ99bpmYp1U6VQRFLZMPa', 'CA-Crop Science (Horticulture)', '2-A', 'student'),
('241-0150-1', 'Kelly', 'P', 'Perez', '$2y$10$OaSwA/GJ9ZEgE4bQgeO9zOARmXLGQ9MHO8Q4ANPgXQtF54dGXIjsK', 'CA-Crop Science (Horticulture)', '1-A', 'student'),
('241-0151-1', 'Ken', 'L', 'Lim', '$2y$10$1xTvPV1lPRHc3z3PEsFZn.d9YKBewLtWquRzmoRVg0C.VhBz8vmvu', 'CA-Soil Science', '1-B', 'student'),
('241-0152-1', 'Kendra', 'D', 'David', '$2y$10$BE9ZIuaB1j8x/jxaV1izVOyuT7kc9EVjP7k5UzVa6lKf0U3Zs8VXi', 'CA-Soil Science', '3-A', 'student'),
('241-0153-1', 'Kevin', 'O', 'Ocampo', '$2y$10$y0Hc8N/Ds78gaQvnHiYjDuajCjB4tOYk/ut0s6/WKD.rkJXmkoP5S', 'CA-Soil Science', '2-D', 'student'),
('241-0154-1', 'Kimberly', 'M', 'Mendoza', '$2y$10$gSW.3skFDp/i76PW1empEOa8BA5UFFTMz8fNppziKqXvsWwBoFsRq', 'CA-Soil Science', '3-A', 'student'),
('241-0155-1', 'Kingston', 'F', 'Flores', '$2y$10$TShRg4kJODGY2fkEVpxO.uwgU3ZhhIM0xyXrVTHmapqibeNkMkFv.', 'CA-Soil Science', '2-D', 'student'),
('241-0156-1', 'Kira', 'G', 'Gonzales', '$2y$10$4SiHrhfMBSGi92Fm5ks.hOTTh2KLroPoUwgamdUrn6ZnA12Qx6X0a', 'CA-Soil Science', '3-A', 'student'),
('241-0157-1', 'Kyle', 'L', 'Lopez', '$2y$10$RTvHFSR258foLZW9RanuN.6sogyZPW2YTsEc5hGg6LEQNowTHY.wa', 'CA-Soil Science', '2-B', 'student'),
('241-0158-1', 'Kylie', 'R', 'Rodriguez', '$2y$10$ha4yX10jBvWImT2GNWf.muiY201FtX9OFH7Es/d4.G0tlnEieHvKO', 'CA-Soil Science', '1-C', 'student'),
('241-0159-1', 'Lance', 'A', 'Aquino', '$2y$10$SlQUMk7uI9kXRbOPURHE6eTiV5qD.w9kIWUzLAW99W1hxyxqP8rFK', 'CA-Soil Science', '4-B', 'student'),
('241-0160-1', 'Lana', 'T', 'Torres', '$2y$10$kYfs/.KOyj0hRAWCcQ3QcuXG6unIEgkYbL5F0uzEstCEyO1DVMOG6', 'CA-Soil Science', '3-C', 'student'),
('241-0161-1', 'Landon', 'S', 'Santos', '$2y$10$RmWoTvacK9R59hvKEwo/WuRemv3Y0XHn4UolTM.a4elDP1O8BtN6y', 'CAFF-Agroforestry', '4-A', 'student'),
('241-0162-1', 'Laura', 'R', 'Reyes', '$2y$10$XBNmWgEL4KYOVEzFeDTIIOQIwkjPSviL540OSwX2.1zzyhRvnGPEa', 'CAFF-Agroforestry', '1-A', 'student'),
('241-0163-1', 'Lawrence', 'C', 'Cruz', '$2y$10$7YYXFB3LKmQhDgMnj7s69.afjEAJfGhL2qjssm3PbsoqUdLpCE5uq', 'CAFF-Agroforestry', '3-B', 'student'),
('241-0164-1', 'Layla', 'G', 'Garcia', '$2y$10$HiPEUKSo5k5xFo6LjhnTAu9wHG4skSBBlrnejvcv4gAQ.53RKz8TK', 'CAFF-Agroforestry', '1-B', 'student'),
('241-0165-1', 'Lee', 'T', 'Tan', '$2y$10$w8340RXfGJbst.P/4Ro.reFdBeHbPbUmZwM0YDf7JTZibSEKlpT2S', 'CAFF-Agroforestry', '3-B', 'student'),
('241-0166-1', 'Leah', 'P', 'Perez', '$2y$10$kWqazqqOu9aEHu7kWPgfCuZW8.MWmloizjFTp/9MSpWtkreQxwcTS', 'CAFF-Agroforestry', '4-B', 'student'),
('241-0167-1', 'Leo', 'L', 'Lim', '$2y$10$P5aTXstA/adXzel94RJLKe/kYaUs3Hf7vGIipjoi3x/Y4gh1Z52by', 'CAFF-Agroforestry', '1-D', 'student'),
('241-0168-1', 'Lena', 'D', 'David', '$2y$10$7fB5emAZlFnK3cA1qOgPDONPzbzKOs8EVdbgvKT/FgI1K0bCVvSZ6', 'CAFF-Agroforestry', '3-B', 'student'),
('241-0169-1', 'Leon', 'O', 'Ocampo', '$2y$10$.GLqCVHx7crbdu9HdkUF1O06pnkYrEYMEXcVurHeyCokTHSmWWLqy', 'CAFF-Agroforestry', '2-B', 'student'),
('241-0170-1', 'Leslie', 'M', 'Mendoza', '$2y$10$My.ZHXZ.lKA6XS8umRdFZOHprPKGO.Fz4bLwL7ZA19ZbS1H6cpWQK', 'CAFF-Agroforestry', '4-D', 'student'),
('241-0171-1', 'Levi', 'F', 'Flores', '$2y$10$8viuoc/zHzA.YQvWI9Z66.bL9ySKjfy/aEDpvHTvAl01/21QYi6u6', 'CAFF-Forestry', '3-D', 'student'),
('241-0172-1', 'Lexi', 'G', 'Gonzales', '$2y$10$cGO30PvxGP4Y2fU6KDRm9Oae3V2e6AxEdsvBVEZXm34HQISU9rv4m', 'CAFF-Forestry', '4-A', 'student'),
('241-0173-1', 'Liam', 'L', 'Lopez', '$2y$10$rRRlJUTMC4/EP9kEx8bzHeu3dqk50Ta66hstPiLj5wnwT3uNjrXzG', 'CAFF-Forestry', '3-C', 'student'),
('241-0174-1', 'Lila', 'R', 'Rodriguez', '$2y$10$o5F.PDvK721PkeN/BY6azuSZtzBI1Bm8.qhe4T973hx.RR1yLf9Fu', 'CAFF-Forestry', '4-D', 'student'),
('241-0175-1', 'Lincoln', 'A', 'Aquino', '$2y$10$CfqszD6TVR9aMDUtnmcYi.C1qOd7wOMTnvp0ZBXdpzk9ZCp5byizW', 'CAFF-Forestry', '4-D', 'student'),
('241-0176-1', 'Lillian', 'T', 'Torres', '$2y$10$3qDrJ0LLvzUNiizscj1NzuGvgvf35TUnu6qdxnWNQxqhvYzNSw/Wa', 'CAFF-Forestry', '4-A', 'student'),
('241-0177-1', 'Lionel', 'S', 'Santos', '$2y$10$tdwT4AV7WXtjdWGMT8Xr/OEFESOltABVUj2zF8ugJUUJ25.FBdV2K', 'CAFF-Forestry', '1-C', 'student'),
('241-0178-1', 'Lily', 'R', 'Reyes', '$2y$10$kXz7iRy01orBBccMwINfKeUfvCexzPaHIZwsUXFQ8ikkZKAVlnj2u', 'CAFF-Forestry', '2-A', 'student'),
('241-0179-1', 'Logan', 'C', 'Cruz', '$2y$10$nQO7SI4xYVFzM6mqxSV3deCYZyJF16108bIvXLXGrl7DmIDxp3Suq', 'CAFF-Forestry', '1-B', 'student'),
('241-0180-1', 'Lola', 'G', 'Garcia', '$2y$10$JkUZ3epUkI6SRwC9.pXsc.lfhnOwvg0BFC0d9vEvQQ.k3g9xSLZXy', 'CAFF-Forestry', '2-D', 'student'),
('241-0181-1', 'Lorenzo', 'T', 'Tan', '$2y$10$TtotbAeRNJiIlthrljsqquZsAsRXL7R77FlxjUl7M0s9BI5Ej0oVy', 'CAS-Arts in English Language', '2-A', 'student'),
('241-0182-1', 'Louisa', 'P', 'Perez', '$2y$10$XFKv5hdjtNxzzEA8.hsAhOUuhx6IwHVqETc3yIFPzpOK/Ejtczxme', 'CAS-Arts in English Language', '4-A', 'student'),
('241-0183-1', 'Louis', 'L', 'Lim', '$2y$10$5DDdia8xw65Qgb2GV3uO2uCxiCoU6kIzPm9Fr8IwAss3tfplOHwVK', 'CAS-Arts in English Language', '1-C', 'student'),
('241-0184-1', 'Lucia', 'D', 'David', '$2y$10$WxCk7OwVs6mAqiu1sFTtO.Rcb/J6TsA8CfCTG6hAOWn372w/hwE22', 'CAS-Arts in English Language', '2-B', 'student'),
('241-0185-1', 'Luca', 'O', 'Ocampo', '$2y$10$ZeVVOD3l1BUtu83aubwWGeERquh6qpv3g3n7RyW5Ex93Yi9svUTK.', 'CAS-Arts in English Language', '1-C', 'student'),
('241-0186-1', 'Lucy', 'M', 'Mendoza', '$2y$10$/BIAaHOdc19S4CNR4lrNwedRLNt3GrbhiexqnGKFpbK/kP9xD9OmO', 'CAS-Arts in English Language', '4-D', 'student'),
('241-0187-1', 'Lucas', 'F', 'Flores', '$2y$10$HkkS3v9Uov57iuqZEZylEeEKGQ6IgIJtVLmki9voQELUqiT63BQMC', 'CAS-Arts in English Language', '1-D', 'student'),
('241-0188-1', 'Luna', 'G', 'Gonzales', '$2y$10$iJags6borPh94OwuAvyY5upFHmnxAoUv2Gf2TlGnWHcZYR.MGws86', 'CAS-Arts in English Language', '1-D', 'student'),
('241-0189-1', 'Luke', 'L', 'Lopez', '$2y$10$XJGSaIOdR6nCYVDreXWpYew.6jVWCnEQUUUUyoaM1KULC5yktvwp2', 'CAS-Arts in English Language', '2-B', 'student'),
('241-0190-1', 'Lydia', 'R', 'Rodriguez', '$2y$10$oPg3OWDzrCSedajZTCQGe.p.67qJjYJUmdQ.NAK1FH5slHIe/ABnO', 'CAS-Arts in English Language', '2-A', 'student'),
('241-0191-1', 'Maddox', 'A', 'Aquino', '$2y$10$blPz/5FfGI2iqzf2W.acWeAkQ2dO9jDvPYjff7M8QmurhWT62O63G', 'CAS-Science in Biology', '1-D', 'student'),
('241-0192-1', 'Mackenzie', 'T', 'Torres', '$2y$10$TUxwi7RuJD5m1dG6N5YKIuy7nkfDrip0yI8./w4ZcNamvE1S3c.ly', 'CAS-Science in Biology', '2-B', 'student'),
('241-0193-1', 'Malachi', 'S', 'Santos', '$2y$10$n98r6XIinIY4kZyL4rN23uJAQTWTKSx3s2ifUv.Jyqgpp4Cb0mVY6', 'CAS-Science in Biology', '1-B', 'student'),
('241-0194-1', 'Madeline', 'R', 'Reyes', '$2y$10$uQ80StCM0j3n94mKB0PMlekyvXHhPotwZ4FY57mvJTtvatwD.oiBK', 'CAS-Science in Biology', '2-C', 'student'),
('241-0195-1', 'Manuel', 'C', 'Cruz', '$2y$10$pJ0.mmvP99osJipD.TX8xet9szQhlGnVze/fNBH8j2EdW2.7Nnbau', 'CAS-Science in Biology', '4-B', 'student'),
('241-0196-1', 'Madison', 'G', 'Garcia', '$2y$10$5jnq2XbfIBhDQHPhCMRoseB0qiwotOdwC0d91LQCP0Px7FsExgzcO', 'CAS-Science in Biology', '1-A', 'student'),
('241-0197-1', 'Marco', 'T', 'Tan', '$2y$10$hCLTphH1q/dbk42/CaxzWuZpSogJIBbqDHhgab5rqYUkUAOjQiSLG', 'CAS-Science in Biology', '2-D', 'student'),
('241-0198-1', 'Margaret', 'P', 'Perez', '$2y$10$Utj6iT1Hn2UwuvNJjeL9behNQ6Kf7Rp1W31v3etip20QhAT7Py9EO', 'CAS-Science in Biology', '2-A', 'student'),
('241-0199-1', 'Marcus', 'L', 'Lim', '$2y$10$i/MpbT.JyEhHzqisD4EFiu6DHgZrqqNQJ0gDJzTVvxDx.fN6IlrHi', 'CAS-Science in Biology', '4-D', 'student'),
('241-0200-1', 'Maria', 'D', 'David', '$2y$10$K3NlkOBLA64X3mdJbzRhqeoaK/0.3f8cJvkkLLz9iTpYkYBcnDR1y', 'CAS-Science in Biology', '4-C', 'student'),
('241-0201-1', 'Mario', 'O', 'Ocampo', '$2y$10$kpWhffDk3gYPenXM/hzS9OJszMjN7esaQ3zN/kgkDcwbAUSTX7UNS', 'CIS', '3-D', 'student'),
('241-0202-1', 'Mariana', 'M', 'Mendoza', '$2y$10$qAXcE0N/leBXGE157etIKeYmnaxoZlVkVgybgIztoU6/1gpiefkk6', 'CIS', '4-B', 'student'),
('241-0203-1', 'Mark', 'F', 'Flores', '$2y$10$C8zsVVz6l11P0PEOwbjuwOpoVqLZ38b5zXNmHchg3kN5DZdqmZvIS', 'CIS', '1-A', 'student'),
('241-0204-1', 'Marley', 'G', 'Gonzales', '$2y$10$TroUafDP04V4yOv7Sb03D.KvDbwYHh9q5Ehi3B2k1OkzbsKHw8xsu', 'CIS', '3-C', 'student'),
('241-0205-1', 'Martin', 'L', 'Lopez', '$2y$10$nolHFm2FpTCTKATrbNIrf.4kPsFKjl51J4QEPc7iFLRa9705JKEvG', 'CIS', '1-C', 'student'),
('241-0206-1', 'Mary', 'R', 'Rodriguez', '$2y$10$ssJbhtocVSQiXET.hohyxOC8YzGUFB2/eoBIDRO4Ghxaranux5rny', 'CIS', '3-D', 'student'),
('241-0207-1', 'Mateo', 'A', 'Aquino', '$2y$10$LIrIxmXp5goeMYS0pq2Bs..nZm45dA1MhtTQVKr2Z4TlaWzejQbHq', 'CIS', '1-C', 'student'),
('241-0208-1', 'Maya', 'T', 'Torres', '$2y$10$2s4aS/FeMW0SgRTauOzEt.UUwnE3dfIPYIb7jSpFydILUHdzc/H6i', 'CIS', '3-D', 'student'),
('241-0209-1', 'Matthew', 'S', 'Santos', '$2y$10$PJ3HxKULmu90BAHsqhNuSeNlEyJAGFka5TgQQnq8fzEdvsLlhzIJe', 'CIS', '1-C', 'student'),
('241-0210-1', 'Melody', 'R', 'Reyes', '$2y$10$Vy7z3Bvk6EnXBpJax0CMY.y9RiEn8Ww1TSfLMLQyFgZKw8HzEMF22', 'CIS', '3-A', 'student'),
('241-0211-1', 'Maverick', 'C', 'Cruz', '$2y$10$Di.fK9iNuh60bpTX4vayB.2efFTzQ.68yNZ4kQ7vSSYTuwu2QRdxC', 'CVM', '1-C', 'student'),
('241-0212-1', 'Mila', 'G', 'Garcia', '$2y$10$WH9xnARqMBOzgIzfuSzNieSI6U800NpEL72pP.hwTBvJWBuZpRDaK', 'CVM', '1-C', 'student'),
('241-0213-1', 'Max', 'T', 'Tan', '$2y$10$zHKR53QmM7RAq9SHUZrK/.i7G..JCAfOlBQowQ3WYZsrmlpXaHB3K', 'CVM', '2-C', 'student'),
('241-0214-1', 'Millie', 'P', 'Perez', '$2y$10$8JZqm5RAiO31NNQCGQ6//OVkCDdCUG.HBmQM..O3jREBsiJrC5Ce6', 'CVM', '2-C', 'student'),
('241-0215-1', 'Maximilian', 'L', 'Lim', '$2y$10$XMT.0OVsS0TS1rZDjI0PiuxWk2eeaABOOkGJvGQlOiYwRL2ukwlsS', 'CVM', '4-D', 'student'),
('241-0216-1', 'Miranda', 'D', 'David', '$2y$10$z.w9fpq5aniJXS8wDFRt/uryV286dJ4kB8nV2qjIHyVMs2r99ISvO', 'CVM', '3-C', 'student'),
('241-0217-1', 'Maxwell', 'O', 'Ocampo', '$2y$10$Rzzd.vLKP86nABC0Ep/5j.cjs245guHKM/yq7kM78m/yP3dA4lGCW', 'CVM', '2-B', 'student'),
('241-0218-1', 'Molly', 'M', 'Mendoza', '$2y$10$KYZnrUgOgHWHkWaiRC7p2u.vP8b7P9j/vGefpIsQRKT2WIip2olDW', 'CVM', '3-D', 'student'),
('241-0219-1', 'Micah', 'F', 'Flores', '$2y$10$uDDbnQ8JdJO818XGLOeDqe419UGDHeRzyJ1zA.54jvnjVOv3sGvaG', 'CVM', '2-D', 'student'),
('241-0220-1', 'Morgan', 'G', 'Gonzales', '$2y$10$JlsWVP6LAaM7H.8nZ.1Q2.GCXTQRAVLBmTGSWKCVzvkKWE4FT6qiG', 'CVM', '2-A', 'student'),
('241-0221-1', 'Michael', 'L', 'Lopez', '$2y$10$lB6w1LPJ/7P8V3ME8vqccOt2Kempk33kOyQsRaHgCxz2sdHHDGpE6', 'IABE', '4-B', 'student'),
('241-0222-1', 'Naomi', 'R', 'Rodriguez', '$2y$10$cJB.0NZxee9329SXqNJZr.hl5.X6f5oFgxwNwZpWsFXCbgSqCbIhy', 'IABE', '3-A', 'student'),
('241-0223-1', 'Miles', 'A', 'Aquino', '$2y$10$JSHSjKFPygRPbvKCQQIgVuuDwalc6ugxbEfiIoXLe2LJg82Mq/VO6', 'IABE', '4-D', 'student'),
('241-0224-1', 'Natalia', 'T', 'Torres', '$2y$10$9yQYr2vs/GfRgQ4r0DGn/uuaau6WENKjFtIa.Rh/dXHXhJlcTyrkC', 'IABE', '2-D', 'student'),
('241-0225-1', 'Milo', 'S', 'Santos', '$2y$10$YPgpdDJqxMYPyI1AY3HxD.m.NvtFyA5U2mb4P1jk0bxPqJ3j548v.', 'IABE', '2-A', 'student'),
('241-0226-1', 'Nevaeh', 'R', 'Reyes', '$2y$10$Xwk47kumppFq3QNsR9XzSeuJZmwJ7Er/AIpQ1ljmZDMD6F0KPD.vi', 'IABE', '1-B', 'student'),
('241-0227-1', 'Mitchell', 'C', 'Cruz', '$2y$10$59TPiiir4VMn.AQIGWS1Qu5.HZ1f0GNwqGNgzX/0hIFCmh/0IIEey', 'IABE', '3-A', 'student'),
('241-0228-1', 'Nicole', 'G', 'Garcia', '$2y$10$lMkRXZ0sxqFDWo6IDcR76OUZs8hjamSGbpwLDoR6C9W1qK444zoka', 'IABE', '2-C', 'student'),
('241-0229-1', 'Moses', 'T', 'Tan', '$2y$10$tDwPCStZ4BbMjRy9Q80krO4AoqF1m4YFPGV1wMlHrFOzI2zb9/D2y', 'IABE', '2-B', 'student'),
('241-0230-1', 'Nina', 'P', 'Perez', '$2y$10$Q2liqdpvA3p5fo..fKBBQOtoNvfOcHssQiq5vwUgLYGJuJAqlbXWu', 'IABE', '2-D', 'student'),
('241-0231-1', 'Nash', 'L', 'Lim', '$2y$10$HaFNmELkbcul.eX9DUa4KucINZq8kfAosKjiD.AXnPyPSGPz5zihC', 'IES', '2-A', 'student'),
('241-0232-1', 'Nora', 'D', 'David', '$2y$10$Fd/awToR5tVLcHPtzFA7H.XS6tQ9voC4fkpNaThfTPYQvz5gAxPm2', 'IES', '1-C', 'student'),
('241-0233-1', 'Nathan', 'O', 'Ocampo', '$2y$10$SX6VSaLicHQhTifsrtNLyOGFKOfjE3HI7bleQPXUSoCO.GCMN.ML.', 'IES', '1-A', 'student'),
('241-0234-1', 'Nova', 'M', 'Mendoza', '$2y$10$0W.qSXOt0jsji91xMcyPgeYqziOIltEKZd1VrlWsz8BaiD2KwfLDO', 'IES', '2-D', 'student'),
('241-0235-1', 'Nathaniel', 'F', 'Flores', '$2y$10$8mSMHLwKFJA2KCTx12MPI.dY2idedgq0J19fq38BhhsdBENly7rwG', 'IES', '2-A', 'student'),
('241-0236-1', 'Oakley', 'G', 'Gonzales', '$2y$10$szue7suw2XXfjlUbY7cxOun3bb25Bp4q41xkd7XvUMycPbciUWmJe', 'IES', '4-C', 'student'),
('241-0237-1', 'Neil', 'L', 'Lopez', '$2y$10$1k2QKFS2h8Yje6eyfC4Vvu16a9gPouL0Ao.n7U/PglzHp/YVFwF5W', 'IES', '4-B', 'student'),
('241-0238-1', 'Octavia', 'R', 'Rodriguez', '$2y$10$HsIQFqhNSlhETWgvlLx/Te9E3yBboXPPddYj2WFVB1WWyOP8tUuCO', 'IES', '2-C', 'student'),
('241-0239-1', 'Nicolas', 'A', 'Aquino', '$2y$10$FTt6RhD6oADmTQ96EBYDnu.Zv.TILYXJbbgfVq8xdX3CTGCLbYRCu', 'IES', '3-B', 'student'),
('241-0240-1', 'Olive', 'T', 'Torres', '$2y$10$U/kU02dlvxx9NDTE1t0HH.OJsg/rJ0llAs3D/MIV7YEI08aH/AY7K', 'IES', '4-A', 'student');

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
(58, '241-0047-1', 'GECC105', '00011', 'BECEd', '2025-2026', '1st Semester', '2025-10-20 18:19:46', '{\"q0\":5,\"q1\":4,\"q2\":4,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":4,\"q8\":3,\"q9\":3,\"q10\":4,\"q11\":5,\"q12\":5,\"q13\":4,\"q14\":5}', 64, 85.33, ''),
(59, '241-0047-1', 'GEMC101a', '00012', 'BECEd', '2025-2026', '1st Semester', '2025-10-20 18:27:08', '{\"q0\":5,\"q1\":4,\"q2\":4,\"q3\":4,\"q4\":5,\"q5\":4,\"q6\":3,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":4,\"q12\":5,\"q13\":4,\"q14\":4}', 64, 85.33, ''),
(60, '241-0208-1', 'ISPC104 ', '221-0387-1', 'CIS', '2025-2026', '1st Semester', '2025-10-21 21:58:04', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":3,\"q6\":3,\"q7\":4,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":3,\"q12\":4,\"q13\":4,\"q14\":4}', 60, 80.00, '');

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
(118, '000-0000-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(119, '000-0000-1', 'ISBA105', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(120, '221-0388-1', 'ISBA105', '2025-2026', '2nd Semester', '001-0000-0', NULL),
(121, '221-0388-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(122, '000-0000-1', 'ISPC104 ', '2025-2026', '2nd Semester', '221-0387-1', NULL),
(123, '000-0000-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(124, '221-0388-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(125, '221-0388-1', 'ISPC104 ', '2025-2026', '2nd Semester', '221-0387-1', NULL),
(126, '000-0000-1', 'ISPC106', '2025-2026', '2nd Semester', '100-0000-0', NULL),
(127, '221-0388-1', 'ISPC106', '2025-2026', '2nd Semester', '100-0000-0', NULL),
(128, '000-0000-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(129, '221-0388-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(130, '241-0203-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(131, '241-0205-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(132, '241-0207-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(133, '241-0209-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(134, '241-0210-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(135, '241-0046-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(136, '241-0041-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(137, '241-0041-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(138, '241-0044-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(139, '241-0044-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(140, '241-0043-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(141, '241-0043-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(142, '241-0047-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(143, '241-0047-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(144, '241-0042-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(145, '241-0042-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(146, '241-0045-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(147, '241-0045-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(148, '241-0046-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(149, '241-0046-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(150, '241-0048-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(151, '241-0048-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(152, '241-0003-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(153, '241-0003-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(154, '241-0003-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(155, '241-0003-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(156, '241-0003-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(157, '241-0004-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(158, '241-0004-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(159, '241-0004-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(160, '241-0004-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(161, '241-0004-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(162, '241-0010-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(163, '241-0010-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(164, '241-0010-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(165, '241-0010-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(166, '241-0010-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(167, '241-0009-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(168, '241-0009-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(169, '241-0009-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(170, '241-0009-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(171, '241-0009-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(172, '241-0006-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(173, '241-0006-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(174, '241-0006-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(175, '241-0006-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(176, '241-0006-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(177, '241-0002-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(178, '241-0002-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(179, '241-0002-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(180, '241-0002-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(181, '241-0002-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(182, '241-0005-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(183, '241-0005-1', 'GECC105', '2025-2026', '1st Semester', '00011', NULL),
(184, '241-0005-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(185, '241-0005-1', 'GEMC101a', '2025-2026', '1st Semester', '00012', NULL),
(186, '241-0005-1', 'ISBA105', '2025-2026', '1st Semester', '001-0000-0', NULL),
(187, '241-0008-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(188, '241-0008-1', 'ISPC106', '2025-2026', '1st Semester', '100-0000-0', NULL),
(189, '241-0202-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL),
(190, '241-0208-1', 'ISPC104 ', '2025-2026', '1st Semester', '221-0387-1', NULL);

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
  `department` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`, `admin_id`, `department`) VALUES
(52, 'ISBA105', 'Analytics Application', '001-0000-0', NULL, 'CIS'),
(53, 'ISPC104 ', ' IT Audit and Control', '221-0387-1', NULL, 'CIS'),
(54, 'ISPC106', 'Financial Management', '100-0000-0', NULL, 'CIS'),
(55, 'GECC105', 'Theory of Probability', '00011', NULL, 'BECEd'),
(56, 'GEMC101a', 'Life and Works of Rizal', '00012', NULL, 'BECEd');

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
('221-0387-1', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$0MVzJneKo66YW/H.pvOIROKSb.42/xD4OR2lytbaCGkNdKBNUkYxK', 'superadmin', 'CIS', 'Professor IV', 'Head Instruction', 'active', 'Yes');

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
  ADD KEY `fk_admin_faculty_rank` (`faculty_rank`);

--
-- Indexes for table `admin_departments`
--
ALTER TABLE `admin_departments`
  ADD PRIMARY KEY (`admin_idnumber`,`department_name`),
  ADD KEY `department_name` (`department_name`);

--
-- Indexes for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_evaluator` (`evaluator_id`),
  ADD KEY `fk_faculty_evaluatee` (`evaluatee_id`),
  ADD KEY `fk_evaluator_position` (`evaluator_position`),
  ADD KEY `fk_admin_evaluation_department` (`department`);

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
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `fk_superadmin_faculty_rank` (`faculty_rank`),
  ADD KEY `fk_superadmin_position` (`position`),
  ADD KEY `fk_superadmin_department` (`department`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=409;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_faculty_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_position` FOREIGN KEY (`position`) REFERENCES `adds` (`position_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_departments`
--
ALTER TABLE `admin_departments`
  ADD CONSTRAINT `admin_departments_ibfk_1` FOREIGN KEY (`admin_idnumber`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_departments_ibfk_2` FOREIGN KEY (`department_name`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD CONSTRAINT `fk_admin_evaluation_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_evaluator` FOREIGN KEY (`evaluator_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eval_admin` FOREIGN KEY (`evaluator_id`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eval_faculty` FOREIGN KEY (`evaluatee_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluator_position` FOREIGN KEY (`evaluator_position`) REFERENCES `admin` (`position`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_evaluatee` FOREIGN KEY (`evaluatee_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `faculty_id_key` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluation_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
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

--
-- Constraints for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD CONSTRAINT `fk_superadmin_department` FOREIGN KEY (`department`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_superadmin_faculty_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_superadmin_position` FOREIGN KEY (`position`) REFERENCES `adds` (`position_name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
