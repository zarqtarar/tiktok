-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2024 at 11:55 PM
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
-- Database: `videos1`
--

-- --------------------------------------------------------

--
-- Table structure for table `agerating`
--

CREATE TABLE `agerating` (
  `id` int(11) NOT NULL,
  `rating_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agerating`
--

INSERT INTO `agerating` (`id`, `rating_name`) VALUES
(1, 'G'),
(2, 'PG'),
(3, 'PG-13'),
(4, 'R'),
(5, 'NC-17');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `video_id` int(11) DEFAULT NULL,
  `commenter_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `upload_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `video_id`, `commenter_id`, `comment`, `upload_datetime`) VALUES
(1, 1, 2, 'Mind-bending movie!', '2024-05-04 15:56:55'),
(2, 1, 4, 'One of my all-time favorites', '2024-05-04 15:56:55'),
(3, 2, 1, 'Heath Ledger was phenomenal as the Joker', '2024-05-04 15:56:55'),
(4, 3, 3, 'Classic movie with an unforgettable ending', '2024-05-04 15:56:55'),
(5, 4, 5, 'Tom Hanks delivered an outstanding performance', '2024-05-04 15:56:55'),
(6, 5, 2, 'Marlon Brando was brilliant in this role', '2024-05-04 15:56:55'),
(7, 7, 6, 'Inshallah', NULL),
(8, 7, 6, 'test', '2024-05-04 21:47:54'),
(9, 7, 6, 'hello', '2024-05-04 21:50:22'),
(10, 7, 6, 'hello', '2024-05-04 21:56:28'),
(11, 7, 6, 'Hello', '2024-05-04 22:01:04'),
(12, 7, 7, 'Ots me', '2024-05-04 22:02:48'),
(13, 1, 7, 'hello', '2024-05-04 22:29:48'),
(14, 5, 7, 'hello', '2024-05-04 22:40:05'),
(15, 6, 7, 'hello', '2024-05-04 22:41:36'),
(16, 3, 7, 'hello', '2024-05-04 22:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `dislikes`
--

CREATE TABLE `dislikes` (
  `id` int(11) NOT NULL,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `dislike_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dislikes`
--

INSERT INTO `dislikes` (`id`, `video_id`, `user_id`, `dislike_datetime`) VALUES
(1, 1, 4, '2024-05-04 15:56:55'),
(2, 2, 5, '2024-05-04 15:56:55'),
(3, 4, 3, '2024-05-04 15:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `genre_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `genre_name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Comedy'),
(4, 'Drama'),
(5, 'Fantasy'),
(6, 'Horror'),
(7, 'Mystery'),
(8, 'Romance'),
(9, 'Sci-Fi'),
(10, 'Thriller');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `video_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `like_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `video_id`, `user_id`, `like_datetime`) VALUES
(1, 1, 3, '2024-05-04 15:56:55'),
(2, 2, 4, '2024-05-04 15:56:55'),
(3, 3, 1, '2024-05-04 15:56:55'),
(4, 3, 5, '2024-05-04 15:56:55'),
(5, 4, 2, '2024-05-04 15:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `FName` varchar(255) NOT NULL,
  `LName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ContactNumber` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `FName`, `LName`, `Email`, `ContactNumber`) VALUES
(1, 'ali_khan', 'password123', 'Ali', 'Khan', 'ali@example.com', '1234567890'),
(2, 'fatima_ahmed', 'password456', 'Fatima', 'Ahmed', 'fatima@example.com', '0987654321'),
(3, 'zainab_abbas', 'password789', 'Zainab', 'Abbas', 'zainab@example.com', '9876543210'),
(4, 'saad_khalid', 'password321', 'Saad', 'Khalid', 'saad@example.com', '4567890123'),
(5, 'sana_malik', 'password654', 'Sana', 'Malik', 'sana@example.com', '3210987654'),
(6, 'Nabeel', '1234567', 'Nabeel', 'Aslam', 'n@yahoo.com', '1234567'),
(7, 'Sharjeel', '1234567', 'Sharjeel', 'Aslam', 's@yahoo.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `Publisher` varchar(255) DEFAULT NULL,
  `Producer` varchar(255) DEFAULT NULL,
  `Genre` varchar(100) DEFAULT NULL,
  `AgeRating` varchar(10) DEFAULT NULL,
  `upload_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `filename`, `thumbnail`, `uploader_id`, `Publisher`, `Producer`, `Genre`, `AgeRating`, `upload_datetime`) VALUES
(1, 'Inception', 'A thief who enters the dreams of others to steal their secrets', 'inception.mp4', 'inception_thumbnail.jpg', 1, 'Warner Bros. Pictures', 'Christopher Nolan', 'Thriller', 'PG-13', '2024-05-04 15:56:55'),
(2, 'The Dark Knight', 'Batman must confront the Joker to save Gotham City from destruction', 'dark_knight.mp4', 'dark_knight_thumbnail.jpg', 2, 'Warner Bros. Pictures', 'Christopher Nolan', 'Action', 'PG-13', '2024-05-04 15:56:55'),
(3, 'The Shawshank Redemption', 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency', 'shawshank_redemption.mp4', 'shawshank_thumbnail.jpg', 3, 'Columbia Pictures', 'Frank Darabont', 'Drama', 'R', '2024-05-04 15:56:55'),
(4, 'Forrest Gump', 'The story depicts several decades in the life of Forrest Gump, a slow-witted but kind-hearted man from Alabama who witnesses and unwittingly influences several defining historical events in the 20th century United States', 'forrest_gump.mp4', 'forrest_gump_thumbnail.jpg', 4, 'Paramount Pictures', 'Robert Zemeckis', 'Drama', 'PG-13', '2024-05-04 15:56:55'),
(5, 'The Godfather', 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son', 'godfather.mp4', 'godfather_thumbnail.jpg', 5, 'Paramount Pictures', 'Francis Ford Coppola', 'Crime', 'R', '2024-05-04 15:56:55'),
(6, 'Nabeel', 'Test', '101.mp3', '1.png', 6, 'test', 'Test', 'Action', 'PG-13', '2024-05-04 17:07:43'),
(7, 'Sharjeel', 'test', '555.mp3', 'Aysha-passport.JPG', 6, 'test', 'test', 'Mystery', 'PG-13', '2024-05-04 17:28:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agerating`
--
ALTER TABLE `agerating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `commenter_id` (`commenter_id`);

--
-- Indexes for table `dislikes`
--
ALTER TABLE `dislikes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agerating`
--
ALTER TABLE `agerating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `dislikes`
--
ALTER TABLE `dislikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`commenter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `dislikes`
--
ALTER TABLE `dislikes`
  ADD CONSTRAINT `dislikes_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`),
  ADD CONSTRAINT `dislikes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
