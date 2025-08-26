-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 11:13 AM
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
-- Database: `mccopes`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year` varchar(10) NOT NULL,
  `semester` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `used` tinyint(4) NOT NULL DEFAULT 0,
  `open_at` datetime DEFAULT NULL,
  `close_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year`, `semester`, `is_active`, `used`, `open_at`, `close_at`, `created_at`, `updated_at`) VALUES
(47, '2025-2026', 1, 0, 1, NULL, NULL, '2025-08-19 11:53:11', '2025-08-19 14:51:31'),
(48, '2025-2026', 2, 0, 1, NULL, NULL, '2025-08-19 15:33:14', '2025-08-21 17:33:15'),
(49, '2026-2027', 1, 0, 1, NULL, NULL, '2025-08-22 02:15:17', '2025-08-22 09:05:49'),
(50, '2026-2027', 2, 1, 0, NULL, NULL, '2025-08-26 05:22:11', '2025-08-26 05:22:19');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `response` varchar(100) NOT NULL COMMENT 'Stores option_value from response_options',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comments` text DEFAULT NULL,
  `response_score` int(11) NOT NULL DEFAULT 0 COMMENT 'Stores option_order for calculations'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_03_19_000000_create_users_table', 1),
(2, '2025_01_20_000000_add_main_admin_and_last_active_to_users_table', 1),
(3, '2025_06_17_123312_create_staff_table', 1),
(4, '2025_06_17_124941_create_evaluations_table', 1),
(5, '2025_06_17_130928_create_questions_table', 1),
(6, '2025_06_17_140000_create_academic_years_table', 1),
(7, '2025_06_17_140100_create_acad_questions_table', 1),
(8, '2025_06_17_140200_create_response_options_table', 1),
(9, '2025_07_06_092401_add_unique_to_staff_id', 1),
(10, '2025_07_09_000001_add_is_active_to_academic_years_table', 1),
(11, '2025_07_09_000002_add_updated_at_to_academic_years_table', 1),
(12, '2025_07_09_072029_add_is_active_to_academic_years_table', 1),
(13, '2025_07_10_125804_add_academic_year_id_to_evaluations_table', 2),
(14, '2025_01_21_000000_add_password_to_staff_table', 3),
(15, '2025_01_21_000001_add_last_login_to_staff_table', 4),
(16, '2025_07_11_111415_create_request_signin_table', 5),
(17, '2025_07_12_000001_add_open_at_close_at_to_academic_years_table', 6),
(18, '2025_07_14_173330_create_saved_questions_table', 7),
(19, '2025_07_15_000000_create_save_eval_result_table', 8),
(20, '2025_07_15_000001_create_save_questionnaires_result_table', 8),
(21, '2025_07_15_074944_drop_save_questionnaires_result_table', 9),
(22, '2025_07_15_075008_drop_questionnaires_result_table', 9),
(23, '2025_07_15_104727_alter_is_open_default_on_questions_table', 10),
(24, '2025_07_16_000000_add_used_to_academic_years_table', 10),
(25, '2025_08_01_131104_create_subjects_table', 11),
(26, '2025_08_01_140000_add_year_level_to_request_signin_table', 12),
(27, '2025_08_01_150000_add_year_level_to_users_table', 13),
(28, '2025_08_07_155107_add_subject_type_to_subjects_table', 14),
(29, '2025_08_08_150357_add_section_to_request_signin_table', 15),
(30, '2025_08_08_150723_add_section_to_users_table', 16),
(31, '2025_08_11_224934_create_semis_subjects_table', 17),
(32, '2025_08_11_231253_add_section_and_semester_to_subjects_table', 18),
(33, '2025_08_14_124623_add_semester_to_academic_years_table', 19),
(34, '2025_08_14_125658_add_unique_constraint_to_academic_years_table', 20),
(35, '2025_08_16_175005_add_unique_constraint_to_questions_table', 21),
(36, '2025_08_20_221414_remove_unique_constraint_from_subjects_sub_code', 22),
(37, '2025_08_20_221515_add_unique_constraint_sub_code_section_to_subjects', 23);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `staff_type` enum('teaching','non-teaching') NOT NULL,
  `response_type` varchar(50) NOT NULL DEFAULT 'rating_5',
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_open` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `title`, `description`, `staff_type`, `response_type`, `academic_year_id`, `created_at`, `is_open`) VALUES
(503, 'Instructional Competence', 'Organization of lesson content', 'teaching', 'Rating_Scale', 50, '2025-08-26 05:22:44', 1),
(504, 'Instructional Competence', 'Ability to adjust to student learning needs', 'teaching', 'Rating_Scale', 50, '2025-08-26 05:22:44', 1),
(505, 'Classroom Management', 'Maximizes instructional time', 'teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(506, 'Classroom Management', 'Starts and ends class on time', 'teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(507, 'Communication and Interpersonal Skills', 'Is approachable and respectful', 'teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(508, 'Communication and Interpersonal Skills', 'Listens actively to student concerns', 'teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(509, 'Work Habits and Punctuality', 'Completes tasks without reminders', 'non-teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(510, 'Work Habits and Punctuality', 'Wears proper uniform/ID', 'teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(511, 'Attitude and Teamwork', 'Responds respectfully to instructions', 'non-teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(512, 'Attitude and Teamwork', 'Maintains a positive attitude', 'non-teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(513, 'Work Habits and Punctuality', 'Uses tools and supplies properly', 'non-teaching', 'Frequency', 50, '2025-08-26 05:22:44', 1),
(514, 'Service and Responsiveness', 'Satisfaction with behavior toward students', 'non-teaching', 'Satisfaction', 50, '2025-08-26 05:22:44', 1),
(515, 'Service and Responsiveness', 'Satisfaction with courtesy to visitors', 'non-teaching', 'Satisfaction', 50, '2025-08-26 05:22:44', 1);

-- --------------------------------------------------------

--
-- Table structure for table `request_signin`
--

CREATE TABLE `request_signin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `profile_image` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `is_main_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `response_options`
--

CREATE TABLE `response_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `response_type` varchar(50) NOT NULL,
  `option_value` varchar(100) NOT NULL,
  `option_label` varchar(255) NOT NULL,
  `option_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `response_options`
--

INSERT INTO `response_options` (`id`, `response_type`, `option_value`, `option_label`, `option_order`, `created_at`) VALUES
(1, 'Rating_Scale', 'poor', 'Poor', 1, '2025-06-04 14:05:50'),
(2, 'Rating_Scale', 'fair', 'Fair', 2, '2025-06-04 14:05:50'),
(3, 'Rating_Scale', 'good', 'Good', 3, '2025-06-04 14:05:50'),
(4, 'Rating_Scale', 'very_good', 'Very Good', 4, '2025-06-04 14:05:50'),
(5, 'Rating_Scale', 'excellent', 'Excellent', 5, '2025-06-04 14:05:50'),
(6, 'Frequency', 'rarely', 'Rarely', 1, '2025-06-04 14:05:50'),
(7, 'Frequency', 'sometimes', 'Sometimes', 2, '2025-06-04 14:05:50'),
(8, 'Frequency', 'most_time', 'Most of the Time', 3, '2025-06-04 14:05:50'),
(9, 'Frequency', 'always', 'Always', 4, '2025-06-04 14:05:50'),
(10, 'Agreement', 'strongly_disagree', 'Strongly Disagree', 1, '2025-06-04 14:05:50'),
(11, 'Agreement', 'disagree', 'Disagree', 2, '2025-06-04 14:05:50'),
(12, 'Agreement', 'neutral', 'Neutral', 3, '2025-06-04 14:05:50'),
(13, 'Agreement', 'agree', 'Agree', 4, '2025-06-04 14:05:50'),
(14, 'Agreement', 'strongly_agree', 'Strongly Agree', 5, '2025-06-04 14:05:50'),
(15, 'Satisfaction', 'very_satisfied', 'Very Satisfied', 1, '2025-06-04 14:05:50'),
(16, 'Satisfaction', 'satisfied', 'Satisfied', 2, '2025-06-04 14:05:50'),
(17, 'Satisfaction', 'neutral', 'Neutral', 3, '2025-06-04 14:05:50'),
(18, 'Satisfaction', 'dissatisfied', 'Dissatisfied', 4, '2025-06-04 14:05:50'),
(19, 'Satisfaction', 'very_dissatisfied', 'Very Dissatisfied', 5, '2025-06-04 14:05:50'),
(20, 'Yes_No', 'no', 'No', 0, '2025-06-04 14:05:50'),
(21, 'Yes_no', 'yes', 'Yes', 1, '2025-06-04 14:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `saved_questions`
--

CREATE TABLE `saved_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `staff_type` varchar(255) NOT NULL,
  `response_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_questions`
