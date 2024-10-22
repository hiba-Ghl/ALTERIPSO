-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 15 fév. 2024 à 08:44
-- Version du serveur : 8.2.0
-- Version de PHP : 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `rsmartv`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etablissement_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  `html` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `package` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `it` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ru` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `de` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3AF34668FF631228` (`etablissement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `etablissement_id`, `nom`, `titre`, `active`, `position`, `html`, `logo`, `package`, `fr`, `en`, `es`, `pt`, `it`, `ru`, `de`, `zh`, `ar`, `background`) VALUES
(1, 87371, 'Télevision', 'television', 0, 1, 'television.html', 'categories/4911b25f8d41d47e9ce86aceeb277bc3.png', 'television', 'Télevision', 'Television', 'Televisión', 'Televisã', 'Televisione', 'Телевидение', 'Fernsehen', '电视', 'تلفاز', 'categories/83e99b700cffed9e465d4cf3c8a361eb.png'),
(2, 87371, 'Radio', 'Radio', 1, 2, 'Radio.html', 'categories/a546f7929cd4bc6d72407b2a479b2672.png', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'Radio', 'categories/77d00fa6b241792aea74c74aa3f98693.png'),
(3, 87371, 'Service', 'Service', 0, 3, 'Service.html', 'categories/6d5ff4037194f589972103e2ad3b8d46.png', 'Service', 'Service', 'Service', 'Service', 'Service', 'Service', 'Service', 'Service', 'Service', 'Service', 'categories/467a466a5b07a0f30e152b4eda393c49.png'),
(4, 87371, 'Application', 'Application', 1, 4, 'Application.html', 'categories/07733a0574cb6e8e58165ff0f722b6b1.png', 'Application', 'Application', 'Application', 'Application', 'Application', 'Application', 'Application', 'Application', 'Application', 'Application', 'categories/e342605005a1778a41f230e5f7c2ff09.png'),
(5, 87371, 'jeux', 'jeux', 0, 5, 'jeux.html', 'categories/56144e58e2b93da02c694d7f1b94e2a6.png', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'jeux', 'categories/aca0fbcef58015bc08df0ccf6defd8d0.png'),
(6, 87371, 'Vod', 'vod', 1, 6, 'vod.html', 'categories/38c4ad4e330c539b4ac4789fd230c5af.png', 'vod', 'Vod', 'vod', 'vod', 'vod', 'vod', 'vod', 'vod', 'vod', 'vod', 'categories/717a8bddaa506d320ec54730272fd22f.png');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240201154413', '2024-02-01 15:45:51', 993),
('DoctrineMigrations\\Version20240205123028', '2024-02-05 12:31:25', 355),
('DoctrineMigrations\\Version20240208100056', '2024-02-08 10:01:24', 924),
('DoctrineMigrations\\Version20240208100640', '2024-02-08 10:06:54', 402),
('DoctrineMigrations\\Version20240208101017', '2024-02-08 10:11:05', 1346),
('DoctrineMigrations\\Version20240208110055', '2024-02-08 11:42:13', 8),
('DoctrineMigrations\\Version20240208111409', '2024-02-08 11:42:13', 3296);

-- --------------------------------------------------------

--
-- Structure de la table `etablissement`
--

DROP TABLE IF EXISTS `etablissement`;
CREATE TABLE IF NOT EXISTS `etablissement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `licence` int DEFAULT NULL,
  `adresse` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msgbienvenu` int DEFAULT NULL,
  `msgap` int DEFAULT NULL,
  `access_tv_in_checkout` int DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_etablissement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87372 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etablissement`
--

INSERT INTO `etablissement` (`id`, `nom`, `code`, `description`, `licence`, `adresse`, `logo`, `background`, `type`, `msgbienvenu`, `msgap`, `access_tv_in_checkout`, `ville`, `nom_etablissement`, `prenom`, `pays`, `genre`) VALUES
(75209, 'kousas', '40000', NULL, NULL, 'immeuble mjadli etage 4 N 12 gueliz', NULL, NULL, NULL, NULL, NULL, NULL, 'Marrakech', 'douaa kousas', 'douaa', 'France', '0'),
(87371, 'kousas', '40000', NULL, NULL, 'immeuble mjadli etage 4 N 12 gueliz', 'etablissement/logo.png', 'etablissement/aa0e5bb9f11f8e02fcffffb2cae375cc.jpg', NULL, NULL, NULL, NULL, 'Marrakech', 'douaa kousas', 'douaa', 'Morocco', '2');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etablissement_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  KEY `IDX_8D93D649FF631228` (`etablissement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `etablissement_id`) VALUES
(18, 'douaa.kousas@gmail.com', '[]', '$2y$13$DciKLfRdq0QkDi2P5A4DS.GGW8Ky98BnQZ48VM1bbSTUsmUvsNRmC', 'douaa', 87371),
(19, 'douaa.kousa1s@gmail.com', '[]', '$2y$13$RqnEzKOIrU4sNz.Q41bYouISH/bD24KnWrE3H4.jCAeXE2MdyXo8S', 'douaa2', 75209);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `FK_3AF34668FF631228` FOREIGN KEY (`etablissement_id`) REFERENCES `etablissement` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649FF631228` FOREIGN KEY (`etablissement_id`) REFERENCES `etablissement` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
