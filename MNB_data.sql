-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 11, 2024 at 09:12 AM
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
-- Table structure for table `ChatMessages`
--

CREATE TABLE `ChatMessages` (
  `IDMessage` int(11) NOT NULL,
  `IDUser` varchar(255) NOT NULL,
  `IDProjet` int(11) NOT NULL,
  `Message` text NOT NULL,
  `Date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ChatMessages`
--

INSERT INTO `ChatMessages` (`IDMessage`, `IDUser`, `IDProjet`, `Message`, `Date`) VALUES
(1, '1', 17, 'test', '2024-12-09 17:41:06'),
(2, '10', 17, 'wee', '2024-12-09 17:44:42');

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
(18, 'Gestions des plats', '16', 'Management des plats dans un restaurant', NULL, 'En cours', 1692, '1'),
(19, 'Gestion des quantités des produits', '21', 'Les stocks des produits a vendre', NULL, 'En cours', 13340, '1');

-- --------------------------------------------------------

--
-- Table structure for table `Tache`
--

CREATE TABLE `Tache` (
  `IDTache` int(11) NOT NULL,
  `Titre` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `datedebut` varchar(50) NOT NULL,
  `datefin` varchar(50) NOT NULL,
  `IDUser` text DEFAULT NULL,
  `IDProjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tache`
--

INSERT INTO `Tache` (`IDTache`, `Titre`, `description`, `datedebut`, `datefin`, `IDUser`, `IDProjet`) VALUES
(61666, 'Configuration de la base de données', 'Concevoir le schéma de la base de données et implémenter les tables, relations et procédures nécessaires', '2024-12-08', '2024-12-13', '1', 18),
(61667, 'Implémentation de l’authentification', 'Ajouter un système d’authentification sécurisé avec gestion des rôles et permissions', '2024-12-08', '2024-12-10', '1', 19),
(61668, 'dd', 'dd', '2024-12-02', '2024-12-08', '1', 20);

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
(1, 'alice@test.com', '$2y$10$l64frc2UI1tOD.uJwGxubucfGiV3W8la627GMBUoF3Sh/SYRHde3C', 'Alice', 'Smith', '675031c1f36e5_147568.jpg', 'Admin'),
(10, 'lucie.lopez@gmail.com', '$2y$10$fNxYFk0IK8X66oZSvV2SqODAiNQmrJeIw2xxTamV5tjFB/DCFHwwC', 'Lucie', 'Lopez', 'default.png', 'User'),
(11, 'sd@gmail.com', '$2y$10$N079tzx.fwgpRSb3HvK/z.Hc1WKj50pdsxadH36xS4lleQNmqahpe', 'ww', 'ss', 'default.png', 'User'),
(12, 'qswwq@gmail.com', '$2y$10$YnRByE7MIOBuhtDhbMQMweGYjQG4f0zbdvNl98ab94xQd0rn9zWhO', 'qqs', 'qwq', 'default.png', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ChatMessages`
--
ALTER TABLE `ChatMessages`
  ADD PRIMARY KEY (`IDMessage`);

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
  ADD PRIMARY KEY (`IDTache`);

--
-- Indexes for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`IDUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ChatMessages`
--
ALTER TABLE `ChatMessages`
  MODIFY `IDMessage` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Files`
--
ALTER TABLE `Files`
  MODIFY `IDFile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Projet`
--
ALTER TABLE `Projet`
  MODIFY `IDProjet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Tache`
--
ALTER TABLE `Tache`
  MODIFY `IDTache` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61674;

--
-- AUTO_INCREMENT for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `IDUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
