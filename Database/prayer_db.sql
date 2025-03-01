-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2025 at 08:17 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prayer_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text DEFAULT NULL,
  `sent_datetime` datetime DEFAULT current_timestamp(),
  `reply_text` text DEFAULT NULL,
  `reply_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message_text`, `sent_datetime`, `reply_text`, `reply_datetime`) VALUES
(4, 4, 0, 'cadsa', '2024-11-27 10:55:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prayers`
--

CREATE TABLE `prayers` (
  `prayer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prayer_name` enum('Fajr','Dhuhr','Asr','Maghrib','Isha') NOT NULL,
  `date` date NOT NULL,
  `status` enum('completed','missed','qaza') NOT NULL,
  `logged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prayers`
--

INSERT INTO `prayers` (`prayer_id`, `user_id`, `prayer_name`, `date`, `status`, `logged_at`) VALUES
(21, 3, 'Fajr', '2025-03-01', 'completed', '2025-03-01 11:53:41'),
(22, 3, 'Dhuhr', '2025-03-01', 'qaza', '2025-03-01 11:53:43'),
(23, 3, 'Maghrib', '2025-03-01', 'missed', '2025-03-01 11:53:45'),
(24, 3, 'Isha', '2025-03-01', 'completed', '2025-03-01 12:03:26');

-- --------------------------------------------------------

--
-- Table structure for table `prayer_guidance`
--

CREATE TABLE `prayer_guidance` (
  `guidance_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prayer_guidance`
--

INSERT INTO `prayer_guidance` (`guidance_id`, `title`, `description`, `video_url`, `created_at`, `updated_at`) VALUES
(1, 'dadfsew', 'dfaadfa', NULL, '2024-11-27 10:06:44', '2024-11-27 10:10:34'),
(2, 'dsa', 'dfa', 'https://youtu.be/88TfxIO45nA?si=CGGmKsYpT7tERKTi', '2024-11-27 10:08:21', '2024-11-27 10:08:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `age`, `full_name`, `bio`, `status`) VALUES
(3, 'nasir', '$2y$10$a/SgsGBswoo1YfNrJd./p.Jqey.MDYC.QoLsyJGas3H5cdesUPvjq', 'nasiryt.827@gmail.com', '9853', 23, 'NASIR ABBAS', 'software developer', 'active'),
(4, 'abbas12', '$2y$10$Gd4HpAg44BiF8suT7q1T5OvUTR90pkfhyBpIB1GDbh/jvGzLrke/C', 'abbas@gmail.com', '43443', 23, '', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prayers`
--
ALTER TABLE `prayers`
  ADD PRIMARY KEY (`prayer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prayer_guidance`
--
ALTER TABLE `prayer_guidance`
  ADD PRIMARY KEY (`guidance_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prayers`
--
ALTER TABLE `prayers`
  MODIFY `prayer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `prayer_guidance`
--
ALTER TABLE `prayer_guidance`
  MODIFY `guidance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `prayers`
--
ALTER TABLE `prayers`
  ADD CONSTRAINT `prayers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
