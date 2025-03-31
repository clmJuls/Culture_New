-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2025 at 09:57 AM
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
-- Database: `blog_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment_text`, `created_at`) VALUES
(36, 44, 6, 'asd', '2025-03-17 07:37:06'),
(37, 47, 6, 'test', '2025-03-17 07:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `culture_posts`
--

CREATE TABLE `culture_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `culture_posts`
--

INSERT INTO `culture_posts` (`id`, `title`, `description`, `content`, `image_url`, `category`, `user_id`, `created_at`, `updated_at`) VALUES
(14, 'Test me', 'test', 'test', 'uploads/culture/67cc21cb8d555_481234611_122140772222562748_6460917983758228935_n.jpg', 'traditions', 3, '2025-03-08 10:54:03', '2025-03-08 10:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `demographics_posts`
--

CREATE TABLE `demographics_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geography_posts`
--

CREATE TABLE `geography_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `geography_posts`
--

INSERT INTO `geography_posts` (`id`, `title`, `description`, `content`, `image_url`, `user_id`, `created_at`, `updated_at`) VALUES
(12, 'test', 'test', 'test', 'uploads/geography/67d7c09c9a324.png', 6, '2025-03-17 06:26:37', '2025-03-17 06:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `history_posts`
--

CREATE TABLE `history_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_posts`
--

INSERT INTO `history_posts` (`id`, `title`, `description`, `content`, `category`, `image_url`, `user_id`, `created_at`, `updated_at`) VALUES
(10, 'asdasd', 'asdasd', 'asdasd', 'movements', 'uploads/history/67d7c0b72b8fc.png', 6, '2025-03-17 06:27:03', '2025-03-17 06:27:03');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `user_id`, `is_active`, `created_at`) VALUES
(45, 52, 3, 1, '2025-03-30 15:05:38'),
(46, 53, 3, 1, '2025-03-30 15:05:38'),
(47, 55, 3, 1, '2025-03-30 15:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `culture_elements` varchar(255) DEFAULT NULL,
  `learning_styles` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `description`, `file_path`, `culture_elements`, `learning_styles`, `status`, `created_at`) VALUES
(52, 3, 'admin', 'test', 'uploads/67e95037abf5f_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Visual', 'approved', '2025-03-30 14:07:51'),
(53, 4, 'user', 'test', 'uploads/67e95079e03fc_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Visual', 'approved', '2025-03-30 14:08:57'),
(54, 4, 'all type', 'test', 'uploads/67e950adaa508_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Visual,Auditory & Oral,Read & Write,Kinesthetic', 'pending', '2025-03-30 14:09:49'),
(55, 3, 'test', 'test', 'uploads/67e95c21bbb21_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Kinesthetic', 'approved', '2025-03-30 14:58:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `about` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `isPremium` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `full_name`, `about`, `location`, `birthday`, `website`, `skills`, `profile_picture`, `isAdmin`, `isPremium`) VALUES
(1, 'superadmin@gmail.com', 'admin', '$2y$10$GUeBKow2oHnd6EpwSZ.I/OSP26532YiTySW6FUGkvCC12RhiTbwJS', 'admin account', NULL, '', NULL, '', NULL, 'uploads/RobloxScreenShot20241027_124339467.png', 1, 0),
(2, 'jamesdy02@gmail.com', 'james', '$2y$10$AjiUxyVF3OiJx4bkZ6mBv.eBQOjZoaOxlltzLKBF1RbckLZeNSfO.', '', NULL, '', NULL, '', NULL, 'uploads/Screenshot 2025-02-12 205955.png', 0, 0),
(3, 'clmjuls25@gmail.com', 'clmjuls', '$2y$10$aKByFvJ0pBhQ52be94Cn8OtThOltI7NSNIMw.oEnXlrTrYT4xaXf2', '', NULL, '', NULL, '', NULL, 'uploads/41a357c3028363d1b6962ab77e0bbdc5.jpg', 1, 0),
(4, 'mjbcoloma@gmail.com', 'juls', '$2y$10$wB41R.BOb6IJw42BXt983uOZFixQvcxAVhbK9MINr5QhsMU4nxm5C', '', NULL, '', NULL, '', NULL, 'uploads/WIN_20221222_18_40_16_Pro.jpg', 0, 0),
(5, 'clmjuls@gmail.com', 'testtest', '$2y$10$/98I0lHWEqgKQvyCZjmRE.qEk2H.9IwwECT/zqgeyAiOMzcxWZ792', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(6, 'jamesdy03@gmail.com', 'James3', '$2y$10$JoSm77EkRSBDbTJmgXoEweypPuJ0A33pkmPwcFDIddLDzc1cRbEGm', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(7, 'asdasd@asdasd.com', 'James4', '$2y$10$zE/7klEQb9muzTI2IyndTupwe1AnKBo6qIl244tZqyL7iszi4X8w2', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `culture_posts`
--
ALTER TABLE `culture_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `demographics_posts`
--
ALTER TABLE `demographics_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `geography_posts`
--
ALTER TABLE `geography_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `history_posts`
--
ALTER TABLE `history_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_post_like` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at_index` (`created_at`),
  ADD KEY `user_posts_index` (`user_id`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `culture_posts`
--
ALTER TABLE `culture_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `demographics_posts`
--
ALTER TABLE `demographics_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `geography_posts`
--
ALTER TABLE `geography_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `history_posts`
--
ALTER TABLE `history_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `culture_posts`
--
ALTER TABLE `culture_posts`
  ADD CONSTRAINT `culture_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `demographics_posts`
--
ALTER TABLE `demographics_posts`
  ADD CONSTRAINT `demographics_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `geography_posts`
--
ALTER TABLE `geography_posts`
  ADD CONSTRAINT `geography_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `history_posts`
--
ALTER TABLE `history_posts`
  ADD CONSTRAINT `history_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
