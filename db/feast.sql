-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 03:36 PM
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
  `role` varchar(255) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `position`, `faculty`, `role`) VALUES
('000-0000-0', 'Ma\'amEdith', 'Admin', 'Faculty', 'sampleadmin@email.com', 'ILOVEDMMMSU', 'CIS', 'Dean', 'yes', 'admin'),
('001-0000-1', 'Sample', 'Admin', '!Faculty', 'sampleadmin2@email.com', 'ILOVEDMMMSU', 'CAS', 'Campus-Administrator', 'no', 'admin'),
('002-0000-2', 'Admin', 'Sample', 'Faculty', 'sampleadmin3@email.com', 'ILOVEDMMMSU', 'CAS', 'Dean', 'yes', 'admin'),
('003-0000-3', 'AdminAs', 'Faculty', 'Sample', 'sampleadmin4@emial.com', 'ILOVEDMMMSU', 'CVM', 'Dean', 'yes', 'admin'),
('004-0000-4', 'Admin', 'As', 'Faculty', 'adminasfaculty@email.com', 'ILOVEDMMMSU', 'CAFF', 'Dean', 'yes', 'admin');

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
(13, '000-0000-0', '000-0000-1', 'Dean', '2025-2026', '1st Semester', 73, 97.33, 'very good teacher', 'CIS', '2025-07-08 20:44:00');

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
(55, '111-1111-1', 'CIS', 'ISBA-101', 'Business Again', '2025-2026', '000-0000-0', 75.00, 100.00, '', '2025-07-05 12:26:31', '1st Semester', '4-B'),
(56, '111-1111-1', 'CIS', 'ISBA-101', 'Business Again', '2023-2024', '000-0000-0', 42.00, 56.00, '', '2025-07-07 13:45:47', '1st Semester', '4-B'),
(57, '123-4567-8', 'CIS', 'ISBA-101', 'Business Again', '2025-2026', '000-0000-0', 59.00, 78.67, '', '2025-07-08 12:33:55', '1st Semester', '1-A'),
(61, '111-1111-1', 'CIS', 'ISPC-101', 'Computer Programmerist', '2025-2026', '000-0000-1', 61.00, 81.33, '', '2025-07-08 12:42:58', '1st Semester', '4-B');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `faculty_rank` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `faculty_rank`, `role`) VALUES
('000-0000-0', 'MaamEdith', 'Admin', 'Faculty', '', '', 'CIS', 'Dean', 'faculty'),
('000-0000-1', 'Maricel', 'Omagad', 'Pre', 'email@email.com', 'ILOVEDMMMSU', 'CIS', 'Assistant Professor I', 'faculty'),
('000-0000-2', 'Faculty', 'Sample', 'Value', 'samplefaculty@email.com', 'ILOVEDMMMSU', 'CVM', 'Assistant Professor I', 'faculty'),
('000-0000-3', 'First', 'Middle', 'Last', 'samplefaculty2@email.com', 'ILOVEDMMMSU', 'CIS', 'Assistant Professor I', 'faculty'),
('002-0000-2', 'Admin', 'Sample', 'Faculty', '', '', 'CAS', '', 'faculty'),
('003-0000-3', 'AdminAs', 'Faculty', 'Sample', '', '', 'CVM', '', 'faculty'),
('004-0000-4', 'Admin', 'As', 'Faculty', '', '', 'CAFF', 'Dean', 'faculty');

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
('111-1111-1', 'Student', 'Sample', 'Value', 'sample@email.com', 'ILOVEDMMMSU', 'CVM', '4-B', 'student'),
('111-2222-3', 'Kulark', 'Juswa', 'Rujas', 'email@email.com', '12345678', 'CAS', '1-B', 'student'),
('123-4567-8', 'Clark Joshua', 'Velasco', 'Rojas', 'email@email.com', '12345678', 'CIS', '1-A', 'student');

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
(20, '111-1111-1', 'ISPC-101', '000-0000-1', NULL);

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
(16, 'ISBA-102', 'Business Nambato', '000-0000-0', NULL);

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
  `role` varchar(255) NOT NULL DEFAULT 'superadmin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `role`) VALUES
('221-0387-1', 'clark joshua', 'velasco', 'rojas', 'clarkjoshuavelasco.rojas@student.dmmmsu.edu.ph', '12345678', 'superadmin');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `faculty_peer_evaluation`
--
ALTER TABLE `faculty_peer_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
