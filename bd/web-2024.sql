-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Авг 06 2025 г., 15:25
-- Версия сервера: 8.0.41
-- Версия PHP: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `web-2024`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dates_meetings`
--

CREATE TABLE `dates_meetings` (
  `id` int NOT NULL,
  `meetings_id` int NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `dates_meetings`
--

INSERT INTO `dates_meetings` (`id`, `meetings_id`, `date`) VALUES
(312, 241, '2025-09-02'),
(313, 241, '2025-08-09'),
(314, 241, '2025-08-05'),
(315, 241, '2025-08-04'),
(316, 241, '2025-08-06'),
(317, 242, '2025-09-02'),
(318, 242, '2025-08-09'),
(319, 242, '2025-08-05'),
(320, 242, '2025-08-04'),
(321, 242, '2025-08-06'),
(327, 244, '2025-09-02'),
(328, 244, '2025-08-09'),
(329, 244, '2025-08-05'),
(330, 244, '2025-08-04'),
(331, 244, '2025-08-06'),
(332, 245, '2025-09-02'),
(333, 245, '2025-08-09'),
(334, 245, '2025-08-05'),
(335, 245, '2025-08-04'),
(336, 245, '2025-08-06'),
(337, 246, '2025-09-02'),
(338, 246, '2025-08-09'),
(339, 246, '2025-08-05'),
(340, 246, '2025-08-23'),
(341, 246, '2025-08-06'),
(342, 247, '2025-09-02'),
(343, 247, '2025-08-09'),
(344, 247, '2025-08-05'),
(345, 247, '2025-08-23'),
(346, 247, '2025-08-06');

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `meetings_id` int NOT NULL,
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `meetings`
--

CREATE TABLE `meetings` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `leader_id` int NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `is_block` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `meetings`
--

INSERT INTO `meetings` (`id`, `title`, `description`, `hash`, `leader_id`, `start`, `end`, `is_block`) VALUES
(241, 'Necessary', NULL, 'Sdcluve4BpIjKVUSyp4sMelL6ZZnPJgm', 98, '09:00:00', '18:00:00', 0),
(242, 'Necessary', NULL, '8JWw1ZUDfRJS5rgKelu_V40ZFwgNPIeV', 99, '09:00:00', '18:00:00', 0),
(244, 'Necessary', NULL, 'G83wTEtQePKu3R7EwMlMlJ5x-RvHMpvT', 100, '09:00:00', '18:00:00', 0),
(245, 'Necessary', NULL, 'R-8S7lkh6QCevxpkh2UbhPzIRz3DclxI', 101, '09:00:00', '18:00:00', 0),
(246, 'Necessary', NULL, 'JOwvFlwFIG43rRJt1-tH850Qi9HRu8vM', 98, '09:00:00', '18:00:00', 0),
(247, 'Necessary', NULL, '2yfIHkuVo1blBiVaxLhzOWVXi8DcaVr2', 98, '09:00:00', '18:00:00', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `token`, `hash`) VALUES
(96, 'Necessari@mail.ru', '$2y$13$YtCh6DgTU7vnbNFWMr0kCuYfjtMXJCATI0ifsz2fKY/UqNAxmDSmq', 'plj6SC0nKVndx9LJHemNLqaarRbF6yL5', 'JgSfhTnnEItVm5HQqUocQ4_jfNLFeE5B'),
(97, 'Necessarii@mail.ru', '$2y$13$9FOOk.ZO5AaL2IFJH8mMMuLNJnbthc1D9jYO5IY6zoiuGQppxTGFS', 'GoenySJQNreaXFxTvm4AJ8lg603R5Wrf', 'tYjI-lILDuHFNXWnioOcqsYgpSxw63my'),
(98, 'user1@user.ru', '$2y$13$cxafrPdk2HSFk1LoNwjHTeUmORHsCKkzoO/4kVXoI6oj0pM9kPnRu', '6mFTYfVqEeVkxXCPD1d3DLD0lURuJlM6', 'eCQ-rizDuBWxb8VU0IHAPKWOTbw62slZ'),
(99, 'user2@user.ru', '$2y$13$LudB2eUt7MHpxgZTuTmUmO6XAZ24c1rZu1K5LZ5331UV5H9SvlNHu', 'oUksxo-uFJTTRC7lSe9IZQLA598JWk2U', 'Xwy6nY3CkuCo8rT75b3BNfGI7IFPLZ6O'),
(100, 'user3@user.ru', '$2y$13$I9dwEVahSn4FucNvs1YRO.JfjmpeaVIOhVBekW65SGghbnX/D3glK', 'oe9gwv6hwmxmJ3cBHu12C8PCinn7oBpD', 'nDE2n7YIbD-nQ4pMEeExGq4J0J9msX0x'),
(101, 'user4@user.ru', '$2y$13$wYC5REXf4WfFejaEEcRVN.1aM.M2uvIeFFjzve/0tQ4RsgkKu7Kyu', 'g2h8znO5morm5nagoxuP0aqop7buYLfU', '0DUoZ3ZgjJlubmBAYbku3N_8ryAxuhxZ');

-- --------------------------------------------------------

--
-- Структура таблицы `users_meetings`
--

CREATE TABLE `users_meetings` (
  `id` int NOT NULL,
  `meetings_id` int NOT NULL,
  `users_id` int NOT NULL,
  `availables` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users_meetings`
--

INSERT INTO `users_meetings` (`id`, `meetings_id`, `users_id`, `availables`) VALUES
(289, 241, 99, '[1, 1, 1, 1, 1, 1, 1, 1]'),
(290, 241, 100, '[1, 1, 1, 1, 1, 1, 1, 1]');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dates_meetings`
--
ALTER TABLE `dates_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dates_meetings_ibfk_1` (`meetings_id`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `files_ibfk_1` (`meetings_id`);

--
-- Индексы таблицы `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identifier` (`hash`),
  ADD KEY `leader_id` (`leader_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users_meetings`
--
ALTER TABLE `users_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meetings_id` (`meetings_id`),
  ADD KEY `users_id` (`users_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dates_meetings`
--
ALTER TABLE `dates_meetings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=347;

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT для таблицы `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT для таблицы `users_meetings`
--
ALTER TABLE `users_meetings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dates_meetings`
--
ALTER TABLE `dates_meetings`
  ADD CONSTRAINT `dates_meetings_ibfk_1` FOREIGN KEY (`meetings_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`meetings_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_meetings`
--
ALTER TABLE `users_meetings`
  ADD CONSTRAINT `users_meetings_ibfk_2` FOREIGN KEY (`meetings_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_meetings_ibfk_3` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
