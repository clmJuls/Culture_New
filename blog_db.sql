-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 12:28 PM
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
(36, 50, 3, 'test', '2025-03-08 11:16:05'),
(37, 45, 3, 'test', '2025-03-08 11:20:20');

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
(12, 'ttest', 'test', 'test', 'uploads/geography/67c95aca4c408.png', 3, '2025-03-06 08:20:26', '2025-03-06 08:20:26'),
(14, 'test', 'test', 'test', 'uploads/geography/67cc1e7052ac9.jpg', 3, '2025-03-08 10:39:44', '2025-03-08 10:39:44');

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
(10, 'test', 'test', 'test', 'ancient', 'uploads/history/67cc248d55d92.jpg', 3, '2025-03-08 11:05:49', '2025-03-08 11:05:49');

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
(45, 51, 3, 1, '2025-03-08 11:20:45'),
(46, 52, 3, 1, '2025-03-08 11:20:52'),
(48, 52, 4, 1, '2025-03-08 11:21:11'),
(49, 50, 4, 1, '2025-03-08 11:22:40'),
(51, 45, 4, 1, '2025-03-08 11:22:54'),
(52, 51, 4, 1, '2025-03-08 11:23:18');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `description`, `file_path`, `culture_elements`, `learning_styles`, `created_at`) VALUES
(45, 3, 'test', 'test', 'uploads/67cc2573c4159_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Auditory & Oral', '2025-03-08 11:09:39'),
(46, 3, 'test', 'test', 'uploads/67cc25d284eb1_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Auditory & Oral,Kinesthetic', '2025-03-08 11:11:14'),
(47, 3, 'test', 'test', 'uploads/67cc25e0baba8_481234611_122140772222562748_6460917983758228935_n.jpg', 'History', 'Auditory & Oral', '2025-03-08 11:11:28'),
(48, 3, 'test', 'test', 'uploads/67cc25edf2569_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Auditory & Oral', '2025-03-08 11:11:41'),
(49, 3, 'test', 'test', 'uploads/67cc25f947760_481234611_122140772222562748_6460917983758228935_n.jpg', 'Geography', 'Auditory & Oral', '2025-03-08 11:11:53'),
(50, 3, 'test', 'test', 'uploads/67cc26020263c_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Visual', '2025-03-08 11:12:02'),
(51, 3, 'test', 'test', 'uploads/67cc260b01f50_481234611_122140772222562748_6460917983758228935_n.jpg', '', 'Read & Write', '2025-03-08 11:12:11'),
(52, 3, 'test', 'test', 'uploads/67cc279fe4ec4_blank-profile-picture-973460_960_720.webp', '', 'Visual', '2025-03-08 11:18:55');

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
  `profile_picture` varchar(255) DEFAULT 'user/assets/hero/v07_20@Shanks.png',
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
(5, 'clmjuls@gmail.com', 'testtest', '$2y$10$/98I0lHWEqgKQvyCZjmRE.qEk2H.9IwwECT/zqgeyAiOMzcxWZ792', '', NULL, NULL, NULL, NULL, NULL, 'user/assets/hero/v07_20@Shanks.png', 0, 0);

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
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `history_posts`
--
ALTER TABLE `history_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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

-- Add unique constraint to prevent duplicate likes from the same user on the same post
ALTER TABLE `likes` 
ADD UNIQUE KEY `unique_user_post_like` (`user_id`, `post_id`);

-- Add indexes to improve query performance and prevent duplicates
ALTER TABLE `posts`
ADD INDEX `created_at_index` (`created_at`),
ADD INDEX `user_posts_index` (`user_id`, `created_at`);

-- Add foreign key constraint for posts.user_id if not already present
ALTER TABLE `posts`
ADD CONSTRAINT `posts_ibfk_1` 
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
