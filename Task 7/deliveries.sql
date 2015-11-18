-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2015 at 07:34 PM
-- Server version: 5.6.24
-- PHP Version: 5.5.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `deliveries`
--
CREATE DATABASE IF NOT EXISTS `deliveries` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `deliveries`;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_method`
--

CREATE TABLE IF NOT EXISTS `delivery_method` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `weight_from` float DEFAULT NULL,
  `weight_to` float DEFAULT NULL,
  `notes` text NOT NULL,
  `status` enum('unavailable','free','has_price','ranges') NOT NULL DEFAULT 'unavailable'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `delivery_method`:
--

--
-- Dumping data for table `delivery_method`
--

INSERT INTO `delivery_method` (`id`, `name`, `value`, `url`, `weight_from`, `weight_to`, `notes`, `status`) VALUES
(1, 'Delivery method 1', '35.50', 'http://www.malasrpskaprodavnica.com', 0, 5, 'Sending goods to EU countries', 'has_price'),
(2, 'Delivery method 2', NULL, 'http://shop.ristora.it', 3, 10, 'Ristora tea and hot chocolate', 'ranges'),
(3, 'Delivery method 3', NULL, '', NULL, NULL, 'Any weight', 'ranges'),
(4, 'Delivery method 4', NULL, 'http://www.djaksport.com', 0.5, 15, 'Sports equipment with two categories – small and large', 'unavailable'),
(5, 'Delivery method 5', '0.00', 'http://www.sportvision.rs', NULL, NULL, 'Shoes and sneakers', 'free');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_ranges`
--

CREATE TABLE IF NOT EXISTS `delivery_ranges` (
  `id` int(11) NOT NULL,
  `delivery_method_id` int(11) NOT NULL,
  `range_from` decimal(10,2) DEFAULT NULL,
  `range_to` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- RELATIONS FOR TABLE `delivery_ranges`:
--   `delivery_method_id`
--       `delivery_method` -> `id`
--

--
-- Dumping data for table `delivery_ranges`
--

INSERT INTO `delivery_ranges` (`id`, `delivery_method_id`, `range_from`, `range_to`, `price`) VALUES
(1, 2, '30.00', '80.00', '0.50'),
(2, 2, '80.01', '120.00', '5.20'),
(3, 2, '120.01', '200.00', '8.40'),
(4, 3, '5.00', '250.00', '10.00'),
(5, 3, '251.00', '1000.00', '20.00'),
(6, 2, '200.01', '500.00', '10.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivery_method`
--
ALTER TABLE `delivery_method`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_ranges`
--
ALTER TABLE `delivery_ranges`
  ADD PRIMARY KEY (`id`), ADD KEY `delivery_method_id` (`delivery_method_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivery_method`
--
ALTER TABLE `delivery_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `delivery_ranges`
--
ALTER TABLE `delivery_ranges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_ranges`
--
ALTER TABLE `delivery_ranges`
ADD CONSTRAINT `fk_delivery_method_id` FOREIGN KEY (`delivery_method_id`) REFERENCES `delivery_method` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
