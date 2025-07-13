-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 06:10 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `position` varchar(50) NOT NULL,
  `faculty` varchar(11) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `position`, `faculty`, `role`, `status`) VALUES
('000-0000-0', 'Ma\'amEdith', 'Admin', 'Faculty', 'sampleadmin@email.com', 'ILOVEDMMMSU', 'CIS', 'Dean', 'yes', 'admin', 'active'),
('000-0000-1', 'Maricel', 'O', 'Pre', 'email@email.com', 'ILOVEDMMMSU', 'CIS', 'Dean', 'yes', 'admin', 'active'),
('000-0000-3', 'First', 'Middle', 'Last', 'samplefaculty2@email.com', 'ILOVEDMMMSU', 'CIS', 'Dean', 'yes', 'admin', 'active'),
('001-0000-1', 'Sample', 'Admin', '!Faculty', 'sampleadmin2@email.com', 'ILOVEDMMMSU', 'CAS', 'Campus-Administrator', 'no', 'admin', 'active'),
('002-0000-2', 'Admin', 'Sample', 'Faculty', 'sampleadmin3@email.com', 'ILOVEDMMMSU', 'CAS', 'Dean', 'yes', 'admin', 'active'),
('003-0000-3', 'AdminAs', 'Faculty', 'Sample', 'sampleadmin4@emial.com', 'ILOVEDMMMSU', 'CVM', 'Dean', 'yes', 'admin', 'active'),
('004-0000-4', 'Admin', 'As', 'Faculty', 'adminasfaculty@email.com', 'ILOVEDMMMSU', 'CAFF', 'Dean', 'yes', 'admin', 'active'),
('221-0422-1', 'Mark', 'Kristian', 'Lagman', 'markkristian@email.com', 'ILOVEDMMMSU', 'CVM', 'Vice-President', 'yes', 'admin', 'active');

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
(12, '000-0000-0', '000-0000-3', 'Dean', '2025-2026', '1st Semester', 71, 94.67, '', 'CIS', '2025-07-06 20:52:13'),
(16, '000-0000-0', '000-0000-1', 'Dean', '2024-2025', '2nd Semester', 69, 92.00, 'yes', 'CIS', '2025-07-09 18:11:10'),
(17, '000-0000-0', '000-0000-1', 'Dean', '2025-2026', '1st Semester', 71, 94.67, 'yes no', 'CIS', '2025-07-09 19:48:25');

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
(62, '111-1111-1', 'CAS', 'PATHFIT-2', 'Physical Damage', '2025-2026', '002-0000-2', 73.00, 97.33, '', '2025-07-09 11:42:43', '1st Semester', '1-B'),
(63, '123-4567-8', 'CVM', 'ISPC-114', 'Capstone Project 2', '2025-2026', '221-0422-1', 61.00, 81.33, 'mark lng sakamal', '2025-07-10 15:11:44', '1st Semester', '1-B'),
(64, '123-4567-8', 'CVM', 'GECC-105', 'Mathematics', '2025-2026', '000-0000-2', 72.00, 96.00, '', '2025-07-11 16:39:44', '1st Semester', '1-B');

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
(1, '1st Semester', '2025-2026', '2025-07-12 07:42:19');

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
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `department` varchar(50) NOT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `faculty_rank`, `role`, `status`) VALUES
('000-0000-0', 'MaamEdith', 'Admin', 'Faculty', '', '', 'CIS', '', '', ''),
('000-0000-1', 'Maricel', 'O', 'Pre', '', '', 'CIS', 'Professor IV', 'faculty', 'inactive'),
('000-0000-2', 'Faculty', 'Sample', 'Value', 'samplefaculty@email.com', 'ILOVEDMMMSU', 'CVM', 'Assistant Professor I', 'faculty', 'active'),
('000-0000-3', 'First', 'Middle', 'Last', '', '', 'CIS', 'Assistant Professor I', 'faculty', 'active'),
('000-0000-4', 'Rufo', 'A', 'Baro', 'email@email.com', 'ILOVEDMMMSU', 'CIS', 'Professor V', 'faculty', 'active'),
('002-0000-2', 'Admin', 'Sample', 'Faculty', '', '', 'CAS', '', 'faculty', 'active'),
('003-0000-3', 'AdminAs', 'Faculty', 'Sample', '', '', 'CVM', '', 'faculty', 'inactive'),
('004-0000-4', 'Admin', 'As', 'Faculty', '', '', 'CAFF', 'Dean', 'faculty', 'active'),
('221-0422-1', 'Mark', 'Kristian', 'Lagman', 'markkristian@email.com', 'ILOVEDMMMSU', 'CVM', 'Professor V', 'faculty', 'active');

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
-- Table structure for table `faculty_ranks`
--

