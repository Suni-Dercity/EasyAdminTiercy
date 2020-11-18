-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : mariadb:3306
-- Généré le : jeu. 10 sep. 2020 à 20:23
-- Version du serveur :  10.5.4-MariaDB-1:10.5.4+maria~focal
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `testdb`
--
CREATE DATABASE IF NOT EXISTS `testdb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `testdb`;

-- --------------------------------------------------------

--
-- Structure de la table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
CREATE TABLE IF NOT EXISTS `USERS` (
  `IdUser` int(11) NOT NULL AUTO_INCREMENT,
  `PrenomUser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NomUser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MailUser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MotDePasseUser` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TelephoneUser` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DateNaissanceUser` date NOT NULL,
  `SexeUser` enum('Homme','Femme') COLLATE utf8mb4_unicode_ci NOT NULL,
  `DescriptionUser` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `PointUser` int(11) NOT NULL,
  PRIMARY KEY (`IdUser`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `USERS`
--

INSERT INTO `USERS` (`IdUser`, `PrenomUser`, `NomUser`, `MailUser`, `MotDePasseUser`, `TelephoneUser`, `DateNaissanceUser`, `SexeUser`, `DescriptionUser`, `PointUser`) VALUES
(1, 'Admin', 'Admin', 'adminFest@gmail.com', '2d030eb9f2812d6eb65c907d6d7fb0b273caa9e6', '', '0000-00-00', 'Homme', '', 0),
(2, 'Anne-Marie', 'Puizillout', 'ampuiz@univ-lemans.fr', '3e0a725fcabaff5010e12df2faa69ee73a712ed7', '', '0000-00-00', 'Homme', '', 0),
(3, 'DIWA', 'LP', 'lpdiwa@univ-lemans.fr', '4abbbdceab126987fa51061ca76fb5a361be809f', '0601020304', '0000-00-00', 'Femme', 'Ceci est une description', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
