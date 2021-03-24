-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 14 Lut 2021, 18:01
-- Wersja serwera: 10.4.11-MariaDB
-- Wersja PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `d2doc_documentation`
--
CREATE DATABASE IF NOT EXISTS `d2doc_documentation` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `d2doc_documentation`;

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_documentation` (IN `docID` INT)  BEGIN
	DECLARE result int;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
        GET DIAGNOSTICS CONDITION 1
   		@p2 = MESSAGE_TEXT;
        SELECT @p2;
        #SELECT -1 as 'result';
    END;
    START TRANSACTION;
        DELETE FROM AttackSpeed WHERE DocumentationID = docID;
        DELETE FROM CommonWeapons WHERE DocumentationID = docID;
        DELETE FROM CommonArmor WHERE DocumentationID = docID;
        DELETE FROM CommonMisc WHERE DocumentationID = docID;

        DELETE FROM UniqueItemsLangProperties WHERE UniqueItemsID IN (
        	SELECT ID FROM UniqueItems
            WHERE DocumentationID = docID
        );
        DELETE FROM LangProperties WHERE DocumentationID = docID;
        DELETE FROM UniqueItems WHERE DocumentationID = docID;

        DELETE FROM Common WHERE DocumentationID = docID;
        DELETE FROM LangName WHERE DocumentationID = docID;
        DELETE FROM LangType WHERE DocumentationID = docID;
        DELETE FROM DocumentationLanguages WHERE DocumentationID = docID;
        DELETE FROM Documentation WHERE ID = docID;
        SET result = ROW_COUNT();
    COMMIT;

    SELECT IF(result >= 1, 1, 0) as 'result';
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `armor`
--

CREATE TABLE `armor` (
  `id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `min_dmg` smallint(6) DEFAULT NULL,
  `max_dmg` smallint(6) DEFAULT NULL,
  `min_ac` smallint(6) NOT NULL,
  `max_ac` smallint(6) NOT NULL,
  `block` tinyint(4) NOT NULL,
  `speed` tinyint(4) NOT NULL,
  `str_bonus` smallint(6) NOT NULL,
  `dex_bonus` smallint(6) NOT NULL,
  `req_str` smallint(6) NOT NULL,
  `durability` smallint(6) NOT NULL,
  `nodurability` bit(1) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `level_req` tinyint(4) NOT NULL,
  `gemsockets` tinyint(4) NOT NULL,
  `invfile` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `nightmare_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `hell_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `quest` bit(1) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `documentation`
--

