-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Gegenereerd op: 29 jun 2025 om 19:39
-- Serverversie: 11.8.2-MariaDB-ubu2404
-- PHP-versie: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cateringdb`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Facility`
--

CREATE TABLE `Facility` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Facility`
--

INSERT INTO `Facility` (`id`, `name`, `creation_date`, `location_id`) VALUES
(1, 'Authentico', '2025-05-28', 1),
(2, 'Tasty Two', '2025-05-28', 1),
(4, 'Vegan Restaurant', '2025-06-01', 2),
(7, 'Chinese Restaurant', '2025-06-01', 2),
(9, 'Texan-Mexico', '2025-06-05', 1),
(10, 'Indian Restaurant', '2025-06-11', 2),
(11, 'Pakistani Restaurant', '2025-06-11', 2),
(13, 'Eisbein & Schweinhaxe', '2025-06-13', 2),
(15, 'Eisbein & Schweinhaxe', '2025-06-22', 2),
(16, 'Food', '2025-06-22', 2),
(17, 'Food', '2025-06-22', 2),
(18, 'Unreal Restaurant', '2025-06-29', 2),
(20, 'Plastic Restaurant', '2025-06-29', 2),
(21, 'Pyongyang Restaurant', '2025-06-29', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Facility_Tag`
--

CREATE TABLE `Facility_Tag` (
  `tag_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Facility_Tag`
--

INSERT INTO `Facility_Tag` (`tag_id`, `facility_id`) VALUES
(1, 1),
(11, 4),
(2, 7),
(10, 9),
(12, 10),
(13, 11),
(14, 13),
(15, 17),
(16, 20),
(17, 20),
(18, 21);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Location`
--

CREATE TABLE `Location` (
  `id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(12) NOT NULL,
  `country_code` char(2) NOT NULL,
  `phone_number` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Location`
--

INSERT INTO `Location` (`id`, `city`, `address`, `zip_code`, `country_code`, `phone_number`) VALUES
(1, 'Amsterdam', 'Sint Nicolaasstraat 9', '1012 NJ', 'NL', '+31 25 341 5818'),
(2, 'Den Haag', 'Willekeurigestraat 11', '8347 JS', 'NL', '+31 23 544 3294');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Tag`
--

CREATE TABLE `Tag` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `Tag`
--

INSERT INTO `Tag` (`id`, `name`) VALUES
(2, 'chinese'),
(17, 'garbage'),
(14, 'german, pork'),
(12, 'indian'),
(1, 'mexican'),
(18, 'north korean'),
(13, 'pakistani'),
(16, 'plastic'),
(10, 'tex-mex'),
(15, 'veal, lamb'),
(11, 'vegan');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Facility`
--
ALTER TABLE `Facility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexen voor tabel `Facility_Tag`
--
ALTER TABLE `Facility_Tag`
  ADD PRIMARY KEY (`tag_id`,`facility_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexen voor tabel `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `Tag`
--
ALTER TABLE `Tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Facility`
--
ALTER TABLE `Facility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT voor een tabel `Location`
--
ALTER TABLE `Location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `Tag`
--
ALTER TABLE `Tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Facility`
--
ALTER TABLE `Facility`
  ADD CONSTRAINT `location_id` FOREIGN KEY (`location_id`) REFERENCES `Location` (`id`);

--
-- Beperkingen voor tabel `Facility_Tag`
--
ALTER TABLE `Facility_Tag`
  ADD CONSTRAINT `facility_id` FOREIGN KEY (`facility_id`) REFERENCES `Facility` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_id` FOREIGN KEY (`tag_id`) REFERENCES `Tag` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
