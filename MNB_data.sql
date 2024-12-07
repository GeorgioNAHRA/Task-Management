-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 07, 2024 at 04:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mnb_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `Files`
--

CREATE TABLE `Files` (
  `IDFile` int(11) NOT NULL,
  `FileName` varchar(255) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `DateUploaded` datetime DEFAULT current_timestamp(),
  `UploadedBy` int(11) NOT NULL,
  `IDProjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Projet`
--

CREATE TABLE `Projet` (
  `IDProjet` int(11) NOT NULL,
  `nomProjet` varchar(50) NOT NULL,
  `Duree_projet` varchar(50) NOT NULL,
  `descriptionProjet` varchar(50) NOT NULL,
  `IDTache` int(11) DEFAULT NULL,
  `Statu` varchar(50) NOT NULL,
  `budget` int(11) NOT NULL,
  `IDUsers` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Projet`
--

INSERT INTO `Projet` (`IDProjet`, `nomProjet`, `Duree_projet`, `descriptionProjet`, `IDTache`, `Statu`, `budget`, `IDUsers`) VALUES
(1, 'Projet Alpha', '6 mois', 'Développement de la plateforme Alpha', NULL, 'En cours', 10000, NULL),
(2, 'Projet Beta', '3 mois', 'Mise à jour de la plateforme Beta', NULL, 'Terminé', 5000, NULL),
(14, 'TestLOL', '12', 'LOL', NULL, 'En cours', 10000, '1,2'),
(15, 'test', '111', 'sss', NULL, 'En cours', 11, '2,9');

-- --------------------------------------------------------

--
-- Table structure for table `Tache`
--

CREATE TABLE `Tache` (
  `IDTache` int(11) NOT NULL,
  `Titre` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `datedebut` varchar(50) NOT NULL,
  `datefin` varchar(50) NOT NULL,
  `IDUser` text DEFAULT NULL,
  `IDProjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tache`
--

INSERT INTO `Tache` (`IDTache`, `Titre`, `description`, `datedebut`, `datefin`, `IDUser`, `IDProjet`) VALUES
(61663, 'dsd', 'sd', '2024-12-11', '2024-12-25', '9', 15);

-- --------------------------------------------------------

--
-- Table structure for table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `IDUser` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `MDP` varchar(255) DEFAULT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `photo` varchar(50) NOT NULL,
  `Statu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Utilisateur`
--

INSERT INTO `Utilisateur` (`IDUser`, `Email`, `MDP`, `Nom`, `Prenom`, `photo`, `Statu`) VALUES
(1, 'alice@example.com', 'password123', 'Alice', 'Smith', '675031c1f36e5_147568.jpg', 'Admin'),
(2, 'bob@example.com', 'password456', 'Bob', 'Brown', 'test.jpg', 'User'),
(3, 'charlie@example.com', 'password789', 'Charlie', 'Davis', 'test.jpg', 'User'),
(9, 'qq@gmail.com', 'q', 'Q', 'q', '67502f4ed4e6b_147568.jpg', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Files`
--
ALTER TABLE `Files`
  ADD PRIMARY KEY (`IDFile`),
  ADD KEY `UploadedBy` (`UploadedBy`),
  ADD KEY `IDProjet` (`IDProjet`);

--
-- Indexes for table `Projet`
--
ALTER TABLE `Projet`
  ADD PRIMARY KEY (`IDProjet`);

--
-- Indexes for table `Tache`
--
ALTER TABLE `Tache`
  ADD PRIMARY KEY (`IDTache`),
  ADD UNIQUE KEY `Tache_AK` (`IDUser`,`IDProjet`) USING HASH;

--
-- Indexes for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`IDUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Files`
--
ALTER TABLE `Files`
  MODIFY `IDFile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Projet`
--
ALTER TABLE `Projet`
  MODIFY `IDProjet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Tache`
--
ALTER TABLE `Tache`
  MODIFY `IDTache` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61664;

--
-- AUTO_INCREMENT for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `IDUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Files`
--
ALTER TABLE `Files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`UploadedBy`) REFERENCES `Utilisateur` (`IDUser`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`IDProjet`) REFERENCES `Projet` (`IDProjet`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