CREATE TABLE `documentation` (
  `id` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  `default_language` char(2) COLLATE utf8_polish_ci NOT NULL,
  `add_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `documentation_contributors`
--

CREATE TABLE `documentation_contributors` (
  `id` int(11) NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `documentation_languages`
--

CREATE TABLE `documentation_languages` (
  `id` int(11) NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `language` char(2) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `documentation_roles`
--

CREATE TABLE `documentation_roles` (
  `id` int(11) NOT NULL,
  `name_pl` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `name_en` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `description_pl` varchar(1000) COLLATE utf8_polish_ci DEFAULT NULL,
  `description_en` varchar(1000) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_names`
--

CREATE TABLE `item_names` (
  `id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `pl` varchar(150) COLLATE utf8_polish_ci DEFAULT NULL,
  `en` varchar(150) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_properties`
--

CREATE TABLE `item_properties` (
  `id` int(11) NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `unique_items_id` int(11) NOT NULL,
  `pl` varchar(200) COLLATE utf8_polish_ci DEFAULT NULL,
  `en` varchar(200) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_types`
--

CREATE TABLE `item_types` (
  `id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_polish_ci NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `pl` varchar(150) COLLATE utf8_polish_ci DEFAULT NULL,
  `en` varchar(150) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `misc`
--

CREATE TABLE `misc` (
  `id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `speed` tinyint(4) NOT NULL,
  `min_stack` smallint(6) DEFAULT NULL,
  `max_stack` smallint(6) DEFAULT NULL,
  `level` tinyint(4) NOT NULL,
  `level_req` tinyint(4) NOT NULL,
  `gemsockets` tinyint(4) NOT NULL,
  `invfile` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `nightmare_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `hell_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `quest` bit(1) NOT NULL,
  `type` varchar(10) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `normal` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `exceptional` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `elite` varchar(255) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `unique_items`
--

CREATE TABLE `unique_items` (
  `id` int(11) NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `index` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `level` tinyint(4) NOT NULL,
  `level_req` tinyint(4) NOT NULL,
  `invfile` varchar(255) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `weapons`
--

CREATE TABLE `weapons` (
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `documentation_id` int(11) NOT NULL,
  `one_hand_min_dmg` smallint(6) DEFAULT NULL,
  `one_hand_max_dmg` smallint(6) DEFAULT NULL,
  `two_hand_min_dmg` smallint(6) DEFAULT NULL,
  `two_hand_max_dmg` smallint(6) DEFAULT NULL,
  `missile_min_dmg` smallint(6) DEFAULT NULL,
  `missile_max_dmg` smallint(6) DEFAULT NULL,
  `min_stack` smallint(6) DEFAULT NULL,
  `max_stack` smallint(6) DEFAULT NULL,
  `rangeadder` tinyint(4) NOT NULL,
  `str_bonus` smallint(6) NOT NULL,
  `dex_bonus` smallint(6) NOT NULL,
  `req_str` smallint(6) NOT NULL,
  `req_dex` smallint(6) NOT NULL,
  `durability` smallint(6) NOT NULL,
  `nodurability` bit(1) NOT NULL,
  `wclass` char(3) COLLATE utf8_polish_ci NOT NULL,
  `two_hand_wclass` char(3) COLLATE utf8_polish_ci NOT NULL,
  `level` tinyint(4) NOT NULL,
  `level_req` tinyint(4) NOT NULL,
  `gemsockets` tinyint(4) NOT NULL,
  `invfile` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `nightmare_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `hell_upgrade` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `quest` bit(1) NOT NULL,
  `rank_id` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `weapons_attack_speed`
--

CREATE TABLE `weapons_attack_speed` (
  `documentation_id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `character` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `mode` char(2) COLLATE utf8_polish_ci NOT NULL,
  `wclass` char(3) COLLATE utf8_polish_ci NOT NULL,
  `attack_speed` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `armor`
--
ALTER TABLE `armor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_armor__code_documentation_id` (`code`,`documentation_id`),
  ADD KEY `fk_armor__ranks_id` (`rank_id`),
  ADD KEY `fk_armor__documentation_id` (`documentation_id`);

--
-- Indeksy dla tabeli `documentation`
--
ALTER TABLE `documentation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_documentation__name` (`name`) USING BTREE;

--
-- Indeksy dla tabeli `documentation_contributors`
--
ALTER TABLE `documentation_contributors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_documentation_id_user_id_role_id` (`documentation_id`,`user_id`,`role_id`) USING BTREE,
  ADD KEY `fk_documentation_contributors__documentation_role_id` (`role_id`),
  ADD KEY `fk_documentation_contributors__user_id` (`user_id`);

--
-- Indeksy dla tabeli `documentation_languages`
--
ALTER TABLE `documentation_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_documentation_id_language` (`documentation_id`,`language`);

--
-- Indeksy dla tabeli `documentation_roles`
--
ALTER TABLE `documentation_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `item_names`
--
ALTER TABLE `item_names`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_item_names__code_documentation_id` (`code`,`documentation_id`),
  ADD KEY `fk_item_names__documentation_id` (`documentation_id`);

--
-- Indeksy dla tabeli `item_properties`
--
ALTER TABLE `item_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_properties__documentation_id` (`documentation_id`),
  ADD KEY `fk_item_properties__unique_items_id` (`unique_items_id`);

--
-- Indeksy dla tabeli `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_item_types__type_documentation_id` (`type`,`documentation_id`);

--
-- Indeksy dla tabeli `misc`
--
ALTER TABLE `misc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_misc__code_documentation_id` (`code`,`documentation_id`) USING BTREE,
  ADD KEY `fk_misc__documentation_id` (`documentation_id`);

--
-- Indeksy dla tabeli `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `unique_items`
--
ALTER TABLE `unique_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_unique_items__documentation_id` (`documentation_id`);

--
-- Indeksy dla tabeli `weapons`
--
ALTER TABLE `weapons`
  ADD PRIMARY KEY (`code`,`documentation_id`),
  ADD KEY `fk_weapons__documentation_id` (`documentation_id`),
  ADD KEY `fk_weapons__ranks_id` (`rank_id`);

--
-- Indeksy dla tabeli `weapons_attack_speed`
--
ALTER TABLE `weapons_attack_speed`
  ADD PRIMARY KEY (`documentation_id`,`code`,`character`,`mode`,`wclass`),
  ADD KEY `fk_weapons_attack_speed__weapons_code_documentation_id` (`code`,`documentation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `documentation`
--
ALTER TABLE `documentation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `documentation_roles`
--
ALTER TABLE `documentation_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `item_properties`
--
ALTER TABLE `item_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `unique_items`
--
ALTER TABLE `unique_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `armor`
--
ALTER TABLE `armor`
  ADD CONSTRAINT `fk_armor__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`),
  ADD CONSTRAINT `fk_armor__ranks_id` FOREIGN KEY (`rank_id`) REFERENCES `ranks` (`id`);

--
-- Ograniczenia dla tabeli `documentation_contributors`
--
ALTER TABLE `documentation_contributors`
  ADD CONSTRAINT `fk_documentation_contributors__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`),
  ADD CONSTRAINT `fk_documentation_contributors__documentation_role_id` FOREIGN KEY (`role_id`) REFERENCES `documentation_roles` (`id`),
  ADD CONSTRAINT `fk_documentation_contributors__user_id` FOREIGN KEY (`user_id`) REFERENCES `d2doc_application`.`user` (`id`);

--
-- Ograniczenia dla tabeli `documentation_languages`
--
ALTER TABLE `documentation_languages`
  ADD CONSTRAINT `fk_documentation_languages__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`);

--
-- Ograniczenia dla tabeli `item_names`
--
ALTER TABLE `item_names`
  ADD CONSTRAINT `fk_item_names__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`);

--
-- Ograniczenia dla tabeli `item_properties`
--
ALTER TABLE `item_properties`
  ADD CONSTRAINT `fk_item_properties__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`),
  ADD CONSTRAINT `fk_item_properties__unique_items_id` FOREIGN KEY (`unique_items_id`) REFERENCES `unique_items` (`id`);

--
-- Ograniczenia dla tabeli `misc`
--
ALTER TABLE `misc`
  ADD CONSTRAINT `fk_misc__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`);

--
-- Ograniczenia dla tabeli `unique_items`
--
ALTER TABLE `unique_items`
  ADD CONSTRAINT `fk_unique_items__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`);

--
-- Ograniczenia dla tabeli `weapons`
--
ALTER TABLE `weapons`
  ADD CONSTRAINT `fk_weapons__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`),
  ADD CONSTRAINT `fk_weapons__ranks_id` FOREIGN KEY (`rank_id`) REFERENCES `ranks` (`id`);

--
-- Ograniczenia dla tabeli `weapons_attack_speed`
--
ALTER TABLE `weapons_attack_speed`
  ADD CONSTRAINT `fk_weapons_attack_speed__documentation_id` FOREIGN KEY (`documentation_id`) REFERENCES `documentation` (`id`),
  ADD CONSTRAINT `fk_weapons_attack_speed__weapons_code_documentation_id` FOREIGN KEY (`code`,`documentation_id`) REFERENCES `weapons` (`code`, `documentation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
