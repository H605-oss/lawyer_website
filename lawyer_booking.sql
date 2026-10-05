-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 11:45 AM
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
-- Database: `lawyer_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `section_name`, `title`, `description`, `image`, `created_at`) VALUES
(3, 'hero', 'About Us', ' we provide reliable legal services that protect our clients\' rights.\r\nOur experienced attorneys deliver personalized strategies with ', '1758545798_service.jpg', '2025-09-22 12:56:38'),
(7, 'mission', 'Our Mission', 'Our mission is to provide comprehensive and reliable legal services that protect the rights of our clients and ensure fair outcomes.', '1759420207_our_mission.jpg', '2025-10-02 15:50:07'),
(8, 'mission', 'Our Mission', 'Our mission is to provide comprehensive and reliable legal services that protect the rights of our clients and ensure fair outcomes...', '1759420396_1759420207_our_mission.jpg', '2025-10-02 15:53:16'),
(9, 'hero', 'About Us', 'we provide reliable legal services that protect our clients\' rights. Our exper...', '1759420695_1758545798_service.jpg', '2025-10-02 15:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `App_ID` int(11) NOT NULL,
  `App_Name` varchar(50) NOT NULL,
  `App_Law_ID` int(11) NOT NULL,
  `App_Date` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`App_ID`, `App_Name`, `App_Law_ID`, `App_Date`, `status`) VALUES
(1, 'Vishal', 1, '2022-07-26 12:25:00', 0),
(2, 'Hussian', 2, '2019-07-16 12:25:00', 2),
(3, 'Testing12', 2, '2021-07-30 10:23:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `full_address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `preferred_attorney` varchar(255) DEFAULT NULL,
  `legal_issue` text NOT NULL,
  `legal_matter` varchar(255) NOT NULL,
  `status` enum('pending','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_typing` datetime DEFAULT NULL,
  `last_typing_by` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `lawyer_id`, `full_name`, `full_address`, `phone_number`, `email_address`, `appointment_date`, `appointment_time`, `preferred_attorney`, `legal_issue`, `legal_matter`, `status`, `created_at`, `last_typing`, `last_typing_by`) VALUES
