-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 01 2025 г., 08:32
-- Версия сервера: 8.3.0
-- Версия PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `talivo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `sender_type` enum('user','seller') COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `sender_type`, `receiver_id`, `message`, `created_at`) VALUES
(1, 11, 'user', 2, 'ZZZ', '2025-09-29 21:41:37'),
(2, 11, 'user', 1, 'txyfcgjvhkbjlnmlkjhgv bnm', '2025-09-29 21:49:19'),
(3, 11, 'user', 1, 'dwwddwwdwdw', '2025-09-29 21:49:45'),
(4, 11, 'user', 4, 'fchgjvhkbln;ml,;mlkjhbvgb mn,khjbvgb mn', '2025-09-29 21:56:08'),
(5, 4, 'user', 11, 'DPDPDPDPPDDPDPDPDP', '2025-09-29 21:56:46'),
(6, 11, 'user', 4, 'ХУЙ', '2025-09-29 22:11:31'),
(7, 11, 'user', 4, 'GPBLF', '2025-09-29 22:11:56'),
(8, 11, 'user', 4, 'GPBLF', '2025-09-29 22:12:10'),
(9, 11, 'user', 4, 'GPBLF', '2025-09-29 22:12:16'),
(10, 11, 'user', 4, 'GPBLF', '2025-09-29 22:14:20'),
(11, 11, 'user', 4, 'вуувув', '2025-09-29 22:14:23'),
(12, 4, 'seller', 11, 'вцвцц', '2025-09-29 22:16:21'),
(13, 4, 'seller', 11, 'вцвццц', '2025-09-29 22:16:25'),
(14, 4, 'seller', 11, 'вцвцвцвццв', '2025-09-29 22:16:28'),
(15, 11, 'user', 4, 'cfcb', '2025-09-30 10:28:40'),
(16, 4, 'seller', 11, 'ВДВДВДВ', '2025-10-01 05:40:44');

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` decimal(3,2) DEFAULT '0.00',
  `count` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `title`, `description`, `price`, `image`, `seller_id`, `created_at`, `rating`, `count`) VALUES
(2, 'fhcgjvhkbln;', 'ctughvkjlbknlmknljbhvgc', 123.00, 'img_68dafac2e5364.png', 2, '2025-09-29 21:31:46', 0.00, 0),
(3, 'wdwkdowdwkowk', 'dokwkdowdkwodkwodkowdkw', 123131.00, 'img_68dafb4a01fc5.png', 2, '2025-09-29 21:34:02', 0.00, 0),
(4, 'cgjvhkbln;m\',;', 'xycugvhkbjlknjhbvgchfgbjgyiuvbn', 2122222.00, 'img_68dafe9d77fab.jpg', 1, '2025-09-29 21:48:13', 0.00, 0),
(5, 'fcgvjhkblnm;,mknjbhvg', 'cfgvjhkblnlkbjhvgcjkblnjbhvg ', 2232232.00, 'img_68db005a5e68a.jpg', 4, '2025-09-29 21:55:38', 0.00, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `selers`
--

DROP TABLE IF EXISTS `selers`;
CREATE TABLE IF NOT EXISTS `selers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('Игрушки','Одежда','Электроника','Книги') COLLATE utf8mb4_unicode_ci DEFAULT 'Игрушки',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `selers`
--

INSERT INTO `selers` (`id`, `name`, `email`, `password`, `category`, `created_at`) VALUES
(4, 'Ivan', 'uzvenkoivan49@gmail.com', '$2y$10$9mVxMCNsKRee52lsMaLLrOeFGelgmll/W3Os75zGK6WGNPFclYK5a', 'Игрушки', '2025-09-29 21:54:14');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `activation_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_active`, `activation_code`, `created_at`) VALUES
(11, 'Иван', 'uzvenkoivan49@gmail.com', '$2y$10$EiMvShz.QL7tIBN7N595cug6QkQ9tgxoRaa2hqn/hksPiNWy9GKPO', 1, NULL, '2025-09-28 14:01:54');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
