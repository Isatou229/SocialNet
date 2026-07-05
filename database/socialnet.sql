-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 05 juil. 2026 à 17:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `socialnet`
--

-- --------------------------------------------------------

--
-- Structure de la table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiration` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `user_id`, `token`, `expiration`) VALUES
(1, 12, '64aeeae4aa46bba17960c1201e0dfcf2ffd2ad1a3a3f90f946aff8c226b12f34', '0000-00-00 00:00:00'),
(2, 12, '262f5fb901c2eb29645e4152334f60b289b139c6e7bd342f098a65bfd4d895d9', '0000-00-00 00:00:00'),
(3, 8, '248e36c91648ad59e823b68359c8b02ef14cf2132b6209a04a4332c872a2b07b', '0000-00-00 00:00:00'),
(4, 13, '29d3cc6a5fd5a6dc6677cb783d82bdf7dcc7e8935ee60849ef18bbb881cdbdfe', '0000-00-00 00:00:00'),
(6, 14, '46919969c1dd6b22b8f7cd5b5d5c8b721c8f74ab2d57ab35e511ec25caa8cb5c', '0000-00-00 00:00:00'),
(8, 15, '0bd1e822dfd0ceea703f769f41d2b089670a86f32c14e27847df763ce7d99b30', '0000-00-00 00:00:00'),
(9, 16, 'c05b0086898998b7547b8a30c20d3f075588f5e0ea930d8a679cb252fda3bf7f', '0000-00-00 00:00:00'),
(10, 8, '37e0a10e5a1fc530b277d25ea1e4f7e537b9d5262ef3912d39cf132557fcd154', '0000-00-00 00:00:00'),
(11, 8, 'ab870bcc63875d063c0b33cbaa2e839d6eebc54ca50027d0f1672306632eb05b', '0000-00-00 00:00:00'),
(12, 16, '647273f7b59a55e21abebe58489701f8d99b7b6292bdd46cc6f15a34be87213c', '0000-00-00 00:00:00'),
(13, 16, '027746e2d013e1a6a915e4472af020a2a4cf4976b3513160ba4db44c0e081df1', '0000-00-00 00:00:00'),
(14, 8, 'dceff588d357a49bc9dedd5399977e301982ec235babd69e8d6e4defd6f92a4a', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_commentaire` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','refused') NOT NULL DEFAULT 'pending',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `friends`
--