(8, 6, 14, 'Hajra Bibi', 'karachi', '02112262780', 'hajrabibi012003@gmail.com', '2025-09-12', '19:14:00', 'Farman', 'kfnhcmm,', 'Criminal Law', 'confirmed', '2025-09-25 10:14:39', NULL, NULL),
(9, 6, 15, 'Hajra Bibi', 'Sohrab goth Scheem 33 saindad goth house no 12\\c', '03112262780', 'hajrabibi012003@gmail.com', '2025-10-11', '21:48:00', 'Sadiq', 'ljhj', 'Criminal Law', 'pending', '2025-10-08 16:44:21', NULL, NULL),
(10, 6, 15, 'Hajra Bibi', 'Sohrab goth Scheem 33 saindad goth house no 12\\c', '03112262780', 'hajrabibi012003@gmail.com', '2025-10-18', '21:59:00', 'Sadiq', 'jhjk', 'Criminal Law', 'rejected', '2025-10-08 16:55:41', NULL, NULL),
(11, 7, 15, 'hooriashireen', 'Sohrab goth Scheem 33 saindad goth house no 12\\c', '03112262780', 'hooriashireen0@gmail.com', '2025-10-31', '22:06:00', 'Sadiq', 'm,hjj', 'Real Estate Law', 'rejected', '2025-10-08 17:04:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `Book_ID` int(11) NOT NULL,
  `Law_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `requirements` text NOT NULL,
  `Book_Date` timestamp NULL DEFAULT NULL,
  `Book_Status` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`Book_ID`, `Law_ID`, `User_ID`, `requirements`, `Book_Date`, `Book_Status`, `created_at`) VALUES
(3, 7, 22, 'wqe', '2021-08-31 19:37:00', 2, '2021-08-02 19:38:45'),
(4, 1, 22, 'qweqw', '2021-08-02 20:02:00', 1, '2021-08-02 20:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_sections`
--

CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_sections`
--

INSERT INTO `homepage_sections` (`id`, `section_name`, `heading`, `content`, `image`, `button_text`, `button_link`) VALUES
(1, 'Hero', 'Need Legal Help?', 'Schedule your free consultation today.\r\n\r\nOur expert lawyers are ready to assist you with personalized legal solutions.', 'images/68d25ef14e06e_backgroung.jpg', 'Contact Us', 'contactus.php'),
(2, 'cta', 'Need Legal Help?', 'Schedule your free consultation today.\r\n\r\nOur expert lawyers are ready to assist you with personalized legal solutions.', '', 'Contact Us', 'contactus.php');

-- --------------------------------------------------------

--
-- Table structure for table `lawyers`
--

CREATE TABLE `lawyers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `specialty` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lawyers`
--

INSERT INTO `lawyers` (`id`, `name`, `profile_image`, `email`, `password`, `specialty`, `location`, `bio`, `created_at`, `profile_pic`) VALUES
(13, 'Mustafa', 'lawyer_68d4f3ac50f977.13726614.jpg', 'mustafa123@gmail.com', '$2y$10$YoT.Jp/9fCYQTam4Xt4O6u55QSnjMMoP6BKE.kcqWirjBsZPFJbJu', 'crime', 'karachi', 'won only two cases only', '2025-09-25 07:47:56', NULL),
(14, 'Farman', 'noman.jpg', 'farman123@gmail.com', '$2y$10$NOQLy9ktRURi5PKdNJxNj.visn6WiCEdsCgf1PCOvcAObOdnqrdUa', 'family', 'north nazimaabd', 'two cases won', '2025-09-25 10:13:21', NULL),
(15, 'Sadiq', 'lawyer_68db780ae30bb5.27240426.jpg', 'sadiq123@gmail.com', '$2y$10$NtM2ETvhNFLlTAudrl/GuOhb9Z3iKEDgAfWmzLtugAuCpyhDfbvLu', 'family', 'north nazimaabd', '-----', '2025-09-30 06:26:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `sender_type` enum('lawyer','client') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `appointment_id`, `sender_type`, `sender_id`, `message`, `created_at`) VALUES
(8, 8, 'client', 6, 'hi', '2025-10-09 06:40:52'),
(9, 8, 'client', 6, 'hi', '2025-10-09 06:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `expertise` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `subtitle`, `description`, `expertise`) VALUES
(2, 'Employment Law', 'Workplace Protection', 'Employee rights, workplace disputes, employer defense.', 'Employee'),
(4, 'Corporate Law', 'Trusted Advisors for Your Business', 'From startups to corporations, we deliver legal clarity in contracts', 'compliance, governance'),
(7, 'Criminal Defense', 'Strong Advocacy When It Matters Most', 'Defending from minor charges to serious felonies.', 'all criminal matters'),
(10, 'Family Law', 'Protecting Families, Securing Futures', 'Compassionate handling of guardianship with child-focused outcomes.', 'custody, planning');

-- --------------------------------------------------------

--
-- Table structure for table `typing_status`
--

CREATE TABLE `typing_status` (
  `appointment_id` int(11) NOT NULL,
  `sender_type` enum('lawyer','client') NOT NULL,
  `last_typing` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `typing_status`
--

INSERT INTO `typing_status` (`appointment_id`, `sender_type`, `last_typing`) VALUES
(12, 'client', '2025-10-09 09:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('customer','lawyer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_type`, `created_at`, `profile_pic`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin123', 'admin', '2025-09-20 06:49:41', NULL),
(2, 'hajra', 'hajra123@gmail.com', 'hajra123', 'customer', '2025-09-08 09:50:26', NULL),
(3, 'Hajra Bibi', 'hajra0202@gmail.com', 'hajra0202', 'customer', '2025-09-08 10:03:06', NULL),
(4, 'noman', 'nomanmuneercheema@gmail.com', 'noman00', 'customer', '2025-09-08 10:04:56', NULL),
(5, 'usman', 'usman00@gmail.com', 'usman00', 'customer', '2025-09-08 10:34:10', NULL),
(6, 'Hajra Bibi', 'hajrabibi012003@gmail.com', 'hajra0202', 'customer', '2025-09-08 16:03:01', NULL),
(7, 'hooriashireen', 'hooriashireen0@gmail.com', 'beesfeb345', 'customer', '2025-09-09 10:25:35', NULL),
(8, 'tehzeeb', 'tehzeebsheraz77@gmail.com', '123', 'customer', '2025-09-11 16:03:16', NULL),
(9, 'iqra', 'iqra123@gmail.com', '123', 'lawyer', '2025-09-17 16:05:06', NULL),
(11, 'iqracheema@gmail.com', 'iqracheema123@gmail.com', '123', 'lawyer', '2025-09-20 07:20:33', NULL),
(12, 'noman', 'nomaan@gmail.com', '123', 'lawyer', '2025-09-20 07:24:59', NULL),
(14, 'Noman mumtaaz', 'nomanmumtaaz123@gmail.com', '$2y$10$D87add9/M6SLAJbrJdpjHOM4jRo2fRIAx3tKs/zbE8E.j9teM/7FO', 'lawyer', '2025-09-20 07:53:15', NULL),
(16, 'Noman Cheema', 'nomankhan123@gmail.com', '$2y$10$zCvcEUn3AWdB9yDdsb2.uOSMf7YtJv0WR1VgLJFnf3Z2fNoYmT4sm', 'lawyer', '2025-09-25 07:37:56', NULL),
(18, 'Salman', 'salman123@gmail.com', '$2y$10$7oyZEizPlJLjuSzgMFCF7.zi2iwnmFP01oKC7GaEFwjyZFPB4FlH.', 'lawyer', '2025-09-25 07:43:11', NULL),
(19, 'Mustafa', 'mustafa123@gmail.com', '$2y$10$YoT.Jp/9fCYQTam4Xt4O6u55QSnjMMoP6BKE.kcqWirjBsZPFJbJu', 'lawyer', '2025-09-25 07:47:56', NULL),
(20, 'Farman', 'farman123@gmail.com', '$2y$10$NOQLy9ktRURi5PKdNJxNj.visn6WiCEdsCgf1PCOvcAObOdnqrdUa', 'lawyer', '2025-09-25 10:13:21', NULL),
(21, 'Sadiq', 'sadiq123@gmail.com', '$2y$10$NtM2ETvhNFLlTAudrl/GuOhb9Z3iKEDgAfWmzLtugAuCpyhDfbvLu', 'lawyer', '2025-09-30 06:26:18', NULL),
(22, 'Babar', 'mrsb12285@gmail.com', '$2y$10$3ZZfe9tV4LImGxzQi/4xreCXkHIQNpMKyDG62Xi/HRkmtkm/ydd/m', 'lawyer', '2025-10-09 06:06:04', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lawyer_id` (`lawyer_id`);

--
-- Indexes for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lawyers`
--
ALTER TABLE `lawyers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `typing_status`
--
ALTER TABLE `typing_status`
  ADD PRIMARY KEY (`appointment_id`,`sender_type`);

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
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `homepage_sections`
--
ALTER TABLE `homepage_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lawyers`
--
ALTER TABLE `lawyers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
