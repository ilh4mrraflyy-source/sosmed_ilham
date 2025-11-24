-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2025 at 05:14 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_medsos`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `CommentID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `Text` text,
  `Date` date DEFAULT NULL,
  `Time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`CommentID`, `PostID`, `UserName`, `Text`, `Date`, `Time`) VALUES
(1, 13, 'ilham', 'keren', '2025-11-10', '01:49:23'),
(2, 13, 'ilham', 'keren', '2025-11-10', '01:52:52'),
(3, 12, 'ilham', 'ass', '2025-11-10', '01:54:02'),
(4, 12, 'ilham', 'ass', '2025-11-10', '01:57:14'),
(5, 14, 'rama', 'nego bang', '2025-11-10', '05:01:43'),
(6, 14, 'rama', '120 k', '2025-11-10', '05:01:49'),
(7, 15, 'ilham', 'nego bang', '2025-11-10', '05:14:45'),
(8, 17, 'rama', 'pppppppppp', '2025-11-17', '02:22:46'),
(9, 15, 'ilham', 'p', '2025-11-19', '02:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `detailpost`
--

CREATE TABLE `detailpost` (
  `PostID` int(11) NOT NULL,
  `ImageID` int(11) NOT NULL,
  `Comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detailpost`
--

INSERT INTO `detailpost` (`PostID`, `ImageID`, `Comment`) VALUES
(2, 1, ''),
(5, 2, ''),
(6, 3, ''),
(7, 4, ''),
(8, 5, ''),
(9, 6, ''),
(10, 7, ''),
(11, 8, ''),
(14, 9, ''),
(15, 10, ''),
(16, 11, ''),
(17, 12, '');

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE `follow` (
  `FollowID` int(11) NOT NULL,
  `FollowName` varchar(50) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `follow`
--

INSERT INTO `follow` (`FollowID`, `FollowName`, `UserName`) VALUES
(1, 'admin', 'dede'),
(2, 'ilham', 'dede'),
(3, 'opik', 'rama'),
(4, 'admin', 'ilham'),
(5, 'ilham', 'admin'),
(6, 'admin', 'sae'),
(7, 'dede', 'sae'),
(8, 'ilham', 'sae'),
(9, 'opik', 'sae'),
(10, 'rama', 'sae'),
(11, 'rpl', 'sae');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `ID` int(11) NOT NULL,
  `UserA` varchar(100) DEFAULT NULL,
  `UserB` varchar(100) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`ID`, `UserA`, `UserB`, `CreatedAt`) VALUES
(6, 'rama', 'ilham', '2025-11-10 11:56:01'),
(7, 'ilham', 'admin', '2025-11-10 12:06:47'),
(9, 'rpl', 'ilham', '2025-11-10 12:13:18'),
(10, 'rama', 'ilham', '2025-11-10 14:01:51'),
(11, 'opik', 'rama', '2025-11-10 14:02:13'),
(12, 'opik', 'rama', '2025-11-10 14:14:41'),
(13, 'opik', 'ilham', '2025-11-10 14:14:42'),
(14, 'dede', 'ilham', '2025-11-10 14:18:21'),
(15, 'lj', 'ilham', '2025-11-19 08:45:36'),
(16, 'ilham', 'sae', '2025-11-19 09:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `friend_request`
--

CREATE TABLE `friend_request` (
  `ID` int(11) NOT NULL,
  `Sender` varchar(100) NOT NULL,
  `Receiver` varchar(100) NOT NULL,
  `Status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `Date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `RequestID` int(11) NOT NULL,
  `FromUser` varchar(100) DEFAULT NULL,
  `ToUser` varchar(100) DEFAULT NULL,
  `Status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`RequestID`, `FromUser`, `ToUser`, `Status`, `CreatedAt`) VALUES
