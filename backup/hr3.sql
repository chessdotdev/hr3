-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 08:29 AM
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
-- Database: `hr3`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(10) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `firstname`, `lastname`, `middlename`, `contact_no`, `username`, `password`, `role_id`, `status`) VALUES
(40, 'test', 'admin', '', '09132655412', 'admin', '$2y$10$98NokWt7ylUnH20yjaoxVOiB63ZxOeg.xb6uNJoM6E8j/Eqsagrwi', 2, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `claim_type` varchar(50) NOT NULL,
  `incident_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `driver_id`, `claim_type`, `incident_date`, `amount`, `description`, `proof_file`, `status`, `created_at`) VALUES
(1, 7, 'Cancelled Trip', '2025-08-15', 200.00, 'biglang ni cancel aray ko', '../uploads/proof_68f1b5450965f8.17381885.png', 'Approved', '2025-10-17 03:17:25'),
(2, 7, 'Accident', '2025-08-14', 5500.00, 'nabangga nang truck', '../Uploads/proof_68f1be2be1ef43.35969339.jpg', 'Pending', '2025-10-17 03:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `role_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`id`, `firstname`, `lastname`, `middlename`, `contact_no`, `password`, `email`, `plate_number`, `vehicle_type`, `status`, `role_id`) VALUES
(6, 'Robert', 'Pradilla', 'Toledo', '09263422473', '$2y$10$7tn4gpi6dRbYa2UmaLK52unoIWGzLICTk5c7iRhLDi/wgKDhm8Qbq', 'pradillaprintingshop@gmail.com', '', '', 'Active', 3),
(7, 'John', 'Doe', 'D', '09123456789', '$2y$10$m5pN/7YOYwyuBFrw3x3YtedNvaAtJpW806akm3ciadwwi.0a31Xoe', 'johndoe@gmail.com', 'ABC-234-DEF', 'SUV', 'Active', 3),
(9, 'Jane', 'Doe', 'D', '09321654987', '$2y$10$BfgJfMj6CGosLNyevQRnren9HTxoWdlvtBRegJzgqm2IkSD21ITUK', 'janedoe@gmail.com', 'CDA-BCD-DCE', 'SEDAN', 'Active', 3),
(10, 'Kevin Nash', 'Fontanilla', '', '09369258147', '$2y$10$KR0/WsKMz26/QcXc.BKspumdIlJKCQ5fcaDVQ1lQkZZOsPiiwi2Ni', 'kevinash@gmail.com', 'CDA-BCD-ACD', 'SEDAN', 'Active', 3);

-- --------------------------------------------------------

--
-- Table structure for table `employee_time_logs`
--

