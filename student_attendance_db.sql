-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 06:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_attendance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `contact`, `email`) VALUES
(1, 'ravina', '$2y$10$PxQU4DviFI9JGieo5ueHneKL6DZFl/Pda5QEdbZVSiVdG8JAVGKiS', '1234567890', 'ravinagolani677@gmail.com'),
(2, 'kinjal', '$2y$10$3SxcdmhGxMKosLnQp/Yu4ewmGux0DNlEEKBz6vbwBpWGmeASk9fgq', '1235678904', 'kinj4385@gmail.com'),
(3, 'priyanka', '$2y$10$NWXhoMPQPYpVCGOg0S/i7OT5rX0208L39cyyTk5AAOHyTbIEtzXvG', '1231234568', 'priyakariya182@gmail.com'),
(4, 'satvik', '$2y$10$zIF4wp.cl7mCaqayCI97Ru2gi98Elwu9PAJxz6o6a8.20XQBnVBDe', '9228283184', 'MJKACC2001@GMAIL.COM'),
(5, 'ravira', '$2y$10$pWX5UxW9Lwcf8oP9OGM6ZOtW8gMWxXIOoOiOD6X29G25exJAeaWS.', '9773286353', 'ravinagolani60@gmail.co');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Leave') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `subject_id`, `teacher_id`, `date`, `status`) VALUES
(1, 2, 12, 16, '2025-08-01', 'Present'),
(2, 2, 12, 16, '2025-09-01', 'Present'),
(3, 3, 12, 16, '2025-09-01', 'Present'),
(4, 5, 12, 16, '2025-09-01', 'Present'),
(5, 2, 12, 16, '2025-09-02', 'Absent'),
(6, 3, 12, 16, '2025-09-02', 'Present'),
(7, 5, 12, 16, '2025-09-02', 'Present'),
(8, 9, 11, 15, '2025-09-03', 'Present'),
(9, 2, 1, 8, '2025-09-13', 'Present'),
(10, 3, 1, 8, '2025-09-13', 'Present'),
(11, 5, 1, 8, '2025-09-13', 'Present'),
(12, 2, 12, 16, '2025-09-11', 'Present'),
(13, 3, 12, 16, '2025-09-11', 'Absent'),
(14, 5, 12, 16, '2025-09-11', 'Leave'),
(15, 2, 4, 16, '2025-09-13', 'Present'),
(16, 3, 4, 16, '2025-09-13', 'Present'),
(17, 5, 4, 16, '2025-09-13', 'Present'),
(18, 2, 1, 14, '2025-09-12', 'Present'),
(19, 3, 1, 14, '2025-09-12', 'Absent'),
(20, 5, 1, 14, '2025-09-12', 'Leave'),
(21, 2, 12, 16, '2025-10-01', 'Present'),
(22, 26, 12, 16, '2025-10-01', 'Leave'),
(23, 26, 4, 21, '2025-10-01', 'Present'),
(24, 26, 12, 16, '2025-09-01', 'Present'),
(25, 27, 12, 16, '2025-09-01', 'Absent'),
(26, 29, 12, 16, '2025-09-01', 'Present'),
(27, 30, 12, 16, '2025-09-01', 'Present'),
(28, 31, 12, 16, '2025-09-01', 'Leave'),
(29, 32, 12, 16, '2025-09-01', 'Present'),
(30, 26, 12, 16, '2025-10-02', 'Present'),
(31, 27, 12, 16, '2025-10-02', 'Present'),
(32, 29, 12, 16, '2025-10-02', 'Present'),
(33, 30, 12, 16, '2025-10-02', 'Leave'),
(34, 31, 12, 16, '2025-10-02', 'Absent'),
(35, 32, 12, 16, '2025-10-02', 'Present'),
(36, 26, 12, 16, '2025-09-03', 'Leave'),
(37, 27, 12, 16, '2025-09-03', 'Present'),
(38, 29, 12, 16, '2025-09-03', 'Present'),
(39, 30, 12, 16, '2025-09-03', 'Present'),
(40, 31, 12, 16, '2025-09-03', 'Present'),
(41, 32, 12, 16, '2025-09-03', 'Absent'),
(42, 26, 12, 16, '2025-09-04', 'Present'),
(43, 27, 12, 16, '2025-09-04', 'Present'),
(44, 29, 12, 16, '2025-09-04', 'Present'),
(45, 30, 12, 16, '2025-09-04', 'Present'),
(46, 31, 12, 16, '2025-09-04', 'Present'),
(47, 32, 12, 16, '2025-09-04', 'Present'),
(48, 26, 12, 16, '2025-09-05', 'Present'),
(49, 27, 12, 16, '2025-09-05', 'Present'),
(50, 29, 12, 16, '2025-09-05', 'Present'),
(51, 30, 12, 16, '2025-09-05', 'Absent'),
(52, 31, 12, 16, '2025-09-05', 'Present'),
(53, 32, 12, 16, '2025-09-05', 'Present'),
(54, 26, 12, 16, '2025-09-02', 'Present'),
(55, 27, 12, 16, '2025-09-02', 'Present'),
(56, 29, 12, 16, '2025-09-02', 'Leave'),
(57, 30, 12, 16, '2025-09-02', 'Present'),
(58, 31, 12, 16, '2025-09-02', 'Absent'),
(59, 32, 12, 16, '2025-09-02', 'Present'),
(60, 26, 12, 16, '2025-09-06', 'Present'),
(61, 27, 12, 16, '2025-09-06', 'Present'),
(62, 29, 12, 16, '2025-09-06', 'Present'),
(63, 30, 12, 16, '2025-09-06', 'Present'),
(64, 31, 12, 16, '2025-09-06', 'Present'),
(65, 32, 12, 16, '2025-09-06', 'Present'),
(66, 26, 12, 16, '2025-09-07', 'Absent'),
(67, 27, 12, 16, '2025-09-07', 'Present'),
(68, 29, 12, 16, '2025-09-07', 'Present'),
(69, 30, 12, 16, '2025-09-07', 'Present'),
(70, 31, 12, 16, '2025-09-07', 'Leave'),
(71, 32, 12, 16, '2025-09-07', 'Present'),
(72, 26, 12, 16, '2025-09-08', 'Present'),
(73, 27, 12, 16, '2025-09-08', 'Present'),
(74, 29, 12, 16, '2025-09-08', 'Present'),
(75, 30, 12, 16, '2025-09-08', 'Absent'),
(76, 31, 12, 16, '2025-09-08', 'Present'),
(77, 32, 12, 16, '2025-09-08', 'Present'),
(78, 26, 12, 16, '2025-09-09', 'Present'),
(79, 27, 12, 16, '2025-09-09', 'Present'),
(80, 29, 12, 16, '2025-09-09', 'Present'),
(81, 30, 12, 16, '2025-09-09', 'Present'),
(82, 31, 12, 16, '2025-09-09', 'Present'),
(83, 32, 12, 16, '2025-09-09', 'Present'),
(84, 26, 12, 16, '2025-09-10', 'Present'),
(85, 27, 12, 16, '2025-09-10', 'Present'),
(86, 29, 12, 16, '2025-09-10', 'Present'),
(87, 30, 12, 16, '2025-09-10', 'Present'),
(88, 31, 12, 16, '2025-09-10', 'Present'),
(89, 32, 12, 16, '2025-09-10', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'BCA'),
(2, 'BA'),
(3, 'BCom');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `name`, `email`, `rating`, `message`, `created_at`) VALUES
(1, 'kinjal', 'kinjalolakiya60@gmail.com', 5, 'bahut achha', '2025-09-20 11:38:00'),
(2, 'kinjal', 'kinjalolakiya60@gmail.com', 5, 'bahut achha', '2025-09-20 11:39:20'),
(3, 'kinjal', 'kinjalolakiya60@gmail.com', 5, 'bahut achha', '2025-09-20 11:39:51'),
(4, 'kinju', 'ravinagolani60@gmail.co', 5, 'good', '2025-09-20 11:47:52');

