-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2024 at 04:29 PM
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
-- Database: `qms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'a', '1234'),
(2, 'user1212', '$2y$10$I28IlT.AYyAeH0SS8LW09ewlxK2i6UVSVI3RRoM6Cs.'),
(3, 'samin', '13');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `question_id` int(11) NOT NULL,
  `questiontext` varchar(100) NOT NULL,
  `optionA` varchar(50) NOT NULL,
  `optionB` varchar(50) NOT NULL,
  `optionC` varchar(50) NOT NULL,
  `optionD` varchar(50) NOT NULL,
  `answer` varchar(50) NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`question_id`, `questiontext`, `optionA`, `optionB`, `optionC`, `optionD`, `answer`, `difficulty`, `subject`) VALUES
(1, 'What is the national animal of Bd?', 'cat', 'dog', 'lion', 'tiger', 'D', 'Hard', 'GK'),
(2, 'Who is football GOAT', 'pele', 'maradona', 'messi', 'ronaldo', 'C', 'medium', 'football'),
(7, 'who is our faculty', 'iqn', 'a', 'lion', 'c', 'A', 'Easy', 'cse'),
(8, 'Who is our most Hated faculty?', 'iqn', 'a', 'bb', 'a', 'A', 'Easy', 'cse'),
(9, 'Balon D or winner 2024', 'rodri', 'messi', 'vini', 'carv', 'A', 'Easy', 'football'),
(10, 'wc winner 2022', 'arg', 'bra', 'ger', 'ita', 'A', 'Hard', 'football');

-- --------------------------------------------------------

--
-- Table structure for table `quizsession`
--

CREATE TABLE `quizsession` (
  `session_id` int(50) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `score` int(50) NOT NULL,
  `rewardPoints` int(50) NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizsession`
--

INSERT INTO `quizsession` (`session_id`, `time`, `score`, `rewardPoints`, `difficulty`, `subject`, `student_id`) VALUES
(46, 0, 1, 0, 'Medium', 'Mathematics', 10),
(47, 0, 1, 0, 'Medium', 'Mathematics', 10),
(48, 4, 0, 0, 'Medium', 'Mathematics', 10),
(49, 0, 1, 0, 'Medium', 'Mathematics', 10),
(50, 85, 1, 0, 'Medium', 'Mathematics', 10),
(51, 88, 1, 0, 'Medium', 'Mathematics', 10),
(52, 11, 1, 0, 'Medium', 'Mathematics', 10),
(53, NULL, 1, 0, '', '', 10),
(54, NULL, 1, 0, '', '', 4),
(55, NULL, 1, 0, '', '', 4),
(56, 110, 1, 0, '', '', 4),
(57, 14, 0, 0, '', '', 4),
(58, 11, 0, 0, '', '', 4),
(59, 3, 0, 0, '', '', 4),
(60, 15, 0, 0, '', '', 10),
(61, 2, 0, 0, '', '', 10),
(62, 231, 0, 0, '', '', 10),
(63, 4, 0, 0, '', '', 10),
(64, 109, 0, 0, '', '', 10),
(65, 121, 0, 0, '', '', 10),
(66, 6, 6, 0, '', '', 10),
(67, 16, 5, 0, '', '', 10),
(68, 7, 4, 0, '', '', 4),
(69, 7, 3, 0, '', '', 4),
(70, 7, 5, 0, '', '', 4),
(71, 7, 5, 0, '', '', 4),
(72, 13, 6, 0, '', '', 9),
(73, 18, 6, 0, '', '', 9),
(74, 10, 5, 0, '', '', 9);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `rewardPoints` int(50) NOT NULL,
  `totalMarks` int(50) NOT NULL,
  `badge` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `username`, `password`, `rewardPoints`, `totalMarks`, `badge`) VALUES
(1, 'student1', '$2y$10$RNwBIyifSmGa6FNsbeI11ujewBqZmeBmzXU6Qf1xbuG', 0, 0, ''),
(2, 'admin', '123', 0, 0, ''),
(3, 'admin2', '111', 0, 0, ''),
(4, 'samin', '13', 8, 22, ''),
(5, 'tofael', '69', 0, 0, ''),
(6, 'samadmin', '1', 0, 0, ''),
(7, 'user', '420', 0, 0, ''),
(8, 'user1', '12', 0, 0, ''),
(9, 'B', '12', 0, 17, ''),
(10, '1', '1', 0, 13, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `quizsession`
--
ALTER TABLE `quizsession`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `student_idfk` (`student_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quizsession`
--
ALTER TABLE `quizsession`
  MODIFY `session_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quizsession`
--
ALTER TABLE `quizsession`
  ADD CONSTRAINT `student_idfk` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