CREATE TABLE `employee_time_logs` (
  `id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `driver_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_time_logs`
--

INSERT INTO `employee_time_logs` (`id`, `time_in`, `time_out`, `created_at`, `driver_id`) VALUES
(6, '2025-10-09 07:03:27', '2025-10-09 10:04:40', '2025-10-09', 7),
(10, '2025-10-13 06:04:22', '2025-10-13 07:59:45', '2025-10-13', 7),
(11, '2025-10-10 02:05:19', '2025-10-10 04:56:54', '2025-10-10', 7),
(12, '2025-10-11 05:57:49', '2025-10-11 09:57:53', '2025-10-11', 7),
(13, '2025-10-12 05:58:13', '2025-10-12 12:58:16', '2025-10-12', 7),
(14, '2025-10-14 05:59:22', '2025-10-14 08:59:25', '2025-10-14', 7),
(16, '2025-10-16 21:27:02', '2025-10-16 22:40:59', '2025-10-16', 7),
(23, '2025-10-19 03:03:42', '2025-10-19 03:06:34', '2025-10-19', 7);

-- --------------------------------------------------------

--
-- Table structure for table `leave_request`
--

CREATE TABLE `leave_request` (
  `id` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `timestamp` time DEFAULT current_timestamp(),
  `documents` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_request`
--

INSERT INTO `leave_request` (`id`, `reason`, `start_date`, `end_date`, `timestamp`, `documents`, `created_at`, `status`, `driver_id`) VALUES
(49, 'sick', '2025-10-14', NULL, '02:27:03', '../uploads/down-arrow.png', '2025-10-14', 'Approved', 7),
(50, 'vacation', '2025-10-14', '2025-10-21', '02:43:51', NULL, '2025-10-14', 'Approved', 6),
(51, 'vacation', '2025-10-14', '2025-10-21', '03:48:03', NULL, '2025-10-14', 'Pending', 6),
(52, 'vacation', '2025-10-17', '2025-10-27', '21:33:18', NULL, '2025-10-16', 'Pending', 7);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`) VALUES
(1, 'superadmin'),
(2, 'admin'),
(3, 'driver');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `shift_date` date DEFAULT NULL,
  `shift_start` varchar(255) DEFAULT NULL,
  `shift_end` varchar(255) DEFAULT NULL,
  `shift_type` varchar(255) DEFAULT NULL,
  `scheduled_at` date DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `shift_date`, `shift_start`, `shift_end`, `shift_type`, `scheduled_at`, `driver_id`) VALUES
(25, '2025-12-05', '5:00 PM', '12:00 AM', 'Night Shift', '2025-10-12', 7),
(26, '2025-10-05', '5:00 AM', '5:00 PM', 'Morning Shift', '2025-10-12', 7),
(27, '2025-10-31', '6:36 AM', '7:35 PM', 'Morning Shift', '2025-10-14', 7),
(28, NULL, NULL, NULL, NULL, NULL, 10),
(29, '2025-10-25', '6:00 AM', '5:00 PM', 'Morning Shift', '2025-10-19', 7),
(30, '2025-10-30', '8:00 AM', '5:00 PM', 'Morning Shift', '2025-10-19', 7),
(31, '2025-11-05', '1:00 PM', '9:00 PM', 'Night Shift', '2025-10-19', 7),
(32, '2025-11-11', '8:00 AM', '5:00 PM', 'Morning Shift', '2025-10-19', 7);

-- --------------------------------------------------------

--
-- Table structure for table `shift_req`
--

CREATE TABLE `shift_req` (
  `shift_id` int(11) NOT NULL,
  `shift_date` varchar(255) DEFAULT NULL,
  `shift_start` varchar(255) DEFAULT NULL,
  `shift_end` varchar(255) DEFAULT NULL,
  `shift_type` varchar(20) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending',
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_req`
--

INSERT INTO `shift_req` (`shift_id`, `shift_date`, `shift_start`, `shift_end`, `shift_type`, `date`, `status`, `driver_id`) VALUES
(27, '2025-10-31', '6:36 AM', '7:35 PM', 'Morning Shift', '2025-10-13 13:58:22', 'APPROVED', 7),
(28, '2025-10-20', '1:00 PM', '8:00 PM', 'Night Shift', '2025-10-14 11:30:49', 'APPROVED', 7),
(29, '2025-10-25', '6:00 AM', '5:00 PM', 'Morning Shift', '2025-10-14 11:39:33', 'APPROVED', 7),
(30, '2025-10-30', '1:00 PM', '8:00 PM', 'Night Shift', '2025-10-19 09:26:26', 'APPROVED', 7),
(31, '2025-10-30', '5:00 AM', '8:00 PM', 'Morning Shift', '2025-10-19 09:39:18', 'APPROVED', 7),
(32, '2025-10-30', '8:00 AM', '5:00 PM', 'Morning Shift', '2025-10-19 09:45:00', 'APPROVED', 7),
(33, '2025-11-05', '1:00 PM', '9:00 PM', 'Night Shift', '2025-10-19 09:55:33', 'APPROVED', 7),
(34, '2025-11-11', '8:00 AM', '5:00 PM', 'Morning Shift', '2025-10-19 10:04:34', 'APPROVED', 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `contact_no` (`contact_no`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contact_no` (`contact_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employee_time_logs`
--
ALTER TABLE `employee_time_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_req`
--
ALTER TABLE `shift_req`
  ADD PRIMARY KEY (`shift_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employee_time_logs`
--
ALTER TABLE `employee_time_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `leave_request`
--
ALTER TABLE `leave_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `shift_req`
--
ALTER TABLE `shift_req`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
