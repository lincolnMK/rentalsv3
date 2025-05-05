-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 25, 2025 at 12:08 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `landlord`
--

DROP TABLE IF EXISTS `landlord`;
CREATE TABLE IF NOT EXISTS `landlord` (
  `landlord_id` int(11) NOT NULL AUTO_INCREMENT,
  `landlord_type_id` int(11) DEFAULT NULL,
  `national_id` varchar(255) DEFAULT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `vendor_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`landlord_id`),
  KEY `landlord_type_id` (`landlord_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `tenancy_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `rent_per_month` decimal(10,2) DEFAULT NULL,
  `vat` decimal(10,2) DEFAULT NULL,
  `total_payment` decimal(10,2) DEFAULT NULL,
  `annual_gross_rent` decimal(10,2) DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `approval_status` varchar(255) DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `payment_received_by_landlord` tinyint(1) DEFAULT NULL,
  `balance_due` decimal(10,2) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`payment_id`),
  KEY `tenancy_id` (`tenancy_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `tenancy_id`, `payment_date`, `rent_per_month`, `vat`, `total_payment`, `annual_gross_rent`, `period`, `approval_status`, `approval_date`, `payment_received_by_landlord`, `balance_due`, `comments`) VALUES
(1, 101, '2025-01-01', '500.00', '50.00', '550.00', '6000.00', 'Jan 2025', 'Approved', '2025-01-02', 1, '0.00', 'N/A'),
(2, 101, '2025-01-01', '500.00', '50.00', '550.00', '6000.00', 'Jan 2025', 'Approved', '2025-01-02', 1, '0.00', 'N/A'),
(3, 102, '2025-01-05', '600.00', '60.00', '660.00', '7200.00', 'Jan 2025', 'Approved', '2025-01-06', 1, '0.00', 'Late payment'),
(4, 103, '2025-01-10', '700.00', '70.00', '770.00', '8400.00', 'Jan 2025', 'Approved', '2025-01-11', 1, '0.00', 'On time'),
(5, 104, '2025-01-15', '800.00', '80.00', '880.00', '9600.00', 'Jan 2025', 'Pending', '2025-01-16', 0, '880.00', 'Pending approval'),
(6, 105, '2025-01-20', '900.00', '90.00', '990.00', '10800.00', 'Jan 2025', 'Approved', '2025-01-21', 1, '0.00', 'Payment received'),
(7, 106, '2025-01-25', '1000.00', '100.00', '1100.00', '12000.00', 'Jan 2025', 'Approved', '2025-01-26', 1, '0.00', 'Paid'),
(8, 107, '2025-01-30', '1100.00', '110.00', '1210.00', '13200.00', 'Jan 2025', 'Pending', '2025-01-31', 0, '1210.00', 'Awaiting payment'),
(9, 108, '2025-02-01', '1200.00', '120.00', '1320.00', '14400.00', 'Feb 2025', 'Approved', '2025-02-02', 1, '0.00', 'February payment'),
(10, 109, '2025-02-05', '1300.00', '130.00', '1430.00', '15600.00', 'Feb 2025', 'Approved', '2025-02-06', 1, '0.00', 'Payment received on time'),
(11, 110, '2025-02-10', '1400.00', '140.00', '1540.00', '16800.00', 'Feb 2025', 'Approved', '2025-02-11', 1, '0.00', ''),
(12, 111, '2025-02-15', '1500.00', '150.00', '1650.00', '18000.00', 'Feb 2025', 'Pending', '2025-02-16', 0, '1650.00', 'Late payment'),
(13, 112, '2025-02-20', '1600.00', '160.00', '1760.00', '19200.00', 'Feb 2025', 'Approved', '2025-02-21', 1, '0.00', 'Paid on time'),
(14, 113, '2025-02-25', '1700.00', '170.00', '1870.00', '20400.00', 'Feb 2025', 'Approved', '2025-02-26', 1, '0.00', 'Payment received'),
(15, 114, '2025-03-01', '1800.00', '180.00', '1980.00', '21600.00', 'Mar 2025', 'Approved', '2025-03-02', 1, '0.00', 'March payment'),
(16, 115, '2025-03-05', '1900.00', '190.00', '2090.00', '22800.00', 'Mar 2025', 'Approved', '2025-03-06', 1, '0.00', 'Paid on time'),
(17, 116, '2025-03-10', '2000.00', '200.00', '2200.00', '24000.00', 'Mar 2025', 'Pending', '2025-03-11', 0, '2200.00', 'Pending approval'),
(18, 117, '2025-03-15', '2100.00', '210.00', '2310.00', '25200.00', 'Mar 2025', 'Approved', '2025-03-16', 1, '0.00', 'Payment received'),
(19, 118, '2025-03-20', '2200.00', '220.00', '2420.00', '26400.00', 'Mar 2025', 'Approved', '2025-03-21', 1, '0.00', 'Paid'),
(20, 119, '2025-03-25', '2300.00', '230.00', '2530.00', '27600.00', 'Mar 2025', 'Approved', '2025-03-26', 1, '0.00', 'Payment on time'),
(21, 120, '2025-03-30', '2400.00', '240.00', '2640.00', '28800.00', 'Mar 2025', 'Pending', '2025-03-31', 0, '2640.00', 'Awaiting payment'),
(22, 121, '2025-04-01', '2500.00', '250.00', '2750.00', '30000.00', 'Apr 2025', 'Approved', '2025-04-02', 1, '0.00', 'April payment'),
(23, 122, '2025-04-05', '2600.00', '260.00', '2860.00', '31200.00', 'Apr 2025', 'Approved', '2025-04-06', 1, '0.00', 'Paid'),
(24, 123, '2025-04-10', '2700.00', '270.00', '2970.00', '32400.00', 'Apr 2025', 'Pending', '2025-04-11', 0, '2970.00', 'Pending approval'),
(25, 124, '2025-04-15', '2800.00', '280.00', '3080.00', '33600.00', 'Apr 2025', 'Approved', '2025-04-16', 1, '0.00', 'Payment received'),
(26, 125, '2025-04-20', '2900.00', '290.00', '3190.00', '34800.00', 'Apr 2025', 'Approved', '2025-04-21', 1, '0.00', 'Paid on time'),
(27, 126, '2025-04-25', '3000.00', '300.00', '3300.00', '36000.00', 'Apr 2025', 'Approved', '2025-04-26', 1, '0.00', ''),
(28, 127, '2025-04-30', '3100.00', '310.00', '3410.00', '37200.00', 'Apr 2025', 'Pending', '2025-05-01', 0, '3410.00', 'Late payment'),
(29, 128, '2025-05-01', '3200.00', '320.00', '3520.00', '38400.00', 'May 2025', 'Approved', '2025-05-02', 1, '0.00', 'May payment'),
(30, 129, '2025-05-05', '3300.00', '330.00', '3630.00', '39600.00', 'May 2025', 'Approved', '2025-05-06', 1, '0.00', 'Paid on time'),
(31, 130, '2025-05-10', '3400.00', '340.00', '3740.00', '40800.00', 'May 2025', 'Pending', '2025-05-11', 0, '3740.00', 'Awaiting approval'),
(32, 131, '2025-05-15', '3500.00', '350.00', '3850.00', '42000.00', 'May 2025', 'Approved', '2025-05-16', 1, '0.00', 'Payment received'),
(33, 255, '2025-01-31', '346.00', '16.00', '12300.00', '123333.00', 'Jan 2025', 'Approved', '2025-01-26', 0, '0.00', 'none'),
(34, 256, '2025-01-25', '666666.00', '16.00', '12300.00', '123333.00', 'Jan 2025', 'Approved', '2025-01-25', 1, '0.00', 'testing more');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `property_id` int(11) NOT NULL AUTO_INCREMENT,
  `sn_file` varchar(255) DEFAULT NULL,
  `plot_number` varchar(255) DEFAULT NULL,
  `landlord_id` int(11) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `geo_ref_coordinates` varchar(255) DEFAULT NULL,
  `property_type_id` int(11) DEFAULT NULL,
  `rca` varchar(255) DEFAULT NULL,
  `date_of_occupation` date DEFAULT NULL,
  `rent_first_occupation` decimal(10,2) DEFAULT NULL,
  `area_m2` decimal(10,2) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`property_id`),
  KEY `landlord_id` (`landlord_id`),
  KEY `property_type_id` (`property_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','normal') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `user_type`, `created_at`) VALUES
(2, 'admin', '$2y$10$OgzoIPV7aGbghJaoNr8Gj.rZjh8DMNhoIMFOdCAjMCBl4HJX0BqNe', 'admin', '2025-01-23 16:21:22');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
