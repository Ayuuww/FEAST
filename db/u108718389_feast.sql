-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 07:53 AM
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
(630, '40151', 'admin', 'Logged in', '2025-11-28 01:22:52'),
(631, '00000', '', 'Logged in', '2025-11-28 19:04:41'),
(632, '40180', 'admin', 'Logged in', '2025-11-28 19:05:29'),
(633, '40180', 'admin', 'Logged in', '2025-11-28 19:06:51'),
(634, '00000', '', 'Logged in', '2025-11-28 19:10:12'),
(635, '40180', 'faculty', 'Logged in', '2025-11-28 19:42:33'),
(636, '40180', 'admin', 'Logged in', '2025-11-28 19:44:09'),
(637, '40193', 'superadmin', 'Logged in', '2025-11-28 21:25:54'),
(638, '221-0387-1', 'student', 'Logged in', '2025-11-28 22:54:14'),
(639, '40193', 'superadmin', 'Logged in', '2025-11-28 22:55:09'),
(640, '40193', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'1st Semester - 2025-2027\'', '2025-11-28 22:55:19'),
(641, '40193', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2027\' to \'1st Semester - 2025-2026\'', '2025-11-28 22:55:32'),
(642, '40193', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'2nd Semester - 2025-2026\'', '2025-11-28 22:55:38'),
(643, '40193', 'superadmin', 'Updated evaluation settings from \'2nd Semester - 2025-2026\' to \'1st Semester - 2025-2026\'', '2025-11-28 22:55:46'),
(644, '40193', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2025-2026\' to \'1st Semester - 2024-2026\'', '2025-11-28 22:55:57'),
(645, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-25 | End: 2025-11-28', '2025-11-28 22:56:04'),
(646, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-25 | End: 2025-11-28', '2025-11-28 22:56:09'),
(647, '40193', 'superadmin', 'Updated evaluation settings from \'1st Semester - 2024-2026\' to \'1st Semester - 2025-2026\'', '2025-11-28 22:56:20'),
(648, '40151', 'admin', 'Logged in', '2025-11-28 23:02:43'),
(653, '40151', 'admin', 'Logged in', '2025-11-29 18:25:51'),
(654, '00000', '', 'Logged in', '2025-11-29 21:26:21'),
(655, '00711', 'faculty', 'Logged in', '2025-11-30 01:01:47'),
(656, '00000', '', 'Logged in', '2025-11-30 01:26:09'),
(657, '123', 'admin', 'Logged in', '2025-11-30 01:32:31'),
(658, '40045', 'admin', 'Logged in', '2025-11-30 01:32:52'),
(659, '40005', 'admin', 'Logged in', '2025-11-30 01:33:09'),
(660, '123', 'admin', 'Logged in', '2025-11-30 01:33:48'),
(661, '00000', '', 'Logged in', '2025-11-30 02:10:08'),
(662, '123', 'admin', 'Logged in', '2025-11-30 02:33:13'),
(663, '123', 'admin', 'Logged in', '2025-11-30 02:55:21'),
(664, '40151', 'admin', 'Logged in', '2025-11-30 03:16:04'),
(665, '40151', 'admin', 'Logged in', '2025-11-30 03:26:39'),
(666, '40151', 'admin', 'Logged in', '2025-11-30 16:36:34'),
(667, '40151', 'admin', 'Logged in', '2025-11-30 17:11:16'),
(668, '221-0387-1', 'student', 'Logged in', '2025-11-30 17:15:37'),
(669, '221-0387-1', 'student', 'Logged in', '2025-11-30 17:17:19'),
(670, '00000', '', 'Logged in', '2025-11-30 17:17:29'),
(671, '00000', '', 'Logged in', '2025-11-30 17:18:10'),
(672, '40151', 'admin', 'Logged in', '2025-11-30 17:18:33'),
(673, '221-0387-1', 'student', 'Logged in', '2025-11-30 17:21:46'),
(674, '40182', 'faculty', 'Logged in', '2025-11-30 17:23:43'),
(675, '40045', 'admin', 'Logged in', '2025-11-30 17:24:16'),
(676, '00000', '', 'Logged in', '2025-11-30 17:24:32'),
(677, '00000', '', 'Logged in', '2025-11-30 17:26:41'),
(678, '40045', 'faculty', 'Logged in', '2025-11-30 17:29:07'),
(679, '00000', '', 'Logged in', '2025-11-30 17:31:42'),
(680, '40045', 'faculty', 'Logged in', '2025-11-30 17:32:55'),
(681, '00000', '', 'Logged in', '2025-11-30 17:33:23'),
(682, '40045', 'admin', 'Logged in', '2025-11-30 17:33:51'),
(683, '40045', 'faculty', 'Logged in', '2025-11-30 17:34:23'),
(684, '40045', 'faculty', 'Logged in', '2025-11-30 17:37:37'),
(685, '00000', '', 'Logged in', '2025-11-30 17:37:53'),
(686, '00000', '', 'Logged in', '2025-11-30 17:38:32'),
(687, '40045', 'admin', 'Logged in', '2025-11-30 17:38:47'),
(688, '00000', '', 'Logged in', '2025-11-30 17:38:58'),
(689, '00000', '', 'Logged in', '2025-11-30 17:42:09'),
(690, '40193', 'faculty', 'Logged in', '2025-11-30 17:42:30'),
(691, '00000', '', 'Logged in', '2025-11-30 17:46:07'),
(692, '40193', 'superadmin', 'Logged in', '2025-11-30 17:46:42'),
(693, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2025-11-30 17:46:57'),
(694, '00000', '', 'Logged in', '2025-11-30 17:47:49'),
(695, '00421', 'faculty', 'Logged in', '2025-11-30 17:50:22'),
(696, '00000', '', 'Logged in', '2025-11-30 17:50:44'),
(697, '40193', 'faculty', 'Logged in', '2025-11-30 17:53:10'),
(698, '40193', 'superadmin', 'Logged in', '2025-11-30 17:53:42'),
(699, '40193', 'faculty', 'Logged in', '2025-11-30 17:54:12'),
(700, '40151', 'admin', 'Logged in', '2025-11-30 18:43:52'),
(701, '40193', 'superadmin', 'Logged in', '2025-11-30 18:44:09'),
(702, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-25 | End: 2025-11-28', '2025-11-30 18:44:15'),
(703, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2025-11-30 18:44:15'),
(704, '40193', 'superadmin', 'Evaluation turned on | Start: 2025-11-30 | End: 2025-12-01', '2025-11-30 18:44:34'),
(705, '40151', 'admin', 'Logged in', '2025-12-07 21:42:39'),
(706, '40193', 'superadmin', 'Logged in', '2025-12-07 21:55:56'),
(707, '221-0387-1', 'student', 'Logged in', '2026-03-25 06:03:24'),
(708, '221-0387-1', 'student', 'Logged in', '2026-03-25 06:05:20'),
(709, '40151', 'admin', 'Logged in', '2026-03-25 06:09:26'),
(710, '221-0852-1', 'student', 'Logged in', '2026-03-25 06:13:51'),
(711, '221-0387-1', 'student', 'Logged in', '2026-03-25 06:18:16'),
(712, '40193', 'superadmin', 'Logged in', '2026-03-25 06:19:11'),
(713, '00000', '', 'Logged in', '2026-03-25 07:58:29'),
(714, '40193', 'superadmin', 'Logged in', '2026-03-25 08:00:29'),
(715, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2026-03-25 08:00:54'),
(716, '00000', '', 'Logged in', '2026-03-25 08:02:44'),
(717, '40151', 'admin', 'Logged in', '2026-03-25 08:08:43'),
(718, '221-0387-1', 'student', 'Logged in', '2026-03-26 04:45:38'),
(719, '221-0387-1', 'student', 'Logged in', '2026-03-28 02:41:05'),
(720, '40151', 'admin', 'Logged in', '2026-03-28 02:42:07'),
(721, '221-0387-1', 'student', 'Logged in', '2026-03-28 03:49:31'),
(722, '221-0387-1', 'student', 'Logged in', '2026-03-28 03:57:59'),
(723, '40193', 'superadmin', 'Logged in', '2026-03-28 04:23:11'),
(724, '40151', 'admin', 'Logged in', '2026-03-28 04:25:13'),
(725, '40193', 'superadmin', 'Logged in', '2026-03-28 04:25:27'),
(726, '221-0852-1', 'student', 'Logged in', '2026-03-28 04:26:09'),
(727, '40193', 'superadmin', 'Evaluation turned on | Start: 2026-03-28 | End: 2026-03-30', '2026-03-28 04:26:14'),
(728, '221-0733-1', 'student', 'Logged in', '2026-03-28 04:26:21'),
(729, '40151', 'admin', 'Logged in', '2026-03-28 04:26:28'),
(730, '00000', '', 'Logged in', '2026-03-28 04:26:55'),
(731, '40193', 'superadmin', 'Logged in', '2026-03-28 05:11:47'),
(732, '00000', '', 'Logged in', '2026-03-28 05:12:36'),
(733, '40151', 'admin', 'Logged in', '2026-03-28 05:46:41'),
(734, '00000', '', 'Logged in', '2026-04-10 17:40:16'),
(735, '40151', 'admin', 'Logged in', '2026-04-10 17:52:57'),
(736, '40151', 'admin', 'Logged in', '2026-04-10 18:31:35'),
(737, '40045', 'admin', 'Logged in', '2026-04-10 18:55:02'),
(738, '40193', 'superadmin', 'Logged in', '2026-04-10 19:06:13'),
(739, '40151', 'admin', 'Logged in', '2026-04-10 19:07:46'),
(740, '40193', 'superadmin', 'Logged in', '2026-04-10 19:41:07'),
(741, '40193', 'superadmin', 'Logged in', '2026-04-10 20:09:02'),
(742, 'system', '', 'Evaluation auto turned OFF (end date reached)', '2026-04-10 20:10:07'),
(743, '40193', 'superadmin', 'Logged in', '2026-04-10 21:03:24'),
(744, '40193', 'superadmin', 'Logged in', '2026-04-12 12:08:58'),
(745, '221-0388-1', 'student', 'Logged in', '2026-04-12 12:17:05'),
(746, '40193', 'superadmin', 'Logged in', '2026-04-12 12:17:19'),
(747, '40193', 'superadmin', 'Evaluation turned on | Start: 2026-04-12 | End: 2026-04-15', '2026-04-12 12:17:30'),
(748, '221-0388-1', 'student', 'Rated 97.33% for ISBA 105 handled by RUFO AGALOOS BARO', '2026-04-12 12:19:27'),
(749, '221-0388-1', 'student', 'Rated 93.33% for ISAE 107 handled by RHODA MARQUEZ LILAN', '2026-04-12 12:24:57'),
(750, '221-0388-1', 'student', 'Rated 93.33% for ISBA 105 handled by RUFO AGALOOS BARO', '2026-04-12 12:27:04'),
(751, '221-0388-1', 'student', 'Rated 93.33% for ISBA 105 handled by RUFO AGALOOS BARO', '2026-04-12 12:28:33'),
(752, '40151', 'admin', 'Logged in', '2026-04-12 12:38:41'),
(753, '40151', 'admin', 'Evaluated Faculty: FREDIZ WINDA F. BADUA for 2025-2026 1st Semester', '2026-04-12 12:47:27'),
(754, '40151', 'admin', 'Evaluated Faculty: LARMIE D. BARCELONA for 2025-2026 1st Semester', '2026-04-12 13:20:03'),
(755, '40151', 'admin', 'Evaluated Faculty: RUFO A. BARO for 2025-2026 1st Semester', '2026-04-12 13:23:51'),
(756, '221-0388-1', 'student', 'Rated 89.33% for ISAE 108 handled by DANIEL ALMOJUELA NERI', '2026-04-12 13:35:40');

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
(117, NULL, NULL, NULL, 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(118, 'Instructor II', NULL, NULL, NULL, NULL),
(119, NULL, 'Program Chair', NULL, NULL, NULL),
(120, NULL, NULL, '1-A', NULL, NULL),
(121, NULL, 'Staff', NULL, NULL, NULL),
(122, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', NULL),
(123, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'BACHELOR OF SECONDARY EDUCATION'),
(124, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'BACHELOR OF EARLY CHILDHOOD EDUCATION'),
(125, NULL, 'Head Instruction', NULL, NULL, NULL),
(129, NULL, 'Dean', NULL, NULL, NULL),
(130, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', NULL),
(131, NULL, NULL, NULL, 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'BACHELOR OF SCIENCE IN AGRICULTURAL AND BIOSYSTEMS ENGINEERING'),
(132, 'Associate Professor IV', NULL, NULL, NULL, NULL),
(133, NULL, NULL, '4-B', NULL, NULL),
(134, NULL, NULL, '4-A', NULL, NULL),
(135, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', NULL),
(136, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE'),
(137, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF SCIENCE IN BIOLOGY'),
(138, NULL, NULL, NULL, 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF GENERAL EDUCATION'),
(139, 'Instructor III', NULL, NULL, NULL, NULL),
(140, 'Assistant Professor II', NULL, NULL, NULL, NULL),
(141, 'Assistant Professor IV', NULL, NULL, NULL, NULL),
(142, 'Associate Professor V', NULL, NULL, NULL, NULL),
(143, 'Associate Professor II', NULL, NULL, NULL, NULL),
(144, 'Associate Professor III', NULL, NULL, NULL, NULL),
(145, 'Instructor I', NULL, NULL, NULL, NULL),
(146, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'BACHELOR IN PHYSICAL EDUCATION'),
(148, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'BACHELOR OF ELEMENTARY EDUCATION'),
(149, NULL, NULL, NULL, 'COLLEGE OF EDUCATION', 'BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION'),
(150, NULL, NULL, NULL, 'COLLEGE OF VETERINARY MEDICINE', NULL),
(151, NULL, NULL, NULL, 'COLLEGE OF VETERINARY MEDICINE', 'DOCTOR OF VETERINARY MEDICINE'),
(152, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', NULL),
(153, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'BACHELOR OF SCIENCE IN FORESTRY'),
(154, NULL, NULL, NULL, 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'BACHELOR OF SCIENCE IN AGROFORESTRY'),
(155, NULL, NULL, NULL, 'COLLEGE OF AGRICULTURE', NULL),
(156, NULL, NULL, NULL, 'COLLEGE OF AGRICULTURE', 'BACHELOR OF SCIENCE IN AGRICULTURE'),
(157, NULL, NULL, NULL, 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', NULL),
(158, NULL, NULL, NULL, 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', 'BACHELOR OF SCIENCE IN AGRIBUSINESS MANAGEMENT'),
(159, NULL, NULL, NULL, 'INSTITUTE OF ENVIRONMENTAL STUDIES', NULL),
(160, NULL, NULL, NULL, 'INSTITUTE OF ENVIRONMENTAL STUDIES', 'BACHELOR OF SCIENCE IN ENVIRONMENTAL SCIENCE'),
(161, NULL, 'Director', NULL, NULL, NULL),
(162, 'Assistant Professor III', NULL, NULL, NULL, NULL),
(163, 'Assistant Professor I', NULL, NULL, NULL, NULL),
(164, 'Associate Professor I', NULL, NULL, NULL, NULL),
(165, 'Professor I', NULL, NULL, NULL, NULL),
(166, 'Professor II', NULL, NULL, NULL, NULL),
(167, 'Professor III', NULL, NULL, NULL, NULL),
(168, 'Professor IV', NULL, NULL, NULL, NULL),
(169, 'Professor V', NULL, NULL, NULL, NULL),
(170, NULL, NULL, '1-B', NULL, NULL),
(171, NULL, NULL, '1-C', NULL, NULL),
(172, NULL, NULL, '1-D', NULL, NULL),
(173, NULL, NULL, '2-A', NULL, NULL),
(174, NULL, NULL, '2-B', NULL, NULL),
(175, NULL, NULL, '2-C', NULL, NULL),
(176, NULL, NULL, '2-D', NULL, NULL),
(177, NULL, NULL, '3-A', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(255) NOT NULL,
  `idnumber` varchar(255) NOT NULL,
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

INSERT INTO `admin` (`id`, `idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `position`, `role`, `status`, `faculty_rank`) VALUES
(13, '40005', 'THERESA', 'CRISPINO', 'CACHERO', '$2y$10$7ddHbp89iZO1TUPNADI7YeeoXSXU37LtR2rpNL0ipjRuJ9dDGrFIC', 'Program Chair', 'admin', 'active', 'Assistant Professor IV'),
(14, '40014', 'NEL BRYAN', 'CARIÑO', 'TUGELIDA', '$2y$10$iRU7UNcH2guHlzFGjnhR3etFBcRaR6.f8p/jBHYatizFT8pHF4xFO', 'Dean', 'admin', 'active', 'Assistant Professor I'),
(15, '40045', 'CHRISTIANNE GLORY', 'L', 'ARBOLLENTE', '$2y$10$AL3JqU.T22XvzvwruiOuTuIZGPLP1TOgB.oZOYgNWI79bTA8RF1Kq', 'Program Chair', 'admin', 'active', 'Instructor III'),
(16, '40050', 'LYNBELLE', 'CHAN', 'PASCUA', '$2y$10$zY3B9.cA1L3I/btGOpiEpuDfvsOPzS2OBT/LgINzG05GYucNc9X22', 'Program Chair', 'admin', 'active', 'Instructor I'),
(17, '40151', 'EDELITA', 'CORPUZ', 'EBUENGA', '$2y$10$Ltz8JeCc3EJ1vVtTPbSE5.rh3X7Rb4EEO28tQ69OLePJi78EZ0wu.', 'Dean', 'admin', 'active', 'Associate Professor V');

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
('40005', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF SCIENCE IN BIOLOGY'),
('40014', 'COLLEGE OF EDUCATION', 'BACHELOR IN PHYSICAL EDUCATION'),
('40014', 'COLLEGE OF EDUCATION', 'BACHELOR OF EARLY CHILDHOOD EDUCATION'),
('40014', 'COLLEGE OF EDUCATION', 'BACHELOR OF ELEMENTARY EDUCATION'),
('40014', 'COLLEGE OF EDUCATION', 'BACHELOR OF SECONDARY EDUCATION'),
('40014', 'COLLEGE OF EDUCATION', 'BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION'),
('40045', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF GENERAL EDUCATION'),
('40050', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE'),
('40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS');

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
  `evaluation_date` datetime DEFAULT current_timestamp(),
  `answers` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation`
--

INSERT INTO `admin_evaluation` (`id`, `evaluator_id`, `evaluatee_id`, `evaluator_position`, `academic_year`, `semester`, `total_score`, `computed_rating`, `comments`, `college`, `evaluation_date`, `answers`) VALUES
(85, '40151', '40193', 'Dean', '2025-2026', '1st Semester', 69, 92.00, '', 'COLLEGE OF EDUCATION', '2026-04-12 12:47:27', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":5,\"q_7\":4,\"q_8\":5,\"q_9\":4,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":4,\"q_15\":5}'),
(86, '40151', '02860', 'Dean', '2025-2026', '1st Semester', 68, 90.67, '', 'COLLEGE OF ARTS AND SCIENCES', '2026-04-12 13:20:03', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":4,\"q_7\":5,\"q_8\":4,\"q_9\":5,\"q_10\":4,\"q_11\":5,\"q_12\":4,\"q_13\":5,\"q_14\":5,\"q_15\":4}'),
(87, '40151', '40180', 'Dean', '2025-2026', '1st Semester', 70, 93.33, '', 'COLLEGE OF INFORMATION SYSTEMS', '2026-04-12 13:23:51', '{\"q_1\":5,\"v_1\":[\"Daily time record\",\"Informal interview with students\"],\"q_2\":5,\"v_2\":[\"Receipts or Acknowledgment emails\"],\"q_3\":4,\"v_3\":[\"LMS logs\"],\"q_4\":4,\"v_4\":[\"LMS logs\",\"Informal interviews\"],\"q_5\":5,\"v_5\":[\"Consultation logs\"],\"q_6\":4,\"v_6\":[\"Graded work\",\"Consultation logs\"],\"q_7\":5,\"v_7\":[\"Instructional Materials\"],\"q_8\":5,\"v_8\":[\"Presentations\"],\"q_9\":5,\"v_9\":[\"Daily life examples\"],\"q_10\":4,\"v_10\":[\"LMS logs\"],\"q_11\":5,\"v_11\":[\"Rubrics\"],\"q_12\":5,\"v_12\":[\"Classroom observation\"],\"q_13\":5,\"v_13\":[\"Advisory logs\"],\"q_14\":4,\"v_14\":[\"Rubrics\",\"Informal interviews\"],\"q_15\":5,\"v_15\":[\"Observations\"]}');

-- --------------------------------------------------------

--
-- Table structure for table `admin_evaluation_categories`
--

CREATE TABLE `admin_evaluation_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `order_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation_categories`
--

INSERT INTO `admin_evaluation_categories` (`id`, `category_name`, `order_by`) VALUES
(1, 'Manage of Teaching and Learning', 1),
(2, 'Content Knowledge, Pedagogy and Technology', 2),
(3, 'Commitment and Transparency', 3);

-- --------------------------------------------------------

--
-- Table structure for table `admin_evaluation_questions`
--

CREATE TABLE `admin_evaluation_questions` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `verifications` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `order_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_evaluation_questions`
--

INSERT INTO `admin_evaluation_questions` (`id`, `category_id`, `question_text`, `verifications`, `status`, `order_by`) VALUES
(1, 1, 'Comes to class on time regularly.', 'Daily time record, Faculty schedule, Informal interview with students', 'active', 1),
(2, 1, 'Submits updated syllabus, grade sheets, and other required reports on time.', 'Submission logs, Receipts or Acknowledgment emails', 'active', 2),
(3, 1, 'Maximizes the allocated time/learning hours effectively.', 'Syllabus, Learning plan, LMS logs, Classroom observations', 'active', 3),
(4, 1, 'Provide appropriate learning activities that facilitate critical thinking and creativity of students.', 'Course syllabus, LMS logs, Informal interviews', 'active', 4),
(5, 1, 'Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.', 'Work samples, Consultation logs, Classroom observations', 'active', 5),
(6, 1, 'Communicates constructive feedback to students for their academic growth.', 'Graded work, Consultation logs, LMS logs', 'active', 6),
(7, 2, 'Demonstrates extensive and broad knowledge of the subject/course.', 'Syllabus, Learning plan, Instructional Materials', 'active', 1),
(8, 2, 'Simplifies complex ideas in the lesson for ease of understanding.', 'Lecture notes, Presentations, Observations', 'active', 2),
(9, 2, 'Integrates contemporary issues and developments in the discipline and/or daily life activities in the syllabus.', 'Syllabus, Webinars, Daily life examples', 'active', 3),
(10, 2, 'Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.', 'Multimedia, LMS logs, Classroom observations', 'active', 4),
(11, 2, 'Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes', 'Assessment tools, Rubrics, Samples', 'active', 5),
(12, 3, 'Recognizes and values the unique diversity and individual differences among students.', 'IMs, Classroom observation, Student diversity notes', 'active', 1),
(13, 3, 'Assist students with their learning challenges during consultation hours.', 'Advisory logs, Consult hours, LMS logs', 'active', 2),
(14, 3, 'Provide immediate feedback on student outputs and performance.', 'Rubrics, Feedback, Informal interviews', 'active', 3),
(15, 3, 'Provides transparent and clear criteria in rating student\'s performance.', 'Syllabus, Student outputs, Observations', 'active', 4);

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
(61, '40151', '40193', '1st Semester', '2025-2026', 69, 92.00, '', '2026-04-12 12:47:27', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":5,\"q_7\":4,\"q_8\":5,\"q_9\":4,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":4,\"q_15\":5}'),
(62, '40151', '02860', '1st Semester', '2025-2026', 68, 90.67, '', '2026-04-12 13:20:03', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":4,\"q_7\":5,\"q_8\":4,\"q_9\":5,\"q_10\":4,\"q_11\":5,\"q_12\":4,\"q_13\":5,\"q_14\":5,\"q_15\":4}'),
(63, '40151', '40180', '1st Semester', '2025-2026', 70, 93.33, '', '2026-04-12 13:23:51', '{\"q_1\":5,\"v_1\":[\"Daily time record\",\"Informal interview with students\"],\"q_2\":5,\"v_2\":[\"Receipts or Acknowledgment emails\"],\"q_3\":4,\"v_3\":[\"LMS logs\"],\"q_4\":4,\"v_4\":[\"LMS logs\",\"Informal interviews\"],\"q_5\":5,\"v_5\":[\"Consultation logs\"],\"q_6\":4,\"v_6\":[\"Graded work\",\"Consultation logs\"],\"q_7\":5,\"v_7\":[\"Instructional Materials\"],\"q_8\":5,\"v_8\":[\"Presentations\"],\"q_9\":5,\"v_9\":[\"Daily life examples\"],\"q_10\":4,\"v_10\":[\"LMS logs\"],\"q_11\":5,\"v_11\":[\"Rubrics\"],\"q_12\":5,\"v_12\":[\"Classroom observation\"],\"q_13\":5,\"v_13\":[\"Advisory logs\"],\"q_14\":4,\"v_14\":[\"Rubrics\",\"Informal interviews\"],\"q_15\":5,\"v_15\":[\"Observations\"]}');

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
(5, 'COLLEGE OF ARTS AND SCIENCE', 'Bachelor of Arts in English Language', 'www.dmmmsu.edu.ph', '090909', ''),
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
(195, '221-0388-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISBA 105', 'Analytics Application', '2025-2026', '40180', 70.00, 93.33, '', '2026-04-12 04:28:33', '1st Semester', '4-B', 'no'),
(196, '221-0388-1', 'COLLEGE OF INFORMATION SYSTEMS', 'ISAE 108', 'Technoprenuership', '2025-2026', '40182', 67.00, 89.33, 'Good', '2026-04-12 05:35:40', '1st Semester', '4-B', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_categories`
--

CREATE TABLE `evaluation_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `order_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_categories`
--

INSERT INTO `evaluation_categories` (`id`, `category_name`, `order_by`) VALUES
(1, 'Manage of Teaching and Learning', 1),
(2, 'Content Knowledge, Pedagogy and Technology', 2),
(3, 'Commitment and Transparency', 3);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_questions`
--

CREATE TABLE `evaluation_questions` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `order_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_questions`
--

INSERT INTO `evaluation_questions` (`id`, `category_id`, `question_text`, `status`, `order_by`) VALUES
(1, 1, 'Comes to class on time regularly.', 'active', 1),
(2, 1, 'Explains learning outcomes, expectations, grading system, and various requirements of the subject/course.', 'active', 2),
(3, 1, 'Maximizes the allocated time/learning hours effectively.', 'active', 3),
(4, 1, 'Facilitates students to think critically and creatively by providing appropriate learning activities.', 'active', 4),
(5, 1, 'Guides students to learn on their own, reflect on new ideas and experiences, and make decisions in accomplishing given tasks.', 'active', 5),
(6, 1, 'Communicates constructive feedback to students for their academic growth.', 'active', 6),
(7, 2, 'Demonstrates extensive and broad knowledge of the subject/course.', 'active', 1),
(8, 2, 'Simplifies complex ideas in the lesson for ease of understanding.', 'active', 2),
(9, 2, 'Relates the subject matter to contemporary issues and developments in the discipline and/or daily life activities.', 'active', 3),
(10, 2, 'Promotes active learning and student engagement by using appropriate teaching and learning resources including ICT Tools and platforms.', 'active', 4),
(11, 2, 'Uses appropriate assessment (projects, exams, quizzes, etc.) to align with the learning outcomes.', 'active', 5),
(12, 3, 'Recognizes and values the unique diversity and individuality difference among students.', 'active', 1),
(13, 3, 'Assist students with their learning challenges during consultation hours.', 'active', 2),
(14, 3, 'Provide immediate feedback on student outputs and performance.', 'active', 3),
(15, 3, 'Provides transparent and clear criteria in rating student\'s performance.', 'active', 4),
(16, 2, 'testing', 'inactive', 6);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_rating_scales`
--

CREATE TABLE `evaluation_rating_scales` (
  `id` int(11) NOT NULL,
  `scale_value` int(11) NOT NULL,
  `qualitative_description` varchar(255) NOT NULL,
  `operational_definition` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_rating_scales`
--

INSERT INTO `evaluation_rating_scales` (`id`, `scale_value`, `qualitative_description`, `operational_definition`) VALUES
(1, 5, 'Always manifested', 'The behavior, characteristic, or condition is consistently and unfailingly demonstrated in all relevant situations or instances. There is no observed deviation from this pattern. Operationally, this could mean occurring in 95-100% of observed opportunities or instances.'),
(2, 4, 'Often manifested', 'The behavior, characteristic, or condition is demonstrated frequently, though occasional instances of non-manifestation may occur. Operationally, this could mean occurring in 60-94% of observed opportunities or instances.'),
(3, 3, 'Sometimes manifested', 'The behavior, characteristic, or condition is demonstrated intermittently or irregularly, with an approximately equal likelihood occurrence and non-occurrence. Operationally, this could mean occurring in 40-60% of observed opportunities or instances.'),
(4, 2, 'Seldom manifested', 'The behavior, characteristic, or condition is demonstrated infrequently and is generally absent in most relevant situations. Operationally, this could mean occurring in 25-40% of observed opportunities or instances.'),
(5, 1, 'Rarely manifested', 'The behavior, characteristic, or condition is almost never demonstrated, with only isolated or exceptional instances of occurrence. Operationally, this could mean occurring in 0-24% of observed opportunities or instances.');

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
(1, '1st Semester', '2025-2026', 1, '2025-11-28 14:56:20');

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
(7, 'on', '40193', '2026-04-12', '2026-04-15');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(255) NOT NULL,
  `idnumber` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) DEFAULT NULL,
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

INSERT INTO `faculty` (`id`, `idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `faculty_rank`, `role`, `status`) VALUES
(40, '00421', 'KRISTINE MAYLAN', 'SABADO', 'ESPERO', '$2y$10$6sDggnKta7b7xCdaP1Ib/eIe85tOjuo1lb3v7am4JtP4vZ9M1eG5W', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE', 'Instructor I', 'faculty', 'active'),
(41, '00711', 'MARK KENNETH', 'MOLINA', 'MANGASER', '$2y$10$STXSd0A4CyFoab/G/ADv7.MOKWUTUpQTz66kdgIfF.lcval9XV5b6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Instructor I', 'faculty', 'active'),
(42, '02860', 'LARMIE', 'DOSONO', 'BARCELONA', '$2y$10$Z/Bqwy3zpmgkVX6A7uoo7eCyBX01V64mIOyjhRbxa/1FHyCkxUfve', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF GENERAL EDUCATION', 'Instructor I', 'faculty', 'active'),
(43, '0716', 'REINER JAN', 'AGUSTIN', 'CASTELO', '$2y$10$fRUv6hw/4/5ZkrIF8VvoKOMdomyPzx6YnjwyGYzeaqenfhqK9h7Fa', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF SCIENCE IN BIOLOGY', 'Instructor I', 'faculty', 'active'),
(44, '40005', 'THERESA', 'CRISPINO', 'CACHERO', '$2y$10$hro5VK.m8yKAi1OFGh9ZXOMmitsthk4Bv7JsLdBdNN1qCGDVp0QRW', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF SCIENCE IN BIOLOGY', 'Assistant Professor IV', 'faculty', 'active'),
(45, '40014', 'NEL BRYAN', 'CARIÑO', 'TUGELIDA', '$2y$10$iRU7UNcH2guHlzFGjnhR3etFBcRaR6.f8p/jBHYatizFT8pHF4xFO', 'COLLEGE OF EDUCATION', 'BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION', 'Assistant Professor I', 'faculty', 'active'),
(46, '40023', 'SHALIMAR', 'LICUDINE', 'NAVALTA', '$2y$10$hyxK8egZRYMGC1dzTm6.iu5YXk02AQBd4iBhfXKMZk34NDUFzWzoC', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor V', 'faculty', 'active'),
(47, '40045', 'CHRISTIANNE GLORY', 'L', 'ARBOLLENTE', '$2y$10$c00mYOVf8FHBRFKI/6/WYOMOdkvOMFqldqXz5DiwU/OsF43AH5oQq', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF GENERAL EDUCATION', 'Instructor III', 'faculty', 'active'),
(48, '40050', 'LYNBELLE', 'CHAN', 'PASCUA', '$2y$10$FugDB2YfrmFODnV9UcrsZOpjZvW4djSXZJs0r3XOmTj3FcgPxmbF2', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE', 'Instructor I', 'faculty', 'active'),
(49, '40094', 'MARICEL', 'OFICIAR', 'PRE', '$2y$10$7Q4SgyxGufI6HtGAQ9Ssa..7t6bpDWmjcSvphzG6Cd3cfXWbchyPy', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Instructor I', 'faculty', 'active'),
(50, '40112', 'JHONALYN', 'BAUTISTA', 'LARDIZABAL', '$2y$10$HnDfJjH8lTY4stHShIsMeeNMZzoYWR6jnL8cP1n.BHPLu1fOczE0i', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Instructor III', 'faculty', 'active'),
(51, '40151', 'EDELITA', 'CORPUZ', 'EBUENGA', '$2y$10$m1ouEV9QEoCimrW3268Uy.XPEzVd8HB.g7LoE1ybJuGVGmTc7nILG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor V', 'faculty', 'active'),
(52, '40180', 'RUFO', 'AGALOOS', 'BARO', '$2y$10$nhjJHEGLIeMTBT/zBWXB3.iPLEMiiCMy4OH7EsPUt9kE01Mhqd9a.', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor V', 'faculty', 'active'),
(53, '40182', 'DANIEL', 'ALMOJUELA', 'NERI', '$2y$10$Lzvob3L7hGF1aqMPlcgeNuVg0HgBYFqbk.degfuS52jDPOV0gphNS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor II', 'faculty', 'active'),
(54, '40184', 'HERVE', 'ESTRADA', 'ORPILLA', '$2y$10$Vgm05e3hAx2417BIfAIS4uEpPTWzLOu8KTGMhSZs3W1QAcF.uiUtu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor III', 'faculty', 'active'),
(55, '40193', 'FREDIZ WINDA', 'FERRER', 'BADUA', '$2y$10$FXc/x9GWqxqXqC2mf9JO9O0Pfy6o0DbPD6myd8YovtqI59UxkgxSS', 'COLLEGE OF EDUCATION', 'BACHELOR IN PHYSICAL EDUCATION', 'Assistant Professor IV', 'faculty', 'active'),
(56, '40207', 'RHODA', 'MARQUEZ', 'LILAN', '$2y$10$Dh5Dz3iRIRcg7N.DGYHzou0x1NxTkdgZjq8X/PnqgcPMevcafpqNC', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Associate Professor V', 'faculty', 'active'),
(57, '40413', 'JESSIE', 'BAUTISTA', 'VALLECERA', '$2y$10$JcHvquUKJ96KyfD0Ri8cXODOkZTAxfBWcI0uB86tsV.KBmmLiOqfS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Instructor III', 'faculty', 'active'),
(59, '00424', 'STEPHAN', '', 'KUPSCH', '$2y$10$LXEuveIvZhxyYXt9JB8otejLdYxRkOgT4x/rg4kUjPvxmPNVANtdG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', 'Instructor I', 'faculty', 'active'),
(62, '114001', 'JOMAR', 'LOZANO', 'ABAN', '$2y$10$m9AoTRhBj1GK0nf7GmoqIeP.b8.aEv69AA050gbucfGYUOf.6rjNG', 'COLLEGE OF EDUCATION', 'BACHELOR OF SECONDARY EDUCATION', 'Instructor I', 'faculty', 'active'),
(63, '111001', 'JOEL', 'C', 'AGULLO', '$2y$10$TdFQ8t84zCfmXLrhNW5iV..JoBM9S81H7vqWg0VHSK4GTpRL8YBNC', 'COLLEGE OF EDUCATION', 'BACHELOR IN PHYSICAL EDUCATION', 'Instructor I', 'faculty', 'active'),
(64, '198001', 'SIMILEE', 'M', 'GARCIA', '$2y$10$1z0EA/HZ8ooIAngIGOsfue20Ug9uZlQpeExt8lbUo5x92hK2BkgVm', 'COLLEGE OF AGRICULTURE', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'Instructor I', 'faculty', 'active'),
(65, '115001', 'JULIUS CAESAR', 'M', 'AMOYEN', '$2y$10$wJ7WTCVGv82tRKuRCUSO5ewge2wiR9CbI7qVleBCry..1lSxAYQxW', 'COLLEGE OF AGRICULTURE', 'BACHELOR OF SCIENCE IN AGRICULTURE', 'Instructor I', 'faculty', 'active'),
(66, '122001', 'AGULUS', 'N', 'AGOTO', '$2y$10$7w1kQ/fk1o0/Tl4jUZyNVe8kDcr/CNcq9KyhVOVfp1bchZf49.3jK', 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'BACHELOR OF SCIENCE IN AGROFORESTRY', 'Instructor I', 'faculty', 'active'),
(67, '125001', 'DYRAH RUTA', 'D', 'BEHONG', '$2y$10$2o4CL0L4rw/1IxxoObz18OLQOIorlySaYzKmNOJBbF3ulrfleGXE.', 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'BACHELOR OF SCIENCE IN FORESTRY', 'Instructor I', 'faculty', 'active'),
(68, '196001', 'VICKY', 'A', 'AGPASA', '$2y$10$EDT2XXlgWiwgluEjddtHL.x0doCjZyl15Qjsrp5njLVheSaY6l8vK', 'COLLEGE OF VETERINARY MEDICINE', 'DOCTOR OF VETERINARY MEDICINE', 'Instructor I', 'faculty', 'active'),
(69, '118008', 'ELIZABETH', 'L', 'TANGANGCO', '$2y$10$G030nepNzkh3IyEA8u0mOeATnGY2ta4r9jkMTiePoQSQZFhLF4JHu', 'COLLEGE OF VETERINARY MEDICINE', 'DOCTOR OF VETERINARY MEDICINE', 'Instructor I', 'faculty', 'active'),
(72, '117001', 'ROSALINDA', 'L', 'ABAD', '$2y$10$x8nIHCen.TLXyq0Tt8kcGu.atMhnX6RR/QhdGnPwoU32y5sMlR77i', 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'BACHELOR OF SCIENCE IN AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Instructor I', 'faculty', 'active'),
(73, '109001', 'RENATO', 'B', 'AGUILAR', '$2y$10$I67LL7z.NFXc/CmSszZ1dOPn4IyPZnG3p0fBwIOf3kguW8UJjKjgC', 'INSTITUTE OF AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'BACHELOR OF SCIENCE IN AGRICULTURAL AND BIOSYSTEMS ENGINEERING', 'Instructor I', 'faculty', 'active'),
(78, '104002', 'ARNELIE', 'G', 'LAQUIDAN', '$2y$10$dvNKRnwEiXGY7L90FnW8AOXR6e5HOmU/w/.8fxyFxiAqEcXPLZXV.', 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', 'BACHELOR OF SCIENCE IN AGRIBUSINESS MANAGEMENT', 'Instructor I', 'faculty', 'active'),
(79, '112008', 'VENELYN', 'L', 'BERSAMIRA', '$2y$10$PmbyxMZOD5jGfP6RfbhE5usEqQr6AYe0SfM7B.ydQLfalhzWsxVFe', 'INSTITUTE OF AGRIBUSINESS MANAGEMENT', 'BACHELOR OF SCIENCE IN AGRIBUSINESS MANAGEMENT', 'Instructor I', 'faculty', 'active'),
(80, '113002', 'DESIREE', 'A', 'VILAR', '$2y$10$vM1i4028Pc6k.lx16YQD3OYzyQoKSSM2auHHXzoJhXJa04opzw1J6', 'INSTITUTE OF ENVIRONMENTAL STUDIES', 'BACHELOR OF SCIENCE IN ENVIRONMENTAL SCIENCE', 'Instructor I', 'faculty', 'active'),
(81, '114002', 'JOCELYN', 'C', 'ANDRADA', '$2y$10$8mSf6qm3Ie0AoY4VpUAlN.HvFZEvjhiWyg4FyAzyF8Enpidjog1K6', 'INSTITUTE OF ENVIRONMENTAL STUDIES', 'BACHELOR OF SCIENCE IN ENVIRONMENTAL SCIENCE', 'Instructor I', 'faculty', 'active'),
(82, '1', 'JOMAR', 'LOZANO', 'ABAN', '$2y$10$wWBz2fr4KsJisfwwkpo2iOmQwBVwfhabcxJcdHvdF4pZwYYJKEuxe', 'COLLEGE OF EDUCATION', 'BACHELOR OF SECONDARY EDUCATION', 'Instructor I', 'faculty', 'active'),
(85, '123', '123', '123', '123', '$2y$10$fFBH0bLOVyVkh7pV8tuyLewMJsgY.4bhmvOeNWXi7itVhcb.VWpgq', 'COLLEGE OF AGROFORESTRY AND FORESTRY', 'BACHELOR OF SCIENCE IN AGROFORESTRY', 'Assistant Professor III', 'faculty', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_dev_plan`
--

CREATE TABLE `faculty_dev_plan` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(50) DEFAULT NULL,
  `semester` varchar(50) NOT NULL,
  `academic_year` varchar(50) NOT NULL,
  `areas_improvement` text DEFAULT NULL,
  `proposed_activities` text DEFAULT NULL,
  `action_plan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_dev_plan`
--

INSERT INTO `faculty_dev_plan` (`id`, `faculty_id`, `semester`, `academic_year`, `areas_improvement`, `proposed_activities`, `action_plan`, `created_at`, `updated_at`) VALUES
(1, '40207', '1st Semester', '2025-2026', 'In teaching sided', 'O yeah', 'Nothing', '2025-12-07 13:49:13', '2025-12-07 13:54:27');

-- --------------------------------------------------------

--
-- Table structure for table `registrar`
--

CREATE TABLE `registrar` (
  `id` int(255) NOT NULL,
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

INSERT INTO `registrar` (`id`, `idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `status`, `role`, `employment_role`, `faculty_rank`) VALUES
(1, '00000', 'Account', 'Creator', 'System', '$2y$10$oIAZEL68lgR8cOOc6YP5d.UA5GaQwnCLtrSP/7cv03CNmGt6fylRm', NULL, NULL, 'active', 'registrar', 'Non-Teaching', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(255) NOT NULL,
  `idnumber` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mid_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `college` varchar(255) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `section` varchar(11) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'student'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `college`, `program`, `section`, `role`) VALUES
(156, '201-0326-1', 'RODEL ANDRE', 'BUMATAY', 'CARDONA', '$2y$10$.3J8f0HN56qVyud..gugsOsmAykyg2DfS7eBlHfeXM6HEkZB1wEQ2', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(157, '202-0021-1', 'REN REN', 'ALIMPIA', 'DONATO', '$2y$10$hx3jjm..uGUIVfq4PX0w6u7jKAi2cNkSgm/D2O9SvvYBAVgseOC.O', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(158, '211-0004-1', 'SYMPHONY ANGEL', 'MANALO', 'VIVIT', '$2y$10$Zb2BkcFRnQEvUlsxZRKUEO6CRvNQheG0XKgl.iLr4sL6RbFAOtnui', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(159, '211-0161-1 ', 'HEROBIN', 'MIDDLENAME', 'MARIÑAS', '$2y$10$NW9ntaa01AYex8oGt5qCQuNsLLdldbq5lvdyBxY1sAaX4YB4KfW6u', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(160, '211-0164-1', 'EMMA CECILLE', 'ARIOLA', 'BADUA', '$2y$10$kixnr6W1iveVo3C1AVL9fuxeTvehTVPlcPbsyEerfU3kyr3S5svLa', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(161, '211-0174-1', 'JOHN GODWIN', 'MIDDLENAME', 'TANINGCO', '$2y$10$TyLgDbCNfofpYC6M8QOIa.v99.8.TT70QQv2fY3Lwf2rgEt/w0J1i', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(162, '211-0805-1', 'GOHAN', 'ESPE', 'GAMBOA', '$2y$10$BCJGB/C2AVsoe3rUVBU7r.cv6Gjt40eNBnq0R5K1C/XH097iOxUxy', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(163, '221-0411-1', 'CLOUIE ANN', 'MIDDLENAME', 'ACLINEN', '$2y$10$PnJUVdK7rvXHoB.p1LQ8KeVMaVud5x3crYA/Jx3TsW305PCbSEvRO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(164, '221-0005-1', 'ALEXANDREA ', 'JEAN', 'GARCIA', '$2y$10$SHZx36mhnnIZv5gTKSPNoOHrS/poBF/.i6e72Yl58TensgBHI6a0i', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(165, '221-0006-1', 'RENZ ALLEN', 'BAUTISTA', 'MILLER', '$2y$10$Mr8YA1x/ZATqPLAEnexuuucKbVAscbj0I.PUM6dtO.oLYe.ru70cK', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(166, '221-0026-1', 'JEANDRA JOY', 'OPINALDO', 'ORFIANO', '$2y$10$Fo32ZIz4C71NNDETq3MjKuvhdTmouK0opssTw6LybchIEKVyoWROu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(167, '221-0070-1', 'HONEY BOY', 'BUCSIT', 'CORIAL', '$2y$10$E7n0kwQRL38O.oXNMtLtTe9b/KRuFG2CSQBAETxHs43QTNzHfg6h6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(168, '221-0079-1', 'PROCXEL', 'CLARO', 'ALMOITE', '$2y$10$U8jHoYQT6rj91xt6k866fO8zh2ms.OLvKjGsJECHCqWW9AthMqIpi', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(169, '221-0080-1', 'IKA IVANA', 'BUCCAT', 'LICUDAN', '$2y$10$ibS37rjJxS/j7TxicrjCeeXA75OsUci.xpIzqQFNjfWGyeyrVSfk2', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(170, '221-0082-1', 'AERONE', 'MIDDLENAME', 'IMBUEDO', '$2y$10$UvNNBqE4Ys5hsezzk6PPo.c6Y2pUu6/Tpw0oySFPWVSQw1I05Y9xC', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(171, '221-0137-1', 'CHRISTIAN JOHN', 'MIDDLENAME', 'NAVARRO ', '$2y$10$nMC4TqP/R2MhgrJlUjXMA.eJRm3x3eu6pvPT.hddWpknrL8Sr8bDG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(172, '221-0146-1', 'BANIELY', 'MIDDLENAME', 'PAJARIT', '$2y$10$WOUVXxwSUsjrSVkAwXgeSeG.91dcYr9sFvBRGVTWCfnamYgku7Zqi', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(173, '221-0179-1', 'JAIRA MITCH', 'BUEN', 'RIÑON', '$2y$10$ARMg/0iwbzrlqma0ylxRJupczE/zCKvBoZ1VnY4mfhGH9GhOsSMFG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(174, '221-0180-1', 'IRISH JINGLE', ' ADER', 'RIÑON', '$2y$10$mRJUBZ1JvEIcGsGBPernd.nl0B4InRrAGUFHq/l07Wsp0yXcOVoVG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(175, '221-0325-1', 'PRYCE', 'CABANES', 'CABAGBAG', '$2y$10$tIfoQlQKrp/Gu7.8Tu527.MV1LjoOhzOn34WtR2x3mnTY4CHCwere', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(176, '221-0368-1', 'VENCH AXEL ROSS', 'LIBED', 'GLIAM', '$2y$10$OQTkK28o07IJhLfgH5KlFev09/Pk1NZRjHSgqLL7SFABawfCM7t0W', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(177, '221-0387-1', 'CLARK JOSHUA', ' VELASCO', 'ROJAS', '$2y$10$nkDCfWUECOm578UrUqko7OsHIx9gtBiIjhopiDWHCim1.ql3nHxSS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(178, '221-0388-1', 'CHARLS ADONIS', 'VELASCO', 'ROJAS', '$2y$10$FTMsMRx5rgm3IwfnIcAimuUVJNsZwnRKWJFhXzICrPF8i95RshtZy', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(179, '221-0422-1', 'MARK KRISTIAN', 'PARCHAMENTO', 'LAGMAN', '$2y$10$b5nP9UgdeMj7.4zqlWTKru3s4UYQird0is4BuW8DImj5c27iwSKEu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(180, '221-0476-1', 'ANA MARIE', 'EDIC', 'ALMIROL', '$2y$10$EDWbLzNKh14bSUpBXV0ew.cF4CagStZ88wI6BwybgM5yaVpGeyI3i', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(181, '221-0478-1', 'CLAIRE', 'V', 'ALMIROL', '$2y$10$iqgnv81CNjVdkIcQa8PTj.2j/BoUPLUE/1uefiGZxWrixcJV//UaG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(182, '221-0484-1', 'LYZETTE', ' S', 'OLIVEROS', '$2y$10$PNfFYBK/dZDzg.KGcvgzKOcBL2SpDD.bXfh7Urpn3SDjwOnALBAKi', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(183, '221-0485-1', 'LORENZO', 'NODORA', 'CARIAGA', '$2y$10$R0.pcCXXQ4mkYqHlVy7l5.kWhuaFoEgOgB6ypmH/d0EdFjUfnrSCO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(184, '221-0497-1', 'TYRON', 'DOBLE', 'CRUZ', '$2y$10$cFPabS1D63SN0epwj0zlHOMg/ka6mNznxKcxVVOZbPUEHxsq6FzDO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(185, '221-0557-1', 'GIOVANI', 'BATALLER', 'VALDRIZ', '$2y$10$1za0RMhuP.5pXLDZ8PqEgOwnxl2hYfgi3Fzupz4PZbHYYuE2wQkDO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(186, '221-0626-1', 'KRISTELLE', 'PACLEB', 'YARA', '$2y$10$yx8/CGYu41B6wuS3dpqq/u/7ekMUnP9UzFTYqUm9fEjASlzHNZ.Qe', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(187, '221-0629-1', ' KARYL ZYRRA', 'TADIJE', 'ANCHETA', '$2y$10$MZhmnGOakI/J.7bpJhQRS.qMRgSqEAdvsJupP0enOfbOGws0GTqIG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(188, '221-0648-1', 'ANDREI KYLE', 'MIDDLENAME', 'LEGASPI', '$2y$10$Ig4Z1Pq5irU/qk0DE1IrnuhIJRiZ1QemDTFxouolt.m2hM.5lysem', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(189, '221-0733-1', 'VAN APOLLO', 'UNTAL', 'MON', '$2y$10$iY2wgMgnu2aTb/b01Sk.luRLQdCRyomk7PP6U5vUqWFJE020lUEVS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(190, '221-0743- 1', 'JOSE CHRISTOPHER', 'GACRAMA', 'APOCERO', '$2y$10$47xr9TsKglie3rBknlcmguK1ijAJfSOAuQ.eAB5b16rG9ag4c6ORW', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(191, '221-0778-1', 'SHASNEY', 'SABADO', 'ALMINIANA', '$2y$10$EwOwCR9oSwbA1KhLLfLk9eAQyq6lm/UU/.KkcpZqJb/6nEoSO3/vS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(192, '221-0852-1', 'JESIRY', 'BATI', 'SUBLI', '$2y$10$t.fZQFjix1SRep./qelqzeO3hMUva6Wq4Cc27z0j.sBqwpVNhc.L6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(193, '221-0867-1 ', 'ARIANNE YESHA', 'MONES', 'MARCELLA', '$2y$10$Dn1yC8JIxxR7DP5l/ekJFeB4X6y13CCVWOkeRaV80xMbw2SKIMVCi', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(194, '221-0926-1', 'SHINA MARIE', 'SOBREMONTE', 'APOLINAR', '$2y$10$TqYctaOEdA2zRhMT/3P5g.biCBqQKOZy2TdpTF3cxQU8dYo/a4XIS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-A', 'student'),
(195, '222-0014-1', 'JEREMIAH', 'VALERIO', 'LUBIAN', '$2y$10$C8pKTwxlP5xwUOYat/QxBOVU46YZUwPc8Uwp3QbB3gnPgD.Cmu3lO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(196, '231-0884-1', 'DELWIN', 'N', 'CALICA', '$2y$10$md7ym/5eHvgqyduRam.f4O.n5rUbWSSM2TzEphXQ/R7CXkqlKT4Ym', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(197, '231-0923-1', 'VIEN', 'IVORY', 'MARZAN', '$2y$10$fDT3AF2rplrwln2oohZJVOcLJjovjIh4KnAKlAtIjcgmxKvFa4YHm', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '4-B', 'student'),
(198, '251-0004-1', 'UNIKEY', 'GALVEZ', 'VISCA', '$2y$10$Ld1ExLkdvda9TGdauZvCquuB.sOe/u1BFurAgdOr7E/hE1ix7KylG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(199, '251-0008-1', 'JERMAIN', 'MALUBAY', 'MIQUE', '$2y$10$H0nJlh29.ChGKtN54EoEiOCaNzp9HaG6TXGZ2H/OFiICB4cDLUP62', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(200, '251-0009-1', 'RHODNEY JOSH', 'VALDEZ', 'OLIVAR', '$2y$10$gy0oWdcDfrph5NPzkDfmF.yQHHmKD33kvkACt8r/0mB96uVQY5mAC', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(201, '251-0010-1', 'JULIUS', 'REOLA', 'CAMAT', '$2y$10$RWKveJiJhhhfhiiODrq2lei0PFMhJ751pXiGLdbdIEFkRw8vhXT3G', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(202, '251-0019-1', 'PRINCESS NICOLE', 'SALINAS', 'ORDINARIO', '$2y$10$ZVayBsC/DJ7.DyAov.FZC.jC69YKsNMmm/ffcgGuJlQaKvVbPtk1e', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(203, '251-0042-1', 'CEDRIC JYHN', 'ABELLERA', 'GALVEZ', '$2y$10$j3ea1UNjl3dgRUnyPP7zcOUfzzX9FYGZlzy6YXNiyZ/d2oELXSXfu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(204, '251-0047-1', 'KURT RUSSEL', 'PEREZ', 'ALMODOVAR', '$2y$10$zT/A3RDLGpLXKhrApmk5cutmCRyOQIR2KkMvjRaeHHQt56bGBhhNu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(205, '251-0054-1', 'ALTHEA', 'CRUZ', 'NUVAL', '$2y$10$WKLlLVFE8cuy//YggKyd3ORGuRGOxtIBcZKZMaQcDsJRzwgHnsHOG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(206, '251-0055-1', 'ANGELICA', 'MUNAR', 'NUVAL', '$2y$10$4Nkn13lwjeW4XGC1USByzOwBsO33LyCqizsPxfKTWLMzCGMCy/rAy', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(207, '251-0057-1', 'KRISTHEL', 'VALDEZ', 'OBILLE', '$2y$10$M9O4wYDwAOosx25Q6nR5bu8rkIUZHl.IZh3OCgRsrM.S5i.y4hk4K', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(208, '251-0166-1', 'HERANETH', 'FEDERICO', 'BARO', '$2y$10$tkvDUq6DarjGEwLhrIYuCeI.WMeuWrfCxk8ZXL0e9bDlnsYopApaK', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(209, '251-0186-1', 'DERIC', 'GACAYAN', 'COTCHEZA', '$2y$10$kyqxNGttROoX5gMwTuwakeP.FcH1s.nUNctqh8OkCUdH8b2z6IKf.', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(210, '251-0231-1', 'JOHN VINCENT', 'DEGYEM', 'NILLO', '$2y$10$.acgiaRC18Z0mENwI9zXoOt9oyWgmV1vCyO/7Lh5ac14oDNQSmxp2', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(211, '251-0233-1', 'JAYSON', 'CABANBAN', 'DE GUZMAN', '$2y$10$vUCkv/USih7LKjxKaVGNSumkWPc5RfcGPyijd60oumcUCtE879LZe', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(212, '251-0234-1', 'CRISTOBAL', 'PALABAY', 'LEDESMA', '$2y$10$xoJ08IhSh6V7Umk2VE9rz.eJ6qM.yfcsp..cfMxVGBrKXpNRbs2fm', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(213, '251-0238-1', 'RIO', 'CABANBAN', 'CABANBAN', '$2y$10$tTDHv9Hl7QqJTQ3JCBYt.us8SfBp6uXjIPnGeF.mfDYw9AiKe1J5W', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(214, '251-0250-1', 'PRECIOUS LARA', 'MARCELLA', 'ESLAVA', '$2y$10$Z6GGiuk4rYdSJbo9cjZi8OpL4h/vU4y6ZBw9ALhMtWz/NF2rxbsoC', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(215, '251-0261-1', 'JOEMARC', 'USLAY', 'BALISONG', '$2y$10$HJk9R1Q.ooL5keNd.64kxe1AXeiZx9uY2r1eWimW4UGjmvT8iHSa6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(216, '251-0269-1', 'GEORGEN', 'CABANAYAN', 'PEREZ', '$2y$10$JhfwKYAZfn/JR/XMJMYuqO3dutkktiwxgu7ue2XZNTkqjwSycmJQe', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(217, '251-0271-1', 'RHALYLE KENNETH', 'BIORE', 'BENTOY', '$2y$10$50PnL2ckM.c/6H.2PY2rjuHx1nU/KCcyD3x.wpet82uVVrG9O/lYm', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(218, '251-0367-1', 'JEREMI', 'DELARNA', 'CASTRO', '$2y$10$GnPVEmKKHmmH.5EUNKC2neoLffynxjkhblWcwmKX8l34wwbLe4Cz2', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(219, '251-0368-1', 'HANS CHRISTIAN', 'JOVEN', 'CORTEZ', '$2y$10$u0igbzA84B6AnEXi6VFBMOw7beEkLUFHDR1aBr6znyCplCZZnhf6W', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(220, '251-0369-1', 'PRINCESS LEI ANN', 'CARIG', 'TAGUMPAY', '$2y$10$8.4E5Tf34zWNp591NtUAte4XO6cHYoH5HcnQamKmWzO3C9Mjn809i', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(221, '251-0375-1', 'RAFAEL CHRISTIAN MARK', 'NIEVERAS', 'ASTUDILLO', '$2y$10$.QUchP.kcjSvGaDt8RmbD.KlBKNw1xbkrhtga9jNBivQgpdZiXVIq', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(222, '251-0376-1', 'DOMNIC SAVIO ANGELO', 'MUNAR', 'MUNAR', '$2y$10$8qvFF6MdKfVLBPKniDeopuoFIuiS/B3X2wLTapvwt42i3Ime3FB76', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(223, '251-0379-1', 'JERALD COLLIN', 'BAUTISTA', 'LIM', '$2y$10$2qqFcZsZFak.pDhPvhZA4.2KhoIFF5sPSj7AToAJERLgAh4k2n2Ja', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(224, '251-0383-1', 'GIAN KENNETH', 'JALNE', 'NUESCA', '$2y$10$7Z7kQW1Dn86MM.9HW7UXT.Uby9knE/mFQvSH.kofaQpPuD6VeQEF6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(225, '251-0386-1', 'MANILYN', 'BRIONGOS', 'OLITO', '$2y$10$jB6YSapTR3G7UAItE.ajUeHj23C86IJ0Hul/RcmpnGofpWDWySVLi', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(226, '251-0401-1', 'RASHELLE', 'DATING-E', 'MAHGIT', '$2y$10$EskNvmyyLk.JE4lqU.w7.egQROXvQUu86629mKAZGZrsZIl8T7kku', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(227, '251-0416-1', 'RENZ', 'DADCOES', 'MADAYAG', '$2y$10$l6yzt/kE7b8LtVVg2m3mau5/aDnUiADcczbo9Z/JZa..nOO2262oG', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(228, '251-0458-1', 'BONNIE', 'BUCCAT', 'PADUA', '$2y$10$.gSwcxxGZKjA3fqFAKEzD.v2GuAWbxWJiW/mh5CL6.LwtFN2TeRGe', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(229, '251-0477-1', 'MONICA', 'SEGUIN', 'ALBAY', '$2y$10$w3BEYODsBQskDHSB4WAzvOB76hXbSrcUEJuKfUXu6J5UEr50EEoNK', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(230, '251-0482-1', 'AERON', 'NERONA', 'GAO-AN', '$2y$10$jKhWkysKhuxSnSm832n2XeVVNzyTfLyu6KKkU.cw.0k.h2aaM6Rq.', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(231, '251-0512-1', 'PRINCESS DIANAH', 'MONIS', 'LUCENA', '$2y$10$.csmd0itdYAHsyZaLXCVOO87tE9uU7YSzFfg8OFBpdpttwTNt1XZ6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(232, '251-0517-1', 'CHLOE', 'ARQUERO', 'NONES', '$2y$10$esXECc8cqhdbMqJPAJGPo.dZmGGzLcLZIhlV6BZugByuHrIVCNXsm', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '1-A', 'student'),
(260, '231-0354-1', 'REYMART', 'TEQUIL', 'AJERO', '$2y$10$6lJYB82QsCdG3CNx7xdey.fxKxq6ckdYLh3rH9Rbubk3dQ6Yh57zu', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(261, '231-0355-1', 'RUBY', 'PAJARIT', 'ALMOJUELA', '$2y$10$suaQW0iAfDN.PCUrEK1/HOKcqhPCTOQYq65XytC73gEW2ugReY8mS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(262, '231-0410-1', 'CHRISTIAN JAKE', 'DELA CRUZ', 'ANCHETA', '$2y$10$1xq//qnfB.zdfTIII9jgre9IXXSBO32pnofeQFU4Qe24Yp6wz8DY6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(263, '231-0211-1', 'ROD ANDREW', 'PLANTA', 'ANDAYA', '$2y$10$dPntnBV0lMP5Eam2ViPwQeEbL9DbZig/PJdnhcI9NdFHEdVtPdMYq', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(264, '231-0288-1', 'TRIXIE', 'LICUDINE', 'BALLESTEROS', '$2y$10$LQ2jB6GiNZp88iIQLJwdi.Noh7MswLkdBh9zfam7auWPZgKq09jg6', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(265, '211-0815-1', 'ANGIELYN', 'EPI', 'BALUDDA', '$2y$10$rd4zGzzeAqEMg2MzgUfN0eMWOQz3YhAIzz.J0qlKFgKQleGMwuZ1C', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(266, '231-0661-1', 'JORON', 'BALLASIW', 'BONETE', '$2y$10$.wbiPgnAklDo55HEwRDtQeTh6QB09eJMz1xuC3D5cuf/w7hwWYnu.', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(267, '231-0528-1', 'IAN LLOYD', 'RODRIGUEZ', 'BORROMEO', '$2y$10$9BR0HatrzE1O7dKwZlVfo.rkS/EOKhzizVGLkydEW1fUFnMISKyIS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(268, '231-0867-1', 'RUBY JANE', 'GABE', 'BUCSIT', '$2y$10$1g6OItj7.sXnIhcOrBKSQejRxq0XEVO2HlA9TnWJLRYT1qJNLVSX.', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(269, '231-0491-1', 'JUAN CARLO', 'MADRID', 'CARIAGA', '$2y$10$K5XSIBZfVm2KbpXQzca6ueQNRGC4n9wdI8eu3SkxmmSKrVV1Zhhc2', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(270, '221-0553-1', 'DENVER KEITH', 'OTANES', 'CASEM', '$2y$10$sAjzJT73/EjetBVpdP1kZ.ZK1a8GksSgamwY3ql7gC2s4CfbXD.kO', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(271, '231-0507-1', 'MARY JOY', 'TAVISORA', 'CELESTRE', '$2y$10$yCn1VOkzmER96sWA2YGMg.NSnocDxlGIhXYGTVukV3SypSi3k.O72', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student'),
(272, '231-0353-1', 'STEPHANIE', 'BADUA', 'DACANAY', '$2y$10$2JfRsnn9gEw4Alc.MBQYHO74ZoLyxuk3d0Bxmo0EKIiyRjEkDGqAS', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS', '3-A', 'student');

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
(96, '251-0004-1', 'GECC 101', '00421', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:27:54', '{\"q0\":5,\"q1\":5,\"q2\":5,\"q3\":5,\"q4\":5,\"q5\":5,\"q6\":5,\"q7\":5,\"q8\":5,\"q9\":5,\"q10\":5,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":5}', 74, 98.67, 'very joyful teacher', 'no'),
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
(113, '251-0369-1', 'GECC 103', '02860', 'COLLEGE OF ARTS AND SCIENCES', '2025-2026', '1st Semester', '2025-11-06 14:40:43', '{\"q0\":4,\"q1\":4,\"q2\":5,\"q3\":4,\"q4\":4,\"q5\":5,\"q6\":4,\"q7\":4,\"q8\":5,\"q9\":5,\"q10\":4,\"q11\":5,\"q12\":4,\"q13\":5,\"q14\":4}', 66, 88.00, '', 'yes'),
(114, '221-0388-1', 'ISBA 105', '40180', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2026-04-12 12:19:27', '{\"q_1\":5,\"q_2\":5,\"q_3\":5,\"q_4\":5,\"q_5\":5,\"q_6\":5,\"q_7\":5,\"q_8\":5,\"q_9\":4,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":5,\"q_15\":5}', 73, 97.33, '', 'yes'),
(115, '221-0388-1', 'ISAE 107', '40207', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2026-04-12 12:24:57', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":5,\"q_7\":4,\"q_8\":5,\"q_9\":5,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":4,\"q_14\":5,\"q_15\":5}', 70, 93.33, '', 'yes'),
(116, '221-0388-1', 'ISBA 105', '40180', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2026-04-12 12:27:04', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":5,\"q_5\":4,\"q_6\":5,\"q_7\":5,\"q_8\":4,\"q_9\":5,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":4,\"q_15\":5}', 70, 93.33, '', 'no'),
(117, '221-0388-1', 'ISBA 105', '40180', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2026-04-12 12:28:33', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":5,\"q_7\":5,\"q_8\":4,\"q_9\":5,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":5,\"q_15\":4}', 70, 93.33, '', 'no'),
(118, '221-0388-1', 'ISAE 108', '40182', 'COLLEGE OF INFORMATION SYSTEMS', '2025-2026', '1st Semester', '2026-04-12 13:35:40', '{\"q_1\":5,\"q_2\":4,\"q_3\":5,\"q_4\":4,\"q_5\":5,\"q_6\":3,\"q_7\":4,\"q_8\":5,\"q_9\":4,\"q_10\":5,\"q_11\":4,\"q_12\":5,\"q_13\":5,\"q_14\":4,\"q_15\":5}', 67, 89.33, 'Good', 'yes');

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
(984, '221-0411-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(985, '221-0411-1', 'ISPC 112', '2025-2026', '1st Semester', '40184', '40151'),
(986, '221-0411-1', 'ISPC 114', '2025-2026', '1st Semester', '40413', '40151'),
(987, '221-0411-1', 'ISPC 110', '2025-2026', '1st Semester', '40112', '40151'),
(988, '221-0411-1', 'ISAE 107', '2025-2026', '1st Semester', '40207', '40151'),
(989, '221-0411-1', 'ISBA 105', '2025-2026', '1st Semester', '40180', '40151'),
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
(1119, '221-0387-1', 'ISAE 108', '2025-2026', '1st Semester', '40182', '40151'),
(1282, '231-0354-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1283, '231-0355-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1284, '231-0410-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1285, '231-0211-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1286, '231-0288-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1287, '211-0815-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1288, '231-0661-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1289, '231-0528-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1290, '231-0867-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1291, '231-0491-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1292, '221-0553-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1293, '231-0507-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151'),
(1294, '231-0353-1', 'ISAE 106', '2025-2026', '1st Semester', '00424', '40151');

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
(62, 'ISPC 110', 'Business Process Management', '40112', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(63, 'ISPC 112', 'IS Strategy Management and Acquisition', '40184', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(64, 'ISAE 107', 'Professional Engagements', '40207', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(65, 'ISAE 108', 'Technoprenuership', '40182', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(66, 'ISPC 114', 'Capstone Project 2', '40413', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(67, 'ISBA 105', 'Analytics Application', '40180', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(68, 'ISSM 105', 'Principles of Systems Thinking', '40023', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(69, 'GEEC 101', 'Environmental Science', '0716', '40005', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(71, 'GECC 103', 'Mathematics in the Modern World', '02860', '40045', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(72, 'GECC 101', 'Arts Appreciation', '00421', '40045', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(73, 'GECC 102', 'Purposive Communication', '40193', '40050', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(74, 'ISCC 101', 'Introduction to Computing', '00711', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(75, 'ISCC 102', 'Computer Programming 1', '40094', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS'),
(385, 'ISAE 106', 'Operating Systems', '00424', '40151', 'COLLEGE OF INFORMATION SYSTEMS', 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `id` int(255) NOT NULL,
  `idnumber` varchar(255) NOT NULL,
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

INSERT INTO `superadmin` (`id`, `idnumber`, `first_name`, `mid_name`, `last_name`, `password`, `role`, `college`, `program`, `faculty_rank`, `position`, `status`) VALUES
(2, '40193', 'Frediz Winda', 'Ferrer', 'Badua', '$2y$10$e6ZHPQM/6xvk1/jvupujHO4pggmk8P27m5TeUAnRpyZ9Iiblm.lpa', 'superadmin', 'COLLEGE OF ARTS AND SCIENCES', 'BACHELOR OF ARTS IN ENGLISH LANGUAGE', 'Assistant Professor IV', 'Head Instruction', 'active');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `position` (`position`),
  ADD KEY `fk_admin_faculty_rank` (`faculty_rank`),
  ADD KEY `idnumber` (`idnumber`);

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
-- Indexes for table `admin_evaluation_categories`
--
ALTER TABLE `admin_evaluation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_evaluation_questions`
--
ALTER TABLE `admin_evaluation_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
  ADD KEY `fk_evaluation_college` (`college`),
  ADD KEY `fk_evaluation_student_section` (`student_section`);

--
-- Indexes for table `evaluation_categories`
--
ALTER TABLE `evaluation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `evaluation_rating_scales`
--
ALTER TABLE `evaluation_rating_scales`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `department` (`college`),
  ADD KEY `fk_faculty_rank` (`faculty_rank`),
  ADD KEY `fk_faculty_program` (`program`),
  ADD KEY `college` (`college`),
  ADD KEY `idnumber` (`idnumber`);

--
-- Indexes for table `faculty_dev_plan`
--
ALTER TABLE `faculty_dev_plan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_plan` (`faculty_id`,`semester`,`academic_year`);

--
-- Indexes for table `registrar`
--
ALTER TABLE `registrar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_rank` (`faculty_rank`),
  ADD KEY `fk_registrar_department` (`college`),
  ADD KEY `fk_registrar_program` (`program`),
  ADD KEY `idnumber` (`idnumber`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department` (`college`),
  ADD KEY `section` (`section`),
  ADD KEY `section_2` (`section`),
  ADD KEY `idnumber` (`idnumber`),
  ADD KEY `fk_student_program` (`program`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_superadmin_faculty_rank` (`faculty_rank`),
  ADD KEY `fk_superadmin_position` (`position`),
  ADD KEY `fk_superadmin_department` (`college`),
  ADD KEY `fk_superadmin_program` (`program`),
  ADD KEY `idnumber` (`idnumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=757;

--
-- AUTO_INCREMENT for table `adds`
--
ALTER TABLE `adds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `admin_evaluation`
--
ALTER TABLE `admin_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `admin_evaluation_categories`
--
ALTER TABLE `admin_evaluation_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_evaluation_questions`
--
ALTER TABLE `admin_evaluation_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admin_evaluation_submissions`
--
ALTER TABLE `admin_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `college_info`
--
ALTER TABLE `college_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `evaluation_categories`
--
ALTER TABLE `evaluation_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `evaluation_rating_scales`
--
ALTER TABLE `evaluation_rating_scales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `faculty_dev_plan`
--
ALTER TABLE `faculty_dev_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registrar`
--
ALTER TABLE `registrar`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT for table `student_evaluation_submissions`
--
ALTER TABLE `student_evaluation_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `student_subject`
--
ALTER TABLE `student_subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1295;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `idnumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=386;

--
-- AUTO_INCREMENT for table `superadmin`
--
ALTER TABLE `superadmin`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  ADD CONSTRAINT `fk_evaluatee_id` FOREIGN KEY (`evaluatee_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluator_position` FOREIGN KEY (`evaluator_position`) REFERENCES `admin` (`position`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_evaluation_questions`
--
ALTER TABLE `admin_evaluation_questions`
  ADD CONSTRAINT `fk_admin_cat` FOREIGN KEY (`category_id`) REFERENCES `admin_evaluation_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `faculty_id_key` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluation_college` FOREIGN KEY (`college`) REFERENCES `adds` (`college_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluation_student_section` FOREIGN KEY (`student_section`) REFERENCES `student` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_id_key` FOREIGN KEY (`student_id`) REFERENCES `student` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_code_key` FOREIGN KEY (`subject_code`) REFERENCES `subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subject_title_key` FOREIGN KEY (`subject_title`) REFERENCES `subject` (`title`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `evaluation_questions`
--
ALTER TABLE `evaluation_questions`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `evaluation_categories` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `faculty_dev_plan`
--
ALTER TABLE `faculty_dev_plan`
  ADD CONSTRAINT `fk_plan_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`idnumber`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_student_program` FOREIGN KEY (`program`) REFERENCES `adds` (`program_name`) ON DELETE CASCADE ON UPDATE CASCADE,
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
