-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2019 at 04:12 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `psm_zana`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `secret_key` varchar(100) NOT NULL,
  `pass_key` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `secret_key`, `pass_key`) VALUES
(1, 'admin', 'admin123'),
(2, 'zana', 'zana123');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `mac` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `block_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`mac`, `host`, `joined`, `block_status`) VALUES
('1C2-4F91-8D0A-0FAE4F', 'Aliff-pc', '2019-02-24 00:48:17', 'B'),
('4c-dd-31-3f-48-b0', ' Jallo-bette\n', '2019-02-25 01:04:23', 'A'),
('51C2-4F91-8D0A-0FAE4F57CFCB', 'Hacker-pc', '2019-02-24 00:48:17', 'B'),
('54-27-1e-a3-39-49', ' Delz\n', '2019-03-14 08:58:25', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `client_note`
--

CREATE TABLE `client_note` (
  `id` int(11) NOT NULL,
  `client` varchar(255) NOT NULL,
  `session` int(11) NOT NULL,
  `ipaddress` varchar(100) NOT NULL,
  `usage_limit` time DEFAULT NULL,
  `duration_limit` varchar(100) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `client_note`
--

INSERT INTO `client_note` (`id`, `client`, `session`, `ipaddress`, `usage_limit`, `duration_limit`, `date`) VALUES
(13, '4c-dd-31-3f-48-b0', 24, '192.168.137.8', '14:50:00', '0', '2019-03-15 01:38:40'),
(23, '4c-dd-31-3f-48-b0', 28, '192.168.137.85', '00:00:00', '0', '2019-03-16 11:27:33'),
(24, '4c-dd-31-3f-48-b0', 29, '192.168.137.164', '21:12:20', '10', '2019-03-16 12:54:15'),
(25, '4c-dd-31-3f-48-b0', 30, '', NULL, '0', '2019-03-16 13:19:01');

-- --------------------------------------------------------

--
-- Table structure for table `proxy`
--

CREATE TABLE `proxy` (
  `id` int(11) NOT NULL,
  `DNS` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `proxy`
--

INSERT INTO `proxy` (`id`, `DNS`, `date`) VALUES
(1, 'www.test.com', '2019-03-16 13:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `ssid` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `sharing_type` varchar(100) NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session_id`, `ssid`, `password`, `sharing_type`, `start`, `end`) VALUES
(24, 'zana', '12341234', 'Wi-Fi', '2019-03-15 01:27:55', '2019-03-15 02:00:15'),
(25, 'zana', '12341234', 'Wi-Fi', '2019-03-16 00:00:57', '2019-03-16 02:08:32'),
(26, 'zana', '12341234', 'Wi-Fi', '2019-03-16 06:52:06', '2019-03-16 07:14:14'),
(27, 'zana', '12341234', 'Wi-Fi', '2019-03-16 11:22:48', '2019-03-16 11:26:26'),
(28, 'zana', '12341234', 'Wi-Fi', '2019-03-16 11:27:17', '2019-03-16 11:28:15'),
(29, 'zana', '12341234', 'Wi-Fi', '2019-03-16 12:53:49', '2019-03-16 13:02:59'),
(30, 'zana', '12341234', 'Wi-Fi', '2019-03-16 13:18:20', '2019-03-16 13:21:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`mac`);

--
-- Indexes for table `client_note`
--
ALTER TABLE `client_note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client` (`client`),
  ADD KEY `session` (`session`);

--
-- Indexes for table `proxy`
--
ALTER TABLE `proxy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `client_note`
--
ALTER TABLE `client_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `proxy`
--
ALTER TABLE `proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_note`
--
ALTER TABLE `client_note`
  ADD CONSTRAINT `client_note_ibfk_1` FOREIGN KEY (`client`) REFERENCES `client` (`mac`),
  ADD CONSTRAINT `client_note_ibfk_2` FOREIGN KEY (`session`) REFERENCES `session` (`session_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