CREATE TABLE `faculty_ranks` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_ranks`
--

INSERT INTO `faculty_ranks` (`id`, `rank_name`) VALUES
(1, 'Instructor I'),
(2, 'Instructor II'),
(3, 'Instructor III'),
(4, 'Assistant Professor I'),
(5, 'Assistant Professor II'),
(6, 'Assistant Professor III'),
(7, 'Assistant Professor IV'),
(8, 'Associate Professor I'),
(9, 'Associate Professor II'),
(10, 'Associate Professor III'),
(11, 'Associate Professor IV'),
(12, 'Associate Professor V'),
(13, 'Professor I'),
(14, 'Professor II'),
(15, 'Professor III'),
(16, 'Professor IV'),
(17, 'Professor V'),
(18, 'Professor VI');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `section`, `role`) VALUES
('111-1111-1', 'Student', 'Sample', 'Value', 'sample@email.com', 'ILOVEDMMMSU', 'CIS', '1-B', 'student'),
('111-2222-3', 'Kulark', 'Juswa', 'Rujas', 'email@email.com', 'ILOVEDMMMSU', 'CAS', '1-B', 'student'),
('123-4567-8', 'Clark Joshua', 'Velasco', 'Rojas', 'email@email.com', 'ILOVEDMMMSU', 'CIS', '1-A', 'student');

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
(14, '111-2222-3', 'ISPC-101', '000-0000-1', NULL),
(15, '111-2222-3', 'ISBA-101', NULL, '000-0000-0'),
(16, '111-1111-1', 'ISBA-101', NULL, '000-0000-0'),
(17, '111-1111-1', 'PATHFIT-2', NULL, '002-0000-2'),
(18, '123-4567-8', 'ISBA-102', '000-0000-0', NULL),
(19, '123-4567-8', 'ISBA-101', NULL, '000-0000-0'),
(20, '111-1111-1', 'ISPC-101', '000-0000-1', NULL),
(21, '123-4567-8', 'ISPC-114', '221-0422-1', NULL),
(22, '123-4567-8', 'GECC-105', '000-0000-2', NULL),
(23, '111-1111-1', 'ISPC-114', '221-0422-1', NULL),
(24, '123-4567-8', 'ISBA-105', '000-0000-4', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `idnumber` int(11) NOT NULL,
  `code` varchar(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `admin_id` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`, `admin_id`) VALUES
(13, 'ISBA-101', 'Business Again', NULL, '000-0000-0'),
(14, 'ISPC-101', 'Computer Programmerist', '000-0000-1', NULL),
(15, 'PATHFIT-2', 'Physical Damage', NULL, '002-0000-2'),
(16, 'ISBA-102', 'Business Nambato', '000-0000-0', NULL),
(17, 'ISPC-114', 'Capstone Project 2', '221-0422-1', NULL),
(18, 'GECC-105', 'Mathematics', '000-0000-2', NULL),
(19, 'ISBA-105', 'Analytics Application', '000-0000-4', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'superadmin',
  `status` varchar(11) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `role`, `status`) VALUES
('221-0387-1', 'clark joshua', 'velasco', 'rojas', 'clarkjoshuavelasco.rojas@student.dmmmsu.edu.ph', '12345678', 'superadmin', 'active'),
('221-0387-2', 'Clak', 'Juswa', 'Rujas', 'superadmin@email.com', '12345678', 'superadmin', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `position` (`position`);

--
-- Indexes for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_evaluator` (`evaluator_id`),
  ADD KEY `fk_faculty_evaluatee` (`evaluatee_id`),
  ADD KEY `fk_evaluator_position` (`evaluator_position`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_eval` (`student_id`,`subject_code`,`academic_year`,`semester`),
  ADD KEY `subject_code_key` (`subject_code`),
  ADD KEY `faculty_id_key` (`faculty_id`),
  ADD KEY `subject_title` (`subject_title`),
  ADD KEY `department_key` (`department`),
  ADD KEY `evaluation_student_section` (`student_section`);

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
  ADD KEY `department` (`department`);

--
-- Indexes for table `faculty_peer_evaluation`
--
ALTER TABLE `faculty_peer_evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_ranks`
--
ALTER TABLE `faculty_ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `department` (`department`),
  ADD KEY `section` (`section`);

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
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
-- AUTO_INCREMENT for table `faculty_ranks`
--
ALTER TABLE `faculty_ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `department_key` FOREIGN KEY (`department`) REFERENCES `faculty` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluation_student_section` FOREIGN KEY (`student_section`) REFERENCES `student` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_id_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_code_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_title_key` FOREIGN KEY (`subject_title`) REFERENCES `subject` (`title`) ON DELETE CASCADE ON UPDATE CASCADE;

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
