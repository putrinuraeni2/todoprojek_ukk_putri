-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 06:47 AM
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
-- Database: `db_todolistputri`
--

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `subtask` varchar(255) NOT NULL,
  `status` enum('open','close') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`id`, `task_id`, `subtask`, `status`) VALUES
(23, 5, 'iqro', 'open'),
(24, 5, 'alquran', 'open'),
(25, 6, 'ngepel', 'open'),
(26, 6, 'nyapu', 'open'),
(35, 1, 'indonesia', 'open'),
(36, 1, 'bahasa inggris', 'open'),
(37, 1, 'ipaa', 'open'),
(38, 10, 'iqro', 'open'),
(39, 10, 'al quran', 'open'),
(42, 12, 'ipaa', 'open'),
(43, 12, 'sunda', 'open'),
(44, 11, 'indo', 'open'),
(45, 11, 'mtk', 'open'),
(59, 15, 'panda', 'open'),
(60, 15, 'onta', 'open'),
(61, 16, 'daging ', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('Biasa','Cukup Penting','Sangat Penting') NOT NULL,
  `taskstatus` enum('open','close') DEFAULT 'open',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `task`, `due_date`, `priority`, `taskstatus`, `user_id`) VALUES
(1, 'belajar', '2025-02-22', 'Biasa', 'open', NULL),
(5, 'ngaji', '2025-02-25', 'Sangat Penting', 'open', NULL),
(6, 'rumah', '2025-02-26', 'Cukup Penting', 'open', NULL),
(10, 'ngaji', '2025-02-26', 'Sangat Penting', 'open', 15),
(11, 'belajar ', '2025-02-27', 'Sangat Penting', 'open', 14),
(12, 'belajar ', '2025-02-24', 'Cukup Penting', 'open', 14),
(15, 'kungfu', '2025-03-07', 'Cukup Penting', 'open', 18),
(16, 'makan', '2025-04-30', 'Biasa', 'open', 20);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(3, 'nairaa', '$2y$10$sLNHjrfxu9gJc/kbQ3KPduWNQxBMr12Y0yHVGufElQhpfh4EKFFJ6', 'naira@gmail.com'),
(14, 'likara', '$2y$10$QFRwTEvi1voHHqcohA7umOVe00vbNXJjZr7pz2uaFZqZcqf9Rd6YW', 'lika@gmail.com'),
(15, 'irma', '$2y$10$tCIvH6/uX4/ujYtyEVun2u7Y3X7V.EG5htD/FYBjxDPDq5jelXEuG', 'irmanuryanti75@gmail.com'),
(17, 'udin', '$2y$10$vAwPPRK79yq0YdLIQ1vAYeaBwof.3rVYyWa4YcNvM7un3QZ1th4Ci', 'udinrohman@gmail.com'),
(18, 'upin', '$2y$10$lYsEO8ViInu2ltynyrU0M.wPOtG6p4FfSUE5VdU2mqc6H3U3hhRWu', 'upin@gmail.com'),
(19, 'putri', '$2y$10$KXz1DfQcH3FNhJHCdiy/9.XFjjSiO9.oQdHCmHLwhlSXqeQNS9NZS', 'putput@gmail.com'),
(20, 'putput', '$2y$10$KKjfP4PlOd4IUO7.RMeIZe3IL19CpN1SKArTBCGcsG9hlPJcLtt/6', 'putput@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
