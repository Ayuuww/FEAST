-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 09:39 AM
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
  `role` varchar(255) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `role`) VALUES
('000-0000-0', 'clark', 'juswa', 'rojas', 'clarkjoshua@email.com', '12345678', 'CIS', 'admin');

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
  `semester` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `student_id`, `department`, `subject_code`, `subject_title`, `academic_year`, `faculty_id`, `total_score`, `computed_rating`, `comment`, `created_at`, `semester`) VALUES
(44, '123-4567-8', 'CIS', 'PATHFIT-1', 'Physical Damage 1', '2024-2025', '000-0000-0', 73.00, 97.33, '', '2025-07-03 14:31:23', ''),
(45, '123-4567-8', 'CIS', 'PATHFIT-1', 'Physical Damage 1', '2025-2026', '000-0000-0', 73.00, 97.33, '', '2025-07-03 14:36:29', ''),
(46, '123-4567-8', 'CIS', 'ISPC-101', 'Computer Pasakit', '2025-2026', '000-0000-1', 74.00, 98.67, '', '2025-07-03 14:43:02', ''),
(47, '111-2222-3', 'CIS', 'ISPC-101', 'Computer Pasakit', '2025-2026', '000-0000-1', 74.00, 98.67, '', '2025-07-03 14:58:32', ''),
(48, '123-4567-8', 'CIS', 'ISPC-101', 'Computer Pasakit', '2023-2024', '000-0000-1', 75.00, 100.00, '', '2025-07-04 06:52:51', '1st Semester');

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
('000-0000-0', 'Clark Joshua', 'Rojas', 'Faculty', 'faculty@email.com', '12345678', 'CIS', '', 'faculty'),
('000-0000-1', 'Maricel', 'O', 'Pre', 'email@email.com', '12345678', 'CIS', '', 'faculty'),
('000-0000-2', 'Faculty', 'Sample', 'Value', 'samplefaculty@email.com', 'ILOVEDMMMSU', 'CIS', 'Assistant Professor I', 'faculty');

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

--
-- Dumping data for table `faculty_peer_evaluation`
--

INSERT INTO `faculty_peer_evaluation` (`id`, `evaluator_id`, `evaluated_faculty_id`, `school_year`, `semester`, `rating`, `comment`, `created_at`) VALUES
(2, '111-1111-1', '010-0120-1', '2025-2026', '1st Semester', 5.0, '', '2025-07-01 15:44:35'),
(3, '111-1111-1', '010-0120-1', '2023-2024', '1st Semester', 4.7, '', '2025-07-01 15:47:34');

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
('111-1111-1', 'Student', 'Sample', 'Value', 'sample@email.com', 'ILOVEDMMMSU', 'CVM', '1-B', 'student'),
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
  `faculty_id` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`idnumber`, `student_id`, `subject_code`, `faculty_id`) VALUES
(11, '123-4567-8', 'PATHFIT-1', '000-0000-0'),
(12, '123-4567-8', 'ISPC-101', '000-0000-1'),
(13, '111-2222-3', 'ISPC-101', '000-0000-1');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `idnumber` int(11) NOT NULL,
  `code` varchar(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `faculty_id` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`) VALUES
(9, 'PATHFIT-1', 'Physical Damage 1', '000-0000-0'),
(10, 'ISPC-101', 'Computer Pasakit', '000-0000-1');

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
  ADD PRIMARY KEY (`idnumber`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_eval` (`student_id`,`subject_code`,`academic_year`,`semester`),
  ADD KEY `subject_code_key` (`subject_code`),
  ADD KEY `faculty_id_key` (`faculty_id`),
  ADD KEY `subject_title` (`subject_title`),
  ADD KEY `department_key` (`department`);

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
  ADD KEY `department` (`department`);

--
-- Indexes for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `student_key` (`student_id`),
  ADD KEY `subject_key` (`subject_code`),
  ADD KEY `faculty_student_subject` (`faculty_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `faculty_key` (`faculty_id`),
  ADD KEY `code` (`code`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`idnumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `faculty_peer_evaluation`
--
ALTER TABLE `faculty_peer_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `department_key` FOREIGN KEY (`department`) REFERENCES `faculty` (`department`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faculty_id_key` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_id_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_code_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_title_key` FOREIGN KEY (`subject_title`) REFERENCES `subject` (`title`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD CONSTRAINT `faculty_student_subject` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_faculty_id` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