-- --------------------------------------------------------

--
-- Table structure for table `password_otps`
--

CREATE TABLE `password_otps` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `number`) VALUES
(101, 1),
(102, 2),
(103, 3),
(104, 4),
(105, 5),
(106, 6);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `name` enum('Morning','Afternoon') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`) VALUES
(1, 'Morning'),
(2, 'Afternoon');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `roll_no`, `department_id`, `semester_id`, `shift_id`) VALUES
(4, 'priya', '2575', 2, 102, 1),
(26, 'kinjal', '2422', 1, 105, 1),
(27, 'aavni', '2412', 1, 105, 1),
(28, 'aavni', '1212', 2, 102, 1),
(29, 'vidhya', '2437', 1, 105, 1),
(30, 'bharti', '2414', 1, 105, 1),
(31, 'jensi', '2369', 1, 105, 1),
(32, 'semina', '2373', 1, 105, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stud_user`
--

CREATE TABLE `stud_user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `department_id`, `semester_id`) VALUES
(1, 'AI', 1, 105),
(4, 'cyber security', 1, 105),
(9, 'account', 3, 102),
(11, 'hindi', 2, 101),
(12, 'python', 1, 105),
(13, 'c++', 1, 102),
(17, 'English', 3, 103);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `username`, `password`, `department_id`, `shift_id`) VALUES
(8, 'priyanka', 'Priyanka', '$2y$10$IqMKLniV4vUtnDXE.AhJjuG5BA8.JAiTTsLp0tQtudNZVV/osC9W2', 1, 1),
(14, 'kinjal', NULL, NULL, 1, 1),
(15, 'radha', NULL, NULL, 2, 1),
(16, 'bhumika', NULL, NULL, 1, 1),
(21, 'Satvik', NULL, NULL, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_otps`
--
ALTER TABLE `password_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_sem_roll` (`semester_id`,`roll_no`),
  ADD UNIQUE KEY `semester_id` (`semester_id`,`roll_no`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_otps`
--
ALTER TABLE `password_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