INSERT INTO `friends` (`id`, `sender_id`, `receiver_id`, `status`, `date_creation`) VALUES
(1, 12, 7, 'pending', '2026-07-04 13:21:44'),
(2, 12, 13, 'accepted', '2026-07-04 13:32:42'),
(3, 16, 1, 'pending', '2026-07-05 16:36:37'),
(4, 8, 16, 'accepted', '2026-07-05 16:56:17');

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` enum('like','dislike') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `type`) VALUES
(1, 13, 1, 'like'),
(2, 12, 1, 'dislike'),
(3, 12, 2, 'like'),
(4, 13, 2, 'like'),
(5, 8, 4, 'like');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_message` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `image`, `date_message`) VALUES
(1, 13, 12, 'yo boss', NULL, '2026-07-04 13:33:04'),
(2, 12, 13, 'cmt tu vas', NULL, '2026-07-04 13:33:20'),
(3, 13, 12, 'bien et toi chef', NULL, '2026-07-04 13:33:48'),
(4, 12, 13, 'cv ooh', NULL, '2026-07-04 13:33:55'),
(5, 16, 8, 'cc', NULL, '2026-07-05 16:56:33'),
(6, 8, 16, 'cmt vas tu gloria', NULL, '2026-07-05 16:56:51'),
(7, 16, 8, 'bien et toi', NULL, '2026-07-05 16:57:02'),
(8, 8, 16, 'cv ooh', NULL, '2026-07-05 16:57:10');

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contenu` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_publication` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `contenu`, `image`, `date_publication`) VALUES
(1, 12, 'Je suis Overflow', NULL, '2026-07-04 13:35:48'),
(2, 13, 'What&#039;s up ?', NULL, '2026-07-04 13:36:30'),
(4, 16, 'Mega cool', 'assets/images/uploads/posts/img_6a4a7d9ed01e72.07430651.jpg', '2026-07-05 16:51:58');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('user','moderateur','admin') NOT NULL DEFAULT 'user',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `photo`, `bio`, `role`, `date_creation`) VALUES
(1, 'BANDA', 'Vianney', 'vianneyb.596@gmail.com', '$2y$10$5SXfAQWu4bPlM.zdWRT.8uXVKhackLpEU8JkxJxKuavhA72E2wRO.', NULL, NULL, 'user', '2026-06-26 17:59:06'),
(2, 'BANDE', 'Vianney', 'via@gmail.com', '$2y$10$zIzTEGlrXSsv3I3dWJs80.LFM1O5Toskmftr4bBolm/IPKG7zKs8m', NULL, NULL, 'user', '2026-06-26 18:01:37'),
(3, 'Overwatch', 'MAN', 'over@gmail.com', '$2y$10$WHD44iJGYVByz3N35b395uDWUDUxdG8Ls1znWlmc9lb3r3UqgIZ0a', NULL, NULL, 'admin', '2026-06-26 18:08:44'),
(5, 'ESGIS', 'esgis', 'test@gmail.com', '$2y$10$DILKv3nRkFYcv9UT8tUFK.QmACd5kwtN9qF0zRmhFabAunrpZn.a2', NULL, NULL, 'user', '2026-06-26 20:07:19'),
(6, 'BANDA', 'Vianney', 'vianney.596@gmail.com', '$2y$10$hgeSFTl/qsht6bJGLIWoC.7DiUXdX7MQcforeOEChiT/Bx1XitO/G', NULL, NULL, 'user', '2026-07-03 16:08:37'),
(7, 'jean', 'joie', 'j@gmail.com', '$2y$10$f8M6eBzj7uQ7ulR1OnxUbOBkoc8TtF1g8GKvtCjz/p8jm7HrR/4oe', NULL, NULL, 'user', '2026-07-03 16:40:38'),
(8, 'Diallo', 'Hawiz', 'admin@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', NULL, 'Administrateur de la plateforme', 'admin', '2026-07-03 21:39:34'),
(9, 'Diakité', 'Moussa', 'modo@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', NULL, 'Modérateur', 'moderateur', '2026-07-03 21:39:34'),
(10, 'Kourouma', 'Aïcha', 'user@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', NULL, 'Étudiante L2 IRT', 'user', '2026-07-03 21:39:34'),
(11, 'Admin', 'Admin', 'admin@gmail.com', '.6f6wM4p0gM3yViksX2iPo7wV1Z8OOj2', NULL, NULL, 'admin', '2026-07-03 23:52:50'),
(12, 'Test', 'User', 'user.simple@test.com', '$2y$12$7BaIBXq1GmB0e8/lCS5zzO7lD2kfBf4OcfN1mGGtJ3BbJ4KGszuc2', NULL, NULL, 'user', '2026-07-04 13:17:29'),
(13, 'BOSS', 'overflow', 'toi@gmail.com', '$2y$10$EajBU33lhSf/XH1dAABqh.z/TwcAHSaW1Pyku2u7FtfaD85qQQS2.', NULL, NULL, 'user', '2026-07-04 13:31:56'),
(14, 'Dupont', 'Jean', 'jeandupont@test.com', '$2y$10$o0SusNBcwvJuFQxcdBJb3OyY4cbbPfr6jC6knHLrhhPvci6NsGOXe', NULL, NULL, 'user', '2026-07-05 15:49:40'),
(15, 'Martin', 'Sophie', 'sophie.martin2024@test.com', '$2y$10$n86Ro9l4nNaQjVYZg1yZhur4ywAiPfd6xoa..CtutNyJQuxgji/ba', NULL, NULL, 'user', '2026-07-05 16:01:30'),
(16, 'AVE', 'Gloria', 'vian@gmail.com', '$2y$10$IQDY4Gb0W7nT.3X4c7qjxeYZKW/lrZ.UrM6GdiUxVF33E04fuFdaO', NULL, '', 'admin', '2026-07-05 16:35:29');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like_user_post` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
