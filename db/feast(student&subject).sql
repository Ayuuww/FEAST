-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 06:40 PM
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
('000-0000-0', 'clark', 'juswa', 'rojas', 'clarkjoshua@email.com', '12345678', 'cis', 'admin'),
('000-0000-1', 'clark', 'juswa', 'rojas', 'clarkjoshua85@gmail.com', '12345678', 'cis', 'admin'),
('000-0000-3', 'clark', 'juswa', 'rojas', 'asdf@gmailc.com', '12345678', 'cas', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `idnumber` varchar(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`idnumber`, `first_name`, `mid_name`, `last_name`, `email`, `password`, `department`, `role`, `status`) VALUES
('000-0000-0', 'faculty2', 'faculty 2', 'faculty 2', 'faculty2@email.com', '12345678', 'cis', 'faculty', 'approved'),
('0927-4492-1', 'clark', 'juswa', 'rojas', 'asdf@gmailc.com', '12345678', 'cis', 'student', 'pending'),
('098-7654-3', 'faculty', 'faculty', 'faculty', 'faculty@email.com', '12345678', 'cis', 'faculty', 'approved'),
('111-1111-1', 'sample', 'sample', 'sample', 'sample@email.com', '12345678', 'cas', 'student', 'disapproved'),
('123-4567-8', 'clark', 'joshua', 'rojas', 'clark@email.com', '12345678', 'cis', 'student', 'approved'),
('1234', '1234', '1234', '1234', '1234@email.com', '12345678', 'cas', 'student', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `student_subject`
--

CREATE TABLE `student_subject` (
  `idnumber` int(11) NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `subject_code` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subject`
--

INSERT INTO `student_subject` (`idnumber`, `student_id`, `subject_code`) VALUES
(4, '0927-4492-1', 'ISPC-101'),
(5, '111-1111-1', 'ISPC-101');

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
(1, 'ISPC-101', 'Computer Programming', '000-0000-0'),
(2, 'ISPC-101', 'Computer Programming', '098-7654-3');

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
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`idnumber`);

--
-- Indexes for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `student_key` (`student_id`),
  ADD KEY `subject_key` (`subject_code`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `faculty_key` (`faculty_id`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`idnumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_subject`
--
ALTER TABLE `student_subject`
  ADD CONSTRAINT `student_key` FOREIGN KEY (`student_id`) REFERENCES `register` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `faculty_key` FOREIGN KEY (`faculty_id`) REFERENCES `register` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
