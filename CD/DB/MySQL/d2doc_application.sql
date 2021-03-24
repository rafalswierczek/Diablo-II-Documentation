-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 22 Sty 2021, 19:47
-- Wersja serwera: 10.4.17-MariaDB
-- Wersja PHP: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `d2doc_application`
--
CREATE DATABASE IF NOT EXISTS `d2doc_application` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `d2doc_application`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `confirm_account`
--

DROP TABLE IF EXISTS `confirm_account`;
CREATE TABLE IF NOT EXISTS `confirm_account` (
  `hash` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `add_date` datetime NOT NULL,
  PRIMARY KEY (`hash`),
  UNIQUE KEY `uq_confirm_account__user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext COLLATE utf8_polish_ci NOT NULL,
  `add_date` datetime NOT NULL,
  `thread_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_message__thread_id` (`thread_id`),
  KEY `fk_message_sender_id__user_id` (`sender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `message`
--

INSERT INTO `message` (`id`, `text`, `add_date`, `thread_id`, `sender_id`) VALUES
(1, 'tete', '2020-12-26 00:00:00', 1, 6),
(3, 'tete', '2020-12-26 00:00:00', 1, 7),
(5, 'tete', '2020-12-26 00:00:00', 2, 8),
(6, 'tete', '2020-12-26 00:00:00', 2, 9),
(7, 'tete', '2020-12-26 00:00:00', 3, 6),
(8, 'tete', '2020-12-26 00:00:00', 3, 6),
(9, 'tete', '2020-12-26 00:00:00', 3, 8),
(10, 'tete', '2020-12-26 00:00:00', 4, 10),
(12, 'xxx', '2020-12-28 00:00:00', 1, 6);

--
-- Wyzwalacze `message`
--
DROP TRIGGER IF EXISTS `exactly_two_users_for_one_thread_BI`;
DELIMITER $$
CREATE TRIGGER `exactly_two_users_for_one_thread_BI` BEFORE INSERT ON `message` FOR EACH ROW BEGIN
	set @invalidThirdUser = 0;
    
    set @invalidThirdUser = (select 1 where NEW.sender_id not in(
		select user1_id from thread where id = NEW.thread_id
        union all
        select user2_id from thread where id = NEW.thread_id
    ));
    
    if(@invalidThirdUser = 1) then
		SIGNAL SQLSTATE '45002' SET MESSAGE_TEXT = 'You cannot insert message with sender that is not present in this thread.';
    end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` mediumtext COLLATE utf8_polish_ci NOT NULL,
  `lang` char(2) COLLATE utf8_polish_ci NOT NULL,
  `add_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `code` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `uq_role__name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `thread`
--

DROP TABLE IF EXISTS `thread`;
CREATE TABLE IF NOT EXISTS `thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_date` datetime NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_thread_user1_id__user_id` (`user1_id`),
  KEY `fk_thread_user2_id__user_id` (`user2_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `thread`
--

INSERT INTO `thread` (`id`, `add_date`, `user1_id`, `user2_id`) VALUES
(1, '2020-12-26 00:00:00', 6, 7),
(2, '2020-12-26 00:00:00', 8, 9),
(3, '2020-12-26 00:00:00', 6, 8),
(4, '2020-12-26 00:00:00', 10, 9),
(6, '2020-12-29 00:00:00', 6, 9);

--
-- Wyzwalacze `thread`
--
DROP TRIGGER IF EXISTS `thread_users_limitations_BI`;
DELIMITER $$
CREATE TRIGGER `thread_users_limitations_BI` BEFORE INSERT ON `thread` FOR EACH ROW begin
	set @threadUsersAreInUse = 0;
    set @selfMessage = 0;
    
	set @threadUsersAreInUse = (select 1 WHERE EXISTS(
		select id from thread 
        where
			(user1_id = NEW.user1_id AND user2_id = NEW.user2_id) OR
            (user1_id = NEW.user2_id AND user2_id = NEW.user1_id)
	));
    
    set @selfMessage = (select 1 WHERE NEW.user1_id = NEW.user2_id);
    
    if(@threadUsersAreInUse = 1) then
    	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'You cannot insert thread with users that are already in use.';
	end if;
    
    if(@selfMessage= 1) then
    	SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'You cannot insert thread with users with the same id (self message)';
    end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `password` char(60) COLLATE utf8_polish_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `description` varchar(2000) COLLATE utf8_polish_ci DEFAULT NULL,
  `character` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL,
  `add_date` datetime NOT NULL,
  `active` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user__email` (`email`) USING BTREE,
  UNIQUE KEY `uq_user__login` (`login`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `user`
--

INSERT INTO `user` (`id`, `login`, `password`, `name`, `email`, `description`, `character`, `add_date`, `active`) VALUES
(6, 'rafineria1', '123', 'ra', 'em1', 'desc', 'pal', '2020-12-26 00:00:00', b'1'),
(7, 'rafineria2', '123', 'ra', 'em2', 'desc', 'pal', '2020-12-26 00:00:00', b'1'),
(8, 'rafineria3', '123', 'ra', 'em3', 'desc', 'pal', '2020-12-26 00:00:00', b'1'),
(9, 'rafineria4', '123', 'ra', 'em4', 'desc', 'pal', '2020-12-26 00:00:00', b'1'),
(10, 'rafineria5', '123', 'ra', 'em5', 'desc', 'pal', '2020-12-26 00:00:00', b'1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` int(11) NOT NULL,
  `role_code` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role_code`),
  KEY `fK_user_role__role_code` (`role_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `confirm_account`
--
ALTER TABLE `confirm_account`
  ADD CONSTRAINT `fk_confirm_account__user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Ograniczenia dla tabeli `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message__thread_id` FOREIGN KEY (`thread_id`) REFERENCES `thread` (`id`),
  ADD CONSTRAINT `fk_message_sender_id__user_id` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `thread`
--
ALTER TABLE `thread`
  ADD CONSTRAINT `fk_thread_user1_id__user_id` FOREIGN KEY (`user1_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_thread_user2_id__user_id` FOREIGN KEY (`user2_id`) REFERENCES `user` (`id`);

--
-- Ograniczenia dla tabeli `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `fK_user_role__user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `fk_user_role__role_code` FOREIGN KEY (`role_code`) REFERENCES `role` (`code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
