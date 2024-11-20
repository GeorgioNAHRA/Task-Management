-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 20, 2024 at 03:04 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

DROP DATABASE IF EXISTS gate_data;

-- Créer une nouvelle base de données
CREATE DATABASE gate_data;

-- Utiliser la base de données nouvellement créée
USE gate_data;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gate_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `commentaire`
--

CREATE TABLE `commentaire` (
  `IDcommentaire` int(11) NOT NULL,
  `contenu` varchar(50) NOT NULL,
  `datecrea` date NOT NULL,
  `IDTache` int(11) NOT NULL,
  `IDUser` int(11) NOT NULL,
  `IDTache_contenir2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commentaire`
--

INSERT INTO `commentaire` (`IDcommentaire`, `contenu`, `datecrea`, `IDTache`, `IDUser`, `IDTache_contenir2`) VALUES
(1, 'Bon début de travail, continuez comme ça !', '2024-04-02', 1, 3, 1),
(2, 'Vérifiez les détails de la maquette.', '2024-04-06', 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `creer`
--

CREATE TABLE `creer` (
  `IDProjet` int(11) NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creer`
--

INSERT INTO `creer` (`IDProjet`, `IDUser`) VALUES
(1, 3),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Dossier`
--

CREATE TABLE `Dossier` (
  `IDDocumment` int(11) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  `dateup` varchar(50) NOT NULL,
  `IDUser` int(11) NOT NULL,
  `idProjet` int(11) NOT NULL,
  `IDProjet__contenir1` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Dossier`
--

INSERT INTO `Dossier` (`IDDocumment`, `Nom`, `url`, `dateup`, `IDUser`, `idProjet`, `IDProjet__contenir1`) VALUES
(1, 'Spécifications techniques', 'http://example.com/specs.pdf', '2024-04-01', 1, 1, 1),
(2, 'Maquettes UI', 'http://example.com/ui-mockups.pdf', '2024-04-05', 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipe`
--

CREATE TABLE `equipe` (
  `Idequipe` int(11) NOT NULL,
  `roles` varchar(50) NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipe`
--

INSERT INTO `equipe` (`Idequipe`, `roles`, `IDUser`) VALUES
(1, 'Développeur', 1),
(2, 'Designer', 2),
(3, 'Chef de projet', 3);

-- --------------------------------------------------------

--
-- Table structure for table `faire`
--

CREATE TABLE `faire` (
  `IDTache` int(11) NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faire`
--

INSERT INTO `faire` (`IDTache`, `IDUser`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `membreequipe`
--

CREATE TABLE `membreequipe` (
  `IDUser` int(11) NOT NULL,
  `roles` varchar(50) NOT NULL,
  `IDequipe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membreequipe`
--

INSERT INTO `membreequipe` (`IDUser`, `roles`, `IDequipe`) VALUES
(1, 'Développeur', 1),
(2, 'Designer', 2),
(3, 'Chef de projet', 3);

-- --------------------------------------------------------

--
-- Table structure for table `modifier`
--

CREATE TABLE `modifier` (
  `Idequipe` int(11) NOT NULL,
  `IDProjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modifier`
--

INSERT INTO `modifier` (`Idequipe`, `IDProjet`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `IDnotif` int(11) NOT NULL,
  `Message` varchar(50) NOT NULL,
  `datenotif` date NOT NULL,
  `typenotif` varchar(50) NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Notification`
--

INSERT INTO `Notification` (`IDnotif`, `Message`, `datenotif`, `typenotif`, `IDUser`) VALUES
(1, 'Nouvelle tâche assignée', '2024-05-01', 'Tâche', 1),
(2, 'Commentaire ajouté', '2024-05-02', 'Commentaire', 2);

-- --------------------------------------------------------

--
-- Table structure for table `Notifier`
--

CREATE TABLE `Notifier` (
  `IDnotif` int(11) NOT NULL,
  `IDProjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Notifier`
--

INSERT INTO `Notifier` (`IDnotif`, `IDProjet`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Projet`
--

CREATE TABLE `Projet` (
  `IDProjet` int(11) NOT NULL,
  `nomProjet` varchar(50) NOT NULL,
  `Duree_projet` varchar(50) NOT NULL,
  `descriptionProjet` varchar(50) NOT NULL,
  `tachesprojets` varchar(50) NOT NULL,
  `Statu` varchar(50) NOT NULL,
  `budjet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Projet`
--

INSERT INTO `Projet` (`IDProjet`, `nomProjet`, `Duree_projet`, `descriptionProjet`, `tachesprojets`, `Statu`, `budjet`) VALUES
(1, 'Projet Alpha', '6 mois', 'Développement de la plateforme Alpha', 'Tâche 1, Tâche 2', 'En cours', 10000),
(2, 'Projet Beta', '3 mois', 'Mise à jour de la plateforme Beta', 'Tâche 3, Tâche 4', 'Terminé', 5000);

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
  `IDUser` int(11) NOT NULL,
  `IDProjet` int(11) NOT NULL,
  `IDProjet_avoir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Tache`
--

INSERT INTO `Tache` (`IDTache`, `Titre`, `description`, `datedebut`, `datefin`, `IDUser`, `IDProjet`, `IDProjet_avoir`) VALUES
(1, 'Tâche 1', 'Développer la fonctionnalité de login', '2024-04-01', '2024-04-10', 1, 1, 1),
(2, 'Tâche 2', 'Créer la page d accueil', '2024-04-11', '2024-04-20', 2, 1, 1),
(3, 'Tâche 3', 'Mettre à jour le backend', '2024-03-01', '2024-03-15', 3, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `IDUser` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `MDP` varchar(50) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(50) NOT NULL,
  `photo` varchar(50) NOT NULL,
  `Statu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Utilisateur`
--

INSERT INTO `Utilisateur` (`IDUser`, `Email`, `MDP`, `Nom`, `Prenom`, `photo`, `Statu`) VALUES
(1, 'alice@example.com', 'password123', 'Alice', 'Smith', 'default.png', 'Admin'),
(2, 'bob@example.com', 'password456', 'Bob', 'Brown', 'test.jpg', 'accepter'),
(3, 'charlie@example.com', 'password789', 'Charlie', 'Davis', 'test.jpg', 'accepter');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`IDcommentaire`),
  ADD UNIQUE KEY `commentaire_AK` (`IDTache`,`IDUser`),
  ADD KEY `commentaire_Tache_FK` (`IDTache_contenir2`);

--
-- Indexes for table `creer`
--
ALTER TABLE `creer`
  ADD PRIMARY KEY (`IDProjet`,`IDUser`),
  ADD KEY `creer_Utilisateur0_FK` (`IDUser`);

--
-- Indexes for table `Dossier`
--
ALTER TABLE `Dossier`
  ADD PRIMARY KEY (`IDDocumment`),
  ADD UNIQUE KEY `Dossier_AK` (`IDUser`,`idProjet`),
  ADD KEY `Dossier_Projet_FK` (`IDProjet__contenir1`);

--
-- Indexes for table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`Idequipe`),
  ADD KEY `equipe_membreequipe_FK` (`IDUser`);

--
-- Indexes for table `faire`
--
ALTER TABLE `faire`
  ADD PRIMARY KEY (`IDTache`,`IDUser`),
  ADD KEY `faire_membreequipe0_FK` (`IDUser`);

--
-- Indexes for table `membreequipe`
--
ALTER TABLE `membreequipe`
  ADD PRIMARY KEY (`IDUser`),
  ADD UNIQUE KEY `membreequipe_AK` (`IDequipe`);

--
-- Indexes for table `modifier`
--
ALTER TABLE `modifier`
  ADD PRIMARY KEY (`Idequipe`,`IDProjet`),
  ADD KEY `modifier_Projet0_FK` (`IDProjet`);

--
-- Indexes for table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`IDnotif`),
  ADD UNIQUE KEY `Notification_AK` (`IDUser`);

--
-- Indexes for table `Notifier`
--
ALTER TABLE `Notifier`
  ADD PRIMARY KEY (`IDnotif`,`IDProjet`),
  ADD KEY `Notifier_Projet0_FK` (`IDProjet`);

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
  ADD UNIQUE KEY `Tache_AK` (`IDUser`,`IDProjet`),
  ADD KEY `Tache_Projet_FK` (`IDProjet_avoir`);

--
-- Indexes for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`IDUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `IDcommentaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Dossier`
--
ALTER TABLE `Dossier`
  MODIFY `IDDocumment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `Idequipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `IDnotif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Projet`
--
ALTER TABLE `Projet`
  MODIFY `IDProjet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Tache`
--
ALTER TABLE `Tache`
  MODIFY `IDTache` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61660;

--
-- AUTO_INCREMENT for table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `IDUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `commentaire_Tache_FK` FOREIGN KEY (`IDTache_contenir2`) REFERENCES `Tache` (`IDTache`);

--
-- Constraints for table `creer`
--
ALTER TABLE `creer`
  ADD CONSTRAINT `creer_Projet_FK` FOREIGN KEY (`IDProjet`) REFERENCES `Projet` (`IDProjet`),
  ADD CONSTRAINT `creer_Utilisateur0_FK` FOREIGN KEY (`IDUser`) REFERENCES `Utilisateur` (`IDUser`);

--
-- Constraints for table `Dossier`
--
ALTER TABLE `Dossier`
  ADD CONSTRAINT `Dossier_Projet_FK` FOREIGN KEY (`IDProjet__contenir1`) REFERENCES `Projet` (`IDProjet`);

--
-- Constraints for table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `equipe_membreequipe_FK` FOREIGN KEY (`IDUser`) REFERENCES `membreequipe` (`IDUser`);

--
-- Constraints for table `faire`
--
ALTER TABLE `faire`
  ADD CONSTRAINT `faire_Tache_FK` FOREIGN KEY (`IDTache`) REFERENCES `Tache` (`IDTache`),
  ADD CONSTRAINT `faire_membreequipe0_FK` FOREIGN KEY (`IDUser`) REFERENCES `membreequipe` (`IDUser`);

--
-- Constraints for table `modifier`
--
ALTER TABLE `modifier`
  ADD CONSTRAINT `modifier_Projet0_FK` FOREIGN KEY (`IDProjet`) REFERENCES `Projet` (`IDProjet`),
  ADD CONSTRAINT `modifier_equipe_FK` FOREIGN KEY (`Idequipe`) REFERENCES `equipe` (`Idequipe`);

--
-- Constraints for table `Notifier`
--
ALTER TABLE `Notifier`
  ADD CONSTRAINT `Notifier_Notification_FK` FOREIGN KEY (`IDnotif`) REFERENCES `Notification` (`IDnotif`),
  ADD CONSTRAINT `Notifier_Projet0_FK` FOREIGN KEY (`IDProjet`) REFERENCES `Projet` (`IDProjet`);

--
-- Constraints for table `Tache`
--
ALTER TABLE `Tache`
  ADD CONSTRAINT `Tache_Projet_FK` FOREIGN KEY (`IDProjet_avoir`) REFERENCES `Projet` (`IDProjet`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