--

INSERT INTO `saved_questions` (`id`, `academic_year_id`, `title`, `description`, `staff_type`, `response_type`, `created_at`, `updated_at`) VALUES
(235, 47, 'Instructional Competence', 'Organization of lesson content', 'teaching', 'Rating_Scale', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(236, 47, 'Instructional Competence', 'Ability to adjust to student learning needs', 'teaching', 'Rating_Scale', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(237, 47, 'Classroom Management', 'Maximizes instructional time', 'teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(238, 47, 'Classroom Management', 'Starts and ends class on time', 'teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(239, 47, 'Communication and Interpersonal Skills', 'Is approachable and respectful', 'teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(240, 47, 'Communication and Interpersonal Skills', 'Listens actively to student concerns', 'teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(241, 47, 'Work Habits and Punctuality', 'Completes tasks without reminders', 'non-teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(242, 47, 'Work Habits and Punctuality', 'Wears proper uniform/ID', 'teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(243, 47, 'Attitude and Teamwork', 'Responds respectfully to instructions', 'non-teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(244, 47, 'Attitude and Teamwork', 'Maintains a positive attitude', 'non-teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(245, 47, 'Work Habits and Punctuality', 'Uses tools and supplies properly', 'non-teaching', 'Frequency', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(246, 47, 'Service and Responsiveness', 'Satisfaction with behavior toward students', 'non-teaching', 'Satisfaction', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(247, 47, 'Service and Responsiveness', 'Satisfaction with courtesy to visitors', 'non-teaching', 'Satisfaction', '2025-08-19 14:47:37', '2025-08-19 14:47:37'),
(248, 48, 'Instructional Competence', 'Organization of lesson content', 'teaching', 'Rating_Scale', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(249, 48, 'Instructional Competence', 'Ability to adjust to student learning needs', 'teaching', 'Rating_Scale', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(250, 48, 'Classroom Management', 'Maximizes instructional time', 'teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(251, 48, 'Classroom Management', 'Starts and ends class on time', 'teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(252, 48, 'Communication and Interpersonal Skills', 'Is approachable and respectful', 'teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(253, 48, 'Communication and Interpersonal Skills', 'Listens actively to student concerns', 'teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(254, 48, 'Work Habits and Punctuality', 'Completes tasks without reminders', 'non-teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(255, 48, 'Work Habits and Punctuality', 'Wears proper uniform/ID', 'teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(256, 48, 'Attitude and Teamwork', 'Responds respectfully to instructions', 'non-teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(257, 48, 'Attitude and Teamwork', 'Maintains a positive attitude', 'non-teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(258, 48, 'Work Habits and Punctuality', 'Uses tools and supplies properly', 'non-teaching', 'Frequency', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(259, 48, 'Service and Responsiveness', 'Satisfaction with behavior toward students', 'non-teaching', 'Satisfaction', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(260, 48, 'Service and Responsiveness', 'Satisfaction with courtesy to visitors', 'non-teaching', 'Satisfaction', '2025-08-21 17:27:11', '2025-08-21 17:27:11'),
(261, 49, 'Instructional Competence', 'Organization of lesson content', 'teaching', 'Rating_Scale', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(262, 49, 'Instructional Competence', 'Ability to adjust to student learning needs', 'teaching', 'Rating_Scale', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(263, 49, 'Classroom Management', 'Maximizes instructional time', 'teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(264, 49, 'Classroom Management', 'Starts and ends class on time', 'teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(265, 49, 'Communication and Interpersonal Skills', 'Is approachable and respectful', 'teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(266, 49, 'Communication and Interpersonal Skills', 'Listens actively to student concerns', 'teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(267, 49, 'Work Habits and Punctuality', 'Completes tasks without reminders', 'non-teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(268, 49, 'Work Habits and Punctuality', 'Wears proper uniform/ID', 'teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(269, 49, 'Attitude and Teamwork', 'Responds respectfully to instructions', 'non-teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(270, 49, 'Attitude and Teamwork', 'Maintains a positive attitude', 'non-teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(271, 49, 'Work Habits and Punctuality', 'Uses tools and supplies properly', 'non-teaching', 'Frequency', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(272, 49, 'Service and Responsiveness', 'Satisfaction with behavior toward students', 'non-teaching', 'Satisfaction', '2025-08-22 09:05:34', '2025-08-22 09:05:34'),
(273, 49, 'Service and Responsiveness', 'Satisfaction with courtesy to visitors', 'non-teaching', 'Satisfaction', '2025-08-22 09:05:34', '2025-08-22 09:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `save_eval_result`
--

CREATE TABLE `save_eval_result` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `response` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `response_score` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `save_eval_result`
--

INSERT INTO `save_eval_result` (`id`, `staff_id`, `question_id`, `response`, `user_id`, `academic_year_id`, `comments`, `response_score`, `created_at`, `updated_at`) VALUES
(531, 28, 235, 'poor', 71, 47, 'Pnagsa ra mosyud', 1, '2025-08-19 12:16:02', '2025-08-19 14:51:31'),
(532, 28, 236, 'fair', 71, 47, 'Pnagsa ra mosyud', 2, '2025-08-19 12:16:02', '2025-08-19 14:51:31'),
(533, 28, 237, 'rarely', 71, 47, 'Pnagsa ra mosyud', 1, '2025-08-19 12:16:02', '2025-08-19 14:51:31'),
(534, 28, 238, 'rarely', 71, 47, 'Pnagsa ra mosyud', 1, '2025-08-19 12:16:03', '2025-08-19 14:51:31'),
(535, 28, 239, 'sometimes', 71, 47, 'Pnagsa ra mosyud', 2, '2025-08-19 12:16:03', '2025-08-19 14:51:31'),
(536, 28, 240, 'sometimes', 71, 47, 'Pnagsa ra mosyud', 2, '2025-08-19 12:16:03', '2025-08-19 14:51:31'),
(537, 28, 242, 'rarely', 71, 47, 'Pnagsa ra mosyud', 1, '2025-08-19 12:16:03', '2025-08-19 14:51:31'),
(538, 21, 235, 'excellent', 71, 47, 'Good IT HEad', 5, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(539, 21, 236, 'excellent', 71, 47, 'Good IT HEad', 5, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(540, 21, 237, 'always', 71, 47, 'Good IT HEad', 4, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(541, 21, 238, 'always', 71, 47, 'Good IT HEad', 4, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(542, 21, 239, 'always', 71, 47, 'Good IT HEad', 4, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(543, 21, 240, 'always', 71, 47, 'Good IT HEad', 4, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(544, 21, 242, 'always', 71, 47, 'Good IT HEad', 4, '2025-08-19 12:54:59', '2025-08-19 14:51:31'),
(545, 27, 235, 'good', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(546, 27, 236, 'good', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(547, 27, 237, 'most_time', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(548, 27, 238, 'sometimes', 71, 47, 'students inst', 2, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(549, 27, 239, 'most_time', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(550, 27, 240, 'most_time', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(551, 27, 242, 'most_time', 71, 47, 'students inst', 3, '2025-08-19 12:55:51', '2025-08-19 14:51:31'),
(552, 28, 248, 'excellent', 71, 48, 'dssd', 5, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(553, 28, 249, 'excellent', 71, 48, 'dssd', 5, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(554, 28, 250, 'always', 71, 48, 'dssd', 4, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(555, 28, 251, 'always', 71, 48, 'dssd', 4, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(556, 28, 252, 'always', 71, 48, 'dssd', 4, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(557, 28, 253, 'always', 71, 48, 'dssd', 4, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(558, 28, 255, 'always', 71, 48, 'dssd', 4, '2025-08-21 05:20:26', '2025-08-21 17:33:15'),
(559, 15, 261, 'excellent', 71, 49, 'Good Teaching style', 5, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(560, 15, 262, 'excellent', 71, 49, 'Good Teaching style', 5, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(561, 15, 263, 'most_time', 71, 49, 'Good Teaching style', 3, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(562, 15, 264, 'most_time', 71, 49, 'Good Teaching style', 3, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(563, 15, 265, 'most_time', 71, 49, 'Good Teaching style', 3, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(564, 15, 266, 'most_time', 71, 49, 'Good Teaching style', 3, '2025-08-22 08:57:54', '2025-08-22 09:05:49'),
(565, 15, 268, 'most_time', 71, 49, 'Good Teaching style', 3, '2025-08-22 08:57:54', '2025-08-22 09:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `department` varchar(50) NOT NULL,
  `staff_type` enum('teaching','non-teaching') NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) NOT NULL DEFAULT 'default-staff.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `full_name`, `email`, `password`, `remember_token`, `last_login`, `phone`, `department`, `staff_type`, `image_path`, `profile_image`, `created_at`, `updated_at`) VALUES
(8, 'TH0283', 'Alvine B. Billones', 'alvin@gmail.com', '$2y$12$vZAiSNpW0pmxKmgmi5gk7eM0OChjo6CrydEaY4aM2lR4jf4Mx3nBO', NULL, NULL, '09502337796', 'BSIT', 'teaching', 'uploads/staff/TH0282900_1752137658.jpg', 'default-staff.png', '2025-06-04 23:26:08', '2025-07-10 21:38:58'),
(13, 'JM5667', 'Juniel Marfa', 'juniel@gmail.com', '$2y$12$IYrBRgn0lPAildKbJwBgOOcF91kxmbe7vyTUq/kwHUxBZwoltFEku', NULL, NULL, '09502337793', 'BSBA', 'teaching', 'uploads/staff/45454545_1752137502.jpg', 'default-staff.png', '2025-06-30 17:15:38', '2025-07-10 19:43:15'),
(15, 'JC3232', 'Jareed Cueva', 'jareed@gmail.com', '$2y$12$2WKrNVrp2xK/7vCAtFhloO8EE7Rqe2Tr7pMwFmaleLDU0CRwkENoi', NULL, NULL, '09343434314', 'BSIT', 'teaching', 'uploads/staff/34453535_1752138040.jpg', 'default-staff.png', '2025-07-06 16:31:38', '2025-08-16 10:42:28'),
(19, 'RC4343', 'Roberto Caratao Jr.', 'caratao@gmailcom', '$2y$12$gUxAQK7VteOhM/Z27Rilg.MtdQKdWJeVxGuj0JOUMrwEPo3qAukhS', NULL, NULL, '09343437483', 'Registrar', 'non-teaching', 'uploads/staff/434344dsd_1752138624.jpg', 'default-staff.png', '2025-07-10 01:10:24', '2025-07-15 01:36:06'),
(20, 'RB2736', 'Richard Bracero', 'bracero@gmailcom', '$2y$12$mdoG31xeeOOgwJVfdnLQce2RTF8f6eJ6UFgWLM22muI9AGJexQImi', NULL, NULL, '09343437481', 'BSIT', 'teaching', 'uploads/staff/RB2736_1752209425.jpg', 'default-staff.png', '2025-07-10 07:26:46', '2025-08-16 10:41:39'),
(21, 'IT2323', 'Dino L. Ilustrisimo', 'dino@gmail.com', '$2y$12$ExPuqWIzntg/UcZktBS7u./Q9EhPHs2Ufdt5d0FgrV/zgBMI5YNyu', NULL, '2025-07-10 20:12:24', '09443434336', 'BSIT', 'teaching', 'uploads/staff/IT2323_1752209472.jpg', 'default-staff.png', '2025-07-10 15:49:24', '2025-07-10 20:51:12'),
(22, 'GB3434', 'Glenford P. Buncal', 'glen@gmail.com', NULL, NULL, NULL, '09502337796', 'BSED', 'teaching', 'uploads/staff/GB3434_1752220185.jpg', 'default-staff.png', '2025-07-10 23:49:45', '2025-08-08 16:16:56'),
(23, 'WD4343', 'Wendell Denorte', 'wendel@gmail.com', NULL, NULL, NULL, '09343434321', 'HR', 'non-teaching', 'uploads/staff/WD4343_1752543416.jpg', 'default-staff.png', '2025-07-15 01:36:56', '2025-07-15 01:36:56'),
(24, 'KB2121', 'Kurt Bryan S. Alegre', 'alegre@gmail.com', NULL, NULL, NULL, '09345273832', 'BSIT', 'teaching', 'uploads/staff/KB2121_1754026345.jpg', 'default-staff.png', '2025-08-01 05:32:25', '2025-08-16 10:43:31'),
(26, 'MA1212', 'Miko Aguntin', 'miko@gmail.com', NULL, NULL, NULL, '09898989899', 'BEED', 'teaching', 'uploads/staff/MA1212_1755150922.jpg', 'default-staff.png', '2025-08-14 05:55:22', '2025-08-20 14:22:28'),
(27, 'KM3333', 'Kent Medallo', 'kent@gmail.com', NULL, NULL, NULL, '09502337795', 'BSIT', 'teaching', 'uploads/staff/KM3333_1755605386.jpg', 'default-staff.png', '2025-08-19 12:09:46', '2025-08-19 12:09:46'),
(28, 'AL3435', 'Jessica Alcazar', 'jerssica@gmail.com', NULL, NULL, NULL, '09343434345', 'BSBA', 'teaching', 'uploads/staff/AL3435_1755605537.jpg', 'default-staff.png', '2025-08-19 12:12:17', '2025-08-21 05:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_code` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `sub_department` varchar(255) NOT NULL,
  `sub_year` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `semester` enum('1','2') NOT NULL,
  `assign_instructor` varchar(255) DEFAULT NULL,
  `subject_type` enum('Major','Minor','Bridging') NOT NULL DEFAULT 'Major',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `sub_code`, `sub_name`, `sub_department`, `sub_year`, `section`, `semester`, `assign_instructor`, `subject_type`, `created_at`, `updated_at`) VALUES
(21, 'GE 1', 'Understanding The Self', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-20 12:05:59', '2025-08-20 12:51:11'),
(22, 'GE 2', 'Reading in the Philippine', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-20 12:06:57', '2025-08-20 12:50:25'),
(23, 'GE 3', 'The Contemporary World', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-20 12:07:59', '2025-08-20 12:50:37'),
(24, 'GE 4', 'Mathematics in the Modern World', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-20 12:24:58', '2025-08-20 12:49:32'),
(25, 'BA 111', 'Basic  Microeconomics', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Major', '2025-08-20 12:26:37', '2025-08-21 15:54:59'),
(26, 'PATHFit 1', 'Movement Enhancement', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-20 12:32:54', '2025-08-20 12:49:44'),
(27, 'NSTP 111', 'National Services Training Program 1', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-20 12:36:45', '2025-08-21 04:53:35'),
(28, 'BA BR 1', 'Fundamentals of Accounting 1', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Bridging', '2025-08-20 12:37:46', '2025-08-20 12:49:00'),
(30, 'GE 5', 'Ethics', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:40:56', '2025-08-20 12:52:24'),
(31, 'GE 6', 'People and the  Earths  Ecosystem', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:42:19', '2025-08-21 11:40:48'),
(32, 'GE 7', 'The Life and Works of Jose Rizal', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:43:24', '2025-08-20 12:53:39'),
(33, 'CSCI 111', 'Introduction to Computers', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Major', '2025-08-20 12:44:29', '2025-08-20 12:53:12'),
(34, 'BA 122', 'Good Governance and Social Responsiblities', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:45:35', '2025-08-20 12:52:57'),
(35, 'PATHFit 2', 'Exercisebased Fitness Activities', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:46:22', '2025-08-20 12:52:36'),
(36, 'NSTP 2', 'National Services Training Program 2', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Minor', '2025-08-20 12:48:25', '2025-08-20 12:48:25'),
(37, 'BA BR 3', 'Fundamentals of Accounting 2', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Bridging', '2025-08-20 12:55:43', '2025-08-20 12:55:43'),
(38, 'BA BR 4', 'Organization and Management', 'BSBA', '1st Year', 'FM-1A', '2', NULL, 'Bridging', '2025-08-20 12:56:37', '2025-08-20 12:56:37'),
(39, 'GE 1', 'Understanding The Self', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-20 14:18:21', '2025-08-20 14:18:21'),
(40, 'GE 3', 'The Contemporary World', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-20 14:35:01', '2025-08-20 14:35:01'),
(41, 'GE 2', 'Reading in the Philippine', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-21 04:46:04', '2025-08-21 04:46:04'),
(42, 'NSTP 111', 'National Services Training Program 1', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-21 04:54:00', '2025-08-21 04:54:21'),
(43, 'PATHFit 1', 'Movement Enhancement', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-21 04:54:43', '2025-08-21 04:54:43'),
(44, 'GE 4', 'Mathematics in the Modern World', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-21 04:55:28', '2025-08-21 04:55:28'),
(45, 'BA BR 1', 'Fundamentals of Accounting 1', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Bridging', '2025-08-21 04:56:05', '2025-08-21 04:56:05'),
(48, 'BA BR 2', 'Business Finance', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Minor', '2025-08-21 05:01:46', '2025-08-21 05:01:46'),
(49, 'BA BR 2', 'Business Finance', 'BSBA', '1st Year', 'FM-1A', '1', NULL, 'Minor', '2025-08-21 05:02:23', '2025-08-21 05:02:23'),
(50, 'BA 111', 'Basic  Microeconomics', 'BSBA', '1st Year', 'FM-1B', '1', NULL, 'Major', '2025-08-21 05:02:36', '2025-08-21 05:02:36'),
(51, 'GE 5', 'Ethics', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:41:16', '2025-08-21 11:41:16'),
(52, 'PATHFit 2', 'Exercisebased Fitness Activities', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:44:17', '2025-08-21 11:44:17'),
(53, 'BA BR 3', 'Fundamentals of Accounting 2', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Bridging', '2025-08-21 11:44:51', '2025-08-21 11:44:51'),
(54, 'BA 122', 'Good Governance and Social Responsiblities', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:45:32', '2025-08-21 11:45:32'),
(55, 'CSCI 111', 'Introduction to Computers', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Major', '2025-08-21 11:46:41', '2025-08-21 11:46:41'),
(56, 'NSTP 2', 'National Services Training Program 2', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:47:02', '2025-08-21 11:47:02'),
(57, 'BA BR 4', 'Organization and Management', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Bridging', '2025-08-21 11:47:45', '2025-08-21 11:47:45'),
(58, 'GE 6', 'People and the  Earths  Ecosystem', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:48:09', '2025-08-21 11:48:09'),
(59, 'GE 7', 'The Life and Works of Jose Rizal', 'BSBA', '1st Year', 'FM-1B', '2', NULL, 'Minor', '2025-08-21 11:48:37', '2025-08-21 11:48:37'),
(60, 'GE 8', 'Philippine Popular Culture', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Minor', '2025-08-21 11:50:04', '2025-08-21 11:50:04'),
(61, 'GE 8', 'Philippine Popular Culture', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Minor', '2025-08-21 11:50:53', '2025-08-21 11:50:53'),
(62, 'GE 9', 'Art Appreciation', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Minor', '2025-08-21 11:51:25', '2025-08-21 11:51:25'),
(63, 'GE 9', 'Art Appreciation', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Minor', '2025-08-21 11:51:50', '2025-08-21 11:51:50'),
(64, 'ENG 121', 'Speech Improvement', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Minor', '2025-08-21 11:53:06', '2025-08-21 11:53:06'),
(65, 'ENG 121', 'Speech Improvement', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Minor', '2025-08-21 11:53:21', '2025-08-21 11:53:21'),
(66, 'CSCI 213', 'Applications Tools in Business', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Major', '2025-08-21 11:54:20', '2025-08-21 16:34:28'),
(67, 'CSCI 213', 'Applications Tools in Business', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Major', '2025-08-21 11:54:50', '2025-08-21 11:54:50'),
(68, 'FM 211', 'Financial Management', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Major', '2025-08-21 11:55:51', '2025-08-21 11:55:51'),
(69, 'FM 211', 'Financial Management', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Major', '2025-08-21 11:56:12', '2025-08-21 11:56:12'),
(70, 'Acctg 211', 'Financial Accounting 1', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Major', '2025-08-21 11:57:13', '2025-08-21 11:57:13'),
(71, 'Acctg 211', 'Financial Accounting 1', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Major', '2025-08-21 11:57:30', '2025-08-21 11:57:30'),
(72, 'PATHFit 3', 'Physical Activities  Towards Health', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Minor', '2025-08-21 11:58:44', '2025-08-21 11:58:44'),
(73, 'PATHFit 3', 'Physical Activities  Towards Health', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Minor', '2025-08-21 11:59:05', '2025-08-21 11:59:05'),
(74, 'BA BR 5', 'Applied Economics', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Bridging', '2025-08-21 11:59:46', '2025-08-22 02:50:21'),
(75, 'BA BR 5', 'Applied Economics', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Bridging', '2025-08-21 12:00:01', '2025-08-21 16:53:09'),
(76, 'BA BR 6', 'Business Marketing', 'BSBA', '2nd Year', 'FM-2A', '1', NULL, 'Bridging', '2025-08-21 12:00:30', '2025-08-21 12:00:30'),
(77, 'BA BR 6', 'Business Marketing', 'BSBA', '2nd Year', 'FM-2B', '1', NULL, 'Bridging', '2025-08-21 12:00:48', '2025-08-21 12:00:48'),
(78, 'GE 10', 'Purposive Communication', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Minor', '2025-08-21 12:05:15', '2025-08-21 12:05:15'),
(79, 'GE 10', 'Purposive Communication', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Minor', '2025-08-21 12:05:35', '2025-08-21 12:05:35'),
(80, 'GE 11', 'Gender and Society', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Minor', '2025-08-21 12:06:18', '2025-08-21 12:06:18'),
(81, 'GE 11', 'Gender and Society', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Minor', '2025-08-21 12:06:35', '2025-08-21 12:06:35'),
(82, 'BA 213', 'Income Taxation', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Minor', '2025-08-21 12:07:28', '2025-08-21 12:07:28'),
(83, 'BA 213', 'Income Taxation', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Minor', '2025-08-21 12:08:07', '2025-08-21 12:08:07'),
(84, 'FM 222', 'Financial Analysis and Reporting', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Major', '2025-08-21 12:09:22', '2025-08-21 12:09:47'),
(85, 'FM 222', 'Financial Analysis and Reporting', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Major', '2025-08-21 12:10:08', '2025-08-21 12:10:08'),
(86, 'FM 223', 'Banking and Financial Institution', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Major', '2025-08-21 12:11:00', '2025-08-21 12:11:00'),
(87, 'FM 223', 'Banking and Financial Institution', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Major', '2025-08-21 12:11:14', '2025-08-21 12:11:14'),
(88, 'Acctg 222', 'Financial Accounting II', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Major', '2025-08-21 12:12:09', '2025-08-21 12:12:09'),
(89, 'Acctg 222', 'Financial Accounting II', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Major', '2025-08-21 12:12:26', '2025-08-21 12:12:26'),
(90, 'PATHFit 4', 'Physical Activities Towards Health', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Minor', '2025-08-21 12:13:20', '2025-08-21 12:13:20'),
(91, 'PATHFit 4', 'Physical Activities Towards Health', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Minor', '2025-08-21 12:13:41', '2025-08-21 12:13:41'),
(92, 'BA BR 7', 'Business Ethics', 'BSBA', '2nd Year', 'FM-2A', '2', NULL, 'Bridging', '2025-08-21 12:14:18', '2025-08-21 12:14:18'),
(93, 'BA BR 7', 'Business Ethics', 'BSBA', '2nd Year', 'FM-2B', '2', NULL, 'Bridging', '2025-08-21 12:14:34', '2025-08-21 12:14:34'),
(94, 'GE 12', 'Science Technology and Society', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Minor', '2025-08-21 14:06:34', '2025-08-21 14:06:34'),
(95, 'GE 12', 'Science Technology and Society', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Minor', '2025-08-21 14:07:02', '2025-08-21 14:07:02'),
(96, 'BA 314', 'Obligations and Contract', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Major', '2025-08-21 14:07:57', '2025-08-21 14:07:57'),
(97, 'BA 314', 'Obligations and Contract', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Major', '2025-08-21 14:08:17', '2025-08-21 14:08:17'),
(98, 'BM 311', 'Strategic  Management', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Minor', '2025-08-21 14:09:02', '2025-08-21 14:09:02'),
(99, 'BM 311', 'Strategic  Management', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Minor', '2025-08-21 14:09:17', '2025-08-21 14:09:17'),
(100, 'BA 315', 'International Business and Trade', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Major', '2025-08-21 14:10:04', '2025-08-21 14:10:04'),
(101, 'BA 315', 'International Business and Trade', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Major', '2025-08-21 14:10:22', '2025-08-21 14:10:22'),
(102, 'BA 316', 'Business Research', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Major', '2025-08-21 14:10:56', '2025-08-21 14:10:56'),
(103, 'BA 316', 'Business Research', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Major', '2025-08-21 14:11:13', '2025-08-21 14:11:13'),
(104, 'FM 314', 'Monetary Policy and Central Banking', 'BSBA', '3rd Year', 'FM-3A', '1', NULL, 'Major', '2025-08-21 14:12:01', '2025-08-21 14:12:01'),
(105, 'FM 314', 'Monetary Policy and Central Banking', 'BSBA', '3rd Year', 'FM-3B', '1', NULL, 'Major', '2025-08-21 14:12:35', '2025-08-21 14:12:35'),
(106, 'BA 327', 'Human Resource Management', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:13:22', '2025-08-21 14:13:22'),
(107, 'BA 327', 'Human Resource Management', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:13:36', '2025-08-21 14:13:36'),
(108, 'FM 325', 'Investment and Portfolio Management', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:14:46', '2025-08-21 14:14:46'),
(109, 'FM 325', 'Investment and Portfolio Management', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:15:09', '2025-08-21 14:15:09'),
(110, 'FM 326', 'Credit and Collection', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:15:48', '2025-08-21 14:15:48'),
(111, 'FM 326', 'Credit and Collection', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:16:11', '2025-08-21 14:16:11'),
(112, 'FM 327', 'Capital Market', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:16:42', '2025-08-21 14:16:42'),
(113, 'FM 327', 'Capital Market', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:17:19', '2025-08-21 14:17:19'),
(114, 'ELEC 1', 'Treasury Management', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:17:53', '2025-08-21 14:17:53'),
(115, 'ELEC 1', 'Treasury Management', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:18:09', '2025-08-21 14:18:09'),
(116, 'BA 328', 'Thesis or Feasibility Study', 'BSBA', '3rd Year', 'FM-3A', '2', NULL, 'Major', '2025-08-21 14:19:00', '2025-08-21 14:19:00'),
(117, 'BA 328', 'Thesis or Feasibility Study', 'BSBA', '3rd Year', 'FM-3B', '2', NULL, 'Major', '2025-08-21 14:19:16', '2025-08-21 14:19:16'),
(118, 'ELEC 2', 'Franchising', 'BSBA', '4th Year', 'FM-4A', '1', NULL, 'Major', '2025-08-21 14:19:53', '2025-08-21 14:19:53'),
(119, 'ELEC 2', 'Franchising', 'BSBA', '4th Year', 'FM-4B', '1', NULL, 'Major', '2025-08-21 14:20:07', '2025-08-21 14:20:07'),
(120, 'ELEC 3', 'Entrepreneurial Mangement', 'BSBA', '4th Year', 'FM-4A', '1', NULL, 'Major', '2025-08-21 14:21:00', '2025-08-21 14:25:41'),
(121, 'ELEC 3', 'Entrepreneurial Mangement', 'BSBA', '4th Year', 'FM-4B', '1', NULL, 'Major', '2025-08-21 14:21:13', '2025-08-21 14:21:13'),
(122, 'BM 412', 'Operation Management and TQM', 'BSBA', '4th Year', 'FM-4A', '1', NULL, 'Major', '2025-08-21 14:21:58', '2025-08-21 14:21:58'),
(123, 'BM 412', 'Operation Management and TQM', 'BSBA', '4th Year', 'FM-4B', '1', NULL, 'Major', '2025-08-21 14:22:18', '2025-08-21 14:22:18'),
(124, 'ELEC 4', 'Global Finance with Electronic Banking', 'BSBA', '4th Year', 'FM-4A', '1', NULL, 'Major', '2025-08-21 14:23:10', '2025-08-21 14:23:10'),
(125, 'ELEC 4', 'Global Finance with Electronic Banking', 'BSBA', '4th Year', 'FM-4B', '1', NULL, 'Major', '2025-08-21 14:23:22', '2025-08-21 14:23:22'),
(126, 'FM 418', 'Special Topics in Finance Management', 'BSBA', '4th Year', 'FM-4A', '1', NULL, 'Major', '2025-08-21 14:24:10', '2025-08-21 14:24:10'),
(127, 'FM 418', 'Special Topics in Finance Management', 'BSBA', '4th Year', 'FM-4B', '1', NULL, 'Major', '2025-08-21 14:24:30', '2025-08-21 14:24:30'),
(128, 'FM 429', 'PRACTICUM Work Integrated Learning', 'BSBA', '4th Year', 'FM-4A', '2', NULL, 'Major', '2025-08-21 14:27:14', '2025-08-21 14:27:14'),
(129, 'FM 429', 'PRACTICUM Work Integrated Learning', 'BSBA', '4th Year', 'FM-4B', '2', NULL, 'Major', '2025-08-21 14:27:28', '2025-08-21 14:27:28'),
(130, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Major', '2025-08-21 14:30:32', '2025-08-21 14:30:32'),
(131, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Major', '2025-08-21 14:30:57', '2025-08-21 14:30:57'),
(132, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Major', '2025-08-21 14:31:27', '2025-08-21 14:31:27'),
(133, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Major', '2025-08-21 14:31:53', '2025-08-22 05:55:12'),
(134, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-21 14:32:04', '2025-08-21 14:32:04'),
(135, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-21 14:32:15', '2025-08-21 14:32:15'),
(136, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-21 14:32:27', '2025-08-21 14:32:27'),
(137, 'ITE 111', 'Introduction To Computing', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-21 14:32:43', '2025-08-21 14:32:43'),
(138, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Major', '2025-08-21 14:33:25', '2025-08-21 14:33:25'),
(139, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Major', '2025-08-21 14:33:41', '2025-08-21 14:33:41'),
(140, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Major', '2025-08-21 14:33:52', '2025-08-21 14:33:52'),
(141, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Major', '2025-08-21 14:34:03', '2025-08-21 14:34:03'),
(142, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-21 14:34:14', '2025-08-21 14:34:14'),
(143, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-21 14:34:26', '2025-08-21 14:34:26'),
(144, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-21 14:34:42', '2025-08-21 14:34:42'),
(145, 'ITE 112', 'Program Logic Formulation', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-21 14:34:52', '2025-08-21 14:34:52'),
(146, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Major', '2025-08-21 14:35:38', '2025-08-21 14:35:38'),
(147, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Major', '2025-08-21 14:35:50', '2025-08-21 14:35:50'),
(148, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Major', '2025-08-21 14:36:02', '2025-08-21 14:36:02'),
(149, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'EAST', '1', 'Jareed Cueva', 'Major', '2025-08-21 14:36:13', '2025-08-22 08:46:31'),
(150, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-21 14:36:25', '2025-08-21 14:36:25'),
(151, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-21 14:37:03', '2025-08-21 14:37:03'),
(152, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-21 14:37:17', '2025-08-21 14:37:17'),
(153, 'ITE 113', 'Computer Assembly Troubleshooting and Repair', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-21 14:37:32', '2025-08-21 14:37:32'),
(154, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Major', '2025-08-21 14:38:32', '2025-08-21 14:38:32'),
(155, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Major', '2025-08-21 14:38:59', '2025-08-21 14:38:59'),
(156, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Major', '2025-08-21 14:39:09', '2025-08-21 14:39:09'),
(157, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Major', '2025-08-21 14:39:19', '2025-08-21 14:39:19'),
(158, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-21 14:39:32', '2025-08-21 14:39:32'),
(159, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-21 14:39:46', '2025-08-21 14:39:46'),
(160, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-21 14:39:56', '2025-08-21 14:39:56'),
(161, 'GE 3', 'Mathematics in the Modern World', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-21 14:40:08', '2025-08-21 14:40:08'),
(162, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Minor', '2025-08-21 14:40:48', '2025-08-21 14:40:48'),
(163, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Minor', '2025-08-21 14:41:02', '2025-08-21 14:41:02'),
(164, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-21 14:41:13', '2025-08-21 14:41:13'),
(165, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Minor', '2025-08-21 14:41:25', '2025-08-21 14:41:25'),
(166, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-21 14:42:01', '2025-08-21 14:42:01'),
(167, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-21 14:42:18', '2025-08-21 14:42:18'),
(168, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-21 14:42:31', '2025-08-21 14:42:31'),
(169, 'GE 4', 'The Contemporary World', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-21 14:42:42', '2025-08-21 14:42:42'),
(170, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Minor', '2025-08-21 14:43:49', '2025-08-21 14:43:49'),
(171, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Minor', '2025-08-21 14:44:09', '2025-08-21 14:44:09'),
(172, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-21 14:44:22', '2025-08-21 14:44:22'),
(173, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Minor', '2025-08-21 14:44:34', '2025-08-21 14:44:34'),
(174, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-21 14:44:46', '2025-08-21 14:44:46'),
(175, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-21 14:45:01', '2025-08-21 14:45:01'),
(176, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-21 14:45:12', '2025-08-21 14:45:12'),
(177, 'GEFIL 1', 'Komunikasyon sa Akademikong Filpino', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-21 14:45:22', '2025-08-21 14:45:22'),
(179, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Minor', '2025-08-21 14:46:33', '2025-08-21 14:46:33'),
(180, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-21 14:46:43', '2025-08-21 14:46:43'),
(182, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-21 14:47:06', '2025-08-21 14:47:06'),
(184, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-21 14:48:04', '2025-08-21 14:48:04'),
(188, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Minor', '2025-08-21 14:56:39', '2025-08-21 14:56:39'),
(189, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Minor', '2025-08-21 14:57:02', '2025-08-21 14:57:02'),
(190, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-21 14:57:23', '2025-08-21 14:57:23'),
(193, 'GE ELECT 1', 'People and Earths Ecosystem', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-21 15:34:49', '2025-08-21 15:34:49'),
(194, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Minor', '2025-08-21 15:39:29', '2025-08-21 15:39:29'),
(195, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Minor', '2025-08-21 15:43:19', '2025-08-21 15:43:19'),
(196, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-21 15:43:34', '2025-08-21 15:43:34'),
(197, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Minor', '2025-08-21 15:43:48', '2025-08-21 15:43:48'),
(198, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-21 15:44:04', '2025-08-21 15:44:04'),
(199, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-21 15:44:17', '2025-08-21 15:44:17'),
(200, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-21 15:44:32', '2025-08-21 15:44:32'),
(201, 'PATHFit 1', 'Movement Competency Training', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-21 15:44:48', '2025-08-21 15:44:48'),
(202, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'NORTH', '1', NULL, 'Minor', '2025-08-21 15:45:43', '2025-08-21 15:45:43'),
(203, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'WEST', '1', NULL, 'Minor', '2025-08-21 15:46:15', '2025-08-21 15:46:15'),
(204, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-21 15:46:28', '2025-08-21 15:46:28'),
(205, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'EAST', '1', NULL, 'Minor', '2025-08-21 15:46:40', '2025-08-21 15:46:40'),
(206, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-21 15:46:59', '2025-08-21 15:46:59'),
(207, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-21 15:47:14', '2025-08-21 15:47:14'),
(208, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-21 15:47:31', '2025-08-21 15:47:31'),
(209, 'NSTP 1', 'National Service Training Program', 'BSIT', '1st Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-21 15:47:46', '2025-08-21 15:47:46'),
(210, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Major', '2025-08-21 15:56:41', '2025-08-21 15:56:41'),
(211, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Major', '2025-08-21 15:57:16', '2025-08-21 15:57:16'),
(212, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Major', '2025-08-21 15:57:30', '2025-08-21 15:57:30'),
(213, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Major', '2025-08-21 15:57:46', '2025-08-21 15:57:46'),
(214, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Major', '2025-08-21 15:58:01', '2025-08-21 15:58:01'),
(215, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Major', '2025-08-21 15:58:28', '2025-08-21 15:58:28'),
(216, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Major', '2025-08-21 15:58:44', '2025-08-21 15:58:44'),
(217, 'ITE 121', 'Computer Programming 1', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Major', '2025-08-21 15:59:13', '2025-08-21 15:59:13'),
(218, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Major', '2025-08-21 15:59:50', '2025-08-21 15:59:50'),
(219, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Major', '2025-08-21 16:00:09', '2025-08-21 16:00:09'),
(220, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Major', '2025-08-21 16:00:23', '2025-08-21 16:00:23'),
(221, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'EAST', '2', 'Glenford P. Buncal', 'Major', '2025-08-21 16:00:38', '2025-08-26 05:24:00'),
(222, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Major', '2025-08-21 16:00:54', '2025-08-21 16:00:54'),
(223, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Major', '2025-08-21 16:01:11', '2025-08-21 16:01:11'),
(224, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Major', '2025-08-21 16:01:26', '2025-08-21 16:01:26'),
(225, 'ITE 122', 'Computer Networking 1', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Major', '2025-08-21 16:01:41', '2025-08-21 16:01:41'),
(226, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:02:27', '2025-08-21 16:02:27'),
(227, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:02:59', '2025-08-21 16:02:59'),
(228, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:03:18', '2025-08-21 16:03:18'),
(229, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:03:47', '2025-08-21 16:03:47'),
(230, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:04:13', '2025-08-21 16:04:13'),
(231, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:04:31', '2025-08-21 16:04:31'),
(232, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:04:53', '2025-08-21 16:04:53'),
(233, 'ITE 123', 'Discrete Mathematics', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:05:24', '2025-08-21 16:05:24'),
(234, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:08:55', '2025-08-21 16:08:55'),
(235, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:09:22', '2025-08-21 16:09:22'),
(236, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:09:39', '2025-08-21 16:09:39'),
(237, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:10:00', '2025-08-21 16:10:00'),
(238, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:10:20', '2025-08-21 16:10:20'),
(239, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:10:47', '2025-08-21 16:10:47'),
(240, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:11:03', '2025-08-21 16:11:03'),
(241, 'GE 1', 'Understanding The Self', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:11:19', '2025-08-21 16:11:19'),
(242, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:12:26', '2025-08-21 16:12:26'),
(243, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:12:49', '2025-08-21 16:12:49'),
(244, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:13:04', '2025-08-21 16:13:04'),
(245, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:13:22', '2025-08-21 16:13:22'),
(246, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:13:38', '2025-08-21 16:13:38'),
(247, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:13:56', '2025-08-21 16:13:56'),
(248, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:14:13', '2025-08-21 16:14:13'),
(249, 'GE 2', 'Reading in the Philippine History', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:14:32', '2025-08-21 16:14:32'),
(250, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:16:52', '2025-08-21 16:16:52'),
(251, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:17:20', '2025-08-21 16:17:20'),
(252, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:17:36', '2025-08-21 16:17:36'),
(253, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:17:53', '2025-08-21 16:17:53'),
(254, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:18:08', '2025-08-21 16:18:08'),
(255, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:18:27', '2025-08-21 16:18:27'),
(256, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:18:45', '2025-08-21 16:18:45'),
(257, 'GE 6', 'Purposive Communication', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:19:06', '2025-08-21 16:19:06'),
(258, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:20:46', '2025-08-21 16:20:46'),
(259, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:21:01', '2025-08-21 16:21:01'),
(260, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:21:24', '2025-08-21 16:21:24'),
(261, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:21:40', '2025-08-21 16:21:40'),
(262, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:22:33', '2025-08-21 16:22:33'),
(263, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:23:00', '2025-08-21 16:23:00'),
(264, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:23:20', '2025-08-21 16:23:20'),
(265, 'GEFIL 2', 'Pagbasa at Pagsulat Tungo sa Pananaliksik', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:23:38', '2025-08-21 16:23:38'),
(266, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:25:12', '2025-08-21 16:25:12'),
(267, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:26:11', '2025-08-21 16:26:11'),
(268, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:26:38', '2025-08-21 16:26:38'),
(269, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:27:05', '2025-08-21 16:27:05'),
(270, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:27:22', '2025-08-21 16:27:22'),
(271, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:27:40', '2025-08-21 16:27:40'),
(272, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:28:18', '2025-08-21 16:28:18'),
(273, 'PATHFit 2', 'Exercise based Fitness Activities', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:28:33', '2025-08-21 16:28:33'),
(274, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'NORTH', '2', NULL, 'Minor', '2025-08-21 16:29:56', '2025-08-21 16:29:56'),
(275, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'WEST', '2', NULL, 'Minor', '2025-08-21 16:30:22', '2025-08-21 16:30:22'),
(276, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-21 16:30:41', '2025-08-21 16:30:41'),
(277, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'EAST', '2', NULL, 'Minor', '2025-08-21 16:31:00', '2025-08-21 16:31:00'),
(278, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-21 16:31:32', '2025-08-21 16:31:32'),
(279, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-21 16:31:50', '2025-08-21 16:31:50'),
(280, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-21 16:32:09', '2025-08-21 16:32:09'),
(281, 'NSTP 2', 'National Service Training Program 2', 'BSIT', '1st Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-21 16:32:27', '2025-08-21 16:32:27'),
(282, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-21 16:57:43', '2025-08-21 16:57:43'),
(283, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-21 17:04:35', '2025-08-21 17:04:35'),
(284, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-21 17:08:33', '2025-08-21 17:08:33'),
(285, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-21 17:08:53', '2025-08-21 17:08:53'),
(286, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-21 17:09:19', '2025-08-21 17:09:19'),
(287, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-21 17:09:36', '2025-08-21 17:09:36'),
(288, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-21 17:09:55', '2025-08-21 17:09:55'),
(289, 'ITE 211', 'Computer Prorgramming 2', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-21 17:10:10', '2025-08-21 17:10:10'),
(290, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-22 01:08:34', '2025-08-22 01:08:34'),
(291, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-22 01:09:19', '2025-08-22 01:09:19'),
(292, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-22 01:09:49', '2025-08-22 01:09:49'),
(293, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-22 01:10:06', '2025-08-22 01:10:06'),
(294, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-22 01:10:24', '2025-08-22 01:10:24'),
(295, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-22 01:10:42', '2025-08-22 01:10:42'),
(296, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-22 01:10:58', '2025-08-22 01:10:58'),
(297, 'ITE 212', 'Graphics Designing', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-22 01:11:21', '2025-08-22 01:11:21'),
(298, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-22 01:13:03', '2025-08-22 01:13:03'),
(299, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-22 01:13:41', '2025-08-22 01:13:41'),
(300, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-22 01:14:08', '2025-08-22 01:14:08'),
(301, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-22 01:14:25', '2025-08-22 01:14:25'),
(302, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-22 01:14:44', '2025-08-22 01:14:44'),
(303, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-22 01:15:02', '2025-08-22 01:15:02'),
(304, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-22 01:15:21', '2025-08-22 01:15:21'),
(305, 'ITE 213', 'Information Management', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-22 01:15:48', '2025-08-22 01:15:48'),
(306, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-22 01:17:08', '2025-08-22 01:17:08'),
(307, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-22 01:17:47', '2025-08-22 01:17:47'),
(308, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-22 01:18:20', '2025-08-22 01:18:20'),
(309, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-22 01:18:42', '2025-08-22 01:18:42'),
(310, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-22 01:18:57', '2025-08-22 01:18:57'),
(311, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-22 01:19:16', '2025-08-22 01:19:16'),
(312, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-22 01:19:41', '2025-08-22 01:19:41'),
(313, 'ITE 214', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-22 01:20:02', '2025-08-22 01:20:02'),
(314, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-22 01:21:29', '2025-08-22 01:21:29'),
(315, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-22 01:21:47', '2025-08-22 01:21:47'),
(316, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-22 01:22:05', '2025-08-22 01:22:05'),
(317, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-22 01:22:25', '2025-08-22 01:22:25'),
(318, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-22 01:22:42', '2025-08-22 01:22:42'),
(319, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-22 01:23:02', '2025-08-22 01:23:02'),
(320, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-22 01:23:28', '2025-08-22 01:23:28'),
(321, 'ITE 215', 'Platforms Technologies', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-22 01:23:49', '2025-08-22 01:23:49'),
(322, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Major', '2025-08-22 01:26:11', '2025-08-22 01:26:11'),
(323, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Major', '2025-08-22 01:26:43', '2025-08-22 01:26:43'),
(324, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Major', '2025-08-22 01:27:02', '2025-08-22 01:27:02'),
(325, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Major', '2025-08-22 01:27:21', '2025-08-22 01:27:21'),
(326, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Major', '2025-08-22 01:27:51', '2025-08-22 01:27:51'),
(327, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Major', '2025-08-22 01:28:17', '2025-08-22 01:28:17'),
(328, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Major', '2025-08-22 01:28:45', '2025-08-22 01:28:45'),
(329, 'IT Elect 1', 'IT Elective 1 Fundamentals of Accounting', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Major', '2025-08-22 01:29:05', '2025-08-22 01:29:05'),
(330, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Minor', '2025-08-22 01:30:50', '2025-08-22 01:30:50'),
(331, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Minor', '2025-08-22 01:31:22', '2025-08-22 01:31:22'),
(332, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-22 01:31:43', '2025-08-22 01:31:43'),
(333, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Minor', '2025-08-22 01:32:02', '2025-08-22 01:32:02'),
(334, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-22 01:32:22', '2025-08-22 01:32:22'),
(335, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-22 01:32:43', '2025-08-22 01:32:43'),
(336, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-22 01:33:05', '2025-08-22 01:33:05'),
(337, 'GE ELECT 2', 'Philippine Popular Culture', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-22 01:33:25', '2025-08-22 01:33:25'),
(338, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTH', '1', NULL, 'Minor', '2025-08-22 01:37:16', '2025-08-22 01:37:16'),
(339, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'WEST', '1', NULL, 'Minor', '2025-08-22 01:37:43', '2025-08-22 01:37:43'),
(340, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTH', '1', NULL, 'Minor', '2025-08-22 01:38:07', '2025-08-22 01:38:07'),
(341, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'EAST', '1', NULL, 'Minor', '2025-08-22 01:38:28', '2025-08-22 01:38:28'),
(342, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTHWEST', '1', NULL, 'Minor', '2025-08-22 01:38:45', '2025-08-22 01:38:45'),
(343, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTHWEST', '1', NULL, 'Minor', '2025-08-22 01:39:04', '2025-08-22 01:39:04'),
(344, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTHEAST', '1', NULL, 'Minor', '2025-08-22 01:39:20', '2025-08-22 01:39:20'),
(345, 'PATHFit 3', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTHEAST', '1', NULL, 'Minor', '2025-08-22 01:39:42', '2025-08-22 01:39:42'),
(346, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Major', '2025-08-22 01:40:51', '2025-08-22 01:40:51'),
(347, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Major', '2025-08-22 01:42:23', '2025-08-22 01:42:23'),
(348, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Major', '2025-08-22 01:42:40', '2025-08-22 01:42:40'),
(349, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Major', '2025-08-22 01:42:58', '2025-08-22 01:42:58'),
(350, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Major', '2025-08-22 01:43:15', '2025-08-22 01:43:15'),
(351, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Major', '2025-08-22 01:43:33', '2025-08-22 01:43:33'),
(352, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Major', '2025-08-22 01:43:50', '2025-08-22 01:43:50'),
(353, 'ITE 221', 'Data Structures and Algorithms', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Major', '2025-08-22 01:44:07', '2025-08-22 01:44:07'),
(354, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Major', '2025-08-22 01:45:29', '2025-08-22 01:45:29'),
(355, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Major', '2025-08-22 01:45:50', '2025-08-22 01:45:50'),
(356, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Major', '2025-08-22 01:46:05', '2025-08-22 01:46:05'),
(357, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Major', '2025-08-22 01:46:23', '2025-08-22 01:46:23'),
(358, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Major', '2025-08-22 01:46:42', '2025-08-22 01:46:42'),
(359, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Major', '2025-08-22 01:47:01', '2025-08-22 01:47:01'),
(360, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Major', '2025-08-22 01:47:17', '2025-08-22 01:47:17'),
(361, 'ITE 222', 'Multimedia System', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Major', '2025-08-22 01:47:32', '2025-08-22 01:47:32'),
(362, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Major', '2025-08-22 01:48:12', '2025-08-22 01:48:12'),
(363, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Major', '2025-08-22 01:48:28', '2025-08-22 01:48:28'),
(364, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Major', '2025-08-22 01:48:45', '2025-08-22 01:48:45'),
(365, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Major', '2025-08-22 01:49:04', '2025-08-22 01:49:04'),
(366, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Major', '2025-08-22 01:49:27', '2025-08-22 01:49:27'),
(367, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Major', '2025-08-22 01:49:47', '2025-08-22 01:49:47'),
(368, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Major', '2025-08-22 01:50:13', '2025-08-22 01:50:13'),
(369, 'ITE 223', 'Digital Logic Design', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Major', '2025-08-22 01:50:30', '2025-08-22 01:50:30'),
(370, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Minor', '2025-08-22 01:52:04', '2025-08-22 01:52:04'),
(371, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Minor', '2025-08-22 01:52:21', '2025-08-22 01:52:21'),
(372, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-22 01:52:39', '2025-08-22 01:52:39'),
(373, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Minor', '2025-08-22 01:52:59', '2025-08-22 01:52:59'),
(374, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-22 01:53:18', '2025-08-22 01:53:18'),
(375, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-22 01:53:43', '2025-08-22 01:53:43'),
(376, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-22 01:54:02', '2025-08-22 01:54:02'),
(377, 'ITE 224', 'Advanced Office Productivity', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-22 01:54:19', '2025-08-22 01:54:19'),
(378, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Minor', '2025-08-22 01:56:00', '2025-08-22 01:56:00'),
(379, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Minor', '2025-08-22 01:56:21', '2025-08-22 01:56:21');
INSERT INTO `subjects` (`id`, `sub_code`, `sub_name`, `sub_department`, `sub_year`, `section`, `semester`, `assign_instructor`, `subject_type`, `created_at`, `updated_at`) VALUES
(380, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-22 01:56:40', '2025-08-22 01:56:40'),
(381, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Minor', '2025-08-22 01:56:57', '2025-08-22 01:56:57'),
(382, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-22 01:57:14', '2025-08-22 01:57:14'),
(383, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-22 01:57:32', '2025-08-22 01:57:32'),
(384, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-22 01:57:50', '2025-08-22 01:57:50'),
(385, 'IT Elect 2', 'IT Elective2 Ergonomics and Facility Planning', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-22 01:58:11', '2025-08-22 01:58:11'),
(386, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Minor', '2025-08-22 01:58:46', '2025-08-22 01:58:46'),
(387, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Minor', '2025-08-22 01:59:28', '2025-08-22 01:59:28'),
(388, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-22 01:59:47', '2025-08-22 01:59:47'),
(389, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Minor', '2025-08-22 02:00:06', '2025-08-22 02:00:06'),
(390, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-22 02:00:31', '2025-08-22 02:00:31'),
(391, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-22 02:00:51', '2025-08-22 02:00:51'),
(392, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-22 02:01:08', '2025-08-22 02:01:08'),
(393, 'GE 5', 'Ethics', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-22 02:01:31', '2025-08-22 02:01:31'),
(394, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Minor', '2025-08-22 02:02:38', '2025-08-22 02:02:38'),
(395, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Minor', '2025-08-22 02:03:01', '2025-08-22 02:03:01'),
(396, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-22 02:03:25', '2025-08-22 02:03:25'),
(397, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Minor', '2025-08-22 02:03:42', '2025-08-22 02:03:42'),
(398, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-22 02:04:03', '2025-08-22 02:04:03'),
(399, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-22 02:04:22', '2025-08-22 02:04:22'),
(400, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-22 02:04:41', '2025-08-22 02:04:41'),
(401, 'GE 8', 'Science Technology and Society', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-22 02:05:00', '2025-08-22 02:05:00'),
(402, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTH', '2', NULL, 'Minor', '2025-08-22 02:06:50', '2025-08-22 02:06:50'),
(403, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'WEST', '2', NULL, 'Minor', '2025-08-22 02:07:06', '2025-08-22 02:07:06'),
(404, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTH', '2', NULL, 'Minor', '2025-08-22 02:07:24', '2025-08-22 02:07:24'),
(405, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'EAST', '2', NULL, 'Minor', '2025-08-22 02:07:41', '2025-08-22 02:07:41'),
(406, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTHWEST', '2', NULL, 'Minor', '2025-08-22 02:07:57', '2025-08-22 02:07:57'),
(407, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTHWEST', '2', NULL, 'Minor', '2025-08-22 02:08:14', '2025-08-22 02:08:14'),
(408, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'SOUTHEAST', '2', NULL, 'Minor', '2025-08-22 02:08:30', '2025-08-22 02:08:30'),
(409, 'PATHFit 4', 'ChoiceofDanceSportsMartialArtsGroupExercise', 'BSIT', '2nd Year', 'NORTHEAST', '2', NULL, 'Minor', '2025-08-22 02:08:48', '2025-08-22 02:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `profile_image` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `is_main_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `school_id`, `role`, `profile_image`, `course`, `year_level`, `section`, `status`, `is_main_admin`, `last_login`, `last_active_at`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Main Admin', 'admin@gmail.com', '$2y$12$OcLwTbzl2qEX8HA0himNPeTzt6xtdwtOHwMq4wTflz6Z/BHpADri6', 'Administrator', NULL, 'admin', '1750266339_snapedit_1745568681642.jpeg.png', 'Student Affairs', NULL, NULL, 'active', 1, '2025-08-26 05:26:16', NULL, NULL, NULL, '2025-06-16 21:09:38', '2025-08-26 05:26:16'),
(71, 'Wardass', 'warren.ilustrisimo@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$Z25tTkk4ZHJMSUJER3pydQ$+F3EKj+7R5Qe9mEVrure/8JQMR91TrDBsdtnoqhPf1k', 'Warren Ilustrisimo', '2022-1969', 'student', '1755144539_dev1.png', 'BSIT', '1st Year', 'EAST', 'active', 0, '2025-08-26 05:27:04', NULL, NULL, NULL, '2025-08-14 04:09:29', '2025-08-26 05:27:04'),
(72, 'Mikoy', 'michael.layaog@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$MjZhRjN0WGNFOE1XVFV0Ng$hIFOZDWbPHdQDnSRgPhL/WANxKTzHn7k7xqHPKCYpTU', 'Michael Layaog', '2222-2222', 'student', '1755150605_480665539_628400923314794_4185009517735006139_n.jpg', 'BSBA', '3rd Year', 'FM-3H', 'active', 0, NULL, NULL, NULL, NULL, '2025-08-14 05:50:44', '2025-08-14 05:50:44'),
(73, 'Loben', 'harold.ofaga@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$LjNSU3MvRnljNTI0ZlgvMw$hWVd4Lz8Skieeygp58ccVq75JWKOEHfKVU/TTw9y7gA', 'Harold Ofaga', '1111-1111', 'student', '1755150493_loben.jpg', 'BSHM', '3rd Year', 'BSHM-3D', 'active', 0, '2025-08-14 06:05:52', NULL, NULL, NULL, '2025-08-14 05:50:51', '2025-08-14 06:05:52'),
(74, 'Jov', 'jovelyn.canseran@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$Lk1OTnZaSWs5S1NXSmJvQQ$2EzLLxOY0yB+0IYv9NRFBF+Pyok77jrE1nMhUpRD580', 'Jovelyn Canseran', '3333-3333', 'student', '1755150795_genlou.jpg', 'BEED', '2nd Year', '2-TODDLER', 'active', 0, NULL, NULL, NULL, NULL, '2025-08-14 06:57:57', '2025-08-14 06:57:57'),
(76, 'loyd', 'loyd.sarabia@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$OW52d2ZZWHI3ejlTZU9nWA$fFckNm+e551GxNbKXstBeTD1pseQYNZuNDu6cvrmtY4', 'john loyd sarabia', '2022-1896', 'student', '1755600464_1000156289-Picsart-AiImageEnhancer.jpg.png', 'BSIT', '4th Year', 'EAST', 'active', 0, NULL, NULL, NULL, NULL, '2025-08-19 10:48:15', '2025-08-19 10:48:15'),
(77, 'Doy', 'jenford.albaciete@mcclawis.edu.ph', '$argon2id$v=19$m=65536,t=4,p=1$YU9oMDE0YnVzLmVFRDQvRA$TQnowh7BZe/W6VO0aQor6JV5KB68pqYwWBKeWVhws24', 'Jenford Albaciete', '2022-3018', 'student', '1755835857_students.jpg', 'BSIT', '4th Year', 'EAST', 'active', 0, NULL, NULL, NULL, NULL, '2025-08-22 04:11:13', '2025-08-22 04:11:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year_semester` (`year`,`semester`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluations_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `questions_title_description_academic_year_unique` (`title`,`description`,`academic_year_id`) USING HASH;

--
-- Indexes for table `request_signin`
--
ALTER TABLE `request_signin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `response_options`
--
ALTER TABLE `response_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_questions`
--
ALTER TABLE `saved_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saved_questions_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `save_eval_result`
--
ALTER TABLE `save_eval_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_staff_id_unique` (`staff_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sub_code_section` (`sub_code`,`section`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_school_id_unique` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=624;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=516;

--
-- AUTO_INCREMENT for table `request_signin`
--
ALTER TABLE `request_signin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `response_options`
--
ALTER TABLE `response_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `saved_questions`
--
ALTER TABLE `saved_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=274;

--
-- AUTO_INCREMENT for table `save_eval_result`
--
ALTER TABLE `save_eval_result`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=411;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `saved_questions`
--
ALTER TABLE `saved_questions`
  ADD CONSTRAINT `saved_questions_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
