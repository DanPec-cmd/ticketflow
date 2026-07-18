-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 18, 2026 at 04:54 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elatus_tickets`
--

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`id`, `ticket_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 1, 'Ne radi ni nama', '2026-07-18 09:40:21'),
(2, 1, 1, 'Radim na tome', '2026-07-18 09:40:43'),
(3, 1, 1, 'Popravljeno', '2026-07-18 09:41:05'),
(4, 2, 1, 'popravljamo test', '2026-07-18 10:12:46'),
(5, 2, 1, 'test radi', '2026-07-18 10:12:52'),
(6, 3, 1, 'test odgovor za bla bla bla', '2026-07-18 10:13:39'),
(7, 3, 1, 'bla bla bla popravljen', '2026-07-18 10:13:53'),
(8, 4, 1, 'gdfge', '2026-07-18 10:25:28'),
(9, 6, 1, 'Tapš tapš', '2026-07-18 10:45:16'),
(10, 6, 1, 'Probaj ne micati ruku', '2026-07-18 10:45:57'),
(11, 6, 1, 'Satra riješila problem', '2026-07-18 10:46:30'),
(12, 4, 2, 'bls', '2026-07-18 11:15:23'),
(13, 4, 3, 'test', '2026-07-18 12:13:13'),
(14, 8, 3, 'dd', '2026-07-18 14:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_to` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `assigned_to`) VALUES
(1, 1, 'Ne radi mi mail', 'Zašto Vas ja plaćam svaki mjesec? Ne radi mi mail!', 'closed', '2026-07-18 09:33:10', NULL),
(2, 1, 'test', 'test', 'closed', '2026-07-18 10:12:30', NULL),
(3, 1, 'Test 2 točan prikaz statusa na glavnom popisu', 'bla bla bla', 'closed', '2026-07-18 10:13:26', NULL),
(4, 1, 'test', 'test', 'in_progress', '2026-07-18 10:24:56', 2),
(5, 2, 'test', 'bllja', 'open', '2026-07-18 10:25:11', 2),
(6, 1, 'Boli me', 'Boli me jako ruka u laktu. Lijeva. Ne radim ništa na temu toga.', 'closed', '2026-07-18 10:43:37', 2),
(7, 1, 'kreiran od test korisnika', 'test za dodjelu', 'open', '2026-07-18 14:18:49', 2),
(8, 2, 'test od agenta', 'test', 'in_progress', '2026-07-18 14:21:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','agent','pm') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Test Klijent', 'test@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', '2026-07-18 09:15:47'),
(2, 'Danimir', 'danimir@bla.com', '$2y$10$znsaDQUCHGxtYwW3OsauSu1IfMkNrYH6zp0ZI8hUhqBCel1JCjqp2', 'agent', '2026-07-18 10:17:51'),
(3, 'Admin', 'admin@admin.com', '$2y$10$TFK5pKLIp9TF1T2zNdQHUeH.82XqHaSv56n4/vyWV5BT8/EtLy/3i', 'pm', '2026-07-18 12:12:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

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
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
