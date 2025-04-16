-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 06, 2025 at 02:38 PM
-- Server version: 10.3.39-MariaDB-0+deb10u1
-- PHP Version: 8.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `GSBVTTMobile`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cabinet`
--

CREATE TABLE `Cabinet` (
  `id_cabinet` int(11) NOT NULL,
  `rue` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(5) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Cabinet`
--

INSERT INTO `Cabinet` (`id_cabinet`, `rue`, `ville`, `code_postal`, `telephone`, `latitude`, `longitude`) VALUES
(1, '15 rue des Lilas', 'Paris', '75001', '0123456789', 48.85660000, 2.35220000),
(2, '8 avenue Victor Hugo', 'Lyon', '69002', '0234567891', 45.75780000, 4.83200000),
(3, '25 boulevard Pasteur', 'Marseille', '13001', '0345678912', 43.29650000, 5.36980000),
(4, '12 rue de la Paix', 'Lille', '59000', '0456789123', 50.62920000, 3.05730000),
(5, '3 place de la République', 'Bordeaux', '33000', '0567891234', 44.83780000, 0.57920000),
(6, '12 rue Modifiée', 'VilleModifiée', '75000', '0123456789', 48.85660000, 2.35220000),
(7, '12 rue Modifiée', 'VilleModifiée', '75000', '0123456789', 48.85660000, 2.35220000);

-- --------------------------------------------------------

--
-- Table structure for table `Medecin`
--

CREATE TABLE `Medecin` (
  `id_medecin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `id_cabinet` int(11) NOT NULL,
  `id_visiteur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Medecin`
--

INSERT INTO `Medecin` (`id_medecin`, `nom`, `prenom`, `telephone`, `id_cabinet`, `id_visiteur`) VALUES
(2, 'Martin', 'Marie', '0234567891', 2, 1),
(3, 'Bernard', 'Pierre', '0345678912', 3, 2),
(4, 'Robert', 'Sophie', '0456789123', 4, 2),
(5, 'Petit', 'Michel', '0567891234', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Visite`
--

CREATE TABLE `Visite` (
  `id_visite` int(11) NOT NULL,
  `id_visiteur` int(11) NOT NULL,
  `id_medecin` int(11) NOT NULL,
  `date_visite` date NOT NULL,
  `heure_arrivee` datetime DEFAULT NULL,
  `heure_debut_entretien` datetime DEFAULT NULL,
  `temps_attente` time DEFAULT NULL,
  `heure_depart` datetime DEFAULT NULL,
  `temps_visite` time DEFAULT NULL,
  `rendez_vous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Visite`
--

INSERT INTO `Visite` (`id_visite`, `id_visiteur`, `id_medecin`, `date_visite`, `heure_arrivee`, `heure_debut_entretien`, `temps_attente`, `heure_depart`, `temps_visite`, `rendez_vous`) VALUES
(2, 2, 3, '2024-03-15', '2024-03-15 10:30:00', '2024-03-15 10:40:00', '00:10:00', '2024-03-15 11:10:00', '00:30:00', 1),
(3, 3, 5, '2024-03-15', '2024-03-15 14:00:00', '2024-03-15 14:20:00', '00:20:00', '2024-03-15 14:50:00', '00:30:00', 0),
(9, 1, 2, '2025-03-06', '2025-03-06 14:19:51', '2025-03-06 14:34:51', '00:20:00', '2025-03-06 15:04:51', '00:30:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Visiteur`
--

CREATE TABLE `Visiteur` (
  `id_visiteur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL DEFAULT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  `auth_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Visiteur`
--

INSERT INTO `Visiteur` (`id_visiteur`, `nom`, `prenom`, `email`, `telephone`, `password`, `auth_token`, `token_expiration`) VALUES
(1, 'Updated', 'Name', 'pierre.dupont@example.com', '0102030405', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '4af671e41c65e5dbf1bf97d88fec36d5f4ab2affee5ed24a1ac87ac26daf6b55', '2025-03-07 13:08:51'),
(2, 'Martin', 'Sophie', 'sophie.martin@example.com', '0607080910', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(3, 'Leroy', 'Julien', 'julien.leroy@example.com', '0112233445', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(4, 'Moreau', 'Claire', 'claire.moreau@example.com', '0625364758', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(5, 'Gauthier', 'Marc', 'marc.gauthier@example.com', '0692837465', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(6, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(7, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(8, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(9, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(10, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL),
(11, 'Test', 'User', 'test@test.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Cabinet`
--
ALTER TABLE `Cabinet`
  ADD PRIMARY KEY (`id_cabinet`);

--
-- Indexes for table `Medecin`
--
ALTER TABLE `Medecin`
  ADD PRIMARY KEY (`id_medecin`),
  ADD KEY `fk_medecin_cabinet` (`id_cabinet`),
  ADD KEY `fk_medecin_visiteur` (`id_visiteur`);

--
-- Indexes for table `Visite`
--
ALTER TABLE `Visite`
  ADD PRIMARY KEY (`id_visite`),
  ADD KEY `fk_visite_visiteur` (`id_visiteur`),
  ADD KEY `fk_visite_medecin` (`id_medecin`);

--
-- Indexes for table `Visiteur`
--
ALTER TABLE `Visiteur`
  ADD PRIMARY KEY (`id_visiteur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Cabinet`
--
ALTER TABLE `Cabinet`
  MODIFY `id_cabinet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Medecin`
--
ALTER TABLE `Medecin`
  MODIFY `id_medecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Visite`
--
ALTER TABLE `Visite`
  MODIFY `id_visite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Visiteur`
--
ALTER TABLE `Visiteur`
  MODIFY `id_visiteur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Medecin`
--
ALTER TABLE `Medecin`
  ADD CONSTRAINT `fk_medecin_cabinet` FOREIGN KEY (`id_cabinet`) REFERENCES `Cabinet` (`id_cabinet`),
  ADD CONSTRAINT `fk_medecin_visiteur` FOREIGN KEY (`id_visiteur`) REFERENCES `Visiteur` (`id_visiteur`);

--
-- Constraints for table `Visite`
--
ALTER TABLE `Visite`
  ADD CONSTRAINT `fk_visite_medecin` FOREIGN KEY (`id_medecin`) REFERENCES `Medecin` (`id_medecin`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_visite_visiteur` FOREIGN KEY (`id_visiteur`) REFERENCES `Visiteur` (`id_visiteur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
