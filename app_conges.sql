-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 18, 2026 at 08:23 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_conges`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `code_categorie` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_categorie` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`code_categorie`, `libelle_categorie`) VALUES
('CIV', 'Civil'),
('MIL', 'Militaire');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `code_contact` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_contact` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_type_contact` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_contact`),
  KEY `contact_matricule_employe_foreign` (`matricule_employe`),
  KEY `contact_code_type_contact_foreign` (`code_type_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demande`
--

DROP TABLE IF EXISTS `demande`;
CREATE TABLE IF NOT EXISTS `demande` (
  `code_demande` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_demande` date NOT NULL,
  `date_debut_prevu` date NOT NULL,
  `date_fin_prevu` date NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci,
  `statut_demande` enum('en_attente_n1','en_attente_n2','en_attente_n3','en_attente_n4','approuvee','rejetee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente_n1',
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_type_permission` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_demande`),
  KEY `demande_matricule_employe_foreign` (`matricule_employe`),
  KEY `demande_code_type_permission_foreign` (`code_type_permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `demande`
--

INSERT INTO `demande` (`code_demande`, `date_demande`, `date_debut_prevu`, `date_fin_prevu`, `motif`, `statut_demande`, `matricule_employe`, `code_type_permission`) VALUES
('DM-CC4DC281', '2026-06-19', '2026-06-25', '2026-07-25', 'CONGE ANNUELLE', 'en_attente_n1', 'TONGA', 'CONGE_AN'),
('DM-4E5CE333', '2026-07-08', '2026-07-10', '2026-07-14', 'maladie grave', 'en_attente_n1', 'KOFFI', 'PERM_EXC');

-- --------------------------------------------------------

--
-- Table structure for table `employe`
--

DROP TABLE IF EXISTS `employe`;
CREATE TABLE IF NOT EXISTS `employe` (
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `code_localite_naissance` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_compte` enum('actif','inactif','suspendu') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_conge_restant` int NOT NULL DEFAULT '0',
  `code_genre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_categorie` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`matricule_employe`),
  UNIQUE KEY `employe_email_unique` (`email`),
  KEY `employe_code_localite_naissance_foreign` (`code_localite_naissance`),
  KEY `employe_code_genre_foreign` (`code_genre`),
  KEY `employe_code_categorie_foreign` (`code_categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employe`
--

INSERT INTO `employe` (`matricule_employe`, `nom`, `prenom`, `date_naissance`, `code_localite_naissance`, `mot_de_passe`, `statut_compte`, `email`, `total_conge_restant`, `code_genre`, `code_categorie`) VALUES
('EMP2026', 'GBLA', 'ELI', '2006-01-10', 'ABD_YOP', '$2y$12$NYmvLNQXVSFR7oh7TB5bsOeEDODPYUAyLTQgGynPIFQVs0kF.k5KK', 'actif', 'elielgbla@gmail.com', 30, 'M', 'CIV'),
('TONGA', 'TONGA', 'Eliel', '2006-02-10', NULL, '$2y$12$D8Sx7ZE86jaU1hfr1Axslujo.5pPw3J4gDXt16IaqAlStbMtrNQya', 'actif', 'formation@kamakinter.ci', 30, 'M', 'CIV'),
('REGIS', 'KOUAME', 'REGIS', '2004-10-12', NULL, '$2y$12$5DIGN/eW7efo7rUygGX76uZxhGgNHvcFRGTgZvk4rv8bPW1DqFRyO', 'actif', 'regiskouame@gmail.com', 30, 'M', 'CIV'),
('KOFFI', 'KOFFI', 'NESTOR', '1980-12-10', 'ABD_PLT', '$2y$12$otLsoBNl4cI69QMyAVeOfO6S1xqadR4cymFWTpmBcqdzwZGhPUBdC', 'actif', 'koffi@gmail.com', 30, 'M', 'CIV'),
('MARIE', 'MARIE', 'ANGE', '2006-05-24', 'ABD_KSS', '$2y$12$yW6YyO/bBu3VseL2gs.rhuvZXH60Jd34VRuIfukXILZzFzXkaGhY.', 'actif', 'marie@gmail.com', 30, 'F', 'CIV'),
('GORE', 'GORE', 'BI', '1975-04-12', NULL, '$2y$12$SEp.AOtllvGI1Zziocc5nOKe7eKMXLwlyguN9C6FlgAyXRGTwHzpG', 'actif', 'gore@gmail.com', 30, 'M', 'CIV');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `code_genre` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_genre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_genre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`code_genre`, `libelle_genre`) VALUES
('M', 'Masculin'),
('F', 'Féminin');

-- --------------------------------------------------------

--
-- Table structure for table `historique_poste`
--

DROP TABLE IF EXISTS `historique_poste`;
CREATE TABLE IF NOT EXISTS `historique_poste` (
  `code_historique` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_poste` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_service` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  PRIMARY KEY (`code_historique`),
  KEY `historique_poste_matricule_employe_foreign` (`matricule_employe`),
  KEY `historique_poste_code_poste_foreign` (`code_poste`),
  KEY `historique_poste_code_service_foreign` (`code_service`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `historique_poste`
--

INSERT INTO `historique_poste` (`code_historique`, `matricule_employe`, `code_poste`, `code_service`, `date_debut`, `date_fin`) VALUES
('HIST-5EF98929', 'EMP2026', 'ADMIN', 'IT_DEV', '2026-06-18', NULL),
('HIST-7E32398E', 'KOFFI', 'RESP_SERVI', 'IT_DEV', '2026-06-19', NULL),
('HIS00001', 'REGIS', 'DIR_EXPLOI', 'DIR_GEN', '2026-06-19', NULL),
('HIST-971B9A25', 'MARIE', 'RH', 'RH_SERV', '2026-06-19', NULL),
('HIS00002', 'GORE', 'DIR_GENERA', 'DIR_GEN', '2026-06-19', NULL),
('HIS00003', 'TONGA', 'EMPLOYE', 'IT_DEV', '2026-06-19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journalisation`
--

DROP TABLE IF EXISTS `journalisation`;
CREATE TABLE IF NOT EXISTS `journalisation` (
  `code_log` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_concernee` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code_log`),
  KEY `journalisation_matricule_employe_foreign` (`matricule_employe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journalisation`
--

INSERT INTO `journalisation` (`code_log`, `matricule_employe`, `action`, `table_concernee`, `date_action`, `details`, `ip_address`) VALUES
('LOG0000001', 'EMP2026', 'Inscription', 'employe, historique_poste', '2026-06-18 13:43:11', 'Création de compte et affectation initiale au poste: ADMIN.', '127.0.0.1'),
('LOG0000002', 'EMP2026', 'Déconnexion', 'employe', '2026-06-18 14:32:39', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000003', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:09:09', 'Connexion réussie.', '127.0.0.1'),
('LOG0000004', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:12:02', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000005', 'TONGA', 'Connexion', 'employe', '2026-06-19 11:12:26', 'Connexion réussie.', '127.0.0.1'),
('LOG0000006', 'TONGA', 'Déconnexion', 'employe', '2026-06-19 11:13:00', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000007', 'TONGA', 'Connexion', 'employe', '2026-06-19 11:14:33', 'Connexion réussie.', '127.0.0.1'),
('LOG0000008', 'TONGA', 'Déconnexion', 'employe', '2026-06-19 11:17:00', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000009', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:17:20', 'Connexion réussie.', '127.0.0.1'),
('LOG0000010', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:26:35', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000011', 'KOFFI', 'Inscription', 'employe, historique_poste', '2026-06-19 11:28:35', 'Création de compte et affectation initiale au poste: EMPLOYE.', '127.0.0.1'),
('LOG0000012', 'KOFFI', 'Déconnexion', 'employe', '2026-06-19 11:28:52', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000013', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:29:18', 'Connexion réussie.', '127.0.0.1'),
('LOG0000014', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:33:35', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000015', 'MARIE', 'Inscription', 'employe, historique_poste', '2026-06-19 11:35:13', 'Création de compte et affectation initiale au poste: RH.', '127.0.0.1'),
('LOG0000016', 'MARIE', 'Déconnexion', 'employe', '2026-06-19 11:39:03', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000017', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:40:23', 'Connexion réussie.', '127.0.0.1'),
('LOG0000018', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:45:08', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000019', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:46:05', 'Connexion réussie.', '127.0.0.1'),
('LOG0000020', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:46:20', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000021', 'TONGA', 'Connexion', 'employe', '2026-06-19 11:46:45', 'Connexion réussie.', '127.0.0.1'),
('LOG0000022', 'TONGA', 'Déconnexion', 'employe', '2026-06-19 11:46:55', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000023', 'EMP2026', 'Connexion', 'employe', '2026-06-19 11:47:12', 'Connexion réussie.', '127.0.0.1'),
('LOG0000024', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 11:47:57', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000025', 'TONGA', 'Connexion', 'employe', '2026-06-19 11:48:10', 'Connexion réussie.', '127.0.0.1'),
('LOG0000026', 'TONGA', 'Déconnexion', 'employe', '2026-06-19 11:49:53', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000027', 'KOFFI', 'Connexion', 'employe', '2026-06-19 11:50:05', 'Connexion réussie.', '127.0.0.1'),
('LOG0000028', 'KOFFI', 'Déconnexion', 'employe', '2026-06-19 12:06:47', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000029', 'MARIE', 'Connexion', 'employe', '2026-06-19 12:07:32', 'Connexion réussie.', '127.0.0.1'),
('LOG0000030', 'MARIE', 'Déconnexion', 'employe', '2026-06-19 12:09:20', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000031', 'TONGA', 'Connexion', 'employe', '2026-06-19 12:10:13', 'Connexion réussie.', '127.0.0.1'),
('LOG0000032', 'TONGA', 'Déconnexion', 'employe', '2026-06-19 12:11:00', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000033', 'EMP2026', 'Connexion', 'employe', '2026-06-19 12:11:49', 'Connexion réussie.', '127.0.0.1'),
('LOG0000034', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 12:17:31', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000035', 'KOFFI', 'Connexion', 'employe', '2026-06-19 12:17:46', 'Connexion réussie.', '127.0.0.1'),
('LOG0000036', 'KOFFI', 'Déconnexion', 'employe', '2026-06-19 12:32:19', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000037', 'EMP2026', 'Connexion', 'employe', '2026-06-19 12:32:32', 'Connexion réussie.', '127.0.0.1'),
('LOG0000038', 'EMP2026', 'Connexion', 'employe', '2026-06-19 12:34:33', 'Connexion réussie.', '127.0.0.1'),
('LOG0000039', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 13:45:19', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000040', 'EMP2026', 'Connexion', 'employe', '2026-06-19 13:45:41', 'Connexion réussie.', '127.0.0.1'),
('LOG0000041', 'EMP2026', 'Déconnexion', 'employe', '2026-06-19 13:45:52', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000042', 'KOFFI', 'Connexion', 'employe', '2026-06-19 13:46:13', 'Connexion réussie.', '127.0.0.1'),
('LOG0000043', 'EMP2026', 'Connexion', 'employe', '2026-06-26 19:42:41', 'Connexion réussie.', '127.0.0.1'),
('LOG0000044', 'EMP2026', 'Navigation', NULL, '2026-06-26 19:42:56', '{\"url\":\"http:\\/\\/127.0.0.1:8000\\/journalisation\",\"method\":\"GET\",\"input\":[]}', '127.0.0.1'),
('LOG0000045', 'EMP2026', 'Déconnexion', 'employe', '2026-06-26 19:43:18', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000046', 'EMP2026', 'Connexion', 'employe', '2026-06-26 19:44:34', 'Connexion réussie.', '127.0.0.1'),
('LOG0000047', 'EMP2026', 'Déconnexion', 'employe', '2026-06-26 19:55:21', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000048', 'TONGA', 'Connexion', 'employe', '2026-06-26 19:56:04', 'Connexion réussie.', '127.0.0.1'),
('LOG0000049', 'TONGA', 'Déconnexion', 'employe', '2026-06-26 19:56:11', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000050', 'KOFFI', 'Connexion', 'employe', '2026-06-26 19:56:32', 'Connexion réussie.', '127.0.0.1'),
('LOG0000051', 'EMP2026', 'Connexion', 'employe', '2026-07-08 11:13:53', 'Connexion réussie.', '127.0.0.1'),
('LOG0000052', 'EMP2026', 'Déconnexion', 'employe', '2026-07-08 11:25:36', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000053', 'REGIS', 'Connexion', 'employe', '2026-07-08 11:25:41', 'Connexion réussie.', '127.0.0.1'),
('LOG0000054', 'REGIS', 'Déconnexion', 'employe', '2026-07-08 11:25:58', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000055', 'EMP2026', 'Connexion', 'employe', '2026-07-08 11:26:14', 'Connexion réussie.', '127.0.0.1'),
('LOG0000056', 'EMP2026', 'Navigation', NULL, '2026-07-08 11:28:18', '{\"url\":\"http:\\/\\/127.0.0.1:8000\\/journalisation\",\"method\":\"GET\",\"input\":[]}', '127.0.0.1'),
('LOG0000057', 'EMP2026', 'Navigation', NULL, '2026-07-08 11:28:19', '{\"url\":\"http:\\/\\/127.0.0.1:8000\\/journalisation\",\"method\":\"GET\",\"input\":[]}', '127.0.0.1'),
('LOG0000058', 'EMP2026', 'Déconnexion', 'employe', '2026-07-08 11:30:56', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000059', 'REGIS', 'Connexion', 'employe', '2026-07-08 11:31:00', 'Connexion réussie.', '127.0.0.1'),
('LOG0000060', 'REGIS', 'Déconnexion', 'employe', '2026-07-08 11:31:30', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000061', 'MARIE', 'Connexion', 'employe', '2026-07-08 11:32:57', 'Connexion réussie.', '127.0.0.1'),
('LOG0000062', 'MARIE', 'Déconnexion', 'employe', '2026-07-08 11:33:19', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000063', 'EMP2026', 'Connexion', 'employe', '2026-07-08 11:33:42', 'Connexion réussie.', '127.0.0.1'),
('LOG0000064', 'EMP2026', 'Déconnexion', 'employe', '2026-07-08 11:34:40', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000065', 'REGIS', 'Connexion', 'employe', '2026-07-08 11:34:44', 'Connexion réussie.', '127.0.0.1'),
('LOG0000066', 'REGIS', 'Déconnexion', 'employe', '2026-07-08 11:34:52', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1'),
('LOG0000067', 'KOFFI', 'Connexion', 'employe', '2026-07-08 11:35:20', 'Connexion réussie.', '127.0.0.1'),
('LOG0000068', 'KOFFI', 'Déconnexion', 'employe', '2026-07-08 11:45:29', 'L\'utilisateur s\'est déconnecté (Fermeture de session).', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `localite`
--

DROP TABLE IF EXISTS `localite`;
CREATE TABLE IF NOT EXISTS `localite` (
  `code_localite` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_localite` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_localite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `localite`
--

INSERT INTO `localite` (`code_localite`, `libelle_localite`) VALUES
('ABD_PLT', 'PLATEAU'),
('ABD_YOP', 'YOPOUGON'),
('ABD_KSS', 'KOUMASSI'),
('ABD_COCD', 'COCODY');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_24_133210_create_genres_table', 1),
(5, '2026_05_24_133211_create_categories_table', 1),
(6, '2026_05_24_133212_create_postes_table', 1),
(7, '2026_05_24_133214_create_localites_table', 1),
(8, '2026_05_24_133215_create_services_table', 1),
(9, '2026_05_24_133216_create_type_contacts_table', 1),
(10, '2026_05_24_133219_create_type_permissions_table', 1),
(11, '2026_05_24_133237_create_residences_table', 1),
(12, '2026_05_24_133251_create_employes_table', 1),
(13, '2026_05_24_133305_create_historique_postes_table', 1),
(14, '2026_05_24_133306_create_contacts_table', 1),
(15, '2026_05_24_133307_create_plannings_table', 1),
(16, '2026_05_24_133309_create_demandes_table', 1),
(17, '2026_05_24_133310_create_journalisations_table', 1),
(18, '2026_05_24_133312_create_validation_permissions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `planning_journalier_employe`
--

DROP TABLE IF EXISTS `planning_journalier_employe`;
CREATE TABLE IF NOT EXISTS `planning_journalier_employe` (
  `code_planning` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `jour_repos_1` tinyint UNSIGNED NOT NULL,
  `jour_repos_2` tinyint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`code_planning`),
  KEY `planning_journalier_employe_matricule_employe_foreign` (`matricule_employe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `planning_journalier_employe`
--

INSERT INTO `planning_journalier_employe` (`code_planning`, `matricule_employe`, `date_debut`, `date_fin`, `heure_debut`, `heure_fin`, `jour_repos_1`, `jour_repos_2`) VALUES
('PLN0001', 'EMP2026', '2026-02-09', '2026-05-18', '08:00:00', '17:00:00', 6, 7),
('PLN0002', 'EMP2026', '2026-05-19', NULL, '10:00:00', '19:00:00', 6, 7),
('PLN0003', 'TONGA', '2026-06-19', '2026-06-29', '08:00:00', '17:00:00', 6, 7),
('PLN0004', 'TONGA', '2026-06-30', NULL, '15:00:00', '23:30:00', 5, 6),
('PLN0005', 'KOFFI', '2026-06-19', NULL, '06:00:00', '00:00:00', 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `poste`
--

DROP TABLE IF EXISTS `poste`;
CREATE TABLE IF NOT EXISTS `poste` (
  `code_poste` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_poste` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_responsable` tinyint NOT NULL DEFAULT '0',
  `niveau_validation` tinyint DEFAULT NULL,
  `acces_journalisation` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`code_poste`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `poste`
--

INSERT INTO `poste` (`code_poste`, `libelle_poste`, `est_responsable`, `niveau_validation`, `acces_journalisation`) VALUES
('EMPLOYE', 'Employé ordinaire', 0, NULL, 0),
('RESP_SERVI', 'Responsable de service', 1, 1, 0),
('RH', 'Responsable Ressources Humaines', 1, 2, 0),
('DIR_EXPLOI', 'Directeur d\'Exploitation', 1, 3, 0),
('DIR_GENERA', 'Directeur Général', 1, 4, 0),
('ADMIN', 'Administrateur système', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `residence`
--

DROP TABLE IF EXISTS `residence`;
CREATE TABLE IF NOT EXISTS `residence` (
  `code_residence` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `matricule_employe` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code_residence`),
  KEY `residence_matricule_employe_foreign` (`matricule_employe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `code_service` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_service` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_service`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`code_service`, `libelle_service`) VALUES
('IT_DEV', 'Informatique & Développement'),
('RH_SERV', 'Ressources Humaines'),
('DIR_GEN', 'Direction Générale'),
('SERV_COM', 'SERVCISE COMMERCIAL'),
('SERV_HK', 'HK'),
('SER_REST', 'SERVICE BANQUET');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type_contact`
--

DROP TABLE IF EXISTS `type_contact`;
CREATE TABLE IF NOT EXISTS `type_contact` (
  `code_type_contact` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_type_contact` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_type_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type_permission`
--

DROP TABLE IF EXISTS `type_permission`;
CREATE TABLE IF NOT EXISTS `type_permission` (
  `code_type_permission` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_type_permission` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`code_type_permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `type_permission`
--

INSERT INTO `type_permission` (`code_type_permission`, `libelle_type_permission`) VALUES
('CONGE_AN', 'Congé Annuel'),
('PERM_EXC', 'Permission Exceptionnelle');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `validation_permission`
--

DROP TABLE IF EXISTS `validation_permission`;
CREATE TABLE IF NOT EXISTS `validation_permission` (
  `code_validation` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_demande` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule_valideur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_validation` tinyint NOT NULL,
  `decision` enum('valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_decision` date NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code_validation`),
  UNIQUE KEY `validation_permission_code_demande_niveau_validation_unique` (`code_demande`,`niveau_validation`),
  KEY `validation_permission_matricule_valideur_foreign` (`matricule_valideur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `validation_permission`
--

INSERT INTO `validation_permission` (`code_validation`, `code_demande`, `matricule_valideur`, `niveau_validation`, `decision`, `date_decision`, `commentaire`, `signature`) VALUES
('VAL-B1424665', 'DM-CC4DC281', 'KOFFI', 1, 'valide', '2026-06-19', 'avis favorable', NULL),
('VAL-62AF1A11', 'DM-4E5CE333', 'KOFFI', 1, 'valide', '2026-07-08', 'avis favorable', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
