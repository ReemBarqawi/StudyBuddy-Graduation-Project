-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 06:28 PM
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
-- Database: `studdybuddy`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` bigint(20) NOT NULL,
  `First_name` varchar(50) DEFAULT NULL,
  `Last_name` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` enum('student','buddy','admin') DEFAULT NULL,
  `Bio` text NOT NULL,
  `Image` text DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `reset_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `First_name`, `Last_name`, `Email`, `Password`, `Role`, `Bio`, `Image`, `verification_code`, `is_verified`, `reset_code`) VALUES
(1, 'Ahmad', 'Salem', 'ahmad@student.com', 'hashed_pw1', 'student', 'CS student at YU, loves backend development.', 'ahmad.jpg', NULL, 0, NULL),
(2, 'Lina', 'Ali', 'lina@buddy.com', 'hashed_pw2', 'buddy', 'Software engineering graduate, passionate about teaching.', 'lina.jpg', NULL, 0, NULL),
(3, 'Admin', 'System', 'admin@studybuddy.com', 'hashed_pw3', 'admin', 'System administrator responsible for managing platform content.', NULL, NULL, 0, NULL),
(5, 'test', 'tt', 'testdf@cnn.com', '$2y$10$Amh2mC3/awV5qb5j5juU2.XX6Xt0KaMm8tQjLUr0JLP2yYy8JESvm', 'buddy', '', NULL, NULL, 0, NULL),
(6, 'test2', 'ttt', 'jhgfreerwswe@rdy', '$2y$10$NWM9FBiV5mkHo8KpxwhTcO4Oqep9EoPbKo/hh73YgjPOLgR3m1eCu', 'buddy', '', NULL, NULL, 0, NULL),
(7, 'test2', 'Alazzam', 'doha@example.com', '$2y$10$rUuLXx2q1zo23T/aGjOWTuNmGMTCjcp3E6QGviemIYDjXICWl4nX2', 'buddy', '', NULL, NULL, 0, NULL),
(8, 'test3', 'tt', 'hsheyab@gmail.com', '$2y$10$1LsHd3xc5u32cJVah9wrLe803QsxpUIp42h.4ePRagCLyE73srb5O', 'student', '', NULL, NULL, 0, NULL),
(19, 'Douha', 'Alazzam', 'dohaalazzam2@gmail.com', '$2y$10$47s9Etujvi4dUEFnYuOAtu8Uj7pblODpeNVbfs4bSeFTaP4udtTSi', 'buddy', '', NULL, NULL, 1, NULL),
(20, 'testt', 'eee', '2021903034@ses.yu.edu.jo', '$2y$10$xx.WIvKL1HUZ8Acje0KhruwfHlm8fOaGvTmoIDglY5ELH/f241pEK', 'student', '', NULL, NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
