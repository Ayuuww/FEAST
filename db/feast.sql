-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 02:38 PM
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
(408, '221-0387-1', 'superadmin', 'Logged in', '2025-10-21 22:51:59'),
(409, '221-0387-1', 'superadmin', 'Logged in', '2025-10-22 23:29:21'),
(410, '221-0387-1', 'superadmin', 'Logged in', '2025-10-26 13:50:05'),
(411, '00012', 'faculty', 'Logged in', '2025-10-26 13:51:01'),
(412, '00001', 'admin', 'Logged in', '2025-10-27 17:58:20'),
(413, '221-0387-1', 'superadmin', 'Logged in', '2025-10-27 17:58:49'),
(414, '10001', 'admin', 'Logged in', '2025-10-27 18:15:18'),
(415, '221-0387-1', 'superadmin', 'Logged in', '2025-10-27 18:16:18'),
(416, '123', 'admin', 'Logged in', '2025-10-27 18:17:26'),
(417, '221-0387-1', 'superadmin', 'Logged in', '2025-10-27 18:22:58'),
(418, '10001', 'admin', 'Logged in', '2025-10-27 18:24:51'),
(419, '221-0387-1', 'superadmin', 'Logged in', '2025-10-27 18:28:45'),
(420, '100-0000-0', 'admin', 'Logged in', '2025-10-27 18:29:03'),
(421, '001-0000-0', 'faculty', 'Logged in', '2025-10-27 18:30:33'),
(422, '000-0000-1', 'student', 'Logged in', '2025-10-27 18:31:35'),
(423, '10001', 'admin', 'Logged in', '2025-10-27 18:32:41'),
(424, '100-0000-0', 'admin', 'Logged in', '2025-10-27 19:52:30'),
(425, '100-0000-0', 'admin', 'Evaluated Faculty: Rufo A. Baro for 2025-2026 1st Semester', '2025-10-27 19:53:40'),
(426, '00001', 'admin', 'Logged in', '2025-10-27 20:00:08'),
(427, '00121', 'admin', 'Logged in', '2025-10-27 20:03:11'),
(428, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-10-27 20:59:02'),
(429, '221-0387-1', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-10-27 20:59:09'),
(430, '12345', 'admin', 'Logged in', '2025-10-27 21:04:36'),
(431, '221-0387-1', 'superadmin', 'Logged in', '2025-10-29 01:02:03'),
(432, '221-0387-1', 'superadmin', 'Logged in', '2025-11-02 09:34:51'),
(433, '123', 'admin', 'Logged in', '2025-11-02 10:59:09'),
(434, '221-0387-1', 'superadmin', 'Logged in', '2025-11-02 11:16:03'),
(435, '221-0387-1', 'superadmin', 'Evaluation turned on', '2025-11-02 14:44:40'),
(436, '41953', 'superadmin', 'Logged in', '2025-11-02 14:47:16'),
(437, '00021', 'admin', 'Logged in', '2025-11-02 14:48:52'),
(438, '41953', 'superadmin', 'Logged in', '2025-11-02 15:02:07'),
(439, '41953', 'superadmin', 'Logged in', '2025-11-02 16:39:02'),
(440, '00021', 'admin', 'Logged in', '2025-11-02 17:04:23'),
(441, '41953', 'superadmin', 'Logged in', '2025-11-02 17:11:48'),
(442, '00102', 'admin', 'Logged in', '2025-11-02 17:13:18'),
(443, '00021', 'admin', 'Logged in', '2025-11-02 17:14:44'),
(444, '00021', 'admin', 'Evaluated Faculty: Frediz Winda F. Badua for 2025-2026 1st Semester', '2025-11-02 17:15:27'),
(445, '00021', 'admin', 'Evaluated Faculty: Rhoda M. Lilan for 2025-2026 1st Semester', '2025-11-02 17:18:17'),
(446, '00021', 'admin', 'Evaluated Faculty: Frediz Winda F. Badua for 2025-2026 1st Semester', '2025-11-02 17:19:06'),
(447, '00021', 'admin', 'Evaluated Faculty: Rhoda M. Lilan for 2025-2026 1st Semester', '2025-11-02 17:24:41'),
(448, '00021', 'admin', 'Evaluated Faculty: Frediz Winda F. Badua for 2025-2026 1st Semester', '2025-11-02 17:26:40'),
(449, '41953', 'superadmin', 'Logged in', '2025-11-02 17:45:34'),
(450, '00021', 'admin', 'Logged in', '2025-11-02 17:52:08'),
(451, '00102', 'admin', 'Logged in', '2025-11-02 17:53:09'),
(452, '41953', 'superadmin', 'Logged in', '2025-11-02 18:48:50'),
(453, '00102', 'admin', 'Logged in', '2025-11-02 19:58:39'),
(454, '221-0387-11', 'student', 'Logged in', '2025-11-02 19:59:49'),
(455, '221-0387-11', 'student', 'Rated 94.67% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 20:00:12'),
(456, '221-0387-11', 'student', 'Rated 97.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 20:46:31'),
(457, '221-0387-11', 'student', 'Rated 92% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 20:55:40'),
(458, '221-0387-11', 'student', 'Rated 92% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 20:57:03'),
(459, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 20:57:57'),
(460, '221-0387-11', 'student', 'Rated 92% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:08:59'),
(461, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:16:33'),
(462, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:23:03'),
(463, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:30:16'),
(464, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) DEFAULT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adds`
--

INSERT INTO `adds` (`id`, `rank_name`, `position_name`, `section_name`, `department_name`, `program_name`) VALUES
(116, NULL, NULL, NULL, 'COLLEGE OF INFORMATION SYSTEMS', NULL),
(117, NULL, NULL, NULL, 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(118, 'Instructor II', NULL, NULL, NULL, NULL),
(119, NULL, 'Program Chair', NULL, NULL, NULL),
(120, NULL, NULL, '1-A', NULL, NULL),
(121, NULL, 'Staff', NULL, NULL, NULL),
(122, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', NULL),
(123, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'Bachelor of Secondary Education'),
(124, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'Bachelor of Early Childhood Education'),
(125, NULL, 'Head Instruction', NULL, NULL, NULL),
(126, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCE', NULL),
(127, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language'),
(128, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Science in Biology'),
(129, NULL, 'Dean', NULL, NULL, NULL),
(130, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', NULL),
(131, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Bachelor of Science in Agricultural and Biosystems Engineering');

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
  `faculty_rank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `position`, `role`, `status`, `faculty_rank`) VALUES
('00021', 'Edelita', 'C', 'Ebuenga', '$2y$10$boPOZg4h9iqY3DeSUj0H9OUNZ/8tDkC8YwfVaeffrojKNvTxDP4/u', 'Dean', 'admin', 'active', 'Instructor II'),
('00102', 'Sample', 'Admin', 'Yes', '$2y$10$8Fzq/J9BTdYvEy4ZNhHSsedukVB24BwKlC3BTRWAZe2UxTSUP00Mu', 'Program Chair', 'admin', 'active', 'Instructor II');

-- --------------------------------------------------------

--
-- Table structure for table `admin_departments`
--

CREATE TABLE `admin_departments` (
  `admin_idnumber` varchar(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_departments`
--

INSERT INTO `admin_departments` (`admin_idnumber`, `department_name`, `program_name`) VALUES
('00021', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
('00102', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language');

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
(80, '00021', '41953', 'Dean', '2025-2026', '1st Semester', 70, 93.33, '', 'COLLEGE OF ARTS AND SCIENCE', '2025-11-02 17:26:40');

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
(56, '00021', '41953', '1st Semester', '2025-2026', 70, 93.33, '', '2025-11-02 17:26:40', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":5,\"q13\":4,\"q14\":5}');

-- --------------------------------------------------------

--
-- Table structure for table `department_info`
--

CREATE TABLE `department_info` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT 'www.dmmmsu.edu.ph',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_info`
--

INSERT INTO `department_info` (`id`, `department_name`, `program_name`, `website`, `phone`, `email`) VALUES
(5, 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'www.dmmmsu.edu.ph', '', ''),
(6, 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '', '', ''),
(7, 'COLLEGE OF EDUCATION', 'Bachelor of Early Childhood Education', '', '', ''),
(8, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Bachelor of Science in Agricultural and Biosystems Engineering', '', '', '');

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
  `student_section` varchar(11) NOT NULL,
  `is_anonymous` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation`
--

INSERT INTO `evaluation` (`id`, `student_id`, `department`, `subject_code`, `subject_title`, `academic_year`, `faculty_id`, `total_score`, `computed_rating`, `comment`, `created_at`, `semester`, `student_section`, `is_anonymous`) VALUES
(146, '221-0387-11', 'COLLEGE OF ARTS AND SCIENCE', 'ELCO102', 'IT Era', '2025-2026', '41953', 70.00, 93.33, 'rers', '2025-11-02 13:32:08', '1st Semester', '1-A', 'yes');

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
(1, '1st Semester', '2025-2026', '2025-10-27 12:59:09');

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
(5, 'on', '221-0387-1');

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
  `program` varchar(255) DEFAULT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `program`, `faculty_rank`, `role`, `status`) VALUES
('00021', 'Edelita', 'C', 'Ebuenga', '$2y$10$boPOZg4h9iqY3DeSUj0H9OUNZ/8tDkC8YwfVaeffrojKNvTxDP4/u', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor II', 'faculty', 'active'),
('00102', 'Sample', 'Admin', 'Yes', '$2y$10$8Fzq/J9BTdYvEy4ZNhHSsedukVB24BwKlC3BTRWAZe2UxTSUP00Mu', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'Instructor II', 'faculty', 'active'),
('00191', 'Desiry Mitch', 'D', 'Ocampo', '$2y$10$Cr1LIw6bXl1HGL/qneceZu0B.7LYPbOe6hvPsUthLWA8FI.4uhE2G', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'Instructor II', 'faculty', 'active'),
('00919', 'Sample', 'Faculty', 'Side', '$2y$10$Q6jc.hAnKAqb4FzrsMayxuVpOzaaRjX0g5J8YRsVOu58CBPWGsoJq', 'COLLEGE OF EDUCATION', 'Bachelor of Early Childhood Education', 'Instructor II', 'faculty', 'active'),
('10231', 'Jose', 'P', 'Rizal', '$2y$10$/ktgmN78IP1NBfo7nT599ePJ8v0DmRYDuhAMKgXYKzDl40gbucq1.', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Science in Biology', 'Instructor II', 'faculty', 'active'),
('33113', 'Yes', 'No', 'MAYBE', '$2y$10$ZgMDbPRQcY68B2HnHw/AQePujgOZt3kiGm1dl1LvjtK3hiQ9jT.My', 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Bachelor of Science in Agricultural and Biosystems Engineering', 'Instructor II', 'faculty', 'active'),
('40207', 'Rhoda', 'M', 'Lilan', '$2y$10$LbejkMujq4jfIPZnT60KEOsUTHXh/rACORRR28dRtFbFsGXv0uPVC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor II', 'faculty', 'active'),
('41953', 'Frediz Winda', 'Ferrer', 'Badua', '$2y$10$Aa0rYYNDp8B8f8gkNgzXTu.TinSbLy0YQRqgSecD/F7CK/SGZLFsO', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'Instructor II', 'faculty', 'active');

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
  `program` varchar(255) DEFAULT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `department`, `program`, `section`, `role`) VALUES
('221-0387-11', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$gPu55xku85X.9YbtfpjVse2UMcVMw7kotA3AdcKhJy6LQwOh9hFyq', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student');

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
  `comment` text DEFAULT NULL,
  `is_anonymous` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_evaluation_submissions`
--

INSERT INTO `student_evaluation_submissions` (`id`, `student_id`, `subject_code`, `faculty_id`, `department`, `academic_year`, `semester`, `created_at`, `answers`, `total_score`, `computed_rating`, `comment`, `is_anonymous`) VALUES
(68, '221-0387-11', 'ELCO102', '41953', 'COLLEGE OF ARTS AND SCIENCE', '2025-2026', '1st Semester', '2025-11-02 21:32:08', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":4}', 70, 93.33, 'rers', 'yes');

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
(191, '221-0387-11', 'ELCO102', '2025-2026', '1st Semester', '41953', '00102');

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
  `department` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`, `admin_id`, `department`, `program`) VALUES
(59, 'ISAE107', 'Professional Engagements', '40207', '00021', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(60, 'PAHTFIT1', 'Physical Education', '40207', '00021', 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language'),
(61, 'ELCO102', 'IT Era', '41953', '00102', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems');

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
  `program` varchar(255) DEFAULT NULL,
  `faculty_rank` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `status` varchar(11) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `role`, `department`, `program`, `faculty_rank`, `position`, `status`) VALUES
('221-0387-1', 'Clark Joshua', 'Velasco', 'Rojas', '$2y$10$NvnGGTMLAypTJ4d1jA4Gzuy1lmz8zx5c6Hyy2b90rOn1MvHmUXmGS', 'superadmin', NULL, NULL, NULL, 'Staff', 'active'),
('41953', 'Frediz Winda', 'Ferrer', 'Badua', '$2y$10$USXT8cvNM3r8ivD6QErw9e/S0yiERn0k3qUTw.82KMzfo8AAGBqKq', 'superadmin', NULL, NULL, 'Instructor II', 'Head Instruction', 'active');

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
  ADD PRIMARY KEY (`admin_idnumber`,`department_name`,`program_name`),
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
-- Indexes for table `department_info`
--
ALTER TABLE `department_info`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=465;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `department_info`
--
ALTER TABLE `department_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

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
  ADD CONSTRAINT `admin_departments_ibfk_2` FOREIGN KEY (`department_name`) REFERENCES `adds` (`department_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_idnumber`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE;

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