(1, 'ilham', 'opik', 'accepted', '2025-11-10 09:46:15'),
(2, 'ilham', 'rama', 'accepted', '2025-11-10 09:46:29'),
(3, 'admin', 'ilham', 'accepted', '2025-11-10 12:06:34'),
(4, 'ilham', 'rpl', 'accepted', '2025-11-10 12:12:53'),
(5, 'rama', 'opik', 'accepted', '2025-11-10 14:02:01'),
(6, 'ilham', 'dede', 'accepted', '2025-11-10 14:18:10'),
(7, 'sae', 'ilham', 'accepted', '2025-11-11 08:52:28'),
(8, 'ilham', 'lj', 'accepted', '2025-11-19 08:29:11');

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `ImageID` int(11) NOT NULL,
  `ImageName` varchar(100) DEFAULT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`ImageID`, `ImageName`, `Date`) VALUES
(1, '1762317847_download-removebg-preview (19).png', '2025-11-05'),
(2, '1762318151_download-removebg-preview (22).png', '2025-11-05'),
(3, '1762318287_download-removebg-preview (22).png', '2025-11-05'),
(4, '1762318833_749d1e1b71343d924a399887f4b2de19-removebg-preview.png', '2025-11-05'),
(5, '1762319396_430e12c8c466cdf19cb6cfc72fd8906b-removebg-preview.png', '2025-11-05'),
(6, '1762477782_download (6).jpg', '2025-11-07'),
(7, '1762736499_download (3).jpg', '2025-11-10'),
(8, '1762737551_download (7).jpg', '2025-11-10'),
(9, '1762740049_6c1de1b025a26c65189e5b2a1b326172-removebg-preview.png', '2025-11-10'),
(10, '1762751315_download-removebg-preview (17).png', '2025-11-10'),
(11, '1762825977_download (7).jpg', '2025-11-11'),
(12, '1763342990_Mad One Piece GIF by Toei Animation (1).gif', '2025-11-17');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `LikeID` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `UserName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`LikeID`, `PostID`, `UserName`) VALUES
(4, 13, 'ilham'),
(5, 10, 'ilham'),
(6, 8, 'ilham'),
(7, 12, 'ilham'),
(8, 14, 'ilham'),
(12, 9, 'ilham'),
(13, 14, 'rama'),
(14, 13, 'rama'),
(15, 12, 'rama'),
(16, 11, 'rama'),
(17, 1, 'rama'),
(18, 14, 'admin'),
(19, 14, 'rpl'),
(20, 15, 'ilham'),
(21, 15, 'rama'),
(22, 3, 'sae'),
(24, 16, 'ilham'),
(25, 17, 'rama'),
(26, 17, 'lj'),
(27, 18, 'lj'),
(28, 14, 'lj'),
(29, 18, 'ilham');

-- --------------------------------------------------------

--
-- Table structure for table `marketplace`
--

CREATE TABLE `marketplace` (
  `ItemID` int(11) NOT NULL,
  `Seller` varchar(50) DEFAULT NULL,
  `Title` varchar(100) DEFAULT NULL,
  `Price` int(11) DEFAULT NULL,
  `Description` text,
  `Category` varchar(50) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `marketplace`
--

INSERT INTO `marketplace` (`ItemID`, `Seller`, `Title`, `Price`, `Description`, `Category`, `Photo`, `CreatedAt`) VALUES
(1, 'ilham', 'gg', 1234, 'gg', 'Elektronik', '1763343524_download (9).jpg', '2025-11-17 08:38:44'),
(2, 'ilham', 'hp', 1000000, 'poco f7', 'Elektronik', '1763343993_download (10).jpg', '2025-11-17 08:46:33'),
(3, 'rama', 'Pemotong rumput s8000', 2147483647, 'mantap bang', 'Perabot', '1763344697_download (11).jpg', '2025-11-17 08:58:17'),
(4, 'rama', 'cys', 231, 'gag', 'Perabot', '1763345720_gggggg.jpg', '2025-11-17 09:15:20'),
(5, 'rama', 'sapidermen', 2009982, 'sapi mantap bang', 'Perabot', '1763345789_download (12).jpg', '2025-11-17 09:16:29'),
(6, 'ilham', 'Baju', 12000, 'baju sajadah', 'Fashion', '1763345875_shopping.jpg', '2025-11-17 09:17:55'),
(7, 'rama', 'Ferrari sf90', 2147483647, 'no minus', 'Kendaraan', '1763346084_download (13).jpg', '2025-11-17 09:21:24'),
(8, 'lj', 'Mclaren lu warna apa bos', 2000000, 'minusan', 'Kendaraan', '1763515858_download (15).jpg', '2025-11-19 08:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `MessageID` int(11) NOT NULL,
  `FromUser` varchar(50) DEFAULT NULL,
  `ToUser` varchar(50) DEFAULT NULL,
  `MessageText` text,
  `DateSent` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`MessageID`, `FromUser`, `ToUser`, `MessageText`, `DateSent`) VALUES
(1, 'opik', 'dede', 'gg', '2025-11-05 05:15:12'),
(3, 'opik', 'admin', 'gg', '2025-11-05 05:19:30'),
(4, 'rama', 'opik', 'oi pik', '2025-11-07 01:08:51'),
(5, 'admin', 'dede', 'ggggg', '2025-11-10 01:01:52'),
(6, 'ilham', 'rama', 'ggggg', '2025-11-10 04:50:45'),
(7, 'rama', 'ilham', 'ggggg', '2025-11-10 05:09:05'),
(8, 'rama', 'ilham', 'alek', '2025-11-10 05:09:13'),
(9, 'ilham', 'rama', 'oyyyyyy', '2025-11-10 05:12:09'),
(10, 'rama', 'ilham', 'hallooo', '2025-11-10 05:12:33'),
(11, 'ilham', 'rpl', 'hallooo', '2025-11-10 05:47:04'),
(12, 'ilham', 'rama', 'ram', '2025-11-10 07:01:20'),
(13, 'ilham', 'rpl', 'p', '2025-11-10 07:13:28'),
(14, 'ilham', 'opik', 'oi pik', '2025-11-10 07:15:19'),
(15, 'opik', 'ilham', 'pppp', '2025-11-17 00:55:59'),
(16, 'opik', 'ilham', 'rajol dimarih', '2025-11-17 00:56:03'),
(17, 'opik', 'rama', 'oi', '2025-11-17 02:20:00'),
(18, 'ilham', 'admin', 'pppp', '2025-11-19 01:39:59'),
(19, 'lj', 'ilham', 'p', '2025-11-19 01:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `message_requests`
--

CREATE TABLE `message_requests` (
  `ID` int(11) NOT NULL,
  `FromUser` varchar(100) NOT NULL,
  `ToUser` varchar(100) NOT NULL,
  `MessageText` text NOT NULL,
  `DateSent` datetime DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `message_requests`
--

INSERT INTO `message_requests` (`ID`, `FromUser`, `ToUser`, `MessageText`, `DateSent`, `Status`) VALUES
(1, 'ilham', 'opik', 'p', '2025-11-19 01:40:22', 'pending'),
(2, 'ilham', 'opik', 'p', '2025-11-19 01:40:26', 'pending'),
(3, 'ilham', 'lj', 'pp', '2025-11-19 01:44:49', 'accepted'),
(4, 'lj', 'ilham', 'p', '2025-11-19 01:45:13', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `notif`
--

CREATE TABLE `notif` (
  `NotifID` int(11) NOT NULL,
  `FromUser` varchar(100) DEFAULT NULL,
  `ToUser` varchar(100) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Message` text,
  `IsRead` tinyint(1) DEFAULT '0',
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notif`
--

INSERT INTO `notif` (`NotifID`, `FromUser`, `ToUser`, `Type`, `Message`, `IsRead`, `CreatedAt`) VALUES
(1, 'ilham', 'rama', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:32:01'),
(2, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:35:34'),
(3, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:40:34'),
(4, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:40:37'),
(5, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:40:40'),
(6, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 09:40:48'),
(7, 'ilham', 'opik', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 09:46:15'),
(8, 'ilham', 'rama', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 09:46:30'),
(9, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 09:49:35'),
(10, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 11:51:55'),
(11, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 11:52:49'),
(12, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 11:54:35'),
(13, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 11:54:51'),
(14, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 11:56:01'),
(15, 'rama', 'ilham', 'comment', 'mengomentari postingan kamu.', 1, '2025-11-10 12:01:43'),
(16, 'rama', 'ilham', 'comment', 'mengomentari postingan kamu.', 1, '2025-11-10 12:01:49'),
(17, 'admin', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 12:05:54'),
(18, 'admin', 'ilham', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 12:06:34'),
(19, 'ilham', 'admin', 'friend_accept', 'menerima permintaan pertemanan kamu', 0, '2025-11-10 12:06:47'),
(20, 'ilham', 'admin', 'friend_accept', 'menerima permintaan pertemanan kamu', 0, '2025-11-10 12:11:57'),
(21, 'ilham', 'rpl', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 12:12:53'),
(22, 'rpl', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 12:13:18'),
(23, 'rpl', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 12:13:32'),
(24, 'ilham', 'rama', 'comment', 'mengomentari postingan kamu.', 1, '2025-11-10 12:14:45'),
(25, 'ilham', 'rama', 'like', 'menyukai postingan kamu.', 1, '2025-11-10 12:37:50'),
(26, 'rama', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 14:01:51'),
(27, 'rama', 'opik', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 14:02:01'),
(28, 'opik', 'rama', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 14:02:13'),
(29, 'opik', 'rama', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 14:14:41'),
(30, 'opik', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 14:14:42'),
(31, 'ilham', 'dede', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-10 14:18:10'),
(32, 'dede', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 1, '2025-11-10 14:18:21'),
(33, 'sae', 'ilham', 'friend_request', 'mengirim permintaan pertemanan', 1, '2025-11-11 08:52:28'),
(34, 'sae', 'admin', 'like', 'menyukai postingan kamu.', 0, '2025-11-11 08:58:09'),
(35, 'ilham', 'sae', 'like', 'menyukai postingan kamu.', 0, '2025-11-17 07:56:32'),
(36, 'rama', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-17 09:22:40'),
(37, 'rama', 'ilham', 'comment', 'mengomentari postingan kamu.', 1, '2025-11-17 09:22:46'),
(38, 'lj', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-19 08:28:31'),
(39, 'lj', 'ilham', 'like', 'menyukai postingan kamu.', 1, '2025-11-19 08:28:33'),
(40, 'ilham', 'lj', 'friend_request', 'mengirim permintaan pertemanan', 0, '2025-11-19 08:29:11'),
(41, 'lj', 'ilham', 'like', 'menyukai postingan kamu.', 0, '2025-11-19 08:31:29'),
(42, 'lj', 'ilham', 'friend_accept', 'menerima permintaan pertemanan kamu', 0, '2025-11-19 08:45:36'),
(43, 'ilham', 'sae', 'friend_accept', 'menerima permintaan pertemanan kamu', 0, '2025-11-19 09:09:40'),
(44, 'ilham', 'rama', 'comment', 'mengomentari postingan kamu.', 1, '2025-11-19 09:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `NotifID` int(11) NOT NULL,
  `Receiver` varchar(100) NOT NULL,
  `Sender` varchar(100) NOT NULL,
  `Type` enum('friend_request','message','like','comment') NOT NULL,
  `Content` text,
  `Date` datetime DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `PostID` int(11) NOT NULL,
  `Date` date DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `Text` text,
  `UserName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`PostID`, `Date`, `Time`, `Text`, `UserName`) VALUES
