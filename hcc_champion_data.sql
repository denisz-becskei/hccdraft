-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2021 at 10:40 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hcc_champion_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `champion_data`
--

CREATE TABLE `champion_data` (
  `champion_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `picked` int(11) DEFAULT 0,
  `won` int(11) DEFAULT 0,
  `banned` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `champion_data`
--

INSERT INTO `champion_data` (`champion_name`, `picked`, `won`, `banned`) VALUES
('Androxus', 16, 8, 2),
('Ash', 18, 8, 2),
('Atlas', 0, 0, 0),
('Barik', 42, 19, 4),
('Bomb King', 9, 7, 1),
('Buck', 5, 4, 2),
('Cassie', 17, 9, 0),
('Corvus', 3, 3, 0),
('Dredge', 12, 7, 3),
('Drogoz', 11, 5, 6),
('Evie', 9, 8, 9),
('Fernando', 25, 13, 8),
('Furia', 19, 9, 26),
('Grohk', 18, 10, 36),
('Grover', 23, 15, 26),
('Imani', 3, 0, 0),
('Inara', 28, 8, 0),
('Io', 9, 3, 2),
('Jenos', 21, 18, 1),
('Khan', 32, 20, 19),
('Kinessa', 9, 6, 12),
('Koga', 7, 1, 0),
('Lex', 3, 3, 1),
('Lian', 28, 11, 3),
('Maeve', 6, 4, 0),
('Makoa', 39, 17, 10),
('MalDamba', 27, 9, 0),
('Moji', 2, 2, 1),
('Octavia', 1, 1, 0),
('Pip', 5, 5, 0),
('Raum', 3, 2, 0),
('Ruckus', 5, 3, 0),
('Seris', 38, 13, 3),
('Sha Lin', 1, 1, 0),
('Skye', 10, 8, 1),
('Strix', 2, 2, 0),
('Talus', 0, 0, 0),
('Terminus', 18, 9, 41),
('Tiberius', 26, 9, 8),
('Torvald', 2, 1, 1),
('Tyra', 19, 3, 6),
('Vatu', 0, 0, 4),
('Viktor', 1, 0, 0),
('Vivian', 0, 0, 0),
('Vora', 17, 7, 18),
('Willo', 12, 5, 8),
('Yagorath', 7, 5, 13),
('Ying', 11, 8, 0),
('Zhin', 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `played_matches`
--

CREATE TABLE `played_matches` (
  `match_number` int(11) NOT NULL DEFAULT 0,
  `id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `played_matches`
--

INSERT INTO `played_matches` (`match_number`, `id`) VALUES
(62, 0);

-- --------------------------------------------------------

--
-- Table structure for table `player_data`
--

CREATE TABLE `player_data` (
  `player_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `kda` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'INF',
  `damage` float DEFAULT 0,
  `healing` float DEFAULT 0,
  `shielding` float DEFAULT 0,
  `mitigated` float DEFAULT 0,
  `ot` float DEFAULT 0,
  `played` int(11) DEFAULT 0,
  `kills` int(11) DEFAULT 0,
  `deaths` int(11) DEFAULT 0,
  `assists` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `player_data`
--

INSERT INTO `player_data` (`player_name`, `kda`, `damage`, `healing`, `shielding`, `mitigated`, `ot`, `played`, `kills`, `deaths`, `assists`) VALUES
('addathehamster', '3.67', 2167340, 0, 15009, 812947, 502, 17, 285, 101, 258),
('Addative', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('apitta', '1.32', 559223, 205525, 183935, 785664, 746, 10, 44, 60, 105),
('Bayeros', '0.69', 633583, 173214, 127711, 675670, 520, 13, 41, 95, 75),
('BeresBarbara', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('BlackFireZero89', '0.35', 76539, 0, 65527, 241996, 94, 3, 6, 23, 6),
('BlueJones123', '0.86', 665921, 0, 594208, 1499920, 2293, 16, 31, 92, 145),
('ByNexus182', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Csongli', '2.39', 907752, 49188, 714151, 1161370, 1798, 16, 89, 73, 257),
('CzegaMaster', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Dömi', '2.98', 1523470, 0, 0, 656180, 624, 14, 178, 84, 217),
('Draky346', '1.83', 828758, 19327, 53398, 665420, 212, 15, 107, 80, 119),
('FEROX90', '0.27', 265132, 0, 83610, 255618, 68, 6, 11, 45, 3),
('FEX20', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('ForHungary', '1.86', 738797, 176658, 451884, 1169090, 1052, 14, 80, 82, 217),
('frukimochiHUN', '0.24', 127471, 10762, 2640, 152230, 22, 4, 7, 35, 4),
('Gera5', '3.98', 739438, 1770100, 133872, 696916, 2541, 20, 75, 58, 467),
('ghhzgfhdhthfh', '0.61', 222590, 44182, 50794, 308377, 86, 6, 17, 47, 35),
('Gibior', '1.71', 958961, 148605, 204930, 664405, 869, 13, 75, 76, 165),
('Gyur1', '2.54', 1039680, 68212, 274539, 933465, 868, 16, 145, 85, 212),
('Hyuugakun', '0.37', 623881, 43816, 529323, 1230480, 1203, 16, 18, 120, 78),
('iPáSSii', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('IPlayGuitar', '0.93', 646784, 164022, 556780, 1342380, 873, 16, 61, 113, 133),
('jeszkar', '1.34', 847372, 338260, 417737, 1298740, 915, 19, 99, 120, 185),
('JustFinnii', '1.56', 1058130, 419545, 33334, 819205, 553, 16, 96, 98, 171),
('Kiruá', '3.29', 492855, 1064440, 3001, 411755, 1360, 13, 79, 51, 266),
('klogin15', '0.58', 611321, 87816, 189321, 689304, 396, 16, 58, 134, 59),
('Ky0c', '0.84', 95573, 367110, 0, 95072, 279, 3, 4, 21, 41),
('Kythops', '2.12', 246535, 395168, 303032, 455582, 1027, 8, 23, 27, 103),
('lack0', '2.99', 598189, 105911, 17896, 425852, 338, 10, 84, 45, 152),
('LadyCarnival', '0.83', 508392, 502865, 354212, 937640, 1344, 13, 22, 90, 157),
('LelItsEv3', '1.59', 364128, 122770, 24270, 314641, 106, 6, 38, 41, 81),
('loneybee', '1.42', 1095500, 506673, 0, 701885, 378, 16, 113, 114, 148),
('Márton72', '1.2', 943128, 1846, 678695, 1251010, 1458, 16, 52, 83, 144),
('matre101', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Meliodas', '1.63', 752018, 44485, 227018, 850382, 428, 12, 83, 71, 98),
('MexTer131', '2.27', 1366680, 193626, 137620, 1007700, 1108, 21, 178, 112, 228),
('mikeyyLegit', '1.82', 587898, 1435090, 3000, 468462, 667, 13, 36, 58, 208),
('MyIQisToxic', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('NotosLP', '0.87', 323766, 47707, 272321, 740039, 320, 7, 33, 58, 53),
('Physicx1', '2.77', 1036710, 256357, 62052, 673022, 1232, 16, 141, 79, 233),
('Pompeius96', '2.46', 1131930, 589765, 563460, 1188190, 2004, 20, 103, 91, 363),
('rexximan', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Richikee', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('Salazar12', '1.29', 1084410, 143463, 2763, 679120, 375, 16, 108, 111, 106),
('Seleanne', '0.6', 44586, 198902, 0, 54001, 21, 3, 2, 10, 12),
('Sn0wF0xHun', '0.4', 154656, 1059470, 0, 367914, 373, 12, 7, 68, 60),
('SotoMoto0', '1.52', 185625, 2253190, 0, 389388, 533, 16, 13, 60, 235),
('stelardani', '5.7', 260527, 11814, 127311, 313847, 438, 6, 33, 10, 72),
('Szalka', '1.3', 970456, 374382, 83077, 834212, 596, 19, 97, 118, 168),
('tesnea', '3.64', 934001, 247301, 0, 391633, 535, 13, 130, 53, 189),
('TheLastPillow', '2.51', 1514770, 0, 0, 797783, 526, 13, 174, 86, 126),
('TheSGManiac', '1.52', 1012590, 198140, 80153, 1000670, 876, 15, 122, 114, 153),
('Truthahn', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0),
('TuhaPest', '2.97', 705290, 1778650, 0, 620016, 1940, 21, 75, 64, 345),
('Vanitaes', '1.95', 1076580, 31918, 928524, 1916920, 1836, 21, 91, 96, 290),
('virdzs', '1.41', 122611, 124824, 58283, 117485, 327, 3, 10, 17, 42),
('YouWasTaken', '3.45', 528206, 29262, 180790, 506800, 1012, 12, 81, 35, 119),
('z0rn', 'INF', 0, 0, 0, 0, 0, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `champion_data`
--
ALTER TABLE `champion_data`
  ADD PRIMARY KEY (`champion_name`);

--
-- Indexes for table `player_data`
--
ALTER TABLE `player_data`
  ADD PRIMARY KEY (`player_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
