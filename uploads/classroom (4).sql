-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:1055
-- Generation Time: Apr 05, 2025 at 09:24 AM
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
-- Database: `classroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `month_year` varchar(10) NOT NULL,
  `status` enum('Present') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `class_id`, `student_id`, `date`, `month_year`, `status`) VALUES
(2, 2, 3, '2025-04-04', '', 'Present'),
(3, 1, 2, '2025-04-04', '', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_status`
--

CREATE TABLE `attendance_status` (
  `id` int(11) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_status`
--

INSERT INTO `attendance_status` (`id`, `is_open`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `subject`, `teacher_id`, `created_at`, `status`) VALUES
(1, 'CLASS X', NULL, 1, '2025-04-04 04:16:09', 'active'),
(2, 'CLASS XI', NULL, 1, '2025-04-04 04:16:16', 'active'),
(3, 'CLASS XII', NULL, 1, '2025-04-04 04:16:23', 'active'),
(4, 'CLASS II', NULL, 1, '2025-04-04 04:16:31', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `class_requests`
--

CREATE TABLE `class_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_requests`
--

INSERT INTO `class_requests` (`id`, `student_id`, `class_id`, `status`, `requested_at`) VALUES
(1, 2, 1, 'approved', '2025-04-04 04:37:42'),
(2, 2, 3, 'approved', '2025-04-04 04:37:51'),
(3, 2, 4, 'approved', '2025-04-04 04:37:58'),
(4, 3, 2, 'approved', '2025-04-04 04:47:49'),
(5, 3, 4, 'approved', '2025-04-04 04:47:57'),
(6, 3, 1, 'pending', '2025-04-04 10:14:10'),
(7, 2, 2, 'pending', '2025-04-05 07:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

CREATE TABLE `class_students` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_students`
--

INSERT INTO `class_students` (`id`, `class_id`, `student_id`, `joined_at`) VALUES
(1, 1, 2, '2025-04-04 04:39:17'),
(2, 3, 2, '2025-04-04 04:39:20'),
(3, 4, 2, '2025-04-04 04:39:23'),
(4, 2, 3, '2025-04-04 04:48:26'),
(5, 4, 3, '2025-04-04 04:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `due_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `class_id`, `file_name`, `file_path`, `uploaded_at`, `user_id`, `due_date`) VALUES
(1, 1, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 04:23:39', 1, NULL),
(2, 1, 'classroom.sql', 'uploads/classroom.sql', '2025-04-04 04:23:48', 1, NULL),
(3, 1, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 04:27:37', 1, NULL),
(4, 1, 'REGISTRATION_4TH_SEM (1) (1).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (1).pdf', '2025-04-04 04:27:44', 1, NULL),
(5, 2, 'REGISTRATION_4TH_SEM (1) (1).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (1).pdf', '2025-04-04 04:50:45', 3, NULL),
(6, 1, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 07:36:44', 1, '2025-04-25 13:06:00'),
(7, 1, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 07:40:30', 1, '2025-04-25 13:06:00'),
(8, 1, 'classroom.sql', 'uploads/classroom.sql', '2025-04-04 07:40:54', 1, '2025-04-04 13:13:00');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `grade` char(2) DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `class_id`, `message`, `sent_at`, `seen`) VALUES
(1, 1, 2, 1, 'HII ABHI', '2025-04-04 04:41:52', 0),
(2, 1, 2, 1, 'kaisa hai bhai?', '2025-04-04 04:42:01', 0),
(3, 1, 2, 1, 'mai tera papa', '2025-04-04 04:42:11', 0),
(4, 3, 2, 4, 'HEY BRO WHATSUP', '2025-04-04 04:50:08', 1),
(5, 3, 2, 4, 'hii chutiye', '2025-04-04 10:19:07', 1),
(6, 1, 2, 1, 'HLW TEST', '2025-04-05 07:13:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `test_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 1, 'What is the capital of France?', 'BERLIN', 'ROME', 'PARACE', 'MADRID', 'C'),
(2, 1, 'Which planet is known as the \"Red Planet\"?', 'VENUS', 'MARS', 'JUPITER', 'SATURN', 'B'),
(3, 1, 'What is the chemical symbol for water?', 'O2', 'H2O', 'HNO3', 'CO2', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`) VALUES
(1, 'AZLAN', 'AZLAN123@GMAIL.COM');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_option` char(1) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `student_id`, `test_id`, `question_id`, `selected_option`, `is_correct`) VALUES
(1, 2, 1, 1, 'C', 1),
(2, 2, 1, 2, 'A', 0),
(3, 2, 1, 3, 'B', 1),
(4, 2, 1, 1, 'B', 0),
(5, 2, 1, 2, 'B', 1),
(6, 2, 1, 3, 'A', 0);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `file_id` int(11) DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `file_id`, `student_id`, `file_name`, `file_path`, `submitted_at`) VALUES
(8, 4, 2, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/Assignment_submissions/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 06:57:20'),
(9, 4, 2, 'classroom.sql', 'uploads/Assignment_submissions/classroom.sql', '2025-04-04 06:57:31'),
(10, 3, 2, 'REGISTRATION_4TH_SEM (1) (1).pdf', 'uploads/Assignment_submissions/REGISTRATION_4TH_SEM (1) (1).pdf', '2025-04-04 06:59:38'),
(11, 2, 2, 'REGISTRATION_4TH_SEM (1) (2).pdf', 'uploads/Assignment_submissions/REGISTRATION_4TH_SEM (1) (2).pdf', '2025-04-04 06:59:56'),
(12, 1, 2, 'classroom.sql', 'uploads/Assignment_submissions/classroom.sql', '2025-04-04 07:00:11');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `title`, `description`, `class_id`, `created_by`, `created_at`) VALUES
(1, 'SCIENCE CHAPTER 4', 'REVISION TEST', 1, 1, '2025-04-05 10:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_results`
--

INSERT INTO `test_results` (`id`, `student_id`, `test_id`, `score`, `total_questions`, `submitted_at`) VALUES
(1, 2, 1, 2, 3, '2025-04-05 10:17:31'),
(2, 2, 1, 1, 3, '2025-04-05 10:19:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notification` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `notification`) VALUES
(1, 'FEYAZ', 'FEYAZKHAN8800@GMAIL.COM', '$2y$10$FwV42502jRe9YWI/NANcOuMdXQ3fMgWd.P6EN/1FczB2g7d/6el.m', 'teacher', '2025-04-04 04:15:38', NULL),
(2, 'ABHI', 'ABHI123@GMAIL.COM', '$2y$10$WS7Gl//wGXUmawplC6dbMOcivoU93JJSpmE2hg39pgI8tvX7eDycW', 'student', '2025-04-04 04:35:42', 'Your join request for CLASS II has been approved!'),
(3, 'ANKIT', 'ANKIT123@GMAIL.COM', '$2y$10$0MxqhYtWfjy9hqrgPY6R4.7Wjmpk6pcMl37cHcIiArSk4Ug6I3HMG', 'student', '2025-04-04 04:46:45', 'Your join request for CLASS II has been approved!'),
(4, 'AZLAN', 'AZLAN123@GMAIL.COM', '$2y$10$N0Y8t/.n3vqqKwodWOhcH.chupHr4DbWjLZ3XoJO/WjIVm.RIorf2', 'student', '2025-04-04 11:07:59', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`class_id`,`student_id`,`date`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `attendance_status`
--
ALTER TABLE `attendance_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_requests`
--
ALTER TABLE `class_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_submission_student` (`student_id`),
  ADD KEY `fk_submission_file` (`file_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_requests`
--
ALTER TABLE `class_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `class_students`
--
ALTER TABLE `class_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
