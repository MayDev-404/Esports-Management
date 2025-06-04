-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 12:15 PM
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
-- Database: `esports_management`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetMatchesForTeam` (IN `team_id` INT)   BEGIN
    SELECT gm.match_id,
           gm.match_date,
           gm.result,
           t1.team_id AS team1_id,
           t1.name AS team1_name,
           t2.team_id AS team2_id,
           t2.name AS team2_name
    FROM gamematch gm
    JOIN Team t1 ON gm.team1_id = t1.team_id
    JOIN Team t2 ON gm.team2_id = t2.team_id
    WHERE gm.team1_id = team_id OR gm.team2_id = team_id;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetPlayerProfile` (`user` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE profile TEXT;
    DECLARE uname VARCHAR(100);
    DECLARE prole VARCHAR(50);
    DECLARE jdate DATE;
    DECLARE rating DECIMAL(3,2);
    DECLARE tname VARCHAR(100);
    DECLARE tcreated DATE;

    SELECT p.username, p.role, p.join_date, p.avg_rating, t.name, t.creation_date
    INTO uname, prole, jdate, rating, tname, tcreated
    FROM Player p
    LEFT JOIN Team t ON p.team_id = t.team_id
    WHERE p.user_id = user;

    SET profile = CONCAT(
        'Username: ', uname, '\n',
        'Player Role: ', prole, '\n',
        'Join Date: ', jdate, '\n',
        'Avg Rating: ', rating, '\n',
        'Team Name: ', IFNULL(tname, 'No Team'), '\n',
        'Team Created: ', IFNULL(tcreated, 'N/A')
    );

    RETURN profile;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gamematch`
--

CREATE TABLE `gamematch` (
  `match_id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team1_id` int(11) DEFAULT NULL,
  `team2_id` int(11) DEFAULT NULL,
  `match_date` datetime DEFAULT NULL,
  `result` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gamematch`
--

INSERT INTO `gamematch` (`match_id`, `tournament_id`, `team1_id`, `team2_id`, `match_date`, `result`) VALUES
(3, 1, 1, 2, '2025-05-02 16:00:00', 'Pending'),
(4, 1, 2, 4, '2025-05-05 18:00:00', 'Pending'),
(5, 1, 1, 4, '2025-05-08 17:30:00', 'Pending'),
(6, 2, 2, 1, '2025-05-16 15:00:00', 'Pending'),
(7, 2, 4, 2, '2025-05-20 19:00:00', 'Pending'),
(8, 2, 4, 1, '2025-05-23 20:30:00', 'Pending'),
(9, 4, 1, 4, '2025-04-21 14:00:00', 'Team 1 won'),
(10, 4, 2, 4, '2025-04-24 17:00:00', 'Team 4 won'),
(11, 4, 1, 2, '2025-04-28 16:30:00', 'Draw');

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `player_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `avg_rating` decimal(3,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`player_id`, `username`, `team_id`, `role`, `join_date`, `avg_rating`, `user_id`) VALUES
(1, 'ShadowAce', 1, 'Assaulter', '2024-01-20', 4.50, 301),
(2, 'StealthKing', 1, 'Leader', '2024-01-21', 4.20, 302),
(3, 'NightHawk', 1, 'Support', '2024-01-22', 4.00, 303),
(4, 'FlameFury', 2, 'Attacker', '2024-02-07', 4.30, 304),
(5, 'CrimsonGhost', 2, 'Defender', '2024-02-08', 4.10, 305),
(8, 'TitanBlazer', 4, 'Tank', '2024-04-03', 4.00, 306),
(9, 'FireBreaker', 4, 'Striker', '2024-04-04', 3.90, 307),
(13, 'Derek', 1, 'Sniper', '2025-04-14', 4.00, 308);

-- --------------------------------------------------------

--
-- Table structure for table `prize`
--

CREATE TABLE `prize` (
  `prize_id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `match_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prize`
--

INSERT INTO `prize` (`prize_id`, `tournament_id`, `team_id`, `amount`, `match_id`) VALUES
(25, 4, 1, 8000.00, 9),
(26, 4, 4, 5000.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `stat_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `kills` int(11) DEFAULT NULL,
  `deaths` int(11) DEFAULT NULL,
  `win_rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`stat_id`, `player_id`, `tournament_id`, `kills`, `deaths`, `win_rate`) VALUES
(1, 1, 1, 50, 10, 85.00),
(2, 2, 1, 40, 15, 75.00),
(3, 3, 1, 30, 20, 70.00),
(4, 4, 2, 60, 8, 90.00),
(5, 5, 2, 50, 12, 80.00),
(6, 8, 4, 55, 10, 88.00),
(7, 9, 4, 45, 18, 70.00),
(8, 13, 5, 70, 5, 95.00),
(9, 1, 3, 35, 15, 80.00),
(10, 2, 3, 25, 18, 60.00);

-- --------------------------------------------------------

--
-- Table structure for table `stream`
--

CREATE TABLE `stream` (
  `stream_id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `viewer_count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stream`
--

INSERT INTO `stream` (`stream_id`, `match_id`, `platform`, `url`, `viewer_count`) VALUES
(1, 3, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 7566),
(2, 4, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 4398),
(3, 5, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 7290),
(4, 6, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 4258),
(5, 7, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 7420),
(6, 8, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 5326),
(7, 9, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 3370),
(8, 10, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 8875),
(9, 11, 'YouTube', 'https://www.youtube.com/live/ONamVyWsFvo?si=nubIWwGEIvM4c7q9', 6265);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `creation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `name`, `logo`, `manager_id`, `creation_date`) VALUES
(1, 'Shadow Strikers', 'shadow_logo.png', 201, '2024-01-15'),
(2, 'Crimson Blaze', 'crimson_logo.png', 202, '2024-02-05'),
(4, 'Blazing Titans', 'titans_logo.png', 203, '2024-04-01');

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `tournament_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `game_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `prize_pool` decimal(10,2) DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`tournament_id`, `name`, `game_id`, `start_date`, `end_date`, `prize_pool`, `status`) VALUES
(1, 'Battle Royale Bash', 101, '2025-05-01', '2025-05-10', 50000.00, ''),
(2, 'Global Esports Cup', 102, '2025-05-15', '2025-05-25', 75000.00, ''),
(3, 'Legends League', 103, '2025-04-05', '2025-04-15', 60000.00, 'completed'),
(4, 'Pro Gamers Showdown', 104, '2025-04-20', '2025-04-30', 80000.00, 'ongoing'),
(5, 'Ultimate Clash', 105, '2025-06-01', '2025-06-10', 100000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','team_manager','player','spectator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `password_hash`, `role`) VALUES
(1, 'admin@example.com', 'password123', 'admin'),
(2, 'player@example.com', 'password123', 'player'),
(3, 'manager@example.com', 'password123', 'team_manager'),
(4, 'spectator@example.com', 'password123', 'spectator'),
(201, 'manager1@esports.com', 'pass123', 'team_manager'),
(202, 'manager2@esports.com', 'pass123', 'team_manager'),
(203, 'manager3@esports.com', 'pass123', 'team_manager'),
(301, 'shadowace@example.com', 'password123', 'player'),
(302, 'stealthking@example.com', 'password123', 'player'),
(303, 'nighthawk@example.com', 'password123', 'player'),
(304, 'flamefury@example.com', 'password123', 'player'),
(305, 'crimsonghost@example.com', 'password123', 'player'),
(306, 'titanblazer@example.com', 'password123', 'player'),
(307, 'firebreaker@example.com', 'password123', 'player'),
(308, 'derek@example.com', 'password123', 'player'),
(311, 'hello@123.com', '123', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gamematch`
--
ALTER TABLE `gamematch`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `team1_id` (`team1_id`),
  ADD KEY `team2_id` (`team2_id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `prize`
--
ALTER TABLE `prize`
  ADD PRIMARY KEY (`prize_id`),
  ADD UNIQUE KEY `match_id` (`match_id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`stat_id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Indexes for table `stream`
--
ALTER TABLE `stream`
  ADD PRIMARY KEY (`stream_id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`tournament_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gamematch`
--
ALTER TABLE `gamematch`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `prize`
--
ALTER TABLE `prize`
  MODIFY `prize_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `stream`
--
ALTER TABLE `stream`
  MODIFY `stream_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gamematch`
--
ALTER TABLE `gamematch`
  ADD CONSTRAINT `gamematch_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`),
  ADD CONSTRAINT `gamematch_ibfk_2` FOREIGN KEY (`team1_id`) REFERENCES `team` (`team_id`),
  ADD CONSTRAINT `gamematch_ibfk_3` FOREIGN KEY (`team2_id`) REFERENCES `team` (`team_id`);

--
-- Constraints for table `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`),
  ADD CONSTRAINT `player_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `prize`
--
ALTER TABLE `prize`
  ADD CONSTRAINT `prize_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`),
  ADD CONSTRAINT `prize_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`),
  ADD CONSTRAINT `prize_ibfk_3` FOREIGN KEY (`match_id`) REFERENCES `gamematch` (`match_id`);

--
-- Constraints for table `stats`
--
ALTER TABLE `stats`
  ADD CONSTRAINT `stats_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `player` (`player_id`),
  ADD CONSTRAINT `stats_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`);

--
-- Constraints for table `stream`
--
ALTER TABLE `stream`
  ADD CONSTRAINT `stream_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `gamematch` (`match_id`);

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
