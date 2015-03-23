-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 23, 2015 at 04:26 PM
-- Server version: 5.5.41
-- PHP Version: 5.4.36-0+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `brewmaster`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFridgeDuty`()
BEGIN
CREATE TEMPORARY TABLE t1 (id int AUTO_INCREMENT PRIMARY KEY, Date datetime, Action varchar(50));
CREATE TEMPORARY TABLE t2 (id int AUTO_INCREMENT PRIMARY KEY, Date datetime, Action varchar(50));
CREATE TEMPORARY TABLE t3 (id int AUTO_INCREMENT PRIMARY KEY, Date datetime, Action varchar(50));
INSERT INTO t1
SELECT NULL, Date, Action from ActivityLog where Date > DATE_ADD(NOW(), INTERVAL -6 HOUR) order by Date asc;
INSERT INTO t2
SELECT NULL, Date, Action from ActivityLog where Date > DATE_ADD(NOW(), INTERVAL -6 HOUR) order by Date asc;
INSERT INTO t3
SELECT NULL, Date, Action from ActivityLog where Date > DATE_ADD(NOW(), INTERVAL -6 HOUR) order by Date asc;

select
    t.Action,
    SUM(TIME_TO_SEC(TIMEDIFF(tn.Date, t.Date))) as total_seconds
from
    t1 t
    inner join t3 tn on
    	tn.id = (select t2.id 
    			from t2 
    			where t2.id > t.id and t2.Action <> t.Action
    			order by t2.id limit 1)
group by
    t.Action;
drop table t1;
drop table t2;
drop table t3;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ActivityLog`
--

CREATE TABLE IF NOT EXISTS `ActivityLog` (
  `Date` datetime NOT NULL,
  `Action` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TempEmulator`
--

CREATE TABLE IF NOT EXISTS `TempEmulator` (
  `Date` datetime NOT NULL,
  `Temp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TemperatureSchedule`
--

CREATE TABLE IF NOT EXISTS `TemperatureSchedule` (
  `KeyDate` datetime NOT NULL,
  `Temperature` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TemperatureStatistics`
--

CREATE TABLE IF NOT EXISTS `TemperatureStatistics` (
  `ReadingTime` datetime NOT NULL,
  `Temperature` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