(1, '2025-11-05', '04:30:02', 'rrar', 'ilham'),
(2, '2025-11-05', '04:44:07', '', 'admin'),
(3, '2025-11-05', '04:47:04', 'sdad', 'admin'),
(4, '2025-11-05', '04:49:02', 'sdad', 'admin'),
(5, '2025-11-05', '04:49:11', 'gg', 'admin'),
(6, '2025-11-05', '04:51:27', 'gg', 'admin'),
(7, '2025-11-05', '05:00:33', 'puma', 'admin'),
(8, '2025-11-05', '05:09:56', '200 nego', 'opik'),
(9, '2025-11-07', '01:09:42', 'dijual 2 m', 'rama'),
(10, '2025-11-10', '01:01:39', 'dede', 'admin'),
(11, '2025-11-10', '01:19:11', 'panutan', 'ilham'),
(12, '2025-11-10', '01:48:03', '', 'ilham'),
(13, '2025-11-10', '01:48:31', '', 'ilham'),
(14, '2025-11-10', '02:00:49', 'puma', 'ilham'),
(15, '2025-11-10', '05:08:35', 'kuda liar', 'rama'),
(16, '2025-11-11', '01:52:57', 'apapapp', 'sae'),
(17, '2025-11-17', '01:29:50', 'g', 'ilham'),
(18, '2025-11-17', '01:31:42', 'g', 'ilham');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `StoryID` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `ImageName` varchar(255) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ExpiresAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`StoryID`, `UserName`, `ImageName`, `CreatedAt`, `ExpiresAt`) VALUES
(1, 'ilham', '1763342720_images.jpg', '2025-11-17 08:25:20', '2025-11-18 01:25:20'),
(2, 'ilham', '1763343110_download__9_.jpg', '2025-11-17 08:31:50', '2025-11-18 01:31:50'),
(3, 'rama', '1763343132_836aa3dec8bb81dce4dd8661cfb04abf.jpg', '2025-11-17 08:32:12', '2025-11-18 01:32:12'),
(4, 'opik', '1763345918_images__1_.jpg', '2025-11-17 09:18:38', '2025-11-18 02:18:38'),
(5, 'ilham', '1763515434_download__14_.jpg', '2025-11-19 08:23:54', '2025-11-20 01:23:54'),
(6, 'rama', '1763515474_Choso_-_JJK.jpg', '2025-11-19 08:24:34', '2025-11-20 01:24:34'),
(7, 'lj', '1763515900_download__15_.jpg', '2025-11-19 08:31:40', '2025-11-20 01:31:40'),
(8, 'rpl', '1763515910_download__11_.jpg', '2025-11-19 08:31:50', '2025-11-20 01:31:50'),
(9, 'opik', '1763515923_gggggg.jpg', '2025-11-19 08:32:03', '2025-11-20 01:32:03'),
(10, 'dede', '1763515953_download-removebg-preview__25_.png', '2025-11-19 08:32:33', '2025-11-20 01:32:33'),
(11, 'ilham', '1763518155_download__15_.jpg', '2025-11-19 09:09:15', '2025-11-20 02:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `story_views`
--

CREATE TABLE `story_views` (
  `ID` int(11) NOT NULL,
  `StoryID` int(11) NOT NULL,
  `Viewer` varchar(100) NOT NULL,
  `ViewedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `story_views`
--

INSERT INTO `story_views` (`ID`, `StoryID`, `Viewer`, `ViewedAt`) VALUES
(1, 1, 'ilham', '2025-11-17 08:25:22'),
(2, 2, 'ilham', '2025-11-17 08:31:51'),
(3, 2, 'rama', '2025-11-17 08:32:05'),
(4, 3, 'ilham', '2025-11-17 08:32:19'),
(5, 3, 'rama', '2025-11-17 09:13:34'),
(6, 1, 'rama', '2025-11-17 09:13:41'),
(7, 4, 'opik', '2025-11-17 09:18:39'),
(8, 3, 'opik', '2025-11-17 09:18:46'),
(9, 2, 'opik', '2025-11-17 09:18:49'),
(10, 1, 'opik', '2025-11-17 09:18:52'),
(11, 4, 'rama', '2025-11-17 09:22:28'),
(12, 4, 'ilham', '2025-11-17 09:26:22'),
(13, 5, 'ilham', '2025-11-19 08:23:56'),
(14, 6, 'rama', '2025-11-19 08:24:34'),
(15, 6, 'lj', '2025-11-19 08:28:14'),
(16, 5, 'lj', '2025-11-19 08:28:17'),
(17, 9, 'opik', '2025-11-19 08:32:04'),
(18, 10, 'dede', '2025-11-19 08:32:38'),
(19, 9, 'dede', '2025-11-19 08:32:41'),
(20, 10, 'ilham', '2025-11-19 08:54:16'),
(21, 9, 'ilham', '2025-11-19 08:54:18'),
(22, 8, 'ilham', '2025-11-19 08:54:21'),
(23, 7, 'ilham', '2025-11-19 08:54:24'),
(24, 6, 'ilham', '2025-11-19 08:54:27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_groups`
--

CREATE TABLE `tbl_groups` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(100) NOT NULL,
  `Description` text,
  `GroupPhoto` varchar(255) DEFAULT NULL,
  `CreatedBy` varchar(100) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_groups`
--

INSERT INTO `tbl_groups` (`GroupID`, `GroupName`, `Description`, `GroupPhoto`, `CreatedBy`, `CreatedAt`) VALUES
(1, 'jual beli alok', 'gggg', '1762843720_download (7).jpg', 'ilham', '2025-11-11 13:48:40'),
(2, 'jual beli hp second', 'bogor', '1762844823_63d20055b42c2e1c9babdc45_1674708267.jpg', 'ilham', '2025-11-11 14:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_group_members`
--

CREATE TABLE `tbl_group_members` (
  `ID` int(11) NOT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `JoinedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_group_members`
--

INSERT INTO `tbl_group_members` (`ID`, `GroupID`, `Username`, `JoinedAt`) VALUES
(1, 1, 'ilham', '2025-11-11 13:48:40'),
(2, 1, 'rama', '2025-11-11 13:52:35'),
(3, 2, 'ilham', '2025-11-11 14:07:03'),
(4, 2, 'rama', '2025-11-11 14:07:29'),
(5, 2, 'opik', '2025-11-17 07:45:38'),
(6, 1, 'opik', '2025-11-17 07:45:42'),
(7, 2, 'lj', '2025-11-19 08:25:48'),
(8, 1, 'lj', '2025-11-19 08:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_group_posts`
--

CREATE TABLE `tbl_group_posts` (
  `PostID` int(11) NOT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `Content` text,
  `ImageName` varchar(255) DEFAULT NULL,
  `Text` text,
  `Image` varchar(255) DEFAULT NULL,
  `Date` datetime DEFAULT CURRENT_TIMESTAMP,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_group_posts`
--

INSERT INTO `tbl_group_posts` (`PostID`, `GroupID`, `Username`, `Content`, `ImageName`, `Text`, `Image`, `Date`, `CreatedAt`) VALUES
(1, 1, 'rama', 'fgggg', '', NULL, NULL, '2025-11-11 14:05:39', '2025-11-11 14:05:39'),
(2, 1, 'rama', 'hotel', '1762844759_Luxury Hotel Photography Spain - Precise Resort El Rompido Spain.jpg', NULL, NULL, '2025-11-11 14:05:59', '2025-11-11 14:05:59'),
(3, 1, 'ilham', 'alok nominus', '1763346290_download (14).jpg', NULL, NULL, '2025-11-17 09:24:50', '2025-11-17 09:24:50'),
(4, 2, 'lj', 'Poco X7 Pro 8/256Gb\r\nfullset\r\nnominus', '1763515596_download (10).jpg', NULL, NULL, '2025-11-19 08:26:36', '2025-11-19 08:26:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_messages`
--

CREATE TABLE `tbl_messages` (
  `MessageID` int(11) NOT NULL,
  `FromUser` varchar(100) DEFAULT NULL,
  `ToUser` varchar(100) DEFAULT NULL,
  `Message` text,
  `SentAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserName` varchar(50) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `Cover` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserName`, `FirstName`, `LastName`, `Email`, `Password`, `Photo`, `Cover`) VALUES
('admin', NULL, NULL, 'gggg@gmail.com', '1234', '1762751173_836aa3dec8bb81dce4dd8661cfb04abf.jpg', '1762751245_anime-anime-boys-jujutsu-kaisen-yuji-itadori-sakuna-hd-wallpaper-preview.jpg'),
('dede', NULL, NULL, 'deee@gmail.com', '123', '1762759265_download__7_.jpg', NULL),
('ilham', NULL, NULL, 'maildrivezonee@gmail.com', '123', '1762759054_gojo.jpg', '1762737475_jujut.jpg'),
('lee', NULL, NULL, 'fasw@g', '123', NULL, NULL),
('lj', NULL, NULL, 'gagak@gmail.com', '123', '1763515517_Choso_Kamo.jpg', '1763515536_9f25133e1df02c12fef409da0e3d8fdd-removebg-preview.png'),
('opik', NULL, NULL, 'mantap@gmail.com', '123', '1763340379_download__9_.jpg', '1763340403_images.jpg'),
('rama', NULL, NULL, 'ramagangkenari@gmail.com', '123', '1762751143_download__8_.jpg', '1762751281_jujutsuu.jpg'),
('rpl', NULL, NULL, 'maildrivezonee@gmail.com', '123', NULL, NULL),
('sae', NULL, NULL, 'sae@gmail.com', 'saeaja', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`);

--
-- Indexes for table `detailpost`
--
ALTER TABLE `detailpost`
  ADD PRIMARY KEY (`PostID`,`ImageID`),
  ADD KEY `ImageID` (`ImageID`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`FollowID`),
  ADD KEY `FollowName` (`FollowName`),
  ADD KEY `UserName` (`UserName`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `friend_request`
--
ALTER TABLE `friend_request`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`RequestID`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`ImageID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`LikeID`);

--
-- Indexes for table `marketplace`
--
ALTER TABLE `marketplace`
  ADD PRIMARY KEY (`ItemID`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `message_requests`
--
ALTER TABLE `message_requests`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `notif`
--
ALTER TABLE `notif`
  ADD PRIMARY KEY (`NotifID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`NotifID`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `UserName` (`UserName`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`StoryID`),
  ADD KEY `UserName` (`UserName`),
  ADD KEY `CreatedAt` (`CreatedAt`);

--
-- Indexes for table `story_views`
--
ALTER TABLE `story_views`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `StoryID` (`StoryID`),
  ADD KEY `Viewer` (`Viewer`);

--
-- Indexes for table `tbl_groups`
--
ALTER TABLE `tbl_groups`
  ADD PRIMARY KEY (`GroupID`);

--
-- Indexes for table `tbl_group_members`
--
ALTER TABLE `tbl_group_members`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `GroupID` (`GroupID`);

--
-- Indexes for table `tbl_group_posts`
--
ALTER TABLE `tbl_group_posts`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `GroupID` (`GroupID`);

--
-- Indexes for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `FollowID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `friend_request`
--
ALTER TABLE `friend_request`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `ImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `LikeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `marketplace`
--
ALTER TABLE `marketplace`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `message_requests`
--
ALTER TABLE `message_requests`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notif`
--
ALTER TABLE `notif`
  MODIFY `NotifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `NotifID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `StoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `story_views`
--
ALTER TABLE `story_views`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_groups`
--
ALTER TABLE `tbl_groups`
  MODIFY `GroupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_group_members`
--
ALTER TABLE `tbl_group_members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_group_posts`
--
ALTER TABLE `tbl_group_posts`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_messages`
--
ALTER TABLE `tbl_messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detailpost`
--
ALTER TABLE `detailpost`
  ADD CONSTRAINT `detailpost_ibfk_1` FOREIGN KEY (`PostID`) REFERENCES `post` (`PostID`),
  ADD CONSTRAINT `detailpost_ibfk_2` FOREIGN KEY (`ImageID`) REFERENCES `image` (`ImageID`);

--
-- Constraints for table `follow`
--
ALTER TABLE `follow`
  ADD CONSTRAINT `follow_ibfk_1` FOREIGN KEY (`FollowName`) REFERENCES `user` (`UserName`),
  ADD CONSTRAINT `follow_ibfk_2` FOREIGN KEY (`UserName`) REFERENCES `user` (`UserName`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`UserName`) REFERENCES `user` (`UserName`);

--
-- Constraints for table `story_views`
--
ALTER TABLE `story_views`
  ADD CONSTRAINT `story_views_ibfk_1` FOREIGN KEY (`StoryID`) REFERENCES `stories` (`StoryID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_group_members`
--
ALTER TABLE `tbl_group_members`
  ADD CONSTRAINT `tbl_group_members_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `tbl_groups` (`GroupID`);

--
-- Constraints for table `tbl_group_posts`
--
ALTER TABLE `tbl_group_posts`
  ADD CONSTRAINT `tbl_group_posts_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `tbl_groups` (`GroupID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
