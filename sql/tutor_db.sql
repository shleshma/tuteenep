-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2024 at 12:38 PM
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
-- Database: `tutor_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_approval_history`
--

CREATE TABLE `admin_approval_history` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `tutor_name` varchar(100) NOT NULL,
  `tutor_email` varchar(100) NOT NULL,
  `action` enum('approve','reject') NOT NULL,
  `date_approved_rejected` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`admin_id`, `admin_username`, `admin_password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
--

CREATE TABLE `tblbooking` (
  `booking_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','canceled','confirmed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`booking_id`, `session_id`, `student_id`, `booking_date`, `status`) VALUES
(2, 5, 2, '2024-06-25 21:18:30', 'pending'),
(3, 9, 1, '2024-07-13 13:17:21', 'confirmed'),
(4, 1, 1, '2024-07-14 07:57:04', 'pending'),
(7, 15, 1, '2024-07-15 19:50:18', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `tblsession`
--

CREATE TABLE `tblsession` (
  `session_id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `session_duration` varchar(11) NOT NULL,
  `session_grade` varchar(255) NOT NULL,
  `session_subject` varchar(255) NOT NULL,
  `session_location` varchar(255) NOT NULL,
  `session_note` text DEFAULT NULL,
  `is_booked` tinyint(1) DEFAULT 0,
  `session_type` varchar(20) NOT NULL DEFAULT 'Individual',
  `group_size` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsession`
--

INSERT INTO `tblsession` (`session_id`, `tutor_id`, `session_date`, `session_time`, `session_duration`, `session_grade`, `session_subject`, `session_location`, `session_note`, `is_booked`, `session_type`, `group_size`) VALUES
(1, 1, '2024-09-29', '12:00:00', '45', '10', 'Computer Science', 'New Baneshwor', 'Contact me.', 1, 'One-on-one', 1),
(5, 12, '2024-07-04', '11:30:00', '45', '11 Science', 'Physics', 'Sanepa', 'Contact', 0, 'One-on-one', 1),
(7, 8, '2024-06-27', '11:00:00', '45', '10', 'Compulsory Mathematics', 'Gyaneshwor', 'Will help with homework also.', 0, 'One-on-one', 1),
(8, 8, '2024-06-29', '01:00:00', '45', '10', 'Optional Mathematics', 'New Baneshwor', 'Focus on algebra and calculus; customized learning plans and interactive problem-solving sessions included.', 0, 'One-on-one', 1),
(9, 2, '2024-09-27', '10:00:00', '1 hour', '10', 'Optional Mathematics', 'Baneshwor', '', 1, 'One-on-one', 1),
(11, 13, '2024-09-26', '15:00:00', '1hr', '9', 'Science', 'Sanepa', '', 0, 'One-on-one', 1),
(15, 2, '2024-09-17', '10:00:00', '1hr 30min', '9', 'Optional Mathematics', 'Jhamsikhel', 'Focused on Trigonometry only.', 0, 'One-on-one', 1),
(18, 2, '2024-08-15', '12:00:00', '2', '10', 'Compulsory Mathematics', 'Sanepa', '', 0, 'Group', 10),
(19, 2, '2024-08-22', '12:00:00', '2', '9', 'Compulsory Mathematics', 'Sanepa', '', 0, 'Group', 10),
(20, 2, '2024-08-07', '12:00:00', '1', '11 Science', 'Physics', 'Thapathali ', '', 0, 'One-on-one', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `student_password` varchar(255) NOT NULL,
  `student_phone` varchar(255) NOT NULL,
  `student_grade` varchar(255) NOT NULL,
  `student_difficulty` varchar(125) NOT NULL,
  `student_school` varchar(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`student_id`, `student_name`, `student_email`, `student_password`, `student_phone`, `student_grade`, `student_difficulty`, `student_school`) VALUES
(1, 'Shleshma Shrestha', 'shleshmashrestha@gmail.com', '$2y$10$azN1s1EBVQTADjpFLRKOteYcVHWqZAmUqSC02EJXAXRd8TUtsolyS', '9863604066', '10', 'Optional Mathematics', 'Shuvatara'),
(2, 'Arnav Rajbhandari', 'arnavrjb@gmail.com', '$2y$10$5lol4sj7BcUNVkj3od6AiOlikUf5nx26yvSQ7ceFz1Wrk0PBxbKPK', '9872046758', '11 Science', 'Physics', 'United '),
(3, 'Simran Shrestha', 'simran@gmail.com', '$2y$10$tifoP1z17pY36w0OvzlJ.u3XA7FqpgaVRwnOXEo.JetQpK7.KYIQK', '9818025739', '11 Science', 'Physics', 'Kathmandu World School');

-- --------------------------------------------------------

--
-- Table structure for table `tbltutor`
--

CREATE TABLE `tbltutor` (
  `tutor_id` int(11) NOT NULL,
  `tutor_name` varchar(255) NOT NULL,
  `tutor_email` varchar(255) NOT NULL,
  `tutor_phone` varchar(255) NOT NULL,
  `tutor_password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `university` varchar(255) NOT NULL,
  `graduation_year` year(4) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `years_experience` int(11) NOT NULL,
  `description` text NOT NULL,
  `cv` varchar(255) NOT NULL,
  `certificates` text NOT NULL,
  `verification_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `photo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbltutor`
--

INSERT INTO `tbltutor` (`tutor_id`, `tutor_name`, `tutor_email`, `tutor_phone`, `tutor_password`, `address`, `degree`, `university`, `graduation_year`, `subject`, `years_experience`, `description`, `cv`, `certificates`, `verification_status`, `photo_url`) VALUES
(1, 'Kumar Lohala', 'kumar@gmail.com', '9818050277', '$2y$10$Jkl1dimCX8LAOaF22yTM4eVDoI3MivQ/2ipygu.rs/4QXVnEp5zde', 'Baneshwor', 'MIT', 'Kathmandu University', '2010', 'Computer Science', 8, 'A skilled Computer Science tutor proficient in programming languages and concepts.', '', '', 'approved', 'uploads/kumar-lohala.jpg'),
(2, 'Ramesh Kumar Shah', 'ramesh@gmail.com', '9856273056', '$2y$10$lNQnUrXq9A3nh7CrwTkrs.hMPfIaChE9CeBELXAbQzXPhDY5Dripa', 'Lokanthali', 'BBS', 'Coventry University', '2018', 'Science and Mathematics', 3, 'Dedicated tutor with expertise in Science and Mathematics.', '', '', 'approved', 'uploads/ramesh-kumar.jpg'),
(7, 'Ritu Raj Lamsal', 'ritu.lamsal@gmail.com', '9872638900', '$2y$10$g8TlFi2wMZdT66wynWcpwu4qxuZlC1h3.xRedPO8wxEjGP0ZgPN7O', 'Chabahil', 'MIT', 'Universidad de Málaga', '2020', 'Computer Science', 7, 'Experienced tutor specializing in Computer subjects.', '', '', 'approved', 'uploads/Ritu-Lamsal.png'),
(8, 'Gita Singh', 'gita101@gmail.com', '9881820467', '$2y$10$CxFDktFmMwi7nNLCxbZvYes1DHEBYfjGCqrai1apXgnovqXi5ioIe', 'Sanepa', 'Bachelor of Science', 'Tribhuvan University', '2018', 'Mathematics', 4, 'Passionate about teaching Mathematics at all levels.', '', '', 'approved', 'uploads/gita.jpg'),
(9, 'Bibhav Adhikari', 'bibhav@gmail.com', '9871726356', '$2y$10$9JtMwznZapBl4SsCcCv1k.7h68sR0GcvPsDtrTeR1TyLVUxoYakeq', 'Baneshwor', 'MBA', 'Pokhara University', '2017', 'English, Nepali', 6, 'Specializing in English Literature and Language teaching.', '', '', 'approved', 'uploads/Bibhav-Adhikari.png'),
(10, 'Vikram Yadav', 'vikramyadav@gmail.com', '9872635477', '$2y$10$v6P0fwZSf5en58RXDIuBo.kFT54Wx75MsRu8hXbRGGsaUPtbg/7O.', 'Sanepa', 'Bachelor of Science (B.Sc.) in Computer Science', 'Chandigarh University', '2007', 'Computer Science', 5, 'Passionate about fostering a deep understanding of computer science concepts and guiding students.', '', '', 'pending', NULL),
(11, 'Preeti Shrestha', 'preeti@gmail.com', '9802738904', '$2y$10$TVZ7rpqi7mWKamrR2mn.9uvvunYvnisOnM3tIqOsWLityVBvAY99S', 'Sanepa', 'Bachelor of Commerce (B.Com) in Accountancy', 'Tribhuvan University', '2022', 'Accountancy', 1, 'Experienced tutor specializing in Accountancy principles, financial reporting, and managerial accounting. ', '', '', 'rejected', NULL),
(12, 'Pooja Baral', 'poojab@gmail.com', '9876203647', '$2y$10$9nLtez7AsMOYRcvti/LKre/q5MBv60us5ek8pNbI4Ugrb6mKzddc2', 'Sinamangal', 'Ph.D. in Physics', 'Caltech (California Institute of Technology), USA', '2016', 'Physics', 5, 'Dedicated to making complex physics concepts understandable and enjoyable.', '', '', 'approved', 'uploads/pooja.jpg'),
(13, 'Arnav Rajbhandari', 'arnav@gmail.com', '9876098765', '$2y$10$e8sWjQPc17GurjE8vtuZTu.ZJGEUtPMo0.IxCsDqA6T3sSaqgr3KG', 'Sanepa', 'BBIS', 'KU', '2024', 'Mathematics', 1, 'i am tutor.', 'uploads/Pharmacy Administrator System Proposal Draft IV.docx', '', 'approved', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_approval_history`
--
ALTER TABLE `admin_approval_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `tblsession`
--
ALTER TABLE `tblsession`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `tbltutor`
--
ALTER TABLE `tbltutor`
  ADD PRIMARY KEY (`tutor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_approval_history`
--
ALTER TABLE `admin_approval_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblsession`
--
ALTER TABLE `tblsession`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbltutor`
--
ALTER TABLE `tbltutor`
  MODIFY `tutor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD CONSTRAINT `fk_tblbooking_session_id` FOREIGN KEY (`session_id`) REFERENCES `tblsession` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblbooking_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `tblstudent` (`student_id`);

--
-- Constraints for table `tblsession`
--
ALTER TABLE `tblsession`
  ADD CONSTRAINT `tblsession_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `tbltutor` (`tutor_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
