-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 06:30 PM
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
(464, '221-0387-11', 'student', 'Rated 93.33% for ELCO102 handled by Frediz Winda Ferrer Badua', '2025-11-02 21:32:08'),
(465, '221-0387-11', 'student', 'Logged in', '2025-11-03 17:16:54'),
(466, '41953', 'superadmin', 'Logged in', '2025-11-03 17:17:42'),
(467, '41953', 'superadmin', 'Logged in', '2025-11-03 19:49:44'),
(468, '221-0387-1', 'superadmin', 'Logged in', '2025-11-03 19:56:50'),
(469, '221-0387-1', '', 'Logged in', '2025-11-03 19:58:07'),
(470, '221-0387-1', '', 'Logged in', '2025-11-03 19:58:42'),
(471, '41953', 'superadmin', 'Logged in', '2025-11-03 20:08:31'),
(472, '221-0387-1', '', 'Logged in', '2025-11-03 20:08:50'),
(473, '41953', 'superadmin', 'Logged in', '2025-11-03 20:25:22'),
(474, '221-0387-1', '', 'Logged in', '2025-11-03 20:25:45'),
(475, '41953', 'superadmin', 'Logged in', '2025-11-03 20:51:32'),
(476, '00021', 'admin', 'Logged in', '2025-11-03 23:27:02'),
(477, '41953', 'superadmin', 'Logged in', '2025-11-04 18:15:01'),
(478, '00021', 'admin', 'Logged in', '2025-11-04 18:27:25'),
(479, '221-0387-1', '', 'Logged in', '2025-11-04 18:41:03'),
(480, '00021', 'admin', 'Logged in', '2025-11-04 19:24:23'),
(481, '41953', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-11-04 20:02:57'),
(482, '41953', 'superadmin', 'Evaluation turned on', '2025-11-04 20:03:05'),
(483, '221-0387-1', '', 'Logged in', '2025-11-04 20:04:20'),
(484, '221-0387-2', 'faculty', 'Logged in', '2025-11-04 20:14:24'),
(485, '221-0387-1', '', 'Logged in', '2025-11-04 20:41:20'),
(486, '221-0387-2', '', 'Logged in', '2025-11-04 20:41:48'),
(487, '10231', 'faculty', 'Logged in', '2025-11-04 21:31:33'),
(488, '00021', 'admin', 'Logged in', '2025-11-04 21:33:27'),
(489, '221-0387-2', '', 'Logged in', '2025-11-04 22:01:06'),
(490, '221-0387-2', '', 'Logged in', '2025-11-05 18:42:12'),
(491, '00000', '', 'Logged in', '2025-11-05 21:22:17'),
(492, '40151', 'admin', 'Logged in', '2025-11-05 21:23:04'),
(493, '40193', 'superadmin', 'Logged in', '2025-11-05 21:25:59'),
(494, '40193', 'superadmin', 'Evaluation turned on', '2025-11-05 21:26:05'),
(495, '40005', 'admin', 'Logged in', '2025-11-05 21:28:15'),
(496, '40045', 'admin', 'Logged in', '2025-11-05 21:36:27'),
(497, '40050', 'admin', 'Logged in', '2025-11-05 21:38:29'),
(498, '40005', 'admin', 'Logged in', '2025-11-05 21:49:45'),
(499, '40045', 'admin', 'Logged in', '2025-11-05 21:50:27'),
(500, '40151', 'admin', 'Logged in', '2025-11-05 21:51:09'),
(501, '221-0387-1', 'student', 'Logged in', '2025-11-05 21:54:27'),
(502, '40193', 'superadmin', 'Logged in', '2025-11-05 21:55:23'),
(503, '40193', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-11-05 21:55:27'),
(504, '40005', 'admin', 'Logged in', '2025-11-05 21:57:05'),
(505, '40045', 'admin', 'Logged in', '2025-11-05 21:58:23'),
(506, '40050', 'admin', 'Logged in', '2025-11-05 21:58:55'),
(507, '40151', 'admin', 'Logged in', '2025-11-05 21:59:23'),
(508, '221-0387-1', 'student', 'Rated 20% for ISPC 112 handled by Herve Estrada Orpilla', '2025-11-05 22:04:30'),
(509, '221-0387-1', 'student', 'Logged in', '2025-11-05 22:06:45'),
(510, '00000', '', 'Logged in', '2025-11-05 22:10:39'),
(511, '40005', 'admin', 'Logged in', '2025-11-05 22:13:27'),
(512, '40005', 'admin', 'Logged in', '2025-11-05 22:13:54'),
(513, '221-0387-1', 'student', 'Logged in', '2025-11-07 20:56:55'),
(514, '221-0387-1', 'student', 'Logged in', '2025-11-07 20:57:04'),
(515, '221-0387-1', 'student', 'Logged in', '2025-11-08 01:02:43'),
(516, '221-0387-1', 'student', 'Logged in', '2025-11-08 01:02:58'),
(517, '221-0387-1', 'student', 'Logged in', '2025-11-13 18:16:50'),
(518, '40193', 'superadmin', 'Logged in', '2025-11-13 18:51:24'),
(519, '40151', 'admin', 'Logged in', '2025-11-13 18:52:21'),
(520, '221-0387-1', 'student', 'Logged in', '2025-11-13 19:00:51'),
(521, '40193', 'superadmin', 'Logged in', '2025-11-13 20:19:55'),
(522, '221-0387-1', 'student', 'Logged in', '2025-11-13 23:00:59'),
(523, '40151', 'admin', 'Logged in', '2025-11-16 18:58:45'),
(524, '00000', '', 'Logged in', '2025-11-16 19:05:53'),
(525, '40193', 'superadmin', 'Logged in', '2025-11-16 19:09:48'),
(526, '221-0387-1', 'student', 'Logged in', '2025-11-16 19:12:40'),
(527, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 19:46:02'),
(528, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:46:04'),
(529, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:46:06'),
(530, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:46:15'),
(531, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 19:48:27'),
(532, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:48:36'),
(533, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:48:38'),
(534, '40193', 'superadmin', 'Evaluation turned off | Start:  | End: ', '2025-11-16 19:48:40'),
(535, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 19:58:38'),
(536, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 20:01:33'),
(537, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 20:02:36'),
(538, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 20:02:48'),
(539, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-17', '2025-11-16 20:06:11'),
(540, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:06:20'),
(541, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:11:03'),
(542, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:14:44'),
(543, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:20:58'),
(544, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:21:09'),
(545, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:21:14'),
(546, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-16 20:21:18'),
(547, '221-0388-1', 'student', 'Logged in', '2025-11-16 20:51:25'),
(548, '221-0387-1', 'student', 'Logged in', '2025-11-16 20:51:42'),
(549, '40151', 'admin', 'Logged in', '2025-11-16 20:51:54'),
(550, '221-0387-1', 'student', 'Logged in', '2025-11-17 18:10:26'),
(551, '40193', 'superadmin', 'Logged in', '2025-11-17 18:13:48'),
(552, '40193', 'superadmin', 'Evaluation turned off | Start: 2025-11-16 | End: 2025-11-18', '2025-11-17 18:13:54'),
(553, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-17 18:14:01'),
(554, '221-0387-1', 'student', 'Rated 89.33% for ISBA 105 handled by Rufo Agaloos Baro', '2025-11-17 18:16:58'),
(555, '40180', 'faculty', 'Logged in', '2025-11-17 18:20:24'),
(556, '40151', 'admin', 'Logged in', '2025-11-17 18:24:07'),
(557, '40151', 'admin', 'Evaluated Faculty: Larmie D. Barcelona for 2025-2026 1st Semester', '2025-11-17 18:37:01'),
(558, '40151', 'admin', 'Evaluated Faculty: Kristine Maylan S. Espero for 2025-2026 1st Semester', '2025-11-17 18:40:52'),
(559, '40151', 'admin', 'Evaluated Faculty: Reiner Jan A. Castelo for 2025-2026 1st Semester', '2025-11-17 18:43:19'),
(560, '40207', 'faculty', 'Logged in', '2025-11-17 18:56:14'),
(561, '40045', 'admin', 'Logged in', '2025-11-17 18:56:23'),
(562, '40151', 'admin', 'Logged in', '2025-11-17 18:57:02'),
(563, '40151', 'admin', 'Evaluated Faculty: Rufo A. Baro for 2025-2026 1st Semester', '2025-11-17 19:11:22'),
(564, '40193', 'superadmin', 'Logged in', '2025-11-17 19:20:06'),
(565, '00000', '', 'Logged in', '2025-11-17 19:27:46'),
(566, '40193', 'superadmin', 'Logged in', '2025-11-17 21:28:15'),
(567, '40151', 'admin', 'Logged in', '2025-11-17 21:36:38'),
(568, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-16 | End: 2025-11-18', '2025-11-17 22:02:27'),
(569, '00000', '', 'Logged in', '2025-11-17 22:06:46'),
(570, '00000', '', 'Logged in', '2025-11-17 22:46:26'),
(571, '40193', 'superadmin', 'Logged in', '2025-11-18 20:31:28'),
(572, '40193', 'superadmin', 'Logged in', '2025-11-19 18:24:00'),
(573, '40151', 'admin', 'Logged in', '2025-11-19 19:12:08'),
(574, '40151', 'admin', 'Logged in', '2025-11-20 14:49:55'),
(575, '40193', 'superadmin', 'Logged in', '2025-11-20 20:37:34'),
(576, '40151', 'admin', 'Logged in', '2025-11-20 21:10:26'),
(577, '40045', 'admin', 'Logged in', '2025-11-20 21:17:33'),
(578, '40151', 'admin', 'Logged in', '2025-11-21 14:42:14'),
(579, '40151', 'admin', 'Logged in', '2025-11-21 17:54:31'),
(580, '00000', '', 'Logged in', '2025-11-21 18:03:48'),
(581, '00000', '', 'Logged in', '2025-11-22 22:30:28'),
(582, '40151', 'admin', 'Logged in', '2025-11-23 00:11:01'),
(583, '00000', '', 'Logged in', '2025-11-23 00:11:55'),
(584, '221-0388-1', 'student', 'Logged in', '2025-11-25 19:38:33'),
(585, '40151', 'admin', 'Logged in', '2025-11-25 19:46:37'),
(586, '40193', 'superadmin', 'Logged in', '2025-11-25 20:32:55'),
(587, '40193', 'superadmin', 'Logged in', '2025-11-25 20:52:44'),
(588, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2025-11-25 20:53:25'),
(589, '221-0387-1', 'student', 'Logged in', '2025-11-25 20:53:43'),
(590, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-25 | End: 2025-11-28', '2025-11-25 20:54:05'),
(591, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-04 | End: 2025-11-06', '2025-11-25 20:54:16'),
(592, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2025-11-25 20:54:16'),
(593, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-25 | End: 2025-11-28', '2025-11-25 20:54:26'),
(594, '40151', 'admin', 'Logged in', '2025-11-25 20:54:58'),
(595, '00000', '', 'Logged in', '2025-11-26 18:39:35'),
(596, '00001', 'admin', 'Logged in', '2025-11-26 18:41:03'),
(597, '40045', 'admin', 'Logged in', '2025-11-26 18:41:34'),
(598, '40050', 'admin', 'Logged in', '2025-11-26 18:51:08'),
(599, '00001', 'admin', 'Logged in', '2025-11-26 19:03:35'),
(600, '40045', 'admin', 'Logged in', '2025-11-26 19:03:59'),
(601, '40193', 'superadmin', 'Logged in', '2025-11-26 19:04:20'),
(602, '40045', 'admin', 'Logged in', '2025-11-26 19:07:00'),
(603, '40193', 'superadmin', 'Logged in', '2025-11-26 19:41:55'),
(604, '00000', '', 'Logged in', '2025-11-27 19:39:11'),
(605, '40151', 'admin', 'Logged in', '2025-11-27 20:12:31'),
(606, '00001', 'admin', 'Logged in', '2025-11-27 20:12:45'),
(607, '40045', 'admin', 'Logged in', '2025-11-27 20:13:41'),
(608, '00001', 'admin', 'Logged in', '2025-11-27 20:32:29'),
(609, '40005', 'admin', 'Logged in', '2025-11-27 20:33:23'),
(610, '00001', 'admin', 'Logged in', '2025-11-27 20:43:17'),
(611, '40045', 'admin', 'Logged in', '2025-11-27 20:46:33'),
(612, '40151', 'admin', 'Logged in', '2025-11-27 20:46:51'),
(613, '40045', 'admin', 'Logged in', '2025-11-27 20:57:37'),
(614, '00001', 'admin', 'Logged in', '2025-11-27 21:01:25'),
(615, '40045', 'admin', 'Logged in', '2025-11-27 21:07:56'),
(616, '00001', 'admin', 'Logged in', '2025-11-27 21:12:18'),
(617, '40151', 'admin', 'Logged in', '2025-11-27 21:26:13'),
(618, '40045', 'admin', 'Logged in', '2025-11-27 21:26:28'),
(619, '00001', 'admin', 'Logged in', '2025-11-27 21:31:08'),
(620, '40045', 'admin', 'Logged in', '2025-11-27 22:00:34'),
(621, '00001', 'admin', 'Logged in', '2025-11-27 22:01:27'),
(622, '40045', 'admin', 'Logged in', '2025-11-27 22:03:28'),
(623, '00001', 'admin', 'Logged in', '2025-11-27 22:10:24'),
(624, '40151', 'admin', 'Logged in', '2025-11-27 22:50:49'),
(625, '00001', 'admin', 'Logged in', '2025-11-27 23:07:32'),
(626, '40151', 'admin', 'Logged in', '2025-11-27 23:09:38'),
(627, '40151', 'admin', 'Logged in', '2025-11-27 23:28:45'),
(628, '00711', 'faculty', 'Logged in', '2025-11-28 00:42:32'),
(629, '40045', 'admin', 'Logged in', '2025-11-28 01:14:51'),
(630, '40151', 'admin', 'Logged in', '2025-11-28 01:22:52');

-- --------------------------------------------------------

--
-- Table structure for table `adds`
--

CREATE TABLE `adds` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(100) DEFAULT NULL,
  `position_name` varchar(100) DEFAULT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `college_name` varchar(255) DEFAULT NULL,
  `program_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adds`
--

INSERT INTO `adds` (`id`, `rank_name`, `position_name`, `section_name`, `college_name`, `program_name`) VALUES
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
(129, NULL, 'Dean', NULL, NULL, NULL),
(130, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', NULL),
(131, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Bachelor of Science in Agricultural and Biosystems Engineering'),
(132, 'Associate Professor IV', NULL, NULL, NULL, NULL),
(133, NULL, NULL, '4-B', NULL, NULL),
(134, NULL, NULL, '4-A', NULL, NULL),
(135, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', NULL),
(136, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Arts in English Language'),
(137, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Science in Biology'),
(138, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of General Education'),
(139, 'Instructor III', NULL, NULL, NULL, NULL),
(140, 'Assistant Professor II', NULL, NULL, NULL, NULL),
(141, 'Assistant Professor IV', NULL, NULL, NULL, NULL),
(142, 'Associate Professor V', NULL, NULL, NULL, NULL),
(143, 'Associate Professor II', NULL, NULL, NULL, NULL),
(144, 'Associate Professor III', NULL, NULL, NULL, NULL),
(145, 'Instructor I', NULL, NULL, NULL, NULL),
(146, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'Bachelor in Physical Education'),
(148, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'Bachelor of Elementary Education'),
(149, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'Bachelor of Technology and Livelihood Education'),
(150, NULL, NULL, NULL, 'COLLEGE OF VETERINARY MEDICINE', NULL),
(151, NULL, NULL, NULL, 'COLLEGE OF VETERINARY MEDICINE', 'Doctor of Veterinary Medicine'),
(152, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', NULL),
(153, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'Bachelor of Science in Forestry'),
(154, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'Bachelor of Science in Agroforestry'),
(155, NULL, NULL, NULL, 'COLLEGE OF AGRICULTURE', NULL),
(156, NULL, NULL, NULL, 'COLLEGE OF AGRICULTURE', 'Bachelor of Science in Agriculture'),
(157, NULL, NULL, NULL, 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', NULL),
(158, NULL, NULL, NULL, 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', 'Bachelor of Science in Agribusiness Management'),
(159, NULL, NULL, NULL, 'INSTITUTE OF ENVIRONMENTAL STUDIES', NULL),
(160, NULL, NULL, NULL, 'INSTITUTE OF ENVIRONMENTAL STUDIES', 'Bachelor of Science in Environmental Science'),
(161, NULL, 'Director', NULL, NULL, NULL),
(162, 'Assistant Professor III', NULL, NULL, NULL, NULL),
(163, 'Assistant Professor I', NULL, NULL, NULL, NULL),
(164, 'Associate Professor I', NULL, NULL, NULL, NULL),
(165, 'Professor I', NULL, NULL, NULL, NULL),
(166, 'Professor II', NULL, NULL, NULL, NULL),
(167, 'Professor III', NULL, NULL, NULL, NULL),
(168, 'Professor IV', NULL, NULL, NULL, NULL),
(169, 'Professor V', NULL, NULL, NULL, NULL);

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
('40005', 'Theresa', 'Crispino', 'Cachero', '$2y$10$7ddHbp89iZO1TUPNADI7YeeoXSXU37LtR2rpNL0ipjRuJ9dDGrFIC', 'Program Chair', 'admin', 'active', 'Assistant Professor IV'),
('40014', 'Nel Bryan', 'Cariño', 'Tugelida', '$2y$10$iRU7UNcH2guHlzFGjnhR3etFBcRaR6.f8p/jBHYatizFT8pHF4xFO', 'Dean', 'admin', 'active', 'Assistant Professor I'),
('40045', 'Christianne Glory', 'L', 'Arbollente', '$2y$10$AL3JqU.T22XvzvwruiOuTuIZGPLP1TOgB.oZOYgNWI79bTA8RF1Kq', 'Program Chair', 'admin', 'active', 'Instructor III'),
('40050', 'Lynbelle', 'Chan', 'Pascua', '$2y$10$zY3B9.cA1L3I/btGOpiEpuDfvsOPzS2OBT/LgINzG05GYucNc9X22', 'Program Chair', 'admin', 'active', 'Instructor I'),
('40151', 'Edelita', 'Corpuz', 'Ebuenga', '$2y$10$Ltz8JeCc3EJ1vVtTPbSE5.rh3X7Rb4EEO28tQ69OLePJi78EZ0wu.', 'Dean', 'admin', 'active', 'Associate Professor V');

-- --------------------------------------------------------

--
-- Table structure for table `admin_college`
--

CREATE TABLE `admin_college` (
  `admin_idnumber` varchar(11) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_college`
--

INSERT INTO `admin_college` (`admin_idnumber`, `college_name`, `program_name`) VALUES
('40005', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Science in Biology'),
('40014', 'COLLEGE OF EDUCATION', 'Bachelor in Physical Education'),
('40014', 'COLLEGE OF EDUCATION', 'Bachelor of Early Childhood Education'),
('40014', 'COLLEGE OF EDUCATION', 'Bachelor of Elementary Education'),
('40014', 'COLLEGE OF EDUCATION', 'Bachelor of Secondary Education'),
('40014', 'COLLEGE OF EDUCATION', 'Bachelor of Technology and Livelihood Education'),
('40045', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of General Education'),
('40050', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Arts in English Language'),
('40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems');

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
  `college` varchar(255) DEFAULT NULL,
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
-- Table structure for table `college_info`
--

CREATE TABLE `college_info` (
  `id` int(11) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT 'www.dmmmsu.edu.ph',
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_info`
--

INSERT INTO `college_info` (`id`, `college_name`, `program_name`, `website`, `phone`, `email`) VALUES
(5, 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'www.dmmmsu.edu.ph', '', ''),
(6, 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'www.dmmmsu.edu.ph', '', ''),
(7, 'COLLEGE OF EDUCATION', 'Bachelor of Early Childhood Education', '', '', ''),
(8, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Bachelor of Science in Agricultural and Biosystems Engineering', '', '', ''),
(9, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Science in Biology', 'www.dmmmsu.edu.ph', '', ''),
(10, 'COLLEGE OF ARTS AND SCIENCES', 'General Education', 'www.dmmmsu.edu.ph', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `college` varchar(255) NOT NULL,
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

INSERT INTO `evaluation` (`id`, `student_id`, `college`, `subject_code`, `subject_title`, `academic_year`, `faculty_id`, `total_score`, `computed_rating`, `comment`, `created_at`, `semester`, `student_section`, `is_anonymous`) VALUES
(149, '221-0325-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 75.00, 100.00, '', '2025-11-06 05:42:08', '1st Semester', '4-B', 'yes'),
(150, '221-0476-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 75.00, 100.00, 'none', '2025-11-06 05:42:27', '1st Semester', '4-B', 'yes'),
(151, '231-0884-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 45.00, 60.00, 'yes', '2025-11-06 05:43:52', '1st Semester', '4-B', 'yes'),
(152, '221-0422-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 67.00, 89.33, 'napaka galing mo', '2025-11-06 05:44:42', '1st Semester', '4-B', 'yes'),
(153, '211-0004-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 70.00, 93.33, '', '2025-11-06 05:44:49', '1st Semester', '4-B', 'yes'),
(154, '211-0004-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 70.00, 93.33, '', '2025-11-06 05:45:25', '1st Semester', '4-B', 'yes'),
(155, '221-0778-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 75.00, 100.00, '', '2025-11-06 05:45:46', '1st Semester', '4-A', 'yes'),
(156, '221-0368-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 75.00, 100.00, 'Wow', '2025-11-06 05:45:54', '1st Semester', '4-B', 'no'),
(157, '211-0161-1 ', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 74.00, 98.67, '', '2025-11-06 05:45:58', '1st Semester', '4-A', 'yes'),
(158, '221-0733-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 67.00, 89.33, 'N/A', '2025-11-06 05:46:00', '1st Semester', '4-A', 'no'),
(159, '221-0026-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 75.00, 100.00, '', '2025-11-06 05:46:00', '1st Semester', '4-A', 'yes'),
(160, '221-0070-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 70.00, 93.33, 'N/A', '2025-11-06 05:46:27', '1st Semester', '4-A', 'yes'),
(161, '202-0021-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 74.00, 98.67, '', '2025-11-06 05:46:53', '1st Semester', '4-A', 'no'),
(162, '221-0146-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISSM 105', 'Principles of Systems Thinking', '2025-2026', '40023', 60.00, 80.00, 'None', '2025-11-06 05:47:19', '1st Semester', '4-A', 'no'),
(163, '221-0387-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 112', 'IS Strategy Management and Acquisition', '2025-2026', '40184', 67.00, 89.33, 'nICE TEACHING', '2025-11-06 05:48:31', '1st Semester', '4-B', 'yes'),
(164, '221-0852-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 112', 'IS Strategy Management and Acquisition', '2025-2026', '40184', 65.00, 86.67, 'Good teaching ', '2025-11-06 05:50:14', '1st Semester', '4-A', 'yes'),
(165, '221-0867-1 ', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 112', 'IS Strategy Management and Acquisition', '2025-2026', '40184', 67.00, 89.33, 'none', '2025-11-06 05:50:55', '1st Semester', '4-B', 'yes'),
(166, '221-0867-1 ', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 107', 'Professional Engagements', '2025-2026', '40207', 65.00, 86.67, 'None\r\n', '2025-11-06 05:55:15', '1st Semester', '4-B', 'yes'),
(167, '221-0387-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISBA 105', 'Analytics Application', '2025-2026', '40180', 68.00, 90.67, 'Excellent Teaching', '2025-11-06 05:55:42', '1st Semester', '4-B', 'no'),
(168, '221-0478-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISPC 114', 'Capstone Project 2', '2025-2026', '40413', 63.00, 84.00, '', '2025-11-06 06:01:45', '1st Semester', '4-B', 'yes'),
(169, '251-0250-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, 'always take care po maam', '2025-11-06 06:18:10', '1st Semester', '1-A', 'no'),
(170, '251-0416-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 75.00, 100.00, 'Gwapo', '2025-11-06 06:23:19', '1st Semester', '1-A', 'yes'),
(171, '251-0057-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 75.00, 100.00, '', '2025-11-06 06:23:41', '1st Semester', '1-A', 'yes'),
(172, '251-0234-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, 'Very good in teaching', '2025-11-06 06:24:53', '1st Semester', '1-A', 'no'),
(173, '251-0269-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 102', 'Computer Programming 1', '2025-2026', '40094', 75.00, 100.00, 'N/A', '2025-11-06 06:27:41', '1st Semester', '1-A', 'yes'),
(174, '251-0004-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 74.00, 98.67, 'very joyful teacher', '2025-11-06 06:27:54', '1st Semester', '1-A', 'no'),
(175, '251-0009-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 102', 'Computer Programming 1', '2025-2026', '40094', 75.00, 100.00, 'Keep up the good work ma\'am<3', '2025-11-06 06:28:22', '1st Semester', '1-A', 'yes'),
(176, '251-0233-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, 'Very good ', '2025-11-06 06:28:26', '1st Semester', '1-A', 'yes'),
(177, '251-0008-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 102', 'Computer Programming 1', '2025-2026', '40094', 75.00, 100.00, '', '2025-11-06 06:28:32', '1st Semester', '1-A', 'yes'),
(178, '251-0019-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, '', '2025-11-06 06:28:44', '1st Semester', '1-A', 'yes'),
(179, '251-0458-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 102', 'Computer Programming 1', '2025-2026', '40094', 75.00, 100.00, 'N/A', '2025-11-06 06:28:57', '1st Semester', '1-A', 'yes'),
(180, '251-0383-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 74.00, 98.67, 'ma\'am keep up the good work ma\'am really appreciated ^ ^', '2025-11-06 06:29:29', '1st Semester', '1-A', 'no'),
(181, '251-0010-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 102', 'Computer Programming 1', '2025-2026', '40094', 75.00, 100.00, 'the best teacher and easy to approach ', '2025-11-06 06:29:44', '1st Semester', '1-A', 'yes'),
(182, '251-0458-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 75.00, 100.00, 'N/A', '2025-11-06 06:30:15', '1st Semester', '1-A', 'yes'),
(183, '251-0401-1', 'COLLEGE OF ARTS AND SCIENCES', 'GEEC 101', 'Environmental Science', '2025-2026', '0716', 75.00, 100.00, 'No comment ', '2025-11-06 06:31:04', '1st Semester', '1-A', 'yes'),
(184, '251-0482-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, 'Iloveyousomuch ma\'am ', '2025-11-06 06:32:09', '1st Semester', '1-A', 'yes'),
(185, '251-0010-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 75.00, 100.00, 'approachable teacher ', '2025-11-06 06:33:43', '1st Semester', '1-A', 'yes'),
(186, '251-0482-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 75.00, 100.00, '', '2025-11-06 06:34:17', '1st Semester', '1-A', 'no'),
(187, '251-0010-1', 'COLLEGE OF ARTS AND SCIENCES', 'GEEC 101', 'Environmental Science', '2025-2026', '0716', 73.00, 97.33, 'Approachable teacher', '2025-11-06 06:35:29', '1st Semester', '1-A', 'yes'),
(188, '251-0010-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 75.00, 100.00, 'approachable teacher ', '2025-11-06 06:38:25', '1st Semester', '1-A', 'yes'),
(189, '251-0369-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISCC 101', 'Introduction to Computing', '2025-2026', '00711', 60.00, 80.00, '', '2025-11-06 06:38:38', '1st Semester', '1-A', 'yes'),
(190, '251-0369-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 101', 'Arts Appreciation', '2025-2026', '00421', 73.00, 97.33, '', '2025-11-06 06:39:50', '1st Semester', '1-A', 'yes'),
(191, '251-0369-1', 'COLLEGE OF ARTS AND SCIENCES', 'GECC 103', 'Mathematics in the Modern World', '2025-2026', '02860', 66.00, 88.00, '', '2025-11-06 06:40:43', '1st Semester', '1-A', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_settings`
--

CREATE TABLE `evaluation_settings` (
  `id` int(11) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_settings`
--

INSERT INTO `evaluation_settings` (`id`, `semester`, `academic_year`, `status`, `updated_at`) VALUES
(1, '1st Semester', '2025-2026', 1, '2025-11-05 13:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_switch`
--

CREATE TABLE `evaluation_switch` (
  `id` int(11) NOT NULL,
  `status` enum('on','off') NOT NULL DEFAULT 'off',
  `user_id` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_switch`
--

INSERT INTO `evaluation_switch` (`id`, `status`, `user_id`, `start_date`, `end_date`) VALUES
(7, 'on', '40193', '2025-11-25', '2025-11-28');

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
  `college` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `faculty_rank` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'faculty',
  `status` varchar(11) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `faculty_rank`, `role`, `status`) VALUES
('00421', 'Kristine Maylan', 'Sabado', 'Espero', '$2y$10$6sDggnKta7b7xCdaP1Ib/eIe85tOjuo1lb3v7am4JtP4vZ9M1eG5W', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of General Education', 'Instructor I', 'faculty', 'active'),
('00711', 'Mark Kenneth', 'Molina', 'Mangaser', '$2y$10$STXSd0A4CyFoab/G/ADv7.MOKWUTUpQTz66kdgIfF.lcval9XV5b6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor I', 'faculty', 'active'),
('02860', 'Larmie', 'Dosono', 'Barcelona', '$2y$10$Z/Bqwy3zpmgkVX6A7uoo7eCyBX01V64mIOyjhRbxa/1FHyCkxUfve', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of General Education', 'Instructor I', 'faculty', 'active'),
('0716', 'Reiner Jan', 'Agustin', 'Castelo', '$2y$10$fRUv6hw/4/5ZkrIF8VvoKOMdomyPzx6YnjwyGYzeaqenfhqK9h7Fa', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Science in Biology', 'Instructor I', 'faculty', 'active'),
('40005', 'Theresa', 'Crispino', 'Cachero', NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Science in Biology', 'Assistant Professor IV', 'faculty', 'active'),
('40014', 'Nel Bryan', 'Cariño', 'Tugelida', '$2y$10$iRU7UNcH2guHlzFGjnhR3etFBcRaR6.f8p/jBHYatizFT8pHF4xFO', 'COLLEGE OF EDUCATION', 'Bachelor of Technology and Livelihood Education', 'Assistant Professor I', 'faculty', 'active'),
('40023', 'Shalimar', 'Licudine', 'Navalta', '$2y$10$hyxK8egZRYMGC1dzTm6.iu5YXk02AQBd4iBhfXKMZk34NDUFzWzoC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor V', 'faculty', 'active'),
('40045', 'Christianne Glory', 'L', 'Arbollente', NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of General Education', 'Instructor III', 'faculty', 'active'),
('40050', 'Lynbelle', 'Chan', 'Pascua', NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Arts in English Language', 'Instructor I', 'faculty', 'active'),
('40094', 'Maricel', 'Oficiar', 'Pre', '$2y$10$7Q4SgyxGufI6HtGAQ9Ssa..7t6bpDWmjcSvphzG6Cd3cfXWbchyPy', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor I', 'faculty', 'active'),
('40112', 'Jhonalyn', 'Bautista', 'Lardizabal', '$2y$10$HnDfJjH8lTY4stHShIsMeeNMZzoYWR6jnL8cP1n.BHPLu1fOczE0i', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor III', 'faculty', 'active'),
('40151', 'Edelita', 'Corpuz', 'Ebuenga', NULL, 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor V', 'faculty', 'active'),
('40180', 'Rufo', 'Agaloos', 'Baro', '$2y$10$awW24CgCqZVTGxqR6A6THOYA8AxTrg.pxKsOYIhWE8KOiR/4XEz.O', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor V', 'faculty', 'active'),
('40182', 'Daniel', 'Almojuela', 'Neri', '$2y$10$Lzvob3L7hGF1aqMPlcgeNuVg0HgBYFqbk.degfuS52jDPOV0gphNS', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor II', 'faculty', 'active'),
('40184', 'Herve', 'Estrada', 'Orpilla', '$2y$10$Vgm05e3hAx2417BIfAIS4uEpPTWzLOu8KTGMhSZs3W1QAcF.uiUtu', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor III', 'faculty', 'active'),
('40193', 'Frediz Winda', 'Ferrer', 'Badua', NULL, 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Arts in English Language', 'Assistant Professor IV', 'faculty', 'active'),
('40207', 'Rhoda', 'Marquez', 'Lilan', '$2y$10$Dh5Dz3iRIRcg7N.DGYHzou0x1NxTkdgZjq8X/PnqgcPMevcafpqNC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Associate Professor V', 'faculty', 'active'),
('40413', 'Jessie', 'Bautista', 'Vallecera', '$2y$10$JcHvquUKJ96KyfD0Ri8cXODOkZTAxfBWcI0uB86tsV.KBmmLiOqfS', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', 'Instructor III', 'faculty', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `registrar`
--

CREATE TABLE `registrar` (
  `idnumber` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `mid_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `college` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `role` varchar(50) NOT NULL DEFAULT 'registrar',
  `employment_role` enum('Teaching','Non-Teaching') NOT NULL DEFAULT 'Non-Teaching',
  `faculty_rank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrar`
--

INSERT INTO `registrar` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `status`, `role`, `employment_role`, `faculty_rank`) VALUES
('00000', 'Account', 'Creator', 'System', '$2y$10$oIAZEL68lgR8cOOc6YP5d.UA5GaQwnCLtrSP/7cv03CNmGt6fylRm', NULL, NULL, 'active', 'registrar', 'Non-Teaching', NULL);

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
  `college` varchar(255) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `section` varchar(11) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `section`, `role`) VALUES
('201-0326-1', 'Rodel Andre', 'Bumatay', 'Cardona', '$2y$10$.3J8f0HN56qVyud..gugsOsmAykyg2DfS7eBlHfeXM6HEkZB1wEQ2', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('202-0021-1', 'Ren Ren', 'Alimpia', 'Donato', '$2y$10$hx3jjm..uGUIVfq4PX0w6u7jKAi2cNkSgm/D2O9SvvYBAVgseOC.O', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('211-0004-1', 'Symphony Angel', 'Manalo', 'Vivit', '$2y$10$Zb2BkcFRnQEvUlsxZRKUEO6CRvNQheG0XKgl.iLr4sL6RbFAOtnui', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('211-0161-1 ', 'Herobin', 'MiddleName', 'Mariñas', '$2y$10$NW9ntaa01AYex8oGt5qCQuNsLLdldbq5lvdyBxY1sAaX4YB4KfW6u', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('211-0164-1', 'Emma Cecille', 'Ariola', 'Badua', '$2y$10$kixnr6W1iveVo3C1AVL9fuxeTvehTVPlcPbsyEerfU3kyr3S5svLa', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('211-0174-1', 'John Godwin', 'MiddleName', 'Taningco', '$2y$10$TyLgDbCNfofpYC6M8QOIa.v99.8.TT70QQv2fY3Lwf2rgEt/w0J1i', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('211-0805-1', 'Gohan', 'Espe', 'Gamboa', '$2y$10$BCJGB/C2AVsoe3rUVBU7r.cv6Gjt40eNBnq0R5K1C/XH097iOxUxy', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221- 0411-1', 'Clouie Ann', 'MiddleName', 'Aclinen', '$2y$10$PnJUVdK7rvXHoB.p1LQ8KeVMaVud5x3crYA/Jx3TsW305PCbSEvRO', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0005-1', 'Alexandrea ', 'Jean', 'Garcia', '$2y$10$SHZx36mhnnIZv5gTKSPNoOHrS/poBF/.i6e72Yl58TensgBHI6a0i', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0006-1', 'Renz Allen', 'Bautista', 'Miller', '$2y$10$Mr8YA1x/ZATqPLAEnexuuucKbVAscbj0I.PUM6dtO.oLYe.ru70cK', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0026-1', 'Jeandra Joy', 'Opinaldo', 'Orfiano', '$2y$10$Fo32ZIz4C71NNDETq3MjKuvhdTmouK0opssTw6LybchIEKVyoWROu', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0070-1', 'Honey Boy', 'Bucsit', 'Corial', '$2y$10$E7n0kwQRL38O.oXNMtLtTe9b/KRuFG2CSQBAETxHs43QTNzHfg6h6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0079-1', 'Procxel', 'Claro', 'Almoite', '$2y$10$U8jHoYQT6rj91xt6k866fO8zh2ms.OLvKjGsJECHCqWW9AthMqIpi', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0080-1', 'Ika Ivana', 'Buccat', 'Licudan', '$2y$10$ibS37rjJxS/j7TxicrjCeeXA75OsUci.xpIzqQFNjfWGyeyrVSfk2', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0082-1', 'Aerone', 'MiddleName', 'Imbuedo', '$2y$10$UvNNBqE4Ys5hsezzk6PPo.c6Y2pUu6/Tpw0oySFPWVSQw1I05Y9xC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0137-1', 'Christian John', 'MiddleName', 'Navarro ', '$2y$10$nMC4TqP/R2MhgrJlUjXMA.eJRm3x3eu6pvPT.hddWpknrL8Sr8bDG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0146-1', 'Baniely', 'MiddleName', 'Pajarit', '$2y$10$WOUVXxwSUsjrSVkAwXgeSeG.91dcYr9sFvBRGVTWCfnamYgku7Zqi', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0179-1', 'Jaira Mitch', 'Buen', 'Riñon', '$2y$10$ARMg/0iwbzrlqma0ylxRJupczE/zCKvBoZ1VnY4mfhGH9GhOsSMFG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0180-1', 'Irish Jingle', ' Ader', 'Riñon', '$2y$10$mRJUBZ1JvEIcGsGBPernd.nl0B4InRrAGUFHq/l07Wsp0yXcOVoVG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0325-1', 'Pryce', 'Cabanes', 'Cabagbag', '$2y$10$tIfoQlQKrp/Gu7.8Tu527.MV1LjoOhzOn34WtR2x3mnTY4CHCwere', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0368-1', 'Vench Axel Ross', 'Libed', 'Gliam', '$2y$10$OQTkK28o07IJhLfgH5KlFev09/Pk1NZRjHSgqLL7SFABawfCM7t0W', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0387-1', 'Clark Joshua', ' Velasco', 'Rojas', '$2y$10$tCGIAZQf7gD1UdsMTmb.UeBhC9nU22HKqsVY4rIKBjCksPh6vN96y', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0388-1', 'Charls Adonis', 'Velasco', 'Rojas', '$2y$10$FTMsMRx5rgm3IwfnIcAimuUVJNsZwnRKWJFhXzICrPF8i95RshtZy', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0422-1', 'Mark Kristian', 'Parchamento', 'Lagman', '$2y$10$b5nP9UgdeMj7.4zqlWTKru3s4UYQird0is4BuW8DImj5c27iwSKEu', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0476-1', 'Ana Marie', 'Edic', 'Almirol', '$2y$10$EDWbLzNKh14bSUpBXV0ew.cF4CagStZ88wI6BwybgM5yaVpGeyI3i', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0478-1', 'Claire', 'V', 'Almirol', '$2y$10$iqgnv81CNjVdkIcQa8PTj.2j/BoUPLUE/1uefiGZxWrixcJV//UaG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0484-1', 'Lyzette', ' S', 'Oliveros', '$2y$10$PNfFYBK/dZDzg.KGcvgzKOcBL2SpDD.bXfh7Urpn3SDjwOnALBAKi', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0485-1', 'Lorenzo', 'Nodora', 'Cariaga', '$2y$10$R0.pcCXXQ4mkYqHlVy7l5.kWhuaFoEgOgB6ypmH/d0EdFjUfnrSCO', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0497-1', 'Tyron', 'Doble', 'Cruz', '$2y$10$cFPabS1D63SN0epwj0zlHOMg/ka6mNznxKcxVVOZbPUEHxsq6FzDO', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0557-1', 'Giovani', 'Bataller', 'Valdriz', '$2y$10$1za0RMhuP.5pXLDZ8PqEgOwnxl2hYfgi3Fzupz4PZbHYYuE2wQkDO', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0626-1', 'Kristelle', 'Pacleb', 'Yara', '$2y$10$yx8/CGYu41B6wuS3dpqq/u/7ekMUnP9UzFTYqUm9fEjASlzHNZ.Qe', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0629-1', ' Karyl Zyrra', 'Tadije', 'Ancheta', '$2y$10$MZhmnGOakI/J.7bpJhQRS.qMRgSqEAdvsJupP0enOfbOGws0GTqIG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0648-1', 'Andrei Kyle', 'MiddleName', 'Legaspi', '$2y$10$Ig4Z1Pq5irU/qk0DE1IrnuhIJRiZ1QemDTFxouolt.m2hM.5lysem', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0733-1', 'Van Apollo', 'Untal', 'Mon', '$2y$10$iY2wgMgnu2aTb/b01Sk.luRLQdCRyomk7PP6U5vUqWFJE020lUEVS', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0743- 1', 'Jose Christopher', 'Gacrama', 'Apocero', '$2y$10$47xr9TsKglie3rBknlcmguK1ijAJfSOAuQ.eAB5b16rG9ag4c6ORW', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0778-1', 'Shasney', 'Sabado', 'Alminiana', '$2y$10$EwOwCR9oSwbA1KhLLfLk9eAQyq6lm/UU/.KkcpZqJb/6nEoSO3/vS', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0852-1', 'Jesiry', 'Bati', 'Subli', '$2y$10$t.fZQFjix1SRep./qelqzeO3hMUva6Wq4Cc27z0j.sBqwpVNhc.L6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('221-0867-1 ', 'Arianne Yesha', 'Mones', 'Marcella', '$2y$10$Dn1yC8JIxxR7DP5l/ekJFeB4X6y13CCVWOkeRaV80xMbw2SKIMVCi', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('221-0926-1', 'Shina Marie', 'Sobremonte', 'Apolinar', '$2y$10$TqYctaOEdA2zRhMT/3P5g.biCBqQKOZy2TdpTF3cxQU8dYo/a4XIS', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-A', 'student'),
('222-0014-1', 'Jeremiah', 'Valerio', 'Lubian', '$2y$10$C8pKTwxlP5xwUOYat/QxBOVU46YZUwPc8Uwp3QbB3gnPgD.Cmu3lO', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('231-0884-1', 'Delwin', 'N', 'Calica', '$2y$10$md7ym/5eHvgqyduRam.f4O.n5rUbWSSM2TzEphXQ/R7CXkqlKT4Ym', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('231-0923-1', 'Vien', 'Ivory', 'Marzan', '$2y$10$fDT3AF2rplrwln2oohZJVOcLJjovjIh4KnAKlAtIjcgmxKvFa4YHm', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '4-B', 'student'),
('251-0004-1', 'Unikey', 'Galvez', 'Visca', '$2y$10$Ld1ExLkdvda9TGdauZvCquuB.sOe/u1BFurAgdOr7E/hE1ix7KylG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0008-1', 'Jermain', 'Malubay', 'Mique', '$2y$10$H0nJlh29.ChGKtN54EoEiOCaNzp9HaG6TXGZ2H/OFiICB4cDLUP62', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0009-1', 'Rhodney Josh', 'Valdez', 'Olivar', '$2y$10$gy0oWdcDfrph5NPzkDfmF.yQHHmKD33kvkACt8r/0mB96uVQY5mAC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0010-1', 'Julius', 'Reola', 'Camat', '$2y$10$RWKveJiJhhhfhiiODrq2lei0PFMhJ751pXiGLdbdIEFkRw8vhXT3G', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0019-1', 'Princess Nicole', 'Salinas', 'Ordinario', '$2y$10$ZVayBsC/DJ7.DyAov.FZC.jC69YKsNMmm/ffcgGuJlQaKvVbPtk1e', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0042-1', 'Cedric Jyhn', 'Abellera', 'Galvez', '$2y$10$j3ea1UNjl3dgRUnyPP7zcOUfzzX9FYGZlzy6YXNiyZ/d2oELXSXfu', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0047-1', 'Kurt Russel', 'Perez', 'Almodovar', '$2y$10$zT/A3RDLGpLXKhrApmk5cutmCRyOQIR2KkMvjRaeHHQt56bGBhhNu', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0054-1', 'Althea', 'Cruz', 'Nuval', '$2y$10$WKLlLVFE8cuy//YggKyd3ORGuRGOxtIBcZKZMaQcDsJRzwgHnsHOG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0055-1', 'Angelica', 'Munar', 'Nuval', '$2y$10$4Nkn13lwjeW4XGC1USByzOwBsO33LyCqizsPxfKTWLMzCGMCy/rAy', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0057-1', 'Kristhel', 'Valdez', 'Obille', '$2y$10$M9O4wYDwAOosx25Q6nR5bu8rkIUZHl.IZh3OCgRsrM.S5i.y4hk4K', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0166-1', 'Heraneth', 'Federico', 'Baro', '$2y$10$tkvDUq6DarjGEwLhrIYuCeI.WMeuWrfCxk8ZXL0e9bDlnsYopApaK', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0186-1', 'Deric', 'Gacayan', 'Cotcheza', '$2y$10$kyqxNGttROoX5gMwTuwakeP.FcH1s.nUNctqh8OkCUdH8b2z6IKf.', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0231-1', 'John Vincent', 'Degyem', 'Nillo', '$2y$10$.acgiaRC18Z0mENwI9zXoOt9oyWgmV1vCyO/7Lh5ac14oDNQSmxp2', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0233-1', 'Jayson', 'Cabanban', 'De Guzman', '$2y$10$vUCkv/USih7LKjxKaVGNSumkWPc5RfcGPyijd60oumcUCtE879LZe', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0234-1', 'Cristobal', 'Palabay', 'Ledesma', '$2y$10$xoJ08IhSh6V7Umk2VE9rz.eJ6qM.yfcsp..cfMxVGBrKXpNRbs2fm', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0238-1', 'Rio', 'Cabanban', 'Cabanban', '$2y$10$tTDHv9Hl7QqJTQ3JCBYt.us8SfBp6uXjIPnGeF.mfDYw9AiKe1J5W', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0250-1', 'Precious Lara', 'Marcella', 'Eslava', '$2y$10$Z6GGiuk4rYdSJbo9cjZi8OpL4h/vU4y6ZBw9ALhMtWz/NF2rxbsoC', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0261-1', 'Joemarc', 'Uslay', 'Balisong', '$2y$10$HJk9R1Q.ooL5keNd.64kxe1AXeiZx9uY2r1eWimW4UGjmvT8iHSa6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0269-1', 'Georgen', 'Cabanayan', 'Perez', '$2y$10$JhfwKYAZfn/JR/XMJMYuqO3dutkktiwxgu7ue2XZNTkqjwSycmJQe', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0271-1', 'Rhalyle Kenneth', 'Biore', 'Bentoy', '$2y$10$50PnL2ckM.c/6H.2PY2rjuHx1nU/KCcyD3x.wpet82uVVrG9O/lYm', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0367-1', 'Jeremi', 'Delarna', 'Castro', '$2y$10$GnPVEmKKHmmH.5EUNKC2neoLffynxjkhblWcwmKX8l34wwbLe4Cz2', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0368-1', 'Hans Christian', 'Joven', 'Cortez', '$2y$10$u0igbzA84B6AnEXi6VFBMOw7beEkLUFHDR1aBr6znyCplCZZnhf6W', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0369-1', 'Princess Lei Ann', 'Carig', 'Tagumpay', '$2y$10$8.4E5Tf34zWNp591NtUAte4XO6cHYoH5HcnQamKmWzO3C9Mjn809i', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0375-1', 'Rafael Christian Mark', 'Nieveras', 'Astudillo', '$2y$10$.QUchP.kcjSvGaDt8RmbD.KlBKNw1xbkrhtga9jNBivQgpdZiXVIq', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0376-1', 'Domnic Savio Angelo', 'Munar', 'Munar', '$2y$10$8qvFF6MdKfVLBPKniDeopuoFIuiS/B3X2wLTapvwt42i3Ime3FB76', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0379-1', 'Jerald Collin', 'Bautista', 'Lim', '$2y$10$2qqFcZsZFak.pDhPvhZA4.2KhoIFF5sPSj7AToAJERLgAh4k2n2Ja', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0383-1', 'Gian Kenneth', 'Jalne', 'Nuesca', '$2y$10$7Z7kQW1Dn86MM.9HW7UXT.Uby9knE/mFQvSH.kofaQpPuD6VeQEF6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0386-1', 'Manilyn', 'Briongos', 'Olito', '$2y$10$jB6YSapTR3G7UAItE.ajUeHj23C86IJ0Hul/RcmpnGofpWDWySVLi', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0401-1', 'Rashelle', 'Dating-E', 'Mahgit', '$2y$10$EskNvmyyLk.JE4lqU.w7.egQROXvQUu86629mKAZGZrsZIl8T7kku', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0416-1', 'Renz', 'Dadcoes', 'Madayag', '$2y$10$l6yzt/kE7b8LtVVg2m3mau5/aDnUiADcczbo9Z/JZa..nOO2262oG', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0458-1', 'Bonnie', 'Buccat', 'Padua', '$2y$10$.gSwcxxGZKjA3fqFAKEzD.v2GuAWbxWJiW/mh5CL6.LwtFN2TeRGe', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0477-1', 'Monica', 'Seguin', 'Albay', '$2y$10$w3BEYODsBQskDHSB4WAzvOB76hXbSrcUEJuKfUXu6J5UEr50EEoNK', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0482-1', 'Aeron', 'Nerona', 'Gao-An', '$2y$10$jKhWkysKhuxSnSm832n2XeVVNzyTfLyu6KKkU.cw.0k.h2aaM6Rq.', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0512-1', 'Princess Dianah', 'Monis', 'Lucena', '$2y$10$.csmd0itdYAHsyZaLXCVOO87tE9uU7YSzFfg8OFBpdpttwTNt1XZ6', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student'),
('251-0517-1', 'Chloe', 'Arquero', 'Nones', '$2y$10$esXECc8cqhdbMqJPAJGPo.dZmGGzLcLZIhlV6BZugByuHrIVCNXsm', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems', '1-A', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `student_evaluation_submissions`
--

CREATE TABLE `student_evaluation_submissions` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  `faculty_id` varchar(50) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
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

INSERT INTO `student_evaluation_submissions` (`id`, `student_id`, `subject_code`, `faculty_id`, `college`, `academic_year`, `semester`, `created_at`, `answers`, `total_score`, `computed_rating`, `comment`, `is_anonymous`) VALUES
(71, '221-0325-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:42:08', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(72, '221-0476-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:42:27', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'none', 'yes'),
(73, '231-0884-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:43:52', '{\"q0\":3,\"q1\":3,\"q2\":3,\"q3\":3,\"q4\":3,\"q5\":3,\"q6\":3,\"q7\":3,\"q8\":3,\"q9\":3,\"q10\":3,\"q11\":3,\"q12\":3,\"q13\":3,\"q14\":3}', 45, 60.00, 'yes', 'yes'),
(74, '221-0422-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:44:42', '{\"q0\":4,\"q1\":4,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":4,\"q6\":3,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 67, 89.33, 'napaka galing mo', 'yes'),
(75, '211-0004-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:44:49', '{\"q0\":4,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":4,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 70, 93.33, '', 'yes'),
(76, '211-0004-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:45:25', '{\"q0\":4,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":4,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":4}', 70, 93.33, '', 'yes'),
(77, '221-0778-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:45:46', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(78, '221-0368-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:45:54', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Wow', 'no'),
(79, '211-0161-1 ', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:45:58', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, '', 'yes'),
(80, '221-0733-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:46:00', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":5,\"q12\":4,\"q13\":4,\"q14\":5}', 67, 89.33, 'N/A', 'no'),
(81, '221-0026-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:46:00', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(82, '221-0070-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:46:27', '{\"q0\":5,\"q1\":5,\"q2\":4,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 70, 93.33, 'N/A', 'yes'),
(83, '202-0021-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:46:53', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, '', 'no'),
(84, '221-0146-1', 'ISSM 105', '40023', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:47:19', '{\"q0\":4,\"q1\":4,\"q2\":4,\"q3\":4,\"q4\":4,\"q5\":4,\"q6\":4,\"q7\":4,\"q8\":4,\"q9\":4,\"q10\":4,\"q11\":4,\"q12\":4,\"q13\":4,\"q14\":4}', 60, 80.00, 'None', 'no'),
(85, '221-0387-1', 'ISPC 112', '40184', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:48:32', '{\"q0\":5,\"q1\":4,\"q2\":3,\"q3\":3,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 67, 89.33, 'nICE TEACHING', 'yes'),
(86, '221-0852-1', 'ISPC 112', '40184', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:50:14', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":4,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":4,\"q12\":4,\"q13\":5,\"q14\":4}', 65, 86.67, 'Good teaching ', 'yes'),
(87, '221-0867-1 ', 'ISPC 112', '40184', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:50:55', '{\"q0\":5,\"q1\":4,\"q2\":4,\"q3\":5,\"q4\":5,\"q5\":4,\"q6\":5,\"q7\":4,\"q8\":4,\"q9\":5,\"q10\":4,\"q11\":5,\"q12\":5,\"q13\":4,\"q14\":4}', 67, 89.33, 'none', 'yes'),
(88, '221-0867-1 ', 'ISAE 107', '40207', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:55:15', '{\"q0\":4,\"q1\":4,\"q2\":3,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":4,\"q12\":5,\"q13\":4,\"q14\":4}', 65, 86.67, 'None\r\n', 'yes'),
(89, '221-0387-1', 'ISBA 105', '40180', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 13:55:42', '{\"q0\":5,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":5,\"q5\":4,\"q6\":5,\"q7\":4,\"q8\":5,\"q9\":4,\"q10\":5,\"q11\":4,\"q12\":4,\"q13\":5,\"q14\":5}', 68, 90.67, 'Excellent Teaching', 'no'),
(90, '221-0478-1', 'ISPC 114', '40413', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:01:45', '{\"q0\":4,\"q1\":4,\"q2\":4,\"q3\":4,\"q4\":4,\"q5\":4,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":4,\"q10\":4,\"q11\":4,\"q12\":4,\"q13\":4,\"q14\":5}', 63, 84.00, '', 'yes'),
(91, '251-0250-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:18:10', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'always take care po maam', 'no'),
(92, '251-0416-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:23:19', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Gwapo', 'yes'),
(93, '251-0057-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:23:41', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(94, '251-0234-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:24:53', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Very good in teaching', 'no'),
(95, '251-0269-1', 'ISCC 102', '40094', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:27:41', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'N/A', 'yes'),
(96, '251-0004-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:27:54', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 74, 98.67, 'very joyful teacher', 'no'),
(97, '251-0009-1', 'ISCC 102', '40094', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:28:22', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Keep up the good work ma\'am<3', 'yes'),
(98, '251-0233-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:28:26', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Very good ', 'yes'),
(99, '251-0008-1', 'ISCC 102', '40094', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:28:32', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(100, '251-0019-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:28:44', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'yes'),
(101, '251-0458-1', 'ISCC 102', '40094', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:28:57', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'N/A', 'yes'),
(102, '251-0383-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:29:29', '{\"q0\":4,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 74, 98.67, 'ma\'am keep up the good work ma\'am really appreciated ^ ^', 'no'),
(103, '251-0010-1', 'ISCC 102', '40094', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:29:44', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'the best teacher and easy to approach ', 'yes'),
(104, '251-0458-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:30:15', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'N/A', 'yes'),
(105, '251-0401-1', 'GEEC 101', '0716', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:31:04', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'No comment ', 'yes'),
(106, '251-0482-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:32:09', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'Iloveyousomuch ma\'am ', 'yes'),
(107, '251-0010-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:33:43', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'approachable teacher ', 'yes'),
(108, '251-0482-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:34:17', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, '', 'no'),
(109, '251-0010-1', 'GEEC 101', '0716', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:35:29', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 73, 97.33, 'Approachable teacher', 'yes'),
(110, '251-0010-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:38:25', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":5,\"q13\":5,\"q14\":5}', 75, 100.00, 'approachable teacher ', 'yes'),
(111, '251-0369-1', 'ISCC 101', '00711', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2025-11-06 14:38:38', '{\"q0\":4,\"q1\":4,\"q2\":4,\"q3\":3,\"q4\":3,\"q5\":4,\"q6\":4,\"q7\":4,\"q8\":4,\"q9\":3,\"q10\":4,\"q11\":4,\"q12\":5,\"q13\":5,\"q14\":5}', 60, 80.00, '', 'yes'),
(112, '251-0369-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:39:50', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":4}', 73, 97.33, '', 'yes'),
(113, '251-0369-1', 'GECC 103', '02860', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:40:43', '{\"q0\":4,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":4}', 66, 88.00, '', 'yes');

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
(654, '251-0477-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(655, '251-0047-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(656, '251-0375-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(657, '251-0261-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(658, '251-0166-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(659, '251-0271-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(660, '251-0238-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(661, '251-0010-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(662, '251-0367-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(663, '251-0368-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(664, '251-0186-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(665, '251-0233-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(666, '251-0250-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(667, '251-0042-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(668, '251-0482-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(669, '251-0234-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(670, '251-0379-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(671, '251-0512-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(672, '251-0416-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(673, '251-0401-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(674, '251-0008-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(675, '251-0376-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(676, '251-0231-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(677, '251-0517-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(678, '251-0383-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(679, '251-0054-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(680, '251-0055-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(681, '251-0057-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(682, '251-0386-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(683, '251-0009-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(684, '251-0019-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(685, '251-0458-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(686, '251-0269-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(687, '251-0369-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(688, '251-0004-1', 'GEEC 101', '2025-2026', '1st Semester', '0716', '40005'),
(689, '251-0477-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(690, '251-0477-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(691, '251-0047-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(692, '251-0047-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(693, '251-0375-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(694, '251-0375-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(695, '251-0261-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(696, '251-0261-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(697, '251-0166-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(698, '251-0166-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(699, '251-0271-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(700, '251-0271-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(701, '251-0238-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(702, '251-0238-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(703, '251-0010-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(704, '251-0010-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(705, '251-0367-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(706, '251-0367-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(707, '251-0368-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(708, '251-0368-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(709, '251-0186-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(710, '251-0186-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(711, '251-0233-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(712, '251-0233-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(713, '251-0250-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(714, '251-0250-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(715, '251-0042-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(716, '251-0042-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(717, '251-0482-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(718, '251-0482-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(719, '251-0234-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(720, '251-0234-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(721, '251-0379-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(722, '251-0379-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(723, '251-0512-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(724, '251-0512-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(725, '251-0416-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(726, '251-0416-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(727, '251-0401-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(728, '251-0401-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(729, '251-0008-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(730, '251-0008-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(731, '251-0376-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(732, '251-0376-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(733, '251-0231-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(734, '251-0231-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(735, '251-0517-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(736, '251-0517-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(737, '251-0383-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(738, '251-0383-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(739, '251-0054-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(740, '251-0054-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(741, '251-0055-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(742, '251-0055-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(743, '251-0057-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(744, '251-0057-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(745, '251-0386-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(746, '251-0386-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(747, '251-0009-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(748, '251-0009-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(749, '251-0019-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(750, '251-0019-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(751, '251-0458-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(752, '251-0458-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(753, '251-0269-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(754, '251-0269-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(755, '251-0369-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(756, '251-0369-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(757, '251-0004-1', 'GECC 101', '2025-2026', '1st Semester', '00421', '40045'),
(758, '251-0004-1', 'GECC 103', '2025-2026', '1st Semester', '02860', '40045'),
(759, '251-0477-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(760, '251-0047-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(761, '251-0375-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(762, '251-0261-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(763, '251-0166-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(764, '251-0271-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(765, '251-0238-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(766, '251-0010-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(767, '251-0367-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(768, '251-0368-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(769, '251-0186-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(770, '251-0233-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(771, '251-0250-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(772, '251-0042-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(773, '251-0482-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(774, '251-0234-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(775, '251-0379-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(776, '251-0512-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(777, '251-0416-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(778, '251-0401-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(779, '251-0008-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(780, '251-0376-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(781, '251-0231-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(782, '251-0517-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(783, '251-0383-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(784, '251-0054-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(785, '251-0055-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(786, '251-0057-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(787, '251-0386-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(788, '251-0009-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(789, '251-0019-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(790, '251-0458-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(791, '251-0269-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(792, '251-0369-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(793, '251-0004-1', 'GECC 102', '2025-2026', '1st Semester', '40193', '40050'),
(794, '251-0477-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(795, '251-0477-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(796, '251-0047-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(797, '251-0047-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(798, '251-0375-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(799, '251-0375-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(800, '251-0261-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(801, '251-0261-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(802, '251-0166-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(803, '251-0166-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(804, '251-0271-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(805, '251-0271-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(806, '251-0238-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(807, '251-0238-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(808, '251-0010-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(809, '251-0010-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(810, '251-0367-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(811, '251-0367-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(812, '251-0368-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(813, '251-0368-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(814, '251-0186-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(815, '251-0186-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(816, '251-0233-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(817, '251-0233-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(818, '251-0250-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(819, '251-0250-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(820, '251-0042-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(821, '251-0042-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(822, '251-0482-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(823, '251-0482-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(824, '251-0234-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(825, '251-0234-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(826, '251-0379-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(827, '251-0379-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(828, '251-0512-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(829, '251-0512-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(830, '251-0416-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(831, '251-0416-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(832, '251-0401-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(833, '251-0401-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(834, '251-0008-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(835, '251-0008-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(836, '251-0376-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(837, '251-0376-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(838, '251-0231-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(839, '251-0231-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(840, '251-0517-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(841, '251-0517-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(842, '251-0383-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(843, '251-0383-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(844, '251-0054-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(845, '251-0054-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(846, '251-0055-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(847, '251-0055-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(848, '251-0057-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(849, '251-0057-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(850, '251-0386-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(851, '251-0386-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(852, '251-0009-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(853, '251-0009-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(854, '251-0019-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(855, '251-0019-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(856, '251-0458-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(857, '251-0458-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(858, '251-0269-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(859, '251-0269-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(860, '251-0369-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(861, '251-0369-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(862, '251-0004-1', 'ISCC 102', '2025-2026', '1st Semester', '40094', '40151'),
(863, '251-0004-1', 'ISCC 101', '2025-2026', '1st Semester', '00711', '40151'),
(864, '221-0778-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(865, '221-0778-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(866, '221-0778-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(867, '221-0778-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(868, '221-0778-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(869, '221-0778-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(870, '221-0079-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(871, '221-0079-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(872, '221-0079-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(873, '221-0079-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(874, '221-0079-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(875, '221-0079-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(876, '221-0629-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(877, '221-0629-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(878, '221-0629-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(879, '221-0629-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(880, '221-0629-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(881, '221-0629-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(882, '221-0743- 1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(883, '221-0743- 1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(884, '221-0743- 1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(885, '221-0743- 1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(886, '221-0743- 1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(887, '221-0743- 1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(888, '221-0926-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(889, '221-0926-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(890, '221-0926-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(891, '221-0926-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(892, '221-0926-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(893, '221-0926-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(894, '211-0164-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(895, '211-0164-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(896, '211-0164-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(897, '211-0164-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(898, '211-0164-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(899, '211-0164-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(900, '221-0070-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(901, '221-0070-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(902, '221-0070-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(903, '221-0070-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(904, '221-0070-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(905, '221-0070-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(906, '202-0021-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(907, '202-0021-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(908, '202-0021-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(909, '202-0021-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(910, '202-0021-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(911, '202-0021-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(912, '211-0805-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(913, '211-0805-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(914, '211-0805-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(915, '211-0805-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(916, '211-0805-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(917, '211-0805-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(918, '221-0005-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(919, '221-0005-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(920, '221-0005-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(921, '221-0005-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(922, '221-0005-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(923, '221-0005-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(924, '221-0082-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(925, '221-0082-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(926, '221-0082-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(927, '221-0082-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(928, '221-0082-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(929, '221-0082-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(930, '221-0080-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(931, '221-0080-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(932, '221-0080-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(933, '221-0080-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(934, '221-0080-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(935, '221-0080-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(936, '211-0161-1 ', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(937, '211-0161-1 ', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(938, '211-0161-1 ', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(939, '211-0161-1 ', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(940, '211-0161-1 ', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(941, '211-0161-1 ', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(942, '221-0733-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(943, '221-0733-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(944, '221-0733-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(945, '221-0733-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(946, '221-0733-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(947, '221-0733-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(948, '221-0026-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(949, '221-0026-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(950, '221-0026-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(951, '221-0026-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(952, '221-0026-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(953, '221-0026-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(954, '221-0146-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(955, '221-0146-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(956, '221-0146-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(957, '221-0146-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(958, '221-0146-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(959, '221-0146-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(960, '221-0179-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(961, '221-0179-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(962, '221-0179-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(963, '221-0179-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(964, '221-0179-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(965, '221-0179-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(966, '221-0180-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(967, '221-0180-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(968, '221-0180-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(969, '221-0180-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(970, '221-0180-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(971, '221-0180-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(972, '221-0852-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(973, '221-0852-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(974, '221-0852-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(975, '221-0852-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(976, '221-0852-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(977, '221-0852-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(978, '221-0626-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(979, '221-0626-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(980, '221-0626-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(981, '221-0626-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(982, '221-0626-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(983, '221-0626-1', 'ISSM 105', '2025-2026', '1st Semester', '40023', '40151'),
(984, '221- 0411-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(985, '221- 0411-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(986, '221- 0411-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(987, '221- 0411-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(988, '221- 0411-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(989, '221- 0411-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(990, '221-0476-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(991, '221-0476-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(992, '221-0476-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(993, '221-0476-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(994, '221-0476-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(995, '221-0476-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(996, '221-0478-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(997, '221-0478-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(998, '221-0478-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(999, '221-0478-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1000, '221-0478-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1001, '221-0478-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1002, '221-0325-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1003, '221-0325-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1004, '221-0325-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1005, '221-0325-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1006, '221-0325-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1007, '221-0325-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1008, '231-0884-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1009, '231-0884-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1010, '231-0884-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1011, '231-0884-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1012, '231-0884-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1013, '231-0884-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1014, '201-0326-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1015, '201-0326-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1016, '201-0326-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1017, '201-0326-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1018, '201-0326-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1019, '201-0326-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1020, '221-0485-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1021, '221-0485-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1022, '221-0485-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1023, '221-0485-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1024, '221-0485-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1025, '221-0485-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1026, '221-0497-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1027, '221-0497-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1028, '221-0497-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1029, '221-0497-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1030, '221-0497-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1031, '221-0497-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1032, '221-0368-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1033, '221-0368-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1034, '221-0368-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1035, '221-0368-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1036, '221-0368-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1037, '221-0368-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1038, '221-0422-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1039, '221-0422-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1040, '221-0422-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1041, '221-0422-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1042, '221-0422-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1043, '221-0422-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1044, '221-0648-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1045, '221-0648-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1046, '221-0648-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1047, '221-0648-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1048, '221-0648-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1049, '221-0648-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1050, '222-0014-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1051, '222-0014-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1052, '222-0014-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1053, '222-0014-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1054, '222-0014-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1055, '222-0014-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1056, '221-0867-1 ', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1057, '221-0867-1 ', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1058, '221-0867-1 ', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1059, '221-0867-1 ', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1060, '221-0867-1 ', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1061, '221-0867-1 ', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1062, '231-0923-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1063, '231-0923-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1064, '231-0923-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1065, '231-0923-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1066, '231-0923-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1067, '231-0923-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1068, '221-0006-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1069, '221-0006-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1070, '221-0006-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1071, '221-0006-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1072, '221-0006-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1073, '221-0006-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1074, '221-0137-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1075, '221-0137-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1076, '221-0137-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1077, '221-0137-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1078, '221-0137-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1079, '221-0137-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1080, '221-0484-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1081, '221-0484-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1082, '221-0484-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1083, '221-0484-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1084, '221-0484-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1085, '221-0484-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1087, '221-0387-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1088, '221-0387-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1089, '221-0387-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1090, '221-0387-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1091, '221-0387-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1092, '221-0388-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1093, '221-0388-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1094, '221-0388-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1095, '221-0388-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1096, '221-0388-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1097, '221-0388-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1098, '211-0174-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1099, '211-0174-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1100, '211-0174-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1101, '211-0174-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1102, '211-0174-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1103, '211-0174-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1104, '221-0557-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1105, '221-0557-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1106, '221-0557-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1107, '221-0557-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1108, '221-0557-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1109, '221-0557-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1110, '211-0004-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1111, '211-0004-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(1112, '211-0004-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(1113, '211-0004-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(1114, '211-0004-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(1115, '211-0004-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
(1119, '221-0387-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151');

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
  `college` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`idnumber`, `code`, `title`, `faculty_id`, `admin_id`, `college`, `program`) VALUES
(62, 'ISPC 110', 'Business Process Management', '40112', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(63, 'ISPC 112', 'IS Strategy Management and Acquisition', '40184', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(64, 'ISAE 107', 'Professional Engagements', '40207', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(65, 'ISAE 108', 'Technoprenuership', '40182', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(66, 'ISPC 114', 'Capstone Project 2', '40413', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(67, 'ISBA 105', 'Analytics Application', '40180', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(68, 'ISSM 105', 'Principles of Systems Thinking', '40023', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(69, 'GEEC 101', 'Environmental Science', '0716', '40005', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(71, 'GECC 103', 'Mathematics in the Modern World', '02860', '40045', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(72, 'GECC 101', 'Arts Appreciation', '00421', '40045', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(73, 'GECC 102', 'Purposive Communication', '40193', '40050', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(74, 'ISCC 101', 'Introduction to Computing', '00711', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems'),
(75, 'ISCC 102', 'Computer Programming 1', '40094', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'Bachelor of Science in Information Systems');

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
  `college` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `faculty_rank` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `status` varchar(11) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `role`, `college`, `program`, `faculty_rank`, `position`, `status`) VALUES
('40193', 'Frediz Winda', 'Ferrer', 'Badua', '$2y$10$e6ZHPQM/6xvk1/jvupujHO4pggmk8P27m5TeUAnRpyZ9Iiblm.lpa', 'superadmin', 'COLLEGE OF ARTS AND SCIENCES', 'Bachelor of Arts in English Language', 'Assistant Professor IV', 'Head Instruction', 'active');

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
  ADD UNIQUE KEY `program_name` (`program_name`),
  ADD KEY `rank_name` (`rank_name`),
  ADD KEY `position_name` (`position_name`),
  ADD KEY `section_name` (`section_name`),
  ADD KEY `department_name` (`college_name`),
  ADD KEY `college_name` (`college_name`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `position` (`position`),
  ADD KEY `fk_admin_faculty_rank` (`faculty_rank`);

--
-- Indexes for table `admin_college`
--
ALTER TABLE `admin_college`
  ADD PRIMARY KEY (`admin_idnumber`,`college_name`,`program_name`),
  ADD KEY `department_name` (`college_name`),
  ADD KEY `admin_program` (`program_name`);

--
-- Indexes for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_evaluator` (`evaluator_id`),
  ADD KEY `fk_faculty_evaluatee` (`evaluatee_id`),
  ADD KEY `fk_evaluator_position` (`evaluator_position`),
  ADD KEY `fk_admin_evaluation_department` (`college`),
  ADD KEY `college` (`college`);

--
-- Indexes for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `college_info`
--
ALTER TABLE `college_info`
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
  ADD KEY `fk_evaluation_department` (`college`),
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
  ADD KEY `department` (`college`),
  ADD KEY `fk_faculty_rank` (`faculty_rank`),
  ADD KEY `fk_faculty_program` (`program`),
  ADD KEY `college` (`college`);

--
-- Indexes for table `registrar`
--
ALTER TABLE `registrar`
  ADD PRIMARY KEY (`idnumber`),
  ADD UNIQUE KEY `faculty_rank` (`faculty_rank`),
  ADD KEY `fk_registrar_department` (`college`),
  ADD KEY `fk_registrar_program` (`program`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`idnumber`),
  ADD KEY `department` (`college`),
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
  ADD KEY `fk_superadmin_department` (`college`),
  ADD KEY `fk_superadmin_program` (`program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=631;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `college_info`
--
ALTER TABLE `college_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `evaluation_settings`
--
ALTER TABLE `evaluation_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluation_switch`
--
ALTER TABLE `evaluation_switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1150;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

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
-- Constraints for table `admin_college`
--
ALTER TABLE `admin_college`
  ADD CONSTRAINT `admin_college_ibfk_2` FOREIGN KEY (`college_name`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_program` FOREIGN KEY (`program_name`) REFERENCES `adds` (`program_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_idnumber`) REFERENCES `admin` (`idnumber`) ON DELETE CASCADE;

--
-- Constraints for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  ADD CONSTRAINT `fk_admin_evaluation_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `fk_evaluation_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `fk_faculty_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_program` FOREIGN KEY (`program`) REFERENCES `adds` (`program_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `registrar`
--
ALTER TABLE `registrar`
  ADD CONSTRAINT `fk_registrar_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registrar_program` FOREIGN KEY (`program`) REFERENCES `adds` (`program_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_registrar_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `subject_faculty_id` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD CONSTRAINT `fk_superadmin_department` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_superadmin_faculty_rank` FOREIGN KEY (`faculty_rank`) REFERENCES `adds` (`rank_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_superadmin_position` FOREIGN KEY (`position`) REFERENCES `adds` (`position_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_superadmin_program` FOREIGN KEY (`program`) REFERENCES `adds` (`program_name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
