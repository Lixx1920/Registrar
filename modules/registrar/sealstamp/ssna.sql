-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2026 at 02:13 PM
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
-- Database: `sms2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_name` varchar(150) DEFAULT NULL,
  `role_key` varchar(40) DEFAULT NULL,
  `action` varchar(40) NOT NULL,
  `module_key` varchar(60) DEFAULT NULL,
  `detail` varchar(500) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(3, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 08:59:38'),
(4, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated Super Admin profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 09:02:04'),
(5, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 09:02:07'),
(6, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:04:48'),
(7, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:46:02'),
(8, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:46:18'),
(9, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated Super Admin profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:46:29'),
(10, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:46:33'),
(11, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:47:36'),
(12, 1, 'Super Admin', 'admin', 'view', 'enrollment', 'Opened Enrollment Management Module Security', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:51:40'),
(13, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 12:53:42'),
(14, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:05:23'),
(15, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:05:45'),
(16, 1, 'Super Admin', 'admin', 'password_reset_request', 'System', 'Password reset link emailed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:05:59'),
(17, 1, NULL, NULL, 'password_reset', 'System', 'Password reset via token', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:06:28'),
(18, 9, 'Student User', 'student', 'lockout', 'System', 'Login locked after failed attempts', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:06:33'),
(19, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:06:34'),
(20, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:08:29'),
(21, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:08:46'),
(22, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:09:03'),
(23, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:09:41'),
(24, 1, 'Super Admin', 'admin', 'password_reset_request', 'user-management', 'OTP emailed for Super Admin password change', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:10:21'),
(25, 1, 'Super Admin', 'admin', 'password_change', 'user-management', 'Super Admin password reset via account settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:10:29'),
(26, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:11:02'),
(27, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:11:14'),
(28, 9, 'Student User', 'student', 'lockout', 'System', 'Login locked after failed attempts', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:11:19'),
(29, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:11:19'),
(30, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:12:08'),
(31, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:12:17'),
(32, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:13:35'),
(33, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:13:48'),
(34, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:15:37'),
(35, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user registrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:16:14'),
(36, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user s230000001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:17:16'),
(37, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:17:20'),
(38, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:17:32'),
(39, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:17:41'),
(40, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:17:44'),
(41, 9, 'Student User', 'student', 'password_reset_request', 'System', 'Password reset link emailed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:18:27'),
(42, 9, 'Student User', 'student', 'password_reset_request', 'System', 'Password reset link emailed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:18:36'),
(43, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:21:02'),
(44, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:22:47'),
(45, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:25:47'),
(46, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user cradofficer', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:28:10'),
(47, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Archived user #10 (status=inactive)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:29:05'),
(48, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:29:42'),
(49, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:29:51'),
(50, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user cradofficer', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:30:40'),
(51, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:30:43'),
(52, 3, 'CRAD Officer', 'crad_officer', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:30:55'),
(53, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:31:36'),
(54, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user cradofficer', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:31:50'),
(55, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:31:52'),
(56, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:32:15'),
(57, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:41:07'),
(58, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:41:17'),
(59, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:41:26'),
(60, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:41:52'),
(61, 3, 'CRAD Officer', 'crad_officer', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:46:20'),
(62, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:55:08'),
(63, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 13:55:34'),
(64, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:02:03'),
(65, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:02:28'),
(66, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:06:11'),
(67, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:06:38'),
(68, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user s230000001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:17:40'),
(69, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:18:23'),
(70, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:18:37'),
(71, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:32:52'),
(72, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user s230000001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:32:59'),
(73, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user finance', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:35:47'),
(74, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:36:52'),
(75, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:37:11'),
(76, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:43:31'),
(77, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 14:44:03'),
(78, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:07:06'),
(79, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:13:15'),
(80, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:52:18'),
(81, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:52:21'),
(82, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:53:03'),
(83, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 15:54:55'),
(84, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 16:36:05'),
(85, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 16:51:54'),
(86, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 17:34:40'),
(87, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 17:36:17'),
(88, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00002 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 18:22:42'),
(89, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00003 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 18:36:18'),
(90, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 19:10:53'),
(91, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00002 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 19:11:52'),
(92, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:03:13'),
(93, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:03:50'),
(94, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Permission registrar:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:12:08'),
(95, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Permission registrar:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:12:08'),
(96, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user finance', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:20:39'),
(97, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:20:47'),
(98, 4, 'Finance', 'finance', 'login', 'payment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:20:55'),
(99, 4, 'Finance', 'finance', 'logout', 'payment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:21:04'),
(100, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:21:27'),
(101, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:22:34'),
(102, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:25:41'),
(103, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:26:12'),
(104, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-06 20:26:19'),
(105, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:43:55'),
(106, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:44:40'),
(107, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:44:46'),
(108, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 11:45:15'),
(109, 3, 'CRAD Officer', 'crad_officer', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 12:33:08'),
(110, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:08:00'),
(111, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00003 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:15:45'),
(112, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:39:34'),
(113, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:46:38'),
(114, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:46:45'),
(115, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:47:17'),
(116, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:47:43'),
(117, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:51:23'),
(118, 4, 'Finance', 'finance', 'login', 'payment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:51:31'),
(119, 4, 'Finance', 'finance', 'logout', 'payment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:55:57'),
(120, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 13:56:06'),
(121, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:02:05'),
(122, 4, 'Finance', 'finance', 'login', 'payment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:02:19'),
(123, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:06:50'),
(124, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:06:57'),
(125, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:07:01'),
(126, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:07:10'),
(127, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:07:40'),
(128, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:10:29'),
(129, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:10:36'),
(130, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user hr', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:10:53'),
(131, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:10:55'),
(132, 8, 'HR', 'hr', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:11:03'),
(133, 8, 'HR', 'hr', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:11:15'),
(134, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:11:24'),
(135, 4, 'Finance', 'finance', 'logout', 'payment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:11:33'),
(136, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:11:41'),
(137, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:12:33'),
(138, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:22:41'),
(139, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:22:49'),
(140, 1, 'Super Admin', 'admin', 'update', 'System', 'Added passkey: This device', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:23:01'),
(141, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:23:06'),
(142, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:23:21'),
(143, 1, 'Super Admin', 'admin', 'update', 'System', 'Enabled Google Authenticator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:24:39'),
(144, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:24:41'),
(145, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:24:56'),
(146, 1, 'Super Admin', 'admin', 'update', 'System', 'Disabled Google Authenticator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:25:46'),
(147, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:27:34'),
(148, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:27:42'),
(149, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:27:50'),
(150, 4, 'Finance', 'finance', 'login', 'payment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:28:00'),
(151, 4, 'Finance', 'finance', 'logout', 'payment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:28:02'),
(152, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:28:10'),
(153, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:32:37'),
(154, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:33:33'),
(155, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:33:55'),
(156, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:37:05'),
(157, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 14:37:12'),
(158, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:04:56'),
(159, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:18:44'),
(160, 3, 'CRAD Officer', 'crad_officer', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:19:12'),
(161, 3, 'CRAD Officer', 'crad_officer', 'update', 'System', 'Enabled Google Authenticator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:21:14'),
(162, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:21:16'),
(163, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:22:03'),
(164, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:22:09'),
(165, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:22:17'),
(166, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:23:34'),
(167, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:23:46'),
(168, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:23:57'),
(169, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00004 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:27:28'),
(170, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:27:30'),
(171, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:27:51'),
(172, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:29:05'),
(173, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 15:29:25'),
(174, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 21:37:37'),
(175, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 21:37:40'),
(176, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 21:59:44'),
(177, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 22:01:44'),
(178, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 22:01:51'),
(179, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-07 22:14:54'),
(180, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:37:06'),
(181, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:37:41'),
(182, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:37:49'),
(183, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user studentaffairs', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:38:23'),
(184, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:40:43'),
(185, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:40:52'),
(186, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:14'),
(187, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:16'),
(188, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:21'),
(189, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:23'),
(190, 1, 'Super Admin', 'admin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:26'),
(191, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:29'),
(192, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 01:48:41'),
(193, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 12:12:02'),
(194, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 12:12:12'),
(195, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 12:38:51'),
(196, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 12:49:15'),
(197, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 12:53:19'),
(198, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:03:57'),
(199, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:04:02'),
(200, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:05:55'),
(201, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:06:05'),
(202, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:07:15'),
(203, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:08:59'),
(204, 9, 'Student User', 'student', 'create', 'student_portal', 'Resubmitted revised research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:33:56'),
(205, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00006', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:37:18'),
(206, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:37:21'),
(207, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:39:20'),
(208, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:39:46'),
(209, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:39:48'),
(210, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:41:46'),
(211, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00008', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:42:32'),
(212, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:42:39'),
(213, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:44:07'),
(214, 9, 'Student User', 'student', 'create', 'student_portal', 'Resubmitted revised research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 13:45:25'),
(215, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:52:25'),
(216, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:52:55'),
(217, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:53:04');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(218, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:53:10'),
(219, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:53:19'),
(220, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:53:53'),
(221, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:54:01'),
(222, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:54:11'),
(223, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:54:19'),
(224, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:54:41'),
(225, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:54:56'),
(226, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:55:04'),
(227, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 15:55:12'),
(228, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 16:01:45'),
(229, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 16:01:54'),
(230, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 16:32:36'),
(231, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 16:37:00'),
(232, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 16:46:27'),
(233, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:02:31'),
(234, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:03:28'),
(235, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:03:31'),
(236, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:07:08'),
(237, 1, 'Super Admin', 'admin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:07:17'),
(238, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Permission registrar:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:16:54'),
(239, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Permission registrar:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:18:42'),
(240, 1, 'Super Admin', 'admin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:00'),
(241, 1, 'Super Admin', 'admin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:07'),
(242, 1, 'Super Admin', 'superadmin', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:13'),
(243, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:20'),
(244, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:25'),
(245, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:26'),
(246, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:39'),
(247, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:registrar = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:41'),
(248, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:crad = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:42'),
(249, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Reset all role permissions to defaults', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:49'),
(250, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:registrar = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:28:55'),
(251, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:03'),
(252, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:crad = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:06'),
(253, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:lms = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:07'),
(254, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:reports-analytics = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:07'),
(255, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:cocurricular = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:08'),
(256, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:scheduling = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:08'),
(257, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:faculty = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:08'),
(258, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:payment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:09'),
(259, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:accreditation = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:09'),
(260, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:curriculum = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:10'),
(261, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:registrar = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:11'),
(262, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:12'),
(263, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:12'),
(264, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:29:14'),
(265, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:registrar = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:34:34'),
(266, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:34:54'),
(267, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:34:56'),
(268, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission crad_officer:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:10'),
(269, 1, 'Super Admin', 'superadmin', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:11'),
(270, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:19'),
(271, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:22'),
(272, 1, 'Super Admin', 'superadmin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:29'),
(273, 1, 'Super Admin', 'superadmin', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:35'),
(274, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission crad_officer:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:39'),
(275, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:41'),
(276, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:registrar = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:41'),
(277, 1, 'Super Admin', 'superadmin', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:43'),
(278, 1, 'Super Admin', 'superadmin', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:51'),
(279, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:54'),
(280, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:registrar = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:35:54'),
(281, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:reports-analytics = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:40:49'),
(282, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:reports-analytics = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:40:49'),
(283, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Reset all role permissions to defaults', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:41:08'),
(284, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission admission:registrar = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:41:14'),
(285, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:41:16'),
(286, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user registrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:51:29'),
(287, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user registrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:51:38'),
(288, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:51:39'),
(289, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:51:44'),
(290, 2, 'Registrar', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:51:59'),
(291, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:52:07'),
(292, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:52:12'),
(293, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:52:12'),
(294, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:52:13'),
(295, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:52:18'),
(296, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission student:student_portal = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:55:49'),
(297, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission student:student_portal = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:55:50'),
(298, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:55:55'),
(299, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:56:03'),
(300, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:57:09'),
(301, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 17:57:13'),
(302, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission qa:user-management = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:04:41'),
(303, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission qa:user-management = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:04:42'),
(304, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:student_portal = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:05:07'),
(305, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user researchcoordinator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:22:59'),
(306, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:23:01'),
(307, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:23:11'),
(308, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:23:15'),
(309, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:23:28'),
(310, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:33:04'),
(311, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:33:13'),
(312, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:33:20'),
(313, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:33:38'),
(314, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-08 18:38:39'),
(315, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:45:41'),
(316, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:45:47'),
(317, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:45:56'),
(318, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:46:33'),
(319, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:46:36'),
(320, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:46:38'),
(321, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 18:46:55'),
(322, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:14:20'),
(323, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:14:28'),
(324, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:14:39'),
(325, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:14:59'),
(326, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:38:07'),
(327, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:38:17'),
(328, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:38:28'),
(329, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:38:31'),
(330, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:38:50'),
(331, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:49:46'),
(332, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:49:57'),
(333, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:50:05'),
(334, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:50:35'),
(335, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:50:39'),
(336, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:50:58'),
(337, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:51:14'),
(338, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:51:22'),
(339, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:51:24'),
(340, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 20:51:33'),
(341, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:21:51'),
(342, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:21:59'),
(343, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user rsantos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:43:02'),
(344, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:43:13'),
(345, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:43:20'),
(346, 54, 'Dr. Roberto M. Santos', 'adviser', 'view', 'faculty', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:43:55'),
(347, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:54:36'),
(348, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:54:44'),
(349, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:54:48'),
(350, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:55:01'),
(351, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:55:07'),
(352, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:55:21'),
(353, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:55:26'),
(354, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 21:55:35'),
(355, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:00:14'),
(356, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:00:21'),
(357, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:00:59'),
(358, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:enrollment = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:01:11'),
(359, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user registrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:01:25'),
(360, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:01:28'),
(361, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:01:35'),
(362, 2, 'Registrar', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:01:46'),
(363, 2, 'Registrar', 'registrar', 'view', 'scheduling', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:02:12'),
(364, 2, 'Registrar', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:23'),
(365, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:29'),
(366, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:reports-analytics = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:38'),
(367, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:crad = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:38'),
(368, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:lms = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:39'),
(369, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission registrar:cocurricular = grant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:39'),
(370, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:43'),
(371, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:54'),
(372, 2, 'Registrar', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:06:59'),
(373, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:07:07'),
(374, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Reset all role permissions to defaults', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:07:16'),
(375, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Permission superadmin:student_portal = deny', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:07:21'),
(376, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:07:26'),
(377, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:10:03'),
(378, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:10:16'),
(379, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:10:56'),
(380, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:11:01'),
(381, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:11:34'),
(382, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:11:38'),
(383, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:12:27'),
(384, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:13:59'),
(385, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:14:12'),
(386, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:16:46'),
(387, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:16:57'),
(388, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:17:14'),
(389, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:17:22'),
(390, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:17:30'),
(391, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:17:47'),
(392, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-08 22:18:13'),
(393, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:16:17'),
(394, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:16:44'),
(395, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:17:01'),
(396, 54, 'Dr. Roberto M. Santos', 'adviser', 'view', 'faculty', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:17:12'),
(397, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:20:02'),
(398, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:20:15'),
(399, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:33:06'),
(400, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:33:18'),
(401, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:38:10'),
(402, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:38:27'),
(403, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:41:13'),
(404, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:41:21'),
(405, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:42:12'),
(406, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:42:21'),
(407, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:45:56'),
(408, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:46:12'),
(409, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 13:47:42'),
(410, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 13:47:52'),
(411, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:44:20'),
(412, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:44:35'),
(413, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:44:59'),
(414, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:44:59'),
(415, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:45:15'),
(416, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 14:47:33'),
(417, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:03:15'),
(418, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:03:31'),
(419, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:03:44'),
(420, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:03:55'),
(421, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:05'),
(422, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:12'),
(423, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:19'),
(424, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:36'),
(425, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:39'),
(426, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:43'),
(427, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:14:58'),
(428, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 15:38:37'),
(429, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-09 15:43:01'),
(430, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:50:15');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(431, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:51:49'),
(432, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:52:05'),
(433, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:52:22'),
(434, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:52:25'),
(435, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:52:35'),
(436, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 15:52:46'),
(437, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:04'),
(438, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:08'),
(439, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:14'),
(440, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00018', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:32'),
(441, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:34'),
(442, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:38'),
(443, 40, 'Research Coordinator', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:47'),
(444, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:02:54'),
(445, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:05:29'),
(446, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:05:36'),
(447, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:05:44'),
(448, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:06:00'),
(449, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:06:02'),
(450, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:06:08'),
(451, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:06:26'),
(452, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:17:41'),
(453, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:17:48'),
(454, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:18:13'),
(455, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:20:05'),
(456, 9, 'Student User', 'student', 'create', 'student_portal', 'Resubmitted revised research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:35:44'),
(457, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:36:06'),
(458, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:36:08'),
(459, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:36:17'),
(460, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:36:28'),
(461, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:36:55'),
(462, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 16:37:11'),
(463, 3, 'CRAD Officer', 'crad_officer', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:05:20'),
(464, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:08:52'),
(465, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:08:59'),
(466, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:09:05'),
(467, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:09:29'),
(468, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:11:26'),
(469, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:11:34'),
(470, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:12:42'),
(471, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:12:50'),
(472, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:20:57'),
(473, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:21:50'),
(474, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:22:03'),
(475, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:22:16'),
(476, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:22:26'),
(477, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:24:51'),
(478, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:24:59'),
(479, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:25:45'),
(480, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:26:27'),
(481, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user jtan', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:26:41'),
(482, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:26:46'),
(483, NULL, 'Dr. Jose B. Tan', 'panel', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:26:54'),
(484, NULL, 'Dr. Jose B. Tan', 'panel', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:30:40'),
(485, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:30:46'),
(486, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:31:26'),
(487, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:31:34'),
(488, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:39:10'),
(489, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:40:11'),
(490, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:42:06'),
(491, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:42:17'),
(492, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:49:23'),
(493, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:49:42'),
(494, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:49:52'),
(495, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:50:04'),
(496, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:51:28'),
(497, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:51:38'),
(498, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:51:42'),
(499, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:51:51'),
(500, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:54:55'),
(501, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00022', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:55:38'),
(502, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 17:55:48'),
(503, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:01:50'),
(504, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:02:02'),
(505, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:24:27'),
(506, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:24:37'),
(507, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:24:45'),
(508, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:25:17'),
(509, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:25:20'),
(510, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:25:31'),
(511, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:25:43'),
(512, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:26:48'),
(513, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:26:55'),
(514, NULL, 'Unknown', NULL, 'login_failed', 'System', 'Invalid login attempt (unknown credentials)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:27:03'),
(515, NULL, 'Unknown', NULL, 'login_failed', 'System', 'Invalid login attempt (unknown credentials)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:27:13'),
(516, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:27:33'),
(517, 9, 'Student User', 'student', 'create', 'student_portal', 'Resubmitted revised research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:28:13'),
(518, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:28:48'),
(519, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:28:51'),
(520, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:28:59'),
(521, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:29:14'),
(522, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:29:20'),
(523, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:29:28'),
(524, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:30:02'),
(525, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:30:10'),
(526, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:31:16'),
(527, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:31:32'),
(528, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:38:06'),
(529, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:38:10'),
(530, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:38:16'),
(531, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:38:44'),
(532, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:38:47'),
(533, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:08'),
(534, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:18'),
(535, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:27'),
(536, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:37'),
(537, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:46'),
(538, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:39:54'),
(539, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:40:08'),
(540, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:40:17'),
(541, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:50:53'),
(542, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:51:26'),
(543, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:51:29'),
(544, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:51:41'),
(545, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:51:48'),
(546, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:52:08'),
(547, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:52:15'),
(548, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:56:00'),
(549, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:56:07'),
(550, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:57:10'),
(551, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:57:18'),
(552, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:58:49'),
(553, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 18:58:57'),
(554, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:24:27'),
(555, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:24:38'),
(556, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:26:57'),
(557, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:27:09'),
(558, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:46:14'),
(559, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user superadmin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:46:23'),
(560, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user researchdirector', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:48:31'),
(561, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:48:38'),
(562, 116, 'Research Director', 'research_director', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-09 19:48:46'),
(563, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 13:20:13'),
(564, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 13:20:24'),
(565, 116, 'Research Director', 'research_director', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 13:20:43'),
(566, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 14:16:36'),
(567, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:18:33'),
(568, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:36:23'),
(569, 1, 'Super Admin', 'superadmin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:36:30'),
(570, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:36:37'),
(571, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:42:36'),
(572, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:42:43'),
(573, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 19:42:50'),
(574, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user researchgrant', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:07:43'),
(575, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:07:50'),
(576, 222, 'Research Grant', 'research_grant', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:07:55'),
(577, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:08:51'),
(578, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:09:17'),
(579, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:09:25'),
(580, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:09:32'),
(581, 222, 'Research Grant', 'research_grant', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:14:13'),
(582, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:14:21'),
(583, 222, 'Research Grant', 'research_grant', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:21:18'),
(584, 222, 'Research Grant', 'research_grant', 'view', 'crad_grant', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:24:17'),
(585, 222, 'Research Grant', 'research_grant', 'update', 'System', 'Enabled Google Authenticator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:27:06'),
(586, 222, 'Research Grant', 'research_grant', 'update', 'System', 'Disabled Google Authenticator', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:27:16'),
(587, 222, 'Research Grant', 'research_grant', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:37:50'),
(588, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:37:57'),
(589, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:38:04'),
(590, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:38:11'),
(591, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:38:16'),
(592, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:41:24'),
(593, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:42:35'),
(594, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:43:20'),
(595, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:43:26'),
(596, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:44:22'),
(597, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:44:29'),
(598, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:44:44'),
(599, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:44:51'),
(600, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:45:27'),
(601, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:45:36'),
(602, 9, 'Student User', 'student', 'create', 'student_portal', 'Resubmitted revised research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:46:03'),
(603, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:46:07'),
(604, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:46:18'),
(605, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00027', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:46:42'),
(606, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:48:30'),
(607, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:48:40'),
(608, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:48:46'),
(609, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:51:37'),
(610, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:51:43'),
(611, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 20:59:18'),
(612, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:03:31'),
(613, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:03:40'),
(614, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:04:35'),
(615, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:05:08'),
(616, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:16:21'),
(617, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:21:51'),
(618, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:22:00'),
(619, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:25:12'),
(620, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:25:23'),
(621, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:25:33'),
(622, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:25:39'),
(623, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:34:33'),
(624, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:36:31'),
(625, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:38:41'),
(626, 40, 'Research Coordinator', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:38:48'),
(627, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:39:03'),
(628, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:39:18'),
(629, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:39:38'),
(630, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:47:06'),
(631, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 21:47:28'),
(632, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:20:14'),
(633, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:20:29'),
(634, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:29:56'),
(635, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:30:12'),
(636, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-10 22:56:42'),
(637, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:08:32'),
(638, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:08:43'),
(639, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:08:49'),
(640, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:09:01'),
(641, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:24:46');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(642, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:55:23'),
(643, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:55:31'),
(644, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:55:43'),
(645, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:55:53'),
(646, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:00'),
(647, 222, 'Research Grant', 'research_grant', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:09'),
(648, 222, 'Research Grant', 'research_grant', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:18'),
(649, 40, 'Research Coordinator', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:28'),
(650, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:35'),
(651, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:44'),
(652, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 01:56:53'),
(653, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:01:41'),
(654, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Invalid password', '::1', 'curl/8.13.0', '2026-08-11 02:02:36'),
(655, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Invalid password', '::1', 'curl/8.13.0', '2026-08-11 02:03:05'),
(656, 54, 'Dr. Roberto M. Santos', 'adviser', 'lockout', 'System', 'Login locked after failed attempts', '::1', 'curl/8.13.0', '2026-08-11 02:03:20'),
(657, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Invalid password', '::1', 'curl/8.13.0', '2026-08-11 02:03:20'),
(658, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:09'),
(659, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:15'),
(660, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Restored user #54 (status=active)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:31'),
(661, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user rsantos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:39'),
(662, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:42'),
(663, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:48'),
(664, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:07:54'),
(665, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:02'),
(666, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user rsantos', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:10'),
(667, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:17'),
(668, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Login blocked — login locked', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:23'),
(669, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:47'),
(670, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Restored user #54 (status=active)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:51'),
(671, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:08:55'),
(672, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:09:03'),
(673, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:10:20'),
(674, 222, 'Research Grant', 'research_grant', 'login', 'System', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:10:30'),
(675, 222, 'Research Grant', 'research_grant', 'view', 'crad_grant', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:11:53'),
(676, 222, 'Research Grant', 'research_grant', 'logout', 'System', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:23:41'),
(677, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 02:23:50'),
(678, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:13:46'),
(679, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:14:29'),
(680, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:27:26'),
(681, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:27:34'),
(682, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:27:43'),
(683, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:27:55'),
(684, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:15'),
(685, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:17'),
(686, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:25'),
(687, 3, 'CRAD Officer', 'crad_officer', 'update', 'crad', 'Registered approved proposal number:CRD-2026-00028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:44'),
(688, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated research group number: RG-2026-021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:47'),
(689, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:50'),
(690, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:28:59'),
(691, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:33:26'),
(692, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:33:35'),
(693, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:38:00'),
(694, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 12:38:11'),
(695, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:33:29'),
(696, 40, 'Research Coordinator', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:34:40'),
(697, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:34:55'),
(698, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:35:33'),
(699, 40, 'Research Coordinator', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:35:39'),
(700, 40, 'Research Coordinator', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:35:47'),
(701, 40, 'Research Coordinator', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:36:58'),
(702, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:37:21'),
(703, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:38:00'),
(704, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:38:07'),
(705, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:41:27'),
(706, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:41:39'),
(707, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:41:49'),
(708, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:41:57'),
(709, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:50:28'),
(710, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:50:37'),
(711, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:50:50'),
(712, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:51:00'),
(713, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:59:48'),
(714, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 13:59:55'),
(715, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:00:18'),
(716, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:00:43'),
(717, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:01:08'),
(718, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:01:37'),
(719, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:02:57'),
(720, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:03:03'),
(721, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:03:09'),
(722, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:03:45'),
(723, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:03:52'),
(724, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:05:31'),
(725, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:05:39'),
(726, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:07:04'),
(727, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:07:10'),
(728, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:08:13'),
(729, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:08:20'),
(730, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:15:03'),
(731, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:15:14'),
(732, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:18:40'),
(733, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:18:48'),
(734, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:18:59'),
(735, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:19:07'),
(736, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:20:27'),
(737, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:20:33'),
(738, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:20:41'),
(739, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:20:59'),
(740, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:21:14'),
(741, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:21:37'),
(742, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:26:17'),
(743, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:26:23'),
(744, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:30:10'),
(745, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:30:17'),
(746, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:30:44'),
(747, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:30:52'),
(748, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:31:00'),
(749, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:31:15'),
(750, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:31:29'),
(751, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:31:37'),
(752, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:33:59'),
(753, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:34:06'),
(754, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:39:37'),
(755, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:39:44'),
(756, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:40:00'),
(757, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:40:11'),
(758, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:40:20'),
(759, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:40:27'),
(760, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:44:49'),
(761, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:45:10'),
(762, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:45:29'),
(763, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:45:35'),
(764, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:47:17'),
(765, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:47:26'),
(766, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:24'),
(767, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:30'),
(768, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:37'),
(769, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:46'),
(770, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:52'),
(771, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:48:58'),
(772, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:55:39'),
(773, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:55:47'),
(774, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:55:52'),
(775, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:56:00'),
(776, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:56:07'),
(777, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 14:56:13'),
(778, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-022', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:03:30'),
(779, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:15:09'),
(780, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:15:20'),
(781, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:18:46'),
(782, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:18:57'),
(783, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:22:41'),
(784, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (7 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:33:29'),
(785, 9, 'Student User', 'student', 'create', 'student_portal', 'Submitted research document packet ref:CRD-2026-00001 (6 files)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:42:11'),
(786, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:52:50'),
(787, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:52:57'),
(788, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:53:24'),
(789, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:53:35'),
(790, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:55:46'),
(791, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:55:55'),
(792, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:59:25'),
(793, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 15:59:34'),
(794, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:02:05'),
(795, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:02:12'),
(796, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:02:22'),
(797, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:02:36'),
(798, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:02:59'),
(799, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:03:01'),
(800, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:03:13'),
(801, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:03:31'),
(802, 116, 'Research Director', 'research_director', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:04:03'),
(803, 116, 'Research Director', 'research_director', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:04:09'),
(804, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:04:22'),
(805, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:04:35'),
(806, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:04:42'),
(807, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:32:39'),
(808, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:32:47'),
(809, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:32:57'),
(810, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:33:06'),
(811, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:34:51'),
(812, 9, 'Student User', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:35:01'),
(813, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:49:31'),
(814, 116, 'Research Director', 'research_director', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:49:46'),
(815, 116, 'Research Director', 'research_director', 'view', 'faculty', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:49:49'),
(816, 116, 'Research Director', 'research_director', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:49:53'),
(817, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:50:00'),
(818, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:50:36'),
(819, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:50:42'),
(820, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:51:01'),
(821, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:51:07'),
(822, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:52:37'),
(823, 222, 'Research Grant', 'research_grant', 'login', 'crad_grant', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:52:47'),
(824, 222, 'Research Grant', 'research_grant', 'view', 'crad_grant', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:52:49'),
(825, 222, 'Research Grant', 'research_grant', 'logout', 'crad_grant', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:52:51'),
(826, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:53:01'),
(827, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 16:53:02'),
(828, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:01:23'),
(829, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:01:36'),
(830, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:10:37'),
(831, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:10:46'),
(832, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:11:03'),
(833, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:11:18'),
(834, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:14:22'),
(835, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:14:30'),
(836, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:16:00'),
(837, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:16:11'),
(838, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:19:51'),
(839, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:20:01'),
(840, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:20:39'),
(841, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:20:47'),
(842, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:20:55'),
(843, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:21:08'),
(844, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:23:48'),
(845, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:23:59'),
(846, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:24:08'),
(847, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:24:19'),
(848, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:27:43'),
(849, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:27:54'),
(850, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:28:06'),
(851, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:28:14'),
(852, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:09'),
(853, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:16'),
(854, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:24'),
(855, 4, 'Finance', 'finance', 'login', 'payment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:31'),
(856, 4, 'Finance', 'finance', 'logout', 'payment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:39');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(857, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:31:48'),
(858, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:36:55'),
(859, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:37:09'),
(860, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:44:24'),
(861, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:49:04'),
(862, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:50:40'),
(863, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:50:49'),
(864, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:51:34'),
(865, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:51:41'),
(866, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:51:59'),
(867, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:52:01'),
(868, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 17:52:11'),
(869, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:08:26'),
(870, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:08:33'),
(871, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:09:07'),
(872, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:09:16'),
(873, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:10:11'),
(874, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:10:17'),
(875, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:14:22'),
(876, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:14:33'),
(877, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:16:35'),
(878, 54, 'Dr. Roberto M. Santos', 'adviser', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:16:42'),
(879, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:16:49'),
(880, 54, 'Dr. Roberto M. Santos', 'adviser', 'view', 'faculty', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:16:51'),
(881, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:03'),
(882, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:09'),
(883, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:19'),
(884, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:25'),
(885, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:32'),
(886, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:39'),
(887, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:17:46'),
(888, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:21:18'),
(889, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:21:26'),
(890, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:25:43'),
(891, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:25:49'),
(892, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:26:10'),
(893, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:26:19'),
(894, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:27:08'),
(895, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:27:17'),
(896, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:27:21'),
(897, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:27:31'),
(898, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:36:56'),
(899, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:37:08'),
(900, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:50:56'),
(901, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:51:03'),
(902, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:54:07'),
(903, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 18:56:06'),
(904, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:01:47'),
(905, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:02:39'),
(906, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:10:45'),
(907, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:10:59'),
(908, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:11:10'),
(909, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:11:20'),
(910, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-022', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:11:27'),
(911, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:11:31'),
(912, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:11:49'),
(913, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:14:05'),
(914, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:14:12'),
(915, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:24:48'),
(916, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:24:57'),
(917, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:25:28'),
(918, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:25:34'),
(919, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:29:45'),
(920, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:29:52'),
(921, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:30:16'),
(922, 9, 'Student User', 'student', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:31:02'),
(923, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:31:11'),
(924, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:31:50'),
(925, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:31:59'),
(926, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:32:08'),
(927, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:32:20'),
(928, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:32:29'),
(929, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:32:33'),
(930, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:32:41'),
(931, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:33:42'),
(932, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:33:50'),
(933, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:36:42'),
(934, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:36:51'),
(935, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:38:15'),
(936, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:38:24'),
(937, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:45:57'),
(938, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:46:08'),
(939, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:46:24'),
(940, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:46:35'),
(942, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:46:51'),
(943, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:47:36'),
(944, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:48:18'),
(945, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 19:48:28'),
(946, 222, 'Research Grant', 'research_grant', 'login', 'crad_grant', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 20:56:56'),
(947, 222, 'Research Grant', 'research_grant', 'logout', 'crad_grant', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 20:57:03'),
(948, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 20:57:15'),
(949, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 20:57:35'),
(950, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 20:57:41'),
(951, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-11 21:01:22'),
(952, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 12:50:40'),
(953, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 12:51:32'),
(954, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 12:55:13'),
(955, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 12:55:22'),
(956, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'view', 'crad', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 13:07:10'),
(962, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-12 14:19:57'),
(963, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-12 14:21:50'),
(964, 3, NULL, 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-12 14:22:00'),
(965, 3, NULL, 'crad_officer', 'logout', 'crad', 'Logged out', '0.0.0.0', NULL, '2026-08-12 14:22:28'),
(973, 3, 'CRAD Officer', 'crad_officer', 'assign', 'crad', 'Assigned coordinator \"Mrs. Kris Guevarra\" to research group RG-2026-024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 14:38:37'),
(979, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:06:15'),
(980, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:06:24'),
(981, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:26:31'),
(982, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:26:39'),
(983, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:27:08'),
(984, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:27:20'),
(985, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:27:31'),
(986, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:27:41'),
(987, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:27:57'),
(988, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:28:16'),
(989, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:28:23'),
(990, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:29:01'),
(991, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:29:08'),
(992, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:29:14'),
(993, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:29:20'),
(994, 3, 'CRAD Officer', 'crad_officer', 'assign', 'crad', 'Assigned coordinator \"Mrs. Kris Guevarra\" to research group RG-2026-025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:29:32'),
(995, 3, 'CRAD Officer', 'crad_officer', 'activate', 'crad', 'Coordinator \"Mrs. Kris Guevarra\" assignment for group RG-2026-025 set to Active', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:30:35'),
(996, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:56:03'),
(997, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:56:09'),
(998, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:57:59'),
(999, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:58:09'),
(1000, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:58:18'),
(1001, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:58:26'),
(1002, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:58:57'),
(1003, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:59:08'),
(1004, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:59:22'),
(1005, 9, 'Student User', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:59:29'),
(1006, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:59:33'),
(1007, 3, 'CRAD Officer', 'crad_officer', 'create', 'crad', 'Generated title approval research group number: RG-2026-028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 16:59:47'),
(1008, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:08'),
(1009, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:17'),
(1010, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:28'),
(1011, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:39'),
(1012, 40, 'Mrs. Kris Guevarra', 'research_coordinator', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:43'),
(1013, 54, 'Dr. Roberto M. Santos', 'adviser', 'login', 'faculty', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:51'),
(1014, 54, 'Dr. Roberto M. Santos', 'adviser', 'logout', 'faculty', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:00:58'),
(1015, 9, 'Student User', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:01:05'),
(1016, 3, 'CRAD Officer', 'crad_officer', 'assign', 'crad', 'Assigned coordinator \"Mrs. Kris Guevarra\" to research group RG-2026-028', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:01:18'),
(1017, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:48:23'),
(1018, 1, 'Super Admin', 'superadmin', 'view', 'registrar', 'Opened Registrar Module Security', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:48:50'),
(1019, 1, 'Super Admin', 'superadmin', 'update', 'registrar', 'Started Authenticator setup for Registrar (registrar@bestlink.edu.ph)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:49:10'),
(1020, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:49:52'),
(1021, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:51:27'),
(1022, 1, 'Super Admin', 'superadmin', 'create', 'user-management', 'Created user reii', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 02:52:31'),
(1023, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-13 02:53:43'),
(1024, 455, 'Banz', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-13 02:54:27'),
(1025, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-13 05:40:59'),
(1026, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-13 06:13:14'),
(1027, 1, NULL, NULL, 'reg.student.create', 'registrar', 'Created student S260000101 (id=3)', '0.0.0.0', NULL, '2026-08-13 18:20:24'),
(1028, 1, NULL, NULL, 'reg.student.update', 'registrar', 'Updated student id=3 (S260000101)', '0.0.0.0', NULL, '2026-08-13 18:20:24'),
(1029, 1, 'Registrar', 'registrar', 'reg.student.create', 'registrar', 'Created student S280000888 (id=6)', '127.0.0.1', 'cli-post', '2026-08-13 18:24:13'),
(1030, 1, 'Test Registrar', 'registrar', 'test.foundation', 'registrar', 'Foundation test logging', '0.0.0.0', NULL, '2026-08-13 18:54:32'),
(1031, 1, 'Test Registrar', 'registrar', 'test.operation', 'registrar', 'Test operation [{\"test\":\"data\"}]', '0.0.0.0', NULL, '2026-08-13 18:54:32'),
(1032, 1, 'Test Registrar', 'registrar', 'reg.student.status_change', 'registrar', 'Changed student id=5 status to Active', '0.0.0.0', NULL, '2026-08-13 18:54:32'),
(1033, 1, NULL, NULL, 'reg.student.create', 'registrar', 'Created student S260000101 (id=7)', '0.0.0.0', NULL, '2026-08-13 19:03:39'),
(1034, 1, NULL, NULL, 'reg.student.update', 'registrar', 'Updated student id=7 (S260000101)', '0.0.0.0', NULL, '2026-08-13 19:03:39'),
(1035, 1, NULL, NULL, 'reg.student.status_change', 'registrar', 'Changed student id=7 status to Enrolled', '0.0.0.0', NULL, '2026-08-13 19:03:39'),
(1036, 1, NULL, NULL, 'reg.student.create', 'registrar', 'Created student TEST-GUARDIAN-1786619779 (id=9)', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1037, 1, NULL, NULL, 'reg.guardian.create', 'registrar', 'Created guardian for student 9 (id=1, name=Maria Santos)', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1038, 1, NULL, NULL, 'reg.guardian.create', 'registrar', 'Created guardian for student 9 (id=2, name=Juan Santos)', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1039, 1, NULL, NULL, 'reg.guardian.update', 'registrar', 'Updated guardian id=1 for student 9', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1040, 1, NULL, NULL, 'reg.guardian.create', 'registrar', 'Created guardian for student 9 (id=3, name=Anna Santos)', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1041, 1, NULL, NULL, 'reg.guardian.delete', 'registrar', 'Deleted guardian id=2 (name=Juan Santos) from student 9', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1042, 1, NULL, NULL, 'reg.guardian.delete', 'registrar', 'Deleted guardian id=1 (name=Maria Santos Updated) from student 9', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1043, 1, NULL, NULL, 'reg.guardian.delete', 'registrar', 'Deleted guardian id=3 (name=Anna Santos) from student 9', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1044, 1, NULL, NULL, 'reg.student.delete', 'registrar', 'Deleted student id=9 (TEST-GUARDIAN-1786619779)', '0.0.0.0', NULL, '2026-08-13 19:16:19'),
(1045, 1, 'Test Registrar', 'registrar', 'test.foundation', 'registrar', 'Foundation test logging', '0.0.0.0', NULL, '2026-08-13 19:27:03'),
(1046, 1, 'Test Registrar', 'registrar', 'test.operation', 'registrar', 'Test operation [{\"test\":\"data\"}]', '0.0.0.0', NULL, '2026-08-13 19:27:03'),
(1047, 1, 'Test Registrar', 'registrar', 'reg.student.status_change', 'registrar', 'Changed student id=5 status to Active', '0.0.0.0', NULL, '2026-08-13 19:27:03'),
(1048, 1, 'Test Registrar', 'registrar', 'test.foundation', 'registrar', 'Foundation test logging', '0.0.0.0', NULL, '2026-08-13 19:32:05'),
(1049, 1, 'Test Registrar', 'registrar', 'test.operation', 'registrar', 'Test operation [{\"test\":\"data\"}]', '0.0.0.0', NULL, '2026-08-13 19:32:05'),
(1050, 1, 'Test Registrar', 'registrar', 'reg.student.status_change', 'registrar', 'Changed student id=5 status to Active', '0.0.0.0', NULL, '2026-08-13 19:32:05'),
(1051, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-18 21:22:57'),
(1052, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-18 21:23:17'),
(1053, 455, 'Banz', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-18 21:23:24'),
(1054, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-18 21:23:33'),
(1055, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-23 01:27:45'),
(1056, 455, 'Banz', 'registrar', 'save_health_profile', 'registrar', 'Saved health profile for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-23 01:58:04'),
(1057, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0', '2026-08-23 04:35:00'),
(1058, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 15:20:17'),
(1059, 455, 'Banz', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 15:26:53'),
(1060, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated FORM137: BCP26-JLE3B', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 17:06:21'),
(1061, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated FORM137 FORM137-2026-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 17:06:21'),
(1062, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0006 created for student 39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 21:23:55'),
(1063, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0006', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 21:23:55'),
(1064, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-CXDV2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 21:24:09'),
(1065, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 21:24:09'),
(1066, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0007 created for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:40:43'),
(1067, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:40:43'),
(1068, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-3BQZQ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:40:53'),
(1069, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:40:53'),
(1070, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0008 created for student 36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:47:34'),
(1071, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0008', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:47:34'),
(1072, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-Q4J3B', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:47:50'),
(1073, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:47:50'),
(1074, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0009 created for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:50:27'),
(1075, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:50:27'),
(1076, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-7GUHE', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:50:57'),
(1077, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-23 23:50:57'),
(1078, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0010 created for student 33', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:14:44'),
(1079, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:14:44'),
(1080, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-53MN7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:14:53'),
(1081, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:14:53'),
(1082, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0011 created for student 34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:50:47'),
(1083, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:50:47'),
(1084, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-PK2TK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:50:56'),
(1085, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:50:56'),
(1086, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0012 created for student 34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:51:37'),
(1087, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:51:37'),
(1088, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-KEJDG', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:51:44'),
(1089, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0006', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 00:51:44'),
(1090, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0013 created for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:55:19'),
(1091, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0013', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:55:19');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(1092, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-BGL5Q', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:55:29'),
(1093, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:55:29'),
(1094, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0014 created for student 34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:56:40'),
(1095, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:56:40'),
(1096, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-JNN5K', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:56:50'),
(1097, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0002', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 01:56:50'),
(1098, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0015 created for student 32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:07:27'),
(1099, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0015', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:07:27'),
(1100, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-4MEX5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:07:36'),
(1101, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0003', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:07:36'),
(1102, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0016 created for student 36', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:21:41'),
(1103, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0016', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:21:41'),
(1104, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-U3DAF', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:21:51'),
(1105, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0004', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:21:51'),
(1106, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0017 created for student 39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:35:13'),
(1107, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:35:13'),
(1108, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-KWTZD', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:35:20'),
(1109, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0005', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 02:35:20'),
(1110, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0018 created for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:02:26'),
(1111, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0018', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:02:26'),
(1112, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-NPU8Q', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:02:35'),
(1113, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0006', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:02:35'),
(1114, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral: BCP26-96SGK', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:03:10'),
(1115, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated Good Moral GOOD_MORAL_CERTIFICATE-2026-0008', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:03:10'),
(1116, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0019 created for student 40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:16:08'),
(1117, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0019', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:16:08'),
(1118, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-TZ5X6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:16:15'),
(1119, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0007', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 03:16:15'),
(1120, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0020 created for student 32', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:29:01'),
(1121, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0020', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:29:01'),
(1122, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-XDZAH', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:29:12'),
(1123, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0008', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:29:12'),
(1124, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0021 created for student 34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:38:13'),
(1125, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0021', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:38:13'),
(1126, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-XZH7X', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:38:20'),
(1127, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:38:20'),
(1128, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0022 created for student 34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:47:59'),
(1129, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0022', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:47:59'),
(1130, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-4JEUM', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:48:41'),
(1131, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0010', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:48:41'),
(1132, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0023 created for student 39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:54:28'),
(1133, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0023', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:54:28'),
(1134, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-2J4Q8', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:54:36'),
(1135, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0011', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:54:36'),
(1136, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0024 created for student 31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:59:08'),
(1137, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0024', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:59:08'),
(1138, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-ZSA52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:59:16'),
(1139, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0012', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-24 04:59:16'),
(1140, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 12:09:25'),
(1141, 455, 'Banz', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 13:11:15'),
(1142, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 13:11:25'),
(1143, 3, 'CRAD Officer', 'crad_officer', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-28 13:16:49'),
(1144, 3, 'CRAD Officer', 'crad_officer', 'login', 'crad', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-28 13:17:04'),
(1145, 3, 'CRAD Officer', 'crad_officer', 'logout', 'crad', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-28 13:17:25'),
(1146, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user admission', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 13:17:53'),
(1147, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-28 13:18:11'),
(1148, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', '2026-08-28 13:21:26'),
(1149, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 13:29:49'),
(1150, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-28 13:29:57'),
(1151, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-29 20:08:57'),
(1152, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 20:12:28'),
(1153, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-29 20:51:54'),
(1154, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0025 created for student 1796', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 21:35:03'),
(1155, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 21:35:03'),
(1156, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Document request DOC-REQ-2026-0026 created for student 1042', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 22:12:36'),
(1157, 455, 'Banz', 'registrar', 'create_request', 'registrar', 'Created request DOC-REQ-2026-0026', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 22:12:36'),
(1158, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE: BCP26-N9D6K', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 22:13:03'),
(1159, 455, 'Banz', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-29 22:13:03'),
(1160, 455, 'Banz', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 20:05:17'),
(1161, 455, 'Banz', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 20:55:39'),
(1162, 455, 'Banz', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 20:56:06'),
(1163, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 20:57:01'),
(1164, 1, 'Super Admin', 'superadmin', 'create', 'user-management', 'Created user reiiiii', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 21:23:35'),
(1165, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 21:23:52'),
(1166, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-30 21:24:06'),
(1167, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 21:25:28'),
(1168, 1, 'Super Admin', 'superadmin', 'create', 'user-management', 'Created user dave', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 21:26:27'),
(1169, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 21:26:32'),
(1170, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 21:26:41'),
(1171, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'view', 'student_portal', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 21:56:15'),
(1172, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-08-30 23:28:47'),
(1173, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-31 00:07:12'),
(1174, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-31 00:08:37'),
(1175, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-31 00:09:13'),
(1176, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: C5A8EF39F747', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-31 00:38:37'),
(1177, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0014', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-31 00:38:37'),
(1178, 549, 'Raymar Bantonare', 'registrar', 'notify_student', 'registrar', 'Sent email for item #21 to boyrexar02@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 OPR/134.0.0.0', '2026-08-31 01:49:36'),
(1179, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', '2026-09-03 18:37:19'),
(1180, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 19:05:21'),
(1181, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 19:05:44'),
(1182, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 19:05:49'),
(1183, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 20:27:46'),
(1184, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 20:28:00'),
(1185, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 20:29:36'),
(1186, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 20:40:35'),
(1187, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 20:43:38'),
(1188, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-03 21:12:52'),
(1189, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-04 03:07:17'),
(1190, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 12:00:51'),
(1191, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-04 12:03:03'),
(1192, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: 95D40899202D', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 12:06:36'),
(1193, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0017', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 12:06:36'),
(1194, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-04 13:55:38'),
(1195, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '192.168.8.39', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 14:28:44'),
(1196, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 14:34:06'),
(1197, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: 1F1B74BF033B', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 14:44:24'),
(1198, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0029', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 14:44:24'),
(1199, 549, 'Raymar Bantonare', 'registrar', 'notify_student', 'registrar', 'Sent email for item #22 to lixx1920@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-04 14:46:02'),
(1200, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:02:48'),
(1201, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:05:14'),
(1202, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: 633F0563A1F7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:08:47'),
(1203, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0033', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:08:47'),
(1204, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:31:36'),
(1205, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:31:47'),
(1206, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:44:34'),
(1207, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:44:42'),
(1208, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 13:49:52'),
(1209, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 14:19:27'),
(1210, 549, 'Raymar Bantonare', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 14:49:13'),
(1211, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:04:45'),
(1212, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:04:52'),
(1213, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:05:24'),
(1214, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:05:35'),
(1215, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:25:20'),
(1216, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:25:41'),
(1217, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:26:44'),
(1218, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 15:26:50'),
(1219, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: ECB243B85B83', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 16:51:26'),
(1220, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0042', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 16:51:26'),
(1221, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:21:27'),
(1222, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:21:34'),
(1223, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:22:00'),
(1224, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:22:10'),
(1225, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: 149CD553C944', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:22:30'),
(1226, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0046', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:22:30'),
(1227, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:26:02'),
(1228, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:26:08'),
(1229, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:26:31'),
(1230, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:26:42'),
(1231, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE: 2C7E6E71FDCC', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:27:03'),
(1232, 549, 'Raymar Bantonare', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0048', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-05 17:27:03'),
(1233, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 14:33:13'),
(1234, 549, 'Raymar Bantonare', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 14:51:25'),
(1235, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 14:52:07'),
(1236, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 14:58:04'),
(1237, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 14:59:39'),
(1238, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 15:00:10'),
(1239, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 15:01:49'),
(1240, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 15:02:00'),
(1241, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 15:02:31'),
(1242, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 15:25:55'),
(1243, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:20:52'),
(1244, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:21:30'),
(1245, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:34:36'),
(1246, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:34:42'),
(1247, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:42:49'),
(1248, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-08 16:42:55'),
(1249, 582, 'Rorss Banss', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 17:11:19'),
(1250, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', '2026-09-08 17:39:56'),
(1251, 582, 'Rorss Banss', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 17:55:34'),
(1252, 585, 'Rasdhashd PENG', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 18:08:31'),
(1253, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', '2026-09-08 18:54:03'),
(1254, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 OPR/135.0.0.0', '2026-09-08 18:54:33'),
(1255, 585, 'Rasdhashd PENG', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 19:49:07'),
(1256, 585, 'Rasdhashd PENG', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 19:49:13'),
(1257, 585, 'Rasdhashd PENG', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 19:49:26'),
(1258, 587, 'PECKK PINK', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 20:24:17'),
(1259, 587, 'PECKK PINK', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 20:24:45'),
(1260, 587, 'PECKK PINK', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 20:24:49'),
(1261, 587, 'PECKK PINK', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 20:24:53'),
(1262, 588, 'Rochelle Banz', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 22:19:36'),
(1263, 588, 'Rochelle Banz', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 22:20:00'),
(1264, 589, 'Princess Bantonare', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 22:59:14'),
(1265, 589, 'Princess Bantonare', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 22:59:36'),
(1266, 588, 'Rochelle Banz', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:10:51'),
(1267, 588, 'Rochelle Banz', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:11:03'),
(1268, 588, 'Rochelle Banz', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:11:07'),
(1269, 588, 'Rochelle Banz', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:25:41'),
(1270, 590, 'Princessss Bantonare', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:28:54'),
(1271, 590, 'Princessss Bantonare', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:29:15'),
(1272, 590, 'Princessss Bantonare', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-08 23:30:34'),
(1273, 591, 'asdasd asd', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', '2026-09-09 01:22:29'),
(1274, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 12:42:47'),
(1275, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 21:27:39'),
(1276, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 21:33:15'),
(1277, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 21:33:22'),
(1278, 558, 'DEN RELL JOHN DAVE SILVA', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 21:51:45'),
(1279, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 21:51:50'),
(1280, 549, 'Raymar Bantonare', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 22:09:11'),
(1281, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 22:21:11'),
(1282, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 22:21:46'),
(1283, 549, 'Raymar Bantonare', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 22:23:42'),
(1284, 549, 'Raymar Bantonare', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 23:34:50'),
(1285, 549, 'Raymar Bantonare', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-18 23:37:21'),
(1286, 2, 'Registrar', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:17:55'),
(1287, 1, 'Super Admin', 'superadmin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:18:13'),
(1288, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:18:27'),
(1289, 1, 'Super Admin', 'superadmin', 'create', 'user-management', 'Created user denrellregistrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:22:03'),
(1290, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:24:41');
INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `role_key`, `action`, `module_key`, `detail`, `ip_address`, `user_agent`, `created_at`) VALUES
(1291, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:24:59'),
(1292, 604, 'Den Rell Silva', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:26:38'),
(1293, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:31:23'),
(1294, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:48:07'),
(1295, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:57:26'),
(1296, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 00:59:43'),
(1297, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:03:05'),
(1298, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 01:13:14'),
(1299, NULL, 'Unknown', NULL, 'login_failed', 'System', 'Invalid login attempt (unknown credentials)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 02:49:29'),
(1300, 2, 'Registrar', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 02:51:13'),
(1301, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 02:53:24'),
(1302, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 02:57:30'),
(1303, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:13:13'),
(1304, 20, 'Admission', 'admission', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:15:12'),
(1305, 20, 'Admission', 'admission', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:15:27'),
(1306, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:15:46'),
(1307, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user admission', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:16:13'),
(1308, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:16:20'),
(1309, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:16:33'),
(1310, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:17:01'),
(1311, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 03:17:14'),
(1312, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:13:50'),
(1313, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user denrellregistrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:14:34'),
(1314, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:14:52'),
(1315, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:15:45'),
(1316, 2, 'Registrar', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 07:39:42'),
(1317, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:02:45'),
(1318, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:06:25'),
(1319, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:06:44'),
(1320, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:08:20'),
(1321, 617, 'Dengfgf Silvadfdfd', 'pre-enrollee', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:08:35'),
(1322, 617, 'Dengfgf Silvadfdfd', 'pre-enrollee', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:09:20'),
(1323, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:09:58'),
(1324, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:10:33'),
(1325, 617, 'Dengfgf Silvadfdfd', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:10:51'),
(1326, 617, 'Dengfgf Silvadfdfd', 'student', 'logout', 'student_portal', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:12:47'),
(1327, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:13:10'),
(1328, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:25:05'),
(1329, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:27:24'),
(1330, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 09:37:15'),
(1331, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:06:11'),
(1332, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:07:27'),
(1333, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:10:47'),
(1334, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:12:56'),
(1335, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:13:41'),
(1336, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:14:19'),
(1337, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:17:55'),
(1338, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:18:41'),
(1339, 20, 'Admission', 'admission', 'login', 'enrollment', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:20:56'),
(1340, 20, 'Admission', 'admission', 'logout', 'enrollment', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:21:12'),
(1341, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:21:49'),
(1342, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:22:36'),
(1343, 617, 'Dengfgf Silvadfdfd', 'student', 'login', 'student_portal', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-19 10:23:01'),
(1344, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 19:27:44'),
(1345, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 19:40:02'),
(1346, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-23 19:42:30'),
(1347, 2, 'Registrar', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:40:10'),
(1348, 2, 'Registrar', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:40:24'),
(1349, 2, 'Registrar', 'registrar', 'lockout', 'System', 'Login locked after failed attempts', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:40:45'),
(1350, 2, 'Registrar', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:40:45'),
(1351, 1, 'Super Admin', 'superadmin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:41:21'),
(1352, 1, 'Super Admin', 'superadmin', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:41:36'),
(1353, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:42:05'),
(1354, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Restored user #2 (status=active)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:43:01'),
(1355, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user registrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:43:49'),
(1356, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:43:58'),
(1357, 604, 'Den Rell Silva', 'registrar', 'login_failed', 'System', 'Invalid password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:44:41'),
(1358, 1, 'Super Admin', 'superadmin', 'login', 'user-management', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:44:57'),
(1359, 1, 'Super Admin', 'superadmin', 'update', 'user-management', 'Updated user denrellregistrar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:45:42'),
(1360, 1, 'Super Admin', 'superadmin', 'logout', 'user-management', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:45:51'),
(1361, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:46:37'),
(1362, 604, 'Den Rell Silva', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:46:42'),
(1363, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 12:57:17'),
(1364, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 13:38:37'),
(1365, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 13:49:12'),
(1366, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:03:19'),
(1367, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:12:39'),
(1368, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:26:23'),
(1369, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:29:31'),
(1370, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:29:49'),
(1371, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:34:53'),
(1372, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:48:07'),
(1373, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-03 14:48:18'),
(1374, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 10:16:05'),
(1375, 604, 'Den Rell Silva', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-04 10:18:50'),
(1376, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', '2026-11-10 21:43:45'),
(1377, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 17:00:03'),
(1378, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:00:05'),
(1379, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:05:39'),
(1380, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:06:54'),
(1381, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:22:14'),
(1382, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:32:30'),
(1383, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:47:19'),
(1384, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-11 21:49:29'),
(1385, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 12:56:16'),
(1386, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 13:06:07'),
(1387, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 13:28:39'),
(1388, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 13:37:48'),
(1389, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 14:02:12'),
(1390, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 14:03:28'),
(1391, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 14:35:53'),
(1392, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 14:48:02'),
(1393, 604, 'Den Rell Silva', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-12 14:48:47'),
(1394, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 12:17:39'),
(1395, 604, 'Den Rell Silva', 'registrar', 'update_subject', 'registrar', 'Updated subject IT301 for student 2141', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 12:19:47'),
(1396, 604, 'Den Rell Silva', 'registrar', 'update_subject', 'registrar', 'Updated subject IT401 for student 2141', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 12:20:03'),
(1397, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 12:27:30'),
(1398, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 12:43:36'),
(1399, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 13:05:28'),
(1400, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 13:15:22'),
(1401, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 13:35:13'),
(1402, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 13:36:05'),
(1403, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 21:36:48'),
(1404, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 22:09:15'),
(1405, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 22:12:24'),
(1406, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 22:35:36'),
(1407, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 22:45:45'),
(1408, 604, 'Den Rell Silva', 'registrar', 'view', 'registrar', 'Opened Security Settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-18 22:53:30'),
(1409, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:27:36'),
(1410, 604, 'Den Rell Silva', 'registrar', 'generate_document', 'registrar', 'Generated COE: 306B86C6F427', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:32:34'),
(1411, 604, 'Den Rell Silva', 'registrar', 'generate_document', 'registrar', 'Generated COE CERTIFICATE_OF_ENROLLMENT-2026-0049', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:32:34'),
(1412, 604, 'Den Rell Silva', 'registrar', 'update_request_status', 'registrar', 'Request 24 status: Submitted', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:32:46'),
(1413, 604, 'Den Rell Silva', 'registrar', 'update_request_status', 'registrar', 'Request 24 -> Submitted', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:32:46'),
(1414, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 19:34:11'),
(1415, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 20:38:37'),
(1416, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 20:46:27'),
(1417, 604, 'Den Rell Silva', 'registrar', 'login', 'registrar', 'Logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 20:51:49'),
(1418, 604, 'Den Rell Silva', 'registrar', 'update_request_status', 'registrar', 'Request 24 status: For Release', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 20:55:12'),
(1419, 604, 'Den Rell Silva', 'registrar', 'update_request_status', 'registrar', 'Request 24 -> For Release', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 20:55:12'),
(1420, 604, 'Den Rell Silva', 'registrar', 'logout', 'registrar', 'Logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', '2026-11-19 21:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `enr_pre_registrations`
--

CREATE TABLE `enr_pre_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_no` varchar(40) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `middle_name` varchar(150) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `complete_address` varchar(500) NOT NULL,
  `sex` varchar(20) NOT NULL,
  `birth_date` date NOT NULL,
  `type_of_enrollee` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `program` varchar(150) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `last_school_attended` varchar(255) DEFAULT NULL,
  `last_school_year_attended` varchar(50) DEFAULT NULL,
  `guardian_name` varchar(150) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_throttles`
--

CREATE TABLE `login_throttles` (
  `id` int(10) UNSIGNED NOT NULL,
  `throttle_key` char(64) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token_hash`, `expires_at`, `used_at`, `created_ip`, `created_at`) VALUES
(2, 1, '550d259303762ee9ce8b5378b3b6b1e212a4b5cf796b005404689bb4c5596866', '2026-08-06 14:05:55', '2026-08-06 13:06:28', '::1', '2026-08-06 13:05:55'),
(3, 9, '691edab739335bc353c7ecaa7d183393ea51e47def723d4f3e68adccdd10fcb9', '2026-08-06 14:18:23', '2026-08-06 13:18:33', '::1', '2026-08-06 13:18:23'),
(4, 9, '55eabae148518a30c44e17552b678572afdda8d79fc2c59f95152780545da52b', '2026-08-06 14:18:33', NULL, '::1', '2026-08-06 13:18:33');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

CREATE TABLE `password_reset_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `module_key` varchar(60) NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `requested_password_hash` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `admin_note` varchar(500) DEFAULT NULL,
  `temp_password_set` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_academic_history`
--

CREATE TABLE `reg_academic_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `school_name` varchar(200) NOT NULL,
  `level` varchar(60) DEFAULT NULL,
  `from_year` smallint(5) UNSIGNED DEFAULT NULL,
  `to_year` smallint(5) UNSIGNED DEFAULT NULL,
  `awards` varchar(255) DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `file_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_academic_history`
--

INSERT INTO `reg_academic_history` (`id`, `student_id`, `school_name`, `level`, `from_year`, `to_year`, `awards`, `remarks`, `file_id`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(41, 2141, 'San Jose Elementary School', 'Elementary', 2014, 2020, 'Honor Pupil', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(42, 2141, 'Bulacan National High School', 'Junior High School', 2020, 2024, 'With Honors', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(43, 2141, 'Bestlink College of the Philippines - SHS', 'Senior High School', 2024, 2026, 'With Honors', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(44, 2142, 'Bulacan Central Elementary School', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(45, 2142, 'Quezon City High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(46, 2142, 'Arellano University Senior High', 'Senior High School', 2024, 2026, 'Good Standing', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(47, 2143, 'St. Joseph Academy Elementary', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(48, 2143, 'St. Mary\'s High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(49, 2143, 'AMA Computer College SHS', 'Senior High School', 2024, 2026, 'With Honors', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(50, 2144, 'Bagong Silang Elementary School', 'Elementary', 2014, 2020, 'Honor Pupil', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(51, 2144, 'Caloocan National High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(52, 2144, 'University of Caloocan City SHS', 'Senior High School', 2024, 2026, 'Good Standing', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(53, 2145, 'Caloocan Elementary School', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(54, 2145, 'Novalliches High School', 'Junior High School', 2020, 2024, 'With Honors', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(55, 2145, 'Bestlink College of the Philippines - SHS', 'Senior High School', 2024, 2026, 'With Honors', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(56, 2146, 'San Jose Elementary School', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(57, 2146, 'Bulacan National High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(58, 2146, 'Arellano University Senior High', 'Senior High School', 2024, 2026, 'Good Standing', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(59, 2147, 'Bulacan Central Elementary School', 'Elementary', 2014, 2020, 'Honor Pupil', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(60, 2147, 'Quezon City High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(61, 2147, 'AMA Computer College SHS', 'Senior High School', 2024, 2026, 'With Honors', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(62, 2148, 'St. Joseph Academy Elementary', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(63, 2148, 'St. Mary\'s High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(64, 2148, 'University of Caloocan City SHS', 'Senior High School', 2024, 2026, 'Good Standing', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(65, 2149, 'Bagong Silang Elementary School', 'Elementary', 2014, 2020, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(66, 2149, 'Caloocan National High School', 'Junior High School', 2020, 2024, 'With Honors', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(67, 2149, 'Bestlink College of the Philippines - SHS', 'Senior High School', 2024, 2026, 'With Honors', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(68, 2150, 'Caloocan Elementary School', 'Elementary', 2014, 2020, 'Honor Pupil', 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(69, 2150, 'Novalliches High School', 'Junior High School', 2020, 2024, NULL, 'Completed', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL),
(70, 2150, 'Arellano University Senior High', 'Senior High School', 2024, 2026, 'Good Standing', 'Graduated', NULL, '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_academic_subjects`
--

CREATE TABLE `reg_academic_subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject_code` varchar(40) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `units` decimal(5,2) DEFAULT NULL,
  `year_level` varchar(20) NOT NULL DEFAULT '1st Year',
  `term` varchar(40) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Passed',
  `instructor` varchar(150) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_academic_subjects`
--

INSERT INTO `reg_academic_subjects` (`id`, `student_id`, `subject_code`, `subject_name`, `units`, `year_level`, `term`, `academic_year`, `grade`, `remarks`, `status`, `instructor`, `created_at`, `updated_at`, `created_by`) VALUES
(539, 2141, 'CC101', 'Introduction to Computing', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. R. Cruz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(540, 2141, 'CC102', 'Fundamentals of Programming', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Engr. J. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(541, 2141, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(542, 2141, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(543, 2141, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(544, 2141, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(545, 2141, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(546, 2141, 'CC103', 'Intermediate Computer Programming', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Engr. J. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(547, 2141, 'CC104', 'Data Structures and Algorithms', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. R. Cruz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(548, 2141, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(549, 2141, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(550, 2141, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(551, 2141, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(552, 2141, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(553, 2141, 'IT201', 'Object-Oriented Programming', 3.00, '2nd Year', '1st', '2024-2025', '1.50', 'Passed', 'Passed', 'Engr. J. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(554, 2141, 'IT202', 'Information Management / DBMS', 3.00, '2nd Year', '1st', '2024-2025', '1.75', 'Passed', 'Passed', 'Prof. L. Mendoza', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(555, 2141, 'IT203', 'Discrete Mathematics', 3.00, '2nd Year', '1st', '2024-2025', '2.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(556, 2141, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '2.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(557, 2141, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(558, 2141, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(559, 2141, 'IT204', 'Web Systems and Technologies', 3.00, '2nd Year', '2nd', '2024-2025', '1.75', 'Passed', 'Passed', 'Prof. E. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(560, 2141, 'IT205', 'Operating Systems and Architecture', 3.00, '2nd Year', '2nd', '2024-2025', '2.00', 'Passed', 'Passed', 'Prof. L. Mendoza', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(561, 2141, 'IT206', 'Networking 1 (Fundamentals)', 3.00, '2nd Year', '2nd', '2024-2025', '2.25', 'Passed', 'Passed', 'Engr. M. Tolentino', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(562, 2141, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '2.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(563, 2141, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '2.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(564, 2141, 'IT301', 'Systems Analysis and Design', 3.00, '3rd Year', '1st', '2025-2026', '1.50', 'Passed', 'Passed', 'Prof. L. Mendoza', '2026-08-29 22:57:50', '2026-11-18 12:19:47', NULL),
(565, 2141, 'IT302', 'Advanced Web Development', 3.00, '3rd Year', '1st', '2025-2026', '2.25', 'Passed', 'Passed', 'Prof. E. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(566, 2141, 'IT303', 'Networking 2 (Routing & Switching)', 3.00, '3rd Year', '1st', '2025-2026', '2.50', 'Passed', 'Passed', 'Engr. M. Tolentino', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(567, 2141, 'IT304', 'Information Assurance & Security', 3.00, '3rd Year', '1st', '2025-2026', '2.75', 'Passed', 'Passed', 'Prof. S. Navarro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(568, 2141, 'ITE101', 'IT Elective 1 (Cloud Computing)', 3.00, '3rd Year', '1st', '2025-2026', '3.00', 'Passed', 'Passed', 'Prof. R. Cruz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(569, 2141, 'IT305', 'Mobile Application Development', 3.00, '3rd Year', '2nd', '2025-2026', '2.25', 'Passed', 'Passed', 'Prof. E. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(570, 2141, 'IT306', 'Capstone Project & Research 1', 3.00, '3rd Year', '2nd', '2025-2026', '2.50', 'Passed', 'Passed', 'Dr. V. Aquino', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(571, 2141, 'IT307', 'Quantitative Methods & Analytics', 3.00, '3rd Year', '2nd', '2025-2026', '2.75', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(572, 2141, 'ITE102', 'IT Elective 2 (Cybersecurity)', 3.00, '3rd Year', '2nd', '2025-2026', '3.00', 'Passed', 'Passed', 'Prof. S. Navarro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(573, 2141, 'IT401', 'Capstone Project & Research 2', 3.00, '4th Year', '1st', '2026-2027', '1.50', 'Passed', 'Passed', 'Dr. V. Aquino', '2026-08-29 22:57:50', '2026-11-18 12:20:03', NULL),
(574, 2141, 'IT402', 'Systems Administration & Maintenance', 3.00, '4th Year', '1st', '2026-2027', '2.75', 'Passed', 'Passed', 'Engr. M. Tolentino', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(575, 2141, 'IT403', 'Practicum / Industry Internship (300 hrs)', 6.00, '4th Year', '1st', '2026-2027', '3.00', 'Passed', 'Passed', 'Dr. V. Aquino', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(576, 2142, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(577, 2142, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(578, 2142, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(579, 2142, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(580, 2142, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(581, 2142, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(582, 2142, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(583, 2142, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(584, 2142, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(585, 2142, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(586, 2142, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(587, 2142, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(588, 2142, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(589, 2142, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(590, 2142, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '1.75', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(591, 2142, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '2.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(592, 2142, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '2.25', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(593, 2142, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(594, 2142, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(595, 2142, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(596, 2142, 'BA204', 'Financial Management', 3.00, '2nd Year', '2nd', '2024-2025', '2.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(597, 2142, 'BA205', 'Human Resource Management', 3.00, '2nd Year', '2nd', '2024-2025', '2.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(598, 2142, 'BA206', 'Macroeconomics', 3.00, '2nd Year', '2nd', '2024-2025', '2.50', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(599, 2142, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '2.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(600, 2142, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '3.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(601, 2142, 'BA301', 'Operations Management and TQM', 3.00, '3rd Year', '1st', '2025-2026', '2.25', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(602, 2142, 'BA302', 'Business Analytics and Forecasting', 3.00, '3rd Year', '1st', '2025-2026', '2.50', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(603, 2142, 'BA303', 'Taxation (Income and Business)', 3.00, '3rd Year', '1st', '2025-2026', '2.75', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(604, 2142, 'BA304', 'Corporate Governance and Ethics', 3.00, '3rd Year', '1st', '2025-2026', '3.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(605, 2143, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(606, 2143, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(607, 2143, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(608, 2143, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(609, 2143, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(610, 2143, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(611, 2143, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(612, 2143, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(613, 2143, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(614, 2143, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(615, 2143, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(616, 2143, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(617, 2143, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(618, 2143, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(619, 2143, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '2.00', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(620, 2143, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '2.25', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(621, 2143, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(622, 2143, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(623, 2143, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(624, 2143, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '1.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(625, 2144, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(626, 2144, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(627, 2144, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(628, 2144, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(629, 2144, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(630, 2144, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(631, 2144, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(632, 2144, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(633, 2144, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(634, 2144, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(635, 2144, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(636, 2144, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(637, 2144, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(638, 2144, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(639, 2144, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '2.25', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(640, 2144, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(641, 2144, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(642, 2144, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(643, 2144, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(644, 2144, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '1.25', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(645, 2145, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(646, 2145, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(647, 2145, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(648, 2145, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(649, 2145, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(650, 2145, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(651, 2145, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(652, 2145, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(653, 2145, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(654, 2145, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(655, 2145, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(656, 2145, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(657, 2145, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(658, 2145, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(659, 2145, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(660, 2145, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(661, 2145, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(662, 2145, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '1.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(663, 2145, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '1.25', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(664, 2145, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '1.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(665, 2145, 'BA204', 'Financial Management', 3.00, '2nd Year', '2nd', '2024-2025', '2.75', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(666, 2145, 'BA205', 'Human Resource Management', 3.00, '2nd Year', '2nd', '2024-2025', '3.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(667, 2145, 'BA206', 'Macroeconomics', 3.00, '2nd Year', '2nd', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(668, 2145, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '1.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(669, 2145, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '1.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(670, 2145, 'BA301', 'Operations Management and TQM', 3.00, '3rd Year', '1st', '2025-2026', '3.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(671, 2145, 'BA302', 'Business Analytics and Forecasting', 3.00, '3rd Year', '1st', '2025-2026', '1.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(672, 2145, 'BA303', 'Taxation (Income and Business)', 3.00, '3rd Year', '1st', '2025-2026', '1.25', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(673, 2145, 'BA304', 'Corporate Governance and Ethics', 3.00, '3rd Year', '1st', '2025-2026', '1.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(674, 2145, 'BA305', 'Strategic Management', 3.00, '3rd Year', '2nd', '2025-2026', '1.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(675, 2145, 'BA306', 'Business Feasibility and Research 1', 3.00, '3rd Year', '2nd', '2025-2026', '1.25', 'Passed', 'Passed', 'Dr. H. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(676, 2145, 'BA307', 'International Business and Trade', 3.00, '3rd Year', '2nd', '2025-2026', '1.50', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(677, 2145, 'BA401', 'Business Feasibility and Research 2', 3.00, '4th Year', '1st', '2026-2027', '1.25', 'Passed', 'Passed', 'Dr. H. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(678, 2145, 'BA402', 'Corporate Internship / Practicum (300 hrs)', 6.00, '4th Year', '1st', '2026-2027', '1.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(679, 2146, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(680, 2146, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(681, 2146, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(682, 2146, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(683, 2146, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(684, 2146, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(685, 2146, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(686, 2146, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(687, 2146, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(688, 2146, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(689, 2146, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(690, 2146, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(691, 2146, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(692, 2146, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(693, 2146, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '2.75', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(694, 2146, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(695, 2146, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(696, 2146, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '1.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(697, 2146, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '1.50', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(698, 2146, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '1.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(699, 2146, 'BA204', 'Financial Management', 3.00, '2nd Year', '2nd', '2024-2025', '3.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(700, 2146, 'BA205', 'Human Resource Management', 3.00, '2nd Year', '2nd', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(701, 2146, 'BA206', 'Macroeconomics', 3.00, '2nd Year', '2nd', '2024-2025', '1.25', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(702, 2146, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '1.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(703, 2146, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '1.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(704, 2146, 'BA301', 'Operations Management and TQM', 3.00, '3rd Year', '1st', '2025-2026', '1.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(705, 2146, 'BA302', 'Business Analytics and Forecasting', 3.00, '3rd Year', '1st', '2025-2026', '1.25', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(706, 2146, 'BA303', 'Taxation (Income and Business)', 3.00, '3rd Year', '1st', '2025-2026', '1.50', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(707, 2146, 'BA304', 'Corporate Governance and Ethics', 3.00, '3rd Year', '1st', '2025-2026', '1.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(708, 2147, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(709, 2147, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(710, 2147, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(711, 2147, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(712, 2147, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(713, 2147, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(714, 2147, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(715, 2147, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(716, 2147, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(717, 2147, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(718, 2147, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(719, 2147, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(720, 2147, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(721, 2147, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(722, 2147, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '3.00', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(723, 2147, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(724, 2147, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '1.25', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(725, 2147, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '1.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(726, 2147, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '1.75', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(727, 2147, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '2.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(728, 2147, 'BA204', 'Financial Management', 3.00, '2nd Year', '2nd', '2024-2025', '1.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(729, 2147, 'BA205', 'Human Resource Management', 3.00, '2nd Year', '2nd', '2024-2025', '1.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(730, 2147, 'BA206', 'Macroeconomics', 3.00, '2nd Year', '2nd', '2024-2025', '1.50', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(731, 2147, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '1.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(732, 2147, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '2.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(733, 2147, 'BA301', 'Operations Management and TQM', 3.00, '3rd Year', '1st', '2025-2026', '1.25', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(734, 2147, 'BA302', 'Business Analytics and Forecasting', 3.00, '3rd Year', '1st', '2025-2026', '1.50', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(735, 2147, 'BA303', 'Taxation (Income and Business)', 3.00, '3rd Year', '1st', '2025-2026', '1.75', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(736, 2147, 'BA304', 'Corporate Governance and Ethics', 3.00, '3rd Year', '1st', '2025-2026', '2.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(737, 2147, 'BA305', 'Strategic Management', 3.00, '3rd Year', '2nd', '2025-2026', '1.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(738, 2147, 'BA306', 'Business Feasibility and Research 1', 3.00, '3rd Year', '2nd', '2025-2026', '1.75', 'Passed', 'Passed', 'Dr. H. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(739, 2147, 'BA307', 'International Business and Trade', 3.00, '3rd Year', '2nd', '2025-2026', '2.00', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(740, 2147, 'BA401', 'Business Feasibility and Research 2', 3.00, '4th Year', '1st', '2026-2027', '1.75', 'Passed', 'Passed', 'Dr. H. Santos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(741, 2147, 'BA402', 'Corporate Internship / Practicum (300 hrs)', 6.00, '4th Year', '1st', '2026-2027', '2.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(742, 2148, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '2.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(743, 2148, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(744, 2148, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(745, 2148, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(746, 2148, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(747, 2148, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(748, 2148, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(749, 2148, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(750, 2148, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(751, 2148, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(752, 2148, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(753, 2148, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(754, 2148, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(755, 2148, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(756, 2149, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '3.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(757, 2149, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(758, 2149, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(759, 2149, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(760, 2149, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(761, 2149, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(762, 2149, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(763, 2149, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(764, 2149, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(765, 2149, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(766, 2149, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(767, 2149, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(768, 2149, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(769, 2149, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(770, 2149, 'BA201', 'Business Law and Obligations', 3.00, '2nd Year', '1st', '2024-2025', '1.25', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(771, 2149, 'BA202', 'Marketing Management', 3.00, '2nd Year', '1st', '2024-2025', '1.50', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(772, 2149, 'BA203', 'Microeconomics', 3.00, '2nd Year', '1st', '2024-2025', '1.75', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(773, 2149, 'GE106', 'Ethics', 3.00, '2nd Year', '1st', '2024-2025', '2.00', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(774, 2149, 'GE107', 'Science, Technology and Society', 3.00, '2nd Year', '1st', '2024-2025', '2.25', 'Passed', 'Passed', 'Prof. K. Perez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(775, 2149, 'PE103', 'Individual and Dual Sports', 2.00, '2nd Year', '1st', '2024-2025', '2.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(776, 2149, 'BA204', 'Financial Management', 3.00, '2nd Year', '2nd', '2024-2025', '1.50', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(777, 2149, 'BA205', 'Human Resource Management', 3.00, '2nd Year', '2nd', '2024-2025', '1.75', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(778, 2149, 'BA206', 'Macroeconomics', 3.00, '2nd Year', '2nd', '2024-2025', '2.00', 'Passed', 'Passed', 'Prof. F. Gomez', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(779, 2149, 'GE108', 'Life and Works of Rizal', 3.00, '2nd Year', '2nd', '2024-2025', '2.25', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(780, 2149, 'PE104', 'Team Sports', 2.00, '2nd Year', '2nd', '2024-2025', '2.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(781, 2149, 'BA301', 'Operations Management and TQM', 3.00, '3rd Year', '1st', '2025-2026', '1.75', 'Passed', 'Passed', 'Prof. E. Morales', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(782, 2149, 'BA302', 'Business Analytics and Forecasting', 3.00, '3rd Year', '1st', '2025-2026', '2.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(783, 2149, 'BA303', 'Taxation (Income and Business)', 3.00, '3rd Year', '1st', '2025-2026', '2.25', 'Passed', 'Passed', 'Atty. R. Tan', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(784, 2149, 'BA304', 'Corporate Governance and Ethics', 3.00, '3rd Year', '1st', '2025-2026', '2.50', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(785, 2150, 'BA101', 'Principles of Management', 3.00, '1st Year', '1st', '2023-2024', '1.00', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(786, 2150, 'BA102', 'Financial Accounting Fundamentals', 3.00, '1st Year', '1st', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(787, 2150, 'GE101', 'Understanding the Self', 3.00, '1st Year', '1st', '2023-2024', '1.50', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(788, 2150, 'GE102', 'Purposive Communication', 3.00, '1st Year', '1st', '2023-2024', '1.75', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(789, 2150, 'MATH101', 'Mathematics in the Modern World', 3.00, '1st Year', '1st', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. D. Garcia', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(790, 2150, 'NSTP101', 'National Service Training Program 1', 3.00, '1st Year', '1st', '2023-2024', '2.25', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(791, 2150, 'PE101', 'Physical Fitness and Wellness', 2.00, '1st Year', '1st', '2023-2024', '2.50', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(792, 2150, 'BA103', 'Business Organization and Environment', 3.00, '1st Year', '2nd', '2023-2024', '1.25', 'Passed', 'Passed', 'Prof. M. Castro', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(793, 2150, 'BA104', 'Managerial Accounting', 3.00, '1st Year', '2nd', '2023-2024', '1.50', 'Passed', 'Passed', 'Prof. J. Dizon', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(794, 2150, 'GE103', 'Readings in Philippine History', 3.00, '1st Year', '2nd', '2023-2024', '1.75', 'Passed', 'Passed', 'Dr. A. Reyes', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(795, 2150, 'GE104', 'The Contemporary World', 3.00, '1st Year', '2nd', '2023-2024', '2.00', 'Passed', 'Passed', 'Prof. M. Ramos', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(796, 2150, 'GE105', 'Art Appreciation', 3.00, '1st Year', '2nd', '2023-2024', '2.25', 'Passed', 'Passed', 'Prof. C. Flores', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(797, 2150, 'NSTP102', 'National Service Training Program 2', 3.00, '1st Year', '2nd', '2023-2024', '2.50', 'Passed', 'Passed', 'Capt. V. Luna', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL),
(798, 2150, 'PE102', 'Rhythmic Activities', 2.00, '1st Year', '2nd', '2023-2024', '2.75', 'Passed', 'Passed', 'Coach B. Diaz', '2026-08-29 22:57:50', '2026-08-29 22:57:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_counters`
--

CREATE TABLE `reg_counters` (
  `id` int(10) UNSIGNED NOT NULL,
  `counter_key` varchar(60) NOT NULL,
  `counter_value` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `prefix` varchar(40) DEFAULT NULL,
  `format_pattern` varchar(40) DEFAULT NULL,
  `reset_frequency` varchar(20) NOT NULL DEFAULT 'yearly',
  `last_reset_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_counters`
--

INSERT INTO `reg_counters` (`id`, `counter_key`, `counter_value`, `prefix`, `format_pattern`, `reset_frequency`, `last_reset_at`, `updated_at`) VALUES
(5, 'REQ_NO', 4, 'DOC-REQ', 'YYYY-####', 'yearly', NULL, '2026-08-29 23:56:43'),
(6, 'FORM137_NO', 0, 'FORM137', 'YYYY-###', 'yearly', NULL, '2026-08-29 22:57:36'),
(7, 'GMC_NO', 0, 'GMC', 'YYYY-###', 'yearly', NULL, '2026-08-29 22:57:36'),
(8, 'RELEASE_NO', 0, 'REL', 'YYYY-###', 'yearly', NULL, '2026-08-29 22:57:36'),
(9, 'GOOD_MORAL_CERTIFICATE', 8, 'GOOD_MORAL_CERTIFICATE', 'YYYY-####', 'yearly', NULL, '2026-08-30 20:09:49'),
(10, 'FORM137', 2, 'FORM137', 'YYYY-####', 'yearly', NULL, '2026-08-29 23:54:51'),
(11, 'CERTIFICATE_OF_ENROLLMENT', 49, 'CERTIFICATE_OF_ENROLLMENT', 'YYYY-####', 'yearly', NULL, '2026-11-19 19:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `reg_credentials`
--

CREATE TABLE `reg_credentials` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `credential_type` varchar(40) NOT NULL,
  `token_value` varchar(255) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Active',
  `activated_at` datetime DEFAULT NULL,
  `deactivated_at` datetime DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_credentials`
--

INSERT INTO `reg_credentials` (`id`, `student_id`, `credential_type`, `token_value`, `status`, `activated_at`, `deactivated_at`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
(21, 2141, 'RFID', '65FD8D103C93', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(22, 2141, 'QR', 'SMS2QR2024001', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(23, 2142, 'RFID', '400543E69453', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(24, 2142, 'QR', 'SMS2QR2024002', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(25, 2143, 'RFID', '4614D12D3D97', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(26, 2143, 'QR', 'SMS2QR2024003', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(27, 2144, 'RFID', '43F6E3B91B9D', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(28, 2144, 'QR', 'SMS2QR2024004', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(29, 2145, 'RFID', 'EB51EE740A83', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(30, 2145, 'QR', 'SMS2QR2024005', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(31, 2146, 'RFID', 'A939667BCEC1', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(32, 2146, 'QR', 'SMS2QR2024006', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(33, 2147, 'RFID', 'FBCAC59499AD', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(34, 2147, 'QR', 'SMS2QR2024007', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(35, 2148, 'RFID', '062B929A3F0D', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(36, 2148, 'QR', 'SMS2QR2024008', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(37, 2149, 'RFID', '5A2C0D526E7E', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(38, 2149, 'QR', 'SMS2QR2024009', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(39, 2150, 'RFID', '99F5291A3225', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(40, 2150, 'QR', 'SMS2QR2024010', 'Active', '2026-08-29 22:57:35', NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_doc_releases`
--

CREATE TABLE `reg_doc_releases` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED DEFAULT NULL,
  `request_item_id` int(10) UNSIGNED DEFAULT NULL,
  `claim_type` varchar(40) NOT NULL,
  `release_slip_no` varchar(40) NOT NULL,
  `released_at` datetime NOT NULL DEFAULT current_timestamp(),
  `released_by` int(10) UNSIGNED NOT NULL,
  `claimant_name` varchar(200) NOT NULL,
  `claimant_id` varchar(100) DEFAULT NULL,
  `claimant_signature` longblob DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_doc_requests`
--

CREATE TABLE `reg_doc_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_no` varchar(40) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `channel` varchar(40) NOT NULL DEFAULT 'walk-in',
  `student_email` varchar(190) DEFAULT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `payment_ref` varchar(80) DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Submitted',
  `requested_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_doc_requests`
--

INSERT INTO `reg_doc_requests` (`id`, `request_no`, `student_id`, `purpose`, `channel`, `student_email`, `paid`, `payment_ref`, `status`, `requested_by`, `created_at`, `updated_at`, `created_by`) VALUES
(6, 'DOC-REQ-2026-0001', 2141, 'Application to visa', 'walk-in', 'student1@email.com', 0, NULL, 'Submitted', NULL, '2026-08-29 22:57:36', '2026-08-29 22:57:36', NULL),
(7, 'DOC-REQ-2026-0002', 2142, 'Application to another school', 'online', 'student2@email.com', 0, NULL, 'Submitted', NULL, '2026-08-29 22:57:36', '2026-08-29 22:57:36', NULL),
(8, 'DOC-REQ-2026-0003', 2143, 'Application to employer', 'walk-in', 'student3@email.com', 0, NULL, 'Verified', NULL, '2026-08-29 22:57:36', '2026-08-29 22:57:36', NULL),
(9, 'DOC-REQ-2026-0004', 2144, 'Application to another school', 'online', 'student4@email.com', 0, NULL, 'Verified', NULL, '2026-08-29 22:57:36', '2026-08-29 22:57:36', NULL),
(10, 'DOC-REQ-2026-0005', 2145, 'Application to another school', 'walk-in', 'student5@email.com', 0, NULL, 'Processing', NULL, '2026-08-29 22:57:36', '2026-08-29 22:57:36', NULL),
(15, 'REQ-20260830-234655', 2141, 'Scholarship', 'email', 'boyrexar02@gmail.com', 1, NULL, 'For Review', NULL, '2026-08-30 23:46:55', '2026-08-30 23:46:55', NULL),
(16, 'REQ-20260830-234728', 2141, 'Scholarship', 'email', 'boyrexar02@gmail.com', 1, NULL, 'For Review', NULL, '2026-08-30 23:47:28', '2026-08-30 23:47:28', NULL),
(17, 'REQ-20260830-235408', 2141, 'Scholarship', 'email', 'boyrexar02@gmail.com', 1, NULL, 'Released', NULL, '2026-08-30 23:54:08', '2026-08-31 01:49:36', NULL),
(18, 'REQ-20260904-120402', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'Released', NULL, '2026-09-04 12:04:02', '2026-09-04 14:46:02', NULL),
(19, 'REQ-20260904-135601', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'For Review', NULL, '2026-09-04 13:56:01', '2026-09-04 13:56:01', NULL),
(20, 'REQ-20260904-145535', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'For Review', NULL, '2026-09-04 14:55:35', '2026-09-04 14:55:35', NULL),
(21, 'REQ-20260905-150515', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'For Review', NULL, '2026-09-05 15:05:15', '2026-09-05 15:05:15', NULL),
(22, 'REQ-20260905-172153', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'For Review', NULL, '2026-09-05 17:21:53', '2026-09-05 17:21:53', NULL),
(23, 'REQ-20260905-172623', 2141, 'Scholarship', 'email', 'lixx1920@gmail.com', 1, NULL, 'For Review', NULL, '2026-09-05 17:26:23', '2026-09-05 17:26:23', NULL),
(24, 'REQ-20260919-091242', 2166, 'Employment', 'email', 'denrellsilva08@gmail.com', 1, NULL, 'For Release', NULL, '2026-09-19 09:12:42', '2026-11-19 20:55:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_doc_request_items`
--

CREATE TABLE `reg_doc_request_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `request_id` int(10) UNSIGNED NOT NULL,
  `doc_type` varchar(60) NOT NULL,
  `copies` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `format` varchar(20) NOT NULL DEFAULT 'PDF',
  `status` varchar(40) NOT NULL DEFAULT 'Pending',
  `notes` varchar(255) DEFAULT NULL,
  `generated_file_id` int(10) UNSIGNED DEFAULT NULL,
  `verification_code_id` int(10) UNSIGNED DEFAULT NULL,
  `released_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_doc_request_items`
--

INSERT INTO `reg_doc_request_items` (`id`, `request_id`, `doc_type`, `copies`, `format`, `status`, `notes`, `generated_file_id`, `verification_code_id`, `released_at`, `created_at`) VALUES
(11, 6, 'GMC', 2, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(12, 7, 'GMC', 3, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(13, 7, 'TOR', 3, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(14, 8, 'GMC', 1, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(15, 9, 'TOR', 1, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(16, 9, 'FORM137', 1, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(17, 10, 'TOR', 2, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(18, 10, 'TOR', 3, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-29 22:57:36'),
(19, 15, 'coe', 1, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-30 23:46:55'),
(20, 16, 'coe', 1, 'PDF', 'Pending', NULL, NULL, NULL, NULL, '2026-08-30 23:47:28'),
(21, 17, 'COE', 1, 'PDF', 'Released', NULL, 1, 1, NULL, '2026-08-30 23:54:08'),
(22, 18, 'COE', 1, 'PDF', 'Released', NULL, 2, 2, NULL, '2026-09-04 12:04:02'),
(23, 19, 'COE', 1, 'PDF', 'Generated', NULL, 3, 3, NULL, '2026-09-04 13:56:01'),
(24, 20, 'COE', 1, 'PDF', 'Generated', NULL, 4, 4, NULL, '2026-09-04 14:55:35'),
(25, 21, 'COE', 1, 'PDF', 'Generated', NULL, 5, 5, NULL, '2026-09-05 15:05:15'),
(26, 22, 'COE', 1, 'PDF', 'Generated', NULL, 6, 6, NULL, '2026-09-05 17:21:53'),
(27, 23, 'COE', 1, 'PDF', 'Generated', NULL, 7, 7, NULL, '2026-09-05 17:26:23'),
(28, 24, 'COE', 1, 'PDF', 'Generated', NULL, 32, 8, NULL, '2026-09-19 09:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `reg_doc_templates`
--

CREATE TABLE `reg_doc_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `doc_type` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `template_path` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_doc_templates`
--

INSERT INTO `reg_doc_templates` (`id`, `template_name`, `doc_type`, `description`, `template_path`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'Form 137', 'FORM137', 'Form 137 - Transcript of Records', '/templates/form137.php', 1, '2026-08-29 22:57:36', '2026-08-29 22:57:36'),
(6, 'Certificate of Good Moral', 'GMC', 'Certificate of Good Moral Character', '/templates/gmc.php', 1, '2026-08-29 22:57:36', '2026-08-29 22:57:36'),
(7, 'TOR Transcript', 'TOR', 'Transcript of Records', '/templates/tor.php', 1, '2026-08-29 22:57:36', '2026-08-29 22:57:36'),
(8, 'Certification', 'CERT', 'General Certification', '/templates/certification.php', 1, '2026-08-29 22:57:36', '2026-08-29 22:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `reg_faculty_clearances`
--

CREATE TABLE `reg_faculty_clearances` (
  `id` int(10) UNSIGNED NOT NULL,
  `faculty_id` varchar(50) NOT NULL,
  `faculty_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `employment_status` varchar(50) NOT NULL DEFAULT 'Regular',
  `contract_expiry` date DEFAULT NULL,
  `academic_year` varchar(30) NOT NULL DEFAULT '2024-2025',
  `semester` varchar(30) NOT NULL DEFAULT '1st Semester',
  `requirement_name` varchar(100) NOT NULL DEFAULT 'Academic Clearance',
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Not Submitted',
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_faculty_clearances`
--

INSERT INTO `reg_faculty_clearances` (`id`, `faculty_id`, `faculty_name`, `email`, `department`, `employment_status`, `contract_expiry`, `academic_year`, `semester`, `requirement_name`, `file_path`, `file_name`, `file_size`, `status`, `submitted_at`, `reviewed_by`, `reviewed_at`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'FAC-2026-0005', 'Jean Claude Espejo', 'j.espejo@bcp.edu.ph', 'BSIT', 'Probationary', '2032-06-09', '2024-2025', '1st Semester', 'Academic Clearance', 'uploads/clearances/faculty/LonzagaFS_0907.pdf', 'LonzagaFS_0907.pdf', 1420500, 'Under Verification', '2026-09-18 10:15:00', NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(2, 'FAC-2026-0008', 'diosdado diosdado aban jy', 'd.aban@bcp.edu.ph', 'BSIT', 'Regular', '2027-11-18', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(3, 'FAC-2026-0047', 'dsadwadsa dsadwadsa asdfg', 'd.asdfg@bcp.edu.ph', 'BSIT', 'Contractual', NULL, '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(4, 'FAC-2026-0006', 'Brix brax brix', 'b.brix@bcp.edu.ph', 'BSIT', 'Probationary', '2026-08-14', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(5, 'FAC-2026-0059', 'Michael Vincent V Castillo', 'm.castillo@bcp.edu.ph', 'BSIT', 'Part-Time', NULL, '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(6, 'FAC-2026-0010', 'Ramon Garcia', 'ramon.garcia@bcp.edu.ph', 'BSCpE', 'Regular', '2029-05-20', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(7, 'FAC-2026-0012', 'Maria Clara Santos', 'maria.clara.santos@bcp.edu.ph', 'BSBA', 'Regular', '2030-03-15', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(8, 'FAC-2026-0015', 'Jose Rizal Lopez', 'jose.rizal.lopez@bcp.edu.ph', 'BSIT', 'Probationary', '2027-09-30', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(9, 'FAC-2026-0018', 'Ana Marie Villanueva', 'ana.marie.villanueva@bcp.edu.ph', 'BSCrim', 'Regular', '2028-12-31', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(10, 'FAC-2026-0021', 'Eduardo Manalo', 'eduardo.manalo@bcp.edu.ph', 'BSAIS', 'Contractual', '2026-12-15', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(11, 'FAC-2026-0024', 'Lorna Tolentino', 'lorna.tolentino@bcp.edu.ph', 'BSHM', 'Regular', '2031-01-20', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(12, 'FAC-2026-0027', 'Fernando Poe Dalisay', 'fernando.poe.dalisay@bcp.edu.ph', 'BSCrim', 'Regular', '2029-10-10', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(13, 'FAC-2026-0030', 'Teresa Magbanua', 'teresa.magbanua@bcp.edu.ph', 'BSIT', 'Probationary', '2027-04-18', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(14, 'FAC-2026-0033', 'Gabriel Silang', 'gabriel.silang@bcp.edu.ph', 'BSCpE', 'Part-Time', NULL, '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(15, 'FAC-2026-0036', 'Melchora Aquino Ramos', 'melchora.aquino.ramos@bcp.edu.ph', 'BSBA', 'Regular', '2032-11-05', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(16, 'FAC-2026-0039', 'Andres Bonifacio Del Rosario', 'andres.bonifacio.del.rosario@bcp.edu.ph', 'BSIT', 'Regular', '2030-08-22', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(17, 'FAC-2026-0042', 'Apolinario Mabini Cruz', 'apolinario.mabini.cruz@bcp.edu.ph', 'BSIT', 'Contractual', '2027-02-28', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(18, 'FAC-2026-0045', 'Emilio Aguinaldo Famy', 'emilio.aguinaldo.famy@bcp.edu.ph', 'BSAIS', 'Regular', '2028-06-30', '2024-2025', '1st Semester', 'Academic Clearance', NULL, NULL, NULL, 'Not Submitted', NULL, NULL, NULL, NULL, '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(19, 'FAC-2025-0001', 'Prof. Roberto D. Alvarez', 'robertodalvarez@bcp.edu.ph', 'BSIT', 'Regular', '2028-05-15', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0001.pdf', 'Clearance_FAC-2025-0001_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Cleared - Complete grade sheets and final grading records filed.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(20, 'FAC-2025-0002', 'Dr. Elena S. Mendoza', 'elenasmendoza@bcp.edu.ph', 'BSBA', 'Regular', '2030-01-10', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0002.pdf', 'Clearance_FAC-2025-0002_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'All course syllabi and grade rosters verified.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(21, 'FAC-2025-0003', 'Engr. Daniel V. Cruz', 'danielvcruz@bcp.edu.ph', 'BSCpE', 'Regular', '2029-08-20', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0003.pdf', 'Clearance_FAC-2025-0003_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Final grades submitted on schedule. Approved.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(22, 'FAC-2025-0004', 'Prof. Carmela M. Bautista', 'carmelambautista@bcp.edu.ph', 'BSCrim', 'Regular', '2027-12-15', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0004.pdf', 'Clearance_FAC-2025-0004_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Complete academic documents verified.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(23, 'FAC-2025-0005', 'Dr. Arthur T. Panganiban', 'arthurtpanganiban@bcp.edu.ph', 'BSIT', 'Regular', '2031-03-30', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0005.pdf', 'Clearance_FAC-2025-0005_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Class records and grade sheets signed and archived.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(24, 'FAC-2025-0006', 'Prof. Gina L. Soriano', 'ginalsoriano@bcp.edu.ph', 'BSAIS', 'Regular', '2028-09-15', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0006.pdf', 'Clearance_FAC-2025-0006_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Cleared for previous semester.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(25, 'FAC-2025-0007', 'Engr. Victor H. Morales', 'victorhmorales@bcp.edu.ph', 'BSCpE', 'Regular', '2032-06-18', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0007.pdf', 'Clearance_FAC-2025-0007_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'All requirements completed.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(26, 'FAC-2025-0008', 'Dr. Cynthia B. Reyes', 'cynthiabreyes@bcp.edu.ph', 'BSBA', 'Regular', '2029-04-22', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0008.pdf', 'Clearance_FAC-2025-0008_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Grade sheets and instructional materials verified.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(27, 'FAC-2025-0009', 'Prof. Noel K. Francisco', 'noelkfrancisco@bcp.edu.ph', 'BSIT', 'Regular', '2027-11-05', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0009.pdf', 'Clearance_FAC-2025-0009_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Cleared by Registrar.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(28, 'FAC-2025-0010', 'Prof. Marilou G. Ocampo', 'marilougocampo@bcp.edu.ph', 'BSHM', 'Regular', '2030-07-12', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0010.pdf', 'Clearance_FAC-2025-0010_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Requirement approved.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(29, 'FAC-2025-0011', 'Dr. Jonathan P. Santos', 'jonathanpsantos@bcp.edu.ph', 'BSIT', 'Regular', '2028-10-30', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0011.pdf', 'Clearance_FAC-2025-0011_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'All student concerns resolved. Approved.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(30, 'FAC-2025-0012', 'Prof. Rachel E. Gomez', 'rachelegomez@bcp.edu.ph', 'BSBA', 'Regular', '2031-05-14', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0012.pdf', 'Clearance_FAC-2025-0012_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Cleared.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(31, 'FAC-2025-0013', 'Engr. Paul C. Domingo', 'paulcdomingo@bcp.edu.ph', 'BSCpE', 'Regular', '2029-01-25', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0013.pdf', 'Clearance_FAC-2025-0013_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Academic records archived.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(32, 'FAC-2025-0014', 'Dr. Beatrice N. Tan', 'beatricentan@bcp.edu.ph', 'BSAIS', 'Regular', '2032-08-19', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0014.pdf', 'Clearance_FAC-2025-0014_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Approved and verified.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(33, 'FAC-2025-0015', 'Prof. Vincent R. Pascual', 'vincentrpascual@bcp.edu.ph', 'BSCrim', 'Regular', '2028-03-11', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0015.pdf', 'Clearance_FAC-2025-0015_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Complete compliance verified.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(34, 'FAC-2025-0016', 'Prof. Diana F. Ramos', 'dianaframos@bcp.edu.ph', 'BSHM', 'Regular', '2030-12-08', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0016.pdf', 'Clearance_FAC-2025-0016_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Cleared.', '2026-11-12 13:46:06', '2026-11-12 13:46:06'),
(35, 'FAC-2025-0017', 'Dr. Kenneth M. Villanueva', 'kennethmvillanueva@bcp.edu.ph', 'BSIT', 'Regular', '2029-09-17', '2023-2024', '2nd Semester', 'Academic Clearance', 'uploads/clearances/faculty/archived_fac-2025-0017.pdf', 'Clearance_FAC-2025-0017_signed.pdf', 1250000, 'Approved', '2026-03-15 14:00:00', NULL, '2026-03-18 16:30:00', 'Approved by Registrar Officer.', '2026-11-12 13:46:06', '2026-11-12 13:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `reg_files`
--

CREATE TABLE `reg_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(60) NOT NULL DEFAULT 'General',
  `original_name` varchar(300) NOT NULL DEFAULT '',
  `stored_name` varchar(300) NOT NULL DEFAULT '',
  `mime` varchar(120) DEFAULT NULL,
  `size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sha256_hash` char(64) DEFAULT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Active',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_files`
--

INSERT INTO `reg_files` (`id`, `student_id`, `category`, `original_name`, `stored_name`, `mime`, `size`, `sha256_hash`, `verification_token`, `uploaded_by`, `status`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0014', 'certificate_of_enrollment-2024001_1788107911.pdf', 'application/pdf', 501100, '3e7d5010bf31f51f6dd635b731e2bf6d0e474b1a0b40f7be5a4fa28670589bc7', NULL, 1, 'Active', 0, '2026-08-31 00:38:36', '2026-08-31 00:38:36'),
(2, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0017', 'certificate_of_enrollment-2024001_1788494789.pdf', 'application/pdf', 501100, 'c6d6713a69af9f352a9646e0fa0819d3b08c411a5c2f42b65aaed0d683731ed0', NULL, 1, 'Active', 0, '2026-09-04 12:06:36', '2026-09-04 12:06:36'),
(3, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0029', 'certificate_of_enrollment-2024001_1788504263.pdf', 'application/pdf', 520402, 'b23822aa4600621d845125cd7dd015d6b4e1aea19a6cfbb22f8a7d2328a430ba', '13ef5aac0ec440d198478195fd398ba8', 1, 'Active', 0, '2026-09-04 14:44:24', '2026-09-04 14:44:24'),
(4, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0033', 'certificate_of_enrollment-2024001_1788584921.pdf', 'application/pdf', 520391, '5f680c965a0aadad68f5e194012e35e9921c002181580816a0426760bb65ec9e', '420afc0c5712e23cca530c8afc50dff9', 1, 'Active', 0, '2026-09-05 13:08:47', '2026-09-05 13:08:47'),
(5, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0042', 'certificate_of_enrollment-2024001_1788598273.pdf', 'application/pdf', 520371, '93e7c6a7d0f1c96b1ec74df0c9f04f90012cdd016600c3f69c0373d2646e7753', '01c6a762a5e9c1b04a4b3f42dafdd1c6', 1, 'Active', 0, '2026-09-05 16:51:26', '2026-09-05 16:51:26'),
(6, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0046', 'certificate_of_enrollment-2024001_1788600145.pdf', 'application/pdf', 520136, '70c05a683cf6304424b0edff82a06e734a637e48b83323da5ac67dd9784fd4cb', 'e3b8f367a58eebdf54ba94d8ecae568b', 1, 'Active', 0, '2026-09-05 17:22:30', '2026-09-05 17:22:30'),
(7, 2141, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0048', 'certificate_of_enrollment-2024001_1788600419.pdf', 'application/pdf', 520446, '9a5e3d9237e350d3e67cf6461bc45fb854ccc4d44a9a0e99b09cb5ed408425a4', 'd26e79b536dae964f42f07c5493a7427', 1, 'Active', 0, '2026-09-05 17:27:03', '2026-09-05 17:27:03'),
(8, 2155, 'form_138', 'Ban Reii student-admission-slip.pdf', 'e9d15f290bd946630c0dc52a96692043.pdf', 'application/pdf', 174152, NULL, NULL, 585, 'Active', 0, '2026-09-08 18:10:24', '2026-09-08 19:41:14'),
(9, 2155, 'good_moral', 'Ban Reii student-admission-slip.pdf', '2490ac9413f94c0f2d66fe30ffc4e137.pdf', 'application/pdf', 174152, NULL, NULL, 585, 'Active', 0, '2026-09-08 18:10:24', '2026-09-08 19:41:14'),
(10, 2155, 'psa_birth_cert', 'Ban Reii student-admission-slip.pdf', 'eb9518eeb288e4103f169c7fec483f36.pdf', 'application/pdf', 174152, NULL, NULL, 585, 'Active', 0, '2026-09-08 18:10:24', '2026-09-08 19:41:14'),
(11, 2155, 'id_picture', '798414015_1081290994299575_2326789361396915968_n.jpg', 'd6fcc4d131d0ad08d7e521ceefd14fbf.jpg', 'image/jpeg', 167970, NULL, NULL, 585, 'Active', 0, '2026-09-08 18:10:24', '2026-09-08 19:41:14'),
(12, 2157, 'form_138', 'Ban Reii student-admission-slip.pdf', '5022c05f874bb5e9aec4b7d4b59a47ef.pdf', 'application/pdf', 174152, NULL, NULL, 587, 'Active', 0, '2026-09-08 20:24:40', '2026-09-08 20:24:40'),
(13, 2157, 'good_moral', 'Ban Reii student-admission-slip.pdf', '1000b2a9f8c6923f679c3138ea963a75.pdf', 'application/pdf', 174152, NULL, NULL, 587, 'Active', 0, '2026-09-08 20:24:40', '2026-09-08 20:24:40'),
(14, 2157, 'psa_birth_cert', 'Ban Reii student-admission-slip.pdf', 'd1a31d73a8943736df37e95e4ad14222.pdf', 'application/pdf', 174152, NULL, NULL, 587, 'Active', 0, '2026-09-08 20:24:40', '2026-09-08 20:24:40'),
(15, 2157, 'id_picture', '794327095_1271871481672766_1280323881804511319_n.jpg', '93618be30608503518953bf25def4602.jpg', 'image/jpeg', 134870, NULL, NULL, 587, 'Active', 0, '2026-09-08 20:24:40', '2026-09-08 20:24:40'),
(16, 2158, 'form_138', 'Ban Reii student-admission-slip.pdf', '7680052f72794b18f4ada6a48db7f9f8.pdf', 'application/pdf', 174152, NULL, NULL, 588, 'Active', 0, '2026-09-08 22:19:56', '2026-09-08 22:19:56'),
(17, 2158, 'good_moral', 'Ban Reii student-admission-slip.pdf', '686012530a6dc61223c862533e465b32.pdf', 'application/pdf', 174152, NULL, NULL, 588, 'Active', 0, '2026-09-08 22:19:57', '2026-09-08 22:19:57'),
(18, 2158, 'psa_birth_cert', 'Ban Reii student-admission-slip.pdf', '5d50138d11d4585a3472aabb394ce47f.pdf', 'application/pdf', 174152, NULL, NULL, 588, 'Active', 0, '2026-09-08 22:19:57', '2026-09-08 22:19:57'),
(19, 2158, 'id_picture', '794214519_2117558002529636_8581668781255067844_n.jpg', '554969e011d2c41beaa2d3e6768b0210.jpg', 'image/jpeg', 306633, NULL, NULL, 588, 'Active', 0, '2026-09-08 22:19:57', '2026-09-08 22:19:57'),
(20, 2159, 'form_138', 'Ban Reii student-admission-slip.pdf', 'd1e130e1e7848ff385d605723ab6cf99.pdf', 'application/pdf', 174152, NULL, NULL, 589, 'Active', 0, '2026-09-08 22:59:33', '2026-09-08 22:59:33'),
(21, 2159, 'good_moral', 'Ban Reii student-admission-slip.pdf', '08997c7aa7a74ff192744e37aefd721f.pdf', 'application/pdf', 174152, NULL, NULL, 589, 'Active', 0, '2026-09-08 22:59:33', '2026-09-08 22:59:33'),
(22, 2159, 'psa_birth_cert', 'Ban Reii student-admission-slip.pdf', 'b5cf4ac87668ffa59cf69f856bbe0855.pdf', 'application/pdf', 174152, NULL, NULL, 589, 'Active', 0, '2026-09-08 22:59:33', '2026-09-08 22:59:33'),
(23, 2159, 'id_picture', '69f5a3b3-8585-4923-bc98-ff5c89dd01b6.jpg', '0c6a6a5388e27e029eae7af7b7394300.jpg', 'image/jpeg', 302740, NULL, NULL, 589, 'Active', 0, '2026-09-08 22:59:33', '2026-09-08 22:59:33'),
(24, 2161, 'form_138', 'Ban Reii student-admission-slip.pdf', '64432a0a9644e398b71ad91d36ad174a.pdf', 'application/pdf', 174152, NULL, NULL, 590, 'Active', 0, '2026-09-08 23:29:11', '2026-09-08 23:29:11'),
(25, 2161, 'good_moral', 'Ban Reii student-admission-slip.pdf', '13400f4b330131e733ad87829810b70c.pdf', 'application/pdf', 174152, NULL, NULL, 590, 'Active', 0, '2026-09-08 23:29:11', '2026-09-08 23:29:11'),
(26, 2161, 'psa_birth_cert', 'Ban Reii student-admission-slip.pdf', '2ef75d08e797c99d53e8a1393b8eda0f.pdf', 'application/pdf', 174152, NULL, NULL, 590, 'Active', 0, '2026-09-08 23:29:11', '2026-09-08 23:29:11'),
(27, 2161, 'id_picture', '69f5a3b3-8585-4923-bc98-ff5c89dd01b6.jpg', '8e7ec38e46691a557ac5d2ac89b2f3c5.jpg', 'image/jpeg', 302740, NULL, NULL, 590, 'Active', 0, '2026-09-08 23:29:11', '2026-09-08 23:29:11'),
(28, 2166, 'form_138', '07072026-e7a4-48e9-beeb-0e583bc412da.jpg', '62b57c6fca7a8f6740a613e2207eb1aa.jpg', 'image/jpeg', 94258, NULL, NULL, 617, 'Active', 0, '2026-09-19 09:09:10', '2026-09-19 09:09:10'),
(29, 2166, 'good_moral', 'ab272601-167a-4093-98b1-9ecca2a3bee9.jpg', '1b0d359e5dcfdbe4a68a5f0611525ef5.jpg', 'image/jpeg', 98649, NULL, NULL, 617, 'Active', 0, '2026-09-19 09:09:10', '2026-09-19 09:09:10'),
(30, 2166, 'psa_birth_cert', '07072026-e7a4-48e9-beeb-0e583bc412da.jpg', 'cef6db4f5dfc0106ac1f405307e6ef96.jpg', 'image/jpeg', 94258, NULL, NULL, 617, 'Active', 0, '2026-09-19 09:09:10', '2026-09-19 09:09:10'),
(31, 2166, 'id_picture', '171121a7-c3ff-45ea-8cd8-a4ffbda5885d.jpg', '4b2d1f207a3880ce58f2e0c6de2636bb.jpg', 'image/jpeg', 118110, NULL, NULL, 617, 'Active', 0, '2026-09-19 09:09:10', '2026-09-19 09:09:10'),
(32, 2166, 'documents', 'Certificate of Enrollment - CERTIFICATE_OF_ENROLLMENT-2026-0049', 'certificate_of_enrollment-20264728_1795087954.html', 'text/html', 787165, '2fdd651ca66a42f6a17521cca45a56e6a4c446cdc48e3807f2114a3c05c4c76d', 'fda23d92a5fb0a1dfe0dfc77d653a83f', 1, 'Active', 0, '2026-11-19 19:32:34', '2026-11-19 19:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `reg_guardians`
--

CREATE TABLE `reg_guardians` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `relationship` varchar(60) NOT NULL,
  `contact` varchar(40) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_emergency` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_guardians`
--

INSERT INTO `reg_guardians` (`id`, `student_id`, `full_name`, `relationship`, `contact`, `email`, `address`, `is_primary`, `is_emergency`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(21, 2141, 'Mother Name 1', 'Mother', '09668696334', 'mother1@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(22, 2141, 'Father Name 1', 'Father', '09597130036', 'father1@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(23, 2142, 'Mother Name 2', 'Mother', '09206880739', 'mother2@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(24, 2142, 'Father Name 2', 'Father', '09976472647', 'father2@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(25, 2143, 'Mother Name 3', 'Mother', '09483280396', 'mother3@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(26, 2143, 'Father Name 3', 'Father', '09450466898', 'father3@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(27, 2144, 'Mother Name 4', 'Mother', '09364492326', 'mother4@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(28, 2144, 'Father Name 4', 'Father', '09140608611', 'father4@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(29, 2145, 'Mother Name 5', 'Mother', '09965854693', 'mother5@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(30, 2145, 'Father Name 5', 'Father', '09830523141', 'father5@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(31, 2146, 'Mother Name 6', 'Mother', '09377219589', 'mother6@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(32, 2146, 'Father Name 6', 'Father', '09382974489', 'father6@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(33, 2147, 'Mother Name 7', 'Mother', '09597607555', 'mother7@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(34, 2147, 'Father Name 7', 'Father', '09405407485', 'father7@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(35, 2148, 'Mother Name 8', 'Mother', '09501747397', 'mother8@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(36, 2148, 'Father Name 8', 'Father', '09283035219', 'father8@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(37, 2149, 'Mother Name 9', 'Mother', '09553941338', 'mother9@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(38, 2149, 'Father Name 9', 'Father', '09800047550', 'father9@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(39, 2150, 'Mother Name 10', 'Mother', '09861291460', 'mother10@email.com', 'Address Line, City', 1, 0, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(40, 2150, 'Father Name 10', 'Father', '09317348995', 'father10@email.com', 'Address Line, City', 0, 1, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(41, 2160, 'Guard Test 2', 'Mother', '09123', NULL, NULL, 1, 1, '2026-09-08 23:24:44', '2026-09-08 23:24:44', NULL, NULL),
(42, 2161, 'Marshaly', 'Mother', '123123123123', 'lixx1920@gmail.com', 'asdasdasdasd', 1, 1, '2026-09-08 23:27:42', '2026-09-08 23:27:42', NULL, NULL),
(43, 2162, 'Marshaly', 'Mother', '123123123123', 'lixx1920@gmail.com', 'asdasdasdasd', 1, 1, '2026-09-09 00:43:47', '2026-09-09 00:43:47', NULL, NULL),
(44, 2163, 'Marshaly', 'Mother', '123123123123', 'lixx1920@gmail.com', 'asdasdasdasd', 1, 1, '2026-09-09 00:49:01', '2026-09-09 00:49:01', NULL, NULL),
(51, 2166, 'gggggg', 'Father', '09090909876', 'dedede@gmail.com', 'cccccc', 0, 1, '2026-09-19 03:14:52', '2026-09-19 03:14:52', NULL, NULL),
(52, 2166, 'gdfgfdgd', 'Mother', '09876543987', 'dededdadae@gmail.com', 'dfdfdfd', 0, 1, '2026-09-19 03:14:52', '2026-09-19 03:14:52', NULL, NULL),
(53, 2166, 'fdfhajkdfhka', 'Grandparent', '09876489900', 'gggge@gmail.com', 'dedjageflhgyeiwgvcbwnevb', 1, 1, '2026-09-19 03:14:52', '2026-09-19 03:14:52', NULL, NULL),
(54, 2167, 'GAGAGA', 'Father', '9472914781', 'DJSAHJDGSA@gmail.com', 'dgjsagdsagdg', 0, 1, '2026-09-19 10:05:47', '2026-09-19 10:05:47', NULL, NULL),
(55, 2167, 'dhsadjkgsa', 'Mother', '8732749126', 'gdsahgdh@gmail.com', 'djsagdjagsd', 0, 1, '2026-09-19 10:05:47', '2026-09-19 10:05:47', NULL, NULL),
(56, 2167, 'dsadsad', 'Guardian', '3214134134', 'dsadsad@gmail.com', 'dasdhsavdjdvjasdjvdjsdavh', 1, 1, '2026-09-19 10:05:47', '2026-09-19 10:05:47', NULL, NULL),
(57, 2168, 'micky reyes', 'Father', '09741727471', 'micky@gmail.com', 'hdjgasdjgsakdgkjsas', 0, 1, '2026-09-19 10:17:38', '2026-09-19 10:17:38', NULL, NULL),
(58, 2168, 'mouse reyes', 'Mother', '0947324773472', 'mouse@gmail.com', 'hdjsahdjasjd', 0, 1, '2026-09-19 10:17:38', '2026-09-19 10:17:38', NULL, NULL),
(59, 2168, 'mouse reyes', 'Guardian', '097217471774', 'mouse@gmail.com', 'kantot hahaa', 1, 1, '2026-09-19 10:17:38', '2026-09-19 10:17:38', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_health_profiles`
--

CREATE TABLE `reg_health_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `height` varchar(30) DEFAULT NULL,
  `weight` varchar(30) DEFAULT NULL,
  `allergies` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_health_records`
--

CREATE TABLE `reg_health_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `checkup_date` date NOT NULL,
  `complaints` varchar(500) DEFAULT NULL,
  `findings` varchar(500) DEFAULT NULL,
  `vital_signs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vital_signs`)),
  `immunization` varchar(255) DEFAULT NULL,
  `medication` varchar(255) DEFAULT NULL,
  `physician_nurse` varchar(150) DEFAULT NULL,
  `file_id` int(10) UNSIGNED DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_health_records`
--

INSERT INTO `reg_health_records` (`id`, `student_id`, `checkup_date`, `complaints`, `findings`, `vital_signs`, `immunization`, `medication`, `physician_nurse`, `file_id`, `notes`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(21, 2141, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":69}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(22, 2141, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":69}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(23, 2142, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":77}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(24, 2142, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":77}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(25, 2143, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":72}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(26, 2143, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":72}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(27, 2144, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":88}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(28, 2144, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":88}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(29, 2145, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":75}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(30, 2145, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":75}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(31, 2146, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":81}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(32, 2146, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":81}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(33, 2147, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":81}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(34, 2147, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":81}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(35, 2148, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":74}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(36, 2148, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":74}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(37, 2149, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":72}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(38, 2149, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":72}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(39, 2150, '2026-03-01', 'None', 'Healthy', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":95}', 'Up to date', 'None', 'Dr. Health Check', NULL, 'Annual physical examination', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL),
(40, 2150, '2026-05-29', 'Minor cold', 'Common cold, recovering well', '{\"bp\":\"120\\/80\",\"temp\":\"37.0\",\"pulse\":\"72\",\"weight\":95}', 'Up to date', 'Paracetamol as needed', 'Dr. Health Check', NULL, 'Follow-up visit', '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_masterlist_exports`
--

CREATE TABLE `reg_masterlist_exports` (
  `id` int(10) UNSIGNED NOT NULL,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`filters`)),
  `student_count` smallint(5) UNSIGNED DEFAULT 0,
  `export_type` enum('csv','pdf') NOT NULL,
  `exported_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_masterlist_exports`
--

INSERT INTO `reg_masterlist_exports` (`id`, `filters`, `student_count`, `export_type`, `exported_by`, `created_at`) VALUES
(1, '{\"school_year\":\"2026-2027\",\"semester\":\"1st Semester\",\"program\":\"Bachelor of Library Information Science\",\"year_section\":\"II-A\",\"status\":\"Active\",\"section_id\":39}', 32, 'csv', 455, '2026-08-28 12:26:46'),
(2, '{\"school_year\":\"\",\"semester\":\"\",\"program\":\"\",\"year_section\":\"\",\"status\":\"\",\"section_id\":0}', 10, 'csv', 455, '2026-08-30 00:02:54');

-- --------------------------------------------------------

--
-- Table structure for table `reg_persona_files`
--

CREATE TABLE `reg_persona_files` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `file_category` varchar(60) NOT NULL,
  `identity_doc_no` varchar(80) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `file_id` int(10) UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reg_status_history`
--

CREATE TABLE `reg_status_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `changed_from` varchar(50) NOT NULL,
  `changed_to` varchar(50) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reason_category` varchar(100) DEFAULT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `effective_term` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_status_history`
--

INSERT INTO `reg_status_history` (`id`, `student_id`, `changed_from`, `changed_to`, `reason`, `reason_category`, `ref_no`, `effective_term`, `notes`, `changed_by`, `changed_at`) VALUES
(1, 2166, 'Verified', 'Active', 'Activated by Registrar after document validation', NULL, NULL, NULL, 'First time activation from pre-enrollment.', 604, '2026-09-19 09:10:16'),
(2, 2143, 'Active', 'Leave of Absence', 'Medical recuperation leave supported by physician certificate', 'Health / Medical Incapacity', 'LOA-2024-0042', 'AY 2024-2025, 1st Sem', 'Maximum allowable leave approved up to end of 2nd semester.', 604, '2026-09-27 21:55:27'),
(3, 2145, 'Active', 'Irregular', 'Deficient in CC102 prerequisite; enrolled in reduced study plan', 'Academic Deficiency / Prerequisite Failure', 'REG-IRR-2024-19', 'AY 2024-2025, 1st Sem', 'Advised by Academic Program Head to take remedial load.', 604, '2026-10-28 21:55:27'),
(4, 2148, 'Active', 'Transferred', 'Family permanent relocation to provincial address; transferring to State University', 'Transfer to Another HEI (Honorable Dismissal)', 'HD-2024-0081', 'AY 2024-2025, 1st Sem', 'Certificate of Honorable Dismissal and Official Transcript (Copy for Transfer) issued.', 604, '2026-11-06 21:55:27'),
(5, 2141, 'Verified', 'Active', 'Official enrollment validation and document verification complete', 'Return from Approved Leave (Re-admission)', 'REG-ENR-2024-001', 'AY 2024-2025, 1st Sem', 'Regular matriculation validated.', 604, '2026-09-12 21:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `reg_students`
--

CREATE TABLE `reg_students` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_number` varchar(40) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email_address` varchar(150) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `nationality` varchar(80) DEFAULT NULL,
  `program_course` varchar(200) DEFAULT NULL,
  `year_section` varchar(100) DEFAULT NULL,
  `college_department` varchar(200) DEFAULT NULL,
  `birth_cert_no` varchar(80) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Active',
  `photo_file_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_students`
--

INSERT INTO `reg_students` (`id`, `student_number`, `user_id`, `first_name`, `middle_name`, `last_name`, `email_address`, `contact_number`, `suffix`, `date_of_birth`, `gender`, `nationality`, `program_course`, `year_section`, `college_department`, `birth_cert_no`, `enrollment_date`, `status`, `photo_file_id`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(2141, '2024001', NULL, 'Juan', 'D', 'Santos', NULL, NULL, NULL, '2000-05-15', 'Male', 'Filipino', 'Bachelor of Science in Information Technology (BSIT)', 'IV-A', 'College of Computer Studies', '123456789101112', '2020-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2142, '2024002', NULL, 'Maria', 'G', 'Cruz', NULL, NULL, NULL, '2001-03-22', 'Female', 'Filipino', 'Bachelor of Science in Computer Engineering (BSCpE)', 'III-B', 'College of Engineering', '234567890121314', '2021-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2143, '2024003', NULL, 'Pedro', 'R', 'Reyes', NULL, NULL, 'Jr.', '2001-07-10', 'Male', 'Filipino', 'Bachelor of Science in Criminology (BSCrim)', 'II-A', 'College of Criminal Justice', '345678901213141', '2022-06-01', 'Leave of Absence', NULL, '2026-08-29 22:57:35', '2026-11-11 21:55:27', NULL, NULL),
(2144, '2024004', NULL, 'Rosa', 'L', 'Lopez', NULL, NULL, NULL, '2002-01-18', 'Female', 'Filipino', 'Bachelor of Science in Business Administration (BSBA)', 'II-C', 'College of Business & Accountancy', '456789012131415', '2022-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2145, '2024005', NULL, 'Carlos', 'M', 'Mendoza', NULL, NULL, NULL, '2000-11-25', 'Male', 'Filipino', 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'IV-B', 'College of Business & Accountancy', '567890121314151', '2020-06-01', 'Irregular', NULL, '2026-08-29 22:57:35', '2026-11-11 21:55:27', NULL, NULL),
(2146, '2024006', NULL, 'Angela', 'P', 'Pascual', NULL, NULL, NULL, '2001-09-30', 'Female', 'Filipino', 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'III-A', 'College of Business & Accountancy', '678901213141516', '2021-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2147, '2024007', NULL, 'Manuel', 'T', 'Torres', NULL, NULL, 'Sr.', '2000-02-14', 'Male', 'Filipino', 'Bachelor of Science in Office Administration (BSOA)', 'IV-C', 'College of Business & Accountancy', '789012131415161', '2020-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2148, '2024008', NULL, 'Lucia', 'F', 'Fernandez', NULL, NULL, NULL, '2002-08-05', 'Female', 'Filipino', 'Bachelor of Science in Hospitality Management (BSHM)', 'I-A', 'College of Hospitality & Tourism', '890121314151617', '2023-06-01', 'Transferred', NULL, '2026-08-29 22:57:35', '2026-11-11 21:55:27', NULL, NULL),
(2149, '2024009', NULL, 'Roberto', 'G', 'Garcia', NULL, NULL, NULL, '2001-04-20', 'Male', 'Filipino', 'Bachelor of Science in Tourism Management (BSTM)', 'III-C', 'College of Hospitality & Tourism', '901213141516171', '2021-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2150, '2024010', NULL, 'Sofia', 'A', 'Alvarez', NULL, NULL, NULL, '2002-06-12', 'Female', 'Filipino', 'Bachelor of Science in Psychology (BSP)', 'I-B', 'College of Arts and Sciences', '012131415161718', '2023-06-01', 'Active', NULL, '2026-08-29 22:57:35', '2026-09-23 19:51:12', NULL, NULL),
(2152, 'dBtixZ7xpnGhd', NULL, 'Ban Reii', NULL, 'Basilan', NULL, NULL, NULL, '2003-09-17', 'Male', NULL, 'Bachelor of Library and Information Science (BLIS)', 'First Year', 'College of Arts and Sciences', NULL, NULL, 'Verified', NULL, '2026-09-08 16:02:48', '2026-09-23 19:51:12', NULL, NULL),
(2153, '20262420', NULL, 'Ror', 'Bas', 'Ban', NULL, NULL, NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Elementary Education (BEEd)', 'First Year', 'College of Education', NULL, NULL, 'Verified', NULL, '2026-09-08 17:03:06', '2026-09-23 19:51:12', NULL, NULL),
(2154, '20269741', NULL, 'Rorss', 'Bass', 'Banss', 'lixx1920@gmail.com', '123123123123', NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Secondary Education (BSEd)', 'First Year', 'College of Education', NULL, NULL, 'Verified', NULL, '2026-09-08 17:09:59', '2026-09-23 19:51:12', NULL, NULL),
(2155, '20268873', NULL, 'Rasdhashd', 'Pasdhasd', 'PENG', 'lixx1920@gmail.com', '123123123123', NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Technology and Livelihood Education (BTLED)', 'First Year', 'College of Education', NULL, NULL, 'Active', NULL, '2026-09-08 17:56:00', '2026-09-23 19:51:12', NULL, 549),
(2156, '20268269', NULL, 'Bingee', 'Swpasd', 'LONG', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Technical-Vocational Teacher Education (BTVTED)', 'First Year', 'College of Education', NULL, NULL, 'Verified', NULL, '2026-09-08 20:09:29', '2026-09-23 19:51:12', NULL, NULL),
(2157, '20265788', NULL, 'PECKK', 'Swpasd', 'PINK', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Female', NULL, 'Bachelor of Physical Education (BPEd)', 'First Year', 'College of Education', NULL, NULL, 'Active', NULL, '2026-09-08 20:23:28', '2026-09-23 19:51:12', NULL, 549),
(2158, '20269613', NULL, 'Rochelle', 'Basilabn', 'Banz', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Female', NULL, 'Bachelor of Science in Information Technology (BSIT)', 'First Year', 'College of Computer Studies', NULL, NULL, 'Active', NULL, '2026-09-08 22:18:46', '2026-09-23 19:51:12', NULL, 549),
(2159, '20268140', NULL, 'Princess', 'Gallardo', 'Bantonare', 'boyrexar02@gmail.com', '123123123123', NULL, '2003-09-08', 'Female', NULL, 'Bachelor of Science in Computer Engineering (BSCpE)', 'First Year', 'College of Engineering', NULL, NULL, 'Active', NULL, '2026-09-08 22:57:48', '2026-09-23 19:51:12', NULL, 549),
(2160, '99998', NULL, 'Test', NULL, 'Guardian2', NULL, NULL, NULL, NULL, NULL, NULL, 'Bachelor of Science in Criminology (BSCrim)', NULL, 'College of Criminal Justice', NULL, NULL, 'Active', NULL, '2026-09-08 23:24:44', '2026-09-23 19:51:12', NULL, NULL),
(2161, '20261602', NULL, 'Princessss', 'Alumba', 'Bantonare', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Female', NULL, 'Bachelor of Science in Business Administration (BSBA)', 'First Year', 'College of Business & Accountancy', NULL, NULL, 'Active', NULL, '2026-09-08 23:27:42', '2026-09-23 19:51:12', NULL, 549),
(2162, '20264911', NULL, 'Rochelle', 'weqweqw', 'qwe', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'First Year', 'College of Business & Accountancy', NULL, NULL, 'Active', NULL, '2026-09-09 00:43:47', '2026-09-23 19:51:12', NULL, NULL),
(2163, '20266801', NULL, 'asdasd', 'asd', 'asd', 'reijibanz@gmail.com', '123123123123', NULL, '2003-09-08', 'Male', NULL, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'First Year', 'College of Business & Accountancy', NULL, NULL, 'Verified', NULL, '2026-09-09 00:49:01', '2026-09-23 19:51:12', NULL, NULL),
(2166, '20264728', NULL, 'Dengfgf', 'Relldfdfdf', 'Silvadfdfd', 'denrellsilva08@gmail.com', '09876543986', NULL, '2005-09-09', 'Male', NULL, 'Bachelor of Science in Office Administration (BSOA)', 'Second Year', 'College of Business & Accountancy', NULL, NULL, 'Active', NULL, '2026-09-19 03:14:52', '2026-09-23 19:51:12', NULL, 604),
(2167, '20267047', NULL, 'Den', 'Rell', 'Silva', 'denrellsilva08@gmail.com', '09876543986', NULL, '0000-00-00', 'Male', NULL, 'Bachelor of Science in Hospitality Management (BSHM)', 'Second Year', 'College of Hospitality & Tourism', NULL, NULL, 'Verified', NULL, '2026-09-19 10:05:47', '2026-09-23 19:51:12', NULL, NULL),
(2168, '20263534', NULL, 'mike', 'hernandez', 'reyes', 'miketangonan14@gmail.com', '09876543986', NULL, '2001-02-22', 'Male', NULL, 'Bachelor of Science in Tourism Management (BSTM)', 'Third Year', 'College of Hospitality & Tourism', NULL, NULL, 'Verified', NULL, '2026-09-19 10:17:38', '2026-09-23 19:51:12', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_student_ids`
--

CREATE TABLE `reg_student_ids` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `batch_no` varchar(40) DEFAULT NULL,
  `template_name` varchar(100) NOT NULL DEFAULT 'standard',
  `photo_file_id` int(10) UNSIGNED DEFAULT NULL,
  `id_number` varchar(40) DEFAULT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'Draft',
  `printed_at` datetime DEFAULT NULL,
  `released_at` datetime DEFAULT NULL,
  `released_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_student_ids`
--

INSERT INTO `reg_student_ids` (`id`, `student_id`, `batch_no`, `template_name`, `photo_file_id`, `id_number`, `status`, `printed_at`, `released_at`, `released_by`, `notes`, `created_at`, `updated_at`, `created_by`) VALUES
(11, 2141, 'BATCH-2024-001', 'standard', NULL, 'ID202400001', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(12, 2142, 'BATCH-2024-001', 'standard', NULL, 'ID202400002', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(13, 2143, 'BATCH-2024-001', 'standard', NULL, 'ID202400003', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(14, 2144, 'BATCH-2024-001', 'standard', NULL, 'ID202400004', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(15, 2145, 'BATCH-2024-001', 'standard', NULL, 'ID202400005', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(16, 2146, 'BATCH-2024-001', 'standard', NULL, 'ID202400006', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(17, 2147, 'BATCH-2024-001', 'standard', NULL, 'ID202400007', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(18, 2148, 'BATCH-2024-001', 'standard', NULL, 'ID202400008', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(19, 2149, 'BATCH-2024-001', 'standard', NULL, 'ID202400009', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL),
(20, 2150, 'BATCH-2024-001', 'standard', NULL, 'ID202400010', 'Printed', NULL, NULL, NULL, NULL, '2026-08-29 22:57:35', '2026-08-29 22:57:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reg_student_statuses`
--

CREATE TABLE `reg_student_statuses` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(40) NOT NULL,
  `effective_date` date DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_student_statuses`
--

INSERT INTO `reg_student_statuses` (`id`, `student_id`, `status`, `effective_date`, `remarks`, `changed_by`, `created_at`) VALUES
(11, 2141, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(12, 2142, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(13, 2143, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(14, 2144, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(15, 2145, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(16, 2146, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(17, 2147, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(18, 2148, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(19, 2149, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(20, 2150, 'Active', '2026-08-29', 'Initial enrollment', 1, '2026-08-29 22:57:35'),
(21, 2155, 'Active', '2026-09-08', 'Activated by Registrar after document validation', 549, '2026-09-08 19:48:33'),
(22, 2157, 'Active', '2026-09-08', 'Activated by Registrar after document validation', 549, '2026-09-08 20:25:36'),
(23, 2158, 'Active', '2026-09-08', 'Activated by Registrar after document validation', 549, '2026-09-08 22:20:39'),
(24, 2159, 'Active', '2026-09-08', 'Activated by Registrar after document validation', 549, '2026-09-08 22:59:47'),
(25, 2161, 'Active', '2026-09-08', 'Activated by Registrar after document validation', 549, '2026-09-08 23:29:39');

-- --------------------------------------------------------

--
-- Table structure for table `reg_verification_codes`
--

CREATE TABLE `reg_verification_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `doc_hash` char(64) NOT NULL,
  `verification_code` varchar(20) NOT NULL,
  `signed_payload` longtext DEFAULT NULL,
  `signature` longtext DEFAULT NULL,
  `algorithm` varchar(40) NOT NULL DEFAULT 'RSA-SHA256',
  `doc_type` varchar(60) DEFAULT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `verified_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reg_verification_codes`
--

INSERT INTO `reg_verification_codes` (`id`, `doc_hash`, `verification_code`, `signed_payload`, `signature`, `algorithm`, `doc_type`, `student_id`, `generated_at`, `verified_count`, `last_verified_at`, `created_at`) VALUES
(1, '3e7d5010bf31f51f6dd635b731e2bf6d0e474b1a0b40f7be5a4fa28670589bc7', 'C5A8EF39F747', 'VER:C5A8EF39F747|DOC:COE|STU:2141|DATE:2026-08-31 00:38:30', 'KNCXXFJ5zVCsRAEUM79/SYvI/lBvNdWKi22Hguaht/EmL0D42u0qV+mmxc7wM8/9zSFXycPgyQv1MbHKBjQPK6Ib7PqR5oUG6zZnURU4Dn/Xp9QE3pHdtYP1TtzLm3X5siBihxkks/395+z7R1yHRWnFtByuR049rE8ejFDMRlc0f6jxrAVra9jmAhBhl9qXrTrV3GcAN73k9YfJk5O/jSaA4l4JZVwk0nKPOya1HUUgop1U0M+4NArzr2x5lreiItn782KAQQn6qlNeX9+71HAMSq/rQIlec0yxdqLEZ2YqxBjHeVEjkMH55fF9g3eKUVo8ya6PC/AqnoN7gyiy1w==', 'RSA-SHA256', 'COE', 2141, '2026-08-31 00:38:37', 0, NULL, '2026-08-31 00:38:37'),
(2, 'c6d6713a69af9f352a9646e0fa0819d3b08c411a5c2f42b65aaed0d683731ed0', '95D40899202D', 'VER:95D40899202D|DOC:COE|STU:2141|DATE:2026-09-04 12:06:29', 'WfRhHieZ4n5bzh5RnHqJ7uB3fYo8x8HrgwzRc0ObZk7X/wIw1HPKYLDnpe6mBXSIejfKeBHnCGnGn//+r8rosvFsLO5cKWjW9SCXCVS3hwgpJ5246rVk/FhEAcjtGR32Bhf97gtYBVFiEUjcrz+dku4oKbToONTD8O4tEf3nruhb1I73rzkbr5+GmpVtLFnjHippk4IucrrE3VjDtiSOyyxg+Tig+yZAJZAjTrxoIYVDkODhg5V3JxKyS9DL2GZU0NCavVPVG4my7tvvmjrn3sjroa/vM1f2SAvKZeThnaiB6kLNHyKf4wlh66hBHO1J8CoQfBj+6q5OeDlAwhv6Qw==', 'RSA-SHA256', 'COE', 2141, '2026-09-04 12:06:36', 0, NULL, '2026-09-04 12:06:36'),
(3, 'b23822aa4600621d845125cd7dd015d6b4e1aea19a6cfbb22f8a7d2328a430ba', '1F1B74BF033B', 'VER:1F1B74BF033B|DOC:COE|STU:2141|DATE:2026-09-04 14:44:22', 'ZRI3X2G64Vx0VL4AI3xutoyjO7Pr13H+X5CtcDbrkeJmMR+jaaNwD4dlN1MYt/gY0Of1Xm1CY8IkmYuwKP6UWB1vWJkURD2GQtg4QkIbBZu1eOVPuERv6mOCqAWsQJLkdBNVaobJgxf83+t3H+HgFa6Qtfuyd2vH/RnhqUuZl7J2UgOGYOcLkv7G+lEeI0hZkN721XarzxijtrskSeZULyz/vwJ72ugLWUyDNKSp+xn441TpNv1pykWxd//FO/CX0hOoipd0oyXSkZ6qxDjGkx1NTMbD4AEnbA9B211fLS3S0XCUW2BA2mcdcCTOPaL75DqIFPtT5ykz/nzBGlz81g==', 'RSA-SHA256', 'COE', 2141, '2026-09-04 14:44:24', 0, NULL, '2026-09-04 14:44:24'),
(4, '5f680c965a0aadad68f5e194012e35e9921c002181580816a0426760bb65ec9e', '633F0563A1F7', 'VER:633F0563A1F7|DOC:COE|STU:2141|DATE:2026-09-05 13:08:41', 'PSPz05c/cb9yjkIRE1iW6FwRbvCfBHJHNQQkYVKyJWkMQwbIfSaGg/RN62lBDMXfU66wfG4Us/+PVn1PL1NMwnZNS542qDml4iLPeHON2yQYIrXdm1owFSGWJN65rZLUfG9P1QK2u9ovfKWGO0KA3G/Ov4a/+TMhzDvFKU3K+Yd5wsF6KLwGKh5Zlw5q8t36XsT/JW1j8TcXsXNlemPjoPZwInoONuLBX1kIE+wGxfZz+WKWWD+dSmUn57IihfVV2ETlZ2oOwEKD8soqFnivtmJjgCEl7RNRxjwWpsHzHwHnkdD36lvkjsxGq/s5VUPCTtueqYWa9tpsso89h9ysZg==', 'RSA-SHA256', 'COE', 2141, '2026-09-05 13:08:47', 0, NULL, '2026-09-05 13:08:47'),
(5, '93e7c6a7d0f1c96b1ec74df0c9f04f90012cdd016600c3f69c0373d2646e7753', 'ECB243B85B83', 'VER:ECB243B85B83|DOC:COE|STU:2141|DATE:2026-09-05 16:51:13', 'qvXxh2a37XKuZ0BeW/zCfxqixjyBbuO3jO/0qYJRvBJG8qJV4O/Tz0l4C1Z+xK7DebJ0vKClrEal1QuwlsctMDr/5w1nv6PdwGP/iAQp+4SdGyHKcZwaNcwcx12VPyAc/tXJuB3GkHYR/JIgAd6Hv9AYTpTjuWab2YzGx4uj7ajRos4b7z0Hy5aKec18+QfqSkq+1vWpyblTSIHKiR1Hr8zjvqMGaTXiyon7YawUEAHnKWODKevLIa4qIaAAbeVHPAgHY0I30rPzEt2u1mBkJW0haG+ePXJAhMnvXkWQu5y2lUgFofyHW0WOYVseaWi9z8jDctT0r7ltsH1XW81iBQ==', 'RSA-SHA256', 'COE', 2141, '2026-09-05 16:51:26', 0, NULL, '2026-09-05 16:51:26'),
(6, '70c05a683cf6304424b0edff82a06e734a637e48b83323da5ac67dd9784fd4cb', '149CD553C944', 'VER:149CD553C944|DOC:COE|STU:2141|DATE:2026-09-05 17:22:24', 'Fk0ea6Y6dmd+LP6kugjCn8ibX4SMcsLvlCZzSu3Y/eYB5aEkvs3zJf3yd+iIaYmE4I1nK4LGQyBnd2c5IdSLaK0o1vxBMWhLPGLIwthary24PY4dwL6pAvjqt5boqJGZBRdWxMvvhyz1vehkN3KReDk2i8nwpag/Tt+LPWNSd/iLxZtelPmBlQwfRJJF7K7B+CL+Hcyn4HzexFxfLZTMDX2caR3x40N31s3S8QRnORFZenurBVyVml2g9DStfmmi8Du7nCXX/MIh5Y/s3qP+tuJUfOmgoVjI5fMoVIbtESTxbxsH2A8Sf7eEXt98ShtbGiY5aDONiEG4phiE31WuNg==', 'RSA-SHA256', 'COE', 2141, '2026-09-05 17:22:30', 0, NULL, '2026-09-05 17:22:30'),
(7, '9a5e3d9237e350d3e67cf6461bc45fb854ccc4d44a9a0e99b09cb5ed408425a4', '2C7E6E71FDCC', 'VER:2C7E6E71FDCC|DOC:COE|STU:2141|DATE:2026-09-05 17:26:59', 'cAAbWWSuQykqOSNrP61U52uJ5z9k7SfjYRVnVP6YiPtDqhoHHsY6r2Dm/DwrJaoHYOmOcWaVt5xrzu6007U5JbR6rtF0wJtwCbg0Q6gpGl2FV1xD33scdsXn2XgGHH7khhFfuJb1wPjOaOM5pyJXMuB1RyI65xt55EEd3enorRf+MWbyVH/8H6LVmBos/q5L64iB8q5PSWVGTQlCEinAC0UrGE2n+fetmdS2jf8uJFZ6tF5eF278B7PfgCPI4dvrd6mkBOc2XLKmS2N1iTMPn2tnC4j9yGtz2DhRSD2a4B7UwW8ytBYU40uYcOczPQq6I4BSLSAdpp9l4P/kZQH6pA==', 'RSA-SHA256', 'COE', 2141, '2026-09-05 17:27:03', 0, NULL, '2026-09-05 17:27:03'),
(8, '2fdd651ca66a42f6a17521cca45a56e6a4c446cdc48e3807f2114a3c05c4c76d', '306B86C6F427', 'VER:306B86C6F427|DOC:COE|STU:2166|DATE:2026-11-19 19:32:33', 'HO02eH64jD7L6wwCJxbQFFgjQsL6Hey/X9bHV0C2AQEoGhkZOSyXXDLmH7EATwlu46szfXPKTgnLr3EMsxNC32dW03Lz64KPBRBxMR8evL11zgw6xzbElDasj0FyLdrVRM8Gl55GyswORnm5p+MBaKlUWwWe5Mn3XsZPN5Ztt28T4LxC+3Gd8X2qAiSy5KNIEFw+Zv7zUO0UPie4v0mAaIdbra0yf+d6PsHawLH4mFXn4Vrx02lX4xY4Tp19Q/LMLZQZYFKtNojzuoxRthavec1g1ICrIee59fHgKQA9gQ+HcL9f0I70Bl7Ndp6Z8laEqJdodENc4iW2hH4o9KGPng==', 'RSA-SHA256', 'COE', 2166, '2026-11-19 19:32:34', 0, NULL, '2026-11-19 19:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `role_key` varchar(40) NOT NULL,
  `label` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_key`, `label`, `description`, `is_system`, `created_at`) VALUES
(1, 'admin', 'Super Admin', 'Legacy super admin access', 1, '2026-07-22 22:24:44'),
(2, 'registrar', 'Registrar', 'Enrollment, records, scheduling', 1, '2026-07-22 22:24:44'),
(3, 'finance', 'Finance', 'Payments and receivables', 1, '2026-07-22 22:24:44'),
(4, 'hr', 'Dean', 'Dean and faculty processes', 1, '2026-07-22 22:24:44'),
(5, 'it_office', 'IT Office', 'LMS and IT modules', 1, '2026-07-22 22:24:44'),
(6, 'osa', 'OSA', 'Student affairs / co-curricular', 1, '2026-07-22 22:24:44'),
(7, 'qa', 'QA Office', 'Accreditation and quality', 1, '2026-07-22 22:24:44'),
(8, 'crad_officer', 'CRAD Officer', 'Research and development', 1, '2026-07-22 22:24:44'),
(9, 'student', 'Student', 'Student portal only', 1, '2026-07-22 22:24:44'),
(10, 'superadmin', 'Super Admin', 'Full system access', 1, '2026-08-08 17:25:19'),
(11, 'admission', 'Admission', 'Admission office access', 1, '2026-08-08 17:25:19'),
(56, 'research_coordinator', 'Research Coordinator', 'Research coordination access', 1, '2026-08-08 18:13:51'),
(102, 'adviser', 'Adviser', 'Research adviser faculty account', 1, '2026-08-08 21:35:14'),
(213, 'research_director', 'Research Director', 'Research defense scheduling director account', 1, '2026-08-09 19:31:14'),
(384, 'research_grant', 'CRAD Officer', 'Research grant management access', 1, '2026-08-10 20:01:49'),
(893, 'sms_admin', 'Admin', 'General administrator account', 1, '2026-09-03 19:05:30'),
(894, 'department_chair', 'Department Chair', 'Grant approval department chair sign-off', 1, '2026-09-03 19:05:30'),
(895, 'research_office', 'Research Office', 'Grant approval research office sign-off', 1, '2026-09-03 19:05:30'),
(896, 'vpaa', 'VPAA', 'Grant approval VPAA sign-off', 1, '2026-09-03 19:05:30'),
(897, 'grammarian', 'Grammarian', 'Research grammar and manuscript evaluation account', 1, '2026-09-03 19:05:30'),
(898, 'panel', 'Panel Member', 'Research defense panel account', 1, '2026-09-03 19:05:30'),
(899, 'review_committee', 'Review Committee', 'Grant proposal review and rubric evaluation', 1, '2026-09-03 19:05:30'),
(908, 'pre-enrollee', 'Pre-Enrollee', 'Temporary account for online document upload', 1, '2026-09-08 16:53:27');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_key` varchar(40) NOT NULL,
  `module_key` varchar(60) NOT NULL,
  `granted` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_key`, `module_key`, `granted`, `updated_at`) VALUES
(194, 'superadmin', 'user-management', 1, '2026-08-08 22:07:16'),
(195, 'superadmin', 'student_portal', 0, '2026-08-08 22:07:21'),
(196, 'admission', 'enrollment', 1, '2026-08-08 22:07:16'),
(197, 'registrar', 'registrar', 1, '2026-08-08 22:07:16'),
(198, 'registrar', 'curriculum', 1, '2026-08-08 22:07:16'),
(199, 'registrar', 'scheduling', 1, '2026-08-08 22:07:16'),
(200, 'finance', 'payment', 1, '2026-08-08 22:07:16'),
(201, 'hr', 'faculty', 1, '2026-08-08 22:07:16'),
(202, 'adviser', 'faculty', 1, '2026-08-08 22:07:16'),
(204, 'it_office', 'lms', 1, '2026-08-08 22:07:16'),
(205, 'osa', 'cocurricular', 1, '2026-08-08 22:07:16'),
(206, 'qa', 'accreditation', 1, '2026-08-08 22:07:16'),
(207, 'crad_officer', 'crad', 1, '2026-08-08 22:07:16'),
(208, 'research_coordinator', 'crad', 1, '2026-08-08 22:07:16'),
(209, 'student', 'student_portal', 1, '2026-08-08 22:07:16'),
(247, 'research_director', 'faculty', 1, '2026-08-09 19:31:14'),
(353, 'research_grant', 'crad_grant', 1, '2026-08-10 20:01:49'),
(686, 'department_chair', 'crad', 1, '2026-09-03 19:05:30'),
(687, 'research_office', 'crad', 1, '2026-09-03 19:05:30'),
(688, 'vpaa', 'accreditation', 1, '2026-09-03 19:05:30'),
(689, 'sms_admin', 'enrollment', 1, '2026-09-03 19:05:30'),
(690, 'sms_admin', 'registrar', 1, '2026-09-03 19:05:30'),
(691, 'sms_admin', 'curriculum', 1, '2026-09-03 19:05:30'),
(692, 'sms_admin', 'accreditation', 1, '2026-09-03 19:05:30'),
(693, 'sms_admin', 'payment', 1, '2026-09-03 19:05:30'),
(694, 'sms_admin', 'faculty', 1, '2026-09-03 19:05:30'),
(695, 'sms_admin', 'scheduling', 1, '2026-09-03 19:05:30'),
(696, 'sms_admin', 'cocurricular', 1, '2026-09-03 19:05:30'),
(697, 'sms_admin', 'lms', 1, '2026-09-03 19:05:30'),
(698, 'sms_admin', 'crad', 1, '2026-09-03 19:05:30'),
(701, 'grammarian', 'faculty', 1, '2026-09-03 19:05:31'),
(702, 'panel', 'faculty', 1, '2026-09-03 19:05:31'),
(704, 'review_committee', 'crad_grant', 1, '2026-09-03 19:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `sch_sections`
--

CREATE TABLE `sch_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `program_course` varchar(200) NOT NULL,
  `year_section` varchar(100) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `semester` varchar(30) NOT NULL DEFAULT '1st Semester',
  `section_code` varchar(60) DEFAULT NULL,
  `adviser_name` varchar(150) DEFAULT NULL,
  `max_students` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sch_sections`
--

INSERT INTO `sch_sections` (`id`, `program_course`, `year_section`, `school_year`, `semester`, `section_code`, `adviser_name`, `max_students`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'Bachelor of Science in Information Technology (BSIT)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(2, 'Bachelor of Science in Business Administration (BSBA)', 'III-B', '2026-2027', '1st Semester', 'III-B', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(3, 'Bachelor of Technical-Vocational Teacher Education (BTVTED)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(4, 'Bachelor of Secondary Education (BSEd)', 'II-C', '2026-2027', '1st Semester', 'II-C', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(5, 'Bachelor of Science in Computer Engineering (BSCpE)', 'IV-B', '2026-2027', '1st Semester', 'IV-B', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(6, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(7, 'Bachelor of Science in Computer Engineering (BSCpE)', 'IV-C', '2026-2027', '1st Semester', 'IV-C', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(8, 'Bachelor of Science in Psychology (BSP)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(9, 'Bachelor of Science in Hospitality Management (BSHM)', 'III-C', '2026-2027', '1st Semester', 'III-C', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(10, 'Bachelor of Science in Psychology (BSP)', 'I-B', '2026-2027', '1st Semester', 'I-B', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(11, 'Bachelor of Science in Information Technology (BSIT)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(12, 'Bachelor of Science in Information Technology (BSIT)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(13, 'Bachelor of Science in Information Technology (BSIT)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(14, 'Bachelor of Science in Hospitality Management (BSHM)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:49', '2026-09-23 19:51:12', NULL),
(15, 'Bachelor of Science in Hospitality Management (BSHM)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(16, 'Bachelor of Science in Hospitality Management (BSHM)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(17, 'Bachelor of Science in Hospitality Management (BSHM)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(18, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(19, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(20, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(21, 'Bachelor of Science in Accounting Information Systems (BSAIS)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:50', '2026-09-23 19:51:12', NULL),
(22, 'Bachelor of Science in Tourism Management (BSTM)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(23, 'Bachelor of Science in Tourism Management (BSTM)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(24, 'Bachelor of Science in Tourism Management (BSTM)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(25, 'Bachelor of Science in Tourism Management (BSTM)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(26, 'Bachelor of Science in Office Administration (BSOA)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(27, 'Bachelor of Science in Office Administration (BSOA)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(28, 'Bachelor of Science in Office Administration (BSOA)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:51', '2026-09-23 19:51:12', NULL),
(29, 'Bachelor of Science in Office Administration (BSOA)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(30, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(31, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(32, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(33, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(34, 'Bachelor of Science in Business Administration (BSBA)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(35, 'Bachelor of Science in Business Administration (BSBA)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:52', '2026-09-23 19:51:12', NULL),
(36, 'Bachelor of Science in Business Administration (BSBA)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:53', '2026-09-23 19:51:12', NULL),
(37, 'Bachelor of Science in Business Administration (BSBA)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:53', '2026-09-23 19:51:12', NULL),
(38, 'Bachelor of Library and Information Science (BLIS)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:53', '2026-09-23 19:51:12', NULL),
(39, 'Bachelor of Library and Information Science (BLIS)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:53', '2026-09-23 19:51:12', NULL),
(40, 'Bachelor of Library and Information Science (BLIS)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:53', '2026-09-23 19:51:12', NULL),
(41, 'Bachelor of Library and Information Science (BLIS)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:54', '2026-09-23 19:51:12', NULL),
(42, 'Bachelor of Science in Computer Engineering (BSCpE)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:54', '2026-09-23 19:51:12', NULL),
(43, 'Bachelor of Science in Computer Engineering (BSCpE)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:54', '2026-09-23 19:51:12', NULL),
(44, 'Bachelor of Science in Computer Engineering (BSCpE)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:54', '2026-09-23 19:51:12', NULL),
(45, 'Bachelor of Science in Computer Engineering (BSCpE)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:54', '2026-09-23 19:51:12', NULL),
(46, 'Bachelor of Science in Psychology (BSP)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(47, 'Bachelor of Science in Psychology (BSP)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(48, 'Bachelor of Science in Psychology (BSP)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(49, 'Bachelor of Science in Criminology (BSCrim)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(50, 'Bachelor of Science in Criminology (BSCrim)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(51, 'Bachelor of Science in Criminology (BSCrim)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(52, 'Bachelor of Science in Criminology (BSCrim)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:55', '2026-09-23 19:51:12', NULL),
(53, 'Bachelor of Physical Education (BPEd)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(54, 'Bachelor of Physical Education (BPEd)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(55, 'Bachelor of Physical Education (BPEd)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(56, 'Bachelor of Physical Education (BPEd)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(57, 'Bachelor of Technology and Livelihood Education (BTLED)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(58, 'Bachelor of Technology and Livelihood Education (BTLED)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(59, 'Bachelor of Technology and Livelihood Education (BTLED)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:56', '2026-09-23 19:51:12', NULL),
(60, 'Bachelor of Technology and Livelihood Education (BTLED)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(61, 'Bachelor of Elementary Education (BEEd)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(62, 'Bachelor of Elementary Education (BEEd)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(63, 'Bachelor of Elementary Education (BEEd)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(64, 'Bachelor of Elementary Education (BEEd)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(65, 'Bachelor of Secondary Education (BSEd)', 'I-A', '2026-2027', '1st Semester', 'I-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(66, 'Bachelor of Secondary Education (BSEd)', 'II-A', '2026-2027', '1st Semester', 'II-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(67, 'Bachelor of Secondary Education (BSEd)', 'III-A', '2026-2027', '1st Semester', 'III-A', NULL, NULL, '2026-08-28 12:24:57', '2026-09-23 19:51:12', NULL),
(68, 'Bachelor of Secondary Education (BSEd)', 'IV-A', '2026-2027', '1st Semester', 'IV-A', NULL, NULL, '2026-08-28 12:24:58', '2026-09-23 19:51:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sch_section_assignments`
--

CREATE TABLE `sch_section_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Enrolled',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_otps`
--

CREATE TABLE `security_otps` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `purpose` varchar(40) NOT NULL,
  `code_hash` char(64) NOT NULL,
  `module_key` varchar(60) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_otps`
--

INSERT INTO `security_otps` (`id`, `user_id`, `purpose`, `code_hash`, `module_key`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 3, 'auth_setup', '00aa177502733dd1e947e9addf22e1e33ff0a061d3af840955c53e19f129d130', NULL, '2026-07-23 12:32:33', NULL, '2026-07-23 12:22:33'),
(2, 10, 'auth_setup', '434b2a7ce1742c5901ad141e3fd48d88a160776362d41a87fe120c61735dca25', NULL, '2026-07-23 12:41:18', '2026-07-23 12:31:35', '2026-07-23 12:31:18'),
(3, 1, 'login_2fa', 'fe8a2e43fdc5dfd231e4a5365fb4b97accb2d492d0618b8cb1239f2003384197', 'System', '2026-08-06 12:18:17', '2026-08-07 13:47:43', '2026-08-06 12:08:17'),
(4, 1, 'login_2fa', 'e5533c6584ffce50ff4b8a86ab22a27b4aec09511ca75cc45ae10f872e3b395f', 'System', '2026-08-06 12:35:41', '2026-08-07 13:47:43', '2026-08-06 12:25:41'),
(5, 1, 'login_2fa', '42b95c3f3a5bc304110b877053c1d5165d141cb2dd8aa4640078b64f9cd3f55a', 'System', '2026-08-06 12:37:17', '2026-08-07 13:47:43', '2026-08-06 12:27:17'),
(6, 1, 'login_2fa', 'b121a650c927ab1c64dbe5dcd7b1ee6cad3e31559cc17a9498e845353b39543c', 'System', '2026-08-06 12:49:29', '2026-08-07 13:47:43', '2026-08-06 12:39:29'),
(7, 1, 'login_2fa', 'd8a6f5265b58db60841f94a1910e75b34d95e7d484a4d684ba76c8a376ae8b74', 'System', '2026-08-06 12:51:04', '2026-08-07 13:47:43', '2026-08-06 12:41:04'),
(8, 1, 'login_2fa', '464da7d02deaa950b60fba29e5dd949ddb3d4d2108cc4d07dfd084fa472e4c9f', 'System', '2026-08-06 12:54:49', '2026-08-07 13:47:43', '2026-08-06 12:44:49'),
(9, 1, 'login_2fa', '52f02070f7f89d4a26b2de369b5d3f25b1d5ad68edca4505583d7dfae7629fe4', 'System', '2026-08-06 12:57:18', '2026-08-07 13:47:43', '2026-08-06 12:47:18'),
(10, 1, 'login_2fa', 'b901c750520b0d84eccbd2e6c98d5090d087acf2e44adff08fd38976d0db9412', 'System', '2026-08-06 13:19:23', '2026-08-07 13:47:43', '2026-08-06 13:09:23'),
(11, 1, 'password_change', 'a2b739a763e75c0377332f0cf70bc4d4b1fa1e6c777d960a592a3a9a072a3d40', 'admin-account', '2026-08-06 13:20:17', '2026-08-07 13:47:43', '2026-08-06 13:10:17'),
(12, 1, 'login_2fa', '96fb4145442779cd5534a2c540a7e9c6af8ec7d5d65d44f3a362bd466f75ea1f', 'System', '2026-08-06 13:23:25', '2026-08-07 13:47:43', '2026-08-06 13:13:25'),
(13, 1, 'login_2fa', '1a06a98bfb9fd1053e961bd6756f21edf14accb3eec42542a172d8ad2ae0aa66', 'System', '2026-08-06 13:25:26', '2026-08-07 13:47:43', '2026-08-06 13:15:26'),
(14, 1, 'login_2fa', '98e0b51ec04c9d63f34871bfe2e7a6b7284c174c2cdb537b35a4532978a1cff0', 'System', '2026-08-06 13:30:15', '2026-08-07 13:47:43', '2026-08-06 13:20:15'),
(15, 1, 'login_2fa', '6c828c6267e0b07f37a35b2000fc08831c60cece48c87fce4a60aabd2bbf634e', 'System', '2026-08-06 13:35:35', '2026-08-07 13:47:43', '2026-08-06 13:25:35'),
(16, 1, 'login_2fa', '78b7b05beda2d061eafc5c3dc98d62ab33427d54ccad3efbcc93c5686f68379c', 'System', '2026-08-06 13:41:19', '2026-08-07 13:47:43', '2026-08-06 13:31:19'),
(17, 3, 'login_2fa', 'f0bda89589cd1f9af75f462a57bbdf5d5baf94be8e6553c731c314ca8eea028d', 'System', '2026-08-06 13:42:02', '2026-08-07 13:47:43', '2026-08-06 13:32:02'),
(18, 3, 'login_2fa', '38acad02af807fed2131bc29cba07e19e1df2ab2cb5d0dd6324bb02f13a55556', 'System', '2026-08-06 13:51:38', '2026-08-07 13:47:43', '2026-08-06 13:41:38'),
(19, 1, 'login_2fa', 'cdc036ae8ca397a128794e366b26d21e3e8ec44b03aee9f90735aca07f1ad5a6', 'System', '2026-08-06 14:05:22', '2026-08-07 13:47:43', '2026-08-06 13:55:22'),
(20, 1, 'login_2fa', 'edbde5b085641bb56b53105fa228487e2f7d0c2e1a230f920ddbebe2f2c72e14', 'System', '2026-08-06 14:12:17', '2026-08-07 13:47:43', '2026-08-06 14:02:17'),
(21, 1, 'login_2fa', '59e1d8a04bbdb577a5127fa8e4b51d51f57aa2daad911e88e97b6c843272f28d', 'System', '2026-08-06 14:16:28', '2026-08-07 13:47:43', '2026-08-06 14:06:28'),
(22, 3, 'login_2fa', 'e6c1d8b78e999e9bc4f1b633fa0a584669ec5984759d4adabf59745f49bc503e', 'System', '2026-08-06 14:53:44', '2026-08-07 13:47:43', '2026-08-06 14:43:44'),
(23, 1, 'login_2fa', '264797c49afc4ad46350f996be7c1894b7e19828bdb52083f16a24301e3edb82', 'System', '2026-08-06 16:02:00', '2026-08-07 13:47:43', '2026-08-06 15:52:00'),
(24, 3, 'login_2fa', '5c7435d07f6f8c48816e5d6c8522b3add82f6cce0e3284bb92589998761e4583', 'System', '2026-08-06 16:02:53', '2026-08-07 13:47:43', '2026-08-06 15:52:53'),
(25, 3, 'login_2fa', 'a880ff676aac443c05712a3771640b6275a86282c34ba45a2d1075c757b86991', 'System', '2026-08-06 16:45:53', '2026-08-07 13:47:43', '2026-08-06 16:35:53'),
(26, 1, 'login_2fa', 'cd26d8606d44d05c0e9116c5d9b445655a9132310ad7bda5983a969e9ea9f26d', 'System', '2026-08-06 20:13:26', '2026-08-07 13:47:43', '2026-08-06 20:03:26'),
(27, 1, 'login_2fa', '967cb927a3559c93bbbe1d503405f92049cfeaeedcb2fd11c3ec9eee0c4d2f6e', 'System', '2026-08-06 20:31:15', '2026-08-07 13:47:43', '2026-08-06 20:21:15'),
(28, 3, 'login_2fa', '365e076e8bb97bae37a97870e02200186d2776cdced25150c0d37cc086eb8a5b', 'System', '2026-08-06 20:35:28', '2026-08-07 13:47:43', '2026-08-06 20:25:28'),
(29, 1, 'login_2fa', 'ad13979ae9e79127941523c4bbb960dcee573c5765e05db86dbd1bdf67527e62', 'System', '2026-08-07 11:54:29', '2026-08-07 13:47:43', '2026-08-07 11:44:29'),
(30, 3, 'login_2fa', '8c4b0774bc5f82a69bba1cde2b2965d76c7e7db49b4c2a7a4c7415b60bf784cf', 'System', '2026-08-07 11:55:03', '2026-08-07 13:47:43', '2026-08-07 11:45:03'),
(31, 1, 'login_2fa', '90858f8d89d06736dd2f40b75ff8eb3a998e0c93fcbd8e9933f9e73630d7127f', 'System', '2026-08-07 13:57:27', '2026-08-07 13:47:43', '2026-08-07 13:47:27'),
(32, 1, 'passkey_remove', 'df8c90265057ad814bde1ac13e69cd5d1aa480375cd8a00f9e9b4a29e917b7fe', NULL, '2026-08-07 14:33:29', NULL, '2026-08-07 14:23:29'),
(33, 2, 'registrar_login', '2b0c09523bc7dacd711d5577d4a2bb1e59dc78eeed46d5a308c247e30da99b23', NULL, '2026-09-19 07:15:20', '2026-09-19 09:01:39', '2026-09-19 07:13:20'),
(34, 604, 'registrar_login', 'cec1c6f8f290e3fb8fd82fb41bb132b894d2d2f0829bdcccb3afa57c280cf29f', NULL, '2026-09-19 07:17:12', '2026-09-19 07:15:44', '2026-09-19 07:15:12'),
(35, 2, 'registrar_login', '8d1842638508a44e948ba74f6c953ff3602f61d5f657e6e6a3a2d6718a8c00df', NULL, '2026-09-19 09:03:39', '2026-11-03 12:44:12', '2026-09-19 09:01:39'),
(36, 604, 'registrar_login', '1bf7f8f8077eb7e8033b1a8bdc7212571e498b6e3626ff3542f6bdaaf4cf8b6d', NULL, '2026-09-19 09:04:02', '2026-09-19 09:02:45', '2026-09-19 09:02:02'),
(37, 604, 'registrar_login', 'affb11006c2bb0c139c262b58712693811d66cd50c6588db10ff7c88ce9ad002', NULL, '2026-09-19 10:10:29', '2026-09-19 10:10:22', '2026-09-19 10:08:29'),
(38, 604, 'registrar_login', '978feb6cd8e1458d0ad117dc84fec4f2796533ecf18dca8eb6cb26e644617f9d', 'System', '2026-09-19 10:12:22', '2026-09-19 10:10:46', '2026-09-19 10:10:22'),
(39, 604, 'registrar_login', '8c25a3a2287ade5211b8fed74a257cbeb7fadf1251c6352b7b716f2e44759dbe', NULL, '2026-09-23 19:29:05', '2026-09-23 19:27:44', '2026-09-23 19:27:05'),
(40, 2, 'registrar_login', '7ce3dea09b7c6ee617d96a268c43dc7bcc0562ee8830d8921b3546b845953a26', NULL, '2026-11-03 12:46:12', NULL, '2026-11-03 12:44:12'),
(41, 604, 'registrar_login', '043595db3ec55e1ea2abeaab1cf1539d1eefb844320a4d82c34c56cd0723fd44', NULL, '2026-11-03 12:48:05', '2026-11-03 12:46:37', '2026-11-03 12:46:05'),
(42, 604, 'registrar_login', '98e7f19981e56f73812b1117eb39b6357ec0787e845be35dc96dbcb1782c3431', NULL, '2026-11-03 14:04:32', '2026-11-03 14:03:19', '2026-11-03 14:02:32'),
(43, 604, 'registrar_login', '15ec9c5729899d65a70eb2946fc53844c207578d54d03eee23bd1b9a9003778c', NULL, '2026-11-03 15:06:06', '2026-11-04 10:15:39', '2026-11-03 15:04:06'),
(44, 604, 'registrar_login', 'f87d67563f05e9cd4a9c403e9b8f835995a03d06556ec86c4677782d016989bc', NULL, '2026-11-04 10:17:39', '2026-11-04 10:16:05', '2026-11-04 10:15:39'),
(45, 604, 'registrar_login', '653aa825b6f3247f47463f1239e2ba6149a527539b8b47e1f1aa45d40ae826da', NULL, '2026-11-10 21:45:15', '2026-11-10 21:43:45', '2026-11-10 21:43:15'),
(46, 604, 'registrar_login', '18b699de48d48d4afbf06017eecd51de291397d0e4ed021cfc11cad49c893e92', NULL, '2026-11-11 16:52:47', '2026-11-11 16:52:42', '2026-11-11 16:50:47'),
(47, 604, 'registrar_login', '2038e5123b982803c044c81da59c22b6274a136aa810651cefe905d9d07a80ff', NULL, '2026-11-11 16:54:43', '2026-11-11 16:59:13', '2026-11-11 16:52:43'),
(48, 604, 'registrar_login', '104ed3cbacf0bc693b6f68c38e5dff6b6b3928be34439bcaf6ced7e16459c9a2', NULL, '2026-11-11 17:01:13', '2026-11-11 17:00:03', '2026-11-11 16:59:13'),
(49, 604, 'registrar_login', 'd139ee1f841b2fd81d85227c1cc00f80edb1902680fde68d687c9c73ff96f607', NULL, '2026-11-11 21:01:27', '2026-11-11 21:00:05', '2026-11-11 20:59:27'),
(50, 604, 'registrar_login', 'beca0ae4de03934832dd0035f8514a99e626cb5060d1378bc2d25a703eef9433', NULL, '2026-11-12 12:57:24', '2026-11-12 12:56:16', '2026-11-12 12:55:24'),
(51, 604, 'registrar_login', '26df50f1bab67d08b0f5c8fda8dcc387c21f2a6290b991fd2a0340834ce842ab', NULL, '2026-11-12 13:29:01', '2026-11-12 14:02:33', '2026-11-12 13:27:01'),
(52, 604, 'registrar_login', '557b4aa62cfd704eb2f04321e04f769f9e63913b154272fb7b24641f8a58446d', NULL, '2026-11-12 14:04:33', '2026-11-12 14:03:28', '2026-11-12 14:02:33'),
(53, 604, 'registrar_login', '7bf6263d3a7b4db9445476e4649251aae56d86434b369efeaf842ebdddfdc4da', NULL, '2026-11-18 12:19:17', '2026-11-18 12:17:38', '2026-11-18 12:17:17'),
(54, 604, 'registrar_login', '2f63b0ab036c3cb6e0c62602e3da89086c110471e07d2c7e715db209a6e5bb45', NULL, '2026-11-18 13:37:45', '2026-11-18 13:36:05', '2026-11-18 13:35:45'),
(55, 604, 'registrar_login', '5312d11ba43533e060ce7a9cd298761827414e52df3c1cd52833ae9c9ebe0c3e', NULL, '2026-11-18 21:38:27', '2026-11-18 21:36:48', '2026-11-18 21:36:27'),
(56, 604, 'registrar_login', '3429ff51f42259ccca6865e4d0c1f32d213ee7964121ae31d18d530f538e2dea', NULL, '2026-11-18 22:47:10', '2026-11-18 22:45:45', '2026-11-18 22:45:10'),
(57, 604, 'registrar_login', '282d556b74055b65ab1e94191374055ba55ecc9ad4f06d2fc117366aa1bd9449', NULL, '2026-11-19 19:29:20', '2026-11-19 19:27:36', '2026-11-19 19:27:20'),
(58, 604, 'registrar_login', 'b320b6833870c423b74ba0dbfe6ecab8e91055a01db2ce6376c93a3c4e69023a', NULL, '2026-11-19 20:40:18', '2026-11-19 20:38:37', '2026-11-19 20:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('csrf_enabled', '1', '2026-07-22 22:24:44'),
('lockout_minutes', '1', '2026-07-23 08:05:06'),
('lockout_seconds', '15', '2026-07-23 08:05:06'),
('lockout_unit', 'seconds', '2026-07-23 08:05:06'),
('lockout_value', '15', '2026-07-23 08:05:06'),
('mail_admin_email', 'j14677365@gmail.com', '2026-07-23 10:34:27'),
('mail_from_email', 'boyrexar02@gmail.com', '2026-08-31 01:30:21'),
('mail_from_name', 'Registrar Office', '2026-08-31 01:30:21'),
('mail_show_link_on_failure', '0', '2026-07-23 10:34:27'),
('max_failed_logins', '3', '2026-07-23 07:33:05'),
('min_password_length', '8', '2026-07-22 22:24:44'),
('module_kick_epoch_crad', '1784849304', '2026-07-23 15:28:24'),
('module_maintenance_crad', '0', '2026-07-23 15:29:22'),
('module_maintenance_msg_crad', 'The system is currently under maintenance. Some services may be temporarily unavailable.\r\n\r\nThank you for your patience and understanding.', '2026-07-23 15:05:14'),
('password_expiry_days', '0', '2026-07-22 22:24:44'),
('require_password_change_first_login', '0', '2026-07-22 22:24:44'),
('session_timeout_minutes', '30', '2026-07-22 22:24:44'),
('smtp_encryption', 'tls', '2026-07-23 10:33:25'),
('smtp_from_email', 'boyrexar02@gmail.com', '2026-08-31 01:29:20'),
('smtp_from_name', 'Registrar Office', '2026-08-31 01:29:20'),
('smtp_host', 'smtp.gmail.com', '2026-07-23 10:51:48'),
('smtp_pass', 'pqhn jsxr xwmk wpkz', '2026-08-31 01:29:20'),
('smtp_password', 'sms2enc1.o9eK2Ten8tzersLPsU60yqWzYqdbuJfLg37Dw0K+DEScwPBsvNnTlMj9W3LVDek=', '2026-08-31 01:30:21'),
('smtp_port', '587', '2026-07-23 10:33:25'),
('smtp_user', 'boyrexar02@gmail.com', '2026-08-31 01:29:20'),
('smtp_username', 'boyrexar02@gmail.com', '2026-08-31 01:30:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `role_key` varchar(40) NOT NULL,
  `student_id` varchar(40) DEFAULT NULL,
  `status` enum('active','inactive','locked','suspended') NOT NULL DEFAULT 'active',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `failed_login_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_seen_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `digital_signature` varchar(255) DEFAULT NULL,
  `welcome_message_shown` tinyint(1) NOT NULL DEFAULT 0,
  `registrar_terms_agreed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role_key`, `student_id`, `status`, `must_change_password`, `failed_login_attempts`, `locked_until`, `password_changed_at`, `last_login_at`, `last_seen_at`, `last_login_ip`, `notes`, `expires_at`, `created_at`, `updated_at`, `digital_signature`, `welcome_message_shown`, `registrar_terms_agreed`) VALUES
(1, 'superadmin', 'kennethabejuela0308@gmail.com', '$2y$10$/RGwqzpAEAaLyP7pVvhRKeuDDpmD3HIa.IIRuiBH7.Pw7UUiLD8V.', 'Super Admin', 'superadmin', NULL, 'active', 0, 0, NULL, '2026-08-09 19:46:23', '2026-11-03 12:44:57', '2026-11-11 20:57:21', '::1', NULL, NULL, '2026-07-22 22:53:59', '2026-11-11 20:57:21', NULL, 0, 0),
(2, 'registrar', 'registrar@bestlink.edu.ph', '$2y$10$PklShiU7Ek9bI/JqE8fQfug5OGdO1hPXKmNeRuRiztL39t.HpxAeS', 'Registrar', 'registrar', NULL, 'active', 0, 0, NULL, '2026-11-03 12:43:49', '2026-09-19 07:39:42', '2026-09-19 07:41:14', '::1', NULL, NULL, '2026-07-22 22:53:59', '2026-11-03 12:44:11', '/uploads/signatures/sig_1793681029_5940569c.png', 0, 1),
(3, 'cradofficer', 'angelicadublin340@gmail.com', '$2y$10$MpwtxHnKofWTxV5/axRiPuudxLEFIdLJChvSoykU9poFiH/W9wRPK', 'CRAD Officer', 'crad_officer', NULL, 'active', 0, 0, NULL, '2026-08-06 13:31:50', '2026-08-28 13:17:04', NULL, '::1', NULL, NULL, '2026-07-22 22:53:59', '2026-08-28 13:17:25', NULL, 0, 0),
(4, 'finance', 'monvictortesiorna@gmail.com', '$2y$10$mOPKz95hA/OlTNHGzzgLEuvYqMBNAE1RdQFThECQjfv94o.RbvIZq', 'Finance', 'finance', NULL, 'active', 0, 0, NULL, '2026-08-06 20:20:39', '2026-08-11 17:31:31', NULL, '::1', NULL, NULL, '2026-07-22 22:54:00', '2026-08-11 17:31:39', NULL, 0, 0),
(5, 'studentaffairs', 'studentaffairs@bestlink.edu.ph', '$2y$10$QSPLuT09VAB/X8J02CRi3erPEuhgAR2fKNCg.YLaOY.o2mnRuULii', 'Student Affairs', 'osa', NULL, 'active', 0, 0, NULL, '2026-08-08 01:38:23', NULL, NULL, NULL, NULL, NULL, '2026-07-22 22:54:00', '2026-08-08 01:38:23', NULL, 0, 0),
(6, 'itofficer', 'itofficer@bestlink.edu.ph', '$2y$10$fIFFgaSnSssf4ZdaYupnZ.fzX6dYDfE7escqc/GMedxVZUHCaqCPe', 'IT Officer', 'it_office', NULL, 'active', 0, 0, NULL, '2026-07-22 22:54:00', NULL, NULL, NULL, NULL, NULL, '2026-07-22 22:54:00', '2026-07-22 22:54:00', NULL, 0, 0),
(7, 'qualityassurance', 'qualityassurance@bestlink.edu.ph', '$2y$10$Bm/Te5m0uFyTRDhDDV.lf.9HuUEe7qIUOfZtHXF2eufIIXL1N3IVC', 'Quality Assurance', 'qa', NULL, 'active', 0, 0, NULL, '2026-07-22 22:54:00', NULL, NULL, NULL, NULL, NULL, '2026-07-22 22:54:00', '2026-07-22 22:54:00', NULL, 0, 0),
(8, 'dean', 'dean@bestlink.edu.ph', '$2y$10$Vnny70aSsPiimmO3/u6WKelc2VvQaKgSOujmZJP4C7q3IUFsUwfcy', 'Dean', 'hr', NULL, 'active', 0, 0, NULL, '2026-08-07 14:10:53', '2026-08-07 14:11:03', NULL, '::1', NULL, NULL, '2026-07-22 22:54:00', '2026-08-08 17:15:19', NULL, 0, 0),
(9, 's230000001', 'kenlangmalakas0308@gmail.com', '$2y$10$E0IiZOWMscnUfdX8H7gxt.5YzIkUqLK.qn07WF9MCA0StJhToFn2q', 'Student User', 'student', 'S230000001', 'active', 0, 0, NULL, '2026-08-06 13:17:16', '2026-08-12 17:01:05', '2026-08-12 17:01:54', '::1', NULL, NULL, '2026-07-22 22:54:00', '2026-08-12 17:01:54', NULL, 0, 0),
(20, 'admission', 'admission@bestlink.edu.ph', '$2y$10$EY3dISh/tqzwWQ3VP11AkeKz.Vm4sOyhWEZNp/HoFerpiLQ71GoTG', 'Admission', 'admission', NULL, 'active', 0, 0, NULL, '2026-09-19 03:16:13', '2026-09-19 10:20:55', NULL, '::1', NULL, NULL, '2026-08-08 17:25:20', '2026-09-19 10:21:12', NULL, 0, 0),
(40, 'researchcoordinator', 'researchcoordinator@bestlink.edu.ph', '$2y$10$kr8C7.GCqXURUm8BWUNg/.98dRB8HESD90LRGb/LA3iCndzwl9Qa6', 'Mrs. Kris Guevarra', 'research_coordinator', NULL, 'active', 0, 0, NULL, '2026-08-08 18:22:59', '2026-08-12 17:00:39', NULL, '::1', NULL, NULL, '2026-08-08 18:09:48', '2026-08-12 17:00:43', NULL, 0, 0),
(54, 'rsantos', 'rsantos@bestlink.edu.ph', '$2y$10$0XN5yHlMMXzk8nMrP9xqeusk0lcTiH0Y29HtbW3hMK3hbgaMWj3FS', 'Dr. Roberto M. Santos', 'adviser', NULL, 'active', 0, 0, NULL, '2026-08-08 21:43:02', '2026-08-12 17:00:51', NULL, '::1', NULL, NULL, '2026-08-08 21:35:14', '2026-08-12 17:00:58', NULL, 0, 0),
(116, 'researchdirector', 'research.director@bestlink.edu.ph', '$2y$10$KO2qqc52TXijFqOqzaA52.VuAXrqaO71S6Jp0u6YvpyxFe50BoCo.', 'Research Director', 'research_director', NULL, 'active', 0, 0, NULL, '2026-08-09 19:48:31', '2026-08-11 16:49:46', NULL, '::1', NULL, NULL, '2026-08-09 19:31:14', '2026-08-11 16:49:53', NULL, 0, 0),
(222, 'researchgrant', 'researchgrant@bestlink.edu.ph', '$2y$10$Kmx3XLgjIdLL3S4rP0Bs.uKL0oqNZyDRN4DpDwhtbYc249mAcYx8i', 'Research Grant', 'research_grant', NULL, 'active', 0, 0, NULL, '2026-08-10 20:07:43', '2026-08-11 20:56:56', NULL, '::1', NULL, NULL, '2026-08-10 20:01:49', '2026-08-11 20:57:03', NULL, 0, 0),
(455, 'reii', 'reii@bestlink.edu.ph', '$2y$10$W3XNoVVNd7k/u038T9uu7erYQwVtu4q0/di0gYtrs5j/uRTTtaKdy', 'Banz', 'registrar', NULL, 'active', 0, 0, NULL, '2026-08-13 02:52:31', '2026-08-30 20:05:17', NULL, '::1', 'Test', NULL, '2026-08-13 02:52:31', '2026-08-30 20:56:07', NULL, 0, 0),
(549, 'reiiiii', 'reii@gmail.com', '$2y$10$gSmdmvWuWXH.0VHn9ff1kuvLF29/T6381z2NE2Nz1ouFUL5eU.CgO', 'Raymar Bantonare', 'registrar', NULL, 'active', 0, 0, NULL, '2026-08-30 21:23:35', '2026-09-18 23:37:21', '2026-09-18 23:47:07', '::1', NULL, NULL, '2026-08-30 21:23:35', '2026-09-18 23:47:07', '/uploads/signatures/sig_1788096215_fb018f65.png', 0, 1),
(558, 'dave', 'dave@gmail.com', '$2y$10$SStN/C96jf.XSgvqv1C.5eiqlBfxx./LA1aP4oJC73APC.g/bv2Tm', 'DEN RELL JOHN DAVE SILVA', 'student', NULL, 'active', 0, 0, NULL, '2026-08-30 21:26:27', '2026-09-18 21:33:22', NULL, '::1', NULL, NULL, '2026-08-30 21:26:27', '2026-09-18 21:51:45', NULL, 1, 0),
(563, 'admin', 'admin@bestlink.edu.ph', '$2y$10$8/lnpgq0ACj6RdcYp2AmyeIKbSrEHufkC7408vVAX6SE6Lb6cEHMa', 'Admin', 'sms_admin', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:30', NULL, NULL, NULL, NULL, NULL, '2026-09-03 19:05:30', '2026-09-03 19:05:30', NULL, 0, 0),
(566, 'grammarian', 'grammarian@bestlink.edu.ph', '$2y$10$rTVaBfIljOVeNgnjTUR7deXGDb7.IiXie2mAsHXNyE9Uncfi/2JD2', 'Grammarian', 'grammarian', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:31', NULL, NULL, NULL, 'Research grammar and manuscript evaluator', NULL, '2026-09-03 19:05:31', '2026-09-03 19:05:31', NULL, 0, 0),
(567, 'jobertvalentino', 'jobertvalentino@bestlink.edu.ph', '$2y$10$Z/YUYBBnl0m6MCTzP0Uwou37LrTr50oPhM3nIDguj2JGLYV2K0hmC', 'Dr. Jobert Valentino', 'panel', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:31', NULL, NULL, NULL, 'Panel Member', NULL, '2026-09-03 19:05:31', '2026-09-03 19:05:31', NULL, 0, 0),
(568, 'jonathanestrada', 'jonathanestrada@bestlink.edu.ph', '$2y$10$5rc7YaCM50I3w/WxdzQdzujWa/feAjtGzATj9g81hAWTMpuihgD8S', 'Dr. Jonathan Estrada', 'panel', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:31', NULL, NULL, NULL, 'Panel Member', NULL, '2026-09-03 19:05:31', '2026-09-03 19:05:31', NULL, 0, 0),
(569, 'michelleguevarra', 'michelleguevarra@bestlink.edu.ph', '$2y$10$F4Tx87ZHgq0mykjO7pPnOehaolOelhuu2btL0QbGj82AYk8PlFMrq', 'Dr. Michelle Guevarra', 'panel', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:31', NULL, NULL, NULL, 'Panel Member', NULL, '2026-09-03 19:05:31', '2026-09-03 19:05:31', NULL, 0, 0),
(571, 'reviewcommittee', 'reviewcommittee@bestlink.edu.ph', '$2y$10$h8psHXn/m6AgNUyfxFEjhe/6co1pg4nilKMe9Bj1P/DBKxY4z75hi', 'Review Committee Member', 'review_committee', NULL, 'active', 0, 0, NULL, '2026-09-03 19:05:31', NULL, NULL, NULL, NULL, NULL, '2026-09-03 19:05:31', '2026-09-03 19:05:31', NULL, 0, 0),
(580, 'Ba8080#', 'placeholder_dBtixZ7xpnGhd@bcp.edu.ph', '$2y$10$oPDFvaL8A9pl4Zvkx8XU3.KtMS6yfBnm.mYLwpWDlTIAiX8Xur6/y', 'Ban Reii Basilan', 'pre-enrollee', 'dBtixZ7xpnGhd', 'active', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 17:01:23', '2026-09-08 17:01:23', '2026-09-08 17:01:23', NULL, 0, 0),
(581, 'Ba8080#1', 'placeholder_20262420@bcp.edu.ph', '$2y$10$bA3Zj4OKNCgy5ot2//6OjeUyXHR5fSy6tNZB.tZhc/MoFZSgdfl7u', 'Ror Ban', 'pre-enrollee', '20262420', 'active', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 17:03:46', '2026-09-08 17:03:46', '2026-09-08 17:03:46', NULL, 0, 0),
(582, 'Ba8080#2', 'lixx1920@gmail.com', '$2y$10$zZKuwlmgTEybf7ZjffSQCOqkGGxAo3vzOFAuvE7YT/2gGNsv5llaq', 'Rorss Banss', 'pre-enrollee', '20269741', 'active', 0, 0, NULL, NULL, '2026-09-08 17:11:19', NULL, '::1', NULL, '2026-09-15 17:10:07', '2026-09-08 17:10:07', '2026-09-08 17:55:34', NULL, 0, 0),
(585, 'Pe8080#', 'lixx1920+20268873@gmail.com', '$2y$10$7dmwbV7ZKfhf8E8YECpsMO026JMvtpBqVOXouEf32VKMkOkl.H5Ye', 'Rasdhashd PENG', 'student', '20268873', 'active', 0, 0, NULL, NULL, '2026-09-08 19:49:13', NULL, '::1', NULL, NULL, '2026-09-08 18:07:34', '2026-09-08 19:49:27', NULL, 0, 0),
(586, 'Lo8080#', 'reijibanz@gmail.com', '$2y$10$OGFl2PJznPSY/UNEAfdm.OHb0vMpNcXlNNsoitwuKMbAa9txqbgCi', 'Bingee LONG', 'pre-enrollee', '20268269', 'active', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 20:09:51', '2026-09-08 20:09:51', '2026-09-08 20:09:51', NULL, 0, 0),
(587, 'Pi8080#', 'reijibanz+20265788@gmail.com', '$2y$10$uOP5igpeQIilnPvGGUtqMOPmuJICYqm7Dfp5.8tVPeXGW9OhfEBmy', 'PECKK PINK', 'student', '20265788', 'active', 0, 0, NULL, NULL, '2026-09-08 20:24:48', NULL, '::1', NULL, NULL, '2026-09-08 20:23:46', '2026-09-08 20:25:36', NULL, 0, 0),
(588, 'Ba8080#3', 'reijibanz+20269613@gmail.com', '$2y$10$TKv8U20oEXgSyRw6px2kMOOmM7JB4tfDFLOcvrClVIJeNMRMT8O6a', 'Rochelle Banz', 'student', '20269613', 'active', 0, 0, NULL, NULL, '2026-09-08 23:11:06', NULL, '::1', NULL, NULL, '2026-09-08 22:18:56', '2026-09-08 23:25:41', NULL, 1, 0),
(589, 'Ba8080#4', 'boyrexar02@gmail.com', '$2y$10$LjfgOAIXD4ROOWlW4DnuMeHgvtfX/8mHSn62G1OoGYy2dl.DV/u02', 'Princess Bantonare', 'student', '20268140', 'active', 0, 0, NULL, NULL, '2026-09-08 22:59:14', NULL, '::1', NULL, NULL, '2026-09-08 22:58:18', '2026-09-08 22:59:47', NULL, 0, 0),
(590, 'Ba8080#5', 'reijibanz+20261602@gmail.com', '$2y$10$E49HvBiQpkp7nQAqlpWfueKkutgAfP85YPXq3sZ.HrfkUl3gUIyiO', 'Princessss Bantonare', 'student', '20261602', 'active', 0, 0, NULL, NULL, '2026-09-08 23:30:34', '2026-09-08 23:51:25', '::1', NULL, NULL, '2026-09-08 23:27:54', '2026-09-08 23:51:25', NULL, 1, 0),
(591, 'As8080#', 'reijibanz+20266801@gmail.com', '$2y$10$0iYJ8QU3x5RoHMpE9Sd4/.I8yD3hi7I2JZk2qVFroNlU1rR.xw17G', 'asdasd asd', 'pre-enrollee', '20266801', 'active', 0, 0, NULL, NULL, '2026-09-09 01:22:29', '2026-09-09 02:37:25', '::1', NULL, '2026-09-16 00:49:30', '2026-09-09 00:49:30', '2026-09-09 02:37:26', NULL, 0, 0),
(604, 'denrellregistrar', 'denrellsilva08@gmail.com', '$2y$10$Bdstls1Yi6HNAB5B4u8/C.zanWQ6kQV9HqAHTieGCuYvxwk0rB.6m', 'Den Rell Silva', 'registrar', NULL, 'active', 0, 0, NULL, '2026-11-03 12:45:42', '2026-11-19 20:51:49', NULL, '::1', NULL, NULL, '2026-09-19 00:22:03', '2026-11-19 21:27:38', '/uploads/signatures/sig_1793681142_e2bf575c.png', 0, 1),
(617, 'Si8080#', 'denrellsilva08+20264728@gmail.com', '$2y$10$Mh1KzBOcz8KhKFJk2BZKce6rwgqQV9kMIvEqJ9HWrCnwbUNquUzVK', 'Dengfgf Silvadfdfd', 'student', '20264728', 'active', 0, 0, NULL, NULL, '2026-09-19 10:23:01', '2026-09-19 11:26:46', '::1', NULL, NULL, '2026-09-19 03:16:48', '2026-09-19 11:26:46', NULL, 1, 0),
(626, 'Si8080#1', 'denrellsilva08+20267047@gmail.com', '$2y$10$sRoBExv85NXTtrOHJXw2YuZD8T6qz5y0NZSs9YqmVOrNghfCj1lom', 'Den Silva', 'pre-enrollee', '20267047', 'active', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-26 10:06:34', '2026-09-19 10:06:34', '2026-09-19 10:06:34', NULL, 0, 0),
(627, 'Re8080#', 'miketangonan14@gmail.com', '$2y$10$4nuYjMwRUvI6WtqyKLikzerJmWUKIbQWweVuuUxHg990XqVwYtflu', 'mike reyes', 'pre-enrollee', '20263534', 'active', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-26 10:18:20', '2026-09-19 10:18:20', '2026-09-19 10:18:20', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_authenticators`
--

CREATE TABLE `user_authenticators` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `secret` varchar(512) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `pending_secret` varchar(512) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_authenticators`
--

INSERT INTO `user_authenticators` (`user_id`, `secret`, `enabled`, `pending_secret`, `confirmed_at`, `updated_at`, `created_at`) VALUES
(2, 'sms2enc1.Nkl2TkZF3UK2czfXD2ZhAqWoD5VU0GNpQcxKCN/97X775DqWPOsT0Ok0UN02I+J7KLRr5/4iZ5R0+jPN', 0, 'sms2enc1.Nkl2TkZF3UK2czfXD2ZhAqWoD5VU0GNpQcxKCN/97X775DqWPOsT0Ok0UN02I+J7KLRr5/4iZ5R0+jPN', NULL, '2026-08-13 02:49:10', '2026-08-13 02:49:10'),
(222, 'sms2enc1.R966vl8cmeyNxW5So6wOrgQpEEYqOBmBitSjC4TY8JIUf66LRmaaCp7tzcsSHXqAkL8KPzQGzhciEiY5', 0, NULL, NULL, '2026-08-10 20:27:16', '2026-08-10 20:25:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_passkeys`
--

CREATE TABLE `user_passkeys` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `public_key` text NOT NULL,
  `sign_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `device_name` varchar(120) NOT NULL DEFAULT 'Passkey',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_user` (`user_id`),
  ADD KEY `idx_logs_action` (`action`),
  ADD KEY `idx_logs_created` (`created_at`);

--
-- Indexes for table `enr_pre_registrations`
--
ALTER TABLE `enr_pre_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reset_user` (`user_id`),
  ADD KEY `idx_reset_token` (`token_hash`),
  ADD KEY `idx_reset_expires` (`expires_at`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_prr_user` (`user_id`),
  ADD KEY `idx_prr_status` (`status`),
  ADD KEY `idx_prr_module` (`module_key`),
  ADD KEY `fk_prr_admin` (`admin_id`);

--
-- Indexes for table `reg_academic_history`
--
ALTER TABLE `reg_academic_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_academic_student` (`student_id`),
  ADD KEY `fk_reg_academic_file` (`file_id`),
  ADD KEY `fk_reg_academic_created` (`created_by`),
  ADD KEY `fk_reg_academic_updated` (`updated_by`);

--
-- Indexes for table `reg_academic_subjects`
--
ALTER TABLE `reg_academic_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_subj_student` (`student_id`),
  ADD KEY `idx_reg_subj_ay` (`academic_year`),
  ADD KEY `idx_reg_subj_yl` (`year_level`),
  ADD KEY `fk_reg_subj_created` (`created_by`);

--
-- Indexes for table `reg_counters`
--
ALTER TABLE `reg_counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_counter_key` (`counter_key`);

--
-- Indexes for table `reg_credentials`
--
ALTER TABLE `reg_credentials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_cred_token_type` (`token_value`,`credential_type`),
  ADD KEY `idx_reg_cred_student` (`student_id`),
  ADD KEY `idx_reg_cred_type` (`credential_type`),
  ADD KEY `idx_reg_cred_token` (`token_value`),
  ADD KEY `fk_reg_cred_created` (`created_by`);

--
-- Indexes for table `reg_doc_releases`
--
ALTER TABLE `reg_doc_releases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_release_slip` (`release_slip_no`),
  ADD KEY `idx_reg_release_request` (`request_id`),
  ADD KEY `idx_reg_release_date` (`released_at`),
  ADD KEY `fk_reg_release_item` (`request_item_id`),
  ADD KEY `fk_reg_release_user` (`released_by`);

--
-- Indexes for table `reg_doc_requests`
--
ALTER TABLE `reg_doc_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_doc_requests_no` (`request_no`),
  ADD KEY `idx_reg_doc_student` (`student_id`),
  ADD KEY `idx_reg_doc_channel` (`channel`),
  ADD KEY `fk_reg_doc_requested` (`requested_by`),
  ADD KEY `fk_reg_doc_created` (`created_by`);

--
-- Indexes for table `reg_doc_request_items`
--
ALTER TABLE `reg_doc_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_doc_items_request` (`request_id`),
  ADD KEY `idx_reg_doc_items_file` (`generated_file_id`);

--
-- Indexes for table `reg_doc_templates`
--
ALTER TABLE `reg_doc_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_template_name` (`template_name`),
  ADD KEY `idx_reg_template_type` (`doc_type`);

--
-- Indexes for table `reg_faculty_clearances`
--
ALTER TABLE `reg_faculty_clearances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `status` (`status`),
  ADD KEY `department` (`department`);

--
-- Indexes for table `reg_files`
--
ALTER TABLE `reg_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_files_student` (`student_id`),
  ADD KEY `fk_reg_files_uploader` (`uploaded_by`);

--
-- Indexes for table `reg_guardians`
--
ALTER TABLE `reg_guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_guardians_student` (`student_id`),
  ADD KEY `fk_reg_guardians_created` (`created_by`),
  ADD KEY `fk_reg_guardians_updated` (`updated_by`);

--
-- Indexes for table `reg_health_profiles`
--
ALTER TABLE `reg_health_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_health_profile_student` (`student_id`),
  ADD KEY `fk_reg_health_profile_created` (`created_by`),
  ADD KEY `fk_reg_health_profile_updated` (`updated_by`);

--
-- Indexes for table `reg_health_records`
--
ALTER TABLE `reg_health_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_health_student` (`student_id`),
  ADD KEY `idx_reg_health_date` (`checkup_date`),
  ADD KEY `fk_reg_health_file` (`file_id`),
  ADD KEY `fk_reg_health_created` (`created_by`),
  ADD KEY `fk_reg_health_updated` (`updated_by`);

--
-- Indexes for table `reg_masterlist_exports`
--
ALTER TABLE `reg_masterlist_exports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `reg_persona_files`
--
ALTER TABLE `reg_persona_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_persona_student` (`student_id`),
  ADD KEY `fk_reg_persona_file` (`file_id`),
  ADD KEY `fk_reg_persona_verified` (`verified_by`),
  ADD KEY `fk_reg_persona_created` (`created_by`),
  ADD KEY `fk_reg_persona_updated` (`updated_by`);

--
-- Indexes for table `reg_status_history`
--
ALTER TABLE `reg_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `changed_at` (`changed_at`);

--
-- Indexes for table `reg_students`
--
ALTER TABLE `reg_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_students_number` (`student_number`),
  ADD KEY `idx_reg_students_name` (`last_name`,`first_name`),
  ADD KEY `idx_reg_students_user` (`user_id`),
  ADD KEY `fk_reg_students_created` (`created_by`),
  ADD KEY `fk_reg_students_updated` (`updated_by`);

--
-- Indexes for table `reg_student_ids`
--
ALTER TABLE `reg_student_ids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_id_student` (`student_id`),
  ADD KEY `idx_reg_id_batch` (`batch_no`),
  ADD KEY `idx_reg_id_status` (`status`),
  ADD KEY `fk_reg_id_photo` (`photo_file_id`),
  ADD KEY `fk_reg_id_released` (`released_by`),
  ADD KEY `fk_reg_id_created` (`created_by`);

--
-- Indexes for table `reg_student_statuses`
--
ALTER TABLE `reg_student_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reg_status_student` (`student_id`),
  ADD KEY `fk_reg_status_changed` (`changed_by`);

--
-- Indexes for table `reg_verification_codes`
--
ALTER TABLE `reg_verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reg_verify_code` (`verification_code`),
  ADD KEY `idx_reg_verify_hash` (`doc_hash`),
  ADD KEY `idx_reg_verify_student` (`student_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_key` (`role_key`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_module` (`role_key`,`module_key`),
  ADD KEY `idx_perm_module` (`module_key`);

--
-- Indexes for table `sch_sections`
--
ALTER TABLE `sch_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sch_sections_lookup` (`program_course`,`year_section`,`school_year`,`semester`),
  ADD KEY `fk_sch_sections_created` (`created_by`);

--
-- Indexes for table `sch_section_assignments`
--
ALTER TABLE `sch_section_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sch_assignment_student_section` (`section_id`,`student_id`),
  ADD KEY `idx_sch_assignments_student` (`student_id`),
  ADD KEY `fk_sch_assignments_created` (`created_by`);

--
-- Indexes for table `security_otps`
--
ALTER TABLE `security_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role_key`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `idx_users_student_id` (`student_id`),
  ADD KEY `idx_users_last_seen` (`last_seen_at`);

--
-- Indexes for table `user_authenticators`
--
ALTER TABLE `user_authenticators`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_passkeys`
--
ALTER TABLE `user_passkeys`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1421;

--
-- AUTO_INCREMENT for table `enr_pre_registrations`
--
ALTER TABLE `enr_pre_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reg_academic_history`
--
ALTER TABLE `reg_academic_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `reg_academic_subjects`
--
ALTER TABLE `reg_academic_subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=799;

--
-- AUTO_INCREMENT for table `reg_counters`
--
ALTER TABLE `reg_counters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reg_credentials`
--
ALTER TABLE `reg_credentials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `reg_doc_releases`
--
ALTER TABLE `reg_doc_releases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reg_doc_requests`
--
ALTER TABLE `reg_doc_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `reg_doc_request_items`
--
ALTER TABLE `reg_doc_request_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `reg_doc_templates`
--
ALTER TABLE `reg_doc_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reg_faculty_clearances`
--
ALTER TABLE `reg_faculty_clearances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `reg_files`
--
ALTER TABLE `reg_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `reg_guardians`
--
ALTER TABLE `reg_guardians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `reg_health_profiles`
--
ALTER TABLE `reg_health_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reg_health_records`
--
ALTER TABLE `reg_health_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `reg_masterlist_exports`
--
ALTER TABLE `reg_masterlist_exports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reg_persona_files`
--
ALTER TABLE `reg_persona_files`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reg_status_history`
--
ALTER TABLE `reg_status_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reg_students`
--
ALTER TABLE `reg_students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2169;

--
-- AUTO_INCREMENT for table `reg_student_ids`
--
ALTER TABLE `reg_student_ids`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reg_student_statuses`
--
ALTER TABLE `reg_student_statuses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `reg_verification_codes`
--
ALTER TABLE `reg_verification_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=982;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=765;

--
-- AUTO_INCREMENT for table `sch_sections`
--
ALTER TABLE `sch_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sch_section_assignments`
--
ALTER TABLE `sch_section_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2059;

--
-- AUTO_INCREMENT for table `security_otps`
--
ALTER TABLE `security_otps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=648;

--
-- AUTO_INCREMENT for table `user_authenticators`
--
ALTER TABLE `user_authenticators`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `user_passkeys`
--
ALTER TABLE `user_passkeys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `fk_prr_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_academic_history`
--
ALTER TABLE `reg_academic_history`
  ADD CONSTRAINT `fk_reg_academic_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_academic_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_academic_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_academic_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_academic_subjects`
--
ALTER TABLE `reg_academic_subjects`
  ADD CONSTRAINT `fk_reg_subj_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_subj_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_credentials`
--
ALTER TABLE `reg_credentials`
  ADD CONSTRAINT `fk_reg_cred_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_cred_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_doc_releases`
--
ALTER TABLE `reg_doc_releases`
  ADD CONSTRAINT `fk_reg_release_item` FOREIGN KEY (`request_item_id`) REFERENCES `reg_doc_request_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_release_request` FOREIGN KEY (`request_id`) REFERENCES `reg_doc_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_release_user` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `reg_doc_requests`
--
ALTER TABLE `reg_doc_requests`
  ADD CONSTRAINT `fk_reg_doc_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_doc_requested` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_doc_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`);

--
-- Constraints for table `reg_doc_request_items`
--
ALTER TABLE `reg_doc_request_items`
  ADD CONSTRAINT `fk_reg_doc_items_file` FOREIGN KEY (`generated_file_id`) REFERENCES `reg_files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_doc_items_request` FOREIGN KEY (`request_id`) REFERENCES `reg_doc_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_files`
--
ALTER TABLE `reg_files`
  ADD CONSTRAINT `fk_reg_files_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_files_uploader` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_guardians`
--
ALTER TABLE `reg_guardians`
  ADD CONSTRAINT `fk_reg_guardians_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_guardians_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_guardians_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_health_profiles`
--
ALTER TABLE `reg_health_profiles`
  ADD CONSTRAINT `fk_reg_health_profile_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_health_profile_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_health_profile_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_health_records`
--
ALTER TABLE `reg_health_records`
  ADD CONSTRAINT `fk_reg_health_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_health_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_health_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_health_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_persona_files`
--
ALTER TABLE `reg_persona_files`
  ADD CONSTRAINT `fk_reg_persona_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_persona_file` FOREIGN KEY (`file_id`) REFERENCES `reg_files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_persona_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reg_persona_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_persona_verified` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reg_students`
--
ALTER TABLE `reg_students`
  ADD CONSTRAINT `fk_reg_students_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_students_updated` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reg_student_ids`
--
ALTER TABLE `reg_student_ids`
  ADD CONSTRAINT `fk_reg_id_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_id_photo` FOREIGN KEY (`photo_file_id`) REFERENCES `reg_files` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_id_released` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_id_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_student_statuses`
--
ALTER TABLE `reg_student_statuses`
  ADD CONSTRAINT `fk_reg_status_changed` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_status_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reg_verification_codes`
--
ALTER TABLE `reg_verification_codes`
  ADD CONSTRAINT `fk_reg_verify_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_perm_role` FOREIGN KEY (`role_key`) REFERENCES `roles` (`role_key`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sch_sections`
--
ALTER TABLE `sch_sections`
  ADD CONSTRAINT `fk_sch_sections_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sch_section_assignments`
--
ALTER TABLE `sch_section_assignments`
  ADD CONSTRAINT `fk_sch_assignments_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sch_assignments_section` FOREIGN KEY (`section_id`) REFERENCES `sch_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sch_assignments_student` FOREIGN KEY (`student_id`) REFERENCES `reg_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_key`) REFERENCES `roles` (`role_key`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
