-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 25, 2025 at 03:22 PM
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
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `landlord`
--

INSERT INTO `landlord` (`landlord_id`, `landlord_type_id`, `national_id`, `phone_no`, `address`, `bank_name`, `bank_account_name`, `bank_account_number`, `vendor_code`) VALUES
(1, 1, 'NID001', '1234567890', 'Address 1', 'Bank 1', 'Bank Account Name 1', '123456789001', 'VC001'),
(2, 2, 'NID002', '0987654321', 'Address 2', 'Bank 2', 'Bank Account Name 2', '123456789002', 'VC002'),
(3, 3, 'NID003', '1234509876', 'Address 3', 'Bank 3', 'Bank Account Name 3', '123456789003', 'VC003'),
(4, 4, 'NID004', '0987601234', 'Address 4', 'Bank 4', 'Bank Account Name 4', '123456789004', 'VC004'),
(5, 5, 'NID005', '1234987650', 'Address 5', 'Bank 5', 'Bank Account Name 5', '123456789005', 'VC005'),
(6, 6, 'NID006', '0987123456', 'Address 6', 'Bank 6', 'Bank Account Name 6', '123456789006', 'VC006'),
(7, 7, 'NID007', '1234678901', 'Address 7', 'Bank 7', 'Bank Account Name 7', '123456789007', 'VC007'),
(8, 8, 'NID008', '0987012345', 'Address 8', 'Bank 8', 'Bank Account Name 8', '123456789008', 'VC008'),
(9, 9, 'NID009', '1234765890', 'Address 9', 'Bank 9', 'Bank Account Name 9', '123456789009', 'VC009'),
(10, 10, 'NID010', '0987054321', 'Address 10', 'Bank 10', 'Bank Account Name 10', '123456789010', 'VC010'),
(11, 1, 'NID011', '1234567890', 'Address 11', 'Bank 1', 'Bank Account Name 11', '123456789011', 'VC011'),
(12, 2, 'NID012', '0987654321', 'Address 12', 'Bank 2', 'Bank Account Name 12', '123456789012', 'VC012'),
(13, 3, 'NID013', '1234509876', 'Address 13', 'Bank 3', 'Bank Account Name 13', '123456789013', 'VC013'),
(14, 4, 'NID014', '0987601234', 'Address 14', 'Bank 4', 'Bank Account Name 14', '123456789014', 'VC014'),
(15, 5, 'NID015', '1234987650', 'Address 15', 'Bank 5', 'Bank Account Name 15', '123456789015', 'VC015'),
(16, 6, 'NID016', '0987123456', 'Address 16', 'Bank 6', 'Bank Account Name 16', '123456789016', 'VC016'),
(17, 7, 'NID017', '1234678901', 'Address 17', 'Bank 7', 'Bank Account Name 17', '123456789017', 'VC017'),
(18, 8, 'NID018', '0987012345', 'Address 18', 'Bank 8', 'Bank Account Name 18', '123456789018', 'VC018'),
(19, 9, 'NID019', '1234765890', 'Address 19', 'Bank 9', 'Bank Account Name 19', '123456789019', 'VC019'),
(20, 10, 'NID020', '0987054321', 'Address 20', 'Bank 10', 'Bank Account Name 20', '123456789020', 'VC020'),
(21, 0, 'V35RCS6M', '265881261516', 'box1', 'standard', 'kings mkumbwa', '9100003536219', 'theone');

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
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `sn_file`, `plot_number`, `landlord_id`, `region`, `district`, `location`, `geo_ref_coordinates`, `property_type_id`, `rca`, `date_of_occupation`, `rent_first_occupation`, `area_m2`, `description`) VALUES
(1, 'SN001', 'Plot001', 1, 'Region1', 'District1', 'Location1', 'GeoRef1', 1, 'RCA1', '2023-01-01', '1000.00', '150.00', 'Description1'),
(2, 'SN002', 'Plot002', 2, 'Region2', 'District2', 'Location2', 'GeoRef2', 2, 'RCA2', '2023-02-01', '1200.00', '160.00', 'Description2'),
(3, 'SN003', 'Plot003', 3, 'Region3', 'District3', 'Location3', 'GeoRef3', 3, 'RCA3', '2023-03-01', '1300.00', '170.00', 'Description3'),
(4, 'SN004', 'Plot004', 4, 'Region4', 'District4', 'Location4', 'GeoRef4', 4, 'RCA4', '2023-04-01', '1400.00', '180.00', 'Description4'),
(5, 'SN005', 'Plot005', 5, 'Region5', 'District5', 'Location5', 'GeoRef5', 5, 'RCA5', '2023-05-01', '1500.00', '190.00', 'Description5'),
(6, 'SN006', 'Plot006', 6, 'Region6', 'District6', 'Location6', 'GeoRef6', 6, 'RCA6', '2023-06-01', '1600.00', '200.00', 'Description6'),
(7, 'SN007', 'Plot007', 7, 'Region7', 'District7', 'Location7', 'GeoRef7', 7, 'RCA7', '2023-07-01', '1700.00', '210.00', 'Description7'),
(8, 'SN008', 'Plot008', 8, 'Region8', 'District8', 'Location8', 'GeoRef8', 8, 'RCA8', '2023-08-01', '1800.00', '220.00', 'Description8'),
(9, 'SN009', 'Plot009', 9, 'Region9', 'District9', 'Location9', 'GeoRef9', 9, 'RCA9', '2023-09-01', '1900.00', '230.00', 'Description9'),
(10, 'SN010', 'Plot010', 10, 'Region10', 'District10', 'Location10', 'GeoRef10', 10, 'RCA10', '2023-10-01', '2000.00', '240.00', 'Description10'),
(11, 'SN011', 'Plot011', 11, 'Region11', 'District11', 'Location11', 'GeoRef11', 11, 'RCA11', '2023-11-01', '2100.00', '250.00', 'Description11'),
(12, 'SN012', 'Plot012', 12, 'Region12', 'District12', 'Location12', 'GeoRef12', 12, 'RCA12', '2023-12-01', '2200.00', '260.00', 'Description12'),
(13, 'SN013', 'Plot013', 13, 'Region13', 'District13', 'Location13', 'GeoRef13', 13, 'RCA13', '2024-01-01', '2300.00', '270.00', 'Description13'),
(14, 'SN014', 'Plot014', 14, 'Region14', 'District14', 'Location14', 'GeoRef14', 14, 'RCA14', '2024-02-01', '2400.00', '280.00', 'Description14'),
(15, 'SN015', 'Plot015', 15, 'Region15', 'District15', 'Location15', 'GeoRef15', 15, 'RCA15', '2024-03-01', '2500.00', '290.00', 'Description15'),
(16, 'SN016', 'Plot016', 16, 'Region16', 'District16', 'Location16', 'GeoRef16', 16, 'RCA16', '2024-04-01', '2600.00', '300.00', 'Description16'),
(17, 'SN017', 'Plot017', 17, 'Region17', 'District17', 'Location17', 'GeoRef17', 17, 'RCA17', '2024-05-01', '2700.00', '310.00', 'Description17'),
(18, 'SN018', 'Plot018', 18, 'Region18', 'District18', 'Location18', 'GeoRef18', 18, 'RCA18', '2024-06-01', '2800.00', '320.00', 'Description18'),
(19, 'SN019', 'Plot019', 19, 'Region19', 'District19', 'Location19', 'GeoRef19', 19, 'RCA19', '2024-07-01', '2900.00', '330.00', 'Description19'),
(20, 'SN020', 'Plot020', 20, 'Region20', 'District20', 'Location20', 'GeoRef20', 20, 'RCA20', '2024-08-01', '3000.00', '340.00', 'Description20'),
(21, 'sn200', 'plot200', 200, '0', 'lilongwe', 'luwinga', 'https://google.com', 200, 'rca200', '2025-01-25', '200.00', '500.00', 'khaya man');

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
